<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/** 
* Super class for widget classes. Supports validation, preview, hacking, admin
* email, admin error and multiple instances. Common features can be added to
* all widgets via this class.
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 3.0 
* @since 0.13.0
* @access public 
* @copyright Mark Cunningham
* 
*/ 
class TDOMF_Widget {
    
    /** 
     * Determines if widget can be hacked on the form   
     * 
     * @var boolean 
     * @access public 
     * @see enableHack() 
     */ 
    var $hack = false;
    
    /** 
     * Enables or disables widget hacking on the form
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableHack($hack = true) {
        if($this->started && $this->hack != $hack) {
           if($hack && !$this->multipleInstances) {
               tdomf_register_form_widget_hack($this->internalName,$this->displayName, array($this, '_form_hack'), $this->modes);
           } # remove not supported
        }
        $this->hack = $hack;
        return true;
    }
    
    /** 
     * Determines if widget has a preview   
     * 
     * @var boolean 
     * @access public 
     * @see enablePreview() 
     */
    var $preview = false;

    /** 
     * Enables or disables widget preview
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enablePreview($preview = true) {
        if($this->started && $this->preview != $preview) {
           if($preview && !$this->multipleInstances) {
               tdomf_register_form_widget_preview($this->internalName,$this->displayName, array($this, '_preview'), $this->modes);
           } # remove not supported
        }        
        $this->preview = $preview;
        return true;
    }

    /** 
     * Determines if widget preview can be hacked
     * 
     * @var boolean 
     * @access public 
     * @see enablePreviewHack() 
     */
    var $previewHack = false;

    /** 
     * Enables or disables widget preview hack
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enablePreviewHack($previewHack = true) {
        if($this->started && $this->previewHack != $previewHack) {
           if($previewHack && !$this->multipleInstances) {
               tdomf_register_form_widget_preview_hack($this->internalName,$this->displayName, array($this, '_previewHack'), $this->modes);
           } # remove not supported
        }
        $this->previewHack = $previewHack;
        return true;
    }
    
    /** 
     * Determines if widget input will be validated
     * 
     * @var boolean 
     * @access public 
     * @see enableValidate() 
     */
    var $validate = false;

    /** 
     * Enables or disables widget validation
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableValidate($validate = true) {
        if($this->started && $this->validate != $validate) {
           if($validate && !$this->multipleInstances) {
               tdomf_register_form_widget_validate($this->internalName,$this->displayName, array($this, '_validate'), $this->modes);
           } # remove not supported
        }
        $this->validate = $validate;
        return true;
    }

    /** 
     * Determines if widget input should be validated on preview
     * 
     * @var boolean 
     * @access public 
     * @see enableValidatePreview() 
     */
    var $validatePreview = true;

    /** 
     * Enables or disables widget validation
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableValidatePreview($validatePreview = true) {
        $this->validatePreview = $validatePreview;
        return true;
    }    
    
    /** 
     * Determines if widget modifies actual post
     * 
     * @var boolean 
     * @access public 
     * @see enablePost() 
     */
    var $post = false;

    /** 
     * Enables or disables widget post modification
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enablePost($post = true) {
        if($this->started && $this->post != $post) {
           if($post && !$this->multipleInstances) {
               tdomf_register_form_widget_preview_hack($this->internalName,$this->displayName, array($this, '_post'), $this->modes);
           } # remove not supported
        }
        $this->post = $post;
        return true;
    }
    
    /** 
     * Determines if widget sends admin email
     * 
     * @var boolean 
     * @access public 
     * @see enableAdminEmail() 
     */
    var $adminEmail = false;

    /** 
     * Enables or disables widget sending admin email
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableAdminEmail($adminEmail = true) {
        if($this->started && $this->adminEmail != $adminEmail) {
           if($adminEmail && !$this->multipleInstances) {
               tdomf_register_form_widget_adminemail($this->internalName,$this->displayName, array($this, '_adminEmail'), $this->modes);
           } # remove not supported
        }        
        $this->adminEmail = $adminEmail;
        return true;
    }

    /** 
     * Enables support for displaying an error message
     * 
     * @var boolean 
     * @access public 
     * @see enableAdminError() 
     */
    var $adminError = false;

    /** 
     * Enables or disables support for error message
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableAdminError($adminError = true) {
        if($this->started && $this->adminError != $adminError) {
           if($adminError) {
               tdomf_register_form_widget_admin_error($this->internalName,$this->displayName, array($this, '_adminError'), $this->modes);
           } # remove not supported
        }              
        $this->adminError = $adminError;
        return true;
    }
    
    /** 
     * Determines if widget can be configured
     * 
     * @var boolean 
     * @access public 
     * @see enableControl() 
     */    
    var $control = true;
    
    /** 
     * Width of Control Panel
     * 
     * @var integer 
     * @access public 
     * @see enableControl() 
     */        
    var $controlWidth = 100;
    
    /** 
     * Height of Control Panel
     * 
     * @var integer 
     * @access public 
     * @see enableControl() 
     */    
    var $controlHeight = 100;

    /** 
     * Enables or disables widget control panel
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableControl($control = true, $width = 100, $height = 100) {
        if($width <= 0 || $height <= 0) {
            return false;
        }
        
        if($this->started && $this->control != $control) {
           if($control && !$this->multipleInstances) {
               tdomf_register_form_widget_adminemail($this->internalName,$this->displayName, array($this, '_adminEmail'), $width, $height, $this->modes);
           } # remove not supported
        }              
        $this->control = $control;
        $this->controlWidth = $width;
        $this->controlHeight = $height;
        return true;
    }
    
    /** 
     * Multiple Instances Support
     * 
     * @var integer 
     * @access public 
     * @see enableMultipleInstances(() 
     */    
    var $multipleInstances = false;
    
    /** 
     * Key of Multiple Instances count option
     * 
     * @var integer 
     * @access public 
     * @see enableMultipleInstances(() 
     */    
    var $multipleInstancesOptionKey = false;
    
    /** 
     * Display name of multiple instances (must include a %d)
     * 
     * @var integer 
     * @access public 
     * @see enableMultipleInstances(() 
     */    
    var $multipleInstancesDisplayName = false;

    /** 
     * For backwards compatibility, does the first instance have an index?
     * 
     * @var integer 
     * @access public 
     * @see enableMultipleInstances(() 
     */   
    var $multipleInstancesNoIndexOnFirst = false;
    
    /** 
     * Sets modes widget supports. Must be done before widget is started.
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableMultipleInstances($multipleInstances = true, $displayName = false, $optionKey = false, $noIndexOnFirst = false) {    
        $this->multipleInstances = $multipleInstances;
        if($displayName) {
            $this->multipleInstancesDisplayName = $displayName;
        } else {
            $this->multipleInstancesDisplayName = $this->displayName;
        }        
        if($optionKey) {
            $this->multipleInstancesOptionKey = $optionKey;
        } else {
            $this->multipleInstancesOptionKey = 'tdomf_'.$this->internalName.'_widget';
        }
        $this->multipleInstancesNoIndexOnFirst = $noIndexOnFirst;
        return true;
    }   
    
    /** 
     * Displays the Multiple Instances Form on the Widget Page
     * 
     * @access private 
     */     
    function _multipleInstancesForm($form_id,$mode) {
        $count = tdomf_get_option_widget($this->multipleInstancesOptionKey,$form_id);
        if($count <= 0){ $count = 1; }
        $max = tdomf_get_option_form(TDOMF_OPTION_WIDGET_INSTANCES,$form_id);
        if($max == false){ $max = 9; }
        if($count > ($max+1)){ $count = ($max+1); }
  
        if($max > 1) {
  ?>
  <div class="wrap">
    <form method="post">
      <h2><?php echo $this->displayName ?></h2>
      <p style="line-height: 30px;"><?php printf(__("How many %s widgets would you like?","tdomf"),$this->displayName); ?>
      <select id="tdomf-widget-<?php echo $this->internalName; ?>-number" name="tdomf-widget-<?php echo $this->internalName; ?>-number" value="<?php echo $count; ?>">
      <?php for($i = 1; $i < ($max+1); $i++) { ?>
        <option value="<?php echo $i; ?>" <?php if($i == $count) { ?> selected="selected" <?php } ?>><?php echo $i; ?></option>
      <?php } ?>
      </select>
      <span class="submit">
        <input type="submit" value="<?php _e("Save","tdomf"); ?>" id="tdomf-widget-<?php echo $this->internalName; ?>-number-submit" name="tdomf-widget-<?php echo $this->internalName; ?>-number-submit" />
      </span>
      </p>
    </form>
  </div><?php 
        }
    }
    
    /** 
     * Handles the multiple instances input from the form on the Widget Page
     * 
     * @access private 
     */      
    function _multipleInstancesHandler($form_id,$mode) {
        if ( isset($_POST['tdomf-widget-'.$this->internalName.'-number-submit']) ) {
        $count = $_POST['tdomf-widget-'.$this->internalName.'-number'];
        if($count > 0){ tdomf_set_option_widget($this->multipleInstancesOptionKey,$count,$form_id); }
      }
    }
    
