<?php
/*
Plugin Name: Global Translator
Plugin URI: http://www.n2h.it/wp-plugins/wordpress-global-translator-plugin/
Description: Automatically translates a blog in 48 different languages by wrapping four different online translation engines (Google Translation Engine, Babelfish Translation Engine, FreeTranslations.com, Promt). After uploading this plugin click 'Activate' (to the right) and then afterwards you must <a href="options-general.php?page=global-translator/options-translator.php">visit the options page</a> and enter your blog language to enable the translator.
Version: 1.3.2
Author: Davide Pozza
Author URI: http://www.n2h.it/
Disclaimer: Use at your own risk. No warranty expressed or implied is provided. The author will never be liable for any loss of profit, physical or psychical damage, legal problems. The author disclaims any responsibility for any action of final users. It is the final user's responsibility to obey all applicable local, state, and federal laws.

*/

/*  Copyright 2006  Davide Pozza  (email : davide@nothing2hide.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



/* Credits:
Special thanks also to: 
Jason F. Irwin, 
Ibnu Asad, 
Ozh, 
ttancm, 
Fable, 
Satollo, 
Nick Georgakis,
Kaizeku Ban
and the many others who have provided feedback, spotted bugs, and suggested improvements.
*/

/* *****INSTRUCTIONS*****

Installation
============
Upload the folder "global-translator" into your "wp-content/plugins" directory.
Log in to Wordpress Administration area, choose "Plugins" from the main menu, find "Global Translator" 
and click the "Activate" button. From the main menu choose "Options->Global Translator" and select 
your blog language and your preferred configuration options then select "Update Options".

Upgrading
=========
If upgrading from 0.9 or higher, just overwrite the previous version, otherwise uninstall the previous 
version and follow the Installation instructions.

Configuration
=============
If your theme is widged-enabled, just choose "Presentation->Widgets" from the administration main menu
and drag the "Global translator" widget on the preferred position on your sidebar.
If your theme is not widgetized, just add the following php code (usually to the sidebar.php file):  
<?php if(function_exists("gltr_build_flags_bar")) { gltr_build_flags_bar(); } ?>

After this simple operation, a bar containing the flags that represents all the available translations 
for your language will appear on your blog.

Uninstallation
==============
Log in to Wordpress Administration area, choose "Plugins" from the main menu, find the name of the 
plugin "Global Translator", and click the "Deactivate" button.


***********************


Change Log

1.3.2
- Fixed url fragments cleaning

1.3.1
- Removed N2H Link
- Fixed regexp patterns

1.3
- Added new option "Not yet translated pages management": you can choose between a 302 redirect and a 503 error on not yet translated pages
- Better flags layout on admin page

1.2.8
- fixed some 404 issues reported by Google WebMaster and related to bad parameters attached to the url (usg and rurl)

1.2.7
- Added 6 new languages

1.2.6
- Improvements on link cleaning
- default cache expire time switched to 15 days
- replaced 503 HTTP code ("Network Temporarily Unreachable") with a 302 redirect on not yet translated pages in order to remove the warning messages on GooGle WebMaster Tool

1.2.5.1
- some fixes on the new cleaning system

1.2.5
- updated page cleaning system in order to prevent new Google updates on the HTML sources

1.2.4
- Fixed trailing slash issue
- Replaced 404 errors with 302 redicection for better SEO
- Other fixes and optimizations

1.2.3
- Fixed sitemap integration for blogs not installed on the root path
- Fixed encoding problems related to the introduction of the new Google APIs

1.2.2.1
- Hacked new Google URL structure
- Added support for older PHP versions

1.2.1
- Added seven new languages: Albanian,Estonian,Galician,Maltese,Thai,Turkish,Hungarian
- Improved caching performances
- Added Show/Hide button for statistics on options page
- Optimized connections to google translation engine

1.2
- Fixed Chinese (Traditional) translation

1.1.2
- New configuration feature: flags bar in a single image (based on contribution by Amir - http://www.gibni.com)
- Translated Portuguese languages array (Thanks to Henrique Cintra)
- Added Chinese (Traditional) translation
- Fixed "division by zero" error
- Fixed image map configuration error

1.0.9.2
- Better IIS url rewriting support
- Fixed Norwegian configuration
- Moved shared function to header.php

1.0.9.1
- Changed HTTP error for not yet translated pages from 404 to 503 (Service Temporarily Unavailable)

1.0.9
- Added 404 error code for not yet translated pages
- Added support for IIS rewrite rules (based on the usage of "/index.php" at the beginning of the permalink)
- other performances improvements

1.0.8.1
- little fix for cached pages count on options page

1.0.8
- general performance improvement
- added check for blocking nested translation requests (i.e. www.mysite/en/fr/...)
- fixed A tags rendering
- moved cache dir outside the plugin dir
- fixed options page access problem
- fixed trailing slash issue

1.0.7.1
- removed call to "memory_get_usage" on debug method because it is not supported
  by certain php versions

1.0.7
- Added cache compression
- fixed layout bugs
- fixed link building problem (internal anchors not working)
- Added 11 new languages to Google Translation Engine!
 
1.0.6
- Added new optional cache invalidation time based parameter

1.0.5
- Random User Agent selection for translation requests
- Hacked new Google block introduced on (27th of August 2008)

1.0.4
- Performances improvement in cache cleaning algorithm
- fixed the sitemap plugin detection function
- fixed javascript errors on translated pages

1.0.3
- Added Debug option on the admin area
- Added Connection Interval option on the admin area
- Added more detailed messages and info on the admin page
- Updated new Promt translation url
- Fixed some issues about cache cleaning for blogs not using the permalinks
- Added experimental sitemap integration

1.0.2
- Fixed cache issue with blogs not using the pemalinks

1.0.1
- Fixed tags issue with older Wordpress versions (2.3.*)

1.0
- Improved cleaning system for translated pages
- New fast, smart, optimized, self-cleaning and built-in caching system. Drastically reduction of temporarily ban risk
- Added Widget Title
- Added 404 error page for deactivated translations

0.9.1.1
- Bug fix: Google translation issue

0.9.1
- Added file extension exclusion for images and resources (they don't need to be translated)
- Activated new Prompt configuration
- Fixed little issue with Portuguese translation
- Fixed Swedish, Arabic and Czech flags icons (thanks to Mijk Bee and Nigel Howarth)
- Added new (and better) event-based cache invalidation system

0.9
- Added support for 10 new languages for Google Translations engine: Bulgarian, Czech, Croat, Danish, Finnish, Hindi, Polish, Rumanian, Swedish, Greek, Norwegian
- Updated flags icons (provided by famfamfam.com)

0.8
- Updated Prompt engine
- Added experimental translation engines ban prevention system
- Improved caching management
- Improved setup process
- Fixed a bug on building links for "Default Permalink Structure"

0.7.2
- Fixed other bug on building links for "Default Permalink Structure"
- Optimized translation flags for search engines and bots
- changed cached filename in order to prevent duplicates
- added messages for filesystem permissions issues
- updated Google translation languages options (added Greek and Dutch)

0.7.1
- Fixed bug "Call to a member function on a non-object in /[....]/query.php". 
  It happens only on certain servers with a custom PHP configuration
- Fixed bug on building links for "Default Permalink Structure"

0.7
- Added two new translation engines: FreeTranslation and Promt Online Translation
- Added USER-AGENT filter in order to prevent unuseless connections to the translation services
- Added support for Default Permalink Structure (i.e.: "www.site.com/?p=111")
- Added widgetization: Global Translator is now widgetized!
- Fixed some bugs and file permission issues
- Excluded RSS feeds and trackback urls translation
- Fixed some problems on translated pages 

0.6.2
- Updated in order to handle the new Babelfish translation URL.(Thanks to Roel!)

0.6.1
- Fixed some layout issues
- Fixed url parsing bugs

0.6
- Fixed compatibility problem with Firestats
- Added the "gltr_" prefix for all the functions names in order to reduce naming conflicts with other plugins
- Added new configuration feature: now you can choose to enable a custom number of translations
- Removed PHP short tags
- Added alt attribute for flags IMG
- Added support to BabelFish Engine: this should help to solve the "403 Error" by Google
- Added my signature to the translation bar. It can be removed, but you should add a link to my blog on your blogroll.
- Replaced all the flags images
- Added help messages for cache support
- Added automatic permalink update system: you don't need to re-save your permalinks settings
- Fixed many link replacement issues
- Added hreflang attribute to the flags bar links
- Added id attribute to <A> Tag for each flag link
- Added DIV tag for the translation bar
- Added support for the following new languages: Russian, Greek, Dutch

0.5
- Added BLOG_URL variable
- Improved url replacement
- Added caching support (experimental): the cached object will be stored inside the following directory:
"[...]/wp-content/plugins/global-translator/cache".
- Fixed japanese support (just another bug)

0.4.1
- Better request headers
- Bug fix: the translated page contains also the original page

0.4
- The plugin has been completely rewritten
- Added permalinks support for all the supported languages
- Added automatic blog links substitution in order to preserve the selected language.
- Added Arabic support
- Fixed Japanese support
- Removed "setTimeout(180);" call: it is not supported by certain servers
- Added new option which permits to split the flags in more than one row

0.3/0.2
- Bugfix version
- Added Options Page

0.1
- Initial release

*/

