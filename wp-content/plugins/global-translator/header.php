<?php


$gltr_ua = array(
"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.4; en-US; rv:1.9b5)",
"Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.0.1)",
"Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.0.1)",
"Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US)",
"Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us)",
"Mozilla/5.0 (Macintosh; U; PPC; en-US; rv:0.9.2)",
"Mozilla/5.0 (Windows; U; Win98; en-US; rv:0.9.2)",
"Mozilla/5.0 (Windows; U; Win98; en-US; rv:x.xx)",
"Mozilla/5.0 (Windows; U; Win9x; en; Stable)",
"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.5)",
"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x)",
"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.xx)",
"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.xxx)",
"Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9b5)",
"Mozilla/5.0 (Windows; U;XMPP Tiscali Communicator v.10.0.1; Windows NT 5.1; it; rv:1.8.1.3)",
"Mozilla/5.0 (X11; Linux i686; U;rv: 1.7.13)",
"Mozilla/5.0 (X11; U; Linux 2.4.2-2 i586; en-US; m18)",
"Mozilla/5.0 (X11; U; Linux i686; en-GB; rv:1.7.6)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; Nautilus/1.0Final)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:0.9.3)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2b)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.7)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1)",
"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.1)",
"Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9a8)",
);


function gltr_get_flag_image_path($language) {
  return dirname(__file__) . '/flag_' . $language . '.png';
}

function gltr_get_flag_image($language) {
  //thanks neanton!
  $path = strstr(realpath(dirname(__file__)), 'wp-content');
  $path = str_replace('\\', '/', $path);
  return get_settings('siteurl') . '/' . $path . '/flag_' . $language . '.png';
}

function gltr_get_flags_image() {
  $path = strstr(realpath(dirname(__file__)), 'wp-content');
  $path = str_replace('\\', '/', $path);
  return get_settings('siteurl') . '/' . $path . '/gltr_image_map.png';
}


function gltr_sitemap_plugin_detected(){
	if (function_exists('get_plugins')){
		$all_plugins = get_plugins();
		foreach( (array)$all_plugins as $plugin_file => $plugin_data) {
			if ($plugin_file == 'google-sitemap-generator/sitemap.php'||$plugin_file == 'sitemap.php') return true;
		}
		return false;
	} else
		return true;
}

function gltr_create_file($datafile){
	$success = true;
	if (!file_exists($datafile)){
      if (($handle = @fopen($datafile, "wb")) === false) return false;
	    if ((@fwrite($handle, '')) === false) return false;
      @fclose($handle);
	} 
	return true;
}

if (!function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null) {
		if (false === $handle = fopen($filename, 'rb', $incpath)) {
			return false;
		}
		if ($fsize = @filesize($filename)) {
			$buf = fread($handle, $fsize);
		} else {
			$buf = '';
			while (!feof($handle)) {
				$buf .= fread($handle, 8192);
			}
		}
		fclose($handle);
		return $buf;
	}	
}
if(!class_exists("gltr_translation_engine")) {
	class gltr_translation_status {
		
		var $_status;
		var $_last_connection_time;
		
		function gltr_translation_status() {
			$exists = get_option("gltr_translation_status");
			if($exists === false){ 
				add_option("gltr_translation_status","");
			}
			$this->save();
		}
		
		function save() {
			update_option("gltr_translation_status",$this);		
		}		

		function load() {
			$status = @get_option("gltr_translation_status");
			if(is_a($status,"gltr_translation_status")) return $status;
			else return null;	
		}
			
    function save_status($status){
    	$this->_status = $status;
    	$this->save();
    }

    function get_status(){
    	return $this->_status;
    }
		
    function save_last_connection_time($last_connection_time){
    	$this->_last_connection_time = $last_connection_time;
    	$this->save();
    }

    function get_last_connection_time(){
    	return $this->_last_connection_time;
    }
		

	}

}


