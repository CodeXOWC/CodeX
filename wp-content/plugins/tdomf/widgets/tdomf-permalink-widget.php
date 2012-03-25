<?php
/*
Name: "Permalink"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: This widget allows users to modify the permalink
Version: 1
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

  // this is where we "cheat"! :)
  
  define("TDOMF_KEY_CUSTOM_PERMALINK", "_tdomf_custom_permalink");
  
  add_filter('post_link', 'tdomf_widget_permalink_correct_permalink', 10, 2);
  function tdomf_widget_permalink_correct_permalink($permalink,$post) {
      $mypermalink = get_post_meta($post->ID,TDOMF_KEY_CUSTOM_PERMALINK,true);
      if($mypermalink) { return $mypermalink; }
      return $permalink;
  }

  /** 
  * Permalink Widget. This widget allows users to modify the permalink.
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 1.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetPermalink extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetPermalink() {
          $this->enableHack();
          $this->enablePreview();
          $this->enablePreviewHack();
          $this->enableValidate();
          $this->enableValidatePreview();
          $this->enablePost();
          $this->enableAdminEmail();
          $this->enableWidgetTitle();
          $this->enableControl(true,400,500);
          $this->setInternalName('permalink');
          $this->setDisplayName(__('Permalink','tdomf'));
          $this->setOptionKey('tdomf_permalink_widget');
          $this->setModes(array('new'));
          $this->setCustomFields(array(TDOMF_KEY_CUSTOM_PERMALINK => __('Permalink','tdomf')));
          $this->start();
      }

      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
          extract($args);
          $output = "";
          
          if($options['required']) {
              $output .= '<label for="permalink" class="required" >';
          } else {
              $output .= '<label for="permalink" >';
          }
          
          if(!isset($args['permalink'])) { $permalink = $options['default']; }
    
          $output .= __("Permalink:","tdomf");
          $output .= '</label>';
          $output .= '<br/><input type="text" id="permalink" name="permalink" size="60" value="'.htmlentities($permalink,ENT_QUOTES,get_bloginfo('charset')).'" />';
          return $output;
      }
      
      /**
       * Code for hacking form output
       * 
       * @access public
       * @return String
       */
      function formHack($args,$options) {
          
          if($options['required']) {
              $output = "\t\t".'<label for="permalink" class="required" >';
          } else {
              $output = "\t\t".'<label for="permalink" >';
          }
          $output .= "\n\t\t<?php if(!isset(\$permalink)) { \$permalink = '" . $options['default'] . "'; } ?>";
          $output .= "\n\t\t".__("Permalink:","tdomf");
          $output .= '</label>';
          $output .= "\n\t\t<br/>\n\t\t".'<input type="text" id="permalink" name="permalink" size="60" value="';
          $output .= "<?php echo htmlentities(\$permalink,ENT_QUOTES,get_bloginfo('charset')); ?>".'" />';
          
          return $output;
      }

      /**
       * Process form input for widget
       * 
       * @access public
       * @return Mixed
       */
      function post($args,$options) {
          extract($args);     
          $permalink = trim($permalink);
          if($options['required'] || (!empty($permalink) && $permalink != $options['default'])) {
              add_post_meta($post_ID,TDOMF_KEY_CUSTOM_PERMALINK,$permalink);
          }
          return NULL;
      }
      
      /**
       * Generate preview of widget
       * 
       * @access public
       * @return String
       */      
      function preview($args,$options) {
          extract($args);
          if(isset($permalink) && !empty($permalink)) {
              $output .= sprintf(__("<b>Permalink</b>:<br/>%s","tdomf"), strip_tags($permalink));  
              return $output;
          }
          return "";
      }
      
      /**
       * Generate preview hack code of widget
       * 
       * @access public
       * @return String
       */      
      function previewHack($args,$options) {
          
          $output  = "\t<?php \$permalink = strip_tags(trim(\$permalink));\n"; 
          $output .= "\tif(!empty(\$permalink)) { ?>\n";
          $output .= "\t\t<b>".__("Permalink:","tdomf")."</b>\n\t\t<br/>\n\t\t<?php echo \$permalink; ?>\n";
          $output .= "\t<?php } ?>";
          
          return $output;
      }
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview) {
          extract($args);
          $output = "";
          
          $permalink = trim($permalink);
          if($options['required']) {
              if(empty($permalink) || $permalink == $options['default']) {
                  $output .= __('You must specify the permalink.','tdomf');
              }
          } 
          
          if(!empty($permalink) && $permalink != $options['default'] && !tdomf_check_url($permalink)) {
              $output .= __('The permalink you specified seems incorrect','tdomf');
          } else if(!$preview && $options['test'] && function_exists('wp_get_http')) {
              $headers = wp_get_http($permalink,false,1);
              if($headers == false) {
                  $output .= sprintf(__('The permalink doesn\'t doesnt seem to exist.','tdomf'), $headers["response"]);
              } else if($headers["response"] != '200') {
                  $output .= sprintf(__('The permalink doesn\'t doesnt seem to exist. Returned %d error code.','tdomf'), $headers["response"]);
              }
          }
          
          return $output;
      }
   
      /**
       * Append summary of widget data in email to admin
       * 
       * @access public
       * @return String
       */
      function adminEmail($args,$options,$post_ID) {
          extract($args);
          $permalink = get_permalink($post_ID);
          if(!empty($permalink)) {
              return __("Permalink: ","tdomf") . $permalink;
          }
          return "";
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
              $newoptions['required'] = isset($_POST['permalink-required']);
              $newoptions['default']  =       $_POST['permalink-default'];
              $newoptions['test']     = isset($_POST['permalink-test']);
              $options = wp_parse_args($newoptions, $options);
              $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);

          ?>
<div>

<p><?php _e('This widget allows submitters to provide their own "permalink" for the post. However it is not currently possible to modify this permanently in Wordpress. If you disable the plugin, the permalink will revert back to the original post permalink.','tdomf'); ?></p>

<br/>

          <?php $this->controlCommon($options); ?>

<input type="checkbox" name="permalink-required" id="permalink-required" <?php if($options['required']) echo "checked"; ?> >
<label for="permalink-required" style="line-height:35px;"><?php _e("The submitter must supply supply a link","tdomf"); ?></label>
<br/>

<?php if(function_exists('wp_get_http')) { ?>
<input type="checkbox" name="permalink-test" id="permalink-test" <?php if($options['test']) echo "checked"; ?> >
<label for="permalink-test" style="line-height:35px;"><?php _e("Validate link by testing if it returns a valid header code","tdomf"); ?></label>
<br/>
<?php } ?>

<label for="permalink-default" style="line-height:35px;">
<?php _e("Default value of permalink field","tdomf"); ?></label><br/>
<input type="textfield" id="permalink-default" name="permalink-default" size='40' value="<?php echo htmlentities($options['default'],ENT_QUOTES,get_bloginfo('charset')); ?>" />

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
          $defaults = array( 'required' => false,
                             'default' => 'http://',
                             'test' => false );
          $options = TDOMF_Widget::getOptions($form_id);
          $options = wp_parse_args($options, $defaults);
          return $options;
      }
  }
  
  // Create and start the widget
  //
  global $tdomf_widget_permalink;
  $tdomf_widget_permalink = new TDOMF_WidgetPermalink();

?>