# Paytrail for WooCommerce
[Paytrail](https://www.paytrail.com) payment service for [WooCommerce](https://www.woocommerce.com)

**Required WordPress version:** 4.9<br>
**Required WooCommerce version:** 3.5<br>
**Required PHP version:** 7.3

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

## Installation

### From WordPress plugin directory

Open WordPress Admin panel and go to Plugins -> Add New. Search for [Paytrail for WooCommerce](https://wordpress.org/plugins/paytrail-for-woocommerce/), click install and then activate. 

After installation go to WooCommerce -> Settings -> Payments and select "Manage" next to Paytrail for WooCommerce to review settings.

### Via Composer

1. If you have Composer installed:
- You can use the command line to install the plugin:

```
$ composer require paytrail/paytrail-for-woocommerce
```
- Or you can add the following json to your `composer.json` file:

```json
{
  "require": {
    "paytrail/paytrail-for-woocommerce": "*"
  }
}
```
2. Activate the plugin.
3. Go to WooCommerce Settings and open `Payments` tab.
4. Enable Paytrail for WooCommerce with the toggle switch.
5. Configure your own Checkout Finland payment service settings.

## Updating from Checkout Finland

***Checkout Finland for WooCommerce plugin has been deprecated.*** <br />
All of its functionality now exists as the rebranded Paytrail for WooCommerce plugin.

### Update instructions
1. Install Paytrail for WooCommerce *(see previous chapter)*
2. Go to WooCommerce Settings and open `Payments` tab
3. Disable the old Checkout Finland for WooCommerce payment method

***Warning!*** Removing previous plugin results in payments made with Checkout Finland not refundable through WooCommerce. Payments can still be refunded through Paytrail’s Extranet.

Please also note that the module configurations will not carry over from the old plugin. You will need to reapply them yourself. This also applies to the payment page customizations. Certain classes, functions and CSS classes have also been renamed and any third party customizations will have to be revised.

You can move card tokens from the old plugin by enabling Token migration in the new plugin's settings. This will also update tokens for the WooCommerce Subscriptions orders, if module is in use.

## Configuration

There are several settings you can configure via the options page.

### Test mode

If test mode is enabled, your store will automatically use Paytrail's [test credentials](https://paytrail.github.io/api-documentation/#/?id=test-credentials).

### Debug log

Enable logging all payment gateway events. Recommended only for debugging purposes as this might save personal data.

### Payment provider selection

Choose whether you want the payment provider selection to happen on the checkout page or on a separate page.

### Merchant ID and Secret key

Your Paytrail credentials. You can obtain the keys by registering at our [website](https://www.paytrail.com) or contact [asiakaspalvelu@paytrail.com](mailto:asiakaspalvelu@paytrail.com) directly.

### Fallback Country

Setup fallback country to be used if no country is provided from Woocommerce checkout. Use only if billing_country/shipping_country is removed from checkout fields.

### Token migration ###

Choose this to update card information (tokens) from the old Checkout Finland for WooCommerce -plugin. The update is done upon saving settings. 

## Router

### Callback Routes

Permalinks enabled:

    Success: /paytrail-payment-service/callback/index
    Cancel: /paytrail-payment-service/callback/index

Permalinks disabled:

    Success: /index.php?paytrail-payment-service-route=callback&paytrail-payment-service-action=index
    Cancel: /index.php?paytrail-payment-service-route=callback&paytrail-payment-service-action=index

---

**_Disclaimer:_** *This open source plugin is provided to help merchants get started with our payment service. However, we do not offer any warranty or guarantee that the plugin will work as intended and provide limited support for it. Use at your own risk.*
