<?php
/*
Name: "reCaptcha"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: Use recaptcha to verify user input
Version: 3
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

// Path to recaptchalib.php
define("TDOMF_RECAPTCHALIB_PATH",TDOMF_WIDGET_PATH.'recaptcha/recaptchalib.php');

  /** 
   * reCaptcha Widget. Use recaptcha to verify user input
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 3.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetReCaptcha extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetReCaptcha() {
          $this->enableValidate();
          $this->enableWidgetTitle();
          $this->enableControl(true,700,500);
          $this->setInternalName('recaptcha');
          $this->setDisplayName(__('reCaptcha','tdomf'));
          $this->setOptionKey('tdomf_recaptcha_widget');
          $this->setModes(array('new','edit'));
          $this->enableAdminError();
          $this->start();
      }

/* 1. report error */
      
      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
         extract($args);
         $output = "";
         
         if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
             $use_ssl = true;
         } else {
             $use_ssl = false;
         }

         if(!function_exists('recaptcha_get_html')) {
             @require_once(TDOMF_RECAPTCHALIB_PATH);
         }
    
         $output .= <<<END
		<script type='text/javascript'>
		var RecaptchaOptions = { theme : '{$options['theme']}', lang : '{$options['language']}' , tabindex : 30 };
		</script>
