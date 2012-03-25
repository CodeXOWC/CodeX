<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

function tdomf_is_id_spam($id) {
    $retValue = false;
    if(wp_is_post_revision($id)) {
        # todo: we need to grab the edit data associated with this revision id
        #       if there is none, it's not spam per say
        $retValue = false;
    } else {
        # if post id, we're checking against a new submission
        #
        $retValue = get_post_meta($id, TDOMF_KEY_SPAM);
    }
    return $retValue;
}

// @REF: http://akismet.com/development/api/

function tdomf_check_edit_spam($edit_id,$live=true) {
    
  // for debugging purposes
  if(TDOMF_DEBUG_FAKE_SPAM) {
    tdomf_set_state_edit('spam',$edit_id);
    return false;
  }
    
  if(!get_option(TDOMF_OPTION_SPAM)) {
    return true;
  }

  tdomf_cleanup_spam();  
  
  $akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(empty($akismet_key)) {
    tdomf_log_message("No Akismet key set, cannot query if edit $edit_id is spam!",TDOMF_LOG_ERROR);
    return true;
  }
  
  $edit = tdomf_get_edit($edit_id);
  if(empty($edit)) {
    tdomf_log_message("Invalid edit data for $edit_id. Can't check if spam",TDOMF_LOG_ERROR);
    return true;
  }
  
  $query_data = array();
  if(!empty($edit->ip)) {
     $query_data['user_ip'] = $edit->ip;
  }
  if(!isset($edit->data['HTTP_USER_AGENT'])) {
     $query_data['user_agent'] = $edit->data['HTTP_USER_AGENT'];
  }
  if(!isset($edit->data['HTTP_REFERER'])) {
     $query_data['referrer'] = $edit->data['HTTP_REFERER'];
  }
  $query_data['blog'] = get_option('home');
  $query_data['comment_type'] = 'edit-entry';
  
  if($edit->user_id != 0) {
    $user = get_userdata($edit->user_id);
    $query_data['comment_author_email'] = $user->user_email;
    if(!empty($user->user_url)) {
      $query_data['comment_author_url'] = $user->user_url;
    }
    $query_data['comment_author'] = $user->display_name;
  } else {
    if(isset($edit->data[TDOMF_KEY_NAME])) {
      $query_data['comment_author'] = $edit->data[TDOMF_KEY_NAME];
    }
    if(isset($edit->data[TDOMF_KEY_EMAIL])) {
      $query_data['comment_author_email'] = $edit->data[TDOMF_KEY_EMAIL];
    }
    if(isset($edit->data[TDOMF_KEY_WEB])) {
      $query_data['comment_author_url'] = $edit->data[TDOMF_KEY_WEB];
    }
  }
  
  if(TDOMF_DEBUG_AKISMET_FAKE_SPAM) {
    # test - should trigger spam response
    $query_data['comment_author'] = 'viagra-test-123';
  }  
  
  if($edit->revision_id == 0) {
     $post_data = wp_get_single_post($edit->post_id, ARRAY_A);
  } else {
     $post_data = wp_get_single_post($edit->revision_id, ARRAY_A);
  }
  $query_data['comment_content'] = $post_data['post_content'];
     
  if($live) {
     $ignore = array( 'HTTP_COOKIE' );
	   foreach ( $_SERVER as $key => $value )
	   if ( !in_array( $key, $ignore ) ) {
          $post_data["$key"] = $value;
     }
  }

  $query_string = '';
	foreach ( $query_data as $key => $data ) {
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
  }

  tdomf_log_message/*_extra*/("$akismet_key.rest.akismet.com/1.1/comment-check<br/>$query_string");
  $response = tdomf_akismet_send($query_string, $akismet_key.".rest.akismet.com", "/1.1/comment-check", 80);
  if ( 'false' == $response[1] ) {
    tdomf_log_message("Edit $edit_id is not spam (according to Akismet)",TDOMF_LOG_GOOD);
    return true;
  }

  $spam_count = get_option(TDOMF_STAT_SPAM);
  if($spam_count == false) { add_option(TDOMF_STAT_SPAM,1); }
  else { update_option(TDOMF_STAT_SPAM,$spam_count++); }
  if(!$live) {
      $edited_count = get_option(TDOMF_STAT_EDITED);
      update_option(TDOMF_STAT_EDITED,$edited_count--);
  }
  
  tdomf_set_state_edit('spam',$edit_id);
  tdomf_log_message("Edit $edit_id is <b>spam</b> (according to Akismet)<br/><pre>" . var_export($response,true) . "</pre>",TDOMF_LOG_BAD); 
  return false;
}

