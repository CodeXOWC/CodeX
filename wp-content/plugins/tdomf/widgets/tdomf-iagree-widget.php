<?php
/*
Name: "I Agree"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: This widget provides a checkbox that the user must click before a post will be accept such as the classic "I Agree" buttons.
Version: 5
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }
  
  /** 
   * I Agree. This widget provides a checkbox that the user must click before a post will be accept such as the classic "I Agree" buttons.
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 4.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetIAgree extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetIAgree() {
          $this->enableHack();
          $this->enableValidate();
          $this->enableControl(true,400,350);
          $this->setInternalName('i-agree');
          $this->setDisplayName(__('I Agree','tdomf'));
          $this->setOptionKey('tdomf_iagree_widget');
          $this->enableAdminError();
          $this->start();
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return array
       */
      function getOptions($form_id) {
          $defaults = array(   'text' => __("I agree with the <a href='#'>posting policy</a>.","tdomf"),
                               'error-text' => __("You must agree with <a href='#'>posting policy</a> policy before submission!","tdomf") );
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
         
         $output .= '<input type="checkbox" name="iagree" id="iagree" ';
         if($args['iagree']) { $output .= "checked "; }
         $output .= '/><label for="iagree" class="required" > ';
         $output .= $options['text'];
         $output .= ' </label>';
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
            $output = "";
            $output .= "\t\t".'<input type="checkbox" name="iagree" id="iagree" ';
            $output .= "<?php if(\$iagree) { echo 'checked'; } ?>";
            $output .= ' />'."\n\t\t".'<label for="iagree" class="required" > ';
            $output .= $options['text'];
            $output .= ' </label>';
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
          extract($args);
          if(!isset($iagree)) {
              return $options['error-text'];
          } else {
              return NULL;
          }
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
                 $newoptions['text'] = $_POST['i-agree-text'];
                 $newoptions['error-text'] = $_POST['i-agree-error-text'];
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);
        ?>
<div>

<?php $this->controlCommon($options); ?>

<i><?php _e("HTML is permissible in messages.","tdomf"); ?></i>

<br/><br/>

<label for="i-agree-text" ><?php _e("The message to show beside the checkbox:","tdomf"); ?><br/>
<textarea cols="40" rows="2" id="i-agree-text" name="i-agree-text" ><?php echo htmlentities($options['text'],ENT_NOQUOTES,get_bloginfo('charset')); ?></textarea>
</label>
<br/><br/>
<label for="i-agree-error-text" ><?php _e("The message to show when the user has failed to check the box:","tdomf"); ?><br/>
<textarea cols="40" rows="2" id="i-agree-error-text" name="i-agree-error-text" ><?php echo htmlentities($options['error-text'],ENT_NOQUOTES,get_bloginfo('charset')); ?></textarea>
</label>

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
         if($options['text'] == __("I agree with the <a href='#'>posting policy</a>.","tdomf")) {
             $output .= __('<b>Warning</b>: You have not modified the text in "I Agree" widget. This contains just a place holder text and should be at least updated to point to <i>your</i> submission policy.','tdomf');
         }
         return $output;
     }
  }

  // Create and start the widget
  //
  global $tdomf_widget_iagree;
  $tdomf_widget_iagree = new TDOMF_WidgetIAgree();  

?>