# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.7] - 2021-09-30
### Changed
- PHP SDK updated to 1.5 version
- Removed Guzzle dependency

## [1.6] - 2021-06-09
### Changed
- Rebranding from "OP Payment Service" to "Checkout Finland"

## [1.5.15] - 2021-02-23
### Changed
- Add validation for order tokens when creating scheduled Subscription/MIT payment
- Add better error message if no tokens found
- Change PHP version to 7.4 in Github Action

## [1.5.14] - 2021-02-17
### Changed
- Update minimum PHP version to 7.3
- OP Payment Service PHP-SDK version updated to 1.3.4

## [1.5.13] - 2021-02-16
### Changed
- OP Payment Service PHP-SDK version updated

## [1.5.12] - 2021-02-15
### Changed
- Add more logging to gateway
- Hide the payment method group title and expand the group if only one group is available

### Fixed
- Fix payment method description support
- Fix error handling when no payment provider chosen
- Fix initialising of payment method groups in provider form

## [1.5.11] - 2020-12-11
### Fixed
- Add missing register scripts

## [1.5.10] - 2020-12-11
### Fixed
- Updated dist script

## [1.5.9] - 2020-12-11
### Changed
- Added support for WP 5.6 and WC 4.8

## [1.5.8] - 2020-11-10
### Changed
- Added order locking in redirect/callback processing
- Improved logging

## [1.5.7] - 2020-09-11
### Changed
- Changed callback handling 
- Changed log reference

## [1.5.6] - 2020-09-08
### Changed
- Changed the payment page customization texts
- Add callback mode as parameter to gateway and move sleep to gateway, add order ids to log messages

### Fixed
- Fixed 'Highlighted payment method' custom color issue

## [1.5.5] - 2020-09-07
### Changed
- Changed to use the order number as a reference for the payment service providers instead of a random number
- Add delay to callback controller to prevent simultaneous processing of payments

## [1.5.3] - 2020-09-01
### Changed
- Add more debug logging to payment gateway processing
- Use scrolIntoView method after clicking payment method group button on checkout

## [1.5.2] - 2020-07-27
### Changed
- Add extra validation to check that wanted_provider is not null, otherwise use capitalized provider name from checkout-provider parameter.
- Change get_item_description function item description for product items to use either product name or product id
- Add composer.json to require guzzlehttp/guzzle version 6.5.4
- Update finnish translations

### Fixed
- Fix issue when using a gift card code after placing an order

## [1.5.1] - 2020-06-29
### Changed
- Add setting for fallback country in payment gateway settings 

## [1.5.0] - 2020-06-16
### Changed
- Add support for WooCommerce Subscriptions plugin
- Add tokenized card MIT payments functionalities
- Add new Helper class
- Add info and error logging for payment requests if debug log enabled
- Updated to the latest PHP-SDK version

## [1.4.1] - 2020-05-27
### Changed
- New added tokenized card automatically set as users default token payment method
- Updated to the latest PHP-SDK version

## [1.4.0] - 2020-05-26
### Changed
- Add support for adding and using tokenized cards as payment methods
- Add tokenized card CIT payments functionalities
- Add Callback controller class and implementation

## [1.3.1] - 2020-04-15
### Fixed
- Fixed the hosted payment method selection

## [1.3.0] - 2020-04-03
### Changed
- New layout for the payment method selection

## [1.2.8] - 2020-01-28
### Changed
- Bump SDK version to composer.lock 

## [1.2.7] - 2020-01-28
### Changed
- Simplified error handling
- Strict comparison to some validations
- Use mb_substr() to truncate the description

## [1.2.6] - 2020-01-08
### Changed
- Updated to the latest PHP-SDK version
- Version bump to plugin.php 

## [1.2.2] - 2020-01-08
### Changed
- Updated to the latest PHP-SDK version

### Added
- Custom title and description to be used on the Payment provider section.

## [1.2.1] - 2019-10-24
### Changed
- Small fixes

## [1.2.0] - 2019-10-22
### Changed
- Rebranding from Checkout Finland to OP Payment Services for WooCommerce

## [1.1.7] - 2019-10-04
### Fixed
- A bug where an error message from the provider list API would throw a fatal error to user.
- Better PHP version checking for plugin activation.
- Better error handling for when payment provider is not selected.

## [1.1.6] - 2019-09-24
### Fixed
- Order items without taxes no longer cause 500 error on checkout. [Fixes: #9](https://github.com/CheckoutFinland/woocommerce-checkout-finland-gateway/issues/9).

## [1.1.5] - 2019-08-28
### Removed
- Removed the manual installation guide from readme. Manual installation is not yet supported.

## [1.1.4] - 2019-08-28
### Added
- Pass the current order object for WooCommerce when creating the return url. This enables displaying the order data on a "thank you" page.

## [1.1.3] - 2019-06-12
### Changed
- Updated correct dependencies for composer/packagist.

## [1.1.2] - 2019-06-4
### Changed
- Use 1.0.0 tag for php-sdk instead of dev-master.

## [1.1.1] - 2019-05-16
### Added
- A feature that truncates the product description to a maximum of 1000 characaters when sending it to the Checkout Finland API.

## [1.1.0-beta] - 2019-04-24
### Added
- A feature to add a rounding row to the order in case the roundings are off by a few cents.

### Changed
- Better handling for the situation where WooCommerce is not activated when the user tries to activate the gateway.

## [1.0.7-beta] - 2019-04-15
### Fixed
- Correct version number to be passed to the API.
- Another small stylistical fix for the payment provider logo grid.

## [1.0.6-beta] - 2019-04-09
### Fixed
- A small stylistical fix for the payment provider logo grid.

## [1.0.5-beta] - 2019-04-09
### Fixed
- Payment gateway icon disabled due to size issues.
- Added max-width: 300px for the payment provider logos.

## [1.0.4-beta] - 2019-04-08
### Added
- A setting to choose between showing the payment provider selection in-store or rather in an external view by Checkout Finland.

## [1.0.2-beta] - 2019-03-29
### Added
- Plugin version header to be sent with all requests.

## [1.0.1-beta] - 2019-03-26
### Added
- Payment method groupings.
- Better order notes from the payment process.
- Pending payment status support.

## [1.0.0-beta] - 2019-03-25
### Added
- All initial plugin functionalities.
