<?php

//////////////////////////
// Process Form Request //
//////////////////////////

// Load up Wordpress
//
$wp_load = realpath("../../../wp-load.php");
if(!file_exists($wp_load)) {
  $wp_config = realpath("../../../wp-config.php");
  if (!file_exists($wp_config)) {
      exit("Can't find wp-config.php or wp-load.php");
  } else {
      require_once($wp_config);
  }
} else {
  require_once($wp_load);
}
global $wpdb, $tdomf_form_widgets_validate, $tdomf_form_widgets_preview;

// enable all PHP errors
//
if(get_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES) && !get_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES)) {
  error_reporting(E_ALL);
}

// loading text domain for language translation
//
load_plugin_textdomain('tdomf',PLUGINDIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER);

// Form id
//
if(!isset($_POST['tdomf_form_id'])) {
  tdomf_log_message("tdomf-form-post: No Form ID set!",TDOMF_LOG_BAD);
  exit(__("TDOMF: No Form id!","tdomf"));
}
$form_id = intval($_POST['tdomf_form_id']);
if(!tdomf_form_exists($form_id)){
  tdomf_log_message("tdomf-form-post: Bad form id %d!",TDOMF_LOG_BAD);
  exit(__("TDOMF: Bad Form Id","tdomf"));
}

// Submit or Edit?
//
$is_edit = tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id);

// Get Form Data for verficiation check
//
$form_data = tdomf_get_form_data($form_id);

// Get Post ID if there is one
//
$post_id = false;
if($is_edit) {
    if(isset($form_data['tdomf_post_id'])) {
        $post_id = $form_data['tdomf_post_id'];
    } else if(isset($_REQUEST['tdomf_post_id'])) {
        $post_id = $_REQUEST['tdomf_post_id'];
    } else {
        tdomf_log_message("tdomf-form-post: Edit form %d but no post id!",TDOMF_LOG_BAD);
        exit(__("TDOMF: Missing Post Id","tdomf"));
    }
}

// Security Check
//
$tdomf_verify = get_option(TDOMF_OPTION_VERIFICATION_METHOD);
if($tdomf_verify == false || $tdomf_verify == 'default') {
  if(!isset($form_data['tdomf_key_'.$form_id]) || $form_data['tdomf_key_'.$form_id] != $_POST['tdomf_key_'.$form_id]) {
     if(!isset($form_data) || !isset($form_data['tdomf_key_'.$form_id]) || trim($form_data['tdomf_key_'.$form_id]) == "") {
       tdomf_log_message('Key is missing from $form_data: contents of $form_data:<pre>'.var_export($form_data,true)."</pre>",TDOMF_LOG_BAD);
     }
     $session_key = $form_data['tdomf_key_'.$form_id];
     $post_key = $_POST['tdomf_key_'.$form_id];
     $ip = $_SERVER['REMOTE_ADDR'];
     tdomf_log_message("Form ($form_id) submitted with bad key (session = $session_key, post = $post_key) from $ip !",TDOMF_LOG_BAD);
     unset($form_data['tdomf_key_'.$form_id]);
     tdomf_save_form_data($form_id,$form_data);
     exit(__("TDOMF: Bad data submitted. Please return to the previous page and reload it. Then try submitting your post again.","tdomf"));
  }
  unset($form_data['tdomf_key_'.$form_id]);
} else if($tdomf_verify == 'wordpress_nonce') {
  if(!wp_verify_nonce($_POST['tdomf_key_'.$form_id],'tdomf-form-'.$form_id)) {
    $post_key = $_POST['tdomf_key_'.$form_id];
    $ip = $_SERVER['REMOTE_ADDR'];    
    tdomf_log_message("Form ($form_id) submitted with bad nonce key (post = $post_key) from $ip !",TDOMF_LOG_BAD);
    exit(__("TDOMF: Bad data submitted. Please return to the previous page and reload it. Then try submitting your post again.","tdomf"));
  }
}

function tdomf_stripslashes_deep($array) {
    #if (get_magic_quotes_gpc()) { <- requried in wp 2.8.x even with magic quotes off
        if(is_array($array)) {
            return array_map('tdomf_stripslashes_deep', $array);
        } else {
            // check if the string has new lines!
            if(strpos($array,"\n") !== false) {
                $array = explode("\n",$array);                    
                $array = tdomf_stripslashes_deep($array);
                $array = join("\n",$array);
            } else {
                $array = stripslashes($array);
                return str_replace("\\'","'",$array);
            }
        }
    #}
    return $array;
}

function tdomf_fixslashesargs() {
   $_COOKIE = stripslashes_deep($_COOKIE);
   $_POST = tdomf_stripslashes_deep($_POST);
   $_REQUEST = tdomf_stripslashes_deep($_REQUEST);
}

// Double check user permissions
//
$message = tdomf_check_permissions_form($form_id,$post_id);

// Remove magic quote slashes and additionally ones Wordpress "cleverly" adds
tdomf_fixslashesargs();

