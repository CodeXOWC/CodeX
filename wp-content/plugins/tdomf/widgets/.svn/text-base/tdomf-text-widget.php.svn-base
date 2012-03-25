<?php
/*
Name: "Text"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: Insert some text
Version: 4
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }
  
  /** 
   * Text. Adds text to the form
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 4.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetText extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetText() {
          $this->enableWidgetTitle(true,'title');
          $this->enableHack();
          $this->enableControl(true,500,570);
          $this->setInternalName('text');
          $this->setDisplayName(__('Text','tdomf'));
          $this->setOptionKey('tdomf_text_widget'); // index is appended
          $this->enableMultipleInstances(true,__('Text %d','tdomf'),'tdomf_text_widget_count');
          $this->start();
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return array
       */
      function getOptions($form_id,$index='') {
          $defaults = array( 'text' =>  "" );
          $options = TDOMF_Widget::getOptions($form_id,$index); 
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
           extract($args);
           $output  = "";
           if(!isset($tdomf_post_id)) {
               $output .= tdomf_prepare_string($options['text'], $tdomf_form_id, $mode, false, "", $args);
           } else {
               $output .= tdomf_prepare_string($options['text'], $tdomf_form_id, $mode, $tdomf_post_id, "", $args);
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
           extract($args);
           $output .= "\t\t";
           $output .= $options['text'];
           return $output;
      }      
      
      /**
       * Configuration panel for widget
       * 
       * @access public
       */      
      function control($options,$form_id,$index) {
          
          // Store settings for this widget
          //
          if ( $_POST[$this->internalName.$index.'-submit'] ) {
                 $newoptions['text'] = $_POST['text_text_'.$index];
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id,$index);
          }

          // Display control panel for this widget
          //
          extract($options);
        ?>
<div>

<?php $this->controlCommon($options,$index); ?>

<br/>

<i><?php _e("This widget adds text to your form. HTML is permissible. You can also add PHP code here and use MACROs (see list) from the Form Hacker in the output.","tdomf"); ?></i>

<br/><br/>

<ul>
<li><?php printf(__("<code>%s</code> - User name of the currently logged in user","tdomf"),TDOMF_MACRO_USERNAME); ?>
<li><?php printf(__("<code>%s</code> - IP of the current visitor","tdomf"),TDOMF_MACRO_IP); ?>
<li><?php printf(__("<code>%s</code> - The ID of the current form (which is currently %d)","tdomf"),TDOMF_MACRO_FORMID,$form_id); ?>
<li><?php printf(__("<code>%s</code> - Name of the Form (set in options)","tdomf"),TDOMF_MACRO_FORMNAME); ?>
<li><?php printf(__("<code>%s</code> - Form Description (set in options)","tdomf"),TDOMF_MACRO_FORMDESCRIPTION); ?>
</ul>
             
<br/><br/>

<label for="text_text_<?php echo $index; ?>" ><?php _e("Text:","tdomf"); ?></label><br/>
<textarea cols="50" rows="6" id="text_text_<?php echo $index; ?>" name="text_text_<?php echo $index; ?>"><?php echo htmlentities($options['text'],ENT_NOQUOTES,get_bloginfo('charset')); ?></textarea>

</div>

<?php 
     }
  }

  // Create and start the widget
  //
  global $tdomf_widget_text;
  $tdomf_widget_text = new TDOMF_WidgetText();  

?>
