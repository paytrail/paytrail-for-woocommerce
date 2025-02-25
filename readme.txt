=== Paytrail for WooCommerce ===
Contributors: loueranta
Donate link: https://www.paytrail.com/
Tags: woocommerce
Requires at least: 4.9
Tested up to: 6.7
Stable tag: 2.4.1
Requires PHP: 7.3
License: MIT
License URI: https://opensource.org/licenses/MIT

Paytrail is a payment gateway that offers 20+ payment methods for Finnish customers.

== Description ==

Accept all popular payment methods for Finnish B2C and B2B customers with one agreement and one plugin.

[Paytrail](https://www.paytrail.com) provides you with:

* **A trusted and popular Finnish payment service with 17 years of e-commerce experience** that is supported all over Finland and many European countries. 20 000+ online businesses and services including the Finnish State Treasury ‘s Suomi.fi payment service entrust us to handle their transactions.
* **Popular payment methods in Finland to increase conversion rates:** online banks, cards, mobile, Buy now, Pay later and PayPal. Detailed payment method information can be found on [our website](https://www.paytrail.com/en/payment-methods).
* **Tokenization and Subscriptions.** The plugin supports card tokenization which allows customers to store their credit card information securely and reliably for later use or with the use of [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) for recurring payments.
* **Fast, reliable payouts** that are made at the same time regardless of payment method used.
* **Accessible, friendly, and fast customer service.** Our customer service team replies to all requests from our customers and our customers’ customers.
* **Reliable and compliant service** with an [uptime of 99.98%](https://status.paytrail.com/). Local and EU regulatory requirements and updates are always made on time.
* **Easy monitoring and detailed reporting.** Track and analyze transactions, create refunds and payment links, and download detailed reports for accounting purposes.

To use this plugin, first [register for a Paytrail account](https://www.paytrail.com/en/order-service).

For pricing, please [visit our website](https://www.paytrail.com/en/pricing).

== Installation ==

Follow these easy steps to install the plugin:

1. Install the plugin through the WordPress plugins screen directly, or upload the plugin files to the `/wp-content/plugins` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the WooCommerce -> Settings -> Payments screen to configure the plugin

== Frequently Asked Questions ==

= Where do I get the required keys to install the plugin? =

Head over to our [website](https://www.paytrail.com) and register for the service to obtain the necessary keys.

= How can I test Paytrail's payment service? =

You can test our payment services in your online store before signing an agreement. Payments made using test credentials will not be processed.
To test the payment service, the test credentials below should be placed in the payment settings of your e-commerce platform. Compatible platforms and links to platform-specific installation instructions are compiled on Paytrail’s [website](https://www.paytrail.com/en/compatible-platforms). If necessary, ask the technical partner of your online business for instructions. Payments to the test merchant account will not be forwarded.

Test credentials:
* Merchant ID: 375917
* Secret key: SAIPPUAKAUPPIAS

With test credentials, you can test most of the payment methods included in Paytrail’s payment service. You can find the payment method specific credentials needed for testing in Paytrail’s [documentation](https://docs.paytrail.com/#/payment-method-providers).

== Changelog ==

= 2.4.1 =
- Added stamp for rounding row item
- Fixed payment status after failed payment

= 2.4.0 =
- Added possibility to display OP Lasku calculator in the product and cart page
- Fixed click listener on shortcode checkout

= 2.3.0 =
- Added Apple Pay support for payment page bypass
- Added supported currency notice

= 2.2.3 =
- Added gateway check for blocks checkout

= 2.2.2 =
- Fixed refund query

= 2.2.1 =
- Fixed subscription/saved card deletion

= 2.2.0 =
- Added checkout blocks support

= 2.1.2 =
- Added more validation for transactions in payment response

= 2.1.1 =
- Fixed VAT rounding problem caused by certain product price settings

= 2.1.0 =
- Added support for decimal values in tax rate
- Added Finnish bank reference option
- Allow selection of HTML tags in the payment description view
- Minor fixes

= 2.0.3 =
- Fixed test mode enablement

= 2.0.2 =
- Fixed test mode settings

= 2.0.1 =
- Removed option to generate Finnish bank reference numbers for transactions

= 2.0.0 =
- Added WooCommerce HPOS support
- Added new customer on-boarding flow
- Added option to generate Finnish bank reference numbers for transactions
- Improve error logging
- Change the Cancel URL to be the 'Pay for Order' page

= 1.3.11 =
- Fixed tax calculations

= 1.3.10 =
- Fixed payment card value fields

= 1.3.9 =
- Fix card form

= 1.3.8 =
- Fix rounding rows again

= 1.3.7 =
- Fixed rounding row negative amount fix

= 1.3.6 =
- Fixed rounding row negative amount

= 1.3.5 =
- Fixed item amount type missmatch

= 1.3.4 =
- Added WP code linter tests

= 1.3.3 =
- Added update status to canceled on each error case when handling refunds
- Fixed possible race condition between redirect and callback payment processing
- Home url cleaned with esc_url

= 1.3.2 =
- Added 3 second delay to Paytrail API callbacks to avoid race condition in cluster environments
- Company name field added to API calls when available

= 1.3.1 =
- Loosened comparisons on the condition related to tax calculations when taxes amount to zero

= 1.3.0 =
- Added support for manual renewals of Subscriptions
- Fixed an issue with rounding taxes for small amounts
- Fixed calculation with zero taxes
- The order->payment_complete now happens later than before

= 1.2.4 =
- Fixed an issue with rounding taxes for small amounts

= 1.2.3 =
- Make it possible to tweak paytrail order items via apply_filters
- Updated various libraries
- PHP-SDK version updated to 2.3
- Fixed language selection issue
- Checkout page login text tweak

= 1.2.2 =
- Added login and registration instructions for guest users, so they can save card details
- Updated supported features
- Hide empty payment method categories

= 1.2.1 =
- Fixed an issue which prevented deleting saved card

= 1.2.0 =
- Updated Paytrail PHP-SDK to 2.2 version
- Fixed Guzzle version conflict issues by adding cUrl as a fallback option

= 1.1.0 =
- Updated Paytrail PHP-SDK version
- Removed symphony packages & composer installers

= 1.0.9 =
- Added direct link to WooCommerce logs from the settings page
- Improvements to order status checking
- Better logging for status changes and references

= 1.0.8 =
- Update payment method image url

= 1.0.7 =
- Added token migration functionality for WooCommerce Subscriptions orders

= 1.0.6 =
- Added token migration functionality and upgrade instructions from old Checkout Finland plugin

= 1.0.5 =
- Reverted a code change that broke customizer options

= 1.0.4 =
- Updated lock files

= 1.0.3 =
* Added wordpress.org assets

= 1.0.2 =
* Added even more escapes to variables and options

= 1.0.1 =
* Added more escapes to variables and options
* Renamed some CSS classes to improve compatibility with the old Checkout Finland for WooCommerce plugin

= 1.0.0 =
* All initial plugin functionalities