require_once (dirname(__file__).'/header.php');

	
define('HARD_CLEAN', true);

define('FLAG_BAR_BEGIN', '<!--FLAG_BAR_BEGIN-->');
define('FLAG_BAR_END', '<!--FLAG_BAR_END-->');
define('USER_AGENT','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20021204');

define('LANGS_PATTERN', 'it|ko|zh-CN|zh-TW|pt|en|de|fr|es|ja|ar|ru|el|nl|zh|zt|no|bg|cs|hr|da|fi|hi|pl|ro|sv|ca|tl|iw|id|lv|lt|sr|sk|sl|uk|vi|sq|et|gl|mt|th|tr|hu|be|ga|is|mk|ms|fa');
define('LANGS_PATTERN_WITH_SLASHES', '/it/|/ko/|/zh-CN/|/zh-TW/|/pt/|/en/|/de/|/fr/|/es/|/ja/|/ar/|/ru/|/el/|/nl/|/zh/|/zt/|/no/|/bg/|/cs/|/hr/|/da/|/fi/|/hi/|/pl/|/ro/|/sv/|/ca/|/tl/|/iw/|/id/|/lv/|/lt/|/sr/|/sk/|/sl/|/uk/|/vi/|/sq/|/et/|/gl/|/mt/|/th/|/tr/|/hu/|/be/|/ga/|/is/|/mk/|/ms/|/fa/');
define('LANGS_PATTERN_WITHOUT_FINAL_SLASH', '/it|/ko|/zh-CN|/zh-TW|/pt|/en|/de|/fr|/es|/ja|/ar|/ru|/el|/nl|/zh|/zt|/no|/bg|/cs|/hr|/da|/fi|/hi|/pl|/ro|/sv|/ca|/tl|/iw|/id|/lv|/lt|/sr|/sk|/sl|/uk|/vi|/sq|/et|/gl|/mt|/th|/tr|/hu|/be|/ga|/is|/mk|/ms|/fa');


define('CONN_INTERVAL', get_option('gltr_conn_interval'));
define('DEBUG', get_option('gltr_enable_debug'));
define('DEBUG_UA', false);
define('BASE_LANG', get_option('gltr_base_lang'));
define('BAR_COLUMNS', get_option('gltr_col_num'));
define('BAN_PREVENTION', get_option('gltr_ban_prevention'));
define('HTML_BAR_TAG', get_option('gltr_html_bar_tag'));
define('TRANSLATION_ENGINE', get_option('gltr_my_translation_engine'));
define('SITEMAP_INTEGRATION', get_option('gltr_sitemap_integration'));
define('EXPIRE_TIME', get_option('gltr_cache_expire_time'));
define('COMPRESS_CACHE', get_option('gltr_compress_cache'));
define('BLOG_HOME', get_settings('home'));
define('BLOG_HOME_ESCAPED', str_replace('/', '\\/', BLOG_HOME));
define('NOT_FOUND',
'<html><head><title>404 Not found</title></head>
<body><center><h2>404 Error: content not found</h2></center></body></html>');
define('USE_302', get_option('gltr_use_302'));

$gltr_result = '';
$gltr_engine = $gltr_available_engines[TRANSLATION_ENGINE];

add_filter('query_vars', 'gltr_insert_my_rewrite_query_vars');
add_action('parse_query', 'gltr_insert_my_rewrite_parse_query',-1);//this action should have the maximum priority!
add_action('admin_menu', 'gltr_add_options_page');
add_action('init', 'gltr_translator_init');

add_action('save_post', 'gltr_erase_common_cache_files');
add_action('delete_post', 'gltr_erase_common_cache_files');
//add_action('comment_post', 'gltr_erase_common_cache_files');
//add_action('publish_phone', 'gltr_erase_common_cache_files');
//add_action('trackback_post', 'gltr_erase_common_cache_files');
//add_action('pingback_post', 'gltr_erase_common_cache_files');
//add_action('edit_comment', 'gltr_erase_common_cache_files');
//add_action('wp_set_comment_status', 'gltr_erase_common_cache_files');
//add_action('delete_comment', 'gltr_erase_common_cache_files');
//add_action('switch_theme', 'gltr_erase_common_cache_files');



if (SITEMAP_INTEGRATION) add_action("sm_buildmap","gltr_add_translated_pages_to_sitemap");

function gltr_translator_init() {
  global $wp_rewrite;
  if (isset($wp_rewrite) && $wp_rewrite->using_permalinks()) {
    define('REWRITEON', true);
    define('LINKBASE', $wp_rewrite->root);
  } else {
    define('REWRITEON', false);
    define('KEYWORDS_REWRITEON', '0');
    define('LINKBASE', '');
  }
  
  if (isset($_GET['rurl']) || isset($_GET['usg']) || strstr(gltr_get_self_url(), "/&rurl")){
    gltr_debug("gltr_translator_init :: stripping usg or rurl: " . gltr_get_self_url());          
    $url = gltr_strip_get_param(gltr_get_self_url(),array('rurl','usg'));
    header( "HTTP/1.1 301 Moved Permanently" );
    header( "Location: $url" );
    die();  
  } 
  
  if (REWRITEON) {
    add_filter('generate_rewrite_rules', 'gltr_translations_rewrite');
  }
  /*
	if (isset($_GET['gltr_redir'])){
		$resource = urldecode($_GET['gltr_redir']);
		gltr_debug("gltr_translator_init :: found gltr_redir=$resource");
		gltr_make_server_redirect_page($resource);
	}
	*/
}

function gltr_strip_get_param($url,$params){
  if (!is_array($params)) $params = array($params);
  foreach($params as $param){    
   $url = preg_replace("/[\?&]{1}$param=[^&?]*/i", "", $url);
  }
  return $url;
}

function gltr_add_translated_pages_to_sitemap() {
	global $gltr_uri_index;
	$start= round(microtime(true),4);
	@set_time_limit(120);
  global $wpdb;
	if (gltr_sitemap_plugin_detected()){
		$generatorObject = &GoogleSitemapGenerator::GetInstance();
	  $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_password='' ORDER BY post_modified DESC");
  	$chosen_langs = get_option('gltr_preferred_languages');
  	
  	//homepages
		foreach($chosen_langs as $lang){
			$trans_link = "";
			if (REWRITEON){
				$trans_link = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME . "/$lang/" , BLOG_HOME );
			} else {
				$trans_link = BLOG_HOME . "?lang=$lang";
			}
			if (gltr_is_cached($trans_link,$lang))
				$generatorObject->AddUrl($trans_link,time(),"daily",1);
		}
		
		//posts
    foreach($chosen_langs as $lang){
		foreach ($posts as $post) {
			$permalink = get_permalink($post->ID);			
				$trans_link = "";
				if (REWRITEON){
					$trans_link = preg_replace("/".BLOG_HOME_ESCAPED."/", BLOG_HOME . "/" . $lang, $permalink );
				} else {
					$trans_link = $permalink . "&lang=$lang";
				}
				if (gltr_is_cached($trans_link,$lang))
					$generatorObject->AddUrl($trans_link,time(),"weekly",0.2);
			}
			$gltr_uri_index[$lang] = array();//unset
		}
	}
  $end = round(microtime(true),4);
 	gltr_debug("Translated pages sitemap addition process total time:". ($end - $start) . " seconds");
	
}

