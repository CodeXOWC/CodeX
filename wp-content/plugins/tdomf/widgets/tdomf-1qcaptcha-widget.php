<?php
/*
Name: "1 Question Captcha"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: The user must answer a simple question before a post is submitted
Version: 6
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

  /** 
   * 1 Question Captcha. The user must answer a simple question before a post is submitted
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 6.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_Widget1QCaptcha extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_Widget1QCaptcha() {
          $this->enableHack();
          $this->enableValidate();
          $this->enableControl(true,350,220);
          $this->setDisplayName(__('1 Question Captcha','tdomf'));  
          // include a seperator because the first instances does not use an
          // index
          $this->setInternalName('1qcaptcha','-');
          $this->setOptionKey('tdomf_1qcaptcha_widget','_');
          $this->enableMultipleInstances(true,__('1 Question Captcha %d','tdomf'),'tdomf_1qcaptcha_widget_count',true);
          $this->start();
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return array
       */
      function getOptions($form_id,$postfix='') {
          $defaults = array( 'question' =>  __("What year is it?","tdomf"),
                             'answer' => __("2009","tdomf"));
          $options = TDOMF_Widget::getOptions($form_id,$postfix); 
          $options = wp_parse_args($options, $defaults);
          return $options;
      }   

      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options,$postfix='') {
           extract($args);
           $output  = "";
           $output .= "\t\t".'<label for="q1captcha'.$postfix.'" class="required" >';
           $output .= $options['question'];
           $output .= '</label>';
           $output .= "\n\t\t<br/>\n\t\t".'<input type="text" id="q1captcha'.$postfix.'" name="q1captcha'.$postfix.'" size="30" value="'.htmlentities($args["q1captcha$postfix"],ENT_QUOTES,get_bloginfo('charset')).'" />';
           return $output;
      }

      /**
       * Code for hacking form output
       * 
       * @access public
       * @return String
       */
      function formHack($args,$options,$postfix='') {
           extract($args);
           $output = "\t\t".'<label for="q1captcha'.$postfix.'" class="required" >';
           $output .= $options['question'];
           $output .= '</label>';
           $output .= '<br/><input type="text" id="q1captcha'.$postfix.'" name="q1captcha'.$postfix.'" size="30" value="';
           $output .= "<?php echo htmlentities(\$post_args['q1captcha$postfix'],ENT_QUOTES,get_bloginfo('charset')); ?>".'" />';
           return $output;
      }      
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview,$postfix='') {
          // only preview - no validation required
          if($preview) {
              return NULL;
          }
          extract($args);
          $simplecaptcha = trim(strtolower($args["q1captcha$postfix"]));
          $answer = trim(strtolower($options['answer']));
          if($simplecaptcha != $answer) {
              return sprintf(__("You must answer the captcha question. Hint: the answer is \"%s\".","tdomf"),$answer);
          }
          return NULL;
      }

      /**
       * Configuration panel for widget
       * 
       * @access public
       */      
      function control($options,$form_id,$postfixOptionKey='',$postfixInternalName='') {
          
          // Store settings for this widget
          //
          if ( $_POST[$this->internalName.$postfixInternalName.'-submit'] ) {
                 $newoptions['question'] = $_POST["q1captcha$postfixOptionKey-question"];
                 $newoptions['answer'] = $_POST["q1captcha$postfixOptionKey-answer"];
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id,$postfixOptionKey);
          }

          // Display control panel for this widget
          //
          extract($options);
        ?>
<div>

<?php $this->controlCommon($options,$postfixOptionKey); ?>

<br/>
      <label for="q1captcha<?php echo $postfixOptionKey; ?>-question" ><?php _e("The simple question:","tdomf"); ?><br/>
<input type="text" size="40" id="q1captcha<?php echo $postfixOptionKey; ?>-question" name="q1captcha<?php echo $postfixOptionKey; ?>-question" value="<?php echo htmlentities($options['question'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
</label>
<br/><br/>
<label for="q1captcha<?php echo $postfixOptionKey; ?>-answer" ><?php _e("The simple answer:","tdomf"); ?><br/>
<input type="text" size="40" id="q1captcha<?php echo $postfixOptionKey; ?>-answer" name="q1captcha<?php echo $postfixOptionKey; ?>-answer" value="<?php echo htmlentities($options['answer'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
</label>

</div>
        <?php 
      }
  }

  // Create and start the widget
  //
  global $tdomf_widget_1qcaptcha;
  $tdomf_widget_1qcaptcha = new TDOMF_Widget1QCaptcha();     

?>