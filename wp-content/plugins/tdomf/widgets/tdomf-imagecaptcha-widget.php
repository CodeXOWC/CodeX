<?php
/*
Name: "Image Captcha"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: The user must enter the text in the image otherwise the form will not be processed
Version: 4
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }
  
  /** 
   * Image Captcha. The user must enter the text in the image otherwise the form will not be processed
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 4.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetImageCaptcha extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetImageCaptcha() {
          $this->enableHack();
          $this->enableValidate();
          $this->setInternalName('imagecaptcha');
          $this->setDisplayName(__('Image Captcha','tdomf'));
          $this->setOptionKey('imagecaptcha');
          #$this->setModes(array('new','edit'));
          $this->start();
      }
      
      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
         $output = "";
         extract($args);

         $form_tag = $tdomf_form_id;
         if(TDOMF_Widget::isEditForm($mode)) {
             $form_tag = $tdomf_form_id . '_' . $tdomf_post_id;
         }
         
         $form_data = tdomf_get_form_data($tdomf_form_id);
         if(!isset($args['imagecaptcha_'.$form_tag])) {
             $form_data['freecap_attempts_'.$form_tag] = 0;
             $form_data['freecap_word_hash_'.$form_tag] = false;
             tdomf_save_form_data($tdomf_form_id,$form_data);
         }
         
        $output .= <<< EOT
		<script type="text/javascript">
		<!--
		function new_freecap_$form_tag()
		{
			// loads new freeCap image
			if(document.getElementById)
			{
				// extract image name from image source (i.e. cut off ?randomness)
				thesrc = document.getElementById("freecap_$form_tag").src;
				// add ?(random) to prevent browser/isp caching
				document.getElementById("freecap_$form_tag").src = thesrc+"?"+Math.round(Math.random()*100000);
			} else {
				alert("Sorry, cannot autoreload freeCap image\\nSubmit the form and a new freeCap will be loaded");
			}
		}
		//-->
		</script>
EOT;
          if(TDOMF_Widget::isEditForm($mode)) {
              $output .= "\n\t\t<img src='".TDOMF_WIDGET_URLPATH."freecap/freecap_tdomf.php?tdomf_form_id=".$tdomf_form_id."&tdomf_post_id=".$tdomf_post_id."'  id='freecap_".$form_tag."' alt='' />\n\t\t<br/>\n";              
          } else {
              $output .= "\n\t\t<img src='".TDOMF_WIDGET_URLPATH."freecap/freecap_tdomf.php?tdomf_form_id=".$tdomf_form_id."'  id='freecap_".$form_tag."' alt='' />\n\t\t<br/>\n";
          }
          $output .= "\t\t<small>".sprintf(__("If you can't read the word in the image, <a href=\"%s\">click here</a>","tdomf"),'#tdomf_form'.$form_tag.'" onclick="this.blur();new_freecap_'.$form_tag.'();return false;')."</small>\n\t\t<br/>\n";
          $output .= "\t\t".'<label for="imagecaptcha_'.$form_tag.'" class="required" >'."\n";
          $output .= "\t\t".__('What is the word in the image? ','tdomf')."\n\t\t<br/>\n";
          $output .= "\t\t".'<input type="text" id="imagecaptcha_'.$form_tag.'" name="imagecaptcha_'.$form_tag.'" size="30" value="'.htmlentities($args["imagecaptcha_$form_tag"],ENT_QUOTES).'" />'."\n";
          $output .= "\t\t".'</label>';
          
          return $output;
      }
      
      /**
       * Code for hacking form output
       * 
       * @access public
       * @return String
       */
      function formHack($args,$options) {
         $output = "";
         extract($args);
         
         // Cheeky! "form" will always be executed so the form_data will be
         // updated automatically
         
         $form_tag = TDOMF_MACRO_FORMID;
         if(TDOMF_Widget::isEditForm($mode)) {
             $form_tag = TDOMF_MACRO_FORMID . '_' . TDOMF_MACRO_POSTID;
         }
         
         $output .= <<< EOT
		<script type="text/javascript">
		<!--
		function new_freecap_$form_tag()
		{
			// loads new freeCap image
			if(document.getElementById)
			{
				// extract image name from image source (i.e. cut off ?randomness)
				thesrc = document.getElementById("freecap_$form_tag").src;
				// add ?(random) to prevent browser/isp caching
				document.getElementById("freecap_$form_tag").src = thesrc+"?"+Math.round(Math.random()*100000);
			} else {
				alert("Sorry, cannot autoreload freeCap image\\nSubmit the form and a new freeCap will be loaded");
			}
		}
		//-->
		</script>
EOT;
          if(TDOMF_Widget::isEditForm($mode)) {
              $output .= "\n\t\t<img src='".TDOMF_WIDGET_URLPATH."freecap/freecap_tdomf.php?tdomf_form_id=".$tdomf_form_id."&tdomf_post_id=".$tdomf_post_id."' id='freecap_".$form_tag."' alt='' />\n\t\t<br/>\n";              
          } else {
              $output .= "\n\t\t<img src='".TDOMF_WIDGET_URLPATH."freecap/freecap_tdomf.php?tdomf_form_id=".$tdomf_form_id."' id='freecap_".$form_tag."' alt='' />\n\t\t<br/>\n";
          }
          $output .= "\t\t<small>".sprintf(__("If you can't read the word in the image, <a href=\"%s\">click here</a>","tdomf"),'#tdomf_form'.$form_tag.'" onclick="this.blur();new_freecap_'.$form_tag.'();return false;')."</small>\n\t\t<br/>\n";
          $output .= "\t\t".'<label for="imagecaptcha_'.$form_tag.'" class="required" >'."\n";
          $output .= "\t\t".__('What is the word in the image? ','tdomf')."\n\t\t<br/>\n";
          $output .= "\t\t".'<input type="text" id="imagecaptcha_'.$form_tag.'" name="imagecaptcha_'.$form_tag.'" size="30" value="<?php echo htmlentities($imagecaptcha_'.$form_tag.',ENT_QUOTES); ?>" />'."\n";
          $output .= "\t\t".'</label>';
          return $output;
      }
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview) {
          if($preview) { return NULL; }
          extract($args);
          $form_data = tdomf_get_form_data($tdomf_form_id);
          $form_tag = $tdomf_form_id;
          if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {
             $form_tag = $tdomf_form_id . '_' . $tdomf_post_id;
          }
          
          // all freeCap words are lowercase.
          // font #4 looks uppercase, but trust me, it's not...
          if($form_data['hash_func_'.$form_tag](strtolower($args["imagecaptcha_".$form_tag]))==$form_data['freecap_word_hash_'.$form_tag])
          {
              // reset freeCap session vars
              // cannot stress enough how important it is to do this
              // defeats re-use of known image with spoofed session id
              $form_data['freecap_attempts_'.$form_tag] = 0;
              $form_data['freecap_word_hash_'.$form_tag] = false;
              tdomf_save_form_data($tdomf_form_id,$form_data);
          } else {
              return __("You must enter the word in the image as you see it.","tdomf");
          }
          return NULL;
      }
  }

  // Create and start the widget
  //
  global $tdomf_widget_imagecaptcha;
  $tdomf_widget_imagecaptcha = new TDOMF_WidgetImageCaptcha();  
  
?>