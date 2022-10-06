# Mi Cuenta Web for Magento 2

Mi Cuenta Web for Magento 2 is an open source plugin that links e-commerce websites based on Magento to Mi Cuenta Web secure payment gateway developed by [Lyra Network](https://www.lyra.com/).

Namely, it enables the following payment methods:
* Mi Cuenta Web - Standard payment
* [mutli] Mi Cuenta Web - Payment in installments
* [gift] Mi Cuenta Web - Gift card payment
* [choozeo] Mi Cuenta Web - Choozeo payment
* [oney] Mi Cuenta Web - Payment in 3 or 4 times Oney
* [fullcb] Mi Cuenta Web - Full CB payment
* [franfinance] Mi Cuenta Web - Franfinance payment
* [sepa] Mi Cuenta Web - SEPA payment
* [paypal] Mi Cuenta Web - PayPal payment
* [other] Mi Cuenta Web - Other payment means

## Installation & upgrade

- Remove app/code/Lyranetwork/Micuentaweb folder if already exists.
- Create a new app/code/Lyranetwork/Micuentaweb folder.
- Unzip module in your Magento 2 app/code/Lyranetwork/Micuentaweb folder.
- Open command line and change to Magento installation root directory.
- Enable module: php bin/magento module:enable --clear-static-content Lyranetwork_Micuentaweb
- Upgrade database: php bin/magento setup:upgrade
- Re-run compile command: php bin/magento setup:di:compile
- Update static files by: php bin/magento setup:static-content:deploy [locale]

In order to deactivate the module: php bin/magento module:disable --clear-static-content Lyranetwork_Micuentaweb

## Configuration

- In Magento 2 administration interface, browse to "STORES > Configuration" menu.
- Click on "Payment Methods" link under the "SALES" section.
- Expand Mi Cuenta Web payment method to enter your gateway credentials.
- Refresh invalidated Magento cache after config saved.

## License

Each Mi Cuenta Web payment module source file included in this distribution is licensed under the Open Software License (OSL 3.0).

Please see LICENSE.txt for the full text of the OSL 3.0 license. It is also available through the world-wide-web at this URL: https://opensource.org/licenses/osl-3.0.php.