function tdomf_check_submissions_spam($post_id,$live=true) {

  // for debugging purposes
  if(TDOMF_DEBUG_FAKE_SPAM) {
    add_post_meta($post_id, TDOMF_KEY_SPAM, true, true);
    return false;
  }
    
  if(!get_option(TDOMF_OPTION_SPAM)) {
    return true;
  }

  tdomf_cleanup_spam();

  $akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(empty($akismet_key)) {
    tdomf_log_message("No Akismet key set, cannot query if post $post_id is spam!",TDOMF_LOG_ERROR);
    return true;
  }

  if ( !get_post( $post_id ) ) {
    tdomf_log_message("Post with ID $post_id does not exist!",TDOMF_LOG_ERROR);
    return false;
  }

  if(!get_post_meta($post_id,TDOMF_KEY_FLAG,true)) {
    tdomf_log_message("$post_id is not managed by TDOMF - will not check if spam!",TDOMF_LOG_BAD);
    return true;
  }

  $query_data = array();

  $query_data['user_ip'] = get_post_meta($post_id,TDOMF_KEY_IP,true);
	$query_data['user_agent'] = get_post_meta($post_id,TDOMF_KEY_USER_AGENT,true);
  $query_data['referrer'] = get_post_meta($post_id,TDOMF_KEY_REFERRER,true);
	$query_data['blog'] = get_option('home');
  $query_data['comment_type'] = 'new-submission';

  if(get_post_meta($post_id,TDOMF_KEY_USER_ID,true)) {
    $user = get_userdata(get_post_meta($post_id,TDOMF_KEY_USER_ID,true));
    $query_data['comment_author_email'] = $user->user_email;
    if(!empty($user->user_url)) {
      $query_data['comment_author_url'] = $user->user_url;
    }
    $query_data['comment_author'] = $user->display_name;
  } else {
    if(get_post_meta($post_id,TDOMF_KEY_NAME,true)) {
      $query_data['comment_author'] = get_post_meta($post_id,TDOMF_KEY_NAME,true);
    }
    if(get_post_meta($post_id,TDOMF_KEY_EMAIL,true)) {
      $query_data['comment_author_email'] = get_post_meta($post_id,TDOMF_KEY_EMAIL,true);
    }
    if(get_post_meta($post_id,TDOMF_KEY_WEB,true)) {
      $query_data['comment_author_url'] = get_post_meta($post_id,TDOMF_KEY_WEB,true);
    }
  }

  if(TDOMF_DEBUG_AKISMET_FAKE_SPAM) {
    # test - should trigger spam response
    $query_data['comment_author'] = 'viagra-test-123';
  }

  $post_data = wp_get_single_post($post_id, ARRAY_A);
  $query_data['comment_content'] = $post_data['post_content'];

  if($live) {
     $ignore = array( 'HTTP_COOKIE' );
	   foreach ( $_SERVER as $key => $value )
	   if ( !in_array( $key, $ignore ) ) {
          $post_data["$key"] = $value;
     }
  }

  $query_string = '';
	foreach ( $query_data as $key => $data ) {
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
  }

  tdomf_log_message_extra("$akismet_key.rest.akismet.com/1.1/comment-check<br/>$query_string");
  $response = tdomf_akismet_send($query_string, $akismet_key.".rest.akismet.com", "/1.1/comment-check", 80);
	if ( 'false' == $response[1] ) {
    tdomf_log_message("$post_id is not spam (according to Akismet)",TDOMF_LOG_GOOD);
    return true;
  }

  $spam_count = get_option(TDOMF_STAT_SPAM);
  if($spam_count == false) { add_option(TDOMF_STAT_SPAM,1); }
  else { update_option(TDOMF_STAT_SPAM,$spam_count++); }
  if(!$live) {
      // we're updating a post
      $submitted_count = get_option(TDOMF_STAT_SUBMITTED);
      update_option(TDOMF_STAT_SUBMITTED,$submitted_count--);
  }

  // Flag post as spam!
  //
  add_post_meta($post_id, TDOMF_KEY_SPAM, true, true);

  tdomf_log_message("$post_id is <b>spam</b> (according to Akismet)<br/><pre>" . var_export($response,true) . "</pre>",TDOMF_LOG_BAD);
  return false;
}