function gltr_patch_translation_url($res) {
	
  if (TRANSLATION_ENGINE == 'google'){
    $maincont = gltr_http_get_content($res);
    $matches = array();
    preg_match( '/(\/translate_p[^"]*)"/',$maincont,$matches);
    $res = "http://translate.google.com" . $matches[1];
    $res = str_replace('&amp;','&', $res);    
    gltr_debug("gltr_patch_translation_url :: Google Patched: $res");
    
  } else if (TRANSLATION_ENGINE == 'babelfish'){
    $maincont = gltr_http_get_content( $res);
    $matches = array();
    preg_match( '/URL=(http:\/\/[0-9\.]*\/babelfish\/translate_url_content[^"]*)"/',$maincont,$matches);
    $res = $matches[1];
    $res = str_replace('&amp;','&', $res);    
    gltr_debug("gltr_patch_translation_url :: Babelfish Patched: $res");
    
  } else if (TRANSLATION_ENGINE == 'freetransl'){
    $tmp_buf = gltr_http_get_content("http://www.freetranslation.com/");
    $matches = array();
    preg_match('/<input type="hidden" name="username" id = "hiddenUsername" value="([^"]*)" \/>[^<]*<input type="hidden" name="password" id = "hiddenPassword" value="([^"]*)" \/>/',$tmp_buf,$matches);      
    $res .= "&username=$matches[1]&password=$matches[2]";
    gltr_debug("gltr_patch_translation_url :: FreeTransl Patched: $res");
  }
	return $res;
}

function gltr_build_translation_url($srcLang, $destLang, $urlToTransl) {
  global $gltr_engine;
  if (TRANSLATION_ENGINE == 'google'){
  	$urlToTransl = urlencode($urlToTransl);  
  }else if (TRANSLATION_ENGINE == 'babelfish'){	
		$urlToTransl = urlencode($urlToTransl);   
	}
  $tokens = array('${URL}', '${SRCLANG}', '${DESTLANG}');
  $srcLang = $gltr_engine->decode_lang_code($srcLang);
  $destLang = $gltr_engine->decode_lang_code($destLang);
  $values = array($urlToTransl, $srcLang, $destLang);
  $res = str_replace($tokens, $values, $gltr_engine->get_base_url());
  return $res;
} 

function gltr_clean_url_to_translate(){
  $url = gltr_get_self_url();
  $url_to_translate = "";

  $blog_home_esc = BLOG_HOME_ESCAPED;

  if (REWRITEON) {
    $contains_index = (strpos($url, 'index.php')!==false);
    if ($contains_index){
      $blog_home_esc .= '\\/index.php';
    }
    $pattern1 = '/(' . $blog_home_esc . ')(\\/(' . LANGS_PATTERN . ')\\/)(.+)/';
    $pattern2 = '/(' . $blog_home_esc . ')\\/(' . LANGS_PATTERN . ')[\\/]{0,1}$/';

    if (preg_match($pattern1, $url)) {
      $url_to_translate = preg_replace($pattern1, '\\1/\\4', $url);
    } elseif (preg_match($pattern2, $url)) {
      $url_to_translate = preg_replace($pattern2, '\\1', $url);
    }
    gltr_debug("gltr_clean_url_to_translate :: [REWRITEON] self url:$url | url_to_translate:$url_to_translate");

  } else {
    $url_to_translate = preg_replace('/[\\?&]{0,1}lang\\=(' . LANGS_PATTERN . ')/i', '', $url);
    gltr_debug("gltr_clean_url_to_translate :: [REWRITEOFF] self url:$url | url_to_translate:$url_to_translate");
  }
  return $url_to_translate;
}

function gltr_make_server_redirect_page($resource){
	if (USE_302){
	  gltr_debug("gltr_make_server_redirect_page :: redirecting to $resource");
	  header("Location: $resource", TRUE, 302); 
	  die();
	} else {
	  $unavail =
	    '<html><head><title>Translation not available</title>
	    <style>html,body {font-family: arial, verdana, sans-serif; font-size: 14px;margin-top:0px; margin-bottom:0px; height:100%;}</style></head>
	    <body><center><br /><br /><b>This page has not been translated yet.<br /><br />The translation process could take a while: in the meantime a semi-automatic translation will be provided in a few seconds.</b><br /><br /><a href="'.get_settings('home').'">Home page</a></center>
	    <script type="text/javascript"><!--
	    setTimeout("Redirect()",5000);
	    function Redirect(){
	     location.href = "{RESOURCE}";
	    }
	    // --></script></body></html>';
	
	  header('HTTP/1.1 503 Service Temporarily Unavailable');//thanks Martin!
	  header('Retry-After: 3600');     
	  $message = str_replace('{RESOURCE}',$resource,$unavail);   
	  die($message);  		
	}	
}  

function gltr_add_get_param($url,$param, $value){
  if (strpos($url,'?')===false)
    $url .= "?$param=$value";
  else
    $url .= "&$param=$value";
  return $url;
}

function gltr_translate($lang) {
  global $gltr_engine;
  
  $page = "";
  $url_to_translate = gltr_clean_url_to_translate();
  $resource = gltr_build_translation_url(BASE_LANG, $lang, $url_to_translate);

  if (!gltr_is_connection_allowed()){
  	$page = gltr_make_server_redirect_page($resource);
  } else {
	  $buf = gltr_http_get_content(gltr_patch_translation_url($resource));
		if (gltr_is_valid_translated_content($buf)){
	  	gltr_store_translation_engine_status('working');
			$page = gltr_clean_translated_page($buf, $lang);
		} else {
	  	gltr_store_translation_engine_status('banned');
	  	gltr_debug("Bad translated content for url: $url_to_translate \n$buf");
	  	$page = gltr_make_server_redirect_page($resource);
	 	}
  }
  return $page;
}

function gltr_http_get_content($resource) {
  $isredirect = true;
  $redirect = null;
	
	while ($isredirect) {
    $isredirect = false;
    if (isset($redirect_url)) {
      $resource = $redirect_url;
    }

    $url_parsed = parse_url($resource);
    $host = $url_parsed["host"];
    $port = $url_parsed["port"];
    if ($port == 0)
      $port = 80;
    $path = $url_parsed["path"];
    if (empty($path))
      $path = "/";
    $query = $url_parsed["query"];
    $http_q = $path . '?' . $query;

    $req = gltr_build_request($host, $http_q);
				
    $fp = @fsockopen($host, $port, $errno, $errstr);

    if (!$fp) {
      return "$errstr ($errno)<br />\n";
    } else {
      fputs($fp, $req, strlen($req)); // send request
      $buf = '';
      $isFlagBar = false;
      $flagBarWritten = false;
      $beginFound = false;
      $endFound = false;
      $inHeaders = true;
			$prevline='';
      while (!feof($fp)) {
        $line = fgets($fp);
        if ($inHeaders) {
        	
          if (trim($line) == '') {
            $inHeaders = false;
            continue;
          }

          $prevline = $line;
          if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
            // Skip to the next header
            continue;
          } 
          $key = strtolower(trim($m[1]));
          $val = trim($m[2]);
					if ($key == 'location') {
            $redirect_url = $val;
          	$isredirect = true;
          	break;
          }
          continue;
        }
				
        $buf .= $line;
      } //end while
    }
    fclose($fp);
  } //while($isredirect) 
  return $buf; 
}


function gltr_is_valid_translated_content($content){
	return (strpos($content, FLAG_BAR_BEGIN) > 0);
}

function gltr_store_translation_engine_status($status){
	$exists = get_option("gltr_translation_status");
	if($exists === false){ 
		add_option("gltr_translation_status","unknown");
	}
	update_option("gltr_translation_status",$status);	
}

function gltr_is_connection_allowed(){

	$last_connection_time = get_option("gltr_last_connection_time");
	if($last_connection_time === false){ 
		add_option("gltr_last_connection_time",0);
		$last_connection_time = 0;
	} 
	
	if ($last_connection_time > 0){
		$now = time();
		$delta = $now - $last_connection_time;
		if ($delta < CONN_INTERVAL){
			gltr_debug("gltr_is_connection_allowed :: Blocking connection request: delta=$delta secs");
			$res = false;
		} else {
			gltr_debug("gltr_is_connection_allowed :: Allowing connection request: delta=$delta secs");
			update_option("gltr_last_connection_time", $now);
	    $res = true;
	  }
	} else {
		gltr_debug("gltr_is_connection_allowed :: Warning: 'last_connection_time' is undefined: allowing translation");
		update_option("gltr_last_connection_time", time());
		$res = true;
	}
	return $res;
}

function gltr_clean_link($matches){
  if (TRANSLATION_ENGINE == 'google'){
  	preg_match("/([^#]*)(#.*)/",$matches[2], $mymatches);
  	if (isset($mymatches[2])){
  		$fragment=$mymatches[2]; 		
  	}
  	$url = urldecode($matches[1]);
    $res = "=\"" . $url . $fragment . "\"";
    if ($matches[3] == '>') $res .= ">";
  } else {
    $res = "=\"" . urldecode($matches[1]) . "\"";
  }
	return $res;
}

