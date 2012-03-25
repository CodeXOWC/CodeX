<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

//////////////////////////////
// Code for Form generation //
//////////////////////////////

// TODO: Clear and/or reset button                                                    

function tdomf_do_spam_check($form_id,$post_id=false,$edit_id=false) {
  $tdomf_spam = get_option(TDOMF_OPTION_SPAM);
  if($tdomf_spam && tdomf_get_option_form(TDOMF_OPTION_SPAM_OVERWRITE,$form_id)) {
      tdomf_log_message("Form $form_id has spam specific options set");
      $tdomf_spam = tdomf_get_option_form(TDOMF_OPTION_SPAM,$form_id);
      $tdomf_nospam_user = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_USER,$form_id);
      $tdomf_nospam_author = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_AUTHOR,$form_id);
      $tdomf_nospam_trusted = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_TRUSTED,$form_id);
      $tdomf_nospam_publish = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_PUBLISH,$form_id);
  } else {
      $tdomf_nospam_user = get_option(TDOMF_OPTION_NOSPAM_USER);
      $tdomf_nospam_author = get_option(TDOMF_OPTION_NOSPAM_AUTHOR);
      $tdomf_nospam_trusted = get_option(TDOMF_OPTION_NOSPAM_TRUSTED);
      $tdomf_nospam_publish = get_option(TDOMF_OPTION_NOSPAM_PUBLISH);
  }
  if(!$tdomf_spam){ 
    tdomf_log_message("Form $form_id : spam check disabled");
    return false; 
  }
  if(is_user_logged_in()) {
      $current_user = wp_get_current_user();
      if($tdomf_nospam_user) {
          tdomf_log_message("Form $form_id : logged in users go spam-check free");
          return false;
      }
      if($tdomf_nospam_publish && current_user_can("publish_posts")) {
          tdomf_log_message("Form $form_id : users with publish rights go spam-check free");
          return false;
      }
     if($tdomf_nospam_trusted && get_usermeta($current_user->ID,TDOMF_KEY_STATUS) == TDOMF_USER_STATUS_TRUSTED) {
         tdomf_log_message("Form $form_id : trusted users go spam-check free");
         return false;
     }
     if($tdomf_nospam_author && $post_id && $edit_id) {
          $post = wp_get_single_post($post_id, ARRAY_A);
          // a valid $edit_id would imply that this is an edit form!
          if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_AUTHOR,$form_id)) {
            $submitter_user_id = get_post_meta($post_id, TDOMF_KEY_USER_ID, true);
            if(!empty($submitter_user_id) && $submitter_user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
                if($current_user->ID == $submitter_user_id) {
                    tdomf_log_message("Form $form_id : submitter of post $post_id go spam-check free");
                    return false;
                }
            } 
            // if the user is the *actual* author
            if($current_user->ID == $post['post_author']) {
                tdomf_log_message("Form $form_id : author of post $post_id go spam-check free");
                return false;
            }
        }
     }
  }
  tdomf_log_message("Form $form_id : do spam-check");
  return true;
}

function tdomf_preg_prepare($message) {
    // prep form: the $ and \\ are special operators in preg_replace replacement string
     $message = str_replace('$','\\$',$message);
     $message = str_replace('\\\\','\\\\\\\\',$message);
     return $message;
}

