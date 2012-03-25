=== Global Translator ===
Tags: translator, multilanguage, automatic translator, google translations, babelfish, promt, freetranslations, widget
Author: Davide Pozza
Contributors:
Donate link: http://www.n2h.it/donate_global_translator.php
Requires at least: 2.3
Tested up to: 3.*
Stable Tag: 1.3.2

Automatically translates your blog in 48 different languages!

== Description ==

Global Translator automatically translates your blog in the following 48 different languages:
Italian, Korean, Chinese (Simplified), Portuguese, English, German, French, Spanish, Japanese, 
Arabic, Russian, Greek, Dutch, Bulgarian, Czech, Croatian, Danish, Finnish, Hindi, Polish, Romanian, 
Swedish, Norwegian, Catalan, Filipino, Hebrew, Indonesian, Latvian, Lithuanian, Serbian, Slovak, 
Slovenian, Ukrainian, Vietnamese,Albanian,Estonian,Galician,Maltese,Thai,Turkish,Hungarian.

The number of available translations will depend on your blog language and the translation engine you will chose to use.
Main features:

* **Four different translation engines**: Google Translation Engine, Babel Fish, Promt, FreeTranslations
* **Search Engine Optimized**: it uses the permalinks by adding the language code at the beginning of all your URI. 
	For example the english version on www.domain.com/mycategory/mypost will be automatically transformed in 
	www.domain.com/en/mycategory/mypost 
* **Fast Caching System**: new fast, smart, optimized, self-cleaning and built-in caching system. Drastically reduction of the risk of temporarily ban from translation engines. 
* **Fully configurable layout**: you can easily customize the appearance of the translation bar by choosing between a TABLE 
	or DIV layout for the flags bar and by selecting the number of translations to make available to your visitors 
* **No database modifications**: Global Translator is not intrusive. It doesn't create or alter any table on your database: this feature permits to obtain better performances.

**Global Translator is the first real (and free) traffic booster for your blog!**
It can help you to reach a lot of new users and consequently to strongly increase your popularity: if you derive some benefits and if you want to support the development, 
please consider to support me with a donation.

For the latest information and changelog visit the website

http://www.n2h.it/wp-plugins/wordpress-global-translator-plugin/



== Installation ==

1. 	Upload the folder "global-translator" to the "wp-content/plugins" directory.
2. 	Activate the plugin through the 'Plugins' menu in WordPress. 
3.	From the main menu choose "Options->Global Translator" and select 
		your blog language and your preferred configuration options then select "Update Options".

**How to upgrade**

