<?php
/**
 * Plugin Name: Paytrail Tests, WooCommerce session upsert
 * Description: Gives the WooCommerce session table the unique index its writes assume. Loaded only inside the Codeception test WP install.
 *
 * WooCommerce saves the session with `INSERT ... ON DUPLICATE KEY UPDATE`, which needs a
 * unique index on `session_key`. The SQLite translation of the schema has none, so every
 * save appends a row and reads pick one at random.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! defined('FQDB') || ! is_file(FQDB)) {
    return;
}

add_action(
    'muplugins_loaded',
    static function (): void {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        global $wpdb;
        $prefix = $wpdb instanceof wpdb ? $wpdb->prefix : 'wp_';
        $table  = $prefix . 'woocommerce_sessions';
        $index  = $prefix . 'paytrail_tests_session_key';

        try {
            // Its own connection: SQLite DDL the translation in front of $wpdb cannot carry.
            $sqlite = new PDO('sqlite:' . FQDB);
            $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $exists = $sqlite
                ->query("SELECT name FROM sqlite_master WHERE type = 'index' AND name = '{$index}'")
                ->fetchColumn();

            if ($exists !== false) {
                return;
            }

            // Duplicates already written go first, newest kept, so the index can be made.
            $sqlite->exec(
                "DELETE FROM `{$table}` WHERE rowid NOT IN ("
                . "SELECT MAX(rowid) FROM `{$table}` GROUP BY session_key)"
            );

            $sqlite->exec("CREATE UNIQUE INDEX {$index} ON `{$table}` (session_key)");
        } catch (Throwable $e) {
            // No table yet, or a database busy elsewhere. The next boot tries again.
        }
    },
    0
);