function gltr_clean_translated_page($buf, $lang) {
  global $gltr_engine;
	global $well_known_extensions;  
	$is_IIS = (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) ? true : false;

	$patterns = $gltr_engine->get_links_pattern();
	foreach( $patterns as $id => $pattern){
  	$buf = preg_replace_callback($pattern, "gltr_clean_link", $buf);
  }
	
  $buf = preg_replace("/<meta name=\"description\"([ ]*)content=\"([^>]*)\"([ ]*)\/>/i", "", $buf);
  $buf = preg_replace("/<meta name='description'([ ]*)content='([^>]*)'([ ]*)\/>/i", "", $buf);
	//TODO: add <meta name="language" content="LANG" />


  $blog_home_esc = BLOG_HOME_ESCAPED;
  $blog_home = BLOG_HOME;

  if (REWRITEON) {
    if ($is_IIS){
      $blog_home_esc .= '\\/index.php';
      $blog_home .= '/index.php';
      $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "(((?![\"])(?!\/trackback)(?!\/feed)" . gltr_get_extensions_skip_pattern() . ".)*)\"([^>]*)>/i";
      $repl = "<a\\1href=\"" . $blog_home . '/' . $lang . "\\2\" \\4>";
      //gltr_debug("IS-IIS".$repl."|".$pattern);
      $buf = preg_replace($pattern, $repl, $buf);
    } else {
      $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "(((?![\"])(?!\/trackback)(?!\/feed)" . gltr_get_extensions_skip_pattern() . ".)*)\"([^>]*)>/i";
      $repl = "<a\\1href=\"" . $blog_home . '/' . $lang . "\\2\" \\4>";
      //gltr_debug($repl."|".$pattern);
      $buf = preg_replace($pattern, $repl, $buf);
    }
  } else {
    $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "\/\?(((?![\"])(?!\/trackback)(?!\/feed)" . gltr_get_extensions_skip_pattern() . ".)*)\"([^>]*)>/i";
    $repl = "<a\\1href=\"" . $blog_home . "?\\2&gtlang=$lang\" \\4>";
    $buf = preg_replace($pattern, $repl, $buf);
    
    $pattern = "/<a([^>]*)href=\"" . $blog_home_esc . "[\/]{0,1}\"([^>]*)>/i";
    $repl = "<a\\1href=\"" . $blog_home . "?gtlang=$lang\" \\2>";
    $buf = preg_replace($pattern, $repl, $buf);
  }

  //let's remove custom tags added by certain engines
  if (TRANSLATION_ENGINE == 'promt') {
    //$buf = preg_replace("/\<div class='PROMT_HEADER'(.*)\<\/div\>/i", "", $buf);
    //$buf = preg_replace("/\<span class=\"UNKNOWN_WORD\"\>([^\<]*)\<\/span\>/i", "\\1",$buf);
    $buf = preg_replace("/onmouseout=\"OnMouseLeaveSpan\(this\)\"/i", "",$buf);
    $buf = preg_replace("/onmouseover=\"OnMouseOverSpanTran\(this,event\)\"/i", "",$buf);
    $buf = preg_replace("/<span class=\"src_para\">/i", "<span style=\"display:none;\">",$buf);
  } else if (TRANSLATION_ENGINE == 'freetransl') {
    $buf = preg_replace("/\<div(.*)http:\/\/www\.freetranslation\.com\/images\/logo\.gif(.*)\<\/div\>/i", "", $buf);
    $buf = str_replace(array("{L","L}"), array("",""), $buf);
  } else if (TRANSLATION_ENGINE == 'google') {
    $buf = preg_replace("/<iframe src=\"http:\/\/translate\.google\.com\/translate_un[^>]*><\/iframe>/i", "",$buf);
    $buf = preg_replace("/<iframe src=\"[^\"]*rurl=[^>]*><\/iframe>/i", "",$buf);
    $buf = preg_replace("/<script>[^<]*<\/script>[^<]*<script src=\"[^\"]*translate_c.js\"><\/script>[^<]*<script>[^<]*_intlStrings[^<]*<\/script>[^<]*<style type=[\"]{0,1}text\/css[\"]{0,1}>\.google-src-text[^<]*<\/style>/i", "",$buf);
    $buf = preg_replace("/_setupIW\(\);_csi\([^\)]*\);/","",$buf);
    $buf = preg_replace("/onmouseout=[\"]{0,1}_tipoff\(\)[\"]{0,1}/i", "",$buf);
    $buf = preg_replace("/onmouseover=[\"]{0,1}_tipon\(this\)[\"]{0,1}/i", "",$buf);
    $buf = preg_replace("/<span class=[\"]{0,1}google-src-text[\"]{0,1}[^>]*>/i", "<span style=\"display:none;\">",$buf);
    $buf = preg_replace("/<span style=\"[^\"]*\" class=[\"]{0,1}google-src-text[\"]{0,1}[^>]*>/i", "<span style=\"display:none;\">",$buf);
  }
  
	if (HARD_CLEAN){
		$out = array();
		$currPos=0;
		$result = "";
		$tagOpenPos = 0;
		$tagClosePos = 0;
		
		while (!($tagOpenPos === false)){
			$beginIdx = $tagClosePos;
      $tagOpenPos = stripos($buf,"<span style=\"display:none;\">",$currPos);
      $tagClosePos = stripos($buf,"</span>",$tagOpenPos);
			if ($tagOpenPos == 0 && ($tagOpenPos === false) && strlen($result) == 0){
				gltr_debug("===>break all!");
				$result = $buf;
				break;
			}
			$offset = substr($buf,$tagOpenPos,$tagClosePos - $tagOpenPos + 7);
			preg_match_all('/<span[^>]*>/U',$offset,$out2,PREG_PATTERN_ORDER);
			$nestedCount = count($out2[0]);
			
			for($i = 1; $i < $nestedCount; $i++){
        $tagClosePos = stripos($buf,"</span>",$tagClosePos + 7);
			}
			if ($beginIdx > 0)$beginIdx += 7;
			
			$result .= substr($buf,$beginIdx,$tagOpenPos - $beginIdx);
			$currPos = $tagClosePos;
		}
		//gltr_debug($result);
		$buf = $result . substr($buf,$beginIdx);//Fixed by adding the last part of the translation: thanks Nick Georgakis!
	}
  
  $buf = gltr_insert_flag_bar($buf);
  
  return $buf;
}


function gltr_insert_flag_bar($buf){
	$bar = gltr_get_flags_bar();

	$startpos = strpos($buf, FLAG_BAR_BEGIN);
	$endpos = strpos($buf, FLAG_BAR_END);
	if ($startpos > 0 && $endpos > 0){
    $buf = substr($buf, 0, $startpos) . $bar . substr($buf, $endpos + strlen(FLAG_BAR_END));
  } else {
    gltr_debug("Flags bar tokens not found: translation failed or denied");
  }
  
  return $buf;
}

function gltr_get_extensions_skip_pattern() {
	global $well_known_extensions;
	
	$res = "";
	foreach ($well_known_extensions as $key => $value) {
		$res .= "(?!\.$value)";
	}
	return $res;
}

function gltr_get_random_UA(){
	global $gltr_ua;
	$tot = count($gltr_ua);
	$id = rand( 0, count($gltr_ua)-1 );
	$ua = $gltr_ua[$id];
	//gltr_debug("Random UA nr $id: $ua");
	return $ua;
}

function gltr_build_request($host, $http_req) {
  $res = "GET $http_req HTTP/1.0\r\n";
  $res .= "Host: $host\r\n";
  $res .= "User-Agent: " . gltr_get_random_UA() . " \r\n";
  $res .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n";
  $res .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
  $res .= "Connection: close\r\n";
  $res .= "\r\n";
  return $res;
}