If upgrading from 0.9 or higher, just overwrite the previous version (don't delete the "cache" directory!!), otherwise uninstall the previous 
version and follow the Installation instructions; don't use the automatic upgrade feature or your cache will be erased!

Starting from 1.0.8 the cache directory has been moved outside the plugin directory in order to support the Wordpress automatic pluging update feature:
the content of the previous existing cache (inside wp-content/plugins/global-translator/cache) will be automatically and progressively moved to the new 
cache location (wp-content/gt-cache) but, if you want, you can manually change the cache location by using your ftp client or a shell access.

== Configuration ==

If your theme is widged-enabled, just choose "Presentation->Widgets" from the administration main menu
and drag the "Global translator" widget on the preferred position on your sidebar.
If your theme is not widgetized, just add the following php code (usually to the sidebar.php file):  

<?php if(function_exists("gltr_build_flags_bar")) { gltr_build_flags_bar(); } ?>

After this simple operation, a bar containing the flags that represents all the available translations 
for your language will appear on your blog.

== Changelog ==

= 1.3.2 =
* Fixed url fragments cleaning

= 1.3.1 =
* Removed N2H Link
* Fixed regexp patterns

= 1.3 =
* Added new option "Not yet translated pages management": you can choose between a 302 redirect and a 503 error on not yet translated pages
* Better flags layout on admin page

= 1.2.8 =
* fixed some 404 issues reported by Google WebMaster and related to bad parameters attached to the url (usg and rurl)

= 1.2.7 =
* Added 6 new languages

= 1.2.6 =
* Improvements on link cleaning
* default cache expire time switched to 15 days
* replaced 503 HTTP code ("Network Temporarily Unreachable") with a 302 redirect on not yet translated pages in order to remove the warning messages on GooGle WebMaster Tool

= 1.2.5.1 =
* some fixes on the new cleaning system

= 1.2.5 =
* updated page cleaning system in order to prevent new Google updates on the HTML sources

= 1.2.4 =
* Fixed trailing slash issue
* Replaced 404 errors with 302 redicection for better SEO
* Other fixes and optimizations

= 1.2.3 =
* Fixed sitemap integration for blogs not installed on the root path
* Fixed encoding problems related to the introduction of the new Google APIs

= 1.2.2.1 =
* Hacked new Google URL structure
* Added support for older PHP versions

= 1.2.1 =
* Added seven new languages: Albanian,Estonian,Galician,Maltese,Thai,Turkish,Hungarian
* Improved caching performances
* Added Show/Hide button for statistics on options page
* Optimized connections to google translation engine

= 1.2 =
* Fixed Chinese (Traditional) translation

= 1.1.2 =
* New configuration feature: flags bar in a single image (based on contribution by Amir - http://www.gibni.com)
* Translated Portuguese languages array (Thanks to Henrique Cintra)
* Added Chinese (Traditional) translation
* Fixed "division by zero" error
* Fixed image map configuration error

= 1.0.9.2 =
* Better IIS url rewriting support
* Fixed Norwegian configuration
* Moved shared function to header.php

= 1.0.9.1 =
* Changed HTTP error for not yet translated pages from 404 to 503 (Service Temporarily Unavailable)

= 1.0.9 =
* Added 404 error code for not yet translated pages
* Added support for IIS rewrite rules (based on the usage of "/index.php" at the beginning of the permalink)
* other performances improvements

= 1.0.8.1 =
* little fix for cached pages count on options page

= 1.0.8 =
* general performance improvement
* added check for blocking nested translation requests (i.e. www.mysite/en/fr/...)
* fixed A tags rendering
* moved cache dir outside the plugin dir
* fixed options page access problem
* fixed trailing slash issue

= 1.0.7.1 =
* removed call to "memory_get_usage" on debug method because it is not supported
  by certain php versions

= 1.0.7 =
* Added cache compression
* fixed layout bugs
* fixed link building problem (internal anchors not working)
* Added 11 new languages to Google Translation Engine!
 
= 1.0.6 =
* Added new optional cache invalidation time based parameter

= 1.0.5 =
* Random User Agent selection for translation requests
* Hacked new Google block introduced on (27th of August 2008)

= 1.0.4 =
* Performances improvement in cache cleaning algorithm
* fixed the sitemap plugin detection function
* fixed javascript errors on translated pages

= 1.0.3 =
* Added Debug option on the admin area
* Added Connection Interval option on the admin area
* Added more detailed messages and info on the admin page
* Updated new Promt translation url
* Fixed some issues about cache cleaning for blogs not using the permalinks
* Added experimental sitemap integration

= 1.0.2 =
* Fixed cache issue with blogs not using the pemalinks

= 1.0.1 =
* Fixed tags issue with older Wordpress versions (2.3.*)

= 1.0 =
* Improved cleaning system for translated pages
* New fast, smart, optimized, self-cleaning and built-in caching system. Drastically reduction of temporarily ban risk
* Added Widget Title
* Added 404 error page for deactivated translations

= 0.9.1.1 =
* Bug fix: Google translation issue

= 0.9.1 =
* Added file extension exclusion for images and resources (they don't need to be translated)
* Activated new Prompt configuration
* Fixed little issue with Portuguese translation
* Fixed Swedish, Arabic and Czech flags icons (thanks to Mijk Bee and Nigel Howarth)
* Added new (and better) event-based cache invalidation system

= 0.9 =
* Added support for 10 new languages for Google Translations engine: Bulgarian, Czech, Croat, Danish, Finnish, Hindi, Polish, Rumanian, Swedish, Greek, Norwegian
* Updated flags icons (provided by famfamfam.com)

= 0.8 =
* Updated Prompt engine
* Added experimental translation engines ban prevention system
* Improved caching management
* Improved setup process
* Fixed a bug on building links for "Default Permalink Structure"

= 0.7.2 =
* Fixed other bug on building links for "Default Permalink Structure"
* Optimized translation flags for search engines and bots
* changed cached filename in order to prevent duplicates
* added messages for filesystem permissions issues
* updated Google translation languages options (added Greek and Dutch)

= 0.7.1 =
* Fixed bug "Call to a member function on a non-object in /[....]/query.php". 
  It happens only on certain servers with a custom PHP configuration
* Fixed bug on building links for "Default Permalink Structure"

= 0.7 =
* Added two new translation engines: FreeTranslation and Promt Online Translation
* Added USER-AGENT filter in order to prevent unuseless connections to the translation services
* Added support for Default Permalink Structure (i.e.: "www.site.com/?p=111")
* Added widgetization: Global Translator is now widgetized!
* Fixed some bugs and file permission issues
* Excluded RSS feeds and trackback urls translation
* Fixed some problems on translated pages 

= 0.6.2 =
* Updated in order to handle the new Babelfish translation URL.(Thanks to Roel!)

= 0.6.1 =
* Fixed some layout issues
* Fixed url parsing bugs

= 0.6 =
* Fixed compatibility problem with Firestats
* Added the "gltr_" prefix for all the functions names in order to reduce naming conflicts with other plugins
* Added new configuration feature: now you can choose to enable a custom number of translations
* Removed PHP short tags
* Added alt attribute for flags IMG
* Added support to BabelFish Engine: this should help to solve the "403 Error" by Google
* Added my signature to the translation bar. It can be removed, but you should add a link to my blog on your blogroll.
* Replaced all the flags images
* Added help messages for cache support
* Added automatic permalink update system: you don't need to re-save your permalinks settings
* Fixed many link replacement issues
* Added hreflang attribute to the flags bar links
* Added id attribute to <A> Tag for each flag link
* Added DIV tag for the translation bar
* Added support for the following new languages: Russian, Greek, Dutch

= 0.5 =
* Added BLOG_URL variable
* Improved url replacement
* Added caching support (experimental): the cached object will be stored inside the following directory: "[...]/wp-content/plugins/global-translator/cache".
* Fixed japanese support (just another bug)

= 0.4.1 = 
* Better request headers
* Bug fix: the translated page contains also the original page

= 0.4 =
* The plugin has been completely rewritten
* Added permalinks support for all the supported languages
* Added automatic blog links substitution in order to preserve the selected language.
* Added Arabic support
* Fixed Japanese support
* Removed "setTimeout(180);" call: it is not supported by certain servers
* Added new option which permits to split the flags in more than one row

= 0.3/0.2 =
* Bugfix version
* Added Options Page

= 0.1 =
* Initial release


== Frequently Asked Questions ==

= The full translation process takes a lot of time. Why? =

In order to prevent from banning by the translation services, only a translation request every 5 minutes will be allowed. This will permit to fully translate
your blog whithout any interruption; this message will completely disappear when all the pages of your blog will be cached.
Remember that this message will also appear if you're currently being banned by the translation engine: this could happen if for example your blog shares the
same ip address with other blogs using older versions of Global Translator.

= When I click on a flag I'm just redirected to Google Translation Services =

Don't worry: this is just a TEMPORARY redirect which will disappear when the page will be cached and saved on your server: in fact, in order to avoid banning issues, the plugin translates and caches a page every 5 minutes

= "This page has not been translated yet. The translation process could take a while: please come back later." message when trying to access a translated page =

Upgrade to 1.0.8 or later. Starting from 1.0.8, a browser asking for a not yet translated page will be warned and redirected in 5 seconds to the translation service 
page in order to provide a temporary "backup translation service". Obviously, when the page is translated and saved on your cache directory, this redirection 
will disappear and the translated and cleaned page will be served by your blog. Starting from version 1.2.9, this message has been removed

= When clicking on a translation flag the page doesn't translate =

This could be due to a change of the permalinks structure of your blog, to a conflict with another plugin or to a custom 
.htaccess file which doesn't permit Global Translator to add its custom permalink rules. Try to refresh the Global Translator 
rewrite rules just pressing the "Update Options" button from the Global Translator admin page. If the problem persists, 
try also to deactivate all the other existing plugins and check your .htaccess file and comment out all the non-standard rewrite rules. 
If you discover a conflicting plugin please send me an email (davide at nothing2hide.net).

= The translated page has a bad/broken layout =

This is due to the translation engine action. I cannot do anything in order to prevent this problems :-)
I suggest you to try all the translation engines in order to choose the best one for your blog layout

= I've just changed my permalinks structure or just upgraded Wordpress to a newer version and Global Translator doesn't translate anymore =

Everytime the permalinks structure of your blog changes, the custom rules previously added by Global Translator are overriden.
To solve the problem you must just refresh the Global Translator Options ("Update Options" button) on the administrative area.

= I've removed one or more available translations but the search engines continue to try to index the corresponding urls =

When you remove one or more translations, the plugin will begin to return a 404 Not Found for all the corresponding translated pages.
In order to notify a search engine that one or more urls are not available anymore you should add a deny rule on your robots.txt file.
For example if you decide to remove the German translation you should modify your robots.txt as follows:
User-agent: *
[....]
Disallow: /de/*

= How can I discover if my blog is currently banned by the translation engine? =

Go to the Global Translator admin page. If your blog has been temporarily banned, a warning message will appear inside the "Translation engine connection" section.

= I've just removed the plugin. How to fix all the SEO issues related to the translated pages which are not jet available? =
Put a rewrite rule like this on your .htaccess:
RedirectMatch 301 ^/(it|ko|zh-CN|zh-TW|pt|en|de|fr|es|ja|ar|ru|el|nl|zh|zt|no|bg|cs|hr|da|fi|hi|pl|ro|sv|ca|tl|iw|id|lv|lt|sr|sk|sl|uk|vi|sq|et|gl|mt|th|tr|hu|be|ga|is|mk|ms|fa)/(.*)$ http://www.mysite.com/$2