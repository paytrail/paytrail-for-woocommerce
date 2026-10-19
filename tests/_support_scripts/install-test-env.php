<?php
/**
 * Set up the local test WordPress environment. Idempotent.
 *
 * Runs from Composer's `post-autoload-dump` hook, because wp-browser's Symlinker runs
 * on MODULE_INIT and needs WordPress already extracted and configured by then.
 */

use lucatume\WPBrowser\Utils\Filesystem as FS;
use lucatume\WPBrowser\WordPress\Database\SQLiteDatabase;
use lucatume\WPBrowser\WordPress\Installation;
use lucatume\WPBrowser\WordPress\InstallationState\Multisite;
use lucatume\WPBrowser\WordPress\InstallationState\Single;

$root = dirname(__DIR__, 2);

if (! is_file($root . '/vendor/autoload.php')) {
    exit(0);
}

require $root . '/vendor/autoload.php';

// A --no-dev install, where the hook still fires but there is nothing to provision.
if (! class_exists(Installation::class)) {
    exit(0);
}

/** The header every mu-plugin in tests/_mu-plugins/ carries, so we only remove our own. */
const MU_PLUGIN_MARKER = 'Plugin Name: Paytrail Tests,';

$wpRoot       = $root . '/tests/_wordpress';
$muSource     = $root . '/tests/_mu-plugins';
$muPluginsDir = $wpRoot . '/wp-content/mu-plugins';
$contentDir   = $wpRoot . '/wp-content';
$dataDir      = $wpRoot . '/data';
$sqliteSrcDir = $root . '/tests/_plugins/sqlite-database-integration';

if (! is_file($wpRoot . '/wp-load.php')) {
    echo "Scaffolding WordPress into tests/_wordpress/ (downloads core once)...\n";
    if (! is_dir($wpRoot) && ! mkdir($wpRoot, 0777, true) && ! is_dir($wpRoot)) {
        fwrite(STDERR, "Failed to create {$wpRoot}\n");
        exit(1);
    }
    $version = getenv('WORDPRESS_VERSION');
    Installation::scaffold($wpRoot, is_string($version) && '' !== $version ? $version : 'latest');
}

if (! is_dir($dataDir) && ! mkdir($dataDir, 0777, true) && ! is_dir($dataDir)) {
    fwrite(STDERR, "Failed to create {$dataDir}\n");
    exit(1);
}

// Prefer the composer-installed sqlite-database-integration over wp-browser's bundled copy.
// Must run before configure(), which would otherwise place the bundled one itself.
if (is_dir($sqliteSrcDir)) {
    $sqliteDestDir = $muPluginsDir . '/sqlite-database-integration';
    $dropinPath    = $contentDir . '/db.php';

    if (! is_dir($sqliteDestDir)) {
        FS::mkdirp($muPluginsDir);
        if (! FS::recurseCopy($sqliteSrcDir, $sqliteDestDir)) {
            fwrite(STDERR, "Failed to copy SQLite mu-plugin to {$sqliteDestDir}\n");
            exit(1);
        }
    }

    $dbCopy = $sqliteDestDir . '/db.copy';
    if (! is_file($dropinPath) && is_file($dbCopy)) {
        $contents = file_get_contents($dbCopy);
        if ($contents === false) {
            fwrite(STDERR, "Could not read {$dbCopy}\n");
            exit(1);
        }
        $contents = str_replace(
            ['{SQLITE_IMPLEMENTATION_FOLDER_PATH}', '{SQLITE_PLUGIN}', '{SQLITE_MAIN_FILE}'],
            [$sqliteDestDir, 'sqlite-database-integration/load.php', $sqliteDestDir . '/load.php'],
            $contents
        );

        // WPLoader's in-process boot never sees our wp-config.php, so without this env
        // fallback it writes test_ tables to a different SQLite file than the PDO reads.
        $envFallback   = "if ( ! defined( 'DB_DIR' ) && getenv( 'DB_DIR' ) ) {\n"
            . "\tdefine( 'DB_DIR', realpath( getenv( 'DB_DIR' ) ) );\n"
            . "}\n"
            . "if ( ! defined( 'DB_FILE' ) && getenv( 'DB_FILE' ) ) {\n"
            . "\tdefine( 'DB_FILE', getenv( 'DB_FILE' ) );\n"
            . "}\n\n";
        $requireMarker = '// Require the implementation from the plugin.';
        $patched       = str_replace($requireMarker, $envFallback . $requireMarker, $contents);
        if ($patched === $contents) {
            fwrite(STDERR, "SQLite db.copy layout changed, could not find the require marker to inject DB_DIR/DB_FILE env fallback. Update install-test-env.php.\n");
            exit(1);
        }
        $contents = $patched;
        if (! file_put_contents($dropinPath, $contents, LOCK_EX)) {
            fwrite(STDERR, "Could not write SQLite dropin to {$dropinPath}\n");
            exit(1);
        }
        if (! is_file($contentDir . '/.gitignore')) {
            file_put_contents($contentDir . '/.gitignore', "db.php\n", LOCK_EX);
        }
    }
}