function gltr_get_flags_bar() {
  global $gltr_engine, $wp_query, $gltr_merged_image;
  $num_cols = BAR_COLUMNS;
	if (!isset($gltr_engine) || $gltr_engine == null ){
		gltr_debug("WARNING! GT Options not correctly set!");
		return "<b>Global Translator not configured yet. Please go to the Options Page</b>";
	}
  

  $buf = '';
	
  
  $transl_map = $gltr_engine->get_languages_matrix();

  $translations = $transl_map[BASE_LANG];

  $transl_count = count($translations); 

  $buf .= "\n" . FLAG_BAR_BEGIN; //initial marker

  if (HTML_BAR_TAG == 'TABLE')
    $buf .= "<table border='0'><tr>";
  else if (HTML_BAR_TAG == 'DIV')
    $buf .= "<div id=\"translation_bar\">";
  else if (HTML_BAR_TAG == 'MAP')
    $buf .= "<div id=\"translation_bar\"><map id=\"gltr_flags_map\" name=\"gltr_flags_map\">";

  $curr_col = 0;
  $curr_row = 0;

  $dst_x = 0;
  $dst_y = 0;
  $map_left=0;
  $map_top=0;
  $map_right=16;
  $map_bottom=11;
  $grid;

  //filter preferred
  $preferred_transl = array();
  foreach ($translations as $key => $value) {
    if ($key == BASE_LANG || in_array($key, get_option('gltr_preferred_languages')))
      $preferred_transl[$key] = $value;
  }
  $num_rows=1;
  if ($num_cols > 0){
    $num_rows = (int)(count($preferred_transl)/$num_cols);
    if (count($preferred_transl)%$num_cols>0)$num_rows+=1;
  }
  if (HTML_BAR_TAG == 'MAP' && !file_exists($gltr_merged_image)){
    $img_width = $num_cols*20;
    $img_height = $num_rows*15;
    $grid = imagecreatetruecolor ($img_width, $img_height);
    imagecolortransparent($grid, 000000);
  }

  foreach ($preferred_transl as $key => $value) {
    if ($curr_col >= $num_cols && $num_cols > 0) {
      if (HTML_BAR_TAG == 'TABLE') $buf .= "</tr><tr>";
      $curr_col = 0;
      $dst_x = 0;
      $map_left = 0;
      $map_right = 16;
      $curr_row++;
    }
    $dst_y = $curr_row * 15;
    $map_top = $curr_row * 15;
    $map_bottom = $curr_row * 15 + 11;

    $flg_url = gltr_get_translated_url($key, gltr_get_self_url());
    $flg_image_url = gltr_get_flag_image($key);
    $flg_image_path = gltr_get_flag_image_path($key);

    if (HTML_BAR_TAG == 'TABLE') $buf .= "<td>";

    if (HTML_BAR_TAG == 'MAP'){
    	$buf .="<area shape='rect' coords='$map_left,$map_top,$map_right,$map_bottom' href='$flg_url' id='flag_$key' $lnk_attr  title='$value'/>";
      $map_left = $map_left+20;
      $map_right= $map_right+20;
    }else{
      $buf .= "<a id='flag_$key' href='$flg_url' hreflang='$key' $lnk_attr><img id='flag_img_$key' src='$flg_image_url' alt='$value flag' title='$value'  border='0' /></a>";
    }

    if (HTML_BAR_TAG == 'TABLE') $buf .= "</td>";

    if ($num_cols > 0) $curr_col += 1;

    if (HTML_BAR_TAG == 'MAP' && !file_exists($gltr_merged_image)){
      $img_tmp = @imagecreatefrompng($flg_image_path);

      imagecopymerge($grid, $img_tmp, $dst_x, $dst_y, 0, 0, 16, 11, 100);
      //gltr_debug("dst_x=$dst_x;dst_y=$dst_y;curr_row=$curr_row;curr_col=$curr_col;num_rows=$num_rows;flg_image_url=$flg_image_url");
      $dst_x = $dst_x + 20;
      @imagedestroy($img_tmp);
    }
  }//end foreach ($preferred_transl as $key => $value) {

  if (HTML_BAR_TAG == 'MAP' && !file_exists($gltr_merged_image)){
    if (!is_writeable(dirname(__file__))){
      return "<b>Permission error: Please make your 'plugins/global-translator' directory writable by Wordpress</b>";
    } else {
      imagepng($grid, $gltr_merged_image);
      imagedestroy($grid);
    }
  }
  if (HTML_BAR_TAG == 'MAP'){
    $merged_image_url=gltr_get_flags_image();
  }

  while ($curr_col < $num_cols && $num_cols > 0) {
    if (HTML_BAR_TAG == 'TABLE') $buf .= "<td>&nbsp;</td>";
    $curr_col += 1;
  }


  if ($num_cols == 0)
    $num_cols = count($translations);
    
  //$n2hlink = "<a style=\"font-size:9px;\" href=\"http://www.n2h.it\">By N2H</a>";
  $n2hlink = "";
  if (HTML_BAR_TAG == 'MAP'){
    $buf .="</map>";
    $buf .= "<img style='border:0px;' src='$merged_image_url' usemap='#gltr_flags_map'/></div>";
  } 
  
  if (HTML_BAR_TAG == 'TABLE')
    $buf .= "</tr><tr><td colspan=\"$num_cols\">$n2hlink</td></tr></table>";
  else if (HTML_BAR_TAG == 'DIV')
    $buf .= "<div id=\"transl_sign\">$n2hlink</div></div>";
  else
    $buf .= "<div id=\"transl_sign\">$n2hlink</div>";
  $buf .= FLAG_BAR_END . "\n"; //final marker
  return $buf;
}

function gltr_build_flags_bar() {
  echo (gltr_get_flags_bar());
}

//ONLY for backward compatibility!
function build_flags_bar() {
  echo (gltr_get_flags_bar());
}

function gltr_get_translated_url($language, $url) {
  if (REWRITEON) {
    $contains_index = (strpos($url, 'index.php')!==false);
    $blog_home_esc = BLOG_HOME_ESCAPED;
    if ($contains_index){
      $blog_home_esc .= '\\/index.php';
    }
		$pattern1 = '/' . $blog_home_esc . '\\/(' . LANGS_PATTERN . ')$/';
		$pattern2 = '/' . $blog_home_esc . '\\/((' . LANGS_PATTERN . ')[\\/])*(.*)/';

    if (!preg_match($pattern1, $url) && preg_match($pattern2, $url)) {
      $uri = preg_replace($pattern2, '\\3', $url);
    } else {
      $uri = '';
    }

    $blog_home = BLOG_HOME;
    if ($contains_index){
      $blog_home .= '/index.php';
    }
    if ($language == BASE_LANG)
      $url = $blog_home . '/' . $uri;
    else
      $url = $blog_home . '/' . $language . '/' . $uri;
  } else {
    //REWRITEOFF
    $pattern1 = '/(.*)([&|\?]{1})gtlang=(' . LANGS_PATTERN . ')(.*)/';
    $pattern2 = '/(.*[&|\?]{1})gtlang=(' . LANGS_PATTERN . ')(.*)/';

    if ($language == BASE_LANG) {
      $url = preg_replace($pattern1, '\\1\\4', $url);
    } else
      if (preg_match($pattern2, $url)) {
        $url = preg_replace($pattern2, '\\1gtlang=' . $language . '\\3', $url);
      } else {
        if (strpos($url,'?')===false)
          $url .= '?gtlang=' . $language;
        else
          $url .= '&gtlang=' . $language;
      }

  }

  return $url;
}


function gltr_get_self_url() {
  $full_url = 'http';
  $script_name = '';
  if (isset($_SERVER['REQUEST_URI'])) {
    $script_name = $_SERVER['REQUEST_URI'];
  } else {
    $script_name = $_SERVER['PHP_SELF'];
    if ($_SERVER['QUERY_STRING'] > ' ') {
      $script_name .= '?' . $_SERVER['QUERY_STRING'];
    }
  }
  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $full_url .= 's';
  }
  $full_url .= '://';
  if ($_SERVER['SERVER_PORT'] != '80') {
    $full_url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $script_name;
  } else {
    $full_url .= $_SERVER['HTTP_HOST'] . $script_name;
  }
  return $full_url;
}

//rewrite rules definitions
function gltr_translations_rewrite($wp_rewrite) {
  $translations_rules = array('^(' . LANGS_PATTERN . ')$' =>
    'index.php?gtlang=$matches[1]', '^(' . LANGS_PATTERN . ')/(.+?)$' =>
    'index.php?gtlang=$matches[1]&url=$matches[2]');
  $wp_rewrite->rules = $translations_rules + $wp_rewrite->rules;
}

function gltr_get_cookies() {
  $string = '';
  while ($key = key($_COOKIE)) {
    if (preg_match("/^wordpress|^comment_author_email_/", $key)) {
      $string .= $_COOKIE[$key] . ",";
    }
    next($_COOKIE);
  }
  reset($_COOKIE);
  return $string;
}