// Checks if current user/ip has permissions to post!
//
function tdomf_check_permissions_form($form_id = 1, $post_id = false, $check_pending_edits = true) {
   global $current_user, $wpdb, $wp_roles;

   get_currentuserinfo();
   
   // User Banned
   //
   if(is_user_logged_in()) {
       $user_status = get_usermeta($current_user->ID,TDOMF_KEY_STATUS);
       if($user_status == TDOMF_USER_STATUS_BANNED) {
          tdomf_log_message_extra("Banned user $current_user->user_name tried to submit a post!",TDOMF_LOG_ERROR);
          return tdomf_get_message_instance(TDOMF_OPTION_MSG_PERM_BANNED_USER,$form_id); 
       }
   }

  // IP banned
  //
  if(isset($_SERVER['REMOTE_ADDR'])) {
      $ip = $_SERVER['REMOTE_ADDR'];
      $banned_ips = get_option(TDOMF_BANNED_IPS);
      if($banned_ips != false && !empty($banned_ips) && strstr($banned_ips,';') !== FALSE) {
        $banned_ips = explode(";",$banned_ips);
        if(in_array($ip,$banned_ips)) {
           tdomf_log_message("Banned ip $ip tried to submit a post!",TDOMF_LOG_ERROR);
           return tdomf_get_message_instance(TDOMF_OPTION_MSG_PERM_BANNED_IP,$form_id);
        }
      }
  } else {
      // WTF? What are we to do in this case?
      tdomf_log_message("Could not get IP of visitor!",TDOMF_LOG_ERROR);
  }

  $edit_form = tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id); 
  
  // if not edit form...
  if(!$edit_form) {
  
      // Throttling Rules
      //
      $rules = tdomf_get_option_form(TDOMF_OPTION_THROTTLE_RULES,$form_id);
      if(is_array($rules) && !empty($rules)) {
          foreach($rules as $rule_id => $rule) {
              $query = "SELECT ID, post_status, post_date ";
              $query .= "FROM $wpdb->posts ";
              $query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
              if($rule['type'] == 'ip') {
                  $query .= "WHERE meta_key = '".TDOMF_KEY_IP."' ";
                  $query .= "AND meta_value = '$ip' ";
              } else if($rule['type'] == 'user') {
                  $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
                  $query .= "AND meta_value = '".$current_user->ID."' ";
              }
              if($rule['sub_type'] == 'unapproved') {
                  $query .= "AND post_status = 'draft' ";
              }
              if($rule['opt1']) {
                  // this may be inaccurate!
                  $timestamp = tdomf_timestamp_wp_sql(time() - $rule['time']);
                  $query .= "AND post_date > '$timestamp' ";
              }
              $query .= "ORDER BY post_date ASC ";
              $query .= "LIMIT " . ($rule['count'] + 1);
              $results = $wpdb->get_results( $query );
              #var_dump($results);
              if(count($results) >= $rule['count']) {
                  tdomf_log_message_extra("IP $ip blocked by Throttle Rule $rule_id",TDOMF_LOG_BAD);
                  return tdomf_get_message_instance(TDOMF_OPTION_MSG_PERM_THROTTLE,$form_id);
              }
          }
      }
  }
  
  if($edit_form) {

      // Throttling rules for edit forms
      //
      $rules = tdomf_get_option_form(TDOMF_OPTION_THROTTLE_RULES,$form_id);
      if(is_array($rules) && !empty($rules)) {
          foreach($rules as $rule_id => $rule) {
              $edit_args = array();
              if($rule['type'] == 'ip') {
                  $edit_args['ip'] = $ip;
              } else if($rule['type'] == 'user') {
                  $edit_args['user_id'] = $current_user->ID;
              }
              if($rule['sub_type'] == 'unapproved') {
                  $edit_args['state'] = 'unapproved';
              }
              if($rule['opt1']) {
                  $edit_args['time_diff'] = tdomf_timestamp_wp_sql(time() - $rule['time']);
              }
              $edit_args['limit'] = $rule['count'];
              $edit_args['sort'] = $rule['DESC'];
              $edit_args['count'] = true;
              $edit_count = tdomf_get_edits($edit_args);
              if($edit_count >= $rule['count']) {
                  tdomf_log_message_extra("This IP $ip blocked by Throttle Rule $rule_id when accessing Form $form_id (on post $post_id)",TDOMF_LOG_BAD);
                  return tdomf_get_message_instance(TDOMF_OPTION_MSG_PERM_THROTTLE,$form_id);
              }
          }
      }
      
      // valid post id value
      //
      if(!$post_id) {
          tdomf_log_message_extra("Bad post_id $post_id!",TDOMF_LOG_ERROR);
          return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_POST,$form_id);
      }
      
      // check if post exists
      //
      $post = wp_get_single_post($post_id, ARRAY_A);
      if($post == NULL) {
          tdomf_log_message_extra("Post with id $post_id does not exist!",TDOMF_LOG_ERROR);
          return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_POST,$form_id);
      }
      
      // check if post is locked
      //
      $locked = get_post_meta($post_id, TDOMF_KEY_LOCK, true);
      if($locked) {
          tdomf_log_message_extra("Post with id $post_id is locked. Cannot be edited.",TDOMF_LOG_BAD);
          return tdomf_get_message_instance(TDOMF_OPTION_MSG_LOCKED_POST,$form_id);
      }
      
      // check if it is a TDOMF post
      //
      if(tdomf_get_option_form(TDOMF_OPTION_EDIT_RESTRICT_TDOMF,$form_id)) {
          $tdomf_flag = get_post_meta($post_id, TDOMF_KEY_FLAG, true);
          if($tdomf_flag == false || empty($tdomf_flag)) {
              tdomf_log_message_extra("Post with id $post_id is not a TDOMF post and cannot be edited with form $form_id!",TDOMF_LOG_ERROR);
              return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_FORM,$form_id);
          }
      }
      
      // form is being used with right post type (i.e. page versus post)
      //      
      $use_pages = tdomf_get_option_form(TDOMF_OPTION_SUBMIT_PAGE,$form_id);
      if( ($use_pages && $post['post_type'] == 'post') || (!$use_pages && $post['post_type'] == 'page')) {
          tdomf_log_message_extra("Post with id $post_id is wrong type of post (".$post['post_type'].") for $form_id!",TDOMF_LOG_ERROR);
          return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_FORM,$form_id);
      }
      
      // do not allow editing of posts with forms unless explicitly asked
      
      if(!tdomf_get_option_form(TDOMF_OPTION_EDIT_PAGE_FORM,$form_id)) {
          if($use_pages) {
              $form_ids = tdomf_get_form_ids();
              foreach($form_ids as $a_form_id) { 
                  $created_pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$a_form_id->form_id);
                  if(is_array($created_pages)) {
                          foreach($created_pages as $created_page) {
                              if($created_page == $post_id) {
                                  tdomf_log_message_extra("Cannot edit page with id $post_id as it is in use as a form for TDOMF!",TDOMF_LOG_ERROR);
                                  return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_POST,$form_id);
                              }
                          }
                  }
              }
          }
          // Load up the post content and check for tags
          //
          if(preg_match('|<!--tdomf_form.*-->|', $post['post_content']) > 0 || preg_match('|\[tdomf_form.*\]|', $post['post_content']) > 0) {
            tdomf_log_message_extra("Cannot edit post/page with id $post_id as it contains tags for TDOMF Forms!",TDOMF_LOG_ERROR);
            return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_POST,$form_id);
          }
      }
      
      // check if it's in the right set of categories
      //
      if(!$use_pages) {
          $form_cats = tdomf_get_option_form(TDOMF_OPTION_EDIT_RESTRICT_CATS);
          if(is_array($form_cats) && !empty($form_cats)) {
              $good_cat = false;
              foreach($form_cats as $form_cat) {
                  if(in_array($form_cat,$post['post_category'])) {
                      $good_cat = true;
                      break;
                  }
              }
              if(!$good_cat) {
                  tdomf_log_message_extra("Post with id $post_id is in wrong categories for $form_id!",TDOMF_LOG_ERROR);
                  return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_FORM,$form_id);
              }
          }
      }
      
      // Is it within the time limit?
      //
      $allow_time = tdomf_get_option_form(TDOMF_OPTION_ALLOW_TIME,$form_id);
      if($allow_time > 0) {
          $diff_time = time() - strtotime($post['post_date']);
          if($diff_time > $allow_time) {
              tdomf_log_message_extra("Post with id $post_id is outside time period by ".($diff_time-$allow_time)." seconds for $form_id!",TDOMF_LOG_ERROR);
              return tdomf_get_message_instance(TDOMF_OPTION_MSG_INVALID_FORM,$form_id);
          }
      }
      
      if($check_pending_edits) {
          // If a post has a spam or unapproved edit, don't edit any more
          //
          $last_edit = tdomf_get_edits(array('post_id' => $post_id, 'limit' => 1));
          if(!empty($last_edit)) {
              if($last_edit[0]->state == 'unapproved') {
                  tdomf_log_message_extra("Post with id $post_id has an unapproved edit " . $last_edit[0]->edit_id . ". Cannot edit at this point.",TDOMF_LOG_ERROR);
                  return tdomf_get_message_instance(TDOMF_OPTION_MSG_UNAPPROVED_EDIT_ON_POST,$form_id);
              } else if($last_edit[0]->state == 'spam') {
                  tdomf_log_message_extra("Post with id $post_id has a spam edit " . $last_edit[0]->edit_id . ". Cannot edit at this point.",TDOMF_LOG_ERROR);
                  return tdomf_get_message_instance(TDOMF_OPTION_MSG_SPAM_EDIT_ON_POST,$form_id);
              }
          }
      }
  }
  
    // What users can access the form
    //
    if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) == false) {

        // is current user the original submitter
        //
        if($edit_form && tdomf_get_option_form(TDOMF_OPTION_ALLOW_AUTHOR,$form_id) && is_user_logged_in()) {
            $submitter_user_id = get_post_meta($post_id, TDOMF_KEY_USER_ID, true);
            $current_user = wp_get_current_user();
            if(!empty($submitter_user_id) && $submitter_user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
                if($current_user->ID == $submitter_user_id) {
                    return NULL;
                }
            } 
            // allow author of post to edit, if permissible
            //
            if($current_user->ID == $post['post_author']) {
                return NULL;
            }
        }
        
        // does the current user have the capability
        //
        if(current_user_can(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id)) { 
            return NULL;
        }
        
        // check if users with publish rights can use form
        //
        if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_PUBLISH,$form_id) == true && current_user_can("publish_posts")) {
            return NULL;
        }
        
        // check if default role is set and if anyone can register => logged
        // in users are valid
        //
        if(!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $roles = $wp_roles->role_objects;
        foreach($roles as $role) {
            if($role->name == get_option('default_role')) {
                $def_role = $role->name;
                break;
            }
        }
        if(is_user_logged_in() && get_option('users_can_register') && isset($def_role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])) {
            return NULL;
        }
        
        // check against selected caps
        //
        $access_caps = tdomf_get_option_form(TDOMF_OPTION_ALLOW_CAPS,$form_id);
        if(is_array($access_caps)) {
            foreach($access_caps as $cap) {
                if(current_user_can($cap)) {
                    return NULL;
                }
            }
        }

        // check against selected users
        //
        $allow_users = tdomf_get_option_form(TDOMF_OPTION_ALLOW_USERS,$form_id);           
        if(is_array($allow_users) && is_user_logged_in() && in_array($current_user->ID,$allow_users)) {
            return NULL;
        }
        
        // If you get this point, all other checks failed
        //
        if(is_user_logged_in()) {
            return tdomf_get_message_instance(TDOMF_OPTION_MSG_PERM_INVALID_USER,$form_id);
        } else {
            return tdomf_get_message_instance(TDOMF_OPTION_MSG_PERM_INVALID_NOUSER,$form_id);
        }
    }
  
  return NULL;
}

