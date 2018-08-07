=== Wordpress Bitcoin Payments - Blockonomics ===
Tags: bitcoin, accept bitcoin, bitcoin woocommerce, bitcoin wordpress plugin, bitcoin payments
Requires at least: 3.0.1
Tested up to: 4.9.1
Stable tag: 1.4.8
License: MIT
License URI: http://opensource.org/licenses/MIT

Accept bitcoin payments and altcoins on your WooCommerce website. Bitcoin payments go directly to your wallet.

== Description ==

The fastest and easiest way to start accepting Bitcoin payments on your Woocommerce online store. Since 2015, Blockonomics has helped thousands of ecommerce sites increase sales by including Bitcoin, Ethereum, Litecoin, and other major altcoins as a payment option for their customers.

= Blockonomics helps merchants sell more =
- Accept Bitcoin payments on your website with ease
- Safe and secure transactions
- No need for exchanges and middle men - coins go directly into your wallet
- Eliminate chargebacks and fraud


= Plugin features =
- Accept BTC and major altcoins on your website like ETH, XRP, BCH, LTC etc. using inbuilt shapeshift integration
- All major HD wallets like trezor, blockchain.info, mycelium supported
- No approvals of API key/documentation required
- Supports all major fiat currencies
- Complete checkout process happens within your website/theme 
- Quick and easy installation - [Installation Video Tutorial](https://www.youtube.com/watch?v=E5nvTeuorE4)

== Installation ==

- [Installation Video Tutorial](https://www.youtube.com/watch?v=E5nvTeuorE4)
- [Blog Tutorial](https://blog.blockonomics.co/how-to-accept-bitcoin-payments-on-woocommerce-using-blockonomics-f18661819a62)

= Blockonomics Setup =
- Complete [blockonomics merchant wizard](https://www.blockonomics.co/merchants) 
- Get API key from Wallet Watcher > Settings

= Woocommerce Setup =
- Make sure you have [woocommerce](https://wordpress.org/plugins/woocommerce/) plugin installed on your wordpress site
- Install plugin from [wordpress plugin directory](https://wordpress.org/plugins/blockonomics-bitcoin-payments/)
- Activate the plugin
- You should be able see Blockonomics submenu inside Settings.  
- Put Blockonomics API key here
- Copy callback url and put into blockonomics [merchants](https://www.blockonomics.co/merchants)

Try checkout product , and you will see pay with bitcoin option.
Use bitcoin to pay and enjoy !

== Frequently Asked Questions ==

= Getting error on checkout: Could not generate new bitcoin address , what to do ? =
Your webhost is probably blocking outgoing HTTPS connections. Blockonomics requires an outgoing HTTPS PORT (port 443) to generate new address. Check with your webhost to allow this. Consult this [article](https://blockonomics.freshdesk.com/solution/articles/33000215104-troubleshooting-unable-to-generate-new-address) for more details.

= Order is still on pending payment status even after two confirmations  =
Your webhost is blocking incoming callbacks from bots, our you have a DDOS protection in place that is causing this. Blockonomics server does payment callbacks to update trasnsaction status and cannot emulate a browser accessing your website. Remove the DDOS protection for blockonomics.co 

= I have multiple websites, how do I set this up? =
Just create a new xpub for each site and add to [blockonomics wallet watcher](https://www.blockonomics/blockonomics). In [merchants tab](https://www.blockonomics.co/merchants) you will get option to specify callback url for each of them.  Install this plugin on each of your sites and following the same setup procedure.  Thats it! You can monitor many sites under same blockonomics emailid.

== Screenshots ==

1. Checkout option

2. Payment screen

3. Settings Panel  

4. Blockonomics configuration  

== Changelog ==

= 1.4.8  =
* Improved error handling when unable to generate address

= 1.4.7  =
* Made compatible for internationalization through translate.wordpress.org

= 1.4.6  =
* Updated README for more description on bitcoin payments

= 1.4.5  =
* Fixed problem with visual composer themes
* Added extra help text on order confirmation page
* Fixed conflict with javascript method
* Updated plugin to fix problem with incorrect commit

= 1.4.4  =
* Fixed problem with visual composer themes
* Added extra help text on order confirmation page
* Fixed conflict with javascript method

= 1.4.3  =
* Added option to configure timeperiod of checkout timer
* Added functionality to regenerate callback URL
* Updates to README and snapshots

= 1.4.2 =
* Fixed bug with conflicting style of spinner

= 1.4.1 =
* Moved all styles to CSS file. Gives ability to control plugin appearance
* Comptatibility with WP 4.9.1

= 1.4.0 =
* Usability improvements to payment screen
* Added Spanish, french and german translation

= 1.3.9 =
* Support for altcoin payments through shapeshift (You need to enable this from Settings)
* Not marking order as failed on overpayment 
* Minified JS files and removed unused ones

= 1.3.8 =
* Support for altcoin payments through shapeshift (You need to enable this from Settings)
* Not marking order as failed on overpayment 

= 1.3.7 =
* Added paid/expected BTC custom fields 
* Updated checkout icon 

= 1.3.6 =
* Improved payment screen user interface
* Comptability with WP 4.8 
* Updated README

= 1.3.5 =
* Improved payment screen user interface 
* Updated README

= 1.3.4 =
* Fixed github repo URL
* Updated README

= 1.3.2  =
* Change in README

= 1.3.1  =
* Minor change in README

= 1.3  =
* Showing errors when unable to generate new address
* Removed unused code

= 1.2 =
* Showing received order page after receiving payment
* fixes problems with multisite and php7 compatibility