if(!class_exists("gltr_translation_engine")) {
	class gltr_translation_engine {
		var $_name;

		var	$_base_url;

		var $_links_pattern;

		var $_links_replacement;

		var $_languages_matrix;

		var $_available_languages;

		function gltr_translation_engine(
			$name,
			$base_url,
			$links_pattern,
			$links_replacement,
			$languages_matrix,
			$available_languages) {
	      $this->set_name($name);
        $this->set_base_url($base_url);
        $this->set_links_pattern($links_pattern);
        $this->set_links_replacement($links_replacement);
        $this->set_languages_matrix($languages_matrix);
        $this->set_available_languages($available_languages);
		}

    function set_name($name){
    	$this->_name = (string)$name;
    }

		function set_base_url($base_url){
    	$this->_base_url = (string)$base_url;
    }

		function set_links_pattern($links_pattern){
    	$this->_links_pattern = (array)$links_pattern;
    }

		function set_links_replacement($links_replacement){
    	$this->_links_replacement = (string)$links_replacement;
    }

		function set_languages_matrix($languages_matrix){
    	$this->_languages_matrix = (array)$languages_matrix;
    }

		function set_available_languages($available_languages){
    	$this->_available_languages = (array)$available_languages;
    }

    function get_name(){
    	return $this->_name;
    }

		function get_base_url(){
    	return $this->_base_url;
    }

		function get_links_pattern(){
    	return $this->_links_pattern;
    }

		function get_links_replacement(){
    	return $this->_links_replacement;
    }

		function get_languages_matrix(){
    	return $this->_languages_matrix;
    }

		function get_available_languages(){
    	return $this->_available_languages;
    }

    function decode_lang_code($res)
    {
      if ($this->_name == 'promt') {
        if ($res == 'es') $res = 's';
        else if ($res == 'de') $res = 'g';
        else $res = substr($res, 0, 1);
      } else if ($this->_name == 'freetransl') {
        $map = array(
      	  'en'    => 'English',
      	  'es'    => 'Spanish',
      	  'fr'    => 'French',
      	  'de'    => 'German',
      	  'it'    => 'Italian',
      	  'nl'    => 'Dutch',
      	  'pt'    => 'Portuguese',
      	  'no'    => 'Norwegian');
     	  $res = $map[$res];
      }
      return $res;
    }
    
    function build_clean_link($matches){
    	$res = "href=";
    	foreach($this->_match_id as $key=>$val){
    		
    	}
			return urldecode();
		}

	}
}

function get_google_default_langs($srclang){
	$lst = get_google_all_langs();
	$key = $srclang;
	$value = $lst[$key];
	unset($lst[$key]);
	$lst=array_merge(array($key=>$value),$lst); 
  return $lst;
}

function get_google_all_langs(){
	return   array(
    'it'    => 'Italian',
    'ko'    => 'Korean',
    'zh-CN' => 'Chinese (Simplified)',
    'zh-TW' => 'Chinese (Traditional)',
    'pt'    => 'Portuguese',
    'en'    => 'English',
    'de'    => 'German',
    'fr'    => 'French',
    'es'    => 'Spanish',
    'ja'    => 'Japanese',
    'ar'    => 'Arabic',
    'ru'    => 'Russian',
    'el'    => 'Greek',
    'nl'    => 'Dutch',
    'bg'    => 'Bulgarian',
    'cs'    => 'Czech',
    'hr'    => 'Croatian',
    'da'    => 'Danish',
    'fi'    => 'Finnish',
    'hi'    => 'Hindi',
    'pl'    => 'Polish',
    'ro'    => 'Romanian',
    'sv'    => 'Swedish',
    'no'    => 'Norwegian',
    'ca'    => 'Catalan',
    'tl'    => 'Filipino',
    'iw'    => 'Hebrew',
    'id'    => 'Indonesian',
    'lv'    => 'Latvian',
    'lt'    => 'Lithuanian',
    'sr'    => 'Serbian',
    'sk'    => 'Slovak',
    'sl'    => 'Slovenian',
    'uk'    => 'Ukrainian',
    'vi'    => 'Vietnamese',
    'sq'    => 'Albanian',
    'et'    => 'Estonian',
    'gl'    => 'Galician',
    'mt'    => 'Maltese',
    'th'    => 'Thai',
    'tr'    => 'Turkish',
    'hu'    => 'Hungarian',
    'be'    => 'Belarus',
    'ga'    => 'Irish',
    'is'    => 'Icelandic',
    'mk'    => 'Macedonian',
    'ms'    => 'Malay',
    'fa'    => 'Persian'
    );
	
}


