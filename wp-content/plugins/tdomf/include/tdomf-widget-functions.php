<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

////////////////////
// Manage Widgets //
////////////////////

function tdomf_starts_with($haystack, $needle){
    return strpos($haystack, $needle) === 0;
}

function tdomf_ends_with($haystack, $needle){
    return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
}

// Load widgets from widget directory
//
function tdomf_load_widgets() {
   if(file_exists(TDOMF_WIDGET_PATH)) {
       #tdomf_log_message_extra("Looking in ".TDOMF_WIDGET_PATH." for widgets...");
	   if ($handle = opendir(TDOMF_WIDGET_PATH)) {
		  while (false !== ($file = readdir($handle))) {
		     if(preg_match('/.*\\.php$/',$file)) {
			 	#tdomf_log_message_extra("Loading widget $file...");
			 	require_once(TDOMF_WIDGET_PATH.$file);
			 }
		  }
	   } else {
		  tdomf_log_message("Could not open directory ".TDOMF_WIDGET_PATH."!",TDOMF_LOG_ERROR);
	   }
   } else {
      tdomf_log_message("Could not find ".TDOMF_WIDGET_PATH."!",TDOMF_LOG_ERROR);
   }
}

// Return the widget order
//
function tdomf_get_widget_order($form_id = 1) {
  $widget_order = tdomf_get_option_form(TDOMF_OPTION_FORM_ORDER,$form_id);
  if($widget_order == false) {
  	return tdomf_get_form_widget_default_order($form_id);
  }
  return $widget_order;
}

// Is a preview avaliable
// Currently selected widgets must provide a preview and preview must be enabled.
//
function tdomf_widget_is_preview_avaliable($form_id = 1) {
   global $tdomf_form_widgets_preview;
   if(!tdomf_get_option_form(TDOMF_OPTION_PREVIEW,$form_id)) {
   	  return false;
   }
   $widget_order = tdomf_get_widget_order($form_id);
   foreach($widget_order as $id) {
      if(isset($tdomf_form_widgets_preview[$id])) {
         return true;
      }
   }
   return false;
}

// AJAX allowed
//
function tdomf_widget_is_ajax_avaliable($form_id = 1) {
   global $tdomf_form_widgets_preview, $tdomf_form_widgets_validate, $tdomf_form_widgets_post;
   if(!tdomf_get_option_form(TDOMF_OPTION_AJAX,$form_id)) {
   	  return false;
   }
   // deprecated (used to check widgets)
   return true;
}

// All Widgets are in this array
//
$tdomf_form_widgets = array();
//
// Configuration panels for Widgets
//
$tdomf_form_widgets_control = array();
//
// Preview post for Widget
//
$tdomf_form_widgets_preview = array();
//
// Form validation for Widgets
//
$tdomf_form_widgets_validate = array();
//
// Post actions for Widgets
//
$tdomf_form_widgets_post = array();
//
// Admin email notifications for Widgets
//
$tdomf_form_widgets_adminemail = array();
//
// Hacked Widgets
//
$tdomf_form_widgets_hack = array();
//
// Hacked Preview Widgets
//
$tdomf_form_widgets_preview_hack = array();
//
// Admin warnings and errors
//
$tdomf_form_widgets_admin_errors = array();

// Filter list of widgets by mode (if a mode set for that widget)
//
function tdomf_filter_widgets($mode,$widgets = false) {
  global $tdomf_form_widgets;
  if(!is_array($widgets)) {
    $widgets = $tdomf_form_widgets;
  }
  $retWidgets = array();
  foreach($widgets as $id => $w) {
     if(!isset($w['modes']) || !is_array($w['modes']) || empty($w['modes']) ) {
       $retWidgets[$id] = $w;
     } else {
       $modes = $w['modes'];
       foreach($modes as $m) {
         if(strpos($mode,$m) !== false) {
           $retWidgets[$id] = $w;
           break;
         }
       }
     }
  }
  return $retWidgets;
}

// All Widgets need to register with this function
//
function tdomf_register_form_widget($id, $name, $callback, $modes = array()) {
   global $tdomf_form_widgets,$tdomf_form_widgets;
   $id = sanitize_title($id);
   if(isset($tdomf_form_widgets[$id])) {
      tdomf_log_message_extra("tdomf_register_form_widget: Widget $id already exists. Overwriting...");
   }
   #tdomf_log_message_extra("Loading Widget $id...");
   $tdomf_form_widgets[$id]['name'] = $name;
   $tdomf_form_widgets[$id]['cb'] = $callback;
   $tdomf_form_widgets[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets[$id]['modes'] = $modes;
}

// Widgets that require configuration must register with this function
//
function tdomf_register_form_widget_control($id, $name, $control_callback, $width = 360, $height = 130, $modes = array()) {
   global $tdomf_form_widgets_control,$tdomf_form_widgets;
   $id = sanitize_title($id);
   if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Control: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_control[$id])) {
         tdomf_log_message_extra("tdomf_register_form_widget_control: Widget $id already exists. Overwriting...");
   }
   #tdomf_log_message_extra("Loading Widget Control $id...");
   $tdomf_form_widgets_control[$id]['name'] = $name;
   $tdomf_form_widgets_control[$id]['cb'] = $control_callback;
   $tdomf_form_widgets_control[$id]['width'] = $width;
   $tdomf_form_widgets_control[$id]['height'] = $height;
   $tdomf_form_widgets_control[$id]['params'] = array_slice(func_get_args(), 6);
   $tdomf_form_widgets_control[$id]['modes'] = $modes;
}