// Generate a preview based on form arguments
//
function tdomf_preview_form($args,$mode=false) {
   global $tdomf_form_widgets_preview,$tdomf_form_widgets_preview_hack;

   $form_id = intval($args['tdomf_form_id']);

  // Set mode of form
   $hack = false;   
   if(!$mode) {
       $mode = tdomf_generate_default_form_mode($form_id);
   }
   if(strpos($mode,'-hack') !== false) {
       $hack = true;
   }
   $edit = false;
   if(strpos($mode,'edit-') !== false) {
      $edit = true;
  }
   
  // grab post id
  $post_id = false;
  if($edit && isset($args['tdomf_post_id'])) {
     $post_id = intval($args['tdomf_post_id']);
  }
  
  // flag as preview
  if(strpos($mode,'-preview') === false) {
      $mode .= '-preview';
  }
  
   do_action('tdomf_preview_form_start',$form_id,$mode);
   
   // handle hacked forms
   //
   if(!$hack) {
      // see if there is a "hacked" preview already! 
      $hacked_message = tdomf_get_option_form(TDOMF_OPTION_FORM_PREVIEW_HACK,$form_id);
      if($hacked_message != false) {
          $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_preview);
          $message = tdomf_prepare_string($hacked_message, $form_id, $mode, $post_id, "", $args);
          
          // basics
          $unused_patterns = array();
          $patterns     = array ();
          $replacements = array ();

          // widgets
          $widget_args = array_merge( array( "before_widget"=>"<p>\n",
                                      "after_widget"=>"\n</p>\n",
                                      "before_title"=>"<b>",
                                      "after_title"=>"</b><br/>",
                                      "mode"=>$mode ),
                                      $args);
          $widget_order = tdomf_get_widget_order($form_id);
          foreach($widget_order as $w) {
              if(isset($widgets[$w])) {
                  // all widgets need to be excuted even if not displayed
                  $replacement = call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
                  $patterns[]     = '/'.TDOMF_MACRO_WIDGET_START.$w.TDOMF_MACRO_END.'/';                  
                  $replacements[] =  tdomf_preg_prepare($replacement);  
     
              } else {
                   $unused_patterns[] = '/'.TDOMF_MACRO_WIDGET_START.$w.TDOMF_MACRO_END.'/';
              }
          }

          // create message
          $message = preg_replace($patterns,$replacements,$message);
          $message = preg_replace($unused_patterns,"",$message);
          
          return $message;
      }
   } 
      
   $message = "";
   if(!$hack) {
       $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_preview);
       $widget_args = array_merge( array( "before_widget"=>"<p>\n",
                                          "after_widget"=>"\n</p>\n",
                                          "before_title"=>"<b>",
                                          "after_title"=>"</b><br/>",
                                          "mode"=>$mode, 
                                          "tdomf_form_id"=>$form_id),
                                          $args);
       $widget_order = tdomf_get_widget_order($form_id);
       foreach($widget_order as $w) {
          if(isset($widgets[$w])) {
            tdomf_log_message_extra("Looking at preview widget $w");
            $message .= call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
          }
       }
   } else {
      $widgets_o = tdomf_filter_widgets($mode, $tdomf_form_widgets_preview);
      $widgets_h = tdomf_filter_widgets($mode, $tdomf_form_widgets_preview_hack);
      $widget_args = array( "before_widget"=>"<p>\n",
                             "after_widget"=>"\n</p>\n",
                             "before_title"=>"<b>",
                             "after_title"=>"</b>\n\t<br/>\n",
                             "mode"=>$mode,
                             "tdomf_form_id"=>$form_id);
      $widget_order = tdomf_get_widget_order($form_id);
      $message .= "\n<!-- widgets start -->\n";
      foreach($widget_order as $w) {
          if(!isset($widgets_h[$w]) && isset($widgets_o[$w])) {
              $message .= "%%WIDGET:$w%%\n";
          } else if(isset($widgets_h[$w])) {
              $message .= "<!-- $w start -->\n";
              $message .= call_user_func($widgets_h[$w]['cb'],$widget_args,$widgets_h[$w]['params']);
              $message .= "<!-- $w end -->\n";
          }
      }
      $message .= "<!-- widgets end -->\n";
   }
   
   if($message == "") {
      tdomf_log_message("Couldn't generate preview!",TDOMF_LOG_ERROR);
	  return __("Error! Could not generate a preview!","tdomf");
   }
   if($edit) {
       return sprintf(__("This is a preview of your contribution:%s\n","tdomf"),$message);
   }
   return sprintf(__("This is a preview of your submission:%s\n","tdomf"),$message);
}

// Validate input using widgets
//
function tdomf_validate_form($args,$preview = false) {
   global $tdomf_form_widgets_validate;

   $form_id = intval($args['tdomf_form_id']);
   
   // Set mode of page
   if(tdomf_get_option_form(TDOMF_OPTION_SUBMIT_PAGE,$form_id)) {
     $mode = 'new-page-validate';
   } else {
     $mode = 'new-post-validate';
   }
   if($preview) { $mode .= '-preview'; }
   
   do_action('tdomf_validate_form_start',$form_id,$mode);
   $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_validate);

   $message = "";
   $widget_args = array_merge( array( "before_widget"=>"",
                                      "after_widget"=>"<br/>\n",
                                      "before_title"=>"<b>",
                                      "after_title"=>"</b><br/>",
                                      "mode"=>$mode),
							   $args);
   $widget_order = tdomf_get_widget_order($form_id,$preview);
   foreach($widget_order as $w) {
	  if(isset($widgets[$w])) {
		$temp_message = call_user_func($widgets[$w]['cb'],$widget_args,$preview,$widgets[$w]['params']);
		if($temp_message != NULL && trim($temp_message) != ""){
		   $message .= $temp_message;
		}
	   }
   }
   // Oh dear! Something didn't validate!
   if(trim($message) != "") {
	  tdomf_log_message("Their submission didn't validate.");
	  return "<font color='red'>$message</font>\n";
   }
   return NULL;
}

function tdomf_timestamp_wp_sql( $timestamp, $gmt = false ) {
   return ( $gmt ) ? gmdate( 'Y-m-d H:i:s', $timestamp ) : gmdate( 'Y-m-d H:i:s', ( $timestamp + ( get_option( 'gmt_offset' ) * 3600 ) ) );
}

function tdomf_queue_date($form_id,$current_ts)  {
    tdomf_log_message("Current ts is $current_ts" );
    $queue_period = intval(tdomf_get_option_form(TDOMF_OPTION_QUEUE_PERIOD,$form_id));
    if($queue_period > 0) {
          tdomf_log_message("Queue period is $queue_period seconds");
          global $wpdb;
          if(tdomf_get_option_form(TDOMF_OPTION_QUEUE_ON_ALL,$form_id)) {
              $query = "SELECT DATE_ADD(post_date, INTERVAL $queue_period SECOND) as the_datetime
                FROM $wpdb->posts
                WHERE $wpdb->posts.post_status='future' OR $wpdb->posts.post_status='publish'
                ORDER BY the_datetime DESC LIMIT 1 ";
          } else {
              /*$query = "SELECT ADDTIME(post_date, SEC_TO_TIME({$queue_period})) 
                FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
                WHERE $wpdb->postmeta.meta_key='".TDOMF_KEY_FORM_ID."'
                    AND $wpdb->postmeta.meta_value='$form_id'
                    AND ($wpdb->posts.post_status='future' OR $wpdb->posts.post_status='publish')
                ORDER BY post_date DESC LIMIT 1 ";*/
              $query = "SELECT DATE_ADD(post_date, INTERVAL $queue_period SECOND) as the_datetime
                FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
                WHERE $wpdb->postmeta.meta_key='".TDOMF_KEY_FORM_ID."'
                    AND $wpdb->postmeta.meta_value='$form_id'
                    AND ($wpdb->posts.post_status='future' OR $wpdb->posts.post_status='publish')
                ORDER BY the_datetime DESC LIMIT 1 ";
          }
          $next_ts = $wpdb->get_var( $query );
          if( null != $next_ts ) {
              tdomf_log_message("Sticking post in queue with ts of $next_ts");
              return $next_ts;
          }
    }
    return $current_ts;
}

