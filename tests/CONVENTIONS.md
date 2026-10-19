# Test conventions

Read this before adding a test. [README.md](README.md) covers the mechanics of the
suites and the fixture API; this file is about what to write and what to leave out.

The suite exists to catch regressions in what the plugin sends to Paytrail and what it
does with the answer. It is not a coverage exercise. Every test costs something to read
and to keep working, so the bar is that a reviewer can tell what broke from the test
name and the failure message alone.

## One test per thing

A "thing" is a decision the plugin makes, not a branch in the code. Whether a pending
invoice is activated is one thing. Whether it is activated for Klarna, Walley and a
bank payment is still one thing, expressed as three provider rows.

Reach for a data provider whenever you would otherwise copy a test method and change
two lines. The row name is the documentation, and it prints in the test output, so
`'a bank payment'` beats a comment explaining the same thing inside a method body.

```php
/**
 * @dataProvider provide_orders_left_alone
 */
public function test_an_order_with_no_invoice_to_activate_is_left_alone( array $payment_status ): void {
```

Providers are worth it at three rows. Below that, two plain methods usually read
better than a provider plus its table.

## Where a test belongs

| Suite | Use it for |
|---|---|
| `Integration` | Anything reachable from PHP with WordPress and WooCommerce loaded. This is where almost everything goes. |
| `EndToEnd` | Only what genuinely needs a browser: the trip out to Paytrail and back, and anything reading `filter_input()`. |
| `Harness` | Tests of the harness itself, not the plugin: the Paytrail API fake, the subscriptions fakes, the log tailing and the artifact redaction. |

Default to Integration. A browser purchase is around eight seconds, needs ngrok, and
breaks when Paytrail changes a page; if a behaviour can be pinned from PHP, pin it from
PHP.

`filter_input()` is the usual reason something cannot be. It reads the real request, not
the superglobal, so setting `$_GET` in a test does nothing. The plugin reads its callback
and its POSTed provider that way, which is why `process_paytrail_payment()` is called
directly rather than through `process_payment()`. When you hit this, say so in a one line
comment and move the coverage to EndToEnd rather than working around it.

A Cest works the same way as a test case with two differences: the provider has to be
`protected`, since Codeception loads every public method as a test, and the row arrives
as a `Codeception\Example` after the actor. The three-row threshold above does not apply
there, because a browser flow is long enough that duplicating it even once is worse than
the provider.

## Request bodies go in snapshots

The biggest single category of assertion here is "what JSON does the plugin send to
Paytrail". Do not hand-assert those key by key. Build the scenario, run the code, and
pin the whole request against a committed fixture:

```php
$this->assertPaymentMatchesSnapshot( $this->apiRequestTo( '/payments' ), 'payment-fi', $order );
```

The fixture in `Support/Data/snapshots/` is the assertion. It records the method, the
endpoint and the decoded body. When you intend to change a payload, run:

```bash
composer test:integration:snapshots
```

then read the diff before committing it. That diff is the review: if a field you did not
mean to touch moved, you will see it there.

Give products explicit names and SKUs so the fixture is stable.
`assertPaymentMatchesSnapshot()` blanks the stamps and substitutes the order id and key;
anything else volatile goes through the placeholder argument:

```php
$this->assertPaymentMatchesSnapshot( $request, 'refund-fi', $order, [
    '<refund-unique-id>' => $this->refundUniqueIdOf( $request ),
] );
```

Placeholder values are matched on digit boundaries, so an order id of 125 will not be
substituted inside an amount of 12500. The site URL and any known credential are masked
for you.

Keep normal assertions for anything that is a rule rather than a shape. "The items add
up to the amount" is a rule Paytrail rejects a payment on, and it deserves a real
assertion, because a snapshot would happily record a body that does not add up. That one
has a helper: `assertItemsAddUp()`.

## Do not pin known bugs

Never write a test that asserts wrong behaviour so that fixing it would fail the suite.
A green test is a statement that the behaviour is correct, and using one to record a
defect makes the suite lie about the plugin.

Report the bug internally instead and leave it out of the suite until it is fixed. Then
add a test that asserts the correct behaviour, which reads like every other test here.

A snapshot that happens to record odd behaviour is fine, because the fixture is data
rather than a claim. Just do not write a test method whose name says the behaviour is
wrong.

## Comments

Keep the comment budget tight. Prose about a fixture goes stale faster than the fixture
does, and a wrong comment is worse than none.

