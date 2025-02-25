# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.1] - 2025-02-25
### Added
- Added stamp for rounding row item
- ### Fixed
- Fixed payment status after failed payment

## [2.4.0] - 2025-02-10
### Added
- Added possibility to display OP Lasku calculator in product and cart page
- ### Fixed
- Fixed click listener on shortcode checkout

## [2.3.0] - 2025-01-22
### Added
- Added Apple Pay support for payment page bypass
- Added supported currency notice

## [2.2.3] - 2024-12-19
### Added
- Added gateway check for blocks checkout

## [2.2.2] - 2024-12-17
### Fixed
- Fixed refund query

## [2.2.1] - 2024-12-09
### Fixed
- Fixed subscription/saved card deletion

## [2.2.0] - 2024-12-02
### Added
- Added checkout blocks support

## [2.1.2] - 2024-11-06
### Added
- Added more validation for transactions in payment response

## [2.1.1] - 2024-08-05
### Fixed
- Fixed VAT rounding problem caused by certain product price settings

## [2.1.0] - 2024-08-01
### Added
- Added support for decimal values in tax rate
- Added Finnish bank reference option
- Allow selection of HTML tags in the payment description view
### Fixed
- Minor fixes

## [2.0.3] - 2024-05-02
### Fixed
- Fixed test mode enablement

## [2.0.2] - 2024-04-18
### Fixed
- Fixed test mode settings

## [2.0.1] - 2024-04-18
### Removed
- Removed option to generate Finnish bank reference numbers for transactions

## [2.0.0] - 2024-04-17
### Added
- Added [WooCommerce HPOS](https://woocommerce.com/document/high-performance-order-storage/) support
- Added new customer on-boarding flow
- Added option to generate Finnish bank reference numbers for transactions
### Fixed
- Improved error logging
- Changed the Cancel URL to be the 'Pay for Order' page

## [1.3.11] - 2023-12-21
### Fixed
- Fixed tax calculations

## [1.3.10] - 2023-11-23
### Fixed
- Fixed payment card value fields

## [1.3.9] - 2023-10-05
### Fixed
- Fix card form

## [1.3.8] - 2023-10-03
### Fixed
- Fix rounding rows again

## [1.3.7] - 2023-09-29
### Fixed
- Fix rounding row negative amount fix

## [1.3.6] - 2023-09-29
### Fixed
- Fix rounding row negative amount

## [1.3.5] - 2023-09-22
### Fixed
- Fixed item amount type missmatch

## [1.3.4] - 2023-09-21
### Added
- Added WP code linter tests

## [1.3.3] - 2023-04-11
### Added
- Added update status to canceled on each error case when handling refunds
### Fixed
- Fixed possible race condition between redirect and callback payment processing
- Home url cleaned with esc_url

## [1.3.2] - 2023-02-23
### Added
- Added 3 second delay to Paytrail API callbacks to avoid race condition in cluster environments
- Company name field added to API calls when available

## [1.3.1] - 2022-12-12
### Fixed
- Loosened comparisons on the condition related to tax calculations when taxes amount to zero

## [1.3.0] - 2022-12-08
### Added
- Add support for manual renewals of Subscriptions
### Fixed
- Fix an issue with rounding taxes for small amounts
- Fix calculation with zero taxes
### Changed
- The order->payment_complete now happens later than before

## [1.2.4] - 2022-10-26
### Fixed
- Fixed an issue with rounding taxes for small amounts

## [1.2.3] - 2022-09-05
### Added
- Make it possible to tweak paytrail order items via apply_filters
### Changed
- PHP-SDK version updated to 2.3
- Updated various libraries
### Fixed
- Fixed language selection issue
- Checkout page login text tweak

## [1.2.2] - 2022-08-25
### Added
- Added login and registration instructions for guest users, so they can save card details
### Changed
- Updated supported features
### Fixed
- Hide empty payment method categories

## [1.2.1] - 2022-06-28
### Fixed
- Fixed an issue which prevented deleting saved card

## [1.2.0] - 2022-06-14
### Changed
- Updated Paytrail PHP-SDK to 2.2 version
### Fixed
- Fixed Guzzle version conflict issues by adding cUrl as a fallback option

## [1.1.0] - 2022-06-02
### Changed
- Updated Paytrail PHP-SDK version
- Removed symphony packages & composer installers

## [1.0.9] - 2022-03-16
### Added
- Added direct link to WooCommerce logs from the settings page
### Changed
- Improvements to order status checking
- Better logging for status changes and references

## [1.0.8] - 2022-02-11
### Fixed
- Update payment method image url

## [1.0.7.2] - 2021-12-08
### Fixed
- Added missing migration interface file

## [1.0.7] - 2021-12-08
### Added
- Added token migration functionality for WooCommerce Subscriptions orders

## [1.0.6] - 2021-12-02
### Added
- Added token migration functionality and upgrade instructions from old Checkout Finland plugin

## [1.0.5] - 2021-11-25
### Fixed
- Reverted a code change that broke customizer options

## [1.0.4] - 2021-11-04
### Changed
- Updated lock files

## [1.0.3] - 2021-11-04
### Added
- Added wordpress.org assets

## [1.0.2] - 2021-11-03
### Added
- Added even more escapes to variables and options

## [1.0.1] - 2021-11-02
### Changed
- Added more escapes to variables and options
- Renamed some CSS classes to improve compatibility with the old Checkout Finland for WooCommerce plugin

## [1.0.0] - 2021-10-20
### Added
- All initial plugin functionalities.
