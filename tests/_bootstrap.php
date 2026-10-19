<?php
/**
 * Shared test bootstrap, required by every suite bootstrap.
 *
 * Syncs tests/_mu-plugins/ into the install and loads tests/.env. Scaffolding the
 * install itself happens earlier, in composer's post-autoload-dump hook.
 */

if (defined('PAYTRAIL_TEST_BOOTSTRAP_DONE')) {
    return;
}
define('PAYTRAIL_TEST_BOOTSTRAP_DONE', true);

$paytrail_mu_source  = dirname(__DIR__) . '/tests/_mu-plugins';
$paytrail_mu_plugins = dirname(__DIR__) . '/tests/_wordpress/wp-content/mu-plugins';

if (is_dir($paytrail_mu_plugins) && is_dir($paytrail_mu_source)) {
    $paytrail_wanted = [];

    foreach ((array) glob($paytrail_mu_source . '/*.php') as $paytrail_src) {
        $paytrail_wanted[] = basename((string) $paytrail_src);
        $paytrail_dest     = $paytrail_mu_plugins . '/' . basename((string) $paytrail_src);
        if (! is_file($paytrail_dest) || filemtime((string) $paytrail_src) > filemtime($paytrail_dest)) {
            copy((string) $paytrail_src, $paytrail_dest);
        }
    }

    // Drop the ones an earlier checkout installed and this one no longer has, matched on
    // our own header so wp-browser's SQLite drop-in is left alone.
    foreach ((array) glob($paytrail_mu_plugins . '/*.php') as $paytrail_installed) {
        if (in_array(basename((string) $paytrail_installed), $paytrail_wanted, true)) {
            continue;
        }
        if (strpos((string) file_get_contents((string) $paytrail_installed), 'Plugin Name: Paytrail Tests,') !== false) {
            unlink((string) $paytrail_installed);
        }
    }
}

unset($paytrail_mu_source, $paytrail_mu_plugins, $paytrail_src, $paytrail_dest, $paytrail_wanted, $paytrail_installed);

// The test WP install's DB connection string and the rest of the config.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/*
 * Pin WP_HOME and WP_SITEURL to WORDPRESS_URL, the local built-in server. Constants
 * rather than option filters, because wp_plugin_directory_constants() freezes
 * WP_CONTENT_URL from siteurl before mu-plugins load; written here rather than
 * committed, because WPDb reloads a dump carrying whoever generated it. The tunnel is
 * dealt with in tests/_mu-plugins/02-paytrail-public-url.php.
 */
$paytrail_wp_config = dirname(__DIR__) . '/tests/_wordpress/wp-config.php';

if (is_file($paytrail_wp_config) && ! empty($_ENV['WORDPRESS_URL'])) {
    $paytrail_wp_url = rtrim((string) $_ENV['WORDPRESS_URL'], '/');

    $paytrail_block = <<<PHP
    // >>> Paytrail tests: the site answers as WORDPRESS_URL, tunnelled requests included.
    if (! defined('WP_HOME')) {
        define('WP_HOME', '{$paytrail_wp_url}');
        define('WP_SITEURL', '{$paytrail_wp_url}');
    }
    // <<< Paytrail tests
    PHP;

    $paytrail_config_contents = (string) file_get_contents($paytrail_wp_config);

    // Whatever a previous run left behind goes first, then the block is re-inserted.
    $paytrail_patched = (string) preg_replace(
        '/\n?\/\/ >>> Paytrail tests:.*?\/\/ <<< Paytrail tests\n/s',
        '',
        $paytrail_config_contents
    );
    $paytrail_patched = (string) preg_replace(
        "/\n?define\('WP_(HOME|SITEURL)',[^;]*\);/",
        '',
        $paytrail_patched
    );
    $paytrail_patched = (string) preg_replace(
        '/^<\?php/',
        "<?php\n\n" . $paytrail_block . "\n",
        $paytrail_patched,
        1
    );

    if ($paytrail_patched !== $paytrail_config_contents) {
        file_put_contents($paytrail_wp_config, $paytrail_patched);
    }

    unset($paytrail_wp_url, $paytrail_block, $paytrail_config_contents, $paytrail_patched);
}

unset($paytrail_wp_config);