- A method gets at most one or two sentences, and only when the name does not already
  say it.
- No `@param` or `@return` blocks on test methods. PHPCS does not ask for them. The
  exception is a genuinely untyped parameter, where one line naming the type earns its
  place.
- Providers keep a single line `@return array<string, array{...}>`, because that is the
  only place the row shape is written down.
- Inline comments are a single line. If you need a paragraph, the fixture is probably
  too clever; simplify it instead.
- Class docblock is one line plus the `@covers` tags.

When you reach for a comment to explain why a fixture looks the way it does, or why a
request count is what it is, put it in the provider row name or the assertion message
instead. Both show up in the failure output, where someone will actually read them.

## Naming

Test methods read as sentences about the plugin, not about the code:

```php
public function test_a_renewal_without_a_card_is_not_charged(): void
public function test_a_provider_that_cannot_refund_falls_back_to_an_email_refund(): void
public function test_a_free_order_is_completed_without_a_payment(): void
```

Say what the behaviour is and, where it is not obvious, why it matters. Avoid naming the
method under test; `@covers` already does that, and a name like
`test_process_refund_returns_true` tells a reviewer nothing about what broke.

Provider keys follow the same idea, in lower case and without a verb: `'a bank
payment'`, `'the items overshoot'`.

## Use the fixtures, do not hand-roll state

`IntegrationTestCase` and its traits exist so tests describe a store rather than build
one. Set a store profile and use the builders:

```php
class MyTest extends IntegrationTestCase {

    protected ?string $storeProfile = 'fi';   // FI / EUR / 25.5% VAT, gateway in test mode

    public function test_something(): void {
        $order = $this->havePaidPaytrailOrder();
```

Profiles are `'fi'`, `'fi-no-tax'`, `'fi-live'` (a real merchant account rather than test
mode), or `null` for WooCommerce defaults with the gateway unconfigured. If you catch
yourself writing four `update_option()` calls, the profile or a trait method already
covers it.

If you add a fixture that writes state somewhere new, clear it in `resetStore()` at the
same time. WPLoader wraps each test in a database transaction, but WooCommerce keeps
plenty outside the database: the session cart, `WC()->customer`, tax caches, and the
gateway object itself, which reads its settings once in its constructor.

Watch for that last one. Anything that changes the settings option has to go through
`setGatewaySettings()` or `haveGatewaySettings()`, which rebuild the gateway. A plain
`update_option()` leaves the gateway holding the previous test's credentials.

## Assert on endpoints, not positions

`apiRequestTo( '/refund' )` survives the plugin adding another call to the same flow.
`apiRequests()[1]` does not. Same for counts: prefer
`assertApiRequestCount( 1, '/activate-invoice' )` over a bare total.

Where the total genuinely is the point, say why in the message. A card charge is a single
call, so a test that only asserted the charge happened would still pass if the plugin
started creating a redirect payment alongside it.

## The API is unreachable, and that is a feature

Nothing in either suite reaches the network. The Paytrail SDK talks over Guzzle or curl
rather than `wp_remote_request()`, so the seam is the SDK client's own HTTP layer, which
`CanInterceptPaytrailApi` replaces with `FakePaytrailApi`. An unqueued call comes back as
a `RequestException`, which is a path production code has to survive anyway, and it is
recorded so you can assert on it. That makes "this code path must not call Paytrail" a
one line test:

```php
$this->assertNoApiRequests( 'A free order has nothing to charge.' );
```

Queue responses with `willRespondWith()`, or better, with the intent helpers in
`CanDriveCheckout` and `CanDriveOrderManagement`. A test that says
`willReportPaymentStatus( 'x', [ 'status' => 'pending' ] )` then `willActivateInvoice()`
reads as what Paytrail answers, not as two response envelopes in the right order.

Queued responses are matched on the longest URI fragment first, so a
`/payments/token/cit/charge` response is not eaten by one queued for `/payments/`.

## Checklist before you open the PR

- Would a reviewer know what broke from the test name and the failure message?
- Is this a new provider row rather than a new method?
- Did you regenerate and read the snapshot diff, rather than just accepting it?
- Any comment longer than two sentences, or any `@param` on a test method?
- Does the new fixture state get cleared in `resetStore()`?
- Run `composer lint:tests`, then `composer test:integration` twice. The second run
  catches state leaking between tests that a single run hides.
- If you touched the EndToEnd suite, run `composer test:e2e` too. It buys real orders in
  Paytrail's test environment, so a green run is the only proof it still works.
