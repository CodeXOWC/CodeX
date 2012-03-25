<?php
/*
Name: "Who Am I"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: One of the default widgets, allowing for more detailed user information
Version: 1
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

///////////////////////
// "Who Am I" Widget //
///////////////////////

  /** 
   * Who Am I Widget. This widget allows users to enter user information
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 1.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetWhoami extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetWhoami() {
          $this->enableHack();
          $this->enablePreview();
          $this->enablePreviewHack();
          $this->enableValidate();
          $this->enableValidatePreview();
          $this->enablePost();
          $this->enableWidgetTitle();
          $this->enableControl(true,600,420);
          $this->setInternalName('who-am-i');
          $this->setDisplayName(__('Who Am I','tdomf'));
          $this->setOptionKey('tdomf_whoami_widget');
          $this->setModes(array('new','edit'));
          $this->start();
      }
      
      /**
       * Store user submitted defaults as cookies
       *
       * @access public
       */
      function tdomf_widget_whoami_store_cookies($name = "", $email = "", $web = "") {
          setcookie("tdomf_whoami_widget_name",$name, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
          setcookie("tdomf_whoami_widget_email",$email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
          setcookie("tdomf_whoami_widget_web",$web, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return String
       */
      function getOptions($form_id) {
          $defaults = array(   'name-enable' => true,
                               'name-required' => true,
                               'email-enable' => true,
                               'email-required' => true,
                               'webpage-enable' => true,
                               'webpage-required' => false );
          $options = TDOMF_Widget::getOptions($form_id); 
          $options = wp_parse_args($options, $defaults);
          return $options;
      }   
      
      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
         global $current_user;
         get_currentuserinfo();
         extract($args);
         $output = "";
         
         // Check if values set in cookie
         if(!isset($whoami_name) && isset($_COOKIE['tdomf_whoami_widget_name'])) {
             $whoami_name = $_COOKIE['tdomf_whoami_widget_name'];
         }
  
         if(!isset($whoami_email) && isset($_COOKIE['tdomf_whoami_widget_email'])) {
             $whoami_email = $_COOKIE['tdomf_whoami_widget_email'];
         }
  
         if(!isset($whoami_webpage) && isset($_COOKIE['tdomf_whoami_widget_web'])) {
             $whoami_webpage = $_COOKIE['tdomf_whoami_widget_web'];
         }
  
         // default webpage value
         if(!isset($whoami_webpage) || empty($whoami_webpage)){ $whoami_webpage = "http://"; }
  
         // If user is logged in, nothing much more to do here!
         if(is_user_logged_in()) {
             $output .= "<p>".sprintf(__("You are currently logged in as <a href=\"%s\">%s</a>.","tdomf"),get_bloginfo('wpurl').'/wp-admin',$current_user->display_name);
             if(current_user_can('manage_options')) {
                 $output .= " <a href='".get_option('siteurl')."/wp-admin/admin.php?page=".TDOMF_FOLDER."'>".__("You can configure this form &raquo;","tdomf")."</a>";
             }
             $output .= "</p>";
         } else {
            $our_uri = esc_url($_SERVER['REQUEST_URI']);
            $login_uri = get_bloginfo('wpurl').'/wp-login.php?redirect_to='.$our_uri;
            $reg_uri = get_bloginfo('wpurl').'/wp-register.php?redirect_to='.$our_uri;
               
            $output .= "<p>".sprintf(__("We do not know who you are. Please supply your name and email address. Alternatively you can <a href=\"%s\">log in</a> if you have a user account or <a href=\"%s\">register</a> for a user account if you do not have one.","tdomf"),$login_uri,$reg_uri)."</p>";
        
            if($options['name-enable']) {
             $output .=  "<label for='whoami_name'";
             if($options['name-required']) {
                $output .= ' class="required" ';
             }
             $output .= ">".__("Name:","tdomf").' <br/><input type="text" value="'.htmlentities($whoami_name,ENT_QUOTES,get_bloginfo('charset')).'" name="whoami_name" id="whoami_name" />';
             if($options['name-required']) {
                $output .= __(" (Required)","tdomf");
             }
             $output .= "</label>";
             $output .= "<br/><br/>\n";
            }
        
            if($options['email-enable']) {
                 $output .=    "<label for='whoami_email'";
             if($options['email-required']) {
                $output .= ' class="required" ';
             }
             $output .= ">".__("Email:","tdomf").'<br/><input type="text" value="'.htmlentities($whoami_email,ENT_QUOTES,get_bloginfo('charset')).'" name="whoami_email" id="whoami_email" />';
                 if($options['email-required']) {
                    $output .= __(" (Required)","tdomf");
                 }
                 $output .= "</label>";
                 $output .= "<br/><br/>\n";
              }
        
           if($options['webpage-enable']) {
                 $output .=    "<label for='whoami_webpage'";
             if($options['webpage-required']) {
                $output .= ' class="required" ';
             }
             $output .= ">".__("Webpage:","tdomf").'<br/><input type="text" value="'.htmlentities($whoami_webpage,ENT_QUOTES,get_bloginfo('charset')).'" name="whoami_webpage" id="whoami_webpage" />';
                 if($options['webpage-required']) {
                    $output .= __(" (Required)","tdomf");
                 }
                 $output .= "</label>";         
                 $output .= "<br/><br/>\n";
              }
          }
          return $output;
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
                 $newoptions['name-enable'] = isset($_POST['who_am_i-name-enable']);;
                 $newoptions['name-required'] = isset($_POST['who_am_i-name-required']);
                 $newoptions['email-enable'] = isset($_POST['who_am_i-email-enable']);
                 $newoptions['email-required'] = isset($_POST['who_am_i-email-required']);
                 $newoptions['webpage-enable'] = isset($_POST['who_am_i-webpage-enable']);
                 $newoptions['webpage-required'] = isset($_POST['who_am_i-webpage-required']);
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);
          ?>
<div>          
          <?php $this->controlCommon($options); ?>
          
<h4><?php _e("Submitter Name","tdomf"); ?></h4>
<label for="who_am_i-name-enable" style="line-height:35px;"><?php _e("Show","tdomf"); ?> <input type="checkbox" name="who_am_i-name-enable" id="who_am_i-name-enable" <?php if($options['name-enable']) echo "checked"; ?> ></label>
<label for="who_am_i-name-required" style="line-height:35px;"><?php _e("Required","tdomf"); ?> <input type="checkbox" name="who_am_i-name-required" id="who_am_i-name-required" <?php if($options['name-required']) echo "checked"; ?> ></label>

<h4><?php _e("Submitter Webpage","tdomf"); ?></h4>
<label for="who_am_i-webpage-enable" style="line-height:35px;"><?php _e("Show","tdomf"); ?> <input type="checkbox" name="who_am_i-webpage-enable" id="who_am_i-webpage-enable" <?php if($options['webpage-enable']) echo "checked"; ?> ></label>
<label for="who_am_i-webpage-required" style="line-height:35px;"><?php _e("Required","tdomf"); ?> <input type="checkbox" name="who_am_i-webpage-required" id="who_am_i-webpage-required" <?php if($options['webpage-required']) echo "checked"; ?> ></label>

<h4><?php _e("Submitter Email","tdomf"); ?></h4>
<label for="who_am_i-email-enable" style="line-height:35px;"><?php _e("Show","tdomf"); ?> <input type="checkbox" name="who_am_i-email-enable" id="who_am_i-email-enable" <?php if($options['email-enable']) echo "checked"; ?> ></label>
<label for="who_am_i-email-required" style="line-height:35px;"><?php _e("Required","tdomf"); ?> <input type="checkbox" name="who_am_i-email-required" id="who_am_i-email-required" <?php if($options['email-required']) echo "checked"; ?> ></label>
</div>
        <?php
      }
      
      /**
       * Generate preview of widget
       * 
       * @access public
       * @return String
       */      
      function preview($args,$options) {
          extract($args);
          global $current_user;
          get_currentuserinfo();
          $output = "";
          if(is_user_logged_in()) {
              $output = sprintf(__("Submitted by %s.","tdomf"),$current_user->display_name);
          } else {
              $link = "";
              if(isset($args['whoami_webpage'])){
                  $link .= "<a href=\"".$args['whoami_webpage']."\">";
              }
              if(isset($args['whoami_name'])){
                  $link .= tdomf_protect_input($args['whoami_name']);
              } else {
                  $link .= __("unknown","tdomf");
              }
              if(isset($args['whoami_webpage'])){
                  $link .= "</a>";
              }
              $output = sprintf(__("Submitted by %s.","tdomf"),$link);
          }
          return $output;
      }
      
      /**
       * Generate preview hack code of widget
       * 
       * @access public
       * @return String
       */      
      function previewHack($args,$options) {
          if(!$options['title-enable'] && !$options['text-enable']) { return ""; }
          extract($args);          
          $output = "";
          $output .= "\t<?php if(is_user_logged_in()) { ?>\n";
          $output .= "\t\t".sprintf(__("Submitted by %s.","tdomf"),TDOMF_MACRO_USERNAME)."\n";  
          $output .= "\t<?php } else { ?>\n";
        
          $nonreg_user  = "<?php if(isset(\$post_args['whoami_webpage'])){ ?>";
          $nonreg_user .= "<a href=\"<?php echo \$whoami_webpage; ?>\">";
          $nonreg_user .= "<?php } ?>";
        
          $nonreg_user .= "<?php if(isset(\$post_args['whoami_name'])){ ";
          $nonreg_user .= "echo tdomf_protect_input(\$whoami_name); ";
          $nonreg_user .= "} else { ?>";
          $nonreg_user .= __("unknown","tdomf");
          $nonreg_user .= "<?php } ?>";
        
          $nonreg_user .= "<?php if(isset(\$post_args['whoami_webpage'])){ ?>";
          $nonreg_user .= "</a>";
          $nonreg_user .= "<?php } ?>";
        
          $output .= "\t\t".sprintf(__("Submitted by %s.","tdomf"),$nonreg_user)."\n";
        
          $output .= "\t<?php } ?>\n";
          
          return $output;
      }
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview) {
          // only preview - no validation required
          if($preview) {
              return NULL;
          }
          // if user logged in, no validation required
          if(is_user_logged_in()){
              return NULL;
          }
          // do validation
          extract($args);
          $output = "";
          if($options['name-enable'] && $options['name-required']
              && (empty($whoami_name) || trim($whoami_name) == "")) {
            $output .= __("You must specify a name.","tdomf");
          }
          if($options['email-enable'] && $options['email-required']
              && (empty($whoami_email) || trim($whoami_email) == "")) {
            if($output != "") { $output .= "<br/>"; }
            $output .= __("You must specify a email address.","tdomf");
          }
          // if something entered for email, check it!
          else if((($options['email-enable'] && $options['email-required']) 
              || ($options['email-enable'] && trim($whoami_email) != "")) 
              && !tdomf_check_email_address($whoami_email)) {
            if($output != "") { $output .= "<br/>"; }
            $output .= __("Your email address does not look correct.","tdomf");
          }
          if($options['webpage-enable'] && $options['webpage-required']
              && (empty($whoami_webpage) || trim($whoami_webpage) == "")) {
            if($output != "") { $output .= "<br/>"; }
            $output .= __("You must specify a valid webpage.","tdomf");
          }
          // if something entered for URL, check it!
          else if((($options['webpage-enable'] && $options['webpage-required'])
              || ($options['webpage-enable'] && trim($whoami_webpage) != "http://" && trim($whoami_webpage) != ""))
              && !tdomf_check_url($whoami_webpage)) {
          if($output != "") { $output .= "<br/>"; }
          $output .= __("Your webpage URL does not look correct.<br/>","tdomf");
          }
          // return output if any
          if($output != "") {
              return $output;
          }
          return NULL;
      }
      
      /**
       * Process form input for widget
       * 
       * @access public
       * @return Mixed
       */
      function post($args,$options) {
          global $current_user;
          get_currentuserinfo();
          extract($args);
          
          // if sumbitting a new post (as opposed to editing)
          // make sure to *append* to post_content. For editing, overwrite.
          //
          if(TDOMF_Widget::isEditForm($mode)) {
              $edit_data = tdomf_get_data_edit($edit_id);
              if(isset($whoami_name)) {
                  $edit_data[TDOMF_KEY_NAME] = tdomf_protect_input($whoami_name);
              } else {
                  $whoami_name = "";
              }
              if(isset($whoami_webpage)) {
                  $edit_data[TDOMF_KEY_WEB] = $whoami_webpage;
              } else {
                  $whoami_webpage = "";
              }
              if(isset($whoami_email)) {
                  $edit_data[TDOMF_KEY_EMAIL] = $whoami_email;
              } else {
                  $whoami_email = "";
              }
              if(is_user_logged_in()) {
                  if($current_user->ID != get_option(TDOMF_DEFAULT_AUTHOR)){
                   $edit_data[TDOMF_KEY_USER_ID] = $current_user->ID;
                   $edit_data[TDOMF_KEY_USER_NAME] = $current_user->user_login;
                   $edit_data[TDOMF_KEY_NAME] = $current_user->display_name;
                   $edit_data[TDOMF_KEY_EMAIL] = $current_user->user_email;
                   $edit_data[TDOMF_KEY_WEB] = $current_user->user_url;
                   update_usermeta($current_user->ID, TDOMF_KEY_FLAG, true);
                  }
              }            
              tdomf_set_data_edit($edit_data,$edit_id);
          } else {
              if(isset($whoami_name)) {
                  add_post_meta($post_ID, TDOMF_KEY_NAME, tdomf_protect_input($whoami_name), true);
              } else {
                  $whoami_name = "";
              }
              if(isset($whoami_webpage)) {
                  add_post_meta($post_ID, TDOMF_KEY_WEB, $whoami_webpage, true);
              } else {
                  $whoami_webpage = "";
              }
              if(isset($whoami_email)) {
                  add_post_meta($post_ID, TDOMF_KEY_EMAIL, $whoami_email, true);
              } else {
                  $whoami_email = "";
              }
              if(is_user_logged_in()) {
                  if($current_user->ID != get_option(TDOMF_DEFAULT_AUTHOR)){
                   add_post_meta($post_ID, TDOMF_KEY_USER_ID, $current_user->ID, true);
                   add_post_meta($post_ID, TDOMF_KEY_USER_NAME, $current_user->user_login, true);
                   add_post_meta($post_ID, TDOMF_KEY_NAME, $current_user->display_name, true);
                   add_post_meta($post_ID, TDOMF_KEY_EMAIL, $current_user->user_email, true);
                   add_post_meta($post_ID, TDOMF_KEY_WEB, $current_user->user_url, true);
                   update_usermeta($current_user->ID, TDOMF_KEY_FLAG, true);
                  }
              }
          }
          TDOMF_WidgetWhoami::tdomf_widget_whoami_store_cookies(tdomf_protect_input($whoami_name),$whoami_email,$whoami_webpage);
          return NULL;
      }

      /**
       * Code for hacking form output
       * 
       * @access public
       * @return String
       */
      function formHack($args,$options) {
            global $current_user;
            get_currentuserinfo();
            extract($args);
              
            $output = "";  
  
            // logged in version
  
            $output .= "\t\t<?php if(is_user_logged_in()) { ?>\n";
            $tdomfurl = get_bloginfo('wpurl')."/wp-admin/admin.php?page=".TDOMF_FOLDER;
            $output .= <<<EOT
			<p>You are currently logged in as %%USERNAME%%.
			<?php if(current_user_can('manage_options')) { ?>
				<a href='$tdomfurl'>You can configure this form &raquo;</a>
			<?php } ?></p>
EOT;
  
            // logged out version
  
            $output .= "\n\t\t<?php } else { ?>\n";
  
            $login_uri = get_bloginfo('wpurl').'/wp-login.php?redirect_to='.TDOMF_MACRO_FORMURL;
            $reg_uri = get_bloginfo('wpurl').'/wp-register.php?redirect_to='.TDOMF_MACRO_FORMURL;
  
            $output .= "\t\t\t<p>".sprintf(__("We do not know who you are. Please supply your name and email address. Alternatively you can <a href=\"%s\">log in</a> if you have a user account or <a href=\"%s\">register</a> for a user account if you do not have one.","tdomf"),$login_uri,$reg_uri)."</p>\n";
  
            if($options['name-enable']) {
                $output .= <<<EOT
			<?php if(!isset(\$whoami_name) && isset(\$_COOKIE['tdomf_whoami_widget_name'])) {
				\$whoami_name = \$_COOKIE['tdomf_whoami_widget_name'];
			} ?>
EOT;
                $output .=  "\n\t\t\t<label for='whoami_name'";
                if($options['name-required']) {
                    $output .= ' class="required" ';
                }
                $output .= ">".__("Name:","tdomf")."\n\t\t\t\t<br/>\n\t\t\t\t<input type=\"text\" value=\"";
                $output .= '<?php echo htmlentities($whoami_name,ENT_QUOTES,get_bloginfo(\'charset\')); ?>';
                $output .= '" name="whoami_name" id="whoami_name" />';
                if($options['name-required']) {
                    $output .= __(" (Required)","tdomf");
                }
                $output .= "\n\t\t\t</label>";
                $output .= "\n\t\t\t<br/>\n\t\t\t<br/>\n";
            }
  
            if($options['email-enable']) {
                $output .= <<<EOT
			<?php if(!isset(\$whoami_email) && isset(\$_COOKIE['tdomf_whoami_widget_name'])) {
				\$whoami_email = \$_COOKIE['tdomf_whoami_widget_email'];
			} ?>
EOT;
                $output .=    "\n\t\t\t<label for='whoami_email'";
                if($options['email-required']) {
                    $output .= ' class="required" ';
                }
                $output .= ">".__("Email:","tdomf")."\n\t\t\t\t<br/>\n\t\t\t\t<input type=\"text\" value=\"";
                $output .= '<?php echo htmlentities($whoami_email,ENT_QUOTES,get_bloginfo(\'charset\')); ?>';
                $output .= '" name="whoami_email" id="whoami_email" />';
                if($options['email-required']) {
                    $output .= __(" (Required)","tdomf");
                }
                $output .= "\n\t\t\t</label>";
                $output .= "\n\t\t\t<br/>\n\t\t\t<br/>\n";
            }
  
            if($options['webpage-enable']) {
                $output .= <<<EOT
			<?php if(!isset(\$whoami_webpage) && isset(\$_COOKIE['tdomf_whoami_widget_name'])) {
				\$whoami_webpage = \$_COOKIE['tdomf_whoami_widget_webpage'];
			}
			if(!isset(\$whoami_webpage) || empty(\$whoami_webpage)){ \$whoami_webpage = "http://"; } ?>
EOT;
                $output .=    "\n\t\t\t<label for='whoami_webpage'";
                if($options['webpage-required']) {
                    $output .= ' class="required" ';
                }
                $output .= ">".__("Webpage:","tdomf")."\n\t\t\t\t<br/>\n\t\t\t\t<input type=\"text\" value=\"";
                $output .= '<?php echo htmlentities($whoami_webpage,ENT_QUOTES,get_bloginfo(\'charset\')); ?>';
                $output .= '" name="whoami_webpage" id="whoami_webpage" />';
                if($options['webpage-required']) {
                    $output .= __(" (Required)","tdomf");
                }
                $output .= "\n\t\t\t</label>";
                $output .= "\n\t\t\t<br/>\n\t\t\t<br/>\n";
            }
  
            $output .= "\t\t<?php } ?>";
            
            return $output;          
      }
  }
    
  // Create and start the widget
  //
  global $tdomf_widget_whoami;
  $tdomf_widget_whoami = new TDOMF_WidgetWhoami();
  
?>