// Updates a post using args
//
function tdomf_update_post($form_id,$mode,$args) {
   global $wp_rewrite, $tdomf_form_widgets_post, $current_user;

   $post_id = intval($args['tdomf_post_id']);

   // set initially to post_id
   
   $returnVal = intval($post_id); 
   
   // hook already performed by tdomf_create_post
   
   tdomf_log_message("Attempting to update post $post_id based on input");
      
   $user_id = false;
   $can_publish = false;
   if(!tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id)) {
       $can_publish = true;
   } else if(is_user_logged_in()) {
       $user_id = $current_user->ID;
       if($user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
         $testuser = new WP_User($user_id,$current_user->user_login);
         $user_status = get_usermeta($user_id,TDOMF_KEY_STATUS);
         if($user_status == TDOMF_USER_STATUS_TRUSTED) {
             tdomf_log_message("User is trusted => will auto-publish.",TDOMF_LOG_GOOD);
             $can_publish = true;
         }
         else if(tdomf_get_option_form(TDOMF_OPTION_PUBLISH_NO_MOD,$form_id) && current_user_can('publish_posts')) {
             tdomf_log_message("User has publish rights => will auto-publish",TDOMF_LOG_GOOD);
             $can_publish = true;
         }
       }
   }
   
   // if versioning enabled, use two versions
   //
   //   * Current represents a copy of the current revision (this will probably 
   //     duplicate revisions but we have no way of knowing if revision X = 
   //     current post). This will be used to support "reverting" to the 
   //     previous version
   //   * The other revision, is the one we'll be modifying and if it's okay
   //     making it the actual version.
   //
   $current_revision_id = wp_save_post_revision($post_id);
   $revision_id = wp_save_post_revision($post_id);
   if($revision_id == NULL || $current_revision_id == NULL) {
       tdomf_log_message("Revisions disabled for post $post_id", TDOMF_LOG_BAD);
       if($revision_id != NULL) {
           wp_delete_revision($revision_id);
           $revision_id = NULL;
       }
       if($current_revision_id != NULL) {
           wp_delete_revision($current_revision_id);
           $current_revision_id = NULL;
       }
   } 
   
   if($revision_id) {
       // we need to backup the custom field info on the post
       $customFields = get_post_meta($post_id, TDOMF_KEY_CUSTOM_FIELDS, true);
       if(!empty($customFields) && is_array($customFields)) {
            foreach ( $customFields as $key => $title ) {
                $value = get_post_meta($post_id, $key, true);
                // Must use TDOMF's version of udpate post meta as this allows
                // revisions to hold custom fields
                TDOMF_Widget::updatePostMeta($current_revision_id, $key, $value);
                TDOMF_Widget::updatePostMeta($revision_id, $key, $value);
            }
       } else {
            $customFieldKeys = get_post_custom_keys($post_id);
            foreach ( $customFieldKeys as $key ) {
                $value = get_post_meta($post_id, $key, true);
                // Must use TDOMF's version of udpate post meta as this allows
                // revisions to hold custom fields
                TDOMF_Widget::updatePostMeta($current_revision_id, $key, $value);
                TDOMF_Widget::updatePostMeta($revision_id, $key, $value);
            }
       }
   }
   
   // flag post under tdomf (if not already)
   //
   add_post_meta($post_id, TDOMF_KEY_FLAG, true, true);
   
   // disable kses filters (as it's going to be moderated)
   //
   if(tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id)){
     kses_remove_filters();
   }
   
   // if versioning disabled
   // if moderation enabled, not trusted user, user doesn't have publish rights...
   // ... set post to 'draft' 
   //
   if($revision_id == NULL && !$can_publish) {
       tdomf_log_message("Setting $post_id to draft");
       $postargs = array (
           "ID"          => $post_id,
           "post_status" => "draft",
           );
       wp_update_post($postargs);
   }
   
   // store information about edit
   
   if(is_int($returnVal))
   {
       $edit_revision_id = 0;
       $edit_current_revision_id = 0;
       if($revision_id) {
           $edit_revision_id = $revision_id;
           $edit_current_revision_id = $current_revision_id;
       }

       // can be used by spam check
       //
       $edit_data = array( 'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                           'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] );
       
       $edit_user_id = 0;
       if($user_id !== false && $user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
           tdomf_log_message("Logging default submitter info (user $user_id) for this post update for $post_id");
           $edit_user_id = $user_id;
           update_usermeta($user_id, TDOMF_KEY_FLAG, true);
           $edit_data["user_login"] = $current_user->user_login;           
       }
       
       $edit_user_ip = 0;
       if(isset($args['ip'])) {
           tdomf_log_message("Logging ip " . $args['ip'] ." for this post update for $post_id");
           $edit_user_ip = $args['ip'];
       }
       
       
       $edit_id = tdomf_create_edit($post_id,$form_id,$edit_revision_id,$edit_current_revision_id,$edit_user_id,$edit_user_ip,'unapproved',$edit_data);
       tdomf_log_message("Edit ID = $edit_id");      
       
       if($edit_id == 0) {
           // error! do something
           tdomf_log_message("Edit $edit_id is invalid!",TDOMF_LOG_ERROR);
       } else {
           $returnVal = intval($edit_id);
       }
   }
   
   tdomf_log_message("Let the widgets do their work on updating $post_id");
   
   // Widgets:post
   //
   $message = "";
   $widget_args = array( "before_widget"=>"",
                         "after_widget"=>"<br/>\n",
                         "before_title"=>"<b>",
                         "after_title"=>"</b><br/>",
                         "mode"=>$mode,
                         "edit_id"=>$edit_id);
   // Use revision_id for post id if avaliable, this makes it transparent
   // to the widgets
   if($revision_id) {
       $widget_args["post_ID"] = $revision_id;
   } else {
       $widget_args["post_ID"] = $post_id;
   }
   $widget_args = array_merge( $widget_args,
                               $args);
   $widget_order = tdomf_get_widget_order($form_id);
   $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_post);
   foreach($widget_order as $w) {
    if(isset($widgets[$w])) {
      $temp_message = call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
      if($temp_message != NULL && trim($temp_message) != ""){
        $message .= $temp_message;
      }
	  }
   }
   // Oh dear! Errors after submission!
   if(trim($message) != "") {
     tdomf_log_message("Post widgets report error!",TDOMF_LOG_BAD);
     $returnVal = "<font color='red'>$message</font>\n";
   }

   if(is_int($returnVal)) 
   {
       $send_moderator_email = true;
       
       $not_spam = true; 
       if(tdomf_do_spam_check($form_id,$post_id,$edit_id)) {
           $not_spam = tdomf_check_edit_spam($edit_id,true);
       }
       if($not_spam) {
           
           // Edited count
           //
           $edited_count = get_option(TDOMF_STAT_EDITED);
           if($edited_count == false) {
              $edited_count = 0;
           }
           $edited_count++;
           update_option(TDOMF_STAT_EDITED,$edited_count);
           tdomf_log_message("Edit $edit_id is $edited_count edit!");
    
           // [queuing is currently disabled for editing]
    
           // Restore version (if using versions) and can publish
           // (if not using revisions, post is only set to draft if !can_publish)
           
           if($can_publish) {
               tdomf_set_state_edit('approved',$edit_id);
               if($revision_id) {
                   tdomf_log_message("Can publish so setting revision $revision_id as main revision for Post $post_id", TDOMF_LOG_GOOD);
                   wp_restore_post_revision($revision_id);
               } 
               if($edit_user_id > 0) {
                   tdomf_trust_user($edit_user_id);
               }
               // if no revisions, post was never not published
           }
       }
       else {
         // it's spam :(
         if(get_option(TDOMF_OPTION_SPAM_NOTIFY) == 'none') {
           $send_moderator_email = false;
         }
       }
    
       // Notify admins
       //
       if($send_moderator_email){
          tdomf_notify_admins_edit($edit_id,$form_id);
       }
   } 
   
   // in case of error, delete revision as there is no point keeping it
   //
   if(!is_int($returnVal)) {
       if($revision_id)  {
           tdomf_log_message("There were errors, delete revision $revision_id", TDOMF_LOG_BAD);
           wp_delete_revision($revision_id);
       }
       tdomf_delete_edit(array($edit_id));
   }
   
   // Re-enable filters so we dont' break anything else!
   //
   if(tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id) && current_user_can('unfiltered_html') == false){
     kses_init_filters();
   }
   
   // hook
   //
   do_action('tdomf_create_post_end',$post_id,$form_id,$mode);
   
   return $returnVal;
}

