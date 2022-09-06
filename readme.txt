=== Paytrail for WooCommerce ===
Contributors: loueranta
Donate link: https://www.paytrail.com/
Tags: woocommerce
Requires at least: 4.9
Tested up to: 6.0
Stable tag: 1.2.3
Requires PHP: 7.3
License: MIT
License URI: https://opensource.org/licenses/MIT

[Paytrail](https://www.paytrail.com) is a payment gateway that offers 20+ payment methods for Finnish customers.

== Description ==

[Paytrail](https://www.paytrail.com) is a payment gateway that offers 20+ payment methods for Finnish customers.

The payment gateway provides 20+ popular payment methods for Finnish customers with one simple integration. The provided payment methods include but are not limited to credit cards, online banking and mobile payments. 

To use this extension, you need to sign up for a Paytrail account. Transaction fees will be charged for every transaction. Transaction cost may vary from merchant to merchant, based on what is agreed upon with Paytrail when negotiating your contract. For more information and to register, please visit [our website](https://www.paytrail.com) or contact [asiakaspalvelu@paytrail.com](mailto:asiakaspalvelu@paytrail.com) directly.

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
* Apple Pay

= International Credit Cards =

* Visa
* MasterCard
* American Express

The plugin supports card tokenization that enables logged-in customers to save card details during checkout process in order to reuse them in future checkouts or with [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) recurring payments.

= B2C/B2B Invoices & Instalments =

* OP Osta laskulla
* OP Lasku
* Walley (Collector Bank)
* Jousto
* AfterPay

== Installation ==

Follow these easy steps to install the plugin:

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the WooCommerce -> Settings -> Payments screen to configure the plugin

== Frequently Asked Questions ==

= Where do I get the required keys to install the plugin? =

Head over to our [website](https://www.paytrail.com) and register for the service to obtain the necessary keys.

== Changelog ==

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
