=== Paytrail for WooCommerce ===
Contributors: loueranta, henrykhelskygeniem
Donate link: https://www.paytrail.com/
Tags: woocommerce
Requires at least: 4.9
Tested up to: 5.8
Stable tag: 1.0.0
Requires PHP: 7.3
License: MIT
License URI: https://opensource.org/licenses/MIT

[Paytrail](https://www.paytrail.com) is a payment gateway that offers 20+ payment methods for Finnish customers.

== Description ==

[Paytrail](https://www.paytrail.com) is a payment gateway that offers 20+ payment methods for Finnish customers.

The payment gateway provides 20+ popular payment methods for Finnish customers with one simple integration. The provided payment methods include but are not limited to credit cards, online banking and mobile payments. 

To use this extension, you need to sign up for a Paytrail account. Transaction fees will be charged for every transaction. Transaction cost may vary from merchant to merchant, based on what is agreed upon with Paytrail when negotiating your contract. For more information and to register, please visit [our website](https://www.paytrail.com)  (in Finnish only) or contact [asiakaspalvelu@paytrail.com](mailto:asiakaspalvelu@paytrail.com) directly.

We employ the industry's best security practices and tools to maintain bank-level security for merchants and end customers. Paytrail is PCI DSS Level I and GDPR compliant. 

Upon checkout, customers are redirected to the Paytrail payment service. The customer enters his or her payment information directly into our secure environment so that the web shop never comes into contact with the customers payment data. Once the payment process is complete, customers will redirected back to your store. Tokenization is used to run transactions with stored payment information. No confidential card data is ever stored on your server.

Supported payment methods include:

= Finnish Online Banking =

* OP
* Nordea
* Danske Bank
* Aktia
* Handelsbanken
* Oma Säästöpankki
* POP Pankki
* S-Pankki
* Säästöpankki
* Ålandsbanken

= Mobile Payments =

* MobilePay
* Pivo
* Siirto
* AinaPay
* Apple Pay*

= International Credit Cards =

* Visa
* MasterCard
* American Express

The plugin supports card tokenization that enables logged-in customers to save card details during checkout process in order to reuse them in future checkouts or with [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) recurring payments.

= B2C/B2B Invoices & Instalments =

* OP Osta laskulla
* OP Lasku
* Collector Bank
* Jousto
* AfterPay

*) Partial support available, full support coming in 2021

== Installation ==

Follow these easy steps to install the plugin:

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the WooCommerce -> Settings -> Payments screen to configure the plugin

== Frequently Asked Questions ==

= Where do I get the required keys to install the plugin? =

Head over to [paytrail.com](https://www.paytrail.com) and register for the service (currently in Finnish only) to obtain the necessary keys.

== Changelog ==

= 1.7 =
* Updated Paytrail PHP SDK to new version, removed dependency for GuzzleHttp

= 1.6 =
* Rebranding from "OP Payment Service" to "Paytrail"

= 1.5.15 =
* Add validation for order tokens when creating scheduled Subscription/MIT payment
* Add better error message if no tokens found
* Change PHP version to 7.4 in Github Action

= 1.5.14 =
* Update minimum PHP version to 7.3
* OP Payment Service PHP-SDK version updated to 1.3.4

= 1.5.13 =
* OP Payment Service PHP-SDK version updated

= 1.5.12 =
* Add more logging to gateway
* Hide the payment method group title and expand the group if only one group is available
* Fix payment method description support
* Fix error handling when no payment provider chosen
* Fix initialising of payment method groups in provider form

= 1.5.11 =
* Add missing register scripts

= 1.5.10 =
* Updated dist script

= 1.5.9 =
* Added support for WP 5.6 and WC 4.8

= 1.5.8 =
* Added order locking in redirect/callback processing
* Improved logging

= 1.5.7 =
* Changed callback handling 
* Changed log reference

= 1.5.6 =
* Fixed 'Highlighted payment method' custom color issue
* Changed the payment page customization texts
* Add callback mode as parameter to gateway and move sleep to gateway, add order ids to log messages

= 1.5.5 =
* Changed to use the order number as a reference for the payment service providers instead of a random number
* Add delay to callback controller to prevent simultaneous processing of payments

= 1.5.3 =
* Add more debug logging to payment gateway processing
* Use scrolIntoView method after clicking payment method group button on checkout

= 1.5.2 =
* Add extra validation to check that wanted_provider is not null, otherwise use capitalized provider name from checkout-provider parameter.
* Change get_item_description function item description for product items to use either product name or product id
* Add composer.json to require guzzlehttp/guzzle version 6.5.4
* Update finnish translations
* Fix issue when using a gift card code after placing an order

= 1.5.1 =
* Add setting for fallback country in payment gateway settings 

= 1.5.0 =
* Add support for WooCommerce Subscriptions plugin
* Add tokenized card MIT payments functionalities
* Add new Helper class
* Add info and error logging for payment requests if debug log enabled
* Updated to the latest PHP-SDK version

= 1.4.1 =
* Tokenized card automatically set as users default payment method
* Updated to the latest PHP-SDK version
* Fixed idn_to_ascii() Guzzle error

= 1.4.0 =
* Add support for adding and using tokenized cards as payment methods
* Add tokenized card CIT payments functionalities
* Add Callback controller class and implementation

= 1.3.1 =
* Fixed the hosted payment method selection

= 1.3.0 =
* New layout for the payment method selection

= 1.2.8 =
* Bump SDK version to composer.lock

= 1.2.7 =
* Simplified error handling
* Strict comparison to some validations
* Use mb_substr() to truncate the description

= 1.2.6 =
* Minor tweaks to readme

= 1.2.5 =
* PHP-SDK updated to latest version (1.0.2)

= 1.2.4 =
* Updated description and installation instructions
* Added Github Action to auto deploy new releases to WordPress.org

= 1.2.0 =
* Rebranding changes from Paytrail to OP Merchant Service for WooCommerce
* First version published at WordPress.org