function gltr_is_cached($url,$lang){
	global $gltr_cache_dir, $gltr_stale_dir;

  $url_parts = parse_url($url);
  $host = 'http://' . $url_parts['host'];
  $host_escaped = str_replace('/', '\\/', $host);

  $cachedir = $gltr_cache_dir."/$lang";
  $staledir = $gltr_stale_dir."/$lang";
  $uri = preg_replace("/$host_escaped/", '', $url);
  $hash = gltr_hashReqUri($uri);
  $filename = $cachedir . '/' . $hash;
  $stale_filename = $staledir . '/' . $hash;
  return (is_file($filename)||is_file($stale_filename));
  
}

function gltr_mkdir($dirtomake){
  if (!is_dir($dirtomake)) { 
    if (!@mkdir($dirtomake, 0777)){
     	die("<b>Global Translator has detected a problem with your filesystem permissions:<br />The cache dir <em>$dirtomake</em> cannot be created. <br />Please make readable and writeable the following directory: <br /><em>".WP_CONTENT_DIR."</em>.</b>");
    }
    if(!file_exists($dirtomake) || !is_readable($dirtomake) || !is_writeable($dirtomake)){
    	die("<b>Global Translator has detected a problem with your filesystem permissions:<br />The cache dir <em>$dirtomake</em> cannot be read or modified. <br />Please chmod it in order to make it readable and writeable.</b>");
    }
  }
	
}


function gltr_get_page_content($lang, $url) {
 	global $gltr_cache_dir;
	global $gltr_stale_dir;

  $page = '';
  $hash = gltr_hashReqUri($_SERVER['REQUEST_URI']);
  //gltr_debug("Hashing uri: ".$_SERVER['REQUEST_URI']." to: $hash");

  $cachedir = $gltr_cache_dir;
  $staledir = $gltr_stale_dir;

  //gltr_debug("==>$cachedir");
  gltr_mkdir($cachedir);
  gltr_mkdir($staledir);

	gltr_move_to_new_cache_loc($hash,$lang);				
	gltr_move_to_new_stale_loc($hash,$lang);				
	
  $filename = $cachedir . '/' . $lang . '/' . $hash;
  $stale_filename = $staledir . '/' . $lang . '/' . $hash;
  
  if(file_exists($filename) && (!is_readable($filename) || !is_writeable($filename))){
  	return "<b>Global Translator has detected a problem with your filesystem permissions:<br />The cached file <em>$filename</em> cannot be read or modified. <br />Please chmod it in order to make it readable and writeable.</b>";
  }
  if(file_exists($stale_filename) && (!is_readable($stale_filename) || !is_writeable($stale_filename))){
  	return "<b>Global Translator has detected a problem with your filesystem permissions:<br />The cached file <em>$stale_filename</em> cannot be read or modified. <br />Please chmod it in order to make it readable and writeable.</b>";
  }
  
  if (file_exists($filename) && filesize($filename) > 0) {

    // We are done, just return the file and exit
    gltr_debug("gltr_get_page_content :: returning cached version ($hash) for url:" . gltr_get_self_url());
    $page = gltr_load_cached_page($filename);
    $page .= "<!--CACHED VERSION ($hash)-->";
    
    $page = gltr_insert_flag_bar($page); //could be skipped    
    $page = preg_replace("/<iframe src=\"[^\"]*rurl=[^>]*><\/iframe>/i", "",$page);
		//check if needs to be scheduled for a new translation
		$filetime_days = (time() - filemtime($filename)) / 86400;
		
		if (EXPIRE_TIME > 0 && $filetime_days >= EXPIRE_TIME ){
			gltr_debug("gltr_get_page_content :: The file $filename has been created more than " . EXPIRE_TIME . " days ago. Scheduling for a new translation");
			gltr_move_cached_file_to_stale($hash,$lang);
		}

  } else {

    $url_to_translate = gltr_clean_url_to_translate();
  	gltr_debug("gltr_get_page_content :: Connecting to engine for url:" . $url_to_translate);
    $page = gltr_translate($lang);
    //check the content to be cached
		if (gltr_is_valid_translated_content($page)) {
			$gltr_last_cached_url = gltr_get_self_url();
      gltr_debug("gltr_get_page_content :: caching ($filename) [".strlen($page)."] url:" . $gltr_last_cached_url);
      gltr_save_cached_page($page,$filename);
      $page .= "<!--NOT CACHED VERSION: ($hash)-->";
      if (file_exists($stale_filename)){
      	unlink($stale_filename);
      }
    } else {
    	gltr_debug("gltr_get_page_content :: translation not available. Switching to stale for url: $url_to_translate");
	    if (file_exists($stale_filename) && filesize($stale_filename) > 0) {
	      gltr_debug("gltr_get_page_content :: returning stale version ($hash) for url:" . $url_to_translate);

	      $page = gltr_load_cached_page($stale_filename);
		    $page = gltr_insert_flag_bar($page); //could be skipped 
	      $page .= "<!--STALE VERSION: ($hash)-->";
	 			$from_cache = true;
	    } else {
	    	
			  $resource = gltr_build_translation_url(BASE_LANG, $lang, $url_to_translate);
        //no cache, no translated,no stale
	      	$page = gltr_make_server_redirect_page($resource);
      }
    }
  }
  
  return $page;
}

function gltr_save_cached_page($data,$filename){
	//gltr_debug("gltr_save_cached_page: Cache compression enabled = " . COMPRESS_CACHE);

	if (COMPRESS_CACHE && function_exists('gzcompress')){
		gltr_debug("gltr_save_cached_page :: using zlib for file: $filename");
		$data = gzcompress($data, 9);
	} else {
		gltr_debug("gltr_save_cached_page :: NOT using zlib for file: $filename");
	} 
  $handle = fopen($filename, "wb");
  if (flock($handle, LOCK_EX)) { // do an exclusive lock
    fwrite($handle, $data); //write
    flock($handle, LOCK_UN); // release the lock
  } else {
    fwrite($handle, $data); 
  }
  fclose($handle);
}

function gltr_load_cached_page($filename){
	//gltr_debug("gltr_load_cached_page: Cache compression enabled = " . COMPRESS_CACHE);
	$data = file_get_contents($filename);
	
	if (function_exists('gzuncompress')){
		if (($tmp = @gzuncompress($data))){
			$data = $tmp;
			if (!COMPRESS_CACHE){
				//save the unzipped version
				gltr_save_cached_page($data,$filename);
			}
		} else if (COMPRESS_CACHE) {
			//save the zipped version
			gltr_save_cached_page(file_get_contents($filename),$filename);
		}
		
	}
	return $data;
}

function gltr_hashReqUri($uri) {
	
	$uri = urldecode($uri);//Adde
  $req = preg_replace('/(.*)\/$/', '\\1', $uri);
  $req = preg_replace('/#.*$/', '', $req);
  $hash = str_replace(array('?','<','>',':','\\','/','*','|','"'), '_', $req);
  return $hash;
}
/*
function gltr_filter_content($content) {
  global $gltr_result;
  return $gltr_result;
}
*/
function gltr_insert_my_rewrite_query_vars($vars) {
  array_push($vars, 'gtlang', 'gturl');
  return $vars;
}