END;

        $form_data = tdomf_get_form_data($args['tdomf_form_id']);
        $error = null;
        if(isset($form_data['recaptcha_error'])) {
            $error = $form_data['recaptcha_error'];
        }

        $output .= recaptcha_get_html ($options['publickey'], $error, $use_ssl, $options['xhtml']);
        return $output;
      }
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview) {
          
           // don't bother validating for preview
           if($preview) {
               return NULL;
           }
          
          extract($args);
          $output = "";
          
          if (empty($args['recaptcha_response_field'])) {
              return __('Please complete the reCAPTCHA.','tdomf');
          }
            
          if(!function_exists('recaptcha_check_answer')) {
              @require_once(TDOMF_RECAPTCHALIB_PATH);
          }  
  
          $response = recaptcha_check_answer($options['privatekey'],
              $_SERVER['REMOTE_ADDR'],
              $args['recaptcha_challenge_field'],
              $args['recaptcha_response_field']);

          if (!$response->is_valid) {
              $form_data = tdomf_get_form_data($args['tdomf_form_id']);  
              $form_data['recaptcha_error'] = $response->error;
              tdomf_save_form_data($args['tdomf_form_id'],$form_data);
              if ($response->error == 'incorrect-captcha-sol') {
                return __('That reCAPTCHA was incorrect.','tdomf');
              } else {
                tdomf_log_message('reCAPTCHA error ' . $response->error . '. Please refer to <a href="http://recaptcha.net/apidocs/captcha/">reCaptcha docs</a> for more information', TDOMF_LOG_ERROR);
                return __('Invalid reCAPTCHA configuration.','tdomf');
              }
          }
          return NULL;
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return String
       */
      function getOptions($form_id) {
          $defaults = array(   'publickey' => '',
                               'privatekey' => '',
                               'theme' => 'red',
                               'language' => 'en',
                               'xhtml' => false,
                               'plugin' => false,
                               'text-rows' => 10, 
                               'quicktags' => false,
                               'restrict-tags' => true,
                               'allowable-tags' => "<p><b><em><u><strong><a><img><table><tr><td><blockquote><ul><ol><li><br><sup>",
                               'char-limit' => 0,
                               'word-limit' => 0 );
          $recaptcha_options = get_option('recaptcha');
          if($recaptcha_options != false) {
              $defaults['publickey'] = $recaptcha_options['pubkey'];
              $defaults['privatekey'] = $recaptcha_options['privkey'];
              $defaults['theme'] = $recaptcha_options['re_theme']; 
              $defaults['language'] = $recaptcha_options['re_lang'];
              $defaults['xhtml'] = $recaptcha_options['re_xhtml'];
          }
          $options = TDOMF_Widget::getOptions($form_id); 
          $options = wp_parse_args($options, $defaults);
          if($options['plugin'] && $recaptcha_options != false) {
              $options['publickey'] = $recaptcha_options['pubkey'];
              $options['privatekey'] = $recaptcha_options['privkey'];
              $options['theme'] = $recaptcha_options['re_theme']; 
              $options['language'] = $recaptcha_options['re_lang'];
              $options['xhtml'] = $recaptcha_options['re_xhtml'];
          }
          return $options;
      }   
      
      /**
       * Configuration panel for widget
       * 
       * @access public
       */      
      function control($options,$form_id) {
          
          // Store settings for this widget
          //
          if ( $_POST[$this->internalName.'-submit'] ) {
                 $newoptions['publickey'] = $_POST['recaptcha-publickey'];
                 $newoptions['privatekey'] = $_POST['recaptcha-privatekey'];
                 $newoptions['theme'] = $_POST['recaptcha-themekey']; 
                 $newoptions['language'] = $_POST['recaptcha-language'];
                 $newoptions['xhtml'] = isset($_POST['recaptcha-xhtml']);
                 $newoptions['plugin'] = isset($_POST['recaptcha-plugin']);
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id);
          }

          if(!function_exists('recaptcha_get_signup_url')) {
              @require_once(TDOMF_RECAPTCHALIB_PATH);
          }  
          
          // get blog domain
          $uri = parse_url(get_settings('siteurl'));
          $blogdomain = $uri['host'];
          
          // Display control panel for this widget
          //
          extract($options);
          ?>
<div>          
          <?php $this->controlCommon($options); ?>
          
          <small><?php _e('If this option is enabled and the reCaptcha plugin is active, the settings for comments will overwrite any configuration below.','tdomf'); ?></small><br/>
    <input type="checkbox" name="recaptcha-plugin" id="recaptcha-plugin" <?php if($options['plugin']) echo "checked"; ?> >
    <label for="recaptcha-plugin" style="line-height:35px;"><?php _e('Use comment settings from <a href="http://wordpress.org/extend/plugins/wp-recaptcha/">reCaptcha plugin</a> if active',"tdomf"); ?></label>
    
    <br/><br/>
    
    <small><?php printf(__('reCAPTCHA requires an API key, consisting of a "public" and a "private" key. You can sign up for a <a href="%s" target="0">free reCAPTCHA key</a>.','tdomf'),recaptcha_get_signup_url ($blogdomain, 'wordpress')); ?></small>
    <br/><br/>

    <label class="which-key" for="recaptcha-publickey" style="line-height:35px;">Public Key:</label>
    <input name="recaptcha-publickey" id="recaptcha-publickey" size="40" value="<?php  echo $options['publickey']; ?>" />
    
    <br />
    <label class="which-key" for="recaptcha-privatekey" style="line-height:35px;">Private Key:</label>
    <input name="recaptcha-privatekey" id="recaptcha-privatekey" size="40" value="<?php  echo $options['privatekey']; ?>" />

    <br/>

    <div class="theme-select">
    <label for="recaptcha-themekey" style="line-height:35px;"><?php _e('Theme:','tdomf'); ?></label>
    <select name="recaptcha-themekey" id="recaptcha-themekey">
    <option value="red" <?php if($options['theme'] == 'red'){echo 'selected="selected"';} ?>><?php _e('Red','tdomf'); ?></option>
    <option value="white" <?php if($options['theme'] == 'white'){echo 'selected="selected"';} ?>><?php _e('White','tdomf'); ?></option>
    <option value="blackglass" <?php if($options['theme'] == 'blackglass'){echo 'selected="selected"';} ?>><?php _e('Black Glass','tdomf'); ?></option>
    <option value="clean" <?php if($options['theme'] == 'clean'){echo 'selected="selected"';} ?>><?php _e('Clean','tdomf'); ?></option>
    </select>
    </div>

    <br/>
    
    <div class="lang-select">
    <label for="recaptcha-language" style="line-height:35px;"><?php _e('Language:','tdomf'); ?></label>
    <select name="recaptcha-language" id="recaptcha-languageg">
    <option value="en" <?php if($options['language'] == 'en'){echo 'selected="selected"';} ?>><?php _e('English','tdomf'); ?></option>
    <option value="nl" <?php if($options['language'] == 'nl'){echo 'selected="selected"';} ?>><?php _e('Dutch','tdomf'); ?></option>
    <option value="fr" <?php if($options['language'] == 'fr'){echo 'selected="selected"';} ?>><?php _e('French','tdomf'); ?></option>
    <option value="de" <?php if($options['language'] == 'de'){echo 'selected="selected"';} ?>><?php _e('German','tdomf'); ?></option>
    <option value="pt" <?php if($options['language'] == 'pt'){echo 'selected="selected"';} ?>><?php _e('Portuguese','tdomf'); ?></option>
    <option value="ru" <?php if($options['language'] == 'ru'){echo 'selected="selected"';} ?>><?php _e('Russian','tdomf'); ?></option>
    <option value="es" <?php if($options['language'] == 'es'){echo 'selected="selected"';} ?>><?php _e('Spanish','tdomf'); ?></option>
    <option value="tr" <?php if($options['language'] == 'tr'){echo 'selected="selected"';} ?>><?php _e('Turkish','tdomf'); ?></option>
    </select>
    </label>
    </div>

    <br/>
    
    <input type="checkbox" name="recaptcha-xhtml" id="recaptcha-xhtml" <?php if($options['xhtml']) echo "checked"; ?> >
    <label for="recaptcha-xhtml" style="line-height:35px;"><?php _e("XHTML 1.0 Strict compliant.","tdomf"); ?></label><br/>
    <small><?php _e('Bad for users who don\'t have Javascript enabled in their browser (Majority do).','tdomf'); ?></small>
    
    </div>
    
<?php
      }
     
     /** 
     * Display error to user for mis-configuration
     * 
     * @access public
     * @return String
     */       
     function adminError($options,$form_id) {
         $output = "";  
         if(empty($options['publickey']) || empty($options['privatekey'])) {
             $output .= __('<b>Error</b>: Widget "reCaptcha" is missing the private and/or public keys and cannot function if theses are not set.','tdomf');
         }
         return $output;
     }      

  }
  
    
  // Create and start the widget
  //
  global $tdomf_widget_recaptcha;
  $tdomf_widget_recaptcha = new TDOMF_WidgetReCaptcha();

?>