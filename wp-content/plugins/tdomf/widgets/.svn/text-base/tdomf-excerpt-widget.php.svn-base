<?php
/*
Name: "Excerpt"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: This widget provides a box to edit the excerpt of a submission
Version: 2
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

  /** 
   * Excerpt Widget. This widget allows users to modify the excerpt of a post
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 1.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetExcerpt extends TDOMF_Widget
  {
    /** 
     * Utility class for text area   
     * 
     * @var TDOMF_WidgetFieldTextArea 
     * @access private
     */       
      var $textarea;
      
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetExcerpt() {
          $this->textarea = new TDOMF_WidgetFieldTextArea('excerpt-');
          $this->enableHack();
          $this->enablePreview();
          $this->enablePreviewHack();
          $this->enableValidate();
          $this->enableValidatePreview();
          $this->enablePost();
          #$this->enableAdminEmail();
          $this->enableWidgetTitle();
          $this->enableControl(true,340,620);
          $this->setInternalName('excerpt');
          $this->setDisplayName(__('Excerpt','tdomf'));
          $this->setOptionKey('tdomf_excerpt_widget');
          $this->setModes(array('new'));
          $this->setFields(array('post_excerpt' => __('Post Excerpt','tdomf')));
          $this->start();
      }
      
      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
         return $this->textarea->form($args,$options);
      }
      
      /**
       * Code for hacking form output
       * 
       * @access public
       * @return String
       */
      function formHack($args,$options) {
          return $this->textarea->formHack($args,$options);
      }
      
      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return String
       */
      function getOptions($form_id) {
          $defaults = array(   'excerpt-title' => __('Excerpt Text','tdomf'),
                               'excerpt-use-filter' => 'preview',
                               'excerpt-filter' => 'the_excerpt',
                               'excerpt-kses' => false,
                               'excerpt-default_text' => "",
                               );
          $options = TDOMF_Widget::getOptions($form_id); 
          $options = wp_parse_args($options, $defaults);
          
          # convert previous textarea options to new utility textarea options
          
          if(isset($options['text-required'])) {
              $options['excerpt-required'] = $options['text-required'];
              unset($options['text-required']);
          }
          
          if(isset($options['text-cols'])) {
              $options['excerpt-cols'] = $options['text-cols'];
              unset($options['text-cols']);
          }
          
          if(isset($options['text-rows'])) {
              $options['excerpt-rows'] = $options['text-rows'];
              unset($options['text-rows']);
          }
          
          if(isset($options['quicktags'])) {
              $options['excerpt-quicktags'] = $options['quicktags'];
              unset($options['quicktags']);
          }
          
          if(isset($options['restrict-tags'])) {
              $options['excerpt-restrict-tags'] = $options['restrict-tags'];
              unset($options['restrict-tags']);
          }
          
          if(isset($options['allowable-tags'])) {
              $options['excerpt-allowable-tags'] = $options['allowable-tags'];
              unset($options['allowable-tags']);
          }
          
          if(isset($options['char-limit'])) {
              $options['excerpt-char-limit'] = $options['char-limit'];
              unset($options['char-limit']);
          }
          
          if(isset($options['word-limit'])) {
              $options['excerpt-word-limit'] = $options['word-limit'];
              unset($options['word-limit']);
          }
          
          # now grab defaults for textarea
          
          $options = $this->textarea->getOptions($options);
          
          return $options;
      }   
      
      /**
       * Generate preview of widget
       * 
       * @access public
       * @return String
       */      
      function preview($args,$options) {
          return $this->textarea->preview($args,$options);
      }

      /**
       * Generate preview hack code of widget
       * 
       * @access public
       * @return String
       */      
      function previewHack($args,$options) {
          return $this->textarea->previewHack($args,$options);
      }
      
      /**
       * Process form input for widget
       * 
       * @access public
       * @return Mixed
       */
      function post($args,$options) {
          extract($args);
  
         // Grab existing data
         $post = wp_get_single_post($post_ID, ARRAY_A);
         if(!empty($post['post_excerpt'])) {
             $post = add_magic_quotes($post);
         }
         $post_excerpt = $post['post_excerpt'];  
          
         // get user input
         
         $post_excerpt .= $this->textarea->post($args,$options,'excerpt_excerpt');
         
          // Update actual post

          $post = array (
              "ID"                      => $post_ID,
              "post_excerpt"            => $post_excerpt,
          );
          $post_ID = wp_update_post($post);
          
          return NULL;
      }
      
      /**
       * Configuration panel for widget
       * 
       * @access public
       */      
      function control($options,$form_id) {
          
          // settings entirely dependant on text area 

          // Display control panel for this widget
          //
          extract($options);
          ?>
<div>          
          <?php $this->controlCommon($options); ?>
     
          <h4><?php _e("Excerpt of Post","tdomf"); ?></h4>
          
          <?php $tashow = array('excerpt-cols',
                                'excerpt-rows',
                                'excerpt-quicktags',
                                'excerpt-restrict-tags',
                                'excerpt-allowable-tags',
                                'excerpt-char-limit',
                                'excerpt-word-limit',
                                'excerpt-required',
                                'excerpt-title',
                                'excerpt-default-text');
          $taoptions = $this->textarea->control($options, $form_id, $tashow, false, $_POST[$this->internalName.'-submit']); 
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
          $output = $this->textarea->validate($args,$options,$preview);
          if(empty($output)) { return NULL; }
          return $output;
      }
  }
  
    
  // Create and start the widget
  //
  global $tdomf_widget_excerpt;
  $tdomf_widget_excerpt = new TDOMF_WidgetExcerpt();
?>