$googleEngine = new gltr_translation_engine(
	'google',
	//'http://translate.google.com/translate?hl=en&ie=UTF-8&oe=UTF-8&langpair=${SRCLANG}|${DESTLANG}&u=${URL}&prev=/language_tools',
	'http://translate.google.com/translate?hl=en&sl=${SRCLANG}&tl=${DESTLANG}&u=${URL}',
   array(
    "/=[^\s|>]*u=(http.*?)&amp;([^\s|>]*)([\s|>]{1})/"
	),
	"href=\"\\1\" ",
	array(
  'it'    => array( 'it'=>'Italiano',
                    'ar'=>'Arabo',
                    'bg'=>'Bulgaro',
                    'ca'=>'Catalano',
                    'zh-CN'=>'Cinese (Semplificato)',
                    'hr'=>'Croato',
                    'cs'=>'Ceco',
                    'da'=>'Danese',
                    'nl'=>'Olandese',
                    'en'=>'Inglese',
                    'tl'=>'Filippino',
                    'fi'=>'Finlandese',
                    'fr'=>'Francese',
                    'de'=>'Tedesco',
                    'el'=>'Greco',
										'iw'=>'Ebraico',
                    'hi'=>'Hindi',
										'id'=>'Indonesiano',
                    'ja'=>'Giapponese',
                    'ko'=>'Coreano',
										'lv'=>'Lettone',
										'lt'=>'Lituano',
    								'no'=>'Norvegese',
                    'pl'=>'Polacco',
                    'pt'=>'Portoghese',
                    'ro'=>'Rumeno',
                    'ru'=>'Russo',
										'sr'=>'Serbo',
										'sk'=>'Slovacco',
										'sl'=>'Sloveno',
                    'es'=>'Spagnolo',
                    'sv'=>'Svedese',
                    'uk'=>'Ucraino',
                    'vi'=>'Vietnamita',
                    'sq'=>'Albanese',
								    'et'=>'Estone',
								    'gl'=>'Galician',
								    'mt'=>'Maltese',
								    'th'=>'Tailandese',
								    'tr'=>'Turco',
								    'hu'=>'Ungherese',
								    'be'=> 'Bielorusso',
								    'ga'=> 'Irlandese',
								    'is'=> 'Isladese',
								    'mk'=> 'Macede',
								    'ms'=> 'Malese',
								    'fa'=> 'Persiano'
                    ),
  'ko'    => get_google_default_langs('ko'),                    
  'zh-CN' => get_google_default_langs('zh-CN'),
  'pt' => get_google_default_langs('pt'),
  'en'    => get_google_default_langs('en'),								    
  'de'    => get_google_default_langs('de'),                    
  'fr'    => get_google_default_langs('fr'),                    
  'es'    => get_google_default_langs('es'),                    
  'ja'    => get_google_default_langs('ja'),                    
  'ar'    => get_google_default_langs('ar'),                    
  'ru'    => get_google_default_langs('ru'),                    
  'el'    => get_google_default_langs('el'),                    
  'nl'    => get_google_default_langs('nl'),                    
  'bg'    => get_google_default_langs('bg'),                    
  'cs'    => get_google_default_langs('cs'),                    
  'hr'    => get_google_default_langs('hr'),                    
  'da'    => get_google_default_langs('da'),                    
  'fi'    => get_google_default_langs('fi'),                    
  'hi'    => get_google_default_langs('hi'),                    
  'pl'    => get_google_default_langs('pl'),                    
  'ro'    => get_google_default_langs('ro'),                    
  'no'    => get_google_default_langs('no'),                    
  'sv'    => get_google_default_langs('sv'),                    
  'ca'    => get_google_default_langs('ca'),                                        
  'tl'    => get_google_default_langs('tl'),                                        
  'iw'    => get_google_default_langs('iw'),                                        
  'id'    => get_google_default_langs('id'),                                        
  'lv'    => get_google_default_langs('lv'),                                        
  'lt'    => get_google_default_langs('lt'),                                        
  'sr'    => get_google_default_langs('sr'),                                        
  'sk'    => get_google_default_langs('sk'),                                        
  'sl'    => get_google_default_langs('sl'),                                        
  'uk'    => get_google_default_langs('uk'),                                        
  'vi'    => get_google_default_langs('vi'),                                        
  'sq'    => get_google_default_langs('sq'),                                        
  'et'    => get_google_default_langs('et'),                                        
  'gl'    => get_google_default_langs('gl'),                                        
  'mt'    => get_google_default_langs('mt'),                                        
  'th'    => get_google_default_langs('tm'),                                        
  'tr'    => get_google_default_langs('tr'),                                        
  'hu'    => get_google_default_langs('hu'),                                        
  'be'    => get_google_default_langs('be'),                                        
  'ga'    => get_google_default_langs('ga'),                                        
  'is'    => get_google_default_langs('is'),                                        
  'mk'    => get_google_default_langs('mk'),                                        
  'ms'    => get_google_default_langs('ms'),                                        
  'fa'    => get_google_default_langs('fa'),                                        

  ),

  get_google_all_langs()

	);


