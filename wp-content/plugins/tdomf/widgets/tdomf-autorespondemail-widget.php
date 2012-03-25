<?php
  /** 
   * Auto Respond Email Widget
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 2.1 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetAutoRespondEmail extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetAutoRespondEmail() {
          $this->enableHack();
          $this->enablePost();
          $this->enableWidgetTitle();
          $this->enableControl(true,600,600);
          $this->setInternalName('autorespondemail');
          $this->setDisplayName(__('Auto Respond Email','tdomf'));
          $this->setOptionKey('tdomf_autorespondemail_widget');
          $this->setModes(array('new'));
          $this->start();
          add_action('tdomf_create_post_end',array($this,'sendMailAction'),10,2);
          if(isset($_GET['tdomf_autorespondemail_post_id'])) { 
              add_action('init',array($this,'handleLinkAction'));
          }
          define("TDOMF_MACRO_AUTORESPONDEMAIL_LINK", "%%AUTORESPONDEMAIL_LINK%%");
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

          $output = '';
   
          // Check if values set in cookie
          if(!isset($autorespondemail_email) && isset($_COOKIE['tdomf_autorespond_widget_email'])) {
            $autorespondemail_email = $_COOKIE['tdomf_autorespond_widget_email'];
          }
  
          $show_email_input = $this->showEmailInput($tdomf_form_id);

          if($show_email_input) {
              $output .=  "<br/><label for='autorespondemail_email'>".__("Email:","tdomf").' <input type="text" value="'.htmlentities($autorespondemail_email,ENT_QUOTES).'" name="autorespondemail_email" id="autorespondemail_email" size="40" /></label>';
          }
          
          return $output;
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
        
        $output = '';
        
        $output .= "\t\t<?php if(TDOMF_WidgetAutoRespondEmail::showEmailInput(%%FORMID%%)) { ?>\n";
        $output .= "\t\t\t<?php if(isset(\$_COOKIE['tdomf_autorespond_widget_email'])) { \$autorespondemail_email = \$_COOKIE['tdomf_autorespond_widget_email']; } ?>\n";
        $output .= "\t\t\t\t<br/>\n\t\t\t\t<label for='autorespondemail_email'>".__("Email for notification:","tdomf").' <input type="text" value="';
        $output .= '<?php echo htmlentities($autorespondemail_email,ENT_QUOTES); ?>'.'" name="autorespondemail_email" id="autorespondemail_email" size="40" /></label>'."\n";
        $output .= "\t\t<?php } ?>";
        
        return $output;
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
         if(!isset($autorespondemail_email)) {
             if(is_user_logged_in() && tdomf_check_email_address($current_user->user_email)) {
                 $autorespondemail_email = $current_user->user_email;
             } else if(isset($whoami_email)) {
                 $autorespondemail_email = $whoami_email;
             } else if(isset($notifyme_email)) {
                 $autorespondemail_email = $notifyme_email;
             } else {
                 tdomf_log_message("Could not find a email address to store for notification!",TDOMF_LOG_ERROR);
                 return __("Could not find email address!","tdomf");
             }
         }
         setcookie("tdomf_autorespond_widget_email",$autorespondemail_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
         add_post_meta($post_ID, "_tdomf_autorespond_widget_email", $autorespondemail_email, true);    

         // mail will be sent after post is created and post is not flagged as spam
  
         return NULL;
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
              $newoptions['subject'] = $_POST['autorespondemail-subject'];
              $newoptions['body']    = $_POST['autorespondemail-body'];
              $newoptions['link']    = $_POST['autorespondemail-link'];
              $options = wp_parse_args($newoptions, $options);
              $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);

          ?>
<div>

<p><?php _e("This widget sends a plain ascii email to the submitter once the form is submitted. Form Hacker macros and PHP code are fine here. Also, if Who Am I widget or Notify Me widget is used, the email address will be taken from there. If not avaliable (or not mandatory) a new field will be added to the form. Please make sure this is one of the bottom widgets on your form","tdomf"); ?></p>

<br/>

          <?php $this->controlCommon($options); ?>

<input type="text" name="autorespondemail-subject" id="autorespondemail-subject" size="70" value="<?php echo htmlentities($subject,ENT_QUOTES,get_bloginfo('charset')); ?>" />
<textarea title="true" rows="5" cols="70" name="autorespondemail-body" id="autorespondemail-body" ><?php echo htmlentities($body,ENT_NOQUOTES,get_bloginfo('charset')); ?></textarea>

<br/><br/>

<p><?php printf(__('This is an experimental feature. You can add a link to the email body above that when the user clicks on it, it will add and a custom field to TRUE on this post. Just give the name fo the Custom Field below and add %s to the email body above.','tdomf'),TDOMF_MACRO_AUTORESPONDEMAIL_LINK); ?></p>

<label for="autorespondemail-link" style="line-height:35px;">
<?php _e("Name of Custom Field","tdomf"); ?></label><br/>
<input type="textfield" id="autorespondemail-link" name="autorespondemail-link" size='40' value="<?php echo htmlentities($options['link'],ENT_QUOTES,get_bloginfo('charset')); ?>" />

</div>
        <?php
      }

      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return String
       */
      function getOptions($form_id) {
          
          $defaults = array( 'subject' => sprintf(__("Your submission '%s' has been recieved!","tdomf"),TDOMF_MACRO_SUBMISSIONTITLE),
                             'body' => sprintf(__("Hi %s,\n\nYour submission %s has been recieved and will be online shortly\nThank you for using this service\nBest Regards\n%s","tdomf"),TDOMF_MACRO_USERNAME,TDOMF_MACRO_SUBMISSIONTITLE,"<?php echo get_bloginfo('title'); ?>"),
                             'link' => false
                           );
          $options = TDOMF_Widget::getOptions($form_id);
          $options = wp_parse_args($options, $defaults);
          return $options;
      }
      
      /**
       * Determines if we need to show email input for this form or can the 
       * email address be sourced from elsewhere
       * 
       * @access public
       * @return Boolean
       */
      function showEmailInput($form_id){
          global $current_user,$tdomf_widget_whoami;
          get_currentuserinfo();
          $show_email_input = true;
          if(is_user_logged_in() && tdomf_check_email_address($current_user->user_email)) {
            // user has already set a valid email address!
            $show_email_input = false;
          } else { 
            $widgets_in_use = tdomf_get_widget_order($form_id);
            if(in_array("who-am-i",$widgets_in_use) && isset($tdomf_widget_whoami)) {
              $whoami_options = $tdomf_widget_whoami->getOptions($form_id);
              if($whoami_options['email-enable'] && $whoami_options['email-required']) {
                // great, who-am-i widget will provide a valid email address!
                $show_email_input = false;
              }
            }
            if($show_email_input && in_array('notifyme',$widgets_in_use)) {
                // just as good! Notify me will supply an email address
                $show_email_input = false;
            }
          }
          return $show_email_input;
      }
      
      /**
       * Action to be processed when post created successfully. Will send email
       * if valid post.
       * 
       * @access public
       * @return Boolean
       */
      function sendMailAction($post_id,$form_id) {
         
        // do nothing if no email set
        //   
        $autorespondemail_email = get_post_meta($post_id, '_tdomf_autorespond_widget_email', true);
        if($autorespondemail_email == false) {
           return false;
        }
        delete_post_meta($post_id, '_tdomf_autorespond_widget_email');
          
        // if spam, do nothing
        //
        if(get_post_meta($post_id,TDOMF_KEY_SPAM,true)) {
          return false;
        }
        
        $options = $this->getOptions($form_id);
        
        if($options['link']) {
            $nonce = wp_create_nonce( 'tdomf-autorespondemail-'.$post_id );
            $url = trailingslashit(get_bloginfo('wpurl')).'?tdomf_autorespondemail_post_id='.$post_id.'&key='.$nonce;
            $patterns[]     = '/'.TDOMF_MACRO_AUTORESPONDEMAIL_LINK.'/';
            $replacements[] = $url;
        }
        
        $subject = tdomf_prepare_string($options['subject'], $form_id, "", $post_id);
        $body = tdomf_prepare_string($options['body'], $form_id, "", $post_id);
        if($options['link']) {
            $body = preg_replace($patterns,$replacements,$body);
        }
        $body = str_replace("\n","\r\n",$body);
          
        // Use custom from field
        //
        if(tdomf_get_option_form(TDOMF_OPTION_FROM_EMAIL,$form_id)) {
        
          // We can modify the "from" field by using the "header" option at the end!
          //
          $headers = "MIME-Version: 1.0\n" .
                     "From: ". tdomf_get_option_form(TDOMF_OPTION_FROM_EMAIL,$form_id) . "\n" .
                     "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
        
          $status = @wp_mail($autorespondemail_email, $subject, $body, $headers);
        } else {
          $status = @wp_mail($autorespondemail_email, $subject, $body);
        }
        
        // should we do some sort of error handling here?
        //
        tdomf_log_message("wp_mail returned $status for auto responde email on post $post_id");
        
        return true;
      }
      
      function handleLinkAction() {

          $post_id = false;
          if(isset($_GET['key']) && isset($_GET['tdomf_autorespondemail_post_id'])) {
              $key = $_GET['key'];
              $post_id = $_GET['tdomf_autorespondemail_post_id'];
              if(!wp_verify_nonce($key,'tdomf-autorespondemail-'.$post_id)) {
                  $message = __('TDOMF: nonce fail','tdomf');
                  tdomf_log_message("[autorespondemail] nonce $key invalid for Post ID $post_id",TDOMF_LOG_ERROR);
                  $post_id = false;
              }
          } else {
              $message = __('TDOMF: Bad input values','tdomf');
              tdomf_log_message("[autorespondemail] Missing 'key' and 'tdomf_autorespondemail_post_id'",TDOMF_LOG_ERROR);
          }

          if($post_id) {
              $tdomf_key = get_post_meta($post_id,TDOMF_KEY_FLAG,true);
              if(!$tdomf_key) {
                  $message = __('This is not a TDOMF post','tdomf');
                  tdomf_log_message("[autorespondemail] Tried to flag post $post_id that isn't a TDOMF post",TDOMF_LOG_ERROR);
                  $post_id = false;                  
              }
          }

          if($post_id) {
              $form_id = get_post_meta($post_id,TDOMF_KEY_FORM_ID,true);
              if(!$form_id) {
                  $message = __('No Form ID is set on this post','tdomf');
                  tdomf_log_message("[autorespondemail] Form ID is not set on this post $post_id",TDOMF_LOG_ERROR);
                  $post_id = false;                  
              }
          }
          
          if($post_id) {
              $options = $this->getOptions($form_id);
              if(!$options['link']) {
                  $message = __('This form is not configured','tdomf');
                  tdomf_log_message("[autorespondemail] Form ID $form_id is not configured for autorespondemail on post $post_id",TDOMF_LOG_ERROR);
                  $post_id = false;
              }
          }
          
          if($post_id) {
              $field = get_post_meta($post_id,$options['link'],true);
              if(!empty($field)) {
                  $message = __('You have already set this post!','tdomf');
                  tdomf_log_message("[autorespondemail] Already flagged post $post_id",TDOMF_LOG_BAD);
                  $post_id = false;                  
              }
          }
          
          if($post_id) {
              update_post_meta($post_id,$options['link'],true);
              $message = __('Thank you','tdomf');
              tdomf_log_message("[autorespondemail] Flagged post $post_id (with Form ID $form_id)",TDOMF_LOG_GOOD);
          }
          
          echo $message;
          exit();
      }
  }
  
  // Create and start the widget
  //
  global $tdomf_widget_autorespondemail;
  $tdomf_widget_autorespondemail = new TDOMF_WidgetAutoRespondEmail();

?>