function tdomf_spam_edit($edit_id) {
  if(!get_option(TDOMF_OPTION_SPAM)) {
    return;
  }

  $akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(empty($akismet_key)) {
    tdomf_log_message("No Akismet key set, cannot submit spam for edit $edit_id!",TDOMF_LOG_ERROR);
    return;
  }

  $edit = tdomf_get_edit($edit_id);
  if(empty($edit)) {
    tdomf_log_message("Invalid edit data for $edit_id. Can't check if spam",TDOMF_LOG_ERROR);
    return true;
  }

  if($edit->state == 'spam') {
    tdomf_log_message("Edit $edit_id is already set as spam!",TDOMF_LOG_BAD);
    return;
  }

  $query_data = array();
  if(!empty($edit->ip)) {
     $query_data['user_ip'] = $edit->ip;
  }
  if(!isset($edit->data['HTTP_USER_AGENT'])) {
     $query_data['user_agent'] = $edit->data['HTTP_USER_AGENT'];
  }
  if(!isset($edit->data['HTTP_REFERER'])) {
     $query_data['referrer'] = $edit->data['HTTP_REFERER'];
  }
  $query_data['blog'] = get_option('home');
  $query_data['comment_type'] = 'edit-entry';
  
  if($edit->user_id != 0) {
    $user = get_userdata($edit->user_id);
    $query_data['comment_author_email'] = $user->user_email;
    if(!empty($user->user_url)) {
      $query_data['comment_author_url'] = $user->user_url;
    }
    $query_data['comment_author'] = $user->display_name;
  } else {
    if(isset($edit->data[TDOMF_KEY_NAME])) {
      $query_data['comment_author'] = $edit->data[TDOMF_KEY_NAME];
    }
    if(isset($edit->data[TDOMF_KEY_EMAIL])) {
      $query_data['comment_author_email'] = $edit->data[TDOMF_KEY_EMAIL];
    }
    if(isset($edit->data[TDOMF_KEY_WEB])) {
      $query_data['comment_author_url'] = $edit->data[TDOMF_KEY_WEB];
    }
  }

  # test - should trigger spam response
  #$query_data['comment_author'] = 'viagra-test-123';

  if($edit->revision_id == 0) {
     $post_data = wp_get_single_post($edit->post_id, ARRAY_A);
  } else {
     $post_data = wp_get_single_post($edit->revision_id, ARRAY_A);
  }
  $query_data['comment_content'] = $post_data['post_content'];

  $query_string = '';
	foreach ( $query_data as $key => $data ) {
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
  }

  tdomf_log_message_extra("$akismet_key.rest.akismet.com/1.1/comment-check<br/>$query_string");
  $response = tdomf_akismet_send($query_string, $akismet_key.".rest.akismet.com", "/1.1/submit-spam", 80);

  // Flag edit as spam!
  //
  tdomf_set_state_edit('spam',$edit_id);

  $spam_count = get_option(TDOMF_STAT_SPAM);
  if($spam_count == false) { add_option(TDOMF_STAT_SPAM,1); }
  else { update_option(TDOMF_STAT_SPAM,$spam_count++); }

  $edited_count = get_option(TDOMF_STAT_EDITED);
  if($edited_count == false) { add_option(TDOMF_STAT_EDITED,0); }
  else { update_option(TDOMF_STAT_EDITED,$edited_count--); }    
  
  tdomf_log_message("Edit $edit_id has been submitted as spam to Akismet)<br/><pre>" . var_export($response,true) . "</pre>");
}

