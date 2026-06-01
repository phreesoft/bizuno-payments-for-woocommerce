=== Bizuno Payments for WooCommerce ===
Contributors: phreesoft
Donate link: https://www.bizuno.com/donate/
Tags: payment-gateway, credit-card, payfabric, purchase-order, woocommerce
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 7.4.2
License: AGPL-3.0-or-later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

Add PayFabric credit-card payments and a business Purchase Order method to WooCommerce checkout, integrated with Bizuno Accounting/ERP.

== Description ==

**Bizuno Payments for WooCommerce** adds two payment methods to your WooCommerce checkout:

* **PayFabric credit-card gateway** — accept credit-card payments through PayFabric (Global Payments), using a hosted payment page so card data never touches your server.
* **Purchase Order** — an offline method that lets approved business customers pay by company purchase order. It is also handy for testing checkout.

It is designed to work alongside the Bizuno Accounting/ERP platform so payments and orders flow into your books, but the payment methods themselves work in any WooCommerce store.

= Features =

* PayFabric hosted payment page (live and sandbox/test modes).
* Purchase Order offline payment method with configurable title, description and instructions.
* WooCommerce High-Performance Order Storage (HPOS) compatible.
* Works standalone or together with the Bizuno Accounting portal.

== Installation ==

1. Install and activate **Bizuno Payments for WooCommerce** from the WordPress Plugins screen. WooCommerce must be installed and active.
2. Go to **WooCommerce → Settings → Payments**.
3. Enable and configure **PayFabric** (enter your PayFabric merchant credentials; choose test or live mode) and/or the **Purchase Order** method.
4. Save. The enabled methods now appear at checkout.

For Bizuno Accounting users, activate alongside the Bizuno portal so payments reconcile with your books.

== Frequently Asked Questions ==

= What payment methods does this add? =
A PayFabric credit-card gateway and an offline business Purchase Order method.

= Do I need a PayFabric account? =
Yes, to accept credit cards you need PayFabric (Global Payments) merchant credentials. The Purchase Order method does not require any third-party account.

= Is card data stored on my server? =
No. PayFabric uses a hosted payment page, so card details are entered on PayFabric's secure page, not your site.

= Does it work with WooCommerce HPOS? =
Yes. The plugin declares compatibility with High-Performance Order Storage.

= Do I need Bizuno Accounting? =
No. The payment methods work in any WooCommerce store. Bizuno Accounting adds full back-office reconciliation.

== External services ==

This plugin connects to the **PayFabric** payment gateway (Global Payments) to process credit-card payments. It does not transmit any data until a customer chooses the PayFabric method and submits a payment.

When a PayFabric payment is made, order and payment information required to authorize the transaction — such as the order amount, currency, order identifier and billing details — is sent to PayFabric so the hosted payment page can be displayed and the charge processed.

* Live endpoint: https://www.payfabric.com
* Test/sandbox endpoint: https://sandbox.payfabric.com

Use of PayFabric is subject to PayFabric/Global Payments terms and privacy policy:

* Terms: https://www.payfabric.com
* Privacy: https://www.globalpayments.com/privacy

The Purchase Order method is fully local and contacts no external service.

== Screenshots ==

1. WooCommerce → Settings → Payments showing the PayFabric and Purchase Order methods.
2. PayFabric gateway settings: merchant credentials and test/live mode.
3. Purchase Order method settings: title, description and customer instructions.
4. Checkout with the PayFabric credit-card option selected.
5. Checkout with the Purchase Order option selected.

== Changelog ==

= 7.4.2 =
* Renamed to "Bizuno Payments for WooCommerce" for the WordPress.org Plugin Directory; text domain aligned to the slug.
* Now a standalone WordPress plugin (no Bizuno core library dependency).
* Declares WooCommerce HPOS (custom order tables) compatibility.
* Declares WooCommerce as a required plugin and adds WooCommerce version compatibility headers.
* Removed unused/duplicate bundled files.

= 6.7 =
* PayFabric hosted payment page integration and Purchase Order method.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 7.4.2 =
Plugin renamed and now standalone. After upgrading, confirm your PayFabric settings under WooCommerce → Settings → Payments.

== About PhreeSoft ==

PhreeSoft has built open-source accounting and ERP tools since creating PhreeBooks in 2007. **Bizuno** is our modern flagship; this plugin brings PayFabric and Purchase Order payments to your WooCommerce store.

* https://www.phreesoft.com
* https://www.bizuno.com
* https://github.com/phreesoft/bizuno-payments-for-woocommerce