// Creates a post using args
//
function tdomf_create_post($args) {
   global $wp_rewrite, $tdomf_form_widgets_post, $current_user;

   $form_id = intval($args['tdomf_form_id']);
   
   // Set mode of page
   //
   $mode = tdomf_generate_default_form_mode($form_id);
   
   do_action('tdomf_create_post_start',$form_id,$mode);

   // if editing a post don't use this function!
   //
   if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id)) {
       return tdomf_update_post($form_id,$mode,$args);
   }
   
   tdomf_log_message("Attempting to create a post based on submission");

   // Default submitter
   $user_id = get_option(TDOMF_DEFAULT_AUTHOR);
   if(is_user_logged_in()) {
      $user_id = $current_user->ID;
   }

   // Default category
   //
   $post_cats = array(tdomf_get_option_form(TDOMF_DEFAULT_CATEGORY,$form_id));

   // Default title (should this be an option?)
   //
   $def_title = tdomf_get_log_timestamp();
   
   // Date of submission of post
   //
   $date = current_time('mysql');
   $date_gmt = get_gmt_from_date($date);

   // Build post and post it as draft
   //
   $post = array (
	   "post_content"   => "",
#	   "post_excerpt"   => "",
	   "post_title"     => $def_title,
	   "post_category"  => $post_cats,
	   "post_author"    => $user_id,
	   "post_status"    => 'draft',
#	   "post_name"      => "",
#	   "post_date"      => $post_date,
#    "post_date_gmt"  => $post_date_gmt,
#	   "comment_status" => get_option('default_comment_status'),
#	   "ping_status"    => get_option('default_ping_status').
   );
   //
   // submit a page instead of a post
   //   
   if(tdomf_get_option_form(TDOMF_OPTION_SUBMIT_PAGE,$form_id)) {
     $post['post_type'] = 'page';
   }
   //
   $post_ID = wp_insert_post($post);
   if($post_ID == 0)
   {
       tdomf_log_message("Failed to create post! \$post_ID == 0",TDOMF_LOG_ERROR);
       return __("TDOMF ERROR: Failed to create post! \$post_ID == 0","tdomf");
   }

   tdomf_log_message("Post with id $post_ID (and default title $def_title) created as draft.");
   
   // Flag this post as TDOMF!
   add_post_meta($post_ID, TDOMF_KEY_FLAG, true, true);

   // Submitter info
   if($user_id != get_option(TDOMF_DEFAULT_AUTHOR)){
     tdomf_log_message("Logging default submitter info (user $user_id) for this post $post_ID");
     add_post_meta($post_ID, TDOMF_KEY_USER_ID, $user_id, true);
     add_post_meta($post_ID, TDOMF_KEY_USER_NAME, $current_user->user_login, true);
     update_usermeta($user_id, TDOMF_KEY_FLAG, true);
   }

   // IP info
   if(isset($args['ip'])){
        $ip = $args['ip'];
        tdomf_log_message("Logging default ip $ip for this post $post_ID");
        add_post_meta($post_ID, TDOMF_KEY_IP, $ip, true);
   }

   // Form Id
   //
   add_post_meta($post_ID, TDOMF_KEY_FORM_ID, $form_id, true);

   // Submission dates
   //
   add_post_meta($post_ID, TDOMF_KEY_SUBMISSION_DATE, $date, true);
   add_post_meta($post_ID, TDOMF_KEY_SUBMISSION_DATE_GMT, $date_gmt, true);
   
   tdomf_log_message("Let the widgets do their work on newly created $post_ID");

   // Disable kses protection! It seems to get over-protective of non-registered
   // posts! If the post is going to be moderated, then we don't have an issue
   // as an admin will verify it... I think. Hope to god this is not a
   // security risk!
   if(tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id)){
     kses_remove_filters();
   }
   
   // Widgets:post
   //
   $message = "";
   $widget_args = array_merge( array( "post_ID"=>$post_ID,
                                      "before_widget"=>"",
                                      "after_widget"=>"<br/>\n",
                                      "before_title"=>"<b>",
                                      "after_title"=>"</b><br/>",
                                      "mode"=>$mode),
                                      $args);
   $widget_order = tdomf_get_widget_order($form_id);
   $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_post);
   foreach($widget_order as $w) {
    if(isset($widgets[$w])) {
      $temp_message = call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
      if($temp_message != NULL && trim(strval($temp_message)) != ""){
        $message .= $temp_message;
      }
	  }
   }
   // Oh dear! Errors after submission!
   if(trim($message) != "") {
     tdomf_log_message("Post widgets report error! Attempting to delete $post_ID post...");
     wp_delete_post($post_ID);
     return "<font color='red'>$message</font>\n";
   }
   

   $send_moderator_email = true;
   
   // Spam check
   //
   add_post_meta($post_ID, TDOMF_KEY_USER_AGENT, $_SERVER['HTTP_USER_AGENT'], true);
   add_post_meta($post_ID, TDOMF_KEY_REFERRER, $_SERVER['HTTP_REFERER'], true);
   $not_spam = true; 
   if(tdomf_do_spam_check($form_id,$post_ID)) {
       $not_spam = tdomf_check_submissions_spam($post_ID);
   }
   if($not_spam) {
       // Submitted post count!
       //
       $submitted_count = get_option(TDOMF_STAT_SUBMITTED);
       if($submitted_count == false) {
          $submitted_count = 0;
       }
       $submitted_count++;
       update_option(TDOMF_STAT_SUBMITTED,$submitted_count);
       tdomf_log_message("post $post_ID is number $submitted_count submission!");
       
     // publish (maybe)
     //
     $publish_now = false;
     if(!tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id)){
         tdomf_log_message("Moderation is disabled. Publishing $post_ID!");
         $publish_now = true;
     } else if($user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
         $testuser = new WP_User($user_id, $current_user->user_login);
         $user_status = get_usermeta($user_id,TDOMF_KEY_STATUS);
         if($user_status == TDOMF_USER_STATUS_TRUSTED) {
             tdomf_log_message("User is trusted. Publishing $post_ID!");
             $publish_now = true;
         }
         else if(tdomf_get_option_form(TDOMF_OPTION_PUBLISH_NO_MOD,$form_id) && current_user_can('publish_posts')) {
             tdomf_log_message("User has publish rights. Publishing $post_ID!");
             $publish_now = true;
         }
     }
     
     // publish it
     //
     if($publish_now){
        //
        // Use update post instead of publish post because in WP2.3, 
        // update_post doesn't seem to add the date correctly!
        // Also when it updates a post, if comments aren't set, sets them to
        // empty! (Not so in WP2.2!)
        
        // Schedule date
        //
        $current_ts = current_time( 'mysql' );
        $ts = tdomf_queue_date($form_id,$current_ts);
        if($current_ts == $ts) {
            $post = array (
              "ID"             => $post_ID,
              "post_status"    => 'publish',
              );
        } else {
            tdomf_log_message("Future Post Date = $ts!");
            $post = array (
              "ID"             => $post_ID,
              "post_status"    => 'future',
              "post_date"      => $ts,
              /* edit date required for wp 2.7 */
              "edit_date"      => $ts,
              );
        }
        
        wp_update_post($post);
        $send_moderator_email = tdomf_get_option_form(TDOMF_OPTION_MOD_EMAIL_ON_PUB,$form_id);
     }
   } else {
     // it's spam :(
     
     if(get_option(TDOMF_OPTION_SPAM_NOTIFY) == 'none') {
       $send_moderator_email = false;
     }
   }
   
   // Notify admins
   //
   if($send_moderator_email){
      tdomf_notify_admins($post_ID,$form_id);
   }

   // Re-enable filters so we dont' break anything else!
   //
   if(tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id) && current_user_can('unfiltered_html') == false){
     kses_init_filters();
   }

   do_action('tdomf_create_post_end',$post_ID,$form_id,$mode);
   
   // Delete all versions but latest
   //
   if( WP_POST_REVISIONS ) {
       $revisions = wp_get_post_revisions( $post_ID, array( 'order' => 'ASC' ) );
       tdomf_log_message("post $post_ID generated ".count($revisions)." revisions.");
       // discount the latest revision
       //$revisions = array_slice( $revisions, 0, (count($revisions) - 1) );
       #tdomf_log_message("<pre>".var_export($revisions,true)."</pre>");
       foreach($revisions as $rev) {
           tdomf_log_message("Deleting revisions ".$rev->ID."");
           wp_delete_post_revision( $rev->ID );
       }
   }
   
   return intval($post_ID);
}

// Generate Form Key and place it in Session for Post forms
//
function tdomf_generate_form_key($form_id) {
 
  $tdomf_verify = get_option(TDOMF_OPTION_VERIFICATION_METHOD);
  if($tdomf_verify == 'wordpress_nonce' && function_exists('wp_create_nonce')) {
    $nonce_string = wp_create_nonce( 'tdomf-form-'.$form_id );
    return "<div><input type='hidden' id='tdomf_key_$form_id' name='tdomf_key_$form_id' value='$nonce_string' /></div>";
  } else if($tdomf_verify == 'none') {
    // do nothing! Bad :(
    return "";
  }
  
  // default
  $form_data = tdomf_get_form_data($form_id);  
  $random_string = tdomf_random_string(100);
  $form_data["tdomf_key_$form_id"] = $random_string;
  tdomf_log_message_extra('Placing key '.$random_string.' in form_data: <pre>'.var_export($form_data,true)."</pre>");
  tdomf_save_form_data($form_id,$form_data);
  return "<div><input type='hidden' id='tdomf_key_$form_id' name='tdomf_key_$form_id' value='$random_string' /></div>";
}