function tdomf_ham_edit($edit_id) {
  if(!get_option(TDOMF_OPTION_SPAM)) {
    return;
  }

  $akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(empty($akismet_key)) {
    tdomf_log_message("No Akismet key set, cannot submit spam for edit $edit_id!",TDOMF_LOG_ERROR);
    return;
  }

  $edit = tdomf_get_edit($edit_id);
  if(empty($edit)) {
    tdomf_log_message("Invalid edit data for $edit_id. Can't check if spam",TDOMF_LOG_ERROR);
    return true;
  }

  if($edit->state != 'spam') {
    tdomf_log_message("Edit $edit_id is not spam!",TDOMF_LOG_BAD);
    return;
  }

  $query_data = array();
  if(!empty($edit->ip)) {
     $query_data['user_ip'] = $edit->ip;
  }
  if(!isset($edit->data['HTTP_USER_AGENT'])) {
     $query_data['user_agent'] = $edit->data['HTTP_USER_AGENT'];
  }
  if(!isset($edit->data['HTTP_REFERER'])) {
     $query_data['referrer'] = $edit->data['HTTP_REFERER'];
  }
  $query_data['blog'] = get_option('home');
  $query_data['comment_type'] = 'edit-entry';
  
  if($edit->user_id != 0) {
    $user = get_userdata($edit->user_id);
    $query_data['comment_author_email'] = $user->user_email;
    if(!empty($user->user_url)) {
      $query_data['comment_author_url'] = $user->user_url;
    }
    $query_data['comment_author'] = $user->display_name;
  } else {
    if(isset($edit->data[TDOMF_KEY_NAME])) {
      $query_data['comment_author'] = $edit->data[TDOMF_KEY_NAME];
    }
    if(isset($edit->data[TDOMF_KEY_EMAIL])) {
      $query_data['comment_author_email'] = $edit->data[TDOMF_KEY_EMAIL];
    }
    if(isset($edit->data[TDOMF_KEY_WEB])) {
      $query_data['comment_author_url'] = $edit->data[TDOMF_KEY_WEB];
    }
  }

  # test - should trigger spam response
  #$query_data['comment_author'] = 'viagra-test-123';

  if($edit->revision_id == 0) {
     $post_data = wp_get_single_post($edit->post_id, ARRAY_A);
  } else {
     $post_data = wp_get_single_post($edit->revision_id, ARRAY_A);
  }
  $query_data['comment_content'] = $post_data['post_content'];

  $query_string = '';
	foreach ( $query_data as $key => $data ) {
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
  }

  tdomf_log_message_extra("$akismet_key.rest.akismet.com/1.1/comment-check<br/>$query_string");
  $response = tdomf_akismet_send($query_string, $akismet_key.".rest.akismet.com", "/1.1/submit-ham", 80);

  // unflag spam
  //
  tdomf_set_state_edit('unapproved',$edit_id);

  $spam_count = get_option(TDOMF_STAT_SPAM);
  if($spam_count == false) { add_option(TDOMF_STAT_SPAM,0); }
  else { update_option(TDOMF_STAT_SPAM,$spam_count--); }

  $edited_count = get_option(TDOMF_STAT_EDITED);
  if($edited_count == false) { add_option(TDOMF_STAT_EDITED,1); }
  else { update_option(TDOMF_STAT_EDITED,$edited_count++); }

  tdomf_log_message("Edit $edit_id has been submitted as ham to Akismet<br/><pre>" . var_export($response,true) . "</pre>");
}