$babelfishEngine = new gltr_translation_engine(
	'babelfish',
	'http://babelfish.yahoo.com/babelfish/trurl_pagecontent?lp=${SRCLANG}_${DESTLANG}&url=${URL}',
	//array("/<a(.*?)href=\"(.*?)url=(.*?)\"([\s|>]{1})/i"),
	array(
		"/href=[']{1}[^']*url=(.*?)[']{1}/",
		"/href=[\"]{1}[^\"]*url=(.*?)[\"]{1}/"
	),		
	"<a href=\"\\1\" ",
	array(
  'it'    => array( 'it'=>'Italiano',
                    'en'=>'Inglese',
                    'fr'=>'Francese'),
  'ko'    => array( 'ko'=>'Korean',
                    'en'=>'English'),
  'zh' 		=> array( 'zh'=>'Chinese (Simplified)',
                    'en'=>'English'),
  'zt' 		=> array( 'zt'=>'Chinese (Traditional)',
                    'en'=>'English'),
  'pt'    => array( 'pt'=>'Portugues',
                    'en'=>'Ingles',
                    'fr'=>'Francais'),//to be verified
  'en'    => array( 'en'=>'English',
                    'zh'=>'Chinese (Simplified)',
                    'zt'=>'Chinese (Traditional)',
                    'nl'=>'Dutch',
                    'fr'=>'French',
                    'de'=>'German',
                    'el'=>'Greek',
                    'it'=>'Italian',
                    'ja'=>'Japanese',
                    'ko'=>'Korean',
                    'pt'=>'Portuguese',
                    'ru'=>'Russian',
                    'es'=>'Spanish'),
  'nl'    => array( 'nl'=>'Dutch',
  									'en'=>'English',
  									'fr'=>'French'),
  'de'    => array( 'de'=>'Deutsch',
                    'en'=>'Englisch',
                    'fr'=>'Franzosisch'),
  'fr'    => array( 'fr'=>'Francais',
                    'en'=>'Anglais',
                    'de'=>'Allemand',
                    'el'=>'Grec',
                    'it'=>'Italien',
                    'pt'=>'Portugais',
                    'es'=>'Espagnol',
                    'nl'=>'Hollandais'),
  'el'    => array( 'el'=>'Greek',
  									'en'=>'English',
  									'fr'=>'French'),
  'es'    => array( 'es'=>'Espanol',
                    'en'=>'Ingles'),
  'ja'    => array( 'ja'=>'Japanese',
                    'en'=>'English'),
  'ru'    => array( 'ru'=>'Russian',
                    'en'=>'English')
  ),

	array(
	  'it'    => 'Italian',
	  'ko'    => 'Korean',
	  'zh' 		=> 'Chinese (Simplified)',
	  'zt' 		=> 'Chinese (Traditional)',
	  'pt'    => 'Portuguese',
	  'en'    => 'English',
	  'el'    => 'Greek',
	  'nl'    => 'Dutch',
	  'de'    => 'German',
	  'fr'    => 'French',
	  'es'    => 'Spanish',
	  'ja'    => 'Japanese',
	  'ru'		=> 'Russian'
	  )
	);

$promtEngine = new gltr_translation_engine(
  'promt',
  'http://www.online-translator.com/url/translation.aspx?autotranslate=on&sourceURL=${URL}&direction=${SRCLANG}${DESTLANG}',
  //http://www.online-translator.com//url/translation.aspx?direction=ie&template=General&autotranslate=on&transliterate=&showvariants=&sourceURL=http://www.nothing2hide.net
  //old version 'http://beta.online-translator.com/url/tran_url.asp?prmtlang=en&autotranslate=on&url=${URL}&direction=${SRCLANG}${DESTLANG}',
  //old version 'http://www.online-translator.com/url/tran_url.asp?url=${URL}&direction=${SRCLANG}${DESTLANG}&cp1=UTF-8&cp2=UTF-8&autotranslate=on',
  //array("/href=\"(.*?)url=(.*?)\"([\s|>]{1})/i"),
	array(
		"/href=[']{1}[^']*sourceURL=([^'|#]*)([#]{0,1}[^']*)[']{1}/",
		"/href=[\"]{1}[^\"]*sourceURL=([^\"|#]*)([#]{0,1}[^\"]*)[\"]{1}/"
	),  
	"href=\"\\1\" ",
  array(
  'it'    => array( 'it'=>'Italiano',
                    'en'=>'Inglese',
                    'ru'=>'Russo'),
  'pt'    => array( 'pt'=>'Portugues',
                    'en'=>'Ingles'),//to be verified
  'en'    => array( 'en'=>'English',
                    'fr'=>'French',
                    'de'=>'German',
                    'it'=>'Italian',
                    'pt'=>'Portuguese',
                    'ru'=>'Russian',
                    'es'=>'Spanish'),
  'de'    => array( 'de'=>'Deutsch',
                    'en'=>'Englisch',
                    'fr'=>'Franzosisch',
                    'es'=>'Spanish',
                    'ru'=>'Russian'
                    ),
  'fr'    => array( 'fr'=>'Francais',
                    'en'=>'Anglais',
                    'de'=>'Allemand',
                    'ru'=>'Russian', //
                    'es'=>'Espagnol'),
  'es'    => array( 'es'=>'Espanol',
                    'en'=>'Ingles',
                    'ru'=>'Russian',
                    'de'=>'German',
                    'fr'=>'French'
                    ),
  'ru'    => array( 'ru'=>'Russian',
                    'en'=>'English',
                    'fr'=>'French',
                    'de'=>'German',
                    'es'=>'Spanish'
                    )
  ),



  array(
    'it'    => 'Italian',
    'pt'    => 'Portuguese',
    'en'    => 'English',
    'de'    => 'German',
    'fr'    => 'French',
    'es'    => 'Spanish',
    'ru'    => 'Russian'
    )
  );