function gltr_insert_my_rewrite_parse_query($query) {
  global $gltr_cache_dir,$gltr_is_translated_page;
  $gltr_result = "";

  if (isset($query->query_vars['gtlang'])) {
  	$lang = $query->query_vars['gtlang'];
    $url = $query->query_vars['gturl'];

    if (empty($url)) {
      $url = '';
    }
  	

    if (!is_dir($gltr_cache_dir)){
      if (!is_readable(WP_CONTENT_DIR) || !is_writable(WP_CONTENT_DIR) ){
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        die ("Unable to complete Global Translator initialization. Plese make writable and readable the following directory:
        <ul><li>".WP_CONTENT_DIR."</li></ul>");
      }
    }

    if (gltr_not_translable_uri()){
   		return;
		}

  	$chosen_langs = get_option('gltr_preferred_languages');

		$can_translate = true;
		$self_url = gltr_get_self_url();
		$self_uri = preg_replace("/" . BLOG_HOME_ESCAPED . "/", '', $self_url);
		
  	if (!in_array($lang, $chosen_langs)){
      $redirect = gltr_clean_url_to_translate($self_url);
  		gltr_debug("Blocking request for not chosed language:$lang redirecting to original page: $redirect");
  		header("Location: $redirect", TRUE, 302);
  		die();
  		//header("HTTP/1.1 404 Not Found");
			//header("Status: 404 Not Found");
  		//$gltr_result = NOT_FOUND;
  		//$can_translate = false;
  	}
	  
	  if (!gltr_is_user_agent_allowed() && BAN_PREVENTION){
  		gltr_debug("Limiting bot/crawler access to resource:".$url);
  		header("HTTP/1.1 404 Not Found");
			header("Status: 404 Not Found");
  		$gltr_result = NOT_FOUND;
  		$can_translate = false;
  	}
  	
  	if (preg_match("/^(" . LANGS_PATTERN . ")$/", $url) || 
  			preg_match("/^(" . LANGS_PATTERN . ")\/(.+)$/", $url) ){
  		gltr_debug("Fixing request for nested translation request:".$lang."|".$url."|".$self_url);
  		$redirect = preg_replace("/(.*)\/(" . LANGS_PATTERN . ")\/(" . LANGS_PATTERN . ")\/(.*)$/", "\\1/\\2/\\4", $self_url);
      header("Location: $redirect", TRUE, 302);
      die();
  		//header("HTTP/1.1 404 Not Found");
			//header("Status: 404 Not Found");
  		//$gltr_result = NOT_FOUND;
  		//$can_translate = false;
  	}
    /*
	  if (REWRITEON){
			if (strpos($self_url,'?')===false && strpos($self_url,'&')>0){
	  		gltr_debug("Blocking bad request:".$lang."|".$url);
  		header("HTTP/1.1 404 Not Found");
			header("Status: 404 Not Found");
  		$gltr_result = NOT_FOUND;
  		$can_translate = false;
			} else if( (strpos($self_url,'?') === false) && 
			           (substr($self_url, -1) != '/') && 
			           (strpos($self_uri,'.') === false) ){
	  		header("HTTP/1.1 302 Moved Temporarily");
	  		header("Location: " . gltr_get_self_url() . '/');
	  		die();  		
  	}
		}
    */
		if (REWRITEON && strpos($self_url,'?')===false){
			if (strpos($self_url,'&')>0){
        gltr_debug("Blocking bad request:".$lang."|".$url);
	  		header("HTTP/1.1 404 Not Found");
				header("Status: 404 Not Found");
	  		$gltr_result = NOT_FOUND;
	  		$can_translate = false;
			} else if (substr($self_url, -1) != '/' && strpos($self_uri,'.') === false){
	  		header("HTTP/1.1 302 Moved Temporarily");
	  		header("Location: " . gltr_get_self_url() . '/');
	  		die();  		
			} else if (substr($self_url, -1) == '/' && strpos($self_uri,'.') !== false){
        header("HTTP/1.1 302 Moved Temporarily");
        $loc = rtrim(gltr_get_self_url(),'/');
        header("Location: " . $loc);
        die();      
			}
		}  	
  	if ($can_translate) {
      $gltr_result = gltr_get_page_content($lang, $url);
		}
		

		die($gltr_result);
  	//$end = round(microtime(true),4);
  	//gltr_debug("Translated page serving total time:". ($end - $start) . " seconds");
    
  }
}

//thanks to Kaizeku Ban
function gltr_add_options_page() {
 $path = dirname(__FILE__).DIRECTORY_SEPARATOR;
 $file = $path. 'options-translator.php';
 add_options_page('Global Translator Options','Global Translator',8,$file);
}

function gltr_debug($msg) {
  if (DEBUG) {
    $today = date("Y-m-d H:i:s ");
    $myFile = dirname(__file__) . "/debug.log";
    $fh = fopen($myFile, 'a') or die("Can't open debug file. Please manually create the 'debug.log' file (inside the 'global-translator' directory) and make it writable.");
    $ua_simple = preg_replace("/(.*)\s\(.*/","\\1",$_SERVER['HTTP_USER_AGENT']);
    //fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - [mem:" . memory_get_usage() . "] " . $msg . "\n");
    if (is_array($msg)){
    	foreach($msg as $key => $item)
    		fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $key . "=>" . $item . "\n");
    }else
    	fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $msg . "\n");
    fclose($fh);
  }
}

function gltr_debug_ua($msg) {
  if (DEBUG_UA) {
    $today = date("Y-m-d H:i:s ");
    $myFile = dirname(__file__) . "/ua.log";
    $fh = fopen($myFile, 'a') or die("Can't open debug file. Please manually create the 'ua.log' file (inside the 'global-translator' directory) and make it writable.");
    $ua_simple = preg_replace("/(.*)\s\(.*/","\\1",$_SERVER['HTTP_USER_AGENT']);
    //fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - [mem:" . memory_get_usage() . "] " . $msg . "\n");
    fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $msg . "\n");
    fclose($fh);
  }
}

function gltr_not_translable_uri(){
	
  $not_translable = array("share-this","download.php");
  $url = gltr_get_self_url();
  if (isset($url))
    $url = strtolower($url);
  else
    $url = "";
  if ($url == "") {
    return false;
  } else {
    while (list($key, $val) = each($not_translable)) {
      if (strstr($url, strtolower($val))) {
        gltr_debug("Detected and blocked untranslable uri: $url");
        return true;
      }
    }
  }  
  return false;
}


function gltr_is_browser() {
  $browsers_ua = array(
  "MSIE", 
  "UP.Browser",
  "Mozilla", 
  "Opera", 
  "NSPlayer", 
  "Avant Browser",
  "Konqueror",
  "Safari",
  "Netscape"  
  );
  if (isset($_SERVER['HTTP_USER_AGENT']))
    $ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
  else
    $ua = "";

  if ($ua == "") {
    return false;
  } else {
    while (list($key, $val) = each($browsers_ua)) {
      if (strstr($ua, strtoupper($val))) {
        return true;
      }
    }
  }
  return false;
}

function gltr_is_user_agent_allowed() {

  $not_allowed = array("Wget", "EmailSiphon", "WebZIP", "MSProxy/2.0", "EmailWolf",
    "webbandit", "MS FrontPage", "GetRight", "AdMuncher", "Sqworm", "SurveyBot",
    "TurnitinBot", "WebMirror", "WebMiner", "WebStripper", "WebSauger", "WebReaper",
    "WebSite eXtractor", "Teleport Pro", "CherryPicker", "Crescent Internet ToolPak",
    "EmailCollect", "ExtractorPro", "NEWT ActiveX", "sexsearcher", "ia_archive",
    "NameCrawler", "Email spider", "GetSmart", "Grabber", "GrabNet", "EmailHarvest",
    "Go!Zilla", "LeechFTP", "Vampire", "SmartDownload", "Sucker", "SuperHTTP",
    "Collector", "Zeus", "Telesoft", "URLBlaze", "VobSub", "Vacuum", "Space Bison",
    "WinWAP", "3D-FTP", "Wapalizer", "DTS agent", "DA 5.", "NetAnts", "Netspider",
    "Disco Pump", "WebFetch", "DiscoFinder", "NetZip", "Express WebPictures",
    "Download Demon", "eCatch", "WebAuto", "Offline Expl", "HTTrack",
    "Mass Download", "Mister Pix", "SuperBot", "WebCopier", "FlashGet", "larbin",
    "SiteSnagger", "FlashGet", "NPBot", "Kontiki","Java","ETS V5.1",
    "IDBot", "id-search", "libwww", "lwp-trivial", "curl", "PHP/", "urllib", 
    "GT::WWW", "Snoopy", "MFC_Tear_Sample", "HTTP::Lite", "PHPCrawl", "URI::Fetch", 
    "Zend_Http_Client", "http client", "PECL::HTTP","libwww-perl","SPEEDY SPIDER",
    "YANDEX","YETI","DOCOMO","DUMBOT","PDFBOT","CAZOODLEBOT","RUNNK","ICHIRO",
    "SPHERE SCOUT");

  $allowed = array("compatible; MSIE", "T720", "MIDP-1.0", "AU-MIC", "UP.Browser",
    "SonyEricsson", "MobilePhone SCP", "NW.Browser", "Mozilla", "UP.Link",
    "Windows-Media-Player", "MOT-TA02", "Nokia", "Opera/7", "NSPlayer",
    "GoogleBot", "Opera/6", "Panasonic", "Thinflow", "contype", "klondike", "UPG1",
    "SEC-SGHS100", "Scooter", "almaden.ibm.com",
    "SpaceBison/0.01 [fu] (Win67; X; ShonenKnife)", "Internetseer","MSNBOT-MEDIA/",
    "MEDIAPARTNERS-GOOGLE","MSNBOT","Avant Browser","GIGABOT","OPERA");

  if (isset($_SERVER['HTTP_USER_AGENT']))
    $ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
  else
    $ua = "";
  if ($ua == "") {
    return false;
  } else {
    while (list($key, $val) = each($not_allowed)) {
      if (strstr($ua, strtoupper($val))) {
        //gltr_debug("Detected and blocked user agent: $ua");
        return false;
      }
    }
  }

  $notknown = 1;
  while (list($key, $val) = each($allowed)) {
    if (strstr($ua, strtoupper($val))) {
      $notknown = 0;
    }
  }

  if ($notknown) {
    gltr_debug_ua("$ua");
  }
  return true;
}