// Create the form!
//
function tdomf_generate_form($form_id = 1,$mode = false,$post_id = false) {
  global $tdomf_form_widgets,$tdomf_form_widgets_hack;

  if(!tdomf_form_exists($form_id)) {
    return sprintf(__("Form %d does not exist.",'tdomf'),$form_id); 
  }

  // Set mode of form
  $hack = false;
  $edit = false;
  if($mode === false) {
      $mode = tdomf_generate_default_form_mode($form_id);
  } else {
      if(strpos($mode,'-hack') !== false) {
         $hack = true;
      }
  }
  if(strpos($mode,'edit-') !== false) {
      $edit = true;
  }
  
  
  // @todo log an error if wrong edit/new or post/page is used
  
  $use_ajax = tdomf_widget_is_ajax_avaliable($form_id);
  if($use_ajax && strpos($mode,'ajax') === false) {
      $mode .= "-ajax";
  }
  
  // grab form data
  
  $form_data = tdomf_get_form_data($form_id);
  
  // do we need post_id, and if so set it

  if($edit && !$post_id) {
      if(isset($form_data['tdomf_post_id'])) {
          $post_id = $form_data['tdomf_post_id'];
      } else if(isset($_REQUEST['tdomf_post_id'])) {
          $post_id = $_REQUEST['tdomf_post_id'];
      }
  }
  
  // @todo we want to check if we can edit the post, but 
  // we do not care about 'pending edits', if we're in the process of editing
  // an post already! - i.e. acknowledgement screen
  
  if(!$hack) {
      $form = tdomf_check_permissions_form($form_id,$post_id,empty($form_data));
      if($form != NULL) {
        return $form;
      }
  }
  
  do_action('tdomf_generate_form_start',$form_id,$mode);

  // initilise some variables
  //
  if($hack) {
      $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_hack);
  } else {
      $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets);
  }
  $form = "";
    
  // we need to tag the mode if it's after a "preview", check if there is
  // there is any $_POST input for the form
  //
  if(isset($form_data['tdomf_form_post_'.$form_id])) {
       
      // Now to make sure, lets check if the preview button was pressed
      //
      // Hate doubling up work here, but need form_id_safe early for this check
      // Will leave the form_id_safe code that exists later in, in case this 
      // check fails
      //
      if($edit && $post_id) {
          $form_id_safe = $form_id.'_'.$post_id;
      } else {
          $form_id_safe = $form_id;
      }
      // Was the preview button pressed?
      if(isset($form_data['tdomf_form_post_'.$form_id]['tdomf_form'.$form_id_safe.'_preview'])) {
          $mode .= '-preview';
          #$form_data['tdomf_form_post_'.$form_id]['tdomf_form'.$form_id_safe.'_preview']['mode'] = $mode;
      }
  }
  
  // handle hacked forms
  //
  if(!$hack) {
      $hacked_form = tdomf_get_option_form(TDOMF_OPTION_FORM_HACK,$form_id);
      if($hacked_form != false) {
          
          // grab form message and post args (if exists)
          //
          $post_args = array();
          $message = false;
          if(isset($form_data['tdomf_form_post_'.$form_id])) {
              // grab post args
              $post_args = $form_data['tdomf_form_post_'.$form_id];
              unset($form_data['tdomf_form_post_'.$form_id]);
              tdomf_save_form_data($form_id,$form_data);
              if(isset($post_args['tdomf_post_message_'.$form_id])) {
                  // grab message (preview/validation)
                  $message = $post_args['tdomf_post_message_'.$form_id];
                  unset($form_data['tdomf_post_message_'.$form_id]);
                  tdomf_save_form_data($form_id,$form_data);
              }
              // form has been turned off! just return message
              if(isset($post_args['tdomf_no_form_'.$form_id])) {
                  unset($post_args['tdomf_no_form_'.$form_id]);
                  tdomf_save_form_data($form_id,$form_data);
                  return $message;
              }
          }
          
          $form = tdomf_prepare_string($hacked_form, $form_id, $mode, $post_id, "", $post_args);
          
          // basics
          $unused_patterns = array();
          $patterns     = array ( '/'.TDOMF_MACRO_FORMKEY.'/');
          $replacements = array ( tdomf_generate_form_key($form_id));
          
          // post id
          if($edit) {
              $patterns[] = '/'.TDOMF_MACRO_POSTID.'/';
              $replacements[] = $post_id;
          }

          // message
          if($use_ajax && $message == false) {
              $patterns[]     = '/'.TDOMF_MACRO_FORMMESSAGE.'/';
              $replacements[] = "<div id='tdomf_form${form_id}_message' id='tdomf_form${form_id}_message' class='hidden'></div>";
          } else {
              $patterns[]     = '/'.TDOMF_MACRO_FORMMESSAGE.'/';
              // prep form: the $ and \\ are special operators in preg_replace replacement string
              $message = str_replace('$','\\$',$message);
              $message = str_replace('\\\\','\\\\\\\\',$message);
              $replacements[] = $message;
          }
          
          // widgets
          $widget_args = array_merge( array( "before_widget"=>"<fieldset>\n",
                                           "after_widget"=>"\n</fieldset>\n",
                                           "before_title"=>"<legend>",
                                           "after_title"=>"</legend>",
                                           "tdomf_form_id"=>$form_id,
                                           "mode"=>$mode),
                                           $post_args);
          if($edit) {
              if(!isset($widget_args['tdomf_post_id'])) {
                  $widget_args['tdomf_post_id'] = $post_id;
              }
              // old way
              if(!isset($widget_args['post_ID'])) {
                  $widget_args['post_ID'] = $post_id;
              }
          }
          $widget_order = tdomf_get_widget_order($form_id);
          foreach($widget_order as $w) {
              if(isset($widgets[$w])) {
                  // all widgets need to be excuted even if not displayed
                  $replacement = call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
                  $patterns[]     = '/'.TDOMF_MACRO_WIDGET_START.$w.TDOMF_MACRO_END.'/';
                  $replacements[] = tdomf_preg_prepare($replacement);
              } else {
                   $unused_patterns[] = '/'.TDOMF_MACRO_WIDGET_START.$w.TDOMF_MACRO_END.'/';
              }                                                                                             
          }
          
          // create form
          $form = preg_replace($patterns,$replacements,$form);
          $form = preg_replace($unused_patterns,"",$form);
          return $form;
      }
  }
  
  if($hack) {
      if($edit) {
          $form_name = 'tdomf_form'.TDOMF_MACRO_FORMID.'-'.TDOMF_MACRO_POSTID;
          $form_id_safe = TDOMF_MACRO_FORMID.'_'.TDOMF_MACRO_POSTID;
      } else {
          $form_name = 'tdomf_form'.TDOMF_MACRO_FORMID;
          $form_id_safe = TDOMF_MACRO_FORMID;
      }
  } else {
      if($edit && $post_id) {
          $form_name = 'tdomf_form'.$form_id.'_'.$post_id;
          $form_id_safe = $form_id.'_'.$post_id;
      } else {
          $form_name = 'tdomf_form'.$form_id;
          $form_id_safe = $form_id;
      }
  }
  
  if($hack) {
     $form .= "\n<!-- Form $form_id start -->\n";
  }
  
  if($use_ajax) {
      $ajax_script = TDOMF_URLPATH.'tdomf-form-ajax.php';
      if($hack) {
          $form .= "<!-- AJAX js start -->\n";
      }
      $jquery_url = get_bloginfo('wpurl').'/wp-includes/js/jquery/jquery.js';
      $form .= "<script type='text/javascript' src='$jquery_url'></script>\n";
      $sack_url = get_bloginfo('wpurl').'/wp-includes/js/tw-sack.js';
      $ajax_error = __("TDOMF: ERROR with AJAX request.","tdomf");
      $form .= "<script type='text/javascript' src='$sack_url'></script>\n";
      $form .= <<<EOT
<script type="text/javascript">
	//<!-- [CDATA[
	function ajaxProgressStart$form_id_safe() {
		var w = jQuery('#ajaxProgress$form_id_safe').width();
		var h = jQuery('#ajaxProgress$form_id_safe').height();
		var offset = jQuery('#$form_name').offset();
		var x = offset.left + ((jQuery('#$form_name').width() - w) / 2);
		var y = offset.top + ((jQuery('#$form_name').height() - h) / 2);
		jQuery('#ajaxProgress$form_id_safe').css({display: 'block', height: h + 'px', width: w + 'px', position: 'absolute', left: x + 'px', top: y + 'px', zIndex: '1000' });
		jQuery('#ajaxProgress$form_id_safe').attr('class','progress');
		ajaxShadow$form_id_safe();
	}
	function ajaxShadow$form_id_safe() {
		var offset = jQuery('#$form_name').offset();
		var w = jQuery('#$form_name').width();
		var h = jQuery('#$form_name').height();
		jQuery('#shadow$form_id_safe').css({ width: w + 'px', height: h + 'px', position: 'absolute', left: offset.left + 'px', top: offset.top + 'px' });
		jQuery('#shadow$form_id_safe').css({zIndex: '999', display: 'block'});
		jQuery('#shadow$form_id_safe').fadeTo('fast', 0.2);
	}
	function ajaxUnshadow$form_id_safe() {
		jQuery('#shadow$form_id_safe').fadeOut('fast', function() {jQuery('#tdomf_shadow').hide()});
	}
	function ajaxProgressStop$form_id_safe() {
		jQuery('#ajaxProgress$form_id_safe').attr('class','hidden');
		jQuery('#ajaxProgress$form_id_safe').hide();
		ajaxUnshadow$form_id_safe();
	}
	function tdomfSubmit$form_id_safe(action) {
		ajaxProgressStart$form_id_safe();
		var mysack = new sack("$ajax_script" );
		mysack.execute = 1;
		mysack.method = 'POST';
		mysack.setVar( "tdomf_action", action );
		mysack.setVar( "tdomf_args", jQuery('#$form_name').serialize());
		mysack.onError = function() { alert('$ajax_error' )};
		mysack.runAJAX();
		return true;
	}
	function tdomfDisplayMessage$form_id_safe(message, mode) {
		if(mode == "full") {
			jQuery('#tdomf_form${form_id_safe}_message').attr('class','hidden');
			document.getElementById('tdomf_form${form_id_safe}_message').innerHTML = "";
			document.$form_name.innerHTML = message;
            jQuery('#$form_name').focus();
            var offset = jQuery('#$form_name').offset();
            window.scrollTo(offset.left,offset.top);
		} else if(mode == "preview") {
			jQuery('#tdomf_form${form_id_safe}_message').attr('class','tdomf_form_preview');
			document.getElementById('tdomf_form${form_id_safe}_message').innerHTML = message;
            jQuery('#tdomf_form${form_id_safe}_message').focus();
            var offset = jQuery('#tdomf_form${form_id_safe}_message').offset();
            window.scrollTo(offset.left,offset.top);
		} else {
            jQuery('#tdomf_form${form_id_safe}_message').attr('class','tdomf_form_message');
			document.getElementById('tdomf_form${form_id_safe}_message').innerHTML = message;
            var offset = jQuery('#tdomf_form${form_id_safe}_message').offset();
            window.scrollTo(offset.left,offset.top);
            jQuery('#tdomf_form${form_id_safe}_message').focus();
		}
		ajaxProgressStop$form_id_safe();
	}
	function tdomfRedirect$form_id_safe(url) {
		//ajaxProgressStop$form_id_safe();
		window.location = url;
	}
	//]] -->
</script>
EOT;
    if($hack) {
        $form .= "\n<!-- AJAX js end -->\n<!-- shadow required for disabling form during AJAX submit -->\n";
    }
    $form .= "<div id='shadow$form_id_safe' class='tdomf_shadow'></div>\n";
    if($hack) {
        $form .= "<!-- ajaxProgress holds the HTML to show during AJAX busy -->\n";
    }
    $form .= "<div id='ajaxProgress$form_id_safe' class='hidden'>".__('Please wait a moment while your submission is processed...','tdomf')."</div>\n";
    if(!$hack) {
        $form .= "<div id='tdomf_form${form_id_safe}_message' class='hidden'></div>";
    }
  } 
  
  $post_args = array();
  if(!$hack) {
     if(isset($form_data['tdomf_form_post_'.$form_id])) {
        $post_args = $form_data['tdomf_form_post_'.$form_id];
        unset($form_data['tdomf_form_post_'.$form_id]);
        tdomf_save_form_data($form_id,$form_data);
        if(isset($post_args['tdomf_post_message_'.$form_id])) {
           $form = $post_args['tdomf_post_message_'.$form_id];
           unset($form_data['tdomf_post_message_'.$form_id]);
           tdomf_save_form_data($form_id,$form_data);
        }
        if(isset($post_args['tdomf_no_form_'.$form_id])) {
           unset($post_args['tdomf_no_form_'.$form_id]);
           tdomf_save_form_data($form_id,$form_data);
           return $form;
        }
     }
  } else {
      $form .= TDOMF_MACRO_FORMMESSAGE."\n";
  }
  
  if($hack) {
        $form .= "<!-- form start -->\n";
  }
     
  $form .= "<form method=\"post\" action=\"".TDOMF_URLPATH."tdomf-form-post.php\" id='$form_name' name='$form_name' class='tdomf_form' >\n";
   
  // generate key
  //
  if($hack) {
      $form .= "\t".TDOMF_MACRO_FORMKEY."\n";
  } else {
      $form .= tdomf_generate_form_key($form_id);
  } 
  
  // Form id
  //
  if($hack) {
      $form .= "\t<div><input type='hidden' id='tdomf_form_id' name='tdomf_form_id' value='".TDOMF_MACRO_FORMID."' /></div>\n";
      if($edit) {
          $form .= "\t<div><input type='hidden' id='tdomf_form_id' name='tdomf_post_id' value='".TDOMF_MACRO_POSTID."' /></div>\n";
      }
  } else {
      $form .= "\t<div><input type='hidden' id='tdomf_form_id' name='tdomf_form_id' value='$form_id' /></div>\n";
      if($edit) {
          $form .= "\t<div><input type='hidden' id='tdomf_form_id' name='tdomf_post_id' value='$post_id' /></div>\n";
      }
  }
  
  if($hack) {
      $redirect_url = TDOMF_MACRO_FORMURL;
  } else {
      # use message id as re-direct because we *know* where this will appear on a non-hacked form
      #$redirect_url = $_SERVER['REQUEST_URI'].'#tdomf_form'.$form_id;
      $redirect_url =  esc_url( $_SERVER['REQUEST_URI']."#tdomf_form${form_id_safe}_message" );
  }
  $form .= "\t<div><input type='hidden' id='redirect' name='redirect' value='$redirect_url' /></div>\n";
  
  // Process widgets
  //
  
  if($hack) {
      $widget_args = array( "before_widget"=>"\t<fieldset>\n",
                            "after_widget"=>"\n\t</fieldset>\n",
                             "before_title"=>"\t\t<legend>",
                             "after_title"=>"</legend>\n",
                             "tdomf_form_id"=>$form_id,
                             "mode"=>$mode);
      $form .= "\t<!-- widgets start -->\n";
      $widget_order = tdomf_get_widget_order($form_id);
      foreach($widget_order as $w) {
          if(!isset($widgets[$w])) {
              $form .= "\t%%WIDGET:$w%%\n";
          } else {
              $form .= "\t<!-- $w start -->\n";
              $form .= call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
              $form .= "\t<!-- $w end -->\n";
          }
      }
      $form .= "\t<!-- widgets end -->\n";
  } else {
      $widget_args = array_merge( array( "before_widget"=>"<fieldset>\n",
                                           "after_widget"=>"\n</fieldset>\n",
                                           "before_title"=>"<legend>",
                                           "after_title"=>"</legend>",
                                           "tdomf_form_id"=>$form_id,
                                           "mode"=>$mode),
                                    $post_args);
      if($edit) {
          if(!isset($widget_args['tdomf_post_id'])) {
              $widget_args['tdomf_post_id'] = $post_id;
          }
          // old way
          if(!isset($widget_args['post_ID'])) {
              $widget_args['post_ID'] = $post_id;
          }
      }
      $widget_order = tdomf_get_widget_order($form_id);
      foreach($widget_order as $w) {
          if(isset($widgets[$w])) {
              $form .= call_user_func($widgets[$w]['cb'],$widget_args,$widgets[$w]['params']);
          }
      }
  }
  
  // Form buttons
  //
  if($hack) {
        $form .= "\t<!-- form buttons start -->\n";
  }  
  $form .= "\t<table class='tdomf_buttons'><tr>\n";
  if(tdomf_widget_is_preview_avaliable($form_id)) {
     $form .= "\t\t".'<td><input type="submit" value="'.__("Preview","tdomf").'" name="tdomf_form'.$form_id_safe.'_preview" id="tdomf_form'.$form_id_safe.'_preview"';
     if($use_ajax) {
         $form .= ' onclick="tdomfSubmit'.$form_id_safe."('preview'); return false;\"";
     }
     $form .= "/></td>\n";
  }
  $form .= "\t\t".'<td><input type="submit" value="'.__("Send","tdomf").'" name="tdomf_form'.$form_id_safe.'_send" id="tdomf_form'.$form_id_safe.'_send"';
  if($use_ajax) {
      $form .= ' onclick="tdomfSubmit'.$form_id_safe."('post'); return false;\"";
  }
  $form .= "/></td>\n";
  $form .= "\t</tr></table>\n";
  if($hack) {
        $form .= "\t<!-- form buttons end -->\n";
  }

  $form .= "</form>\n";

  if($hack) {
      $form .= "<!-- form end -->\n<!-- Form $form_id end -->\n";
  }
  
  return $form;
}

