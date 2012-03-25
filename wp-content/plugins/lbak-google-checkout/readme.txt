=== LBAK Google Checkout ===
Contributors: samwho
Author: Sam Rose
Author URL: http://lbak.co.uk/
Donate link: http://donate.lbak.co.uk/
Tags: e-commerce, google, checkout, sell, products, easy
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.3.4

A simple, easy to use Google Checkout integration WordPress plugin.

== Description ==

IMPORTANT NOTE: The original author of this plugin no longer maintains it. If you
wish to take over the project, please feel free to get in touch.

The LBAK Google Checkout plugin is aimed mainly at small projects that just want
to sell a few things on their blog through Google Checkout and don't want too
much else apart from ease of use.

This plugin is really easy to set up and you will be selling your products
within 20 minutes of installing it. It offers:

* An easy way of integrating Google Checkout into your WordPress blog.
* An easy way to add products to a database on your blog and display them in
your blog posts with short codes.
* Customisable settings and CSS to help you get the look and feel that you want.
* An option to totally customise how your product looks using the Google
Checkout API if you feel comfortable doing that.
* Very little excess and clutter.

== Installation ==

If you do not have a Google Checkout account already then you need to sign up
for one before you start with this plugin. Go to http://checkout.google.com
and follow their steps for signing up.

Once you have your Google Checkout account, you want to install this plugin
either using the WordPress installer or by downloading the .zip file and
extracting it under wp-content/plugins/lbak-google-checkout then activate it
in the plugins menu in your WordPress admin panel and find the Google Checkout
link under the "Tools" menu, click on it and enter the following details:

1. Your Merchant ID. This has to be set for the plugin to function. To find it,
go to your Google Checkout account and look in the top right hand corner of the
page, it should be there. Alternately you will find it in Settings > Profile.

2. The currency you are using. It's essential that you get this right as the
plugin will not work if you specify an incorrect currency. If you are in the UK,
enter "GBP" (no quotes) into the box and if you are in the US enter "USD"
(no quotes) into the box. If you want to use any other currencies you will need
to look up how to do that in Google's documentation (I'm not sure if they have
support for any other currencies yet).

3. The rest of the settings on the Settings page can be left default if you want
but I recommend having a play around with them to get a real feel for what they
do and how they work. This will make it easier for you to customise how you
want the plugin to look and feel.

4. That should be it! You're now ready to start adding products to your database
and selling things. If you have any further queries please consult the Help/FAQ
page in the plugin's menu.

== Frequently Asked Questions ==

The frequently asked questions can be found either inside the plugin or at
http://lbak.co.uk/faq.php?step=get&tag=lbakgc

== Changelog ==

= 1.3.4 =

* As of this release, the original author provides no official support for
this plugin. If you wish to take over writing it, please get in touch.

= 1.3.3 =

* Fixed another bug with the auto updating of the custom html field.
* Added the ability to add a custom dropdown box for things such as colour.

= 1.3.2 =

* Fixed a bug that kept adding </div> to the end of custom html and breaking the html of the page. Kudos to Cameron for the bug report.

= 1.3.1 =

* Fixed a display bug regarding GBP.

= 1.3 =

* Added a variety of validation checking to stop false reports of success from
happening.
* Added the ability to add a User Input field to your products (useful for
adding fields such as engravings)
* Internal file restructuring to help with readability.
* Added in usage tracking to help with debugging in future. There is an opt-out
option on the settings page.
* Made the Help/FAQ still load if your server does not have cURL loaded or
allow_url_fopen set.
*Added some more indexes to the database to allow for faster load times when
displaying products.
* Removed some deprecated files that were causing errors on some systems.

= 1.2.2 =

* Fixed a bug with the database update script.

= 1.2.1 =

* Another CSS bug fix. Who knows, it might work this time.
* Added another setting to the settings panel. The ability to change the title
colour for products.

= 1.2 =

* Potential bug fix loading CSS.
* Added the ability to have a dropdown box of prices.

= 1.1 =

* Fixed a display bug regarding the use of USD as a currency.
* Introduced a new interactive FAQ system.

= 1.0 =

* First stable release.