// Now either generate a preview or create a post
//
$save_post_info = FALSE;
$hide_form = true;
$publish = false;
if($message == NULL) {
  
  if($is_edit) {
      $form_tag = $form_id.'_'.$post_id;
  } else {
      $form_tag = $form_id;
  }
  
  if(isset($_POST['tdomf_form'.$form_tag.'_send'])) {

    tdomf_log_message("Someone is attempting to submit something");

    $message = tdomf_validate_form($_POST,false);
    if($message == NULL) {
      $args = $_POST;
      $args['ip'] = $_SERVER['REMOTE_ADDR'];
      $retVal = tdomf_create_post($args);
      // If retVal is an int it's a post id or an edit id
      $message =  "<div class=\"tdomf_form_message\" id=\"tdomf_form".$form_tag."_message\" name=\"tdomf_form".$form_tag."_message\">";
      $publish = false;
      if(is_int($retVal)) {
        if($is_edit) {
            $edit_id = $retVal;
            $edit = tdomf_get_edit($edit_id);
            // @todo could probably test if $edit is real or not before proceeding
            $post_id = $edit->post_id;
            if($edit->state == 'approved') {
                $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_PUBLISH,$form_id,false,$post_id);
                $publish = true;
            } else if($edit->state == 'spam') {
                $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_SPAM,$form_id,false,$post_id);
            } else { // unapproved
                $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_MOD,$form_id,false,$post_id);
            }
        } else {
            $post_id = $retVal;
            if(get_post_status($post_id) == 'publish') {
              $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_PUBLISH,$form_id,false,$post_id);
              $publish = true;
            } else if(get_post_status($post_id) == 'future') {
              $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_FUTURE,$form_id,false,$post_id);
            } else if(get_post_meta($post_id, TDOMF_KEY_SPAM)) {
              $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_SPAM,$form_id,false,$post_id);
            } else {
              $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_MOD,$form_id,false,$post_id);
            }
        }
      // If retVal is a string, something went wrong!
      } else {
        $message .= tdomf_get_message_instance(TDOMF_OPTION_MSG_SUB_ERROR,$form_id,false,false,$retVal);
        $save_post_info = TRUE;
        $hide_form = FALSE;
        #tdomf_fixslashesargs();
      }
      $message .= "</div>";
    } else {
      $message =  "<div class=\"tdomf_form_message\" id=\"tdomf_form".$form_tag."_message\" name=\"tdomf_form".$form_tag."_message\">".$message."</div>";
      $save_post_info = TRUE;
      $hide_form = false;
      #tdomf_fixslashesargs();
    }
  } else if(isset($_POST['tdomf_form'.$form_tag.'_preview'])) {
       #tdomf_fixslashesargs();
       $save_post_info = TRUE;
       $hide_form = false;
	   $message = tdomf_validate_form($_POST,true);
	   if($message == NULL) {
           $message  = "<div class=\"tdomf_form_preview\" id=\"tdomf_form".$form_tag."_message\" name=\"tdomf_form".$form_tag."_message\">";
           $message .= tdomf_preview_form($_POST);
           $message .= "</div>";
	   } else {
           $message =  "<div class=\"tdomf_form_message\" id=\"tdomf_form".$form_tag."_message\" name=\"tdomf_form".$form_tag."_message\">".$message."</div>";
       }
       
       // allows the final check to work when editing
       //
       unset($post_id);
       
  } else if(isset($_POST['tdomf_form'.$form_tag.'_clear'])) {
    $message = NULL;
    $save_post_info = false;
    $hide_form = false;
  }
}

// update form data *after* widgets have done their work!
//
$form_data = tdomf_get_form_data($form_id);

if(!isset($post_id) || !$publish || !tdomf_get_option_form(TDOMF_OPTION_REDIRECT,$form_id)) {
  // Go back to form with args
  //
  $redirect_url = $_POST['redirect'];
  
  // Hack: set your own URL here if you wish to redirect to a different URL
  // Future versions of TDOMF will provide this as an option.
  //
  #if($publish || isset($post_id)) { $redirect_url = 'http://thedeadone.net/download/tdo-mini-forms-wordpress-plugin/'; }

  if($save_post_info) {
    $args = $_POST;
  } else {
    $args = array();
  }
  if($hide_form) {
     $args['tdomf_no_form_'.$form_id] = true;
  }
  $args['tdomf_post_message_'.$form_id] = $message;
  $form_data['tdomf_form_post_'.$form_id] = $args;
} else {
  unset($form_data['tdomf_form_post_'.$form_id]);
  $redirect_url = get_permalink($post_id);
  
  // Hack: set your own URL here if you wish to redirect to a different URL
  // Future versions of TDOMF will provide this as an option.
  //
  #$redirect_url = 'http://thedeadone.net/download/tdo-mini-forms-wordpress-plugin/';
}

// save it!
//
tdomf_save_form_data($form_id,$form_data);

header("Location: $redirect_url");
exit;
?>