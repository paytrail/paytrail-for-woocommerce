#!/usr/bin/env bash
#
# Regenerate tests/Support/Data/dump.sql from a clean WordPress install.
#
# Usage: composer test:regenerate-dump
# Requires `wp` (WP-CLI) on PATH, and `composer install` to have run.
#
# The Action Scheduler rows are stripped after export because SQLite truncates their
# serialized `schedule` column at the first NUL byte, which aborts WP load on import.

set -euo pipefail

# wp-cli refuses to run as root by default; let it through for CI and docker.
export WP_CLI_ALLOW_ROOT=1

WP_ROOT="tests/_wordpress"
DB_FILE="${WP_ROOT}/data/db.sqlite"
DB_SNAPSHOT="${WP_ROOT}/data/db.sqlite_snapshot"
DUMP_PATH="tests/Support/Data/dump.sql"
PORT="${BUILTIN_SERVER_PORT:-8080}"
WP="wp --path=${WP_ROOT}"

# wp-cli runs outside Codeception, so wp-browser's Symlinker has not run yet.
echo "==> Linking the plugin, WooCommerce and Storefront into the install..."
ln -sfn "$(pwd)" "${WP_ROOT}/wp-content/plugins/paytrail-for-woocommerce"
ln -sfn "$(pwd)/tests/_plugins/woocommerce" "${WP_ROOT}/wp-content/plugins/woocommerce"
ln -sfn "$(pwd)/tests/_themes/storefront" "${WP_ROOT}/wp-content/themes/storefront"

echo "==> Stopping dev servers (best-effort)..."
vendor/bin/codecept dev:stop >/dev/null 2>&1 || true

echo "==> Wiping SQLite DB..."
rm -f "${DB_FILE}" "${DB_SNAPSHOT}"

# The admin password is a placeholder on purpose, so no real credential ends up in the
# committed dump. tests/_mu-plugins/04-paytrail-test-admin-password.php resets it to
# WORDPRESS_ADMIN_PASSWORD at request time.
echo "==> Installing WordPress..."
${WP} core install \
--url="http://localhost:${PORT}" \
--title="Paytrail Test" \
--admin_user="admin" \
--admin_email="admin@localhost.test" \
--admin_password="placeholder" \
--skip-email

echo "==> Setting permalinks..."
${WP} rewrite structure '/%postname%/' --hard

echo "==> Activating plugins (WooCommerce first, then Paytrail)..."
${WP} plugin activate woocommerce
${WP} plugin activate paytrail-for-woocommerce

echo "==> Activating Storefront theme..."
${WP} theme activate storefront

echo "==> Setting WooCommerce defaults (FI / EUR, skip wizard)..."
${WP} option update woocommerce_default_country "FI"
${WP} option update woocommerce_currency "EUR"
${WP} option update woocommerce_store_address "Mannerheimintie 12"
${WP} option update woocommerce_store_city "Helsinki"
${WP} option update woocommerce_store_postcode "00100"
${WP} option update woocommerce_calc_taxes "yes"
${WP} option update woocommerce_prices_include_tax "no"
${WP} option update woocommerce_tax_based_on "billing"
${WP} transient delete _wc_activation_redirect || true
${WP} option update woocommerce_admin_install_timestamp "$(date +%s)" || true

echo "==> Making the checkout page the shortcode one, so the provider list renders..."
CHECKOUT_PAGE_ID=$(${WP} post list --post_type=page --post_status=publish --title="Checkout" --field=ID)
if [ -z "$CHECKOUT_PAGE_ID" ]; then
    echo "Checkout page not found. Aborting."
    exit 1
fi
${WP} post update "${CHECKOUT_PAGE_ID}" --post_content="[woocommerce_checkout]"
${WP} option update woocommerce_terms_page_id 3

echo "==> Enabling the gateway in test mode..."
# No credentials in the dump: test mode signs as Paytrail's published test merchant.
${WP} option update woocommerce_paytrail_settings \
'{"enabled":"yes","enable_test_mode":"yes","provider_selection":"yes","debug":"yes","fallback_country":"FI"}' \
--format=json

echo "==> Truncating Action Scheduler tables (must be empty in the dump, see header)..."
php -r "
\$pdo = new PDO('sqlite:${DB_FILE}');
\$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach (['wp_actionscheduler_actions','wp_actionscheduler_claims','wp_actionscheduler_groups','wp_actionscheduler_logs'] as \$t) {
    try { \$pdo->exec(\"DELETE FROM \$t\"); } catch (Throwable \$e) {}
}
"

echo "==> Exporting dump via wp:db:export..."
vendor/bin/codecept wp:db:export "${WP_ROOT}" "${DUMP_PATH}"

echo "==> Stripping any AS data INSERTs that wp:db:export re-added during its own boot..."
php tests/_support_scripts/strip-actionscheduler-inserts.php "${DUMP_PATH}"

echo "==> Done. Dump: $(wc -c < "${DUMP_PATH}") bytes."