// Replaces <!--tdomf_formX--> or [tdomf_formX] with actual form
//
function tdomf_form_filter($content=''){
   if ('' == $content ||
       (preg_match('|<!--tdomf_form.*-->|', $content) <= 0 && preg_match('|\[tdomf_form.*\]|', $content) <= 0)) {
   	return $content;
   }

   $forms = array();
   if(preg_match_all('|<!--tdomf_form.*-->|', $content, $matches) > 0) {
     foreach($matches[0] as $match) {
       $match = str_replace('<!--tdomf_form','',trim($match));
       $match = intval(str_replace('-->','',$match));
       if(!isset($forms[$match])){
         $forms[$match] = tdomf_generate_form($match);
       }
     }
   }

   if(preg_match_all('|\[tdomf_form.*\]|', $content, $matches) > 0) {
     foreach($matches[0] as $match) {
       $match = str_replace('[tdomf_form','',trim($match));
       $match = intval(str_replace(']','',$match));
       if(!isset($forms[$match])){
         $forms[$match] = tdomf_generate_form($match);
       }
     }
   }

   foreach($forms as $id => $form ) {
     $content = preg_replace('|<!--tdomf_form$id-->|', '[tdomf_form$id]', $content);
     // prep form: the $ and \\ are special operators in preg_replace replacement string
     $form = str_replace('$','\\$',$form);
     $form = str_replace('\\\\','\\\\\\\\',$form);
     // make sure to swallow paragraph markers as well so the form is valid xhtml
     $content = preg_replace("|(<p>)*(\n)*\[tdomf_form$id\](\n)*(</p>)*|", $form, $content);
   }

   return $content;
}
add_filter('the_content', 'tdomf_form_filter');