    /** 
     * Does the initilisation of multiple instance widgets
     * 
     * @access private 
     */  
    function _multipleInstancesInit($form_id,$mode) {
        $count = tdomf_get_option_widget($this->multipleInstancesOptionKey,$form_id);
        if($count <= 0){ $count = 1; } 
     
        $max = tdomf_get_option_form(TDOMF_OPTION_WIDGET_INSTANCES,$form_id);
        if($max <= 1){ $count = 1; }
        else if($count > ($max+1)){ $count = $max + 1; }
     
        $start = 1;
        if($this->multipleInstancesNoIndexOnFirst) {

           // some of the original widgets were adapted later to multiple
           // instances but had to preserve the original options. I did this
           // by not including an index on the first element... now have to 
           // support it here for backwards compatibility
            
           $start = 2;
            
           tdomf_register_form_widget($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_form'), $this->modes);
              
           if($this->hack)
               tdomf_register_form_widget_hack($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_formHack'), $this->modes);
           
           if($this->control)
               tdomf_register_form_widget_control($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_control'), $this->controlWidth, $this->controlHeight, $this->modes);
           
           if($this->preview) {
               tdomf_register_form_widget_preview($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_preview'), $this->modes);
           }
           
           if($this->previewHack) {
               tdomf_register_form_widget_preview_hack($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_previewHack'), $this->modes);
           }
           
           if($this->validate)
               tdomf_register_form_widget_validate($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_validate'), $this->modes);
           
           if($this->post)
               tdomf_register_form_widget_post($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_post'), $this->modes);
           
           if($this->adminEmail)
               tdomf_register_form_widget_adminemail($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_adminEmail'), $this->modes);
           
           if($this->adminError)
               tdomf_register_form_widget_admin_error($this->internalName,sprintf($this->multipleInstancesDisplayName,1), array($this, '_adminError'), $this->modes);
            
        }
        
        for($i = $start; $i <= $count; $i++) {          
           tdomf_register_form_widget($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_form'), $this->modes, $i);
               
           if($this->hack)
               tdomf_register_form_widget_hack($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_formHack'), $this->modes, $i);
           
           if($this->control)
               tdomf_register_form_widget_control($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_control'), $this->controlWidth, $this->controlHeight, $this->modes, $i);
           
           if($this->preview) {
               tdomf_register_form_widget_preview($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_preview'), $this->modes, $i);
           }
           
           if($this->previewHack) {
               tdomf_register_form_widget_preview_hack($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_previewHack'), $this->modes, $i);
           }
           
           if($this->validate)
               tdomf_register_form_widget_validate($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_validate'), $this->modes, $i);
           
           if($this->post)
               tdomf_register_form_widget_post($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_post'), $this->modes, $i);
           
           if($this->adminEmail)
               tdomf_register_form_widget_adminemail($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_adminEmail'), $this->modes, $i);
           
           if($this->adminError)
               tdomf_register_form_widget_admin_error($this->internalName.$this->internalNameSeperator.$i,sprintf($this->multipleInstancesDisplayName,$i), array($this, '_adminError'), $this->modes, $i);
        }
    }
    
    /** 
     * Modes the widget supports
     * 
     * @var Array 
     * @access public 
     * @see setModes() 
     */    
    var $modes = array();
    
    /** 
     * Sets modes widget supports. Must be done before widget is started
     * 
     * @return Boolean 
     * @access public 
     */ 
    function setModes($modes = array()) {
        $retVal = false;
        if(!$this->started && is_array($modes)) {
            $retVal = true;
            $this->modes = $modes;
        }
        return $retVal;
    }   
    
    /** 
     * Widget supports title
     * 
     * @var boolean 
     * @access public 
     * @see enableWidgetTitle() 
     */
    var $widgetTitle = false;

    /** 
     * For backwards compatibility, you can set the previous title key
     * 
     * @var boolean 
     * @access public 
     * @see enableWidgetTitle() 
     */
    var $widgetTitleKey = 'tdomf-title';
        
    /** 
     * Enables support for title in widget display
     * 
     * @return Boolean 
     * @access public 
     */ 
    function enableWidgetTitle($widgetTitle = true,$widgetTitleKey = 'tdomf-title') {
        $this->widgetTitle = $widgetTitle;
        $this->widgetTitleKey = $widgetTitleKey;
        return true;
    }

    /** 
     * Internal name of widget
     * 
     * @var boolean 
     * @access public 
     * @see setInternalName() 
     */    
    var $internalName = false;

    /** 
     * Seperator for internal name for use in multiple instances mode
     * 
     * @var boolean 
     * @access public 
     * @see setInternalName() 
     */
    var $internalNameSeperator = '';
    
    /** 
     * Set internal name of widget. Must be done before widget is started
     * 
     * @return Boolean 
     * @access public 
     */ 
    function setInternalName($name,$seperator = '') {
        $retVal = false;
        if(!$this->started) {
            $retVal = true;
            $this->internalName = $name;
            $this->internalNameSeperator = $seperator;
            if(!$this->optionKey) {
                $this->optionKey = 'tdomf_'.$this->internalName.'_widget';
            }
        }
        return $retVal;
    }    
    
    /** 
     * Name of widget displayed to user. 
     * 
     * @var boolean 
     * @access public 
     * @see setDisplayName() 
     */        
    var $displayName = false;
    
    /** 
     * Set display name of widget. Must be done before widget is started.
     * 
     * @return Boolean 
     * @access public 
     */ 
    function setDisplayName($name) {
        $retVal = false;
        if(!$this->started) {
            $retVal = true;
            $this->displayName = $name;
            if($this->multipleInstances && !$this->multipleInstancesDisplayName) {
                $this->multipleInstancesDisplayName = $name;
            }
        }
        return $retVal;
    }   
    
    /** 
     * Name of options stored in database. Normally genreated from internal
     * name but can be overwritten
     * 
     * @var boolean 
     * @access public 
     * @see setOptionKey() 
     */    
    var $optionKey = false;

    /** 
     * Seperator for option key for use in multiple instances mode
     * 
     * @var boolean 
     * @access public 
     * @see setInternalName() 
     */
    var $optionKeySeperator = '';    
    
    /** 
     * Set option key string.. Must be done before widget is started.
     * 
     * @return Boolean 
     * @access public 
     */ 
    function setOptionKey($key,$seperator = '') {
        $retVal = false;
        if(!$this->started) {
            $retVal = true;
            $this->optionKey = $key;
            $this->optionKeySeperator = $seperator;
        }
        return $retVal;
    }   
    
    /** 
     * List of fields that are modified by this widget 
     * 
     * @var mixed
     * @access public 
     * @see setFields() 
     */    
    var $fields = false;
    
    /** 
     * Set list of fields that are modified by this widget
     * 
     * @return Boolean 
     * @access public 
     */     
    function setFields($fields = false) {
        $retVal = false;
        if(is_array($fields) || $fields == false) {
            $this->fields = $fields;
            $retVal = true;
        }
        return $retVal;
    }
    
    /** 
     * List of custom fields that are modified by this widget 
     * 
     * @var mixed
     * @access public 
     * @see setFields() 
     */    
    var $customFields = false;
    
    /** 
     * Set list of custom fields that are modified by this widget
     * 
     * @return Boolean 
     * @access public 
     */     
    function setCustomFields($customFields = false) {
        $retVal = false;
        if(is_array($customFields) || $customFields == false) {
            $this->customFields = $customFields;
            $retVal = true;
        }
        return $retVal;
    }
    
    /** 
     * Flags if the widget has been started yet
     * 
     * @return Boolean 
     * @access private 
     */     
    var $started = false;
    
    function TDOMF_Widget() {
        /* do nothing */
    }
    
    /**
     * Start widget
     *
     * @access public
     */
    function start() {
       $retVal = false;
       if(!$this->started || !$this->internalName || !$this->displayName)
       {
           $retVal = true;
           
           if($this->multipleInstances) {
               add_action('tdomf_generate_form_start',array($this,'_multipleInstancesInit'),10,2);
               add_action('tdomf_create_post_start',array($this,'_multipleInstancesInit'),10,2);
               add_action('tdomf_control_form_start',array($this,'_multipleInstancesInit'),10,2);
               add_action('tdomf_preview_form_start',array($this,'_multipleInstancesInit'),10,2);
               add_action('tdomf_validate_form_start',array($this,'_multipleInstancesInit'), 10, 2);
               add_action('tdomf_control_form_start',array($this,'_multipleInstancesHandler'),10,2);
               add_action('tdomf_widget_page_bottom',array($this,'_multipleInstancesForm'),10,2);
               add_action('tdomf_upload_inline_form_start',array($this,'_multipleInstancesInit'),10,2);
               
               // for multiple instances, init is handled in _multipleInstancesInit function
               
           } else { 
               tdomf_register_form_widget($this->internalName, $this->displayName, array($this, '_form'), $this->modes);
               
               if($this->hack)
                   tdomf_register_form_widget_hack($this->internalName,$this->displayName, array($this, '_formHack'), $this->modes);
               
               if($this->control)
                   tdomf_register_form_widget_control($this->internalName, $this->displayName, array($this, '_control'), $this->controlWidth, $this->controlHeight, $this->modes);
               
               if($this->preview) {
                   tdomf_register_form_widget_preview($this->internalName, $this->displayName, array($this, '_preview'), $this->modes);
               }
               
               if($this->previewHack) {
                   tdomf_register_form_widget_preview_hack($this->internalName, $this->displayName, array($this, '_previewHack'), $this->modes);
               }
               
               if($this->validate)
                   tdomf_register_form_widget_validate($this->internalName, $this->displayName, array($this, '_validate'), $this->modes);
               
               if($this->post)
                   tdomf_register_form_widget_post($this->internalName, $this->displayName, array($this, '_post'), $this->modes);
               
               if($this->adminEmail)
                   tdomf_register_form_widget_adminemail($this->internalName, $this->displayName, array($this, '_adminEmail'), $this->modes);
               
               if($this->adminError)
                   tdomf_register_form_widget_admin_error($this->internalName, $this->displayName, array($this, '_adminError'), $this->modes);
           }
       }
        return $retVal;
    }
    
    /** 
     * Wraps form output of the widget
     * 
     * @return String 
     * @access private 
     */ 
    function _form($args,$params=array()) {
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($tdomf_form_id,$postfix);

        $output = $before_widget;
        if($this->widgetTitle && $options[$this->widgetTitleKey] != "") {
            $output .= $before_title.$options[$this->widgetTitleKey].$after_title;
        }
        $output .= $this->form($args,$options,$postfix);
        $output .= $after_widget;
        return $output;
    }
    
    /** 
     * Individual widgets should override this function
     * 
     * @return String 
     * @access public
     */    
    function form($args,$options,$postfix='') {
        # do nothing
        return "";
    }

    /** 
     * Wraps post of the widget
     * 
     * @return Mixed 
     * @access private 
     */     
    function _post($args,$params=array()) {
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($tdomf_form_id,$postfix);
        $this->updateFields($args);
        return $this->post($args,$options,$postfix);
    }
    
    /** 
     * Individual widgets that implement post should override this function
     * 
     * @return Mixed 
     * @access public
     */        
    function post($args,$options,$postfix='') {
        # do nothing
        return NULL;
    }   
    
    /** 
     * Wraps preview output of the widget
     * 
     * @return String
     * @access private 
     */         
    function _preview($args,$params=array()) {
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options =  $this->getOptions($tdomf_form_id,$postfix);
        
        $output = "";    
        $widget_output = $this->preview($args,$options,$postfix);
        if($widget_output && !empty($widget_output)) {
          $output  = $before_widget;
          if($this->widgetTitle && $options[$this->widgetTitleKey] != '') {
              $output .= $before_title.$options[$this->widgetTitleKey].$after_title;
          }
          $output .= $widget_output;  
          $output .= $after_widget;
        }
        return $output;
    }
    
    /** 
     * Individual widgets that implement preview should override this function
     * 
     * @return Mixed 
     * @access public
     */        
    function preview($args,$options,$postfix='') {
        # do nothing
        return false;
    }
    
    /** 
     * Wraps validation of the widget input
     * 
     * @return Mixed
     * @access private 
     */      
    function _validate($args,$preview,$params=array()) {
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($tdomf_form_id,$postfix);
        
        if(!$preview || $this->validatePreview) {
            $output = $this->validate($args,$options,$preview,$postfix);
            if($output != NULL && !empty($output)) {
                return $before_widget.$output.$after_widget;
            }
        }
        
        return NULL;
    }
    
    /** 
     * Individual widgets that implement validation should override this function
     * 
     * @return Mixed 
     * @access public
     */            
    function validate($args,$options,$preview,$postfix='') {
        # do nothing
        return NULL;
    }
    
    /** 
     * Wraps admin email of the widget input
     * 
     * @return String
     * @access private 
     */        
    function _adminEmail($args,$params=array()){
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($tdomf_form_id,$postfix);
        $output = "";    
        $widget_output = $this->adminEmail($args,$options,$post_ID,$postfix);
        if($widget_output && !empty($widget_output)) {
          $output  = $before_widget;
          if($this->widgetTitle && $options[$this->widgetTitleKey] != '') {
              $output .= $before_title.$options[$this->widgetTitleKey].$after_title;
          }
          $output .= $widget_output;  
          $output .= $after_widget;
        }
        return $output;
    }
    
    /** 
     * Individual widgets that implement admin should override this function
     * 
     * @return String
     * @access public
     */      
    function adminEmail($args,$options,$post_ID,$postfix='') {
        # do nothing
        return '';
    }
    
    /** 
     * Wraps configuration panel of widget
     * 
     * @access private 
     */       
    function _control($form_id,$params=array()) {
        
        $postfixOptionKey = $this->getPostfixFromParams($params);
        
        $postfixInternalName = '';
        if($this->multipleInstances) {
            $postfixInternalName = 0;
            if(is_array($params) && count($params) >= 1){
                $postfixInternalName = $params[0];
            }
            if($this->multipleInstancesNoIndexOnFirst && $postfixInternalName <= 1) {
                // ignore postfix for first element
                $postfixInternalName = '';
            } else {
                $postfixInternalName = $this->internalNameSeperator.$postfixInternalName;
            }
        }
        
        $options = $this->getOptions($form_id,$postfixOptionKey);
                
        if ( $_POST[$this->internalName.$postfixInternalName.'-submit'] ) {
            if($this->widgetTitle && isset($_POST[$this->internalName.$postfixOptionKey.'-tdomf-title'])) {
                $newoptions[$this->widgetTitleKey] = $_POST[$this->internalName.$postfixOptionKey.'-tdomf-title'];
            }
            if($this->hack) {
                $newoptions['tdomf-hack'] = isset($_POST[$this->internalName.$postfixOptionKey.'-tdomf-hack']);
            }
            if($this->previewHack) {
                $newoptions['tdomf-preview-hack'] = isset($_POST[$this->internalName.$postfixOptionKey.'-tdomf-preview-hack']);
            }
            if ( $options != $newoptions ) {
                $options = wp_parse_args($newoptions, $options,$postfixOptionKey);
                $this->updateOptions($options,$form_id,$postfixOptionKey);
                #$this->updateOptions($options,$form_id,$postfixOptionKey);
                #$options = $newoptions;
            }
        }
        $this->control($options,$form_id,$postfixOptionKey,$postfixInternalName);
    }
    
    /** 
     * Individual widgets that implement a control panel should override this function
     * 
     * @access public
     */     
    function control($options,$form_id,$postfixOptionKey='',$postfixInternalName='') {
        # do nothing
    }

    /** 
     * Displays common configuration options
     * 
     * @access public
     */    
    function controlCommon($options,$postfix='') {
        
        if($this->widgetTitle) { ?>
<label for="<?php echo $this->internalName.$postfix; ?>-tdomf-title" style="line-height:35px;"><?php _e("Widget Title: ","tdomf"); ?></label>
<input type="textfield" id="<?php echo $this->internalName.$postfix; ?>-title" name="<?php echo $this->internalName.$postfix; ?>-tdomf-title" value="<?php echo htmlentities($options[$this->widgetTitleKey],ENT_QUOTES,get_bloginfo('charset')); ?>" /></label>
<br/>
        <?php  }
        if($this->hack) { ?>
<input type="checkbox" name="<?php echo $this->internalName.$postfix; ?>-tdomf-hack" id="<?php echo $this->internalName.$postfix; ?>-tdomf-hack" <?php if($options['tdomf-hack']) echo "checked"; ?> >
<label for="<?php echo $this->internalName.$postfix; ?>-tdomf-hack" style="line-height:35px;"><?php _e("This widget can be modified in the form hacker","tdomf"); ?></label>
<br/>
        <?php }
       if($this->previewHack && $this->preview) { ?>
<input type="checkbox" name="<?php echo $this->internalName.$postfix; ?>-preview-hack" id="<?php echo $this->internalName.$postfix; ?>-tdomf-preview-hack" <?php if($options['tdomf-preview-hack']) echo "checked"; ?> >
<label for="<?php echo $this->internalName.$postfix; ?>-preview-hack" style="line-height:35px;"><?php _e("This widget's preview can be modified in the form hacker","tdomf"); ?></label>
<br/>
        <?php }
    }
    
    /** 
     * Wraps hacked form output of the widget
     * 
     * @return String 
     * @access private 
     */ 
    function _formHack($args,$params=array()) {
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($tdomf_form_id,$postfix);
        if($options['tdomf-hack']) {
            $output = $before_widget;
            if($this->widgetTitle && $options[$this->widgetTitleKey] != "") {
                $output .= $before_title.$options[$this->widgetTitleKey].$after_title;
            }
            $output .= $this->formHack($args,$options,$postfix);
            $output .= $after_widget;
            return $output;
        }
        return TDOMF_MACRO_WIDGET_START.$this->internalName.TDOMF_MACRO_END;
    }
    
    /** 
     * Individual widgets that implement hacked form should override this function
     * 
     * @access public
     * @return String 
     */      
     function formHack($args,$options,$postfix='') {
         return TDOMF_MACRO_WIDGET_START.$this->internalName.TDOMF_MACRO_END."\n";
     }
    
    /** 
     * Wraps hacked form preview output of the widget
     * 
     * @return String 
     * @access private 
     */ 
    function _previewHack($args,$params=array()) {
        extract($args);
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($tdomf_form_id,$postfix);
        if($options['tdomf-hack']) {
            $output = $before_widget;
            if($this->widgetTitle && $options[$this->widgetTitleKey] != "") {
                $output .= $before_title.$options[$this->widgetTitleKey].$after_title;
            }
            $output .= $this->previewHack($args,$options,$postfix);
            $output .= $after_widget;
            return $output;
        }
        return TDOMF_MACRO_WIDGET_START.$this->internalName.TDOMF_MACRO_END;        
    }
    
    /** 
     * Individual widgets that implement hacked preview should override this function
     * 
     * @access public
     * @return String 
     */      
     function previewHack($args,$options,$postfix) {
         return TDOMF_MACRO_WIDGET_START.$this->internalName.TDOMF_MACRO_END;
     }  
     
    /** 
     * Wraps error handler of the widget
     * 
     * @return String 
     * @access private 
     */ 
     function _adminError($form_id,$params=array()) {
        $postfix = $this->getPostfixFromParams($params);
        $options = $this->getOptions($form_id,$postfix);
        $output = $this->adminError($options,$form_id,$postfix);
        return $output;
     }
     
    /** 
     * Individual widgets that implement an error handler should override this function
     * 
     * @access public
     * @return Mixed 
     */       
     function adminError($options,$form_id,$postfix='') {
         return "";
     }

    /** 
     * Returns the options for this widget
     * 
     * @return Array
     * @access public
     */       
    function getOptions($form_id,$postfix='') {
        $defaults = array( $this->widgetTitleKey => $this->displayName,
                          'tdomf-hack'           => $this->hack,
                          'tdomf-preview-hack'   => $this->previewHack );
        $options = tdomf_get_option_widget($this->optionKey.$postfix,$form_id);
        # A bug in a previous version used the unmodified 'internalName' as the option key
        if($options == false) { $options = tdomf_get_option_widget('tdomf_widget_'.$this->internalName,$form_id); }
        if($options == false) { $options = array(); }
        $options = wp_parse_args($options, $defaults);
        return $options;
    }
    
    /** 
     * Updates options for this widget
     * 
     * @access public
     */
    function updateOptions($options,$form_id,$postfix='') {
        $options = tdomf_set_option_widget($this->optionKey.$postfix,$options,$form_id);
    }
    
    /** 
     * Returns if the input form or mode is a edit form or not
     * 
     * @return Boolean
     * @access public
     */    
    /*public static*/ function isEditForm($mode,$form_id=false) {
        if($form_id != false) {
            $mode = tdomf_generate_default_form_mode($form_id);
        }
        if(strpos($mode, "edit-") === 0) {
            return true;
        }
        return false;
    }

    /** 
     * Returns if the input form or mode is a submit/new form or not
     * 
     * @return Boolean
     * @access public
     */    
    /*public static*/ function isSubmitForm($mode,$form_id=false) {
        if($form_id != false) {
            $mode = tdomf_generate_default_form_mode($form_id); 
        }
        if(strpos($mode, "new-") === 0) {
            return true;
        }
        return false;
    }
    
    /** 
     * Returns the postfix from an input param
     * 
     * @return Mixed
     */ 
    function getPostfixFromParams($params = array()) {
        $this->index = 0;
        $postfix = '';
        if($this->multipleInstances) {
            $postfix = 0;
            if(is_array($params) && count($params) >= 1){
                $postfix = $params[0];
                $this->index = intval($params[0]);
            }
            if($this->multipleInstancesNoIndexOnFirst && $postfix <= 1) {
                // ignore postfix for first element
                $postfix = '';
            } else {
                $postfix = $this->optionKeySeperator.$postfix;
            }
        }
        return $postfix;
    }

    /**
     * Stores the current numeric index of multiple instances of this widget
     */ 
    var $index = 0;
    
    /** 
     * Updates fields and custom fields used by this widget
     * 
     * @return Boolean
     * @access private 
     */  
    function updateFields($args) {
        extract($args);
        if(is_array($this->fields) || is_array($this->customFields)) {
            
            if(TDOMF_Widget::isEditForm($mode)) {
                $edit = tdomf_get_edit($edit_id);
                
                if(is_array($this->fields)) {
                    if(!isset($edit->data[TDOMF_KEY_FIELDS]) || !is_array($edit->data[TDOMF_KEY_FIELDS])) {
                        $edit->data[TDOMF_KEY_FIELDS] = $this->fields;
                    } else {
                        $currentFields = array_merge($edit->data[TDOMF_KEY_FIELDS],$this->fields);
                        $edit->data[TDOMF_KEY_FIELDS] = $currentFields;
                    }
                }
                if(is_array($this->customFields)) {
                    if(!isset($edit->data[TDOMF_KEY_CUSTOM_FIELDS]) || !is_array($edit->data[TDOMF_KEY_CUSTOM_FIELDS])) {
                        $edit->data[TDOMF_KEY_CUSTOM_FIELDS] = $this->customFields;
                    } else {
                        $currentFields = array_merge($edit->data[TDOMF_KEY_CUSTOM_FIELDS],$this->customFields);
                        $edit->data[TDOMF_KEY_CUSTOM_FIELDS] = $currentFields;
                    }
                }
                // do update once
                tdomf_set_data_edit($edit->data,$edit_id);
                        
                // update the post id and not revision's list
                $id = $edit->post_id;
            } else {
                // submit form, so just update the post
                $id = $post_ID;
            }
             
            if(is_array($this->fields)) {
                    $currentFields = get_post_meta($id, TDOMF_KEY_FIELDS, true);
                    if(!is_array($currentFields)) {
                        add_post_meta($id, TDOMF_KEY_FIELDS, $this->fields, true);
                    } else {
                        $currentFields = array_merge($currentFields,$this->fields);
                        update_post_meta($id, TDOMF_KEY_FIELDS, $currentFields );
                    }
            }
            if(is_array($this->customFields)) {
                $currentFields = get_post_meta($id, TDOMF_KEY_CUSTOM_FIELDS, true);
                if(!is_array($currentFields)) {
                    add_post_meta($id, TDOMF_KEY_CUSTOM_FIELDS, $this->customFields, true);
                } else {
                    $currentFields = array_merge($currentFields,$this->customFields);
                    update_post_meta($id, TDOMF_KEY_CUSTOM_FIELDS, $currentFields );
                }
            }
        }
        return true;
    }
    
      
      /** 
       * Override the default Wordpress update_post_meta so we can add
       * custom field data to revisions
       *
       * @see update_post_meta()
       * @access private
       */         
      function updatePostMeta($post_id, $meta_key, $meta_value, $prev_value = '') {
          global $wpdb;

          // Make sure we *can* add post meta to a revision
          /*// make sure meta is added to the post, not a revision
          if ( $the_post = wp_is_post_revision($post_id) )
          $post_id = $the_post;*/

          // expected_slashed ($meta_key)
          $meta_key = stripslashes($meta_key);

          if ( !$meta_key )
              return false;

          if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) ) ) {
              return TDOMF_Widget::addPostMeta($post_id, $meta_key, $meta_value);
          }

          $meta_value = maybe_serialize( stripslashes_deep($meta_value) );

          $data  = compact( 'meta_value' );
          $where = compact( 'meta_key', 'post_id' );

          if ( !empty( $prev_value ) ) {
              $prev_value = maybe_serialize($prev_value);
              $where['meta_value'] = $prev_value;
          }

          $wpdb->update( $wpdb->postmeta, $data, $where );
          wp_cache_delete($post_id, 'post_meta');
          return true;
      }
      
      /** 
       * Override the default Wordpress add_post_meta so we can add
       * custom field data to revisions
       *
       * @see add_post_meta()
       * @see updatePostMeta()
       * @access private
       */         
      function addPostMeta($post_id, $meta_key, $meta_value, $unique = false) {
          if ( !$meta_key )
              return false;

          global $wpdb;

          // Make sure we *can* add post meta to a revision
          /*// make sure meta is added to the post, not a revision
          if ( $the_post = wp_is_post_revision($post_id) )
              $post_id = $the_post;*/

          // expected_slashed ($meta_key)
          $meta_key = stripslashes($meta_key);

          if ( $unique && $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) ) )
              return false;

          $meta_value = maybe_serialize( stripslashes_deep($meta_value) );

          $wpdb->insert( $wpdb->postmeta, compact( 'post_id', 'meta_key', 'meta_value' ) );

          wp_cache_delete($post_id, 'post_meta');

          return true;
      }
}

/** 
* Super class for widget field classes. Defines basic layout of class with some
* common utitly functions
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 1.0 
* @since 0.13.5
* @access public 
* @copyright Mark Cunningham
* 
*/
class TDOMF_WidgetField {

    var $prefix = "tdomf_field_";
    function updatePrefix($prefix)
    {
        $this->prefix = $prefix;
    }
    function getId()
    {
        return $this->prefix;
    }
    
    function TDOMF_WidgetField($prefix)
    {
        $this->updatePrefix($prefix);
    }
    
    function getOptions($opts) {
        return array();
    }
   
    function form($args,$opts)
    {
        return "";
    }
    
    function formHack($args,$options)
    {
        return "";
    }
    
    function preview($args,$opts,$original_field_name=false)
    {
        return "";
    }
    
    function previewHack($args,$opts)
    {
       return "";
    }
    
    function control($options,$form_id,$show=false,$hide=false,$save=true)
    {
        return array();
    }
    
    function validate($args,$opts,$preview=false,$original_field_name=false) 
    {
        return NULL;
    }
    
    function post($args,$opts,$original_field_name=false)
    {
        return false;
    }
    
    function postText($args,$opts,$original_field_name=false)
    {
        return strval($this->post($args,$opts,$original_field_name));
    }
    
    /** 
     * 
     * 
     * @var Boolean
     * @access private
     * @see control()
     */ 
    function useOpts($name,$show,$hide)
    {
        if((is_array($show) && in_array($name,$show)) ||
           (is_array($hide) && !in_array($name,$hide)) ||
           (!is_array($show) && !is_array($hide))) {
            return true;
        }
        return false;
    }
    
    /** 
     *
     * 
     * @var Array 
     * @access private
     * @see control()
     */ 
    function updateOptsString($options,$name,$show,$hide)
    {
        if($this->useOpts($name,$show,$hide) && isset($_POST[$name])) {
            $options[$name] = $_POST[$name];
        }
        return $options;
    }/** 
     *
     * 
     * @var Array 
     * @access private
     * @see control()
     */ 
    function updateOptsInt($options,$name,$show,$hide)
    {
        if($this->useOpts($name,$show,$hide) && isset($_POST[$name])) {
            $options[$name] = intval($_POST[$name]);
        }
        return $options;
    }
    
    
    
    /** 
     *
     * 
     * @var Array 
     * @access private
     * @see control()
     */ 
    function updateOptsBoolean($options,$name,$show,$hide)
    {
        if($this->useOpts($name,$show,$hide)) {
            $options[$name] = isset($_POST[$name]);
        }
        return $options;
    }
    
    /** 
     * Converts a string into something that can be used as a Javascript
     * variable
     * 
     * @var String 
     * @access public 
     */     
    function prepJSCode($code)
    {
        $code = esc_js($code);
        $code = str_replace('-','_',$code);
        return $code;
    }
}

/** 
* Utility class for any widget using TextField
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 1.0
* @since 0.13.5
* @access public 
* @copyright Mark Cunningham
* 
*/ 
class TDOMF_WidgetFieldTextField extends TDOMF_WidgetField {
    
    function TDOMF_WidgetFieldTextField($prefix)
    {
        parent::TDOMF_WidgetField($prefix);
    }
    
    function getId()
    {
        return $this->prefix.'tf';
    }
    
    function getOptions($opts) {
        $defs = array( $this->prefix.'size' => 30,
                       $this->prefix.'required' => false, 
                       $this->prefix.'title' => "Text",
                       $this->prefix.'restrict-type' => 'text', #email, url, number, @todo timedate
                       $this->prefix.'validate-url' => false,
                       $this->prefix.'validate-email' => false,
                       $this->prefix.'number-decimal' => true,
                       $this->prefix.'number-start' => false,
                       $this->prefix.'number-end' => false,
                       $this->prefix.'number-js' => false, 
                       $this->prefix.'restrict-tags' => false,
                       $this->prefix.'allowable-tags' => "<p><b><em><u><strong><a><img><table><tr><td><blockquote><ul><ol><li><br><sup>",
                       $this->prefix.'char-limit' => 0,
                       $this->prefix.'word-limit' => 0,                      
                       $this->prefix.'use-filter' => false, #true, 'preview', 'post'
                       $this->prefix.'filter' => 'the_title',
                       $this->prefix.'protect-magic-quotes' => true,
                       $this->prefix.'default-text' => "");        
        $opts = wp_parse_args($opts, $defs);
        return $opts;
    }
   
    function form($args,$opts)
    {
       // contents
       
        $text = $opts[$this->prefix.'default-text'];
        if(isset($args[$this->prefix.'tf'])) { 
            $text = $args[$this->prefix.'tf'];
        }
        
       // pre
       
        if(!empty($opts[$this->prefix.'title'])) {
            if($opts[$this->prefix.'required']) {
                $output = '<label for="'.$this->prefix.'tf" class="required">'.sprintf(__("%s (Required):","tdomf"),$opts[$this->prefix.'title']);
            } else {
                $output = '<label for="'.$this->prefix.'tf" >'.sprintf(__("%s:","tdomf"),$opts[$this->prefix.'title']);
            }
            $output .= "</label>\n<br/>\n";
        }
        
        if($opts[$this->prefix.'restrict-type'] == 'text') {
        
            if(!empty($opts[$this->prefix.'allowable-tags']) && $opts[$this->prefix.'restrict-tags']) {
                $output .= sprintf(__("<small>Allowable Tags: %s</small>","tdomf"),htmlentities($opts[$this->prefix.'allowable-tags']))."<br/>";
            }
            if($opts[$this->prefix.'word-limit'] > 0) {
                $output .= sprintf(__("<small>Max Word Limit: %d</small>","tdomf"),$opts[$this->prefix.'word-limit'])."<br/>";
            }
            if($opts[$this->prefix.'char-limit'] > 0) {
                $output .= sprintf(__("<small>Max Character Limit: %d</small>","tdomf"),$opts[$this->prefix.'char-limit'])."<br/>";
            }
        
        } else if($opts[$this->prefix.'restrict-type'] == 'number') {
            
            if($opts[$this->prefix.'number-js']) {
                // borrowed js from http://snippets.dzone.com/posts/show/5223
            
                $output .= '<script type="text/javascript">'."\n"; 
                $output .= '//<!-- [CDATA['."\n";
                
                $output .= 'function '.$this->prepJSCode($this->prefix).'tf_numbersonly(e) {'."\n";
                $output .= "\t".'var key; var keychar;'."\n";
                $output .= "\t".'if (window.event) {'."\n";
                $output .= "\t\t".'key = window.event.keyCode;'."\n";
                $output .= "\t".'}'."\n";
                $output .= "\t".'else if (e) {'."\n";
                $output .= "\t\t".'key = e.which;'."\n";
                $output .= "\t\t".'}'."\n";
                $output .= "\t".'else {'."\n";
                $output .= "\t\t".'return true;'."\n";
                $output .= "\t".'}'."\n";
                $output .= "\t".'keychar = String.fromCharCode(key);'."\n";
                
                $output .= "\t".'if ((key==null) || (key==0) || (key==8) ||  (key==9) || (key==13) || (key==27) ) {'."\n";
                $output .= "\t\t".'return true;'."\n";
                $output .= "\t".'}'."\n";
                $output .= "\t".'else if ((("0123456789").indexOf(keychar) > -1)) {'."\n";
                $output .= "\t\t".'return true;'."\n";
                $output .= "\t".'}'."\n";
                if($opts[$this->prefix.'number-start'] === false || $opts[$this->prefix.'number-start'] < 0) {
                    $output .= "\t".'else if (keychar == "-") { '."\n";
                    $output .= "\t\t".'return true;'."\n";
                    $output .= "\t".'}'."\n";
                }
                if($opts[$this->prefix.'number-decimal']) {
                    $output .= "\t".'else if (keychar == ".") { '."\n";
                    $output .= "\t\t".'return true;'."\n";
                    $output .= "\t".'}'."\n";
                }
                $output .= "\t".'return false;'."\n";
                $output .= '}'."\n";
                
                $output .= '//]] -->'."\n";
                $output .= '</script>'."\n"; 
            }
            
            if($opts[$this->prefix.'number-decimal']) {
                if($opts[$this->prefix.'number-start'] !== false && $opts[$this->prefix.'number-end'] !== false) {
                    $output .= sprintf(__("<small>Number: %f to %f </small>","tdomf"),$opts[$this->prefix.'number-start'],$opts[$this->prefix.'number-end'])."<br/>";
                } else if($opts[$this->prefix.'number-start'] !== false) {
                    $output .= sprintf(__("<small>Number: Greater than %f</small>","tdomf"),$opts[$this->prefix.'number-start'])."<br/>";
                } else if($opts[$this->prefix.'number-end'] !== false) {
                    $output .= sprintf(__("<small>Number: Smaller than %f</small>","tdomf"),$opts[$this->prefix.'number-end'])."<br/>";
                }
            } else {
                if($opts[$this->prefix.'number-start'] !== false && $opts[$this->prefix.'number-end'] !== false) {
                    $output .= sprintf(__("<small>Number: %d - %d </small>","tdomf"),$opts[$this->prefix.'number-start'],$opts[$this->prefix.'number-end'])."<br/>";
                } else if($opts[$this->prefix.'number-start'] !== false) {
                    $output .= sprintf(__("<small>Number: Greater than %d</small>","tdomf"),$opts[$this->prefix.'number-start'])."<br/>";
                } else if($opts[$this->prefix.'number-end'] !== false) {
                    $output .= sprintf(__("<small>Number: Smaller than %d</small>","tdomf"),$opts[$this->prefix.'number-end'])."<br/>";
                }
            }
            
        }
        
        if($opts[$this->prefix.'restrict-type'] == 'email') {
            $output .= __("Email:","tdomf")." "; 
        } else if($opts[$this->prefix.'restrict-type'] == 'url') {
            $output .= __("URL:","tdomf")." ";
        }
        
        // textfield
        
        $output .= '<input ';
        if($opts[$this->prefix.'restrict-type'] != 'number' || !$opts[$this->prefix.'number-js']) {
            $output .= 'type="text" ';
        }
        $output .= 'title="'.htmlentities($opts[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')).'" ';
        $output .= 'name="'.$this->prefix.'tf" id="'.$this->prefix.'tf" ';
        $output .= 'size="'.$opts[$this->prefix.'size'].'" ';
        $output .= 'value="'.htmlentities(strval($text),ENT_QUOTES,get_bloginfo('charset')).'" ';
        if($opts[$this->prefix.'restrict-type'] == 'number' && $opts[$this->prefix.'number-js']) {
            $output .= 'onKeyPress="return '.$this->prepJSCode($this->prefix).'tf_numbersonly(event)" ';
        }
        $output .= '/>';
        
        // post: nothing
        
        return $output;
    }
    
    function formHack($args,$opts)
    {
        $output = "";

        // contents
        
        $output .= "\t\t".'<?php if(isset($post_args["'.$this->prefix.'tf"])) {'."\n";
           $output .= "\t\t\t".'$temp_text = $post_args["'.$this->prefix.'tf"];'."\n";
        $output .= "\t\t".'} else { '."\n";
            $output .= "\t\t\t".'$temp_text = "'.htmlentities($opts[$this->prefix.'default-text'],ENT_QUOTES,get_bloginfo('charset')).'";'."\n";
        $output .= "\t\t".'} ?>'."\n";
        
        // pre
        
        if(!empty($opts[$this->prefix.'title'])) {
            if($opts[$this->prefix.'required']) {
              $output .= "\t\t".'<label for="'.$this->prefix.'tf" class="required">'.sprintf(__("%s (Required):","tdomf"),$opts[$this->prefix.'title'])."\n\t\t\t<br/>\n";      
            } else {
              $output .= "\t\t".'<label for="'.$this->prefix.'tf">'.sprintf(__("%s:","tdomf"),$opts[$this->prefix.'title'])."\n\t\t\t<br/>\n";
            }
            $output .= "\t\t</label>\n";    
        }
        
        if($opts[$this->prefix.'restrict-type'] == 'text') {
        
            if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
              $output .= "\t\t".sprintf(__("<small>Allowable Tags: %s</small>","tdomf"),htmlentities($opts[$this->prefix.'allowable-tags']))."\n\t\t<br/>\n";
            }
            if($opts[$this->prefix.'word-limit'] > 0) {
              $output .= "\t\t".sprintf(__("<small>Max Word Limit: %d</small>","tdomf"),$opts[$this->prefix.'word-limit'])."\n\t\t<br/>\n";
            }
            if($opts[$this->prefix.'char-limit'] > 0) {
              $output .= "\t\t".sprintf(__("<small>Max Character Limit: %d</small>","tdomf"),$opts[$this->prefix.'char-limit'])."\n\t\t<br/>\n";
            }
        
        } else if($opts[$this->prefix.'restrict-type'] == 'number') {
            
            if($opts[$this->prefix.'number-js']) {
            
                $output .= "\t\t".'<script type="text/javascript">'."\n"; 
                $output .= "\t\t".'//<!-- [CDATA['."\n";
                
                $output .= "\t\t".'function '.$this->prepJSCode($this->prefix).'tf_numbersonly(e) {'."\n";
                $output .= "\t\t"."\t".'var key; var keychar;'."\n";
                $output .= "\t\t"."\t".'if (window.event) {'."\n";
                $output .= "\t\t"."\t\t".'key = window.event.keyCode;'."\n";
                $output .= "\t\t"."\t".'}'."\n";
                $output .= "\t\t"."\t".'else if (e) {'."\n";
                $output .= "\t\t"."\t\t".'key = e.which;'."\n";
                $output .= "\t\t"."\t\t".'}'."\n";
                $output .= "\t\t"."\t".'else {'."\n";
                $output .= "\t\t"."\t\t".'return true;'."\n";
                $output .= "\t\t"."\t".'}'."\n";
                $output .= "\t\t"."\t".'keychar = String.fromCharCode(key);'."\n";
                
                $output .= "\t\t"."\t".'if ((key==null) || (key==0) || (key==8) ||  (key==9) || (key==13) || (key==27) ) {'."\n";
                $output .= "\t\t"."\t\t".'return true;'."\n";
                $output .= "\t\t"."\t".'}'."\n";
                $output .= "\t\t"."\t".'else if ((("0123456789").indexOf(keychar) > -1)) {'."\n";
                $output .= "\t\t"."\t\t".'return true;'."\n";
                $output .= "\t\t"."\t".'}'."\n";
                if($opts[$this->prefix.'number-start'] === false || $opts[$this->prefix.'number-start'] < 0) {
                    $output .= "\t\t"."\t".'else if (keychar == "-") { '."\n";
                    $output .= "\t\t"."\t\t".'return true;'."\n";
                    $output .= "\t\t"."\t".'}'."\n";
                }
                if($opts[$this->prefix.'number-decimal']) {
                    $output .= "\t\t"."\t".'else if (keychar == ".") { '."\n";
                    $output .= "\t\t"."\t\t".'return true;'."\n";
                    $output .= "\t\t"."\t".'}'."\n";
                }
                $output .= "\t\t"."\t".'return false;'."\n";
                $output .= "\t\t".'}'."\n";
                
                $output .= "\t\t".'//]] -->'."\n";
                $output .= "\t\t".'</script>'."\n"; 
            }
            
            if($opts[$this->prefix.'number-decimal']) {
                if($opts[$this->prefix.'number-start'] !== false && $opts[$this->prefix.'number-end'] !== false) {
                    $output .= "\t\t".sprintf(__("<small>Number: %f to %f </small>","tdomf"),$opts[$this->prefix.'number-start'],$opts[$this->prefix.'number-end'])."<br/>";
                } else if($opts[$this->prefix.'number-start'] !== false) {
                    $output .= "\t\t".sprintf(__("<small>Number: Greater than %f</small>","tdomf"),$opts[$this->prefix.'number-start'])."<br/>";
                } else if($opts[$this->prefix.'number-end'] !== false) {
                    $output .= "\t\t".sprintf(__("<small>Number: Smaller than %f</small>","tdomf"),$opts[$this->prefix.'number-end'])."<br/>";
                }
            } else {
                if($opts[$this->prefix.'number-start'] !== false && $opts[$this->prefix.'number-end'] !== false) {
                    $output .= "\t\t".sprintf(__("<small>Number: %d - %d </small>","tdomf"),$opts[$this->prefix.'number-start'],$opts[$this->prefix.'number-end'])."<br/>";
                } else if($opts[$this->prefix.'number-start'] !== false) {
                    $output .= "\t\t".sprintf(__("<small>Number: Greater than %d</small>","tdomf"),$opts[$this->prefix.'number-start'])."<br/>";
                } else if($opts[$this->prefix.'number-end'] !== false) {
                    $output .= "\t\t".sprintf(__("<small>Number: Smaller than %d</small>","tdomf"),$opts[$this->prefix.'number-end'])."<br/>";
                }
            }
        }
        
        if($opts[$this->prefix.'restrict-type'] == 'email') {
            $output .= "\t\t".__("Email:","tdomf")." "; 
        } else if($opts[$this->prefix.'restrict-type'] == 'url') {
            $output .= "\t\t".__("URL:","tdomf")." ";
        }
        
        // textfield
        
        $output .= "\t\t".'<input ';
        if($opts[$this->prefix.'restrict-type'] != 'number' || !$opts[$this->prefix.'number-js']) {
            $output .= 'type="text" ';
        }
        $output .= 'title="'.htmlentities($opts[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')).'" ';
        $output .= 'name="'.$this->prefix.'tf" id="'.$this->prefix.'tf" ';
        $output .= 'size="'.$opts[$this->prefix.'size'].'" ';
        $output .= 'value="<?php echo htmlentities($temp_text,ENT_QUOTES,get_bloginfo(\'charset\')); ?>"';
        if($opts[$this->prefix.'restrict-type'] == 'number' && $opts[$this->prefix.'number-js']) {
            $output .= 'onKeyPress="return '.$this->prepJSCode($this->prefix).'tf_numbersonly(event)" ';
        }
        $output .= '/>';
        
        // post: nothing
        
        return $output;
    }
    
    function preview($args,$opts,$original_field_name=false)
    {
        if(isset($args[$this->prefix.'tf'])) {
            $output = $args[$this->prefix.'tf'];
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $output = $args[$original_field_name];
        } else {
            tdomf_log_message("TextField: can't get any input for preview!",TDOMF_LOG_ERROR);
        }
         
        if($opts[$this->prefix.'restrict-type'] == 'text') {
        
            if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
                $output = strip_tags($output,$opts[$this->prefix.'allowable-tags']);
            }
        
        }
        
        if(($opts[$this->prefix.'use-filter'] === true || $opts[$this->prefix.'use-filter'] == 'preview') 
            && !empty($opts[$this->prefix.'filter'])) {
            $output = apply_filters($opts[$this->prefix.'filter'], $output);
        }
        
        if(!empty($opts[$this->prefix.'title'])) {
            $output = "<b>".sprintf(__("%s: ","tdomf"),$opts[$this->prefix.'title'])."</b>".$output;
        } 
        
        return $output; 
    }
    
    function previewHack($args,$opts)
    {
        $output .= "\t<?php \$temp_text = \$post_args['".$this->prefix."tf'];\n";
        if($opts[$this->prefix.'restrict-type'] == 'text' && $opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
          $output .= "\t".'$temp_text = strip_tags($temp_text,\''.$opts[$this->prefix.'allowable-tags'].'\');'."\n";
        }
        if(($opts[$this->prefix.'use-filter'] === true || $opts[$this->prefix.'use-filter'] == 'preview') 
            && !empty($opts[$this->prefix.'filter'])) {
          $output .= "\t".'$temp_text = apply_filters(\''.$opts[$this->prefix.'filter'].'\',$temp_text);'."\n";
        }
        $output .= "\t?>\n";
        if(!empty($opts[$this->prefix.'title'])) {
            $output .= "\t<b>".sprintf(__("%s: ","tdomf"),$opts[$this->prefix.'title'])."</b>\n";
        }
        $output .= "\t<?php echo \$temp_text; ?>";
        return $output; 
    }
    
    function updateOptsFloatOrBoolean($options,$name,$show,$hide)
    {
        if($this->useOpts($name,$show,$hide) && isset($_POST[$name])) {
            if(empty($_POST[$name])) {
                $options[$name] = false;
            } else {
                $options[$name] = floatval($_POST[$name]);
            }
        }
        return $options;
    }
    
    function control($options,$form_id,$show=false,$hide=false,$save=true)
    {
        if((is_array($show) && empty($show))) {
            # nothing to do if show list is empty
            return array();
        }

        // prepare options!
        
        if($save) {     
            $retOptions = array();
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'size',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'required',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'default-text',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'title',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'restrict-tags',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'allowable-tags',$show,$hide);
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'char-limit',$show,$hide);
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'word-limit',$show,$hide);
            /*$retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'use-filter',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'filter',$show,$hide);*/
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'restrict-type',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'validate-url',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'validate-email',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'number-js',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'number-decimal',$show,$hide);
            $retOptions = $this->updateOptsFloatOrBoolean($retOptions,$this->prefix.'number-start',$show,$hide);
            $retOptions = $this->updateOptsFloatOrBoolean($retOptions,$this->prefix.'number-end',$show,$hide);
            
            $options = wp_parse_args($retOptions, $options);
        }
        
        // Display control panel for this textfield
        
        if($this->useOpts($this->prefix.'required',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>required" style="line-height:35px;"><?php _e("Required","tdomf"); ?></label> 
<input type="checkbox" name="<?php echo $this->prefix; ?>required" id="<?php echo $this->prefix; ?>required" <?php if($options[$this->prefix.'required']) echo "checked"; ?> >
            <?php if(!$this->useOpts($this->prefix.'size',$show,$hide)) { ?>
                <br/>
            <?php } ?>
  <?php } 
        if($this->useOpts($this->prefix.'size',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>size" style="line-height:35px;"><?php _e("Size","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>size" id="<?php echo $this->prefix; ?>size" value="<?php echo htmlentities($options[$this->prefix.'size'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'title',$show,$hide)) { ?>
            <label for="<?php echo $this->prefix; ?>title" style="line-height:35px;"><?php _e("Title:","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>title" id="<?php echo $this->prefix; ?>title" value="<?php echo htmlentities($options[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'default-text',$show,$hide)) { ?>
            <label for="<?php echo $this->prefix; ?>default-text" style="line-height:35px;"><?php _e("Default Text:","tdomf"); ?></label>
<input type="text" title="true" size="30" name="<?php echo $this->prefix; ?>default-text" id="<?php echo $this->prefix; ?>default-text" value="<?php echo htmlentities($options[$this->prefix.'default-text'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'char-limit',$show,$hide)) { ?> 
<label for="<?php echo $this->prefix; ?>char-limit" style="line-height:35px;"><?php _e("Character Limit <i>(0 indicates no limit)</i>","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>char-limit" id="<?php echo $this->prefix; ?>char-limit" value="<?php echo htmlentities($options[$this->prefix.'char-limit'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'word-limit',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>word-limit" style="line-height:35px;"><?php _e("Word Limit <i>(0 indicates no limit)</i>","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>word-limit" id="<?php echo $this->prefix; ?>word-limit" value="<?php echo htmlentities($options[$this->prefix.'word-limit'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
<br/>
  <?php }
         if($this->useOpts($this->prefix.'restrict-tags',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>restrict-tags" style="line-height:35px;"><?php _e("Restrict Tags","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>restrict-tags" id="<?php echo $this->prefix; ?>restrict-tags" <?php if($options[$this->prefix.'restrict-tags']) echo "checked"; ?> >
<br/>
<label for="<?php echo $this->prefix; ?>allowable-tags" style="line-height:35px;"><?php _e("Allowable Tags","tdomf"); ?></label>
<br/>
<textarea title="true" cols="30" name="<?php echo $this->prefix; ?>allowable-tags" id="<?php echo $this->prefix; ?>allowable-tags" ><?php echo $options[$this->prefix.'allowable-tags']; ?></textarea>
<br/>
  <?php }
        /*if($this->useOpts($this->prefix.'use-filter',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>use-filter" style="line-height:35px;"><?php _e("Pass input through a Wordpress filter","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>use-filter" id="<?php echo $this->prefix; ?>use-filter" <?php if($options[$this->prefix.'use-filter']) echo "checked"; ?> >
<br/>
<label for="<?php echo $this->prefix; ?>filter" style="line-height:35px;"><?php _e("Filter:","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>filter" id="<?php echo $this->prefix; ?>filter" value="<?php echo htmlentities($options[$this->prefix.'filter'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }*/
  if($this->useOpts($this->prefix.'restrict-type',$show,$hide)) { ?>
      
<input type="radio" name="<?php echo $this->prefix; ?>restrict-type" id="<?php echo $this->prefix; ?>restrict-type" value="text"
<?php if($options[$this->prefix.'restrict-type'] == 'text') { ?>checked<?php } ?> /> <?php _e("Text","tdomf"); ?><br/>

<input type="radio" name="<?php echo $this->prefix; ?>restrict-type" id="<?php echo $this->prefix; ?>restrict-type" value="email"
<?php if($options[$this->prefix.'restrict-type'] == 'email') { ?>checked<?php } ?> /> <?php _e("Email","tdomf"); ?>
    
    <?php if($this->useOpts($this->prefix.'validate-email',$show,$hide) && function_exists('is_email') && function_exists('checkdnsrr')) { ?>
        <input type="checkbox" name="<?php echo $this->prefix; ?>validate-email" id="<?php echo $this->prefix; ?>validate-email" <?php if($options[$this->prefix.'validate-email']) echo "checked"; ?> >
        <label for="<?php echo $this->prefix; ?>validate-email" style="line-height:35px;"><?php _e("Validate <i>(checks if email domain exists)</i>","tdomf"); ?></label>
    <?php } ?>
    
    <br/>

<input type="radio" name="<?php echo $this->prefix; ?>restrict-type" id="<?php echo $this->prefix; ?>restrict-type" value="url"
<?php if($options[$this->prefix.'restrict-type'] == 'url') { ?>checked<?php } ?> /> <?php _e("URL","tdomf"); ?>

    <?php if($this->useOpts($this->prefix.'validate-url',$show,$hide) && function_exists('wp_get_http')) { ?>
        <input type="checkbox" name="<?php echo $this->prefix; ?>validate-url" id="<?php echo $this->prefix; ?>validate-url" <?php if($options[$this->prefix.'validate-url']) echo "checked"; ?> >
        <label for="<?php echo $this->prefix; ?>validate-url" style="line-height:35px;"><?php _e("Validate <i>(checks if URL exists)</i>","tdomf"); ?></label>
    <?php } ?>
    
    <br/>
    
<input type="radio" name="<?php echo $this->prefix; ?>restrict-type" id="<?php echo $this->prefix; ?>restrict-type" value="number"
<?php if($options[$this->prefix.'restrict-type'] == 'number') { ?>checked<?php } ?> /> <?php _e("Number","tdomf"); ?> <br/>
    
    <?php if($this->useOpts($this->prefix.'number-start',$show,$hide)) { ?>
        <label for="<?php echo $this->prefix; ?>number-start" style="line-height:35px;"><?php _e("Lowest number allowed <i>(leave blank for no limit)</i>:","tdomf"); ?></label>
        <input type="textfield" name="<?php echo $this->prefix; ?>number-start" id="<?php echo $this->prefix; ?>number-start" value="<?php if($options[$this->prefix.'number-start'] !== false) { echo htmlentities($options[$this->prefix.'number-start'],ENT_QUOTES,get_bloginfo('charset')); } ?>" />
<br/>
    <?php } ?>

    <?php if($this->useOpts($this->prefix.'number-end',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>'number-end" style="line-height:35px;"><?php _e("Highest number allowed <i>(leave blank for no limit)</i>:","tdomf"); ?></label>
        <input type="textfield" name="<?php echo $this->prefix; ?>number-end" id="<?php echo $this->prefix; ?>number-end" value="<?php if($options[$this->prefix.'number-end'] !== false) { echo htmlentities($options[$this->prefix.'number-end'],ENT_QUOTES,get_bloginfo('charset')); } ?>" />
<br/>        
    <?php } ?>
    
    <?php if($this->useOpts($this->prefix.'number-js',$show,$hide)) { ?>
        <input type="checkbox" name="<?php echo $this->prefix; ?>number-js" id="<?php echo $this->prefix; ?>number-js" <?php if($options[$this->prefix.'number-js']) echo "checked"; ?> >
        <label for="<?php echo $this->prefix; ?>number-js" style="line-height:35px;"><?php _e("Include Javascript checking code","tdomf"); ?></label
    <?php } ?>
    
    <?php if($this->useOpts($this->prefix.'number-decimal',$show,$hide)) { ?>
        <input type="checkbox" name="<?php echo $this->prefix; ?>number-decimal" id="<?php echo $this->prefix; ?>number-decimal" <?php if($options[$this->prefix.'number-decimal']) echo "checked"; ?> >
        <label for="<?php echo $this->prefix; ?>number-decimal" style="line-height:35px;"><?php _e("Allow decimal numbers","tdomf"); ?></label
    <?php } ?>
    
    <br/>

  <?php }
        return $options;
    }
    
    function validate($args,$opts,$preview=false,$original_field_name=false) 
    {
        $output = "";
        $text = false;

        // grab the input because we're going to test it
        
        $text = false;
        if(empty($output)) {
            if(isset($args[$this->prefix.'tf'])) {
                $text = $args[$this->prefix.'tf'];
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $text = $args[$original_field_name];
            } else {
                $output .= __("ERROR: Form is invalid. Please check TDO Mini Forms admin.","tdomf");
            }
        }
        
        // is it empty?

        if(empty($output) && $opts[$this->prefix.'required']) {    
            if(empty($text) || trim($text) == "" || $text == $opts[$this->prefix.'default-text']) {
                if($opts[$this->prefix.'restrict-type'] == 'url') {
                    if(!empty($opts[$this->prefix.'title'])) {
                        $output .= sprintf(__("You must specify a vaild URL for %s.","tdomf"),$opts[$this->prefix.'title']);
                    } else {
                        $output .= __("You must specify a valid URL.","tdomf");
                    }
                } else if($opts[$this->prefix.'restrict-type'] == 'email') {
                 if(!empty($opts[$this->prefix.'title'])) {
                        $output .= sprintf(__("You must specify a vaild email address for %s.","tdomf"),$opts[$this->prefix.'title']);
                    } else {
                        $output .= __("You must specify a valid email.","tdomf");
                    }     
                } else if($opts[$this->prefix.'restrict-type'] == 'number') {
                 if(!empty($opts[$this->prefix.'title'])) {
                        $output .= sprintf(__("You must specify a number for %s.","tdomf"),$opts[$this->prefix.'title']);
                    } else {
                        $output .= __("You must specify a number.","tdomf");
                    }                    
                } else {
                    #$opts[$this->prefix.'restrict-type'] == 'text'
                    if(!empty($opts[$this->prefix.'title'])) {
                        $output .= sprintf(__("You must specify some %s.","tdomf"),$opts[$this->prefix.'title']);
                    } else {
                        $output .= __("You must specify some text.","tdomf");
                    }
                }
            }
        }
        
                
        // is it a real email, url or number
        
        if(empty($output) && $opts[$this->prefix.'restrict-type'] != 'text') {
            if($opts[$this->prefix.'restrict-type'] == 'url') {
                if(!tdomf_check_url($text)) {
                    if(!empty($opts[$this->prefix.'title'])) {
                      $output .= sprintf(__("The URL \"%s\" for %s does not look correct.","tdomf"),$text,$opts[$this->prefix.'title']);
                    } else {
                      $output .= sprintf(__("The URL \"%s\" does not look correct.","tdomf"),$text);
                    }
                } else if($opts[$this->prefix.'validate-url']){
                    if(function_exists('wp_get_http')) {
                        $headers = wp_get_http($text,false,1);
                        if($headers == false) {
                            $output .= sprintf(__('The URL doesn\'t doesnt seem to exist.','tdomf'), $headers["response"]);
                        } else if($headers["response"] != '200') {
                            $output .= sprintf(__('The link doesn\'t doesnt seem to exist. Returned %d error code.','tdomf'), $headers["response"]);
                        }
                    }
                }
            } else if($opts[$this->prefix.'restrict-type'] == 'email') {
                if(!tdomf_check_email_address($text,$opts[$this->prefix.'validate-email'])) {
                    if(!empty($opts[$this->prefix.'title'])) {
                      $output .= sprintf(__("The email address \"%s\" for %s does not seem to be correct.","tdomf"),$text,$opts[$this->prefix.'title']);
                    } else {
                      $output .= sprintf(__("The email address \"%s\" does not seem to be correct.","tdomf"),$text);
                    }
                }
            } else if($opts[$this->prefix.'restrict-type'] == 'number') {
                if(is_numeric($text)) {
                    if($opts[$this->prefix.'number-decimal']) {
                        $number = floatval($text);
                        if($opts[$this->prefix.'number-start'] !== false && $number < $opts[$this->prefix.'number-start']) {
                            if(!empty($opts[$this->prefix.'title'])) {
                                $output .= sprintf(__("%f for %s is too low. It must be equal to or greater than %f.","tdomf"),$number,$opts[$this->prefix.'title'],$opts[$this->prefix.'number-start']);
                            } else {
                                $output .= sprintf(__("%f is too low. It must be equal to or greater than %f.","tdomf"),$text,$opts[$this->prefix.'number-start']);
                            }
                        } else if($opts[$this->prefix.'number-end'] !== false && $number > $opts[$this->prefix.'number-end']) {
                            if(!empty($opts[$this->prefix.'title'])) {
                                $output .= sprintf(__("%f for %s is too high. It must be equal to or less than %f.","tdomf"),$text,$opts[$this->prefix.'title'],$opts[$this->prefix.'number-start']);
                            } else {
                                $output .= sprintf(__("%f is too high. It must be equal to or less than %f.","tdomf"),$text,$opts[$this->prefix.'number-start']);
                            }                        
                        }
                    } else {
                        $number = intval($text);
                        if($opts[$this->prefix.'number-start'] !== false && $number < $opts[$this->prefix.'number-start']) {
                            if(!empty($opts[$this->prefix.'title'])) {
                                $output .= sprintf(__("%d for %s is too low. It must be equal to or greater than %d.","tdomf"),$number,$opts[$this->prefix.'title'],$opts[$this->prefix.'number-start']);
                            } else {
                                $output .= sprintf(__("%d is too low. It must be equal to or greater than %d.","tdomf"),$text,$opts[$this->prefix.'number-start']);
                            }
                        } else if($opts[$this->prefix.'number-end'] !== false && $number > $opts[$this->prefix.'number-end']) {
                            if(!empty($opts[$this->prefix.'title'])) {
                                $output .= sprintf(__("%d for %s is too high. It must be equal to or less than %d.","tdomf"),$text,$opts[$this->prefix.'title'],$opts[$this->prefix.'number-start']);
                            } else {
                                $output .= sprintf(__("%d is too high. It must be equal to or less than %d.","tdomf"),$text,$opts[$this->prefix.'number-start']);
                            }                        
                        }
                    }
                } else if(trim($text) != ""){
                    if(!empty($opts[$this->prefix.'title'])) {
                      $output .= sprintf(__("\"%s\" for %s is not a valid number.","tdomf"),$text,$opts[$this->prefix.'title']);
                    } else {
                      $output .= sprintf(__("\"%s\" is not a valid number.","tdomf"),$text);
                    }
                }
            }
        }
        
        // does it fit the counts?
        
        if(empty($output) && $opts[$this->prefix.'restrict-type'] == 'text' &&
            ($opts[$this->prefix.'word-limit'] > 0 || $opts[$this->prefix.'char-limit']) > 0) {
            if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
              $text = strip_tags($text,$opts[$this->prefix.'allowable-tags']);
            }
            
            $len = strlen($text);
          if($opts[$this->prefix.'char-limit'] > 0 && $len > $opts[$this->prefix.'char-limit']) {
            if(!empty($opts[$this->prefix.'title'])) {
                $output .= sprintf(__("You have exceeded the max character length by %d characters for %s.","tdomf"),($len - $opts[$this->prefix.'char-limit']),$opts[$this->prefix.'title']);
            } else {
                $output .= sprintf(__("You have exceeded the max character length by %d characters.","tdomf"),($len - $opts[$this->prefix.'char-limit']));
            }
          } else if($opts[$this->prefix.'word-limit'] > 0) {
            // Remove all HTML tags as they do not count as "words"!
            $text = trim(strip_tags($text));
            // Replace newlines with spaces
            $text = preg_replace("/\r?\n/", " ", $text);
            // Remove excess whitespace
            $text = preg_replace('/\s\s+/', ' ', $text);
            // count the words!
            $word_count = count(explode(" ", $text));
            if($word_count > $opts[$this->prefix.'word-limit']) {
              if(!empty($opts[$this->prefix.'title'])) {
                  $output .= sprintf(__("You have exceeded the max word count by %d words for %s.","tdomf"),($word_count - $opts[$this->prefix.'word-limit']),$opts[$this->prefix.'title']);
              } else {
                  $output .= sprintf(__("You have exceeded the max word count by %d words.","tdomf"),($word_count - $opts[$this->prefix.'word-limit']));
              }
            }
          }
        }
        
        return $output;
    }
    
    function post($args,$opts,$original_field_name=false)
    {
        $output = false;
        $text = false;
        
        if(isset($args[$this->prefix.'tf']))
        {
            $text = $args[$this->prefix.'tf'];
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $text = $args[$original_field_name];
        }
        
        if($text != false) {
            
            if($opts[$this->prefix.'restrict-type'] == 'number') {
                
                if($opts[$this->prefix.'number-decimal']) {
                    $output = floatval($text);
                } else {
                    $output = intval($text);
                }
                
            } else {
                $output = $text;
                
                if($opts[$this->prefix.'restrict-type'] == 'text') {
    
                    if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
                        $output = strip_tags($output,$options['allowable-tags']);
                    }
                }
                
                if(($opts[$this->prefix.'use-filter'] === true || $opts[$this->prefix.'use-filter'] == 'post') 
                    && !empty($opts[$this->prefix.'filter'])) {
                   tdomf_log_message("textfield: <pre>" . htmlentities(var_export($opts,true)) . "</pre>");
                   tdomf_log_message("textfield: applying " . $opts[$this->prefix.'filter'] . " to input for " . $this->prefix);
                    $output = apply_filters($opts[$this->prefix.'filter'], $output);
                }
                
                if($opts[$this->prefix.'protect-magic-quotes']) {
                    #if(get_magic_quotes_gpc()) { <- this is not limited to magic quotes being on!
                        /* Wordpress 2.8.x adds slashes to ' and " but not to 
                         * other back slashes. Passing the protected content to post update
                         * works fine then for ' and " but not for slashes. Need to protect
                         * slashes before passing it through 'the_content' */
                        $output = str_replace('\\','\\\\',$output);
                    #}
                }
            }
        }
        
        return $output;
    }
}

/** 
* Utility class for any widget using TextAreas
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 1.0
* @since 0.13.5
* @access public 
* @copyright Mark Cunningham
* 
*/ 
class TDOMF_WidgetFieldTextArea extends TDOMF_WidgetField {

    function TDOMF_WidgetFieldTextArea($prefix)
    {
        parent::TDOMF_WidgetField($prefix);
    }
    
    function getId()
    {
        return $this->prefix.'ta';
    }
    
    function getOptions($opts) {
        $defs = array( $this->prefix.'cols' => 40,
                       $this->prefix.'rows' => 10, 
                       $this->prefix.'quicktags' => false,
                       $this->prefix.'restrict-tags' => true,
                       $this->prefix.'allowable-tags' => "<p><b><em><u><strong><a><img><table><tr><td><blockquote><ul><ol><li><br><sup>",
                       $this->prefix.'char-limit' => 0,
                       $this->prefix.'word-limit' => 0,
                       $this->prefix.'required' => true,
                       $this->prefix.'use-filter' => false, # 'preview', 'post', true/enabled, false/disabled
                       $this->prefix.'filter' => 'the_content',
                       $this->prefix.'kses' => false,
                       $this->prefix.'default-text' => "",
                       $this->prefix.'protect-magic-quotes' => true,
                       $this->prefix.'title' => "Text");
        $opts = wp_parse_args($opts, $defs);
        return $opts;
    }
   
    function form($args,$opts)
    {
        // contents of textarea
        $text = $opts[$this->prefix.'default-text'];
        if(isset($args[$this->prefix.'ta'])) { 
            $text = $args[$this->prefix.'ta'];
        }
        
        // pre
        if(!empty($opts[$this->prefix.'title'])) {
            if($opts[$this->prefix.'required']) {
                $output = '<label for="'.$this->prefix.'ta" class="required">'.sprintf(__("%s (Required):","tdomf"),$opts[$this->prefix.'title']);
            } else {
                $output = '<label for="'.$this->prefix.'ta" >'.sprintf(__("%s:","tdomf"),$opts[$this->prefix.'title']);
            }
            $output .= "</label>\n<br/>\n";
        }
        
        if(!empty($opts[$this->prefix.'allowable-tags']) && $opts[$this->prefix.'restrict-tags']) {
            $output .= sprintf(__("<small>Allowable Tags: %s</small>","tdomf"),htmlentities($opts[$this->prefix.'allowable-tags']))."<br/>";
        }
        if($opts[$this->prefix.'word-limit'] > 0) {
            $output .= sprintf(__("<small>Max Word Limit: %d</small>","tdomf"),$opts[$this->prefix.'word-limit'])."<br/>";
        }
        if($opts[$this->prefix.'char-limit'] > 0) {
            $output .= sprintf(__("<small>Max Character Limit: %d</small>","tdomf"),$opts[$this->prefix.'char-limit'])."<br/>";
        }
        if($opts[$this->prefix.'quicktags']) {
            $qt_path = TDOMF_URLPATH."tdomf-quicktags.js.php?postfix=".$this->prepJSCode($this->prefix).'ta';
            if(!empty($opts[$this->prefix.'allowable-tags']) && $opts[$this->prefix.'restrict-tags']) {
                $qt_path = TDOMF_URLPATH."tdomf-quicktags.js.php?postfix=".$this->prepJSCode($this->prefix)."ta&allowed_tags=".urlencode($opts[$this->prefix.'allowable-tags']);
            }
            $output .= "\n<script src='$qt_path' type='text/javascript'></script>\n";
            $output .= "\n<script type='text/javascript'>edToolbar".$this->prepJSCode($this->prefix)."ta();</script>\n";
        }
        
        // the text area
        $output .= '<textarea title="'.htmlentities($opts[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')).'" rows="'.$opts[$this->prefix.'rows'].'" cols="'.$opts[$this->prefix.'cols'].'" name="'.$this->prefix.'ta" id="'.$this->prefix.'ta" >'.$text.'</textarea>';
        
        // post
        if($opts[$this->prefix.'quicktags']) {
            $output .= "\n<script type='text/javascript'>var edCanvas".$this->prepJSCode($this->prefix)."ta = document.getElementById('".$this->prefix."ta');</script>\n";
        }
        return $output;
    }
    
    function formHack($args,$opts)
    {
        $output = "";
        
        if(!empty($opts[$this->prefix.'title'])) {
            if($opts[$this->prefix.'required']) {
              $output .= "\t\t".'<label for="'.$this->prefix.'ta" class="required">'.sprintf(__("%s (Required):","tdomf"),$opts[$this->prefix.'title'])."\n\t\t\t<br/>\n";      
            } else {
              $output .= "\t\t".'<label for="'.$this->prefix.'ta">'.sprintf(__("%s:","tdomf"),$opts[$this->prefix.'title'])."\n\t\t\t<br/>\n";
            }
            $output .= "\t\t</label>\n";    
        }
        if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
          $output .= "\t\t".sprintf(__("<small>Allowable Tags: %s</small>","tdomf"),htmlentities($opts[$this->prefix.'allowable-tags']))."\n\t\t<br/>\n";
        }
        if($opts[$this->prefix.'word-limit'] > 0) {
          $output .= "\t\t".sprintf(__("<small>Max Word Limit: %d</small>","tdomf"),$opts[$this->prefix.'word-limit'])."\n\t\t<br/>\n";
        }
        if($opts[$this->prefix.'char-limit'] > 0) {
          $output .= "\t\t".sprintf(__("<small>Max Character Limit: %d</small>","tdomf"),$opts[$this->prefix.'char-limit'])."\n\t\t<br/>\n";
        }
        if($opts[$this->prefix.'quicktags'] == true) {
          $qt_path = TDOMF_URLPATH."tdomf-quicktags.js.php?postfix=".$this->prepJSCode($this->prefix).'ta';
          if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
            $qt_path = TDOMF_URLPATH."tdomf-quicktags.js.php?postfix=".$this->prepJSCode($this->prefix)."ta&allowed_tags=".urlencode($opts[$this->prefix.'allowable-tags']);
          }
          $output .= "\t\t<script src='$qt_path' type='text/javascript'></script>\n";
          $output .= "\t\t<script type='text/javascript'>edToolbar".$this->prepJSCode($this->prefix)."ta();</script>\n";
        }
        $output .= "\t\t".'<textarea title="'.htmlentities($opts[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')).'" rows="'.$opts[$this->prefix.'rows'].'" cols="'.$opts[$this->prefix.'cols'].'" name="'.$this->prefix.'ta" id="'.$this->prefix.'ta" >';
        $output .= '<?php if(isset($post_args["'.$this->prefix.'ta"])) {'."\n";
           $output .= "\t\t\t\t".'echo $post_args["'.$this->prefix.'ta"];'."\n";
        $output .= "\t\t\t".'} else { ?>';
            $output .= $opts[$this->prefix.'default-text'];
        $output .= '<?php } ?></textarea>'."\n";
        if($opts[$this->prefix.'quicktags'] == true) {
          $output .= "\t\t<script type='text/javascript'>var edCanvas".$this->prepJSCode($this->prefix)."ta = document.getElementById('".$this->prefix."ta');</script>";
        }

        return $output;
    }
    
    function preview($args,$opts,$original_field_name=false)
    {
        if(isset($args[$this->prefix.'ta'])) {
            $output = $args[$this->prefix.'ta'];
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $output = $args[$original_field_name];
        } else {
            tdomf_log_message("TextArea: can't get any input for preview!",TDOMF_LOG_ERROR);
        }
        
        #if($opts[$this->prefix.'use-filter'] && !empty($opts[$this->prefix.'filter'])) {
            $output = preg_replace('|\<!--tdomf_form.*-->|', '', $output);
            $output = preg_replace('|\[tdomf_form.*\]|', '', $output);
        #}
        
        if($opts[$this->prefix.'kses'] && !tdomf_get_option_form(TDOMF_OPTION_MODERATION,$args['tdomf_form_id'])){
         // if moderation is enabled, we don't do kses filtering, might as well
         // give full picture to user!
         $output = wp_filter_post_kses($output);
        }
        
        if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
            $output = strip_tags($output,$opts[$this->prefix.'allowable-tags']);
        }
        
        if(($opts[$this->prefix.'use-filter'] === true || $opts[$this->prefix.'use-filter'] == 'preview') 
            && !empty($opts[$this->prefix.'filter'])) {
            
            $output = apply_filters($opts[$this->prefix.'filter'], $output);
            
            if($opts[$this->prefix.'filter'] == 'the_content' ) {
                if($opts[$this->prefix.'protect-magic-quotes']) {
                    #if(get_magic_quotes_gpc()) { <- this is not limited to magic quotes being on!
                        /* Wordpress 2.8.x: 'the_content' filter adds slashes to ' and " but not 
                         * to other slashes. Major pain when your doing you're best to keep 
                         * it unnecessary slash clean! */
                        # this should catch most of all the extra slashes used by wptexturize
                        $output = preg_replace('/\\\&\#(\d*)\;/','&#$1;',$output);
                        # but sometimes it donesn't convert one or two (but still adds slashes)
                        $output = str_replace("\\'","'",$output);
                        # I've also seen it "steal" some but not all stand alone backslashes - nothing I can do about it!
                    #}
                }
            }
        }
        
        if(!empty($opts[$this->prefix.'title'])) {
            $output = "<b>".sprintf(__("%s:","tdomf"),$opts[$this->prefix.'title'])."</b><br/>".$output;
        } 
        
        return $output; 
    }
    
    function previewHack($args,$opts)
    {
        $output .= "\t<?php \$temp_text = \$post_args['".$this->prefix."ta'];\n";
        $output .= "\t".'$temp_text = preg_replace(\'|\<!--tdomf_form.*-->|\', \'\', $temp_text);'."\n";
        $output .= "\t".'$temp_text = preg_replace(\'|\\[tdomf_form.*\\]|\', \'\', $temp_text);'."\n";
        if($opts[$this->prefix.'kses'] && !tdomf_get_option_form(TDOMF_OPTION_MODERATION,$args['tdomf_form_id'])){
          $output .= "\t".'$temp_text = wp_filter_post_kses($temp_text);'."\n";
        }
        if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
          $output .= "\t".'$temp_text = strip_tags($temp_text,\''.$opts[$this->prefix.'allowable-tags'].'\');'."\n";
        }
         if(($opts[$this->prefix.'use-filter'] === true || $opts[$this->prefix.'use-filter'] == 'preview') 
            && !empty($opts[$this->prefix.'filter'])) {
          $output .= "\t".'$temp_text = apply_filters(\''.$opts[$this->prefix.'filter'].'\',$temp_text);'."\n";
        }
        if($opts[$this->prefix.'filter'] == 'the_content' ) {
           if($opts[$this->prefix.'protect-magic-quotes']) {
               #if(get_magic_quotes_gpc()) {
                  $output .= "\t".'$temp_text = preg_replace(\'/\\\\\\&\\#(\\d*)\\;/\',\'&#$1;\',$temp_text);'."\n";
                  $output .= "\t".'$temp_text = str_replace("\\\\\'","\'",$temp_text);'."\n";
               #}
           }
        }
        $output .= "\t?>\n";
        if(!empty($opts[$this->prefix.'title'])) {
            $output .= "\t<b>".sprintf(__("%s:","tdomf"),$opts[$this->prefix.'title'])."</b>\n\t<br/>\n";
        }
        $output .= "\t<?php echo \$temp_text; ?>";
        return $output; 
    }
    
    function control($options,$form_id,$show=false,$hide=false,$save=true)
    {
        if((is_array($show) && empty($show))) {
            # nothing to do if show list is empty
            return array();
        }

        // prepare options!
        if($save) {
            $retOptions = array();
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'cols',$show,$hide);
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'rows',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'quicktags',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'restrict-tags',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'allowable-tags',$show,$hide);
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'char-limit',$show,$hide);
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'word-limit',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'required',$show,$hide);
            /*$retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'use-filter',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'filter',$show,$hide);*/        
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'kses',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'default-text',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'title',$show,$hide);
                     
            $options = wp_parse_args($retOptions, $options);
        }
        
        // Display control panel for this textarea
        
        if($this->useOpts($this->prefix.'required',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>required" style="line-height:35px;"><?php _e("Required","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>required" id="<?php echo $this->prefix; ?>required" <?php if($options[$this->prefix.'required']) echo "checked"; ?> >
            <?php if(!$this->useOpts($this->prefix.'quicktags',$show,$hide)) { ?>
                <br/>
            <?php } ?>
  <?php } 
        if($this->useOpts($this->prefix.'quicktags',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>quicktags" style="line-height:35px;"><?php _e("Use Quicktags","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>quicktags" id="<?php echo $this->prefix; ?>quicktags" <?php if($options[$this->prefix.'quicktags']) echo "checked"; ?> >
<br/>
  <?php } 
        if($this->useOpts($this->prefix.'char-limit',$show,$hide)) { ?> 
<label for="<?php echo $this->prefix; ?>char-limit" style="line-height:35px;"><?php _e("Character Limit <i>(0 indicates no limit)</i>","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>char-limit" id="<?php echo $this->prefix; ?>char-limit" value="<?php echo htmlentities($options[$this->prefix.'char-limit'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'word-limit',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>word-limit" style="line-height:35px;"><?php _e("Word Limit <i>(0 indicates no limit)</i>","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>word-limit" id="<?php echo $this->prefix; ?>word-limit" value="<?php echo htmlentities($options[$this->prefix.'word-limit'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'cols',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>cols" style="line-height:35px;"><?php _e("Cols","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>cols" id="<?php echo $this->prefix; ?>cols" value="<?php echo htmlentities($options[$this->prefix.'cols'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
  <?php }
        if($this->useOpts($this->prefix.'rows',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>rows" style="line-height:35px;"><?php _e("Rows","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>rows" id="<?php echo $this->prefix; ?>rows" value="<?php echo htmlentities($options[$this->prefix.'rows'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
  <?php } 
        if($this->useOpts($this->prefix.'cols',$show,$hide) || $this->useOpts($this->prefix.'rows',$show,$hide)) { ?>
<br/>
  <?php }
        if($this->useOpts($this->prefix.'restrict-tags',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>restrict-tags" style="line-height:35px;"><?php _e("Restrict Tags","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>restrict-tags" id="<?php echo $this->prefix; ?>restrict-tags" <?php if($options[$this->prefix.'restrict-tags']) echo "checked"; ?> >
<br/>
<label for="<?php echo $this->prefix; ?>allowable-tags" style="line-height:35px;"><?php _e("Allowable Tags","tdomf"); ?></label>
<br/>
<textarea title="true" cols="30" name="<?php echo $this->prefix; ?>allowable-tags" id="<?php echo $this->prefix; ?>allowable-tags" ><?php echo $options[$this->prefix.'allowable-tags']; ?></textarea>
<br/>
  <?php }
        /*if($this->useOpts($this->prefix.'use-filter',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>use-filter" style="line-height:35px;"><?php _e("Pass input through a Wordpress filter","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>use-filter" id="<?php echo $this->prefix; ?>use-filter" <?php if($options[$this->prefix.'use-filter']) echo "checked"; ?> >
<br/>
<label for="<?php echo $this->prefix; ?>filter" style="line-height:35px;"><?php _e("Filter:","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>filter" id="<?php echo $this->prefix; ?>filter" value="<?php echo htmlentities($options[$this->prefix.'filter'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }*/
        if($this->useOpts($this->prefix.'title',$show,$hide)) { ?>
            <label for="<?php echo $this->prefix; ?>title" style="line-height:35px;"><?php _e("Title:","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>title" id="<?php echo $this->prefix; ?>title" value="<?php echo htmlentities($options[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }
        if($this->useOpts($this->prefix.'default-text',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>default-text" style="line-height:35px;"><?php _e("Default Text","tdomf"); ?></label>
<br/>
<textarea title="true" cols="30" name="<?php echo $this->prefix; ?>default-text" id="<?php echo $this->prefix; ?>default-text" ><?php echo $options[$this->prefix.'default-text']; ?></textarea>
<br/>
  <?php }
  
        // return latest version of options
        return $options;
    }
    
    function validate($args,$opts,$preview=false,$original_field_name=false) {
        
        $output = "";
        $text = false;

        // grab the input because we're going to test it
        
        $text = false;
        if(empty($output)) {
            if(isset($args[$this->prefix.'ta'])) {
                $text = $args[$this->prefix.'ta'];
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $text = $args[$original_field_name];
            } else {
                $output .= __("ERROR: Form is invalid. Please check TDO Mini Forms admin.","tdomf");
            }
        }
        
        // is it empty?

        if(empty($output) && $opts[$this->prefix.'required']) {    
            if(empty($text) || trim($text) == "" || $text == $opts[$this->prefix.'default-text']) {
                if(!empty($opts[$this->prefix.'title'])) {
                    $output .= sprintf(__("You must specify some %s.","tdomf"),$opts[$this->prefix.'title']);
                } else {
                    $output .= __("You must specify some text.","tdomf");
                }
            } 
        }
        
        // does it fit the counts?
        
        if(empty($output) && ($opts[$this->prefix.'word-limit'] > 0 || $opts[$this->prefix.'char-limit'] > 0)) {
                         
          // prefitler the text so it's as close to the end result as possible

          #if($opts[$this->prefix.'use-filter'] && !empty($opts[$this->prefix.'filter'])) {
              $text = preg_replace('|\<!--tdomf_form.*-->|', '', $text);
              $text = preg_replace('|\[tdomf_form.*\]|', '', $text);
          #}
          
          if($opts[$this->prefix.'kses'] && !tdomf_get_option_form(TDOMF_OPTION_MODERATION,$args['tdomf_form_id'])){
              // if moderation is enabled, we don't do kses filtering, might as well
              // give full picture to user!
              $text = wp_filter_post_kses($text);
          }
    
          if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
              $text = strip_tags($text,$opts[$this->prefix.'allowable-tags']);
          }
          
          /*$output .= "Stripped output: <pre>".htmlentities($text)."</pre><br/>";*/
          
          $len = strlen($text);
          if($opts[$this->prefix.'char-limit'] > 0 && $len > $opts[$this->prefix.'char-limit']) {
            if(!empty($opts[$this->prefix.'title'])) {
                $output .= sprintf(__("You have exceeded the max character length by %d characters for %s.","tdomf"),($len - $opts[$this->prefix.'char-limit']),$opts[$this->prefix.'title']);
            } else {
                $output .= sprintf(__("You have exceeded the max character length by %d characters.","tdomf"),($len - $opts[$this->prefix.'char-limit']));
            }
          } else if($opts[$this->prefix.'word-limit'] > 0) {
            // Remove all HTML tags as they do not count as "words"!
            $text = trim(strip_tags($text));
            // Replace newlines with spaces
            $text = preg_replace("/\r?\n/", " ", $text);
            // Remove excess whitespace
            $text = preg_replace('/\s\s+/', ' ', $text);
            // count the words!
            $word_count = count(explode(" ", $text));
            if($word_count > $opts[$this->prefix.'word-limit']) {
              if(!empty($opts[$this->prefix.'title'])) {
                  $output .= sprintf(__("You have exceeded the max word count by %d words for %s.","tdomf"),($word_count - $opts[$this->prefix.'word-limit']),$opts[$this->prefix.'title']);
              } else {
                  $output .= sprintf(__("You have exceeded the max word count by %d words.","tdomf"),($word_count - $opts[$this->prefix.'word-limit']));
              }
            }
          }
        }
        return $output;
    }
    
    function post($args,$opts,$original_field_name=false)
    {
        $output = false;
        $text = false;
        
        if(isset($args[$this->prefix.'ta']))
        {
            $text = $args[$this->prefix.'ta'];
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $text = $args[$original_field_name];
        }
        
        if($text != false) {
            $output = $text;
            
            if($opts[$this->prefix.'allowable-tags'] != "" && $opts[$this->prefix.'restrict-tags']) {
                $output = strip_tags($output,$opts[$this->prefix.'allowable-tags']);
            } 

            if($opts[$this->prefix.'protect-magic-quotes']) {
                #if(get_magic_quotes_gpc()) { <- this is not limited to magic quotes being on!
                    /* Wordpress 2.8.x adds slashes to ' and " but not to 
                     * other back slashes. Passing the protected content to post update
                     * works fine then for ' and " but not for slashes. Need to protect
                     * slashes before passing it through 'the_content' */
                    $output = str_replace('\\','\\\\',$output);
                #}
            }
            
            if(($opts[$this->prefix.'use-filter'] === true || $opts[$this->prefix.'use-filter'] == 'post') 
                && !empty($opts[$this->prefix.'filter'])) {
                $output = apply_filters($opts[$this->prefix.'filter'], $output);
            }
        }
        return $output;
    }
}

/** 
* Utility class for any widget using a Hidden input
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 1.0
* @since 0.13.6
* @access public 
* @copyright Mark Cunningham
* 
*/ 
class TDOMF_WidgetFieldHidden extends TDOMF_WidgetField {
    
    function TDOMF_WidgetFieldHidden($prefix)
    {
        parent::TDOMF_WidgetField($prefix);
    }
    
    function getId()
    {
        return $this->prefix.'h';
    }
    
    function getOptions($opts) {
        $defs = array( $this->prefix.'default-value' => "",
                       $this->prefix.'value-php' => false);
        $opts = wp_parse_args($opts, $defs);
        return $opts;
    }
    
    function form($args,$opts)
    {
        $value = $opts[$this->prefix.'default-value'];
        if($opts[$this->prefix.'value-php']) {
            #tdomf_log_message('Executing "'.$value.'" ...');
            // execute any PHP code in the value    
            ob_start();
            extract($args,EXTR_PREFIX_INVALID,"tdomf_");
            $value = @eval($value);
            $value = ob_get_contents();
            ob_end_clean();
        }
        
        $output = '<input type="hidden" id="'.$this->prefix.'h" name="'.$this->prefix.'h" id="'.$this->prefix.'h" value="'.htmlentities(strval($value),ENT_QUOTES,get_bloginfo('charset')).'" />'."\n";
        
        return $output;
    }
    
    function formHack($args,$opts)
    {
        $value = $opts[$this->prefix.'default-value'];
        
        $output = "\t\t".'<input type="hidden" id="'.$this->prefix.'h" name="'.$this->prefix.'h" id="'.$this->prefix.'h" value="';
        if($opts[$this->prefix.'value-php']) {
            $output .= '<?php '.$value.' ?>';
        } else {
            $output .= htmlentities($value,ENT_QUOTES,get_bloginfo('charset'));
        }
        $output .= '" />'."\n";
        
        return $output;
    }
    
    function preview($args,$opts,$original_field_name=false)
    {
        if(isset($args[$this->prefix.'h'])) {
            $output = $args[$this->prefix.'h'];
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $output = $args[$original_field_name];
        } else {
            tdomf_log_message("Hidden: can't get any input for preview!",TDOMF_LOG_ERROR);
        }

        return $output;
    }
    
    function previewHack($args,$opts,$original_field_name=false)
    {
        $output = "\t<?php echo \$post_args['".$this->prefix."h']; ?>";
        return $output; 
    }
    
    function control($options,$form_id,$show=false,$hide=false,$save=false)
    {
        if((is_array($show) && empty($show))) {
            # nothing to do if show list is empty
            return array();
        }
        
        // prepare options!
        if($save) {
            $retOptions = array();        
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'default-value',$show,$hide);     
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'value-php',$show,$hide);
                     
            $options = wp_parse_args($retOptions, $options);
        }
        
        if($this->useOpts($this->prefix.'default-value',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>default-value" style="line-height:35px;"><?php _e("Default Value:","tdomf"); ?></label>
<input type="text" title="true" size="30" name="<?php echo $this->prefix; ?>default-value" id="<?php echo $this->prefix; ?>default-value" value="<?php echo htmlentities($options[$this->prefix.'default-value'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
        <?php if($this->useOpts($this->prefix.'value-php',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>value-php" style="line-height:35px;"><?php _e("Treat default value as code (php)","tdomf"); ?></label>
<input type="checkbox" name="<?php echo $this->prefix; ?>value-php" id="<?php echo $this->prefix; ?>value-php" <?php if($options[$this->prefix.'value-php']) echo "checked"; ?> >
<br/>
       <?php } ?>
  <?php }
  
        return $options;
    }
    
    function validate($args,$opts,$preview=false,$original_field_name=false) {
        
        $output = "";
        $input = false;

        // grab the input because we're going to test it
        
        $input = false;
        if(empty($output)) {
            if(isset($args[$this->prefix.'h'])) {
                $input = $args[$this->prefix.'h'];
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $input = $args[$original_field_name];
            } else {
                $output .= __("ERROR: Form is invalid. Please check TDO Mini Forms admin.","tdomf");
            }
        }
        
        // eh? What other tests can we do?
        
        return $output;
    }
    
    function post($args,$opts,$original_field_name=false)
    {
        if(isset($args[$this->prefix.'h']))
        {
            $output = $args[$this->prefix.'h'];
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $output = $args[$original_field_name];
        }
        return $output;
    }
}

/** 
* Utility class for any widget using a Checkbox input
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 1.0
* @since 0.13.6
* @access public 
* @copyright Mark Cunningham
* 
*/ 
class TDOMF_WidgetFieldCheckBox extends TDOMF_WidgetField {
    
    function TDOMF_WidgetFieldCheckBox($prefix)
    {
        parent::TDOMF_WidgetField($prefix);
    }
    
    function getId()
    {
        return $this->prefix.'cb';
    }
    
    function getOptions($opts) {
        $defs = array( $this->prefix.'required' => false,
                       $this->prefix.'required-value' => true,
                       $this->prefix.'default-value' => true,
                       $this->prefix.'text' => '');
        $opts = wp_parse_args($opts, $defs);
        return $opts;
    }
    
    function form($args,$opts)
    {
        $value = $opts[$this->prefix.'default-value'];
        if(isset($args[$this->prefix.'cb'])) { 
            $value = $args[$this->prefix.'cb'];
        }
        // we only want this to count for the *first* call, i.e. before preview
        else if(strpos($args['mode'],'-preview') !== false) {
            $value = false;
        }
                
        $output .= '<input type="checkbox" name="'.$this->prefix.'cb" id="'.$this->prefix.'cb"';
        if($value){ $output .= ' checked '; }
        $output .= '/> ';
        
        if(!empty($opts[$this->prefix.'text'])) {
            $output .= '<label for="'.$this->prefix.'cb"';
            if($opts[$this->prefix.'required']) {
                $output .= ' class="required">'.sprintf(__('%s (Required)','tdomf'),$opts[$this->prefix.'text']);
            } else {
                $output .= '>'.$opts[$this->prefix.'text'];
            }
            $output .= '</label>'."\n";
        } else {
            $output .= "\n";
        }
        
        return $output;
    }
    
    function formHack($args,$opts)
    {
        $output = "";

        $defval = ($opts[$this->prefix.'default-value']) ? "true" : "false" ;  
        
        $output = "\t\t<?php \$temp_value = $defval; ".'if(isset($post_args["'.$this->prefix.'cb"])) {'."\n";
           $output .= "\t\t\t".'$temp_value = true;'."\n";
        $output .= "\t\t".'} else if(strpos($post_args[\'mode\'],\'-preview\') !== false) {'."\n";
            $output .= "\t\t\t".'$temp_value = false;'."\n";
        $output .= "\t\t".'} ?>'."\n";
        
        $output .= '<input type="checkbox" name="'.$this->prefix.'cb" id="'.$this->prefix.'cb"';
        $output .= "<?php if(\$temp_value){ ?> checked <?php } ?>";
        $output .= '/> ';
        
        if(!empty($opts[$this->prefix.'text'])) {
            $output .= '<label for="'.$this->prefix.'cb"';
            if($opts[$this->prefix.'required']) {
                $output .= ' class="required">'.sprintf(__('%s (Required)','tdomf'),$opts[$this->prefix.'text']);
            } else {
                $output .= '>'.$opts[$this->prefix.'text'];
            }
            $output .= '</label>'."\n";
        } else {
            $output .= "\n";
        }
       
        return $output;
    }
    
    function preview($args,$opts,$original_field_name=false)
    {
        $value = $opts[$this->prefix.'default-value'];
        if(isset($args[$this->prefix.'cb'])) {
            $value = true;
        } else if($original_field_name != false && isset($args[$original_field_name])) {
            $value = true;
        } 
        $value = ($value) ? __("Checked",'tdomf') : __("Unchecked",'tdomf') ;
        
        if(!empty($opts[$this->prefix.'text'])) {
            $output = "<b>".sprintf(__("%s: ","tdomf"),$opts[$this->prefix.'text'])."</b>".$value;
        } else {
            $output = $value;
        }
        
        return $output;
    }
    
    function previewHack($args,$opts,$original_field_name=false)
    {
        if(!empty($opts[$this->prefix.'text'])) {
            $output .= "\t\t".'<b>'.sprintf(__("%s: ","tdomf"),$opts[$this->prefix.'text']).'</b> '."\n";
        } 
        $output .= "\t\t".'<?php if(isset($post_args["'.$this->prefix.'cb"])) {'."\n";
        $output .= "\t\t\t".'echo "'.__("Checked",'tdomf').'";'."\n";
        $output .= "\t\t".'} else {'."\n";
        $output .= "\t\t\t".'echo "'.__("Unchecked",'tdomf').'";'."\n";
        $output .= "\t\t".'} ?>'."\n";
        
        return $output;
    }
    
    function validate($args,$opts,$preview=false,$original_field_name=false) {
        
        $output = "";
        $input = false;

        // grab the input because we're going to test it
        
        $input = false;
        if(empty($output)) {
            if(isset($args[$this->prefix.'cb'])) {
                $input = true;
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $input = true;
            } 
        }
        
        // required check
        
        if($opts[$this->prefix.'required']) {
            if($input == $opts[$this->prefix.'default-value']) {
                if($input) {
                    if(!empty($opts[$this->prefix.'text'])) {
                        $output .= sprintf(__('You must uncheck "%s".','tdomf'),$opts[$this->prefix.'text']);
                    } else {
                        $output .= __('You must uncheck the box!','tdomf');
                    }
                } else {
                    if(!empty($opts[$this->prefix.'text'])) {
                        $output .= sprintf(__('You must check "%s".','tdomf'),$opts[$this->prefix.'text']);
                    } else {
                        $output .= __('You must check the box!','tdomf');
                    }     
                }
            }
        }
        
        return $output;
    }
    
    function post($args,$opts,$original_field_name=false)
    {
        $output = false;
        /*if(empty($output)) {*/
            if(isset($args[$this->prefix.'cb'])) {
                $output = true;
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $output = true;
            } 
        /*}*/
        return $output;
    }
    
    function control($options,$form_id,$show=false,$hide=false,$save=true)
    {
        if((is_array($show) && empty($show))) {
            # nothing to do if show list is empty
            return array();
        }

        // prepare options!
        if($save) {
            $retOptions = array();
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'required',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'default-value',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'text',$show,$hide);
            
            $options = wp_parse_args($retOptions, $options);
        }
        
        // Display control panel for this textfield
        
        if($this->useOpts($this->prefix.'required',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>required" style="line-height:35px;"><?php _e("Required (the user must select the opposite of the default setting)","tdomf"); ?></label> 
<input type="checkbox" name="<?php echo $this->prefix; ?>required" id="<?php echo $this->prefix; ?>required" <?php if($options[$this->prefix.'required']) echo "checked"; ?> >
<br/>
  <?php } 
        if($this->useOpts($this->prefix.'default-value',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>default-value" style="line-height:35px;"><?php _e("Default Setting","tdomf"); ?></label> 
<input type="checkbox" name="<?php echo $this->prefix; ?>default-value" id="<?php echo $this->prefix; ?>default-value" <?php if($options[$this->prefix.'default-value']) echo "checked"; ?> >
<br/>
  <?php } 
        if($this->useOpts($this->prefix.'text',$show,$hide)) { ?>
            <label for="<?php echo $this->prefix; ?>title" style="line-height:35px;"><?php _e("Text:","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>text" id="<?php echo $this->prefix; ?>text" value="<?php echo htmlentities($options[$this->prefix.'text'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }

        return $options;
    }
    
}

/** 
* Utility class for any widget using a Select Group
* 
* @author Mark Cunningham <tdomf@thedeadone.net> 
* @version 1.0
* @since 0.13.6
* @access public 
* @copyright Mark Cunningham
* 
*/ 
class TDOMF_WidgetFieldSelect extends TDOMF_WidgetField {
    
    # http://dropdown-check-list.googlecode.com/svn/trunk/demo.html (dropdown select list)
    
     function TDOMF_WidgetFieldCheckBox($prefix)
    {
        parent::TDOMF_WidgetField($prefix);
    }
    
   function getId()
    {
        return $this->prefix.'s';
    }
    
    function getOptions($opts) {
        $defs = array( $this->prefix.'size' => 1,
                       $this->prefix.'required' => false,
                       $this->prefix.'default-selected' => array(),
                       $this->prefix.'values' => array(), # two dimensional array: option-text value
                       $this->prefix.'multiple-selection' => false,
                       $this->prefix.'title' => '');
        $opts = wp_parse_args($opts, $defs);
        return $opts;
    }
    
    function form($args,$opts)
    {

        // settings
        
        $select_defaults = $opts[$this->prefix.'default-selected'];
        if(isset($args[$this->prefix.'s'])){
            $select_defaults = $args[$this->prefix.'s'];
        }
        if(!is_array($select_defaults)) {
            $select_defaults = array( $select_defaults );
        }

        $output = '';
        
        // title
        
        if(!empty($opts[$this->prefix.'title'])) {
           
            $output .= '<label for="'.$this->prefix.'s"';
            if($opts[$this->prefix.'required']) {
                $output .=  ' class="required"';
            } 
            $output .= '>';

            if($options['required']) {
                $output .= sprintf(__('%s (Required)','tdomf'),$opts[$this->prefix.'title']);
            } else {
                $output .= $opts[$this->prefix.'title'];
            }
            $output .= '</label><br/>'."\n";
        }    
        
        // select
        
        if($opts[$this->prefix.'multiple-selection']) {
            $output .= '<select name="'.$this->prefix.'s[]" id="'.$this->prefix.'s[]" size="'.$opts['size'].'" multiple="multiple" >';
        } else {
            $output .= '<select name="'.$this->prefix.'s" id="'.$this->prefix.'s" size="'.$opts['size'].'" >';
        }

        if(!empty($opts[$this->prefix.'values'])) {
            foreach($opts[$this->prefix.'values'] as $value => $text) {
                if(trim($text) != "" && trim($value) != "") {
                    $output .= '<option value="'.$value.'"';
                    if(in_array($value,$select_defaults)) {
                        $output .= ' selected=\'selected\'';
                    }
                    $output .= '> '.$text.'</option>'."\n"; 
                }
            }
        }
        
        $output .= "</select>";
    
        return $output;
    }
    
    function formHack($args,$opts)
    {
        // defaults
        
        $output = "\t\t".'<?php $temp_values = array(';
        if(is_array($opts[$this->prefix.'default-selected'])) {
            $first = true;
            foreach($opts[$this->prefix.'default-selected'] as $value) {
                $value = str_replace('"','\\"',$value);
                if($first) { $first = false; }
                else { $output .= ','; }
                $output .= '"'.$value.'"';
            }
        }
        $output .= ');'."\n";

        // current values
           
        $output .= "\t\t".'if(isset($post_args["'.$this->prefix.'s"])){'."\n";
        $output .= "\t\t\t".'$temp_values = $post_args["'.$this->prefix.'s"];'."\n\t\t".'}'."\n";
        $output .= "\t\t".'if(!is_array($temp_values)) {'."\n";
        $output .= "\t\t\t".'$temp_values = array( $temp_values );'."\n\t\t".'}'."\n";
        
        $output .= "\t\t".'?>'."\n";
        
        // title
        
        if(!empty($opts[$this->prefix.'title'])) {
           
            $output .= '<label for="'.$this->prefix.'s"';
            if($opts[$this->prefix.'required']) {
                $output .=  ' class="required"';
            } 
            $output .= '>';

            if($options['required']) {
                $output .= sprintf(__('%s (Required)','tdomf'),$opts[$this->prefix.'title']);
            } else {
                $output .= $opts[$this->prefix.'title'];
            }
            $output .= '</label><br/>'."\n";
        }    
        
        // select
        
        if($opts[$this->prefix.'multiple-selection']) {
            $output .= '<select name="'.$this->prefix.'s[]" id="'.$this->prefix.'s[]" size="'.$opts['size'].'" multiple="multiple" >';
        } else {
            $output .= '<select name="'.$this->prefix.'s" id="'.$this->prefix.'s" size="'.$opts['size'].'" >';
        }

        if(!empty($opts[$this->prefix.'values'])) {
            foreach($opts[$this->prefix.'values'] as $value => $text) {
                if(trim($text) != "" && trim($value) != "") {
                    $output .= '<option value="'.$value.'"';
                    $output .= '<?php if(in_array("'.$value.'",$temp_values)) { ?>';
                        $output .= ' selected=\'selected\'';
                    $output .= '<?php } ?>';
                    $output .= '> '.$text.'</option>'."\n"; 
                }
            }
        }
        
        $output .= "</select>";
    
        return $output;
    }
    
    function preview($args,$opts,$original_field_name=false)
    {
        $output = '';
  
        $value = false;
        if(isset($args[$this->prefix.'s'])){
            $value = $args[$this->prefix.'s'];
        } else if(isset($args[$original_field_name])) {
            $value = $args[$original_field_name];
        } else {
             tdomf_log_message("Select: can't get any input for preview!",TDOMF_LOG_ERROR);
        }
        
        if($value) {
            
            // prepare value
            
            if(is_array($value)) {
                foreach($value as $v) {
                    if(isset($opts[$this->prefix.'values'][$v])) {
                        if(!empty($output)){ $output .= ", "; }
                        $output .= $opts[$this->prefix.'values'][$v];
                    }
                }
            } else if(isset($opts[$this->prefix.'values'][$value])){
                $output = $opts[$this->prefix.'values'][$value];
            }
            
            // format output
            
            if(!empty($output)) {
                if(!empty($opts[$this->prefix.'title'])) {
                    $output = "<b>".sprintf(__("%s: ","tdomf"),$opts[$this->prefix.'title'])."</b>".$output;
                } 
            } else {
                tdomf_log_message("Select: values are bad for preview!",TDOMF_LOG_ERROR);
            }
        }
        
        return $output;
    }
    
    function previewHack($args,$opts,$original_field_name=false)
    {
        $output  = "\t".'<?php $values = array('."\n";
        foreach($opts[$this->prefix.'values'] as $key => $v) {
            $output .= "\t\t".'\''.$key.'\' => \''.htmlentities($v,ENT_QUOTES,get_bloginfo('charset')).'\','."\n";
        }
        $output .= "\t".');'."\n";
        $output .= "\t".'$temp_values = "";'."\n";
        $output .= "\t".'if(isset($post_args["'.$this->prefix.'s"])){'."\n";
        $output .= "\t".'$temp_value = $post_args["'.$this->prefix.'s"]; '."\n";
        $output .= "\t".'if(is_array($temp_value)) {'."\n";
        $output .= "\t\t".'$first = true;'."\n";
        $output .= "\t\t".'foreach($temp_value as $v) {'."\n";
        $output .= "\t\t\t".'if(isset($values[$v])) {'."\n";
        $output .= "\t\t\t\t".'if($first){ $first = false; }'."\n";
        $output .= "\t\t\t\t".'else { $temp_values .= ", "; }'."\n";
        $output .= "\t\t\t\t".'$temp_values .= $values[$v];'."\n";
        $output .= "\t\t\t".'}'."\n";
        $output .= "\t\t".'}'."\n";
        $output .= "\t".'} else if(isset($values[$temp_value])){'."\n";
        $output .= "\t\t".'$temp_values = $values[$temp_value];'."\n";
        $output .= "\t".'}'."\n";
        
                    
        $output .= "\t".'if(!empty($temp_values)) { ?>'."\n";
        if(!empty($opts[$this->prefix.'title'])) {
            $output .= '<b>'.sprintf(__('%s: ','tdomf'),$opts[$this->prefix.'title']).'</b><?php echo $temp_values; ?>'."\n";
        } else {
            $output .= '<?php echo $temp_values; ?>'."\n";            
        }
        $output .= "\t".'<?php } ?>'."\n";
        
        return $output;
    }
    
    function validate($args,$opts,$preview=false,$original_field_name=false) {
        
        $output = "";
        $input = false;

        // grab the input because we're going to test it
        
        $input = false;
        if(empty($output)) {
            if(isset($args[$this->prefix.'s'])) {
                $input = $args[$this->prefix.'s'];
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $input = $args[$this->prefix.'s'];
            } 
        }
        
        // required check
        
        if($input != false && $opts[$this->prefix.'required']) {
            
            $test = $input;
            if(!is_array($input)) {
                 $test = array($input);
            } 
            
            if($opts[$this->prefix.'multiple-selection'] && count($test) < 1) {
                if(!empty($opts[$this->prefix.'title'])) {
                    $output .= sprintf(__("You must select at least one option from %s.","tdomf"),$opts[$this->prefix.'title']);
                } else {
                    $output .= __("You must select at least one options.","tdomf");
                }
            } else if($opts[$this->prefix.'multiple-selection'] && count($opts[$this->prefix.'multiple-selection']) == count($test)){
                $diff = array_diff($opts[$this->prefix.'default-selected'],$test);
                if(empty($test) || empty($diff)) {
                    if(!empty($opts[$this->prefix.'title'])) {
                        $output .= sprintf(__("You must select options from %s.","tdomf"),$opts[$this->prefix.'title']);
                    } else {
                        $output .= __("You must select some options.","tdomf").var_export($diff,true);
                    }
                }
            }
        }
        
        return $output;
    }
    
    function post($args,$opts,$original_field_name=false)
    {
        $output = false;
        if(empty($output)) {
            if(isset($args[$this->prefix.'s'])) {
                $output = $args[$this->prefix.'s'];
            } else if($original_field_name != false && isset($args[$original_field_name])) {
                $output = $args[$original_field_name];
            } 
        }
        return $output;
    }
    
    /** 
     * Returns the "text" values instead of the raw options selected
     * 
     * @return Boolean 
     * @access public 
     */     
    function postText($args,$opts,$original_field_name=false)
    {
        $output = false;
        $input_values = $this->post($args,$opts,$original_field_name);
        if(is_array($input_values) && !empty($input_values)) {
           $output = "";
           foreach($input_values as $in) {
               if(isset($opts[$this->prefix.'values'][$in])) {
                   if(!empty($output)) { $output .= ', '; }
                   $output .= $opts[$this->prefix.'values'][$in];
               }
           }
        } else if($input_values != false) {
            if(isset($opts[$this->prefix.'values'][$input_values])) {
                $output = $opts[$this->prefix.'values'][$input_values];
            }
        }
        return $output;
    }
    
  function control($options,$form_id,$show=false,$hide=false,$save=true)
    {
        if((is_array($show) && empty($show))) {
            # nothing to do if show list is empty
            return array();
        }

        // prepare options!
        
        if($save) {
            $retOptions = array();
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'required',$show,$hide);
            $retOptions = $this->updateOptsBoolean($retOptions,$this->prefix.'multiple-selection',$show,$hide);
            $retOptions = $this->updateOptsString($retOptions,$this->prefix.'title',$show,$hide);
            $retOptions = $this->updateOptsInt($retOptions,$this->prefix.'size',$show,$hide);
        
            // default-selected and values are slightly non-standard
            
            if($this->useOpts($this->prefix.'default-selected',$show,$hide) && isset($_POST[$this->prefix.'default-selected'])) {
                $default_selected = $_POST[$this->prefix.'default-selected'];
                if(!empty($_POST[$this->prefix.'default-selected'])) {
                    $default_selected = explode(';',$default_selected);
                }
                $retOptions[$this->prefix.'default-selected'] = $default_selected;
            }
            
            if($this->useOpts($this->prefix.'values',$show,$hide) && isset($_POST[$this->prefix.'values'])) {
                $values = $_POST[$this->prefix.'values'];
                $values_array = array();
                if(!empty($_POST[$this->prefix.'values'])) {
                    $values = explode(';',$values);
                    foreach($values as $v) {
                        if(!empty($v)) {
                            list($text,$value) = explode(':',$v);
                            if(!empty($value) && !empty($text)) {
                                $values_array[$value] = $text;
                            }
                        }
                    }
                }
                $retOptions[$this->prefix.'values'] = $values_array;
            }
            
            $options = wp_parse_args($retOptions, $options);
        }
        
        // Display control panel for this textfield
        
        if($this->useOpts($this->prefix.'required',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>required" style="line-height:35px;"><?php _e("Required","tdomf"); ?></label> 
<input type="checkbox" name="<?php echo $this->prefix; ?>required" id="<?php echo $this->prefix; ?>required" <?php if($options[$this->prefix.'required']) echo "checked"; ?> >
<br/>
  <?php } 
        if($this->useOpts($this->prefix.'size',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>size" style="line-height:35px;"><?php _e("Number of items to display","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>size" id="<?php echo $this->prefix; ?>size" value="<?php echo htmlentities($options[$this->prefix.'size'],ENT_QUOTES,get_bloginfo('charset')); ?>" size="3" />
<br/>
  <?php } 
        if($this->useOpts($this->prefix.'multiple-selection',$show,$hide)) { ?>
<label for="<?php echo $this->prefix; ?>multiple-selection" style="line-height:35px;"><?php _e("Allow multiple selection","tdomf"); ?></label> 
<input type="checkbox" name="<?php echo $this->prefix; ?>multiple-selection" id="<?php echo $this->prefix; ?>multiple-selection" <?php if($options[$this->prefix.'multiple-selection']) echo "checked"; ?> >
<br/>
  <?php } 
        if($this->useOpts($this->prefix.'title',$show,$hide)) { ?>
            <label for="<?php echo $this->prefix; ?>title" style="line-height:35px;"><?php _e("Title:","tdomf"); ?></label>
<input type="textfield" name="<?php echo $this->prefix; ?>title" id="<?php echo $this->prefix; ?>title" value="<?php echo htmlentities($options[$this->prefix.'title'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/>
  <?php }

        if($this->useOpts($this->prefix.'values',$show,$hide) || $this->useOpts($this->prefix.'default-selected',$show,$hide)) { 

            if($this->useOpts($this->prefix.'default-selected',$show,$hide)) {
                $starting_defaults_text = __('Nothing selected','tdomf');            
                $starting_defaults = '';
                if(is_array($options[$this->prefix.'default-selected']) && !empty($options[$this->prefix.'default-selected'])) {
                    $starting_defaults = htmlentities(implode(';',$options[$this->prefix.'default-selected']),ENT_QUOTES,get_bloginfo('charset'));
                    $starting_defaults_text = '';
                    $first = true;
                    foreach($options[$this->prefix.'default-selected'] as $v) {
                        if(isset($options[$this->prefix.'values'][$v])) {
                            if($first) { $first = false; }
                            else { $starting_defaults_text .= ', '; }
                            $starting_defaults_text .= $options[$this->prefix.'values'][$v];
                        }
                    }
                }
            }
            
            $starting_values = '';
            $starting_values_options = '';
            if(is_array($options[$this->prefix.'values']) && !empty($options[$this->prefix.'values'])) {
                foreach($options[$this->prefix.'values'] as $v => $t) {
                    $starting_values .= $t . ':' . $v . ';';
                    $starting_values_options .= "  <option value='$v'>$t\n";
                }
                $starting_values = htmlentities($starting_values,ENT_QUOTES,get_bloginfo('charset'));
            }
            
            ?>
            
 <!-- Javascript taken (and then hacked) from http://www.mredkj.com/tutorials/tutorial006.html -->
            
<script type="text/javascript">
//<![CDATA[
<?php if($this->useOpts($this->prefix.'values',$show,$hide)) { ?> 
function <?php echo $this->prepJSCode($this->prefix); ?>appendToSelectList() {
    var theSel = document.getElementById("<?php echo $this->prefix; ?>s-list");
    var newText = document.getElementById("<?php echo $this->prefix; ?>s-name").value;
    var newValue = document.getElementById("<?php echo $this->prefix; ?>s-value").value;
    if(newText == "" || newValue == "") {
        alert("<?php _e("You must specify a value for Item name and Item value", "tdomf"); ?>");
        return;
    }
    var settingString = "";
    if (theSel.length == 0) {
        var newOpt1 = new Option(newText, newValue, true, true);
        theSel.options[0] = newOpt1;
        theSel.selectedIndex = 0;
        settingString = settingString + newText + ":" + newValue + ";";
    } else {
        var selText = new Array();
        var selValues = new Array();
        var i;
        for(i=0; i<theSel.length; i++) {
            selText[i] = theSel.options[i].text;
            selValues[i] = theSel.options[i].value;
            if(theSel.options[i].value == newValue) {
                alert("<?php _e("That value already exists!", "tdomf"); ?>");
                return;
            }
        }
        for(i=0; i<theSel.length; i++)
        {
            var newOpt = new Option(selText[i], selValues[i], false, false);
            theSel.options[i] = newOpt;
            settingString = settingString + selText[i] + ":" + selValues[i] + ";";
        }
        //var newOpt2 = new Option(newText, newValue, true, false);
        var newOpt2 = new Option(newText, newValue, false, false);
        theSel.options[i] = newOpt2;
        theSel.selectedIndex = -1;
        settingString = settingString + newText + ":" + newValue + ";";
        theSel.rows = theSel.length;
    }
    document.getElementById("<?php echo $this->prefix; ?>values").value = settingString;
}
function <?php echo $this->prepJSCode($this->prefix); ?>removeFromSelectList()
{
    var theSel = document.getElementById("<?php echo $this->prefix; ?>s-list");
    var selIndex = theSel.selectedIndex;
    if (selIndex != -1) {
        theSel.options[selIndex] = null;
        var settingString = "";
        for(i=0; i<theSel.length; i++) {
            settingString = settingString + theSel.options[i].text + ":" + theSel.options[i].value + ";";
        }
        document.getElementById("<?php echo $this->prefix; ?>values").value = settingString;
    } else {
        alert("<?php _e("Please select item to remove from the list!","tdomf"); ?>");
    }
}
<?php } ?>
<?php if($this->useOpts($this->prefix.'default-selected',$show,$hide)) { ?>
function <?php echo $this->prepJSCode($this->prefix); ?>makeDefaultSelectList()
{
    var theSel = document.getElementById("<?php echo $this->prefix; ?>s-list");
    var settingString = "";
    var messageString = "<?php _e("Default selected options: ","tdomf"); ?>";
    for(i=0; i<theSel.length; i++) {
        if(theSel[i].selected) {
            settingString = settingString + theSel.options[i].value + ";";
            messageString = messageString + theSel.options[i].text + ", ";
        }
    }
    document.getElementById("<?php echo $this->prefix; ?>default-selected").value = settingString;
    document.getElementById("<?php echo $this->prefix; ?>s-defs-msg").innerHTML = messageString;
}
<?php } ?>
//]]>
</script>

<!-- encoded defaults and values @todo use jQuery serialize? -->

<?php if($this->useOpts($this->prefix.'default-selected',$show,$hide)) { ?>
<input type="hidden" name="<?php echo $this->prefix; ?>default-selected" id="<?php echo $this->prefix; ?>default-selected" value="<?php echo $starting_defaults; ?>" />
<?php } ?>
<?php if($this->useOpts($this->prefix.'values',$show,$hide)) { ?>
<input type="hidden" name="<?php echo $this->prefix; ?>values" id="<?php echo $this->prefix; ?>values" value="<?php echo $starting_values; ?>" />
<?php } ?>

<!-- list operations -->

<?php if($this->useOpts($this->prefix.'values',$show,$hide)) { ?>
<input type="button" value="<?php _e("Remove","tdomf"); ?>" onclick="<?php echo $this->prepJSCode($this->prefix); ?>removeFromSelectList();" />
<?php } ?>
<?php if($this->useOpts($this->prefix.'default-selected',$show,$hide)) { ?>
<input type="button" value="<?php _e("Make Current Selection Default","tdomf"); ?>" onclick="<?php echo $this->prepJSCode($this->prefix); ?>makeDefaultSelectList();" />
<?php } ?>
  
<br/><br/>

<?php if($this->useOpts($this->prefix.'default-selected',$show,$hide)) { ?>
<!-- default selection -->

<div id="<?php echo $this->prefix; ?>s-defs-msg" >
  <?php printf(__("Default selected options: %s","tdomf"),$starting_defaults_text); ?>
</div>
<?php } ?>  

<!-- the list (on the left) -->

<?php if($this->useOpts($this->prefix.'values',$show,$hide)) { ?>
    <div style="float:left;">
<?php } ?>
<select name="<?php echo $this->prefix; ?>s-list" id="<?php echo $this->prefix; ?>s-list" size="10" multiple="multiple" style="height:120px;width:200px;">
    <?php echo $starting_values_options; ?>
    </select>
<br/><br/>
<?php if($this->useOpts($this->prefix.'values',$show,$hide)) { ?>
</div>
<?php } ?>

<?php if($this->useOpts($this->prefix.'values',$show,$hide)) { ?>
<!-- the new items on the right -->

<div style="float:right;">
  <label for="<?php echo $this->prefix; ?>s-name">
  <?php _e("Name/Text of Item","tdomf"); ?></label><br/>
  <input type="text" size="30" name="<?php echo $this->prefix; ?>s-name" id="<?php echo $this->prefix; ?>s-name" ?>
  <br/><br/>
  
  <label for="<?php echo $this->prefix; ?>s-value">
  <?php _e("Value of Item","tdomf"); ?></label><br/>
  <input type="text" size="30" name="<?php echo $this->prefix; ?>s-value" id="<?php echo $this->prefix; ?>s-value" ?>
  <br/><br/>
  
  <input type="button" value="<?php _e("Append Item","tdomf"); ?>" onclick="<?php echo $this->prepJSCode($this->prefix); ?>appendToSelectList();" />
</div>
<?php } ?>

        <?php } 
  
        return $options;
    }
    
}

# @todo RadioGroup
# @todo CheckBoxGroup

?>