function tdomf_spam_post($post_id) {
    
  if(!get_option(TDOMF_OPTION_SPAM)) {
    return;
  }

  $akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(empty($akismet_key)) {
    tdomf_log_message("No Akismet key set, cannot submit spam for $post_id!",TDOMF_LOG_ERROR);
    return;
  }

  if ( !get_post( $post_id ) ) {
    tdomf_log_message("Post with ID $post_id does not exist!",TDOMF_LOG_ERROR);
    return;
  }

  if(!get_post_meta($post_id,TDOMF_KEY_FLAG,true)) {
    tdomf_log_message("$post_id is not managed by TDOMF - will not submit as spam!",TDOMF_LOG_BAD);
    return;
  }

  if(get_post_meta($post_id,TDOMF_KEY_SPAM,true)) {
    tdomf_log_message("$post_id is already set as spam!",TDOMF_LOG_BAD);
    return;
  }

  $query_data = array();
  if(!empty($edit->ip)) {
     $query_data['user_ip'] = $edit->ip;
  }
  if(!isset($edit->data['HTTP_USER_AGENT'])) {
     $query_data['user_agent'] = $edit->data['HTTP_USER_AGENT'];
  }
  if(!isset($edit->data['HTTP_REFERER'])) {
     $query_data['referrer'] = $edit->data['HTTP_REFERER'];
  }
  $query_data['blog'] = get_option('home');
  $query_data['comment_type'] = 'edit-entry';
  
  if($edit->user_id != 0) {
    $user = get_userdata($edit->user_id);
    $query_data['comment_author_email'] = $user->user_email;
    if(!empty($user->user_url)) {
      $query_data['comment_author_url'] = $user->user_url;
    }
    $query_data['comment_author'] = $user->display_name;
  } else {
    if(isset($edit->data[TDOMF_KEY_NAME])) {
      $query_data['comment_author'] = $edit->data[TDOMF_KEY_NAME];
    }
    if(isset($edit->data[TDOMF_KEY_EMAIL])) {
      $query_data['comment_author_email'] = $edit->data[TDOMF_KEY_EMAIL];
    }
    if(isset($edit->data[TDOMF_KEY_WEB])) {
      $query_data['comment_author_url'] = $edit->data[TDOMF_KEY_WEB];
    }
  }

  # test - should trigger spam response
  #$query_data['comment_author'] = 'viagra-test-123';

  $post_data = wp_get_single_post($post_id, ARRAY_A);
  $query_data['comment_content'] = $post_data['post_content'];

  $query_string = '';
	foreach ( $query_data as $key => $data ) {
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
  }

  tdomf_log_message_extra("$akismet_key.rest.akismet.com/1.1/comment-check<br/>$query_string");
  $response = tdomf_akismet_send($query_string, $akismet_key.".rest.akismet.com", "/1.1/submit-spam", 80);

  // Flag post as spam!
  //
  add_post_meta($post_id, TDOMF_KEY_SPAM, true, true);

  $spam_count = get_option(TDOMF_STAT_SPAM);
  if($spam_count == false) { add_option(TDOMF_STAT_SPAM,1); }
  else { update_option(TDOMF_STAT_SPAM,$spam_count++); }

  $submitted_count = get_option(TDOMF_STAT_SUBMITTED);
  if($submitted_count == false) { add_option(TDOMF_STAT_SUBMITTED,0); }
  else { update_option(TDOMF_STAT_SUBMITTED,$submitted_count--); }  
  
  tdomf_log_message("$post_id has been submitted as spam to Akismet)<br/><pre>" . var_export($response,true) . "</pre>");
}