// Widgets that provide a preview must register with this function
//
function tdomf_register_form_widget_preview($id, $name, $preview_callback, $modes = array()) {
   global $tdomf_form_widgets_preview,$tdomf_form_widgets;
   $id = sanitize_title($id);
	if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Preview: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_preview[$id])) {
      tdomf_log_message_extra("Preview widget $id already exists. Overwriting...");
   }
   #tdomf_log_message_extra("Loading Widget Preview $id...");
   $tdomf_form_widgets_preview[$id]['name'] = $name;
   $tdomf_form_widgets_preview[$id]['cb'] = $preview_callback;
   $tdomf_form_widgets_preview[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_preview[$id]['modes'] = $modes;
}

// Widgets that vaidate input *before* input
//
function tdomf_register_form_widget_validate($id, $name, $validate_callback, $modes = array()) {
   global $tdomf_form_widgets_validate,$tdomf_form_widgets;
   $id = sanitize_title($id);
	if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Validate: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_validate[$id])) {
      tdomf_log_message_extra("Widget $id already exists. Overwriting...");
   }
   #tdomf_log_message_extra("Loading Widget Validate $id...");
   $tdomf_form_widgets_validate[$id]['name'] = $name;
   $tdomf_form_widgets_validate[$id]['cb'] = $validate_callback;
   $tdomf_form_widgets_validate[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_validate[$id]['modes'] = $modes;
}

// Widgets that modify the post *after* submission 
//
function tdomf_register_form_widget_post($id, $name, $post_callback, $modes = array()) {
   global $tdomf_form_widgets_post,$tdomf_form_widgets;
   $id = sanitize_title($id);
	if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Post: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_post[$id])) {
      tdomf_log_message_extra("tdomf_register_form_widget_post: Widget $id already exists. Overwriting...");
   }
   #tdomf_log_message_extra("Loading Widget Post $id...");
   $tdomf_form_widgets_post[$id]['name'] = $name;
   $tdomf_form_widgets_post[$id]['cb'] = $post_callback;
   $tdomf_form_widgets_post[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_post[$id]['modes'] = $modes;
}

// Widgets that create info for the admin notification
//
function tdomf_register_form_widget_adminemail($id, $name, $post_callback, $modes = array()) {
   global $tdomf_form_widgets_adminemail,$tdomf_form_widgets;
   $id = sanitize_title($id);
	if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Admin Email: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_adminemail[$id])) {
      tdomf_log_message_extra("tdomf_register_form_widget_adminemail: Widget $id already exists. Overwriting...");
   }
   $tdomf_form_widgets_adminemail[$id]['name'] = $name;
   $tdomf_form_widgets_adminemail[$id]['cb'] = $post_callback;
   $tdomf_form_widgets_adminemail[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_adminemail[$id]['modes'] = $modes;
}

// Widgets that support the Form Hacker
//
function tdomf_register_form_widget_hack($id, $name, $hack_callback, $modes = array()) {
   global $tdomf_form_widgets_hack,$tdomf_form_widgets;
   $id = sanitize_title($id);
   if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Hack: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_hack[$id])) {
      tdomf_log_message_extra("tdomf_register_form_widget_hack: Widget $id already exists. Overwriting...");
   }
   $tdomf_form_widgets_hack[$id]['name'] = $name;
   $tdomf_form_widgets_hack[$id]['cb'] = $hack_callback;
   $tdomf_form_widgets_hack[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_hack[$id]['modes'] = $modes;
}

// Widgets that support the Form Hacker Preview
//
function tdomf_register_form_widget_preview_hack($id, $name, $preview_callback, $modes = array()) {
   global $tdomf_form_widgets_preview_hack,$tdomf_form_widgets;
   $id = sanitize_title($id);
	if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Preview Hack: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_preview_hack[$id])) {
      tdomf_log_message_extra("Preview Hack widget $id already exists. Overwriting...");
   }
   $tdomf_form_widgets_preview_hack[$id]['name'] = $name;
   $tdomf_form_widgets_preview_hack[$id]['cb'] = $preview_callback;
   $tdomf_form_widgets_preview_hack[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_preview_hack[$id]['modes'] = $modes;
}

// Widgets that support the admin warnings and errors
//
function tdomf_register_form_widget_admin_error($id, $name, $callback, $modes = array()) {
   global $tdomf_form_widgets_admin_errors,$tdomf_form_widgets;
   $id = sanitize_title($id);
	if(!isset($tdomf_form_widgets[$id])) {
   		 tdomf_log_message_extra("Admin Error: Widget $id has not be registered!...",TDOMF_LOG_ERROR);
   		 return;
   }
   if(isset($tdomf_form_widgets_admin_errors[$id])) {
      tdomf_log_message_extra("Admin Error widget $id already exists. Overwriting...");
   }
   $tdomf_form_widgets_admin_errors[$id]['name'] = $name;
   $tdomf_form_widgets_admin_errors[$id]['cb'] = $callback;
   $tdomf_form_widgets_admin_errors[$id]['params'] = array_slice(func_get_args(), 4);
   $tdomf_form_widgets_admin_errors[$id]['modes'] = $modes;
}

// Return the default widget order!
//
function tdomf_get_form_widget_default_order($form_id) {
   if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id)) {
       return array("who-am-i","content");
   } else {
       return array("who-am-i","content","notifyme");
   }
}

// Simple regex check to validate a URL
//
function tdomf_check_url($url) 
{ 
  return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url); 
} 

?>
