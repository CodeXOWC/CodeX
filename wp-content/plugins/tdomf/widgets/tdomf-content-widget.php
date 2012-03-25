<?php
/*
Name: "Content"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: One of the default widgets, allows submitting and editing of post content and title
Version: 1
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }
  
  /** 
   * Content Widget. This widget allows users to modify the content and title
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 2.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetContent extends TDOMF_Widget
  {
    /** 
     * Utility class for text area   
     * 
     * @var TDOMF_WidgetFieldTextArea 
     * @access private
     */       
      var $textarea;
    
      /** 
     * Utility class for text area   
     * 
     * @var TDOMF_WidgetFieldField
     * @access private
     */       
      var $textfield;
      
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetContent() {
          $this->textarea = new TDOMF_WidgetFieldTextArea('content-text-');
          $this->textfield = new TDOMF_WidgetFieldTextfield('content-title-');
          $this->enableHack();
          $this->enablePreview();
          $this->enablePreviewHack();
          $this->enableValidate();
          $this->enableValidatePreview();
          $this->enablePost();
          $this->enableAdminEmail();
          $this->enableWidgetTitle();
          $this->enableControl(true,450,750);
          $this->setInternalName('content');
          $this->setDisplayName(__('Content','tdomf'));
          $this->setOptionKey('tdomf_content_widget');
          $this->setModes(array('new','edit'));
          $this->setFields(array('post_content' => __('Post Content','tdomf'),
                                 'post_title' => __('Post Title','tdomf')));
          $this->start();
      }
      
      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
         if(!$options['title-enable'] && !$options['text-enable']) { return ""; }
         extract($args);
         $output = "";
         
         if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {
             $post = get_post($tdomf_post_id);
             if($post) {
                 $options['content-title-default-text'] = $post->post_title; 
                 $options['content-text-default-text'] = $post->post_content;
             }
         }
         
         if($options['title-enable']) {
              $output .= $this->textfield->form($args,$options);
              if($options['text-enable']) {
                  $output .= "<br/><br/>";
              }
          }
          
          if($options['text-enable']) {
            $output .= $this->textarea->form($args,$options);
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
          if(!$options['title-enable'] && !$options['text-enable']) { return ""; }
          extract($args);          
         
          $output = "";
          
          if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {
              // @todo
              $output .= "\t\t".'<?php $post = get_post($post_id); if($post) {'."\n";
              if($options['title-enable']) {
                  $output .= "\t\t\t".'if(!isset($post_args[\'content-title-tf\'])) { $post_args[\'content-title-tf\'] = $post->post_title; }'."\n";
              }
              if($options['text-enable']) {
                  $output .= "\t\t\t".'if(!isset($post_args[\'content-text-ta\'])) { $post_args[\'content-text-ta\'] = $post->post_content; }'."\n";
              }
              $output .= "\t\t".'} ?>'."\n";
          }
          
          if($options['title-enable']) {
            $output .= $this->textfield->formHack($args,$options);
            if($options['text-enable']) {
                $output .= "\n\t\t".'<br/><br/>'."\n";
            }
          }
          
          if($options['text-enable']) {
            $output .= $this->textarea->formHack($args,$options);
          }
          return $output;
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return String
       */
      function getOptions($form_id) {
          $defaults = array(   'title-enable' => true,
                               'text-enable' => true,
                                # default options for textfield
                               'content-title-title' => __('Post Title','tdomf'),
                               'content-title-default_text' => "",
                               'content-title-use-filter' => 'preview',
                               'content-title-filter' => 'the_title',
                                # defaults options for textarea
                               'content-text-title' => __('Post Text','tdomf'),
                               'content-text-use-filter' => 'preview',
                               'content-text-filter' => 'the_content',
                               'content-text-kses' => true,
                               'content-text-default_text' => "",
                               );
          $options = TDOMF_Widget::getOptions($form_id); 
          $options = wp_parse_args($options, $defaults);
          
          # convert previous textfield options to new utility textfield options
          
          if(isset($options['title-required'])) {
              $options['content-title-required'] = $options['title-required'];
              unset($options['title-required']);
          }
          
          if(isset($options['title-size'])) {
              $options['content-title-size'] = $options['title-size'];
              unset($options['title-size']);
          }
          
          # convert previous textarea options to new utility textarea options
          
          if(isset($options['text-required'])) {
              $options['content-text-required'] = $options['text-required'];
              unset($options['text-required']);
          }
          
          if(isset($options['text-cols'])) {
              $options['content-text-cols'] = $options['text-cols'];
              unset($options['text-cols']);
          }
          
          if(isset($options['text-rows'])) {
              $options['content-text-rows'] = $options['text-rows'];
              unset($options['text-rows']);
          }
          
          if(isset($options['quicktags'])) {
              $options['content-text-quicktags'] = $options['quicktags'];
              unset($options['quicktags']);
          }
          
          if(isset($options['restrict-tags'])) {
              $options['content-text-restrict-tags'] = $options['restrict-tags'];
              unset($options['restrict-tags']);
          }
          
          if(isset($options['allowable-tags'])) {
              $options['content-text-allowable-tags'] = $options['allowable-tags'];
              unset($options['allowable-tags']);
          }
          
          if(isset($options['char-limit'])) {
              $options['content-text-char-limit'] = $options['char-limit'];
              unset($options['char-limit']);
          }
          
          if(isset($options['word-limit'])) {
              $options['content-text-word-limit'] = $options['word-limit'];
              unset($options['word-limit']);
          }
          
          # now grab defaults for textarea and textfield
          
          $options = $this->textarea->getOptions($options);
          $options = $this->textfield->getOptions($options);
          
          # unconfigurable by user
          
          $options['content-text-use-filter'] = 'preview';
          $options['content-text-filter'] = 'the_content';
          $options['content-text-kses'] = true;
          $options['content-title-use-filter'] = 'preview';
          $options['content-title-filter'] = 'the_title';
          
          return $options;
      }   
      
      /**
       * Generate preview of widget
       * 
       * @access public
       * @return String
       */      
      function preview($args,$options) {
          if(!$options['title-enable'] && !$options['text-enable']) { return ""; }
          extract($args);
          $output = "";
          if($options['title-enable']) {
            $output .= $this->textfield->preview($args,$options,'content_title');
            $output .= "<br/>";
            if($options['text-enable']) {
                $output .= "<br/>";
            }
          }
          if($options['text-enable']) {
            $output .= $this->textarea->preview($args,$options,'content_content');
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
          if($options['title-enable']) {
            $output .= $this->textfield->previewHack($args,$options);
            $output .= "<br/>";
            if($options['text-enable']) {
                $output .= "<br/>";
            }
          }
          if($options['text-enable']) {
            $output .= $this->textarea->previewHack($args,$options);
          }
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
  
          // if sumbitting a new post (as opposed to editing)
          // make sure to *append* to post_content. For editing, overwrite.
          //
          if(TDOMF_Widget::isSubmitForm($mode)) {
              
              // Grab existing data
              $post = wp_get_single_post($post_ID, ARRAY_A);
              if(!empty($post['post_content'])) {
                $post = add_magic_quotes($post);
              }
              
              // Append
              $post_content = $post['post_content'];
              $post_content .= $this->textarea->post($args,$options,'content_content');
              
          } else { // $mode startswith "edit-"
              // Overwrite 
              $post_content = $this->textarea->post($args,$options,'content_content');
          }

          // Title

          if($options['title-enable']) {
            $content_title = tdomf_protect_input($this->textfield->post($args,$options,'content_title'));
          }
          
          // Update actual post

          $post = array (
              "ID"                      => $post_ID,
              "post_content"            => $post_content,
          );
          if($options['title-enable']) {
              $post["post_title"] = $content_title;
              $post["post_name"] = sanitize_title($content_title);
          }
        
          $post_ID = wp_update_post($post);
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
                 $newoptions['title-enable'] = isset($_POST['content-title-enable']);
                 $newoptions['text-enable'] = isset($_POST['content-text-enable']);
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);
          ?>
<div>          
          <?php $this->controlCommon($options); 
          
          if(TDOMF_Widget::isSubmitForm($mode,$form_id)) {
              $tashow = array('content-text-cols',
                              'content-text-rows',
                              'content-text-quicktags',
                              'content-text-restrict-tags',
                              'content-text-allowable-tags',
                              'content-text-char-limit',
                              'content-text-word-limit',
                              'content-text-required',
                              'content-text-title',
                              'content-text-default-text');
              $tfshow = array('content-title-size',
                              'content-title-required',
                              'content-title-title',
                              'content-title-default-text');
          } else {
              $tashow = array('content-text-cols',
                              'content-text-rows',
                              'content-text-quicktags',
                              'content-text-restrict-tags',
                              'content-text-allowable-tags',
                              'content-text-char-limit',
                              'content-text-word-limit',
                              'content-text-required',
                              'content-text-title');
              $tfshow = array('content-title-size',
                              'content-title-required',
                              'content-title-title');              
          } ?>

<h4><?php _e("Title of Post","tdomf"); ?></h4>
<label for="content-title-enable" style="line-height:35px;"><?php _e("Show","tdomf"); ?></label>
<input type="checkbox" name="content-title-enable" id="content-title-enable" <?php if($options['title-enable']) echo "checked"; ?> >

          <?php $tfoptions = $this->textfield->control($options, $form_id, $tfshow, false, $_POST[$this->internalName.'-submit']); 
          if( $_POST[$this->internalName.'-submit'] ) {
              $options = wp_parse_args($tfoptions, $options);
              $this->updateOptions($options,$form_id);
          } ?>   
          
<h4><?php _e("Content of Post","tdomf"); ?></h4>
<label for="content-text-enable" style="line-height:35px;"><?php _e("Show","tdomf"); ?><label>
<input type="checkbox" name="content-text-enable" id="content-text-enable" <?php if($options['text-enable']) echo "checked"; ?> >
          
          <?php $taoptions = $this->textarea->control($options, $form_id, $tashow, false, $_POST[$this->internalName.'-submit']); 
          if( $_POST[$this->internalName.'-submit'] ) {
              $options = wp_parse_args($taoptions, $options);
              $this->updateOptions($options,$form_id);
          }
          
          ?>
</div>
        <?php
      }
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview) {
          if(!$options['title-enable'] && !$options['text-enable']) { return ""; }  
          extract($args);
          $output = "";

          if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {

               // when it goes to validation, the tdomf_post_id will be the 
               // real post id

              $post = &get_post( $tdomf_post_id );
          
              // set default texts to the original post contents

              $options['content-text-default-text'] = $post->post_content;
              $options['content-title-default-text'] = $post->post_title;
          }
          
          if($options['title-enable']) {
              $tf_output = $this->textfield->validate($args,$options,$preview,'content_title');
              if(!empty($tf_output)) {
                  if($output != "") { $output .= "<br/>"; }
                  $output .= $tf_output;
              }
          }

          if($options['text-enable']) {
              $ta_output = $this->textarea->validate($args,$options,$preview,'content_content');
              if(!empty($ta_output)) {
                  if($output != "") { $output .= "<br/>"; }
                  $output .= $ta_output;
              }
          }

          // return output if any
          if($output != "") {
              return $output;
          } else {
              return NULL;
          }
      }
  }
  
    
  // Create and start the widget
  //
  global $tdomf_widget_content;
  $tdomf_widget_content = new TDOMF_WidgetContent();

?>