function tdomf_ham_post($post_id) {
  if(!get_option(TDOMF_OPTION_SPAM)) {
    return;
  }

  $akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(empty($akismet_key)) {
    tdomf_log_message("No Akismet key set, cannot submit ham for $post_id!",TDOMF_LOG_ERROR);
    return;
  }

  if ( !get_post( $post_id ) ) {
    tdomf_log_message("Post with ID $post_id does not exist!",TDOMF_LOG_ERROR);
    return;
  }

  if(!get_post_meta($post_id,TDOMF_KEY_FLAG,true)) {
    tdomf_log_message("$post_id is not managed by TDOMF - will not submit as ham!",TDOMF_LOG_BAD);
    return;
  }

  if(!get_post_meta($post_id,TDOMF_KEY_SPAM,true)) {
    tdomf_log_message("$post_id is not set as spam!",TDOMF_LOG_BAD);
    return;
  }

  $query_data = array();

  $query_data['user_ip'] = get_post_meta($post_id,TDOMF_KEY_IP,true);
	$query_data['user_agent'] = get_post_meta($post_id,TDOMF_KEY_USER_AGENT,true);
  $query_data['referrer'] = get_post_meta($post_id,TDOMF_KEY_REFERRER,true);
	$query_data['blog'] = get_option('home');
  $query_data['comment_type'] = 'new-submission';

  if(get_post_meta($post_id,TDOMF_KEY_USER_ID,true)) {
    $user = get_userdata(get_post_meta($post_id,TDOMF_KEY_USER_ID,true));
    $query_data['comment_author_email'] = $user->user_email;
    if(!empty($user->user_url)) {
      $query_data['comment_author_url'] = $user->user_url;
    }
    $query_data['comment_author'] = $user->display_name;
  } else {
    if(get_post_meta($post_id,TDOMF_KEY_NAME,true)) {
      $query_data['comment_author'] = get_post_meta($post_id,TDOMF_KEY_NAME,true);
    }
    if(get_post_meta($post_id,TDOMF_KEY_EMAIL,true)) {
      $query_data['comment_author_email'] = get_post_meta($post_id,TDOMF_KEY_EMAIL,true);
    }
    if(get_post_meta($post_id,TDOMF_KEY_WEB,true)) {
      $query_data['comment_author_url'] = get_post_meta($post_id,TDOMF_KEY_WEB,true);
    }
  }

  # test - should trigger spam response
  #$query_data['comment_author'] = 'viagra-test-123';

  $post_data = wp_get_single_post($post_id, ARRAY_A);
  $query_data['comment_content'] = $post_data['post_content'];

  /*if($live) {
     $ignore = array( 'HTTP_COOKIE' );
	   foreach ( $_SERVER as $key => $value )
	   if ( !in_array( $key, $ignore ) ) {
          $post_data["$key"] = $value;
     }
  }*/

  $query_string = '';
	foreach ( $query_data as $key => $data ) {
		$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
  }

  tdomf_log_message_extra("$akismet_key.rest.akismet.com/1.1/comment-check<br/>$query_string");
  $response = tdomf_akismet_send($query_string, $akismet_key.".rest.akismet.com", "/1.1/submit-ham", 80);

  // unflag spam
  //
  delete_post_meta($post_id, TDOMF_KEY_SPAM);

  $spam_count = get_option(TDOMF_STAT_SPAM);
  if($spam_count == false) { add_option(TDOMF_STAT_SPAM,0); }
  else { update_option(TDOMF_STAT_SPAM,$spam_count--); }

  $submitted_count = get_option(TDOMF_STAT_SUBMITTED);
  if($submitted_count == false) { add_option(TDOMF_STAT_SUBMITTED,1); }
  else { update_option(TDOMF_STAT_SUBMITTED,$submitted_count++); }

  tdomf_log_message("$post_id has been submitted as ham to Akismet<br/><pre>" . var_export($response,true) . "</pre>");
}

function tdomf_akismet_key_verify($key) {
	$blog = urlencode( get_option('home') );
	$response = tdomf_akismet_send("key=$key&blog=$blog", 'rest.akismet.com', '/1.1/verify-key', 80);
	if ( 'valid' == $response[1] ) {
    tdomf_log_message("Key $key is accepted by Akismet",TDOMF_LOG_GOOD);
		return true;
  } else {
    tdomf_log_message("Key $key has been rejected by Akismet: <br/><pre>" . var_export($response,true) . "</pre>",TDOMF_LOG_BAD);
		return false;
  }
}

