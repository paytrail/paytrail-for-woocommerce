# Tests

Codeception + [wp-browser](https://github.com/lucatume/wp-browser) suites for Paytrail
for WooCommerce. [CONVENTIONS.md](CONVENTIONS.md) covers what to write; this file covers
how the thing runs and what the fixtures give you.

## Suites

| Suite | What it covers |
|---|---|
| `Integration` | The plugin, with WordPress and WooCommerce booted in-process. Almost everything lives here. |
| `EndToEnd` | Real purchases in a browser, against Paytrail's live test environment. |
| `Harness` | The test harness itself: the Paytrail API fake, the WooCommerce Subscriptions fakes, the log tailing and the artifact redaction. |

All three share one SQLite file, so never run two at once. CI runs them in the order
above, which is also the order to run them in locally.

## Getting started

```bash
composer install          # provisions tests/_wordpress/ as a side effect
cp tests/.env.example tests/.env
composer test:integration
```

The Integration and Harness suites need nothing else. The EndToEnd suite additionally
needs Chrome, an ngrok auth token and an ngrok endpoint of its own; see
[Running EndToEnd](#running-endtoend).

`composer install` runs `tests/_support_scripts/install-test-env.php` from
`post-autoload-dump`. That script downloads WordPress core into `tests/_wordpress/`,
configures it against SQLite, installs it and copies the mu-plugins from
`tests/_mu-plugins/`. It is idempotent, so it is safe to re-run, and it exits quietly
when the dev dependencies are absent, so a `--no-dev` install is unaffected.

There is no MySQL and no WP-CLI in the loop. The database is a SQLite file under
`tests/_wordpress/data/`, through the SQLite Database Integration drop-in, and the
install runs through wp-browser's own PHP API.

Nothing in `tests/.env` is a credential. The suites run the gateway in test mode, where
the plugin signs as Paytrail's published test merchant, and no request leaves the
process.

## Running

```bash
composer test:integration            # the plugin
composer test:harness                # the harness
composer test:e2e                    # real purchases in a browser
composer test:integration:snapshots  # rewrite the request fixtures, then read the diff
composer lint:tests                  # phpcs against tests/phpcs.xml
```

A single file or method:

```bash
vendor/bin/codecept run Integration RefundsTest.php
vendor/bin/codecept run Integration RefundsTest.php:test_the_refund_body
```

`--debug` forces wp-browser to reinstall WordPress, which WooCommerce's schema updater
cannot do twice against SQLite. Use `--steps` instead, or write the value you want to see
into an assertion message.

## Running EndToEnd

A browser purchase needs the store reachable from the internet, because Paytrail
redirects the shopper back and calls the site server to server. The browser still drives
the site locally, so only Paytrail's traffic goes through the tunnel and the ngrok
request allowance is not spent on uncached assets. `tests/_mu-plugins/02-paytrail-public-url.php`
is what keeps the two apart.

Before the first run:

1. Set `NGROK_AUTHTOKEN` in `tests/.env`.
2. Set `NGROK_DOMAIN` and `PAYTRAIL_WORDPRESS_URL` to an ngrok endpoint of your own. The
   two are coupled by the team ngrok config, so a new one has to be created there first;
   CI uses one per PR number.
3. `composer test:chromedriver`, once, and again after a Chrome update.

Then `composer test:e2e`. A purchase is around eight seconds, so narrowing the table
while chasing one case is worth it:

```bash
PAYTRAIL_ONLY=rounding composer test:e2e
```

The suite signs as Paytrail's published test merchant, so no credentials are involved
and the payments are real ones in Paytrail's test environment.

What the suite deliberately leaves out: refunds. The published test merchant cannot
receive refunds of e-payments, so the API refuses every one. The refund paths are
covered against the API fake in the Integration suite instead.

The store installs with the shortcode checkout, so that is what most rows buy through.
`BlockCheckoutCest` puts WooCommerce's own checkout block on the checkout page for the
one test that needs it, which WPDb undoes with the dump reload before the next.

## Reports

Each run writes Allure results to `tests/_output/allure-results/<suite>/`. Integration
tests attach the Paytrail traffic they provoked as `paytrail-requests.json`; EndToEnd
tests attach the screenshot, page source, browser console and network log of a failure,
plus whatever WordPress and WooCommerce logged while they ran.

```bash
composer test:report         # renders tests/_output/report/index.html
composer test:report:share   # one self-contained file, for sharing
composer test:report:reset   # throw the accumulated results away
```

Rendering runs `tests/_support_scripts/verify-no-secrets.php`, which scans the artifacts
for every credential named in `SecretRegistry::SECRET_KEYS`, in every encoding the
plugin can emit, and deletes the report rather than hand over one that leaks.

## How a test talks to Paytrail

The Paytrail SDK uses Guzzle or curl, not `wp_remote_request()`, so WordPress' own
`pre_http_request` filter never sees any of it. The seam is the `http_client` property on
`Paytrail\SDK\PaytrailClient`, whose entire surface is one `request()` method.
`CanInterceptPaytrailApi` swaps in `Tests\Support\Fakes\FakePaytrailApi`, which records
what was sent and answers with what the test queued.

Responses come back as the SDK's own `CurlResponse`, signed with the merchant secret the
client was constructed with, because every read path validates the response HMAC before
it looks at the body.

The fake is installed on the gateway `Plugin::instance()` hands out, so a controller that
resolves the singleton gets the same wired-up object. Rebuilding the gateway (which any
settings change does) re-installs it.

## The fixture API

Composed into `Tests\Support\IntegrationTestCase`:

| Trait | What it gives you |
|---|---|
| `CanConfigureStore` | Store country, currency, tax rates, the WooCommerce pages, and the gateway settings option. |
| `CanManageProducts` | `haveSimpleProduct()`, `haveProductWithoutSku()`, `haveVariableProduct()`. |
| `CanBuildCartsAndOrders` | Addresses, carts, shipping, orders, refunds and card tokens. |
| `CanInterceptPaytrailApi` | `gateway()`, `willRespondWith()`, `apiRequestTo()`, `assertNoApiRequests()`. |
| `CanDriveCheckout` | Canned responses for the purchase flow: `willCreatePayment()`, `willChargeCard()`, `willChargeStoredCard()`. |
| `CanDriveOrderManagement` | Canned responses for what an admin does after: `willRefund()`, `willActivateInvoice()`, `willCancelInvoice()`. |
| `CanFakeSubscriptions` | Subscriptions, renewal orders and cart states, without WooCommerce Subscriptions installed. |
| `CanSnapshotRequests` | `assertPaymentMatchesSnapshot()` and `assertMatchesSnapshot()`. |

The EndToEnd suite has its own set, composed into `Tests\Support\EndToEndTester`:

| Trait | What it gives you |
|---|---|
| `CanManageE2EProducts` | Products and variations from `Data\TestProducts`, written straight to the database. |
| `CanManageE2ETaxRates` | Tax classes and rates from `Data\TestTaxRates`. |
| `CanDriveE2ECheckout` | The cart, the shortcode checkout form, the provider list, and the trip out to Paytrail and back. |
| `CanDriveE2EBlockCheckout` | The same purchase through the checkout block, whose fields and provider list the shortcode steps cannot drive. |
| `CanDriveE2EOrderManagement` | The WooCommerce order screen, and reading back what it wrote. |

## Layout

```
tests/
  Integration/            the plugin's tests
  EndToEnd/               browser tests
  Harness/                the harness's own tests
  Support/                fixtures, fakes, extensions and the base test case
    Data/snapshots/       committed request fixtures
    Data/dump.sql         the store the EndToEnd suite reloads before every test
  _mu-plugins/            copied into the test install by install-test-env.php
  _support_scripts/       install, dump, report and secret-scan scripts
  _subscriptions-fakes.php
  _bootstrap.php          shared suite bootstrap
```

`tests/Support/Data/dump.sql` is committed, and rebuilt with
`composer test:regenerate-dump` after an upstream change to the baseline store. That one
needs WP-CLI; nothing else does.

`tests/_wordpress/`, `tests/_plugins/`, `tests/_themes/`, `tests/_output/` and
`tests/.env` are all generated and gitignored.

## Not covered here

Anything the plugin reads with `filter_input()` cannot be reached from the Integration
suite, because it reads the real request rather than the superglobal. That is the payment
callback (`Gateway::check_paytrail_response()` and everything under it) and the POSTed
provider in `Gateway::process_payment()`. Both are covered by the EndToEnd suite, which
drives real requests.

Still uncovered anywhere: the add-card and stored-card flows, which need a logged-in
customer and a card the test merchant will tokenise, and subscription renewals, which
need WooCommerce Subscriptions installed.