$installation = new Installation($wpRoot, false);
if (! $installation->isConfigured()) {
    echo "Configuring WordPress against SQLite DB...\n";
    $installation->configure(new SQLiteDatabase($dataDir, 'db.sqlite'));
}

// Patched in place rather than appended: the generated file defines WP_DEBUG first, so a
// second define() would be silently dropped.
$wpConfigPath = $wpRoot . '/wp-config.php';
if (is_file($wpConfigPath)) {
    $wpConfigContents = (string) file_get_contents($wpConfigPath);
    $updatedContents  = $wpConfigContents;

    // Flip the template's `define( 'WP_DEBUG', false );` to true.
    $updatedContents = (string) preg_replace(
        "/define\(\s*'WP_DEBUG'\s*,\s*false\s*\);/",
        "define( 'WP_DEBUG', true );",
        $updatedContents,
        1
    );

    if (false === strpos($updatedContents, 'WP_DEBUG_LOG')) {
        $updatedContents = (string) preg_replace(
            "/(define\(\s*'WP_DEBUG'\s*,\s*true\s*\);)/",
            "$1\ndefine( 'WP_DEBUG_LOG', true );\ndefine( 'WP_DEBUG_DISPLAY', false );\ndefine( 'SCRIPT_DEBUG', true );",
            $updatedContents,
            1
        );
    }

    if ($updatedContents !== $wpConfigContents) {
        file_put_contents($wpConfigPath, $updatedContents);
        echo "Patched wp-config.php (WP_DEBUG=true, WP_DEBUG_LOG=true, WP_DEBUG_DISPLAY=false, SCRIPT_DEBUG=true).\n";
    }
}

$state = $installation->getState();
if (! ($state instanceof Single || $state instanceof Multisite)) {
    echo "Installing WordPress (DB tables + admin user)...\n";
    $port = (int) (getenv('BUILTIN_SERVER_PORT') ?: 8080);
    try {
        $installation->install(
            "http://localhost:{$port}",
            'admin',
            'password',
            'admin@localhost.test',
            'Paytrail Test'
        );
    } catch (\Throwable $e) {
        // WP's install routine reports "Database Error!" on harmless races, so ask the file.
        try {
            $pdo = new PDO('sqlite:' . $dataDir . '/db.sqlite');
            $row = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='wp_options'")->fetchColumn();
            if ($row !== 'wp_options' && ! str_contains($e->getMessage(), 'already installed')) {
                fwrite(STDERR, 'WP install failed: ' . $e->getMessage() . "\n");
                exit(1);
            }
        } catch (\Throwable $checkException) {
            fwrite(STDERR, 'WP install failed: ' . $e->getMessage() . "\n");
            exit(1);
        }
    }
}

// WAL journal mode, so concurrent readers and writers can share the DB file.
$dbFile = $dataDir . '/db.sqlite';
if (is_file($dbFile)) {
    try {
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $mode = $pdo->query('PRAGMA journal_mode = WAL')->fetchColumn();
        if ('wal' !== strtolower((string) $mode)) {
            fwrite(STDERR, "Warning: could not switch {$dbFile} to WAL (got '{$mode}').\n");
        }
    } catch (\Throwable $walException) {
        fwrite(STDERR, "Could not set WAL on {$dbFile}: " . $walException->getMessage() . "\n");
    }
}

// Safety net: re-place the SQLite drop-in in case something wiped wp-content/db.php.
Installation::placeSqliteMuPlugin($muPluginsDir, $contentDir);

if (! is_dir($muPluginsDir) && ! mkdir($muPluginsDir, 0777, true) && ! is_dir($muPluginsDir)) {
    fwrite(STDERR, "Failed to create {$muPluginsDir}\n");
    exit(1);
}

$copied = 0;
$wanted = [];
foreach ((array) glob($muSource . '/*.php') as $file) {
    $wanted[] = basename((string) $file);
    if (copy((string) $file, $muPluginsDir . '/' . basename((string) $file))) {
        ++$copied;
    }
}

// Drop the ones an earlier checkout installed and this one no longer has, matched on our
// own header so the SQLite drop-in is left alone.
foreach ((array) glob($muPluginsDir . '/*.php') as $installed) {
    if (in_array(basename((string) $installed), $wanted, true)) {
        continue;
    }
    if (strpos((string) file_get_contents((string) $installed), MU_PLUGIN_MARKER) !== false) {
        unlink((string) $installed);
    }
}

echo "Test environment ready ({$copied} project mu-plugin(s) installed).\n";