// Hacked from the akismet version
//
function tdomf_akismet_send($request, $host, $path, $port = 80, $proxy = false) {
  global $wp_version;

 $proxy = false;
  
  // adding proxy support here because my host uses a proxy!
  if ( defined('WP_PROXY_HOST')  && defined('WP_PROXY_PORT') ) {
     $proxy = WP_PROXY_HOST;
     $port = WP_PROXY_PORT;
  }
  


  $ksd_user_agent = "WordPress/$wp_version | TDO-Mini-Forms/".TDOMF_VERSION;

  if($proxy) {
    $http_request  = "POST http://$host$path HTTP/1.0\r\n";
    $http_request .= "Host: http://$host\r\n";
  } else {
     $http_request  = "POST $path HTTP/1.0\r\n";
     $http_request .= "Host: $host\r\n";
  }
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_settings('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= "User-Agent: $ksd_user_agent\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;

	$response = '';
  if($proxy) {
    $fs = @fsockopen($proxy, $port, $errno, $errstr, 3);
  } else {
    $fs = @fsockopen($host, $port, $errno, $errstr, 3);
  }
	if( false !== $fs ) {
		fwrite($fs, $http_request);
		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}
	return $response;
}

function tdomf_cleanup_spam() {
   global $wpdb;

   if(!get_option(TDOMF_OPTION_SPAM_AUTO_DELETE)) { return; }

   // delete edit spam older than a month

   $edit_list = '';
   $time_diff = tdomf_timestamp_wp_sql(time() - 2592000); // 1 month in seconds
   $edits = tdomf_get_edits(array('state' => 'spam',
                                  'older_than' => $time_diff));
   if(count($edits) > 0) {
      foreach($edits as $edit) {
         $edit_list [] = $edit->edit_id;
         if($edit->revision_id != 0) {
            wp_delete_post_revision( $edit->revision_id );
         }
         if($edit->current_revision_id != 0) {
            wp_delete_post_revision( $last_edit[0]->current_revision_id );
         }
      }
      tdomf_delete_edits($edit_list);
      tdomf_log_message("Deleting spam edits older than a month: " . implode(",", $edit_list));
   }

   // delete spam more than a month old

   $query = "SELECT ID, post_modified_gmt
             FROM $wpdb->posts
             LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
             WHERE meta_key = '".TDOMF_KEY_SPAM."'";
   $spam_posts = $wpdb->get_results( $query );

   if(count($spam_posts) > 0) {
       $list = "";
       foreach($spam_posts as $post) {

           // we use to use post_modified_gmt but since 2.6 or 2.7 this is
           // no longer set when the post is initially created in draft
           //
        
           $post_date_gmt = get_post_meta($post->ID, TDOMF_KEY_SUBMISSION_DATE, true);
           if($post_date_gmt != false) {
              $post_date_ts = mysql2date('U',$post_date_gmt);      
              $diff = time() - $post_date_ts;
              if($diff >= 2952000) { // 1 month (30 days)
                 $list .= $post->ID.", ";
                 wp_delete_post($post->ID);
              }
              tdomf_log_message($post->ID . ' ' . $post_data_ts . ' ' . $diff);
           } else {

              // old way
              //
              $last_updated = strtotime( $post->post_modified_gmt );
              $diff = time() - $last_updated;
              if($diff >= 2952000) { // 1 month (30 days)
                $list .= $post->ID.", ";
                wp_delete_post($post->ID);
             }
           }
       }
       if($list != "") {
          tdomf_log_message("Deleting spam posts older than a month: $list");
       }
   } else {
       #tdomf_log_message("No spam submissions to clean up!",TDOMF_LOG_GOOD);
   }
}
?>