function gltr_erase_common_cache_files($post_ID) {
	global $gltr_cache_dir;
	global $gltr_stale_dir;
	global $gltr_engine;
	
	$start= round(microtime(true),4);
	
  $single_post_pattern = "";

	$categories = array();
	$tags =  array();
	$patterns = array();

	if (isset($post_ID)){
		$post = get_post($post_ID); 
		if ($post->post_status != 'publish'){
			gltr_debug("Post not yet published (status=".$post->post_status."): no cached files to erase");
			return;
		} else {
			gltr_debug("Post published ok to cached files erase");
		}
		if (function_exists('get_the_category')) $categories = get_the_category($post_ID);

		if (function_exists('get_the_tags')) $tags = get_the_tags($post_ID);
  	if (REWRITEON) {
  		$uri = substr (get_permalink($post_ID), strlen(get_option('home')) );
  		$single_post_pattern = gltr_hashReqUri($uri);
			if (isset($categories) && is_array($categories)){
				foreach($categories as $category) { 
			    $patterns[] = '_category_' . strtolower($category->slug); 
				} 
			} else {
		    $patterns[] = '_category_'; 
			}
			if (isset($tags) && is_array($tags)){
				foreach($tags as $tag) { 
			    $patterns[] = '_tag_' . $tag->slug; 
				}
			}else{
		    $patterns[] = '_tag_'; 
			}			
  	} else {
  		$single_post_pattern = $post_ID;
			if (isset($categories) && is_array($categories)){
				foreach($categories as $category) { 
			    $patterns[] = '_cat=' . strtolower($category->cat_ID); 
				} 
			} else {
		    $patterns[] = '_cat='; 
			}
			if (isset($tags) && is_array($tags)){
				foreach($tags as $tag) { 
			    $patterns[] = '_tag=' . $tag->slug;  
				}
			}else{
		    $patterns[] = '_tag='; 
			}
  	}

		$datepattern = "";
		$post_time = $post->post_date;
		if (isset($post_time) && function_exists('mysql2date')){
			$year = mysql2date(__('Y'), $post_time);
			$month = mysql2date(__('m'), $post_time);
			//gltr_debug("==>post y=$year m=$month");
			if (REWRITEON){
				$datepattern = $year . "_" . $month;
			} else {
				$datepattern = "$year$month";
			}
		} else {
			if (REWRITEON){
				$datepattern = "[0-9]{4}_[0-9]{2}";
			} else {
				$datepattern = "[0-9]{6}";
			}
		}
  	
  	
	} else {
			gltr_debug("Post ID not set");
	}
	
  $transl_map = $gltr_engine->get_languages_matrix();
  $translations = $transl_map[BASE_LANG];
  foreach ($translations as $key => $value) {
	  $cachedir = $gltr_cache_dir . "/$key";
	  gltr_debug("begin clean $key");
  if (file_exists($cachedir) && is_dir($cachedir) && is_readable($cachedir)) {
    $handle = opendir($cachedir);
    while (FALSE !== ($item = readdir($handle))) {
    	if( $item != '.' && $item != '..' && $item != 'stale' && !is_dir($item)){
	    		gltr_delete_empty_cached_file($item,$key);
    		$donext = true;
				foreach($patterns as $pattern) { 
          if(strstr($item, $pattern)){
	            gltr_move_cached_file_to_stale($item,$key);
            $donext = false;
            break;
          }
				} 
				if ($donext){
		    	if (REWRITEON) {
		        if(	preg_match('/_(' . LANGS_PATTERN . ')_'.$datepattern.'$/', $item) ||
								preg_match('/_(' . LANGS_PATTERN . ')_page_[0-9]+$/', $item) ||
								preg_match('/_(' . LANGS_PATTERN . ')$/', $item) ||
								preg_match('/_(' . LANGS_PATTERN . ')'.$single_post_pattern.'$/', $item)) {
			        		gltr_move_cached_file_to_stale($item,$key);
		        }
		      } else {
		      	//no rewrite rules
		        if(	preg_match('/_p='.$single_post_pattern.'$/', $item) ||
		        		preg_match('/_paged=[0-9]+$/', $item) ||
		        		preg_match('/_m='.$datepattern.'$/', $item) ||
		        		preg_match('/_lang=(' . LANGS_PATTERN . ')$/', $item)) {
			        		gltr_move_cached_file_to_stale($item,$key);
		        }
		      }
		    }
    	}
    }
    closedir($handle);
  }
  }
  //gltr_debug("end clean");
  $end= round(microtime(true),4);
 	gltr_debug("Cache cleaning process total time:". ($end - $start) . " seconds");

  
}

function gltr_delete_empty_cached_file($filename,$lang){
	global $gltr_cache_dir;
	global $gltr_stale_dir;
  $cachedir = $gltr_cache_dir."/$lang";
  $path = $cachedir.'/'.$filename;
  if (file_exists($path) && is_file($path) && filesize($path) == 0){
    gltr_debug("Erasing empty file: $path");
  	unlink($path);
  }
}


function gltr_move_to_new_cache_loc($filename,$lang){
	global $gltr_cache_dir;

	$cachedir = dirname(__file__) . '/cache';
  if (is_dir($cachedir)) {
  $src = $cachedir . '/' . $filename;
  $dst = $gltr_cache_dir . '/' . $filename;
  if (file_exists($src) && !file_exists($dst) ){
	  if (!@rename($src,$dst)){
		  gltr_debug("Unable to move cached file $src to stale $dst");
	  } else {
		  gltr_debug("Moving cached file $src to stale $dst");
	  }
	}
}
  gltr_mkdir($gltr_cache_dir . '/' . $lang);
  $src = $gltr_cache_dir . '/' . $filename;
  $dst = $gltr_cache_dir . '/' . $lang . '/' . $filename;
  if (file_exists($src) && !file_exists($dst)){
    if (!@rename($src,$dst)){
      gltr_debug("Unable to move cached file $src to cache/lang $dst");
    } else {
      gltr_debug("Moving cached file $src to cache/lang $dst");
    }
  }  
}

function gltr_move_to_new_stale_loc($filename,$lang){
	global $gltr_stale_dir;

	$staledir = dirname(__file__) . '/cache/stale';
  if (is_dir($staledir)) {
  $src = $staledir . '/' . $filename;
  $dst = $gltr_stale_dir . '/' . $filename;
  if (file_exists($src) && !file_exists($dst)){
		if (!@rename($src,$dst)){
		  gltr_debug("Unable to move cached file $src to stale $dst");
	  } else {
		  gltr_debug("Moving cached file $src to stale $dst");
	  }
	}
}

  gltr_mkdir($gltr_stale_dir . '/' . $lang);
  $src = $gltr_stale_dir . '/' . $filename;
  $dst = $gltr_stale_dir . '/' . $lang . '/' . $filename;
  if (file_exists($src) && !file_exists($dst)){
    if (!@rename($src,$dst)){
      gltr_debug("Unable to move stale file $src to stale/lang $dst");
    } else {
      gltr_debug("Moving stale file $src to stale/lang $dst");
    }
  }
}

function gltr_move_cached_file_to_stale($filename,$lang){
	global $gltr_cache_dir;
	global $gltr_stale_dir;
	$cachedir = $gltr_cache_dir."/$lang";
	$staledir = $gltr_stale_dir."/$lang";

  $src = $cachedir . '/' . $filename;
  $dst = $staledir . '/' . $filename;
  if (!@rename($src,$dst)){
	  gltr_debug("Unable to move cached file $src to stale $dst");
  } else {
	  gltr_debug("Moving cached file $src to stale $dst");
  }
}

function gltr_delete_cached_file($filename,$lang){
	global $gltr_cache_dir;
  $cachedir = $gltr_cache_dir."/$lang";
  $path = $cachedir.'/'.$filename;
  if (file_exists($path) && is_file($path)){
    gltr_debug("Erasing $path");
  	unlink($path);
  }

}

function widget_global_translator_init() {

  if(!function_exists('register_sidebar_widget')) { return; }
  function widget_global_translator($args) {
    extract($args);
    echo $before_widget . $before_title . "Translator" . $after_title;
    gltr_build_flags_bar();
    echo $after_widget;
  }
  register_sidebar_widget('Global Translator','widget_global_translator');

}
add_action('plugins_loaded', 'widget_global_translator_init');
?>