function tdomf_get_form_data($form_id) {
   $type = get_option(TDOMF_OPTION_FORM_DATA_METHOD);
   if($type == "session") {
      if(!isset($_SESSION)) { @session_start(); }
      if(!isset($_SESSION)) {
         headers_sent($filename,$linenum);
         tdomf_log_message( "session_start() has not been called before generating form! Form will not work.",TDOMF_LOG_ERROR);
         if(!get_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES)) { ?>
            <p><font color=\"red\"><b>
            <?php _e('ERROR: <a href="http://www.google.com/search?client=opera&rls=en&q=php+session_start&sourceid=opera&ie=utf-8&oe=utf-8">session_start()</a> has not been called yet!',"tdomf"); ?>
            </b> <?php _e('This may be due to...','tdomf'); ?>
            <ol> <?php
            if ( !defined('WP_USE_THEMES') || !constant('WP_USE_THEMES') ) { ?>
              <li>
              <?php printf(__('Your theme does not use the get_header template tag. You can confirm this by using the default or classic Wordpress theme and seeing if this error appears. If it does not use get_header, then you must call session_start at the beginning of %s.',"tdomf"),$filename); ?>
              </li> <?php
            } ?> 
            <li>
            <?php printf(__('Another Plugin conflicts with TDOMF. To confirm this, disable all your plugins and then renable only TDOMF. If this error disappears than another plugin is causing the problem.',"tdomf"),$filename); ?>
            </li>
            </li></ol></font></p> <?php
         }
     }
     if(ini_get('register_globals')  && !TDOMF_HIDE_REGISTER_GLOBAL_ERROR){
       if(!get_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES)) { ?>
         <p><font color="red"><b>
         <?php _e('ERROR: <a href="http://ie2.php.net/register_globals"><i>register_globals</i></a> is enabled in your PHP environment!',"tdomf"); ?>
         </font></p>
       <?php }
      tdomf_log_message('register_globals is enabled!',TDOMF_LOG_ERROR);
     }
     if(isset($_SESSION['tdomf_form_data_'.$form_id])) { 
        return $_SESSION['tdomf_form_data_'.$form_id];
     } else {
        return array();
     }
   } else if($type == "db") {
     $data = tdomf_session_get();
     if(!is_array($data)) { return array(); }
     return $data;
   }
   tdomf_log_message("Invalid option set for FORM DATA METHOD: $type",TDOMF_LOG_ERROR);
   return array();
}

function tdomf_save_form_data($form_id,$form_data) {
   $type = get_option(TDOMF_OPTION_FORM_DATA_METHOD);
   if($type == "session") {
      $_SESSION['tdomf_form_data_'.$form_id] = $form_data;
   } else if($type == "db") {
      tdomf_session_set(0,$form_data);
   }
}

function tdomf_generate_default_form_mode($form_id) {
    if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id)) {
        $mode = 'edit';
    } else {
        $mode = 'new';
    }
    if(tdomf_get_option_form(TDOMF_OPTION_SUBMIT_PAGE,$form_id)) {
        $mode .= '-page';
    } else {
        $mode .= '-post';
    }
    if(tdomf_widget_is_ajax_avaliable($form_id)) {
        $mode .= '-ajax';
    }
    return $mode;
}

function tdomf_post_delete_cleanup($post_id) {
    
    tdomf_log_message("Post with id $post_id is being deleted");
    $edits = tdomf_get_edits(array('post_id' => $post_id));
    if(!empty($edits) && is_array($edits)) {
        $edit_ids = array();
        foreach($edits as $edit) {
            $edit_ids [] = $edit->edit_id;
        }
        tdomf_log_message("Deleting associated edits " . implode(", ",$edit_ids));
        tdomf_delete_edits($edit_ids);
    }
}
add_action('delete_post', 'tdomf_post_delete_cleanup');

// With the latest Wordpress (2.8.5), Custom Fields for revisions are no longer
// stored, so the custom field widget hacks a bit and adds the field to the 
// revision instead of the post (bypassing this check)
//
// However, we need to copy the Custom Fields on a revision to the 
// main post when a revision is restored
//
function tdomf_revision_restore_action($post_ID,$revision_ID) {
    // the custom fields list is only on the main post
    $customFields = get_post_meta($post_ID, TDOMF_KEY_CUSTOM_FIELDS, true);
    tdomf_log_message('tdomf_revision_restore_action(' . $post_ID . ', ' . $revision_ID .'): <pre>' . htmlentities(var_export($customFields,true)) . '</pre>');
    if(!empty($customFields) && is_array($customFields)) {
            foreach ( $customFields as $key => $title ) {
                // as of WP 2.8.5, get_post_meta works on revisions as well as
                // post ids
                $value = get_post_meta($revision_ID, $key, true);
                update_post_meta($post_ID, $key, $value);
                tdomf_log_message('tdomf_revision_restore_action(' . $post_ID . ', ' . $revision_ID .'): updating ' . $key . ' with value <pre>' . htmlentities(var_export($value,true)) . '</pre>' );
            }
    }
}
add_action('wp_restore_post_revision', 'tdomf_revision_restore_action',10,2);

// @todo Do we need to support wp_delete_post_revision?

// Create a random string!
// Taken from http://www.tutorialized.com/view/tutorial/PHP-Random-String-Generator/13903
//
function tdomf_random_string($length)
{
    // Error check input
    //
    if($length > 32) { $length = 32; }
    if($length <= 0) { $length = 1; }
  
    // Generate random 32 charecter string
    $string = md5(time());

    // Position Limiting
    $highest_startpoint = 32-$length;

    // Take a random starting point in the randomly
    // Generated String, not going any higher then $highest_startpoint
    $tdomf_random_string = substr($string,rand(0,$highest_startpoint),$length);

    return $tdomf_random_string;

}

?>