$freetranslationEngine = new gltr_translation_engine(
  'freetransl',
  'http://fets5.freetranslation.com/?sequence=core&language=${SRCLANG}/${DESTLANG}&url=${URL}',
  array("/href=\"([^\"]*)\"/i"),
  "href=\"\\1\"", 
  array(
  'it'    => array( 'it'=>'Italiano',
                    'en'=>'Inglese'),
  'pt'    => array( 'pt'=>'Portugues',
                    'en'=>'Ingles'),//to be verified
  'en'    => array( 'en'=>'English',
                    'es'=>'Spanish',
                    'fr'=>'French',
                    'de'=>'German',
                    'it'=>'Italian',
                    'nl'=>'Dutch',
                    'pt'=>'Portuguese',
                    'no'=> 'Norwegian'
                    ),
  'de'    => array( 'de'=>'Deutsch',
                    'en'=>'Englisch'
                    ),
  'fr'    => array( 'fr'=>'Francais',
                    'en'=>'Anglais'
                    ),
  'es'    => array( 'es'=>'Espanol',
                    'en'=>'Ingles',
                    ),
  'nl'    => array( 'nl'=>'Dutch',
                    'en'=>'English'
                    )
  ),

  array(
    'it'    => 'Italian',
    'pt'    => 'Portuguese',
    'en'    => 'English',
    'de'    => 'German',
    'fr'    => 'French',
    'es'    => 'Spanish',
    'no'    => 'Norwegian',
    'nl'    => 'Dutch',
    )
  );

$well_known_extensions =  
    array('swf','gif','jpg','jpeg','bmp','gz','zip','rar','tar','png','xls',
    'doc','ppt','tiff','avi','mpeg','mp3','mov','mp4','c','sh','bat');
$gltr_available_engines = array();
$gltr_available_engines['google'] = $googleEngine;
$gltr_available_engines['promt'] = $promtEngine;
$gltr_available_engines['babelfish'] = $babelfishEngine;
$gltr_available_engines['freetransl'] = $freetranslationEngine;


/*Lets add some default options if they don't exist*/
add_option('gltr_base_lang', 'en');
add_option('gltr_col_num', '0');
add_option('gltr_html_bar_tag', 'MAP');
add_option('gltr_my_translation_engine', 'google');
add_option('gltr_preferred_languages', array());
add_option('gltr_ban_prevention', true);
add_option('gltr_enable_debug', false);
add_option('gltr_conn_interval',300);
add_option('gltr_sitemap_integration',false);
add_option("gltr_last_connection_time",0);
add_option("gltr_translation_status","unknown");
add_option("gltr_cache_expire_time",15);
add_option('gltr_use_302',false);

if (function_exists('gzcompress')){
	add_option("gltr_compress_cache",false);
} else {
	add_option("gltr_compress_cache",false);
}

if (!function_exists('str_ireplace')){
  function str_ireplace($search,$replace,$subject){
    $token = chr(1);
    $haystack = strtolower($subject);
    $needle = strtolower($search);
    while (($pos=strpos($haystack,$needle))!==FALSE){
      $subject = substr_replace($subject,$token,$pos,strlen($search));
      $haystack = substr_replace($haystack,$token,$pos,strlen($search));
    }
    $subject = str_replace($token,$replace,$subject);
    return $subject;
  }
} 
if( !defined('WP_CONTENT_DIR') ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
$gltr_cache_dir = WP_CONTENT_DIR . "/gt-cache";
$gltr_stale_dir = WP_CONTENT_DIR . "/gt-cache/stale";
$gltr_merged_image=dirname(__file__) . '/gltr_image_map.png';
$gltr_uri_index = array();

$gltr_VERSION='1.3.2';
?>