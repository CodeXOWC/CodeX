<?php
/*
Name: "Custom Fields"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: Add a custom field to your form!
Version: 0.7
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

  /** 
   * Custom Fields Widget. This widget allows users to modify the custom fields
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 1.0
   * @since 0.13.5
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetCustomFields extends TDOMF_Widget
  {
      /**
       * List of option names for default values for each field type, for 
       * when editing a custom field
       * @access private
       */      
      var $fieldObjectsDefault = array( 'textfield' => 'default-text',
                                        'hidden'    => 'default-value',
                                        'textarea'  => 'default-text',
                                        'checkbox'  => 'default-value',
                                        'select'    => 'default-selected' );
                                        // new types should be listed here
      
      // @todo controlshow and controlhide for edit                                        
                                        
      /**
       * Show options for each fields control func
       * @access private
       */      
      var $fieldObjectsControlShowNew = array( 'textfield' => false,
                                               'hidden'    => false,
                                               'textarea'  => false,
                                               'checkbox'  => false,
                                               'select'    => false );
                                               // new types should be listed here
                                            
      /**
       * Hide options for each fields control func
       * @access private
       */      
      var $fieldObjectsControlHideNew = array( 'textfield' => array('title'),
                                               'hidden'    => false,
                                               'textarea'  => array('title'),
                                               'checkbox'  => array('text'),
                                               'select'    => array('title') );
                                               // new types should be listed here
                                            
      /**
       * Show options for each fields control func for edit mode
       * @access private
       */      
      var $fieldObjectsControlShowEdit = array( 'textfield' => false,
                                                'hidden'    => false,
                                                'textarea'  => false,
                                                'checkbox'  => false,
                                                'select'    => false );
                                                // new types should be listed here
                                            
      /**
       * Hide options for each fields control func for edit mode
       * @access private
       */      
      var $fieldObjectsControlHideEdit = array( 'textfield' => array('title'/*, 'default-text'*/),
                                                'hidden'    => false /*array('default-value')*/,
                                                'textarea'  => array('title'/*, 'default-text'*/),
                                                'checkbox'  => array('text', 'required'/*, 'default-value'*/),
                                                'select'    => array('title'/*, 'default-selected'*/) );
                                                // new types should be listed here                                               
                                               
      /**
       * List of prefixes used in fields per type
       * @access private
       */
      var $fieldObjectsPrefix = array( 'textfield' => 'customfields-tf-%d-',
                                       'hidden'    => 'customfields-h-%d-',
                                       'textarea'  => 'customfields-ta-%d-',
                                       'checkbox'  => 'customfields-cb-%d-',
                                       'select'    => 'customfields-s-%d-' );
                                       // new types should be listed here 
       
      /**
       * Field classes mapped by 'type'
       * @see initFieldObjects()
       * @access private
       */      
      var $fieldObjects;
      
      /**
       * Localised names of objects mapped by 'type'
       * @see initFieldObjects()
       * @access private
       */
      var $fieldObjectsName;                                
                                
     /** 
       * Initilise the field objects that can be done statically
       * @access private
       */                                
      function initFieldObjects()
      {
          // intilise fields
          
          $this->fieldObjects = array( 'textfield' => new TDOMF_WidgetFieldTextField(''),
                                       'hidden'    => new TDOMF_WidgetFieldHidden(''),
                                       'textarea'  => new TDOMF_WidgetFieldTextArea(''),
                                       'checkbox'  => new TDOMF_WidgetFieldCheckBox(''),
                                       'select'    => new TDOMF_WidgetFieldSelect('') );
                                       // new types should be listed here
          
          $this->fieldObjectsName = array(  'textfield' => __('Text Field','tdomf'),
                                            'hidden'    => __('Hidden Field','tdomf'),
                                            'textarea'  => __('Text Area','tdomf'),
                                            'checkbox'  => __('Check Box','tdomf'),
                                            'select'    => __('List (Select Field)','tdomf'));
                                            // new types should be listed here                                  
      }
      
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetCustomFields() {
          $this->enableHack();
          $this->enablePreview();
          $this->enableValidate();
          $this->enableValidatePreview();
          $this->enablePost();
          $this->enableAdminEmail();
          $this->enableAdminError();
          $this->setDisplayName(__('Custom Field','tdomf'));
          $this->enableMultipleInstances(true,__('Custom Fields %d','tdomf'),'tdomf_customfields_widget_count',false);
          $this->enableControl(true,500, 1000);
          $this->setInternalName('customfields','-');
          $this->setOptionKey('tdomf_customfields_widget','_');
          $this->setModes(array('new', 'edit'));
          #$this->setCustomFields(???)); <- this is done later, when we know the key (@see getOptions)
          $this->initFieldObjects();
          $this->start();
      }

      /** 
       * Format the custom field as per the options set by user
       *
       * @return String
       */ 
      function format($value,$options){
          // boolean is a special case: false turns to '' using strval
          if(is_bool($value)) {
              $value = ($value) ? __('true','tdomf') : __('false','tdomf') ;
          } else {
              $value = strval($value);
          }
          if($value != '0' && (empty($value) || trim($value) == "")) {
              return "";
          }
          $title = $options['title'];
          $key = $options['key'];
          $output = $options['format'];
          
          $patterns = array ( '/%%TITLE%%/',
                            '/%%VALUE%%/',
                            '/%%KEY%%/');
          $replacements = array( $title,
                               tdomf_protect_input($value),
                               $key );
          
          $output = preg_replace($patterns,$replacements,$output);
          
          return $output;
      }
      
      /** 
       * Append Custom Field value to post content as text
       */
      function append($post_ID,$options,$index,$form_id){
          // Grab value
          $value = get_post_meta($post_ID,$options['key'],true);
          
          // select of course has to be a special case!
          if($options['type'] == 'select') {
              $value = $this->selectConvert($value,$options,$index);
          } else if(is_bool($value) || $options['type'] == 'checkbox') {
              $value = ($value) ? __('true','tdomf') : __('false','tdomf') ;
          } else {
              $value = strval($value);
          }
          
          // we should only really care if the field is "empty" ... false is a valid setting
          if((!is_string($value)) || (trim($value) != ""))
          {
                  $fmt = $this->format($value,$options);
                  $fmt = trim(tdomf_prepare_string($fmt,$form_id,"",$post_ID));
                  if($fmt != "") {
                      // Grab existing data
                      $post = wp_get_single_post($post_ID, ARRAY_A);
                      if(!empty($post['post_content'])) {
                          $post = add_magic_quotes($post);
                      }
                      $post_content = $post['post_content'];
                      $post_content .= addslashes($fmt);
                      // Update post
                      $post = array (
                          "ID"                      => $post_ID,
                          "post_content"            => $post_content,
                          );
                      $post_ID = wp_update_post($post);
                  }
                  if($options['type'] == 'checkbox' ){ tdomf_log_message('TDOMF_WidgetCustomFields::append:checkbox:format='.$fmt); }
          } 
      }
      
      /** 
       * Convert the input from a select field into a text value
       *
       * @return String
       */      
      function selectConvert($post_input,$options,$number) {
        $values = $options['customfields-s-'.$number.'-values'];
        $message = '';
        if(is_array($post_input)) {
          $first = true;
          foreach($values as $v => $t) {
            if(in_array($v,$post_input)) {
              if($first) { $first = false; }
              else { $message .= ', '; }
              $message .= $t;
              }
          }
        } else {
          foreach($values as $v => $t) {
            if($v == $post_input) {
              $message = $t;
               break;
            }
          }
       }
        return $message;
      }
      
      function getOptions($form_id,$index) {
          $defaults = array('key' => "TDOMF Form #$form_id Custom Field #$index",
                          'title' => "",
                          'type' => 'textfield',
                          'append' => false,
                          'format' => "<p><b>%%TITLE%%</b>: %%VALUE%%</p>",
                          'preview' => true,
                          'append' => false,
                          'format' => "<p><b>%%TITLE%%</b>: %%VALUE%%</p>");
          $options = TDOMF_Widget::getOptions($form_id,$index); 
          $options = wp_parse_args($options, $defaults);

          // try to update the custom field settings
          $title = $options['title'];
          if(empty($title)) { $title = sprintf(__('Custom Field (\'%s\')','tdomf'),$options['key']); };
          $this->setCustomFields(array($options['key'] => $title));
          
          return $options;
      }

      /**
       * Get field prefix based on type
       * @access private
       * @return String
       */
      function getFieldPrefix($type = 'textfield', $number) {
        if(isset($this->fieldObjectsPrefix[$type])) {
          return sprintf($this->fieldObjectsPrefix[$type], $number);
        } else {
          tdomf_log_message( sprintf('TDOMF_WidgetCustomFields->getFieldPrefix(): ERROR: Invalid \'type\': %s',$options['type']), TDOMF_LOG_ERROR); 
        }
        return false;
      }
      
      /**
       * Get field 'show' list for control based on type for new mode
       * @access private
       * @return String
       */
      function getFieldShowNew($type = 'textfield', $number) {
          if(isset($this->fieldObjectsControlShowNew[$type]) && is_array($this->fieldObjectsControlShowNew[$type])) {
              $prefix = $this->getFieldPrefix($type,$number);
              $returnVal = array();
              foreach($this->fieldObjectsControlShowNew[$type] as $item) {
                  $returnVal[] = $prefix.$item;
              }
              return $returnVal;
          }
          return false;
      }
      
      /**
       * Get field 'hide' list for control based on type for new mode
       * @access private
       * @return String
       */
      function getFieldHideNew($type = 'textfield', $number) {
          if(isset($this->fieldObjectsControlHideNew[$type]) && is_array($this->fieldObjectsControlHideNew[$type])) {
              $prefix = $this->getFieldPrefix($type,$number);
              $returnVal = array();
              foreach($this->fieldObjectsControlHideNew[$type] as $item) {
                  $returnVal[] = $prefix.$item;
              }
              return $returnVal;
          }
          return false;
      }
      
      /**
       * Get field 'show' list for control based on type for edit mode
       * @access private
       * @return String
       */
      function getFieldShowEdit($type = 'textfield', $number) {
          if(isset($this->fieldObjectsControlShowEdit[$type]) && is_array($this->fieldObjectsControlShowEdit[$type])) {
              $prefix = $this->getFieldPrefix($type,$number);
              $returnVal = array();
              foreach($this->fieldObjectsControlShowEdit[$type] as $item) {
                  $returnVal[] = $prefix.$item;
              }
              return $returnVal;
          }
          return false;
      }
      
      /**
       * Get field 'hide' list for control based on type for edit mode
       * @access private
       * @return String
       */
      function getFieldHideEdit($type = 'textfield', $number) {
          if(isset($this->fieldObjectsControlHideEdit[$type]) && is_array($this->fieldObjectsControlHideEdit[$type])) {
              $prefix = $this->getFieldPrefix($type,$number);
              $returnVal = array();
              foreach($this->fieldObjectsControlHideEdit[$type] as $item) {
                  $returnVal[] = $prefix.$item;
              }
              return $returnVal;
          }
          return false;
      }
      
      /**
       * List of original prefixes used in fields per type (before the upgrade
       * to the widget class). Should not be modified.
       */
      var $fieldObjectsPrefixOriginal = array( 'textfield' => 'customfields-textfield-%d',
                                        'hidden'    => 'customfields-hidden-%d',
                                        'textarea'  => 'customfields-textarea-%d',
                                        'checkbox'  => 'customfields-checkbox-%d',
                                        'select'    => 'customfields-s-list-%d' );
      
      /**
       * Get original field prefix based on type
       *
       * @return String
       * @see fieldPrefixOriginal
       */
      function getFieldPrefixOriginal($type = 'textfield', $number) {
        if(isset($this->fieldObjectsPrefixOriginal[$type])) {
          return sprintf($this->fieldObjectsPrefixOriginal[$type], $number);
        }
        return false;
      }
      
      /**
       * Get field class based on type and setup ready for use
       *
       * @return TDOMF_WidgetField
       * @see fields
       */      
      function getField($type = 'textfield', $number) {
        $prefix = $this->getFieldPrefix($type,$number);
        if($prefix != false) {
              if(isset($this->fieldObjects[$type])) {
                  $field = $this->fieldObjects[$type];
                  $field->updatePrefix($prefix);
                  return $field;
              }
          }
          tdomf_log_message( sprintf('TDOMF_WidgetCustomFields->getField(): ERROR: Invalid \'type\': %s',$type), TDOMF_LOG_ERROR); 
          return false;
      }
      
      
      /** 
       * For backwards compatiblity with the origianl Custom Field widget,
       * convert old options into the new ones
       * 
       * @access public
       */ 
      function updateFieldOptions($options,$type = 'textfield',$number) {
          
          $prefix = $this->getFieldPrefix($type, $number);
          $field = $this->getField($type, $number);

          if($prefix != false && $field != false) {
              
              if($type == 'textfield') {
                  
                  # append, size, title, required and defval (aka default-text) are common to all
            
                  if(isset($options['title'])) {
                      $options[$prefix.'title'] = $options['title'];
                  }
          
                  if(isset($options['required'])) {
                      $options[$prefix.'required'] = $options['required'];
                      unset($options['required']);
                  }
          
                  if(isset($options['defval'])) {
                      $options[$prefix.'default-text'] = $options['defval'];
                      unset($options['defval']);
                  }
          
                  if(isset($options['size'])) {
                      $options[$prefix.'size'] = $options['size'];
                      unset($options['size']);
                  }  
          
                  if(isset($options['tf-subtype'])) {
                      $options[$prefix.'restrict-type'] = $options['tf-subtype'];
                      unset($options['tf-subtype']);
                  }
      
              } else if( $type == 'hidden') {
              
                  # defval (aka default-value) are common to all
        
                  if(isset($options['defval'])) {
                      $options[$prefix.'default-value'] = $options['defval'];
                      unset($options['defval']);
                  }
              
              } else if( $type == 'textarea') {
              
                  # title, cols, rows, required and defval (aka default-text) are common to all
        
                  if(isset($options['title'])) {
                      $options[$prefix.'title'] = $options['title'];
                  }
                  
                  if(isset($options['required'])) {
                      $options[$prefix.'required'] = $options['required'];
                      unset($options['required']);
                  }
                  
                  if(isset($options["defval"])) {
                      $options[$prefix.'default-text'] = $options["defval"];
                      unset($options['defval']);
                  }
                  
                  if(isset($options['cols'])) {
                      $options[$prefix.'cols'] = $options['cols'];
                      unset($options['cols']);
                  }
                  
                  if(isset($options['rows'])) {
                      $options[$prefix.'rows'] = $options['rows'];
                      unset($options['rows']);
                  }
                  
                  if(isset($options['ta-quicktags'])) {
                      $options[$prefix.'quicktags'] = $options['ta-quicktags'];
                      unset($options['ta-quicktags']);
                  }
                  
                  if(isset($options['ta-restrict-tags'])) {
                      $options[$prefix.'restrict-tags'] = $options['ta-restrict-tags'];
                      unset($options['ta-restrict-tags']);
                  }
                  
                  if(isset($options['ta-allowable-tags'])) {
                      $options[$prefix.'allowable-tags'] = $options['ta-allowable-tags'];
                      unset($options['ta-allowable-tags']);
                  }
                  
                  if(isset($options['ta-char-limit'])) {
                      $options[$prefix.'char-limit'] = $options['ta-char-limit'];
                      unset($options['ta-char-limit']);
                  }
                  
                  if(isset($options['ta-word-limit'])) {
                      $options[$prefix.'word-limit'] = $options['ta-word-limit'];
                      unset($options['ta-word-limit']);
                  }
                  
                  if(isset($options['ta-content-filter'])) {
                      if($options['ta-content-filter']) {
                          $options[$prefix.'use-filter'] = 'preview';
                      } else {
                          $options[$prefix.'use-filter'] = false;
                      }
                      $options[$prefix.'filter'] = 'the_content';
                      unset($options['ta-content-filter']);
                  }
                          
              } else if($type == 'checkbox') {
                  
                  # title, required and defval are common to all
    
                  if(isset($options['title'])) {
                      $options[$prefix.'text'] = $options['title'];
                  }
  
                  if(isset($options['required'])) {
                      $options[$prefix.'required'] = $options['required'];
                      unset($options['required']);
                  }
                  
                  if(isset($options['defval'])) {
                      $options[$prefix.'default-value'] = $options['defval'];
                      unset($options['defval']);
                  }
              
              } else if($type == 'select') {
                  
                  # title, required and rows are common to all
    
                  if(isset($options['title'])) {
                      $options[$prefix.'title'] = $options['title'];
                  }
                  
                  if(isset($options['required'])) {
                      $options[$prefix.'required'] = $options['required'];
                      unset($options['required']);
                  }
                
                  if(isset($options['rows'])) {
                      $options[$prefix.'size'] = $options['rows'];
                      unset($options['rows']);
                  }
                  
                  if(isset($options['s-defaults'])) {
                      $options[$prefix.'default-selected'] = explode(";",$options['s-defaults']);
                      unset($options['s-defaults']);
                  }
                  
                  if(isset($options['s-multiple'])) {
                      $options[$prefix.'multiple-selection'] = $options['s-multiple'];
                      unset($options['s-multiple']);
                  }
                  
                  if(isset($options['s-values'])) {
                      $select_defaults = array();
                      if(!empty($options['s-values'])) {
                          $select_options = explode(";",$options['s-values']);
                          foreach($select_options as $select_option) {
                              list($text,$value) = explode(":",$select_option,2);
                              if(trim($text) != "" && trim($value) != "") {
                                  $select_defaults[$value] = $text;
                              }
                          }
                      }
                      $options[$prefix.'values'] =  $select_defaults;  
                      unset($options['s-values']);
                  }

              }
              
              # grab default widget field options
      
              $options = $field->getOptions($options);
          }
          
          return $options;
      }
      
      function form($args,$options,$postfix='') {
          $output = "";  
          
          $field = $this->getField($options['type'],$this->index);
          if($field != false) {

              extract($args);
              
              $options = $this->updateFieldOptions($options,$options['type'],$this->index);

              // If this is an edit form, update the field with the default value
              // from the post, but only if this isn't after preview
              //              
              if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id) && strpos($args['mode'],'-preview') === false) {
                  #$keys = get_post_custom_keys($args['post_ID']);
                  #if(is_array($keys) && in_array($options['key'],$keys)) {
                      $fieldPrefix = $this->getFieldPrefix($options['type'],$this->index);
                      $original_value = get_post_meta($args['post_ID'],$options['key'],true);
                      if($options['type'] == 'checkbox' || $original_value != false) {
                          $options[$fieldPrefix . $this->fieldObjectsDefault[$options['type']]] = $original_value;
                      }
                  #}
              }              
              
              $output = $field->form($args,$options);
              
          } else {
             tdomf_log_message('TDOMF_WidgetCustomFields->form(): ERROR: Cant find field', TDOMF_LOG_ERROR); 
          }
          return $output;
      }
      
      function post($args,$options,$postfix='') {

          $value = NULL;
          $field = $this->getField($options['type'],$this->index);
          
          if($field != false) {

              $fieldOriginalPrefix = $this->getFieldPrefixOriginal($options['type'],$this->index);
              extract($args);
              
              $options = $this->updateFieldOptions($options,$options['type'],$this->index);
              
              $value = $field->post($args,$options,$fieldOriginalPrefix);

              // 'false' just seems to set the Custom Field to blank, not false
              if($options['type'] == 'checkbox' && $value == false) { $value = 0; }
              
              if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {
                  #$current_value = get_post_meta($args['post_ID'],$options['key'],true);
                  /*update_post_meta($args['post_ID'],$options['key'],$value);*/
                  $this->updatePostMeta($args['post_ID'],$options['key'],$value);
              } else {
                  /*add_post_meta($args['post_ID'],$options['key'],$value,true);*/
                  $this->addPostMeta($args['post_ID'],$options['key'],$value,true);
              }
              
          } else {
             tdomf_log_message('TDOMF_WidgetCustomFields->post(): ERROR: Cant find field', TDOMF_LOG_ERROR);
             # @todo return error
          }

          if($options['append'] && $value !== NULL){
              $this->append($args['post_ID'],$options,$this->index,$args['tdomf_form_id']);
          }

          return NULL;
      }
      
      function preview($args,$options,$postfix='') {
          
          $output = "";         
          
          if($options['preview']) {
          
              $field = $this->getField($options['type'],$this->index);
              $fieldOriginalPrefix = $this->getFieldPrefixOriginal($options['type'],$this->index);
              
              if($field != false) {
    
                  extract($args);
                  
                  $options = $this->updateFieldOptions($options,$options['type'],$this->index);
                  
                  
                  if($options['append'] && trim($options['format']) != "") {
                      $value = $field->post($args,$options,$fieldOriginalPrefix);
                      // select of course has to be a special case!
                      if($options['type'] == 'select') {
                          $value = $this->selectConvert($value,$options,$this->index);
                      }
                      $fmt = $this->format($value,$options);                      
                      $output .= trim(tdomf_prepare_string($fmt,$tdomf_form_id,$mode));
                  } else {
                      $output .= $field->preview($args,$options);
                  }
                  
              } else {
                 tdomf_log_message('TDOMF_WidgetCustomFields->preview(): ERROR: Cant find field', TDOMF_LOG_ERROR); 
              }
          
          }
          
          return $output;
      }
      
      function validate($args,$options,$preview,$postfix='') {
          
          $output = NULL;         
          $field = $this->getField($options['type'],$this->index);
          $fieldOriginalPrefix = $this->getFieldPrefixOriginal($options['type'],$this->index);
          
          extract($args);
          
          if($field != false) {
              
              $options = $this->updateFieldOptions($options,$options['type'],$this->index);

              if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {
                  // set the default value to the original value of the edit field
                  $fieldPrefix = $this->getFieldPrefix($options['type'],$this->index);
                  $original_value = get_post_meta($tdomf_post_id,$options['key'],true);
                  $options[$fieldPrefix . $this->fieldObjectsDefault[$options['type']]] = $original_value; 
              }        
                            
              $output = $field->validate($args,$options,$fieldOriginalPrefix);
              
          } else {
             tdomf_log_message('TDOMF_WidgetCustomFields->validate(): ERROR: Cant find field', TDOMF_LOG_ERROR); 
          }
          
          if($output != "") {
              return $before_widget.$output.$after_widget;
          } else {
              return NULL;
          }
      }
      
      function adminEmail($args,$options,$post_ID,$postfix='') {
                    
          extract($args);
          
          $output  = $before_widget;
          $output .= $before_title.__("Custom Field: ","tdomf");
          if($options['title'] != "") {
              $output .= '"'.$options['title'].'" ';
          }
          $output .= '['.$options['key'].']';
          $output .= $after_title;

          // format output for email
          
          if( $options['type'] == 'checkbox') {
              if(get_post_meta($post_ID,$options['key'],true)) {
                  $output .= __("Checked","tdomf");
              } else {
                  $output .= __("Not checked","tdomf");
              }
          } else if( $options['type'] == 'select') {
              $value = get_post_meta($post_ID,$options['key'],true);
              $output .= $this->selectConvert($value,$options,$this->index);
          } else {
              $output .= get_post_meta($post_ID,$options['key'],true);
          }

          $output .= $after_widget;
          
          return $output;
      }
      
      function control($options,$form_id,$postfixOptionKey='',$postfixInternalName='') {
          
          // FYI: postfixOptionKey uses '_' and postfixInternalName uses '-'
          
          // Store settings for this widget
          //
          if ( $_POST[$this->internalName.$postfixInternalName.'-submit'] ) {
                
                 $newoptions['title'] = $_POST["customfields-title$postfixInternalName"];
                 $newoptions['key'] = $_POST["customfields-key$postfixInternalName"];;
                 $newoptions['preview'] = isset($_POST["customfields-preview$postfixInternalName"]);
                 $newoptions['type'] = $_POST["customfields-type$postfixInternalName"];
                 $newoptions['append'] = isset($_POST["customfields-append$postfixInternalName"]);
                 $newoptions['format'] = $_POST["customfields-format$postfixInternalName"];
                 
                 $options = wp_parse_args($newoptions, $options);
                 $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);
          ?>
<div>          
          <?php $this->controlCommon($options,$postfixOptionKey); ?>

<label for="customfields-title<?php echo $postfixInternalName; ?>">
<?php _e("Title:","tdomf"); ?><br/>
<input type="text" size="40" id="customfields-title<?php echo $postfixInternalName; ?>" name="customfields-title<?php echo $postfixInternalName; ?>" value="<?php echo htmlentities($options['title'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
</label>

<br/><br/>

<label for="customfields-name<?php echo $postfixInternalName; ?>">
<?php _e("Custom Field Key:","tdomf"); ?><br/>
<small>
<?php _e("You must specify a unique value for the Custom Field key.","tdomf"); ?>
</small><br/>
<input type="text" size="40" id="customfields-key<?php echo $postfixInternalName; ?>" name="customfields-key<?php echo $postfixInternalName; ?>" value="<?php echo htmlentities($options['key'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
</label>

<br/><br/>

<label for="customfields-preview<?php echo $postfixInternalName; ?>">
<input type="checkbox" name="customfields-preview<?php echo $postfixInternalName; ?>" id="customfields-preview<?php echo $postfixInternalName; ?>" <?php if($options['preview']) { ?> checked <?php } ?> />
<?php _e("Include in Preview","tdomf"); ?>
</label>

<br/><br/>

<label for="customfields-append<?php echo $postfixInternalName; ?>">
<input type="checkbox" name="customfields-append<?php echo $postfixInternalName; ?>" id="customfields-append<?php echo $postfixInternalName; ?>" <?php if($options['append']){ ?> checked <?php } ?> />
<?php _e("Append Custom Field to Post Content","tdomf"); ?>
</label>

<br/><br/>

<label for="customfields-format<?php echo $postfixInternalName; ?>">
<?php _e("Format to use:","tdomf"); ?><br/>
<small>
<?php _e("If you enable the append option, this format will be used for preview as well. It supports the Form Hacker macros and you can use PHP code. Additional macros are listed below:","tdomf"); ?>
<br/>
%%VALUE%% <?php _e("= Value of Custom Field","tdomf"); ?></br>
%%KEY%% <?php _e("= Custom Field Key","tdomf"); ?><br/> 
%%TITLE%% <?php _e("= Title","tdomf"); ?>
</small><br/>
<textarea cols="40" rows="3" id="customfields-format<?php echo $postfixInternalName; ?>" name="customfields-format<?php echo $postfixInternalName; ?>"><?php echo $options['format']; ?></textarea>
</label>

<br/><br/> 

<script type="text/javascript">
  //<![CDATA[
  function customfields_change_specific<?php echo $postfixOptionKey; ?>(){
      
    var type = document.getElementById("customfields-type<?php echo $postfixInternalName; ?>").value;
    
    <?php $first = true; foreach ($this->fieldObjects as $key => $obj) { ?>
        <?php if($first) { ?>
            if(type == '<?php echo $key; ?>') {
        <?php } else { ?>
            else if(type == '<?php echo $key; ?>') {
        <?php } ?>
            <?php foreach ($this->fieldObjects as $key2 => $obj2) { ?>
                <?php if($key2 == $key) { ?>
                    document.getElementById("customfields-specific-<?php echo $key; ?><?php echo $postfixInternalName; ?>").style.display = 'inline';
                <?php } else { ?>
                    document.getElementById("customfields-specific-<?php echo $key2; ?><?php echo $postfixInternalName; ?>").style.display = 'none';
                <?php } ?>
            <?php } ?>
        }
    <?php } ?>
  }
  //]]>
</script>

<label for="customfields-type<?php echo $postfixInternalName; ?>"><?php _e("Type: ","tdomf"); ?></label>
<select name="customfields-type<?php echo $postfixInternalName; ?>" id="customfields-type<?php echo $postfixInternalName; ?>" onChange="customfields_change_specific<?php echo $postfixOptionKey; ?>();">
<?php foreach ($this->fieldObjects as $key => $obj) { ?>
    <option value="<?php echo $key; ?>" <?php if($options['type'] == $key) { ?> selected <?php } ?> /><?php echo $this->fieldObjectsName[$key]; ?>
<?php } ?>
</select>

<br/><br/>

<?php foreach ($this->fieldObjects as $key => $obj) { ?>
    <div id="customfields-specific-<?php echo $key; ?><?php echo $postfixInternalName; ?>" <?php if($options['type'] == $key) { ?> style="display:inline;" <?php } else { ?> style="display:none;" <?php } ?>>
    <?php $field = $this->getField($key,$this->index);
          $options = $this->updateFieldOptions($options,$key,$this->index);
          if(TDOMF_Widget::isEditForm(false,$form_id)) {
              $show = $this->getFieldShowEdit($key,$this->index);
              $hide = $this->getFieldHideEdit($key,$this->index);
          } else {
              $show = $this->getFieldShowNew($key,$this->index);
              $hide = $this->getFieldHideNew($key,$this->index);
          }
          $fopts = $field->control($options, $form_id, $show, $hide, $_POST[$this->internalName.$postfixInternalName.'-submit']);
          if( $_POST[$this->internalName.$postfixInternalName.'-submit'] ) {
              $options = wp_parse_args($fopts, $options);
              $this->updateOptions($options,$form_id,$postfixOptionKey);
          } ?>
    </div> <!-- customfields-specific-<?php echo $key; ?><?php echo $postfixInternalName; ?> -->
<?php } ?>

</div> <!-- <?php echo $this->internalName.$postfixInternalName; ?> -->
      <?php  }
      
      function formHack($args,$options,$postfix='') {
          $output = "";         
          $field = $this->getField($options['type'],$this->index);
          if($field != false) {

              extract($args);
              
              $options = $this->updateFieldOptions($options,$options['type'],$this->index);

              if(TDOMF_Widget::isEditForm($mode,$tdomf_form_id)) {
                  #$fieldPrefix = $this->getFieldPrefix($options['type'],$this->index);
                  $original_value = get_post_meta($args['post_ID'],$options['key'],true);
                  $output .= "\t\t".'<?php if(strpos($mode,\'-preview\') === false) {'."\n";
                  $output .= "\t\t\t".'$post = get_post($post_id); if($post) {'."\n";
                  $output .= "\t\t\t\t".'if(!isset($post_args[\''.$field->getId().'\'])) {'."\n";
                  $output .= "\t\t\t\t\t".'$post_args[\''.$field->getId().'\'] = get_post_meta($post_id,\''.$options['key'].'\',true); }'."\n";
                  $output .= "\t\t".'} } ?>'."\n\n";
              }

              $output .= $field->formHack($args,$options);
              
          } else {
             tdomf_log_message('TDOMF_WidgetCustomFields->formHack(): ERROR: Cant find field', TDOMF_LOG_ERROR); 
          }
          
          return $output;
      }
      
      function adminError($options,$form_id,$postfix='') {
          if(empty($options['key']))
          {
              $output .= sprintf(__('<b>Error</b>: Widget "Custom Fields #%d" contains an empty key. The key must be set to something and must be unique.','tdomf'),$postfix);
          }
          /* @todo: grabbing all the other custom field widgets */
      }   
  }
  
  // Create and start the widget
  //
  global $tdomf_widget_customfields;
  $tdomf_widget_customfields = new TDOMF_WidgetCustomFields();

?>
