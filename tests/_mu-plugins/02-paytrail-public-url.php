<?php
/**
 * Plugin Name: Paytrail Tests, public URL for Paytrail
 * Description: Keeps the site local for the browser and public for Paytrail. Loaded only inside the Codeception test WP install.
 *
 * The browser drives the site on WORDPRESS_URL, so its uncached assets never spend the
 * ngrok request allowance. Only Paytrail's traffic uses the tunnel.
 */

if (! defined('ABSPATH')) {
    exit;
}

// EndToEnd only: codeception.yml hands these to the built-in server, so the other
// suites, which never see them, are left alone.
$paytrail_local  = rtrim((string) getenv('WORDPRESS_URL'), '/');
$paytrail_public = rtrim((string) getenv('PAYTRAIL_WORDPRESS_URL'), '/');

if ($paytrail_local === '' || $paytrail_public === '' || $paytrail_local === $paytrail_public) {
    return;
}

// ---------------------------------------------------------------------------
// Inbound: what arrived through the tunnel.
// ---------------------------------------------------------------------------

$paytrail_public_host = (string) parse_url($paytrail_public, PHP_URL_HOST);
$paytrail_local_host  = (string) parse_url($paytrail_local, PHP_URL_HOST);
$paytrail_local_port  = parse_url($paytrail_local, PHP_URL_PORT);

if ($paytrail_local_port !== null) {
    $paytrail_local_host .= ':' . $paytrail_local_port;
}

/** Whether the given header names the public host. The forwarded one can be a list. */
$paytrail_asked_for = static function (string $header) use ($paytrail_public_host): bool {
    foreach (explode(',', (string) ($_SERVER[$header] ?? '')) as $host) {
        if (strcasecmp(strtok(trim($host), ':') ?: '', $paytrail_public_host) === 0) {
            return true;
        }
    }

    return false;
};

// A web request only. WPLoader boots WordPress in-process with no REQUEST_METHOD, and
// redirecting that exits before wp_loaded.
$paytrail_is_web_request = PHP_SAPI !== 'cli' && isset($_SERVER['REQUEST_METHOD']);

if ($paytrail_is_web_request && ($paytrail_asked_for('HTTP_HOST') || $paytrail_asked_for('HTTP_X_FORWARDED_HOST'))) {
    $paytrail_method = strtoupper((string) $_SERVER['REQUEST_METHOD']);
    $paytrail_uri    = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $paytrail_path   = (string) (parse_url($paytrail_uri, PHP_URL_PATH) ?: '/');

    // Server to server: a redirect would drop a POST body, and Paytrail follows none.
    $paytrail_is_callback = in_array($paytrail_method, ['GET', 'HEAD'], true) === false
        || strpos($paytrail_path, '/paytrail/callback/') === 0
        || 'callback' === ($_GET['paytrail-route'] ?? '');

    if ($paytrail_is_callback) {
        // Handled as the local request it would have been.
        $_SERVER['HTTP_HOST']   = $paytrail_local_host;
        $_SERVER['SERVER_NAME'] = strtok($paytrail_local_host, ':');
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_HOST'], $_SERVER['HTTP_X_FORWARDED_PROTO']);
    } else {
        // Anything a browser asks for, Paytrail's return redirect included.
        header('Location: ' . $paytrail_local . $paytrail_uri, true, 302);
        header('Cache-Control: no-store');
        exit;
    }

    unset($paytrail_method, $paytrail_uri, $paytrail_path, $paytrail_is_callback);
}

// ---------------------------------------------------------------------------
// Outbound: the URLs the plugin puts in a payment, which Paytrail will not take
// as localhost.
// ---------------------------------------------------------------------------

/*
 * The SDK talks over Guzzle or curl, so `http_request_args` never sees its bodies. The
 * plugin builds every redirect and callback URL from `home_url()`, so that is what is
 * swapped, and only for the request that creates a payment.
 */
$paytrail_is_store_api_checkout = static function (): bool {
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') {
        return false;
    }

    // Covers both /wp-json/... and the ?rest_route=... form, which REQUEST_URI carries.
    return strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), '/wc/store/v1/checkout') !== false;
};

$paytrail_is_purchase_request = $paytrail_is_web_request && (
    'checkout' === ($_GET['wc-ajax'] ?? '')
    || isset($_POST['woocommerce_pay'])
    // The block checkout places the order through the Store API rather than wc-ajax.
    || $paytrail_is_store_api_checkout()
);

if ($paytrail_is_purchase_request) {
    $paytrail_to_public = static function ($url) use ($paytrail_local, $paytrail_public) {
        return str_replace($paytrail_local, $paytrail_public, (string) $url);
    };

    add_filter('home_url', $paytrail_to_public, PHP_INT_MAX);
    add_filter('site_url', $paytrail_to_public, PHP_INT_MAX);
}

unset(
    $paytrail_local,
    $paytrail_public,
    $paytrail_public_host,
    $paytrail_local_host,
    $paytrail_local_port,
    $paytrail_asked_for,
    $paytrail_is_web_request,
    $paytrail_is_purchase_request
);
