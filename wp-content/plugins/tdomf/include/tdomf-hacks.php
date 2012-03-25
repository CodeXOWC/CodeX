<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/////////////////////////////////////////////////////
// Workarounds and hacks required by TDOMF to work // 
/////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////
// There is a "bug" in wordpress if you publish a post using the
// edit menu, the author cannot be a user so if your user is a subscriber,
// it will become the person who published it. This is the only way to
// fix it without hacking the code base. You can avoid using this hack by
// using the modify author tag option.
//
function tdomf_auto_fix_authors() {
  global $wpdb;
  if(get_option(TDOMF_AUTO_FIX_AUTHOR)) {
    // grab posts
    $query = "SELECT ID, post_author, meta_value ";
    $query .= "FROM $wpdb->posts ";
    $query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
    $query .= "AND meta_value != post_author ";
    $query .= "ORDER BY ID DESC";
    $posts = $wpdb->get_results( $query );
    if(!empty($posts)) {
      $count = 0;
      foreach($posts as $post) {
        if($post->meta_value != $post->post_author && !empty($post->meta_value) && $post->meta_value > 0 ) {
          $count++;
          tdomf_log_message("Changing author (currently $post->post_author) on post $post->ID to submitter setting $post->meta_value.");
          echo $post->ID.", ";
          $postargs = array (
            "ID"             => $post->ID,
            "post_author"    => $post->meta_value,
          );
          wp_update_post($postargs);
        }
      }
      return $count;
    } else {
      return 0;
    }
  }
  return false;
}
// is this a good place to do it?
add_action("wp_head","tdomf_auto_fix_authors");


/////////////////////////////////////////////////////////////////////////
// Amazingly Wordpress does not use or call session_start so we have to
// do it *before* headers are sent. I just hope it doesn't conflict
// with other plugins, however the code shouldn't call session_start,
// if it's already been started
//
function tdomf_start_session() {
   
  // No need to start session for these templates
  if(is_robots() || is_feed() || is_trackback()) {
    return;
  }
  
  // internal session data
  //
  if(get_option(TDOMF_OPTION_FORM_DATA_METHOD) == 'db') {
     if(!tdomf_session_start()) {
        if(headers_sent($filename,$linenum)) { ?>
      <p><font color="red">
      <b><?php printf(__('TDOMF ERROR: Headers have already been sent in file %s on line %d before session could be setup could be called.',"tdomf"),$filename,$linenum); ?></b>
      <?php _e('This may be due to...','tdomf'); ?>
      <ul>
      <?php if ( !defined('WP_USE_THEMES') || !constant('WP_USE_THEMES') ) { ?>
        <li><?php _e("Another plugin inserting HTML before TDOMF's get_header action is activated. You can confirm this by disabling all your other plugins and checking if this error is still reported.","tdomf"); ?></li>
      <?php } ?>
        <li><?php _e('Your current wordpress theme inserting HTML before calling the template tag "get_header". This may be as simple as a blank new line. You can confirm this by using the default or classic Wordpress theme and seeing if this error appears. You can also check your theme where it calls "get_header".',"tdomf"); ?></li>
      </ul>
      </font></p>
    <?php 
        }
        tdomf_log_message("Headers are already sent before TDOMF could setup session_start in file $filename on line $linenum",TDOMF_LOG_ERROR);     
     }
     return;
  } 

  // Use session_start 

  if(!headers_sent() && !isset($_SESSION)) {
    session_start();
    return;
  } 
  
  if(headers_sent($filename,$linenum) && !isset($_SESSION)) { 
    if(!get_option(TDOMF_OPTION_DISABE_ERROR_MESSAGES)) {
    ?>
      <p><font color="red">
      <b><?php printf(__('TDOMF ERROR: Headers have already been sent in file %s on line %d before <a href="http://www.google.com/search?client=opera&rls=en&q=php+session_start&sourceid=opera&ie=utf-8&oe=utf-8">session_start()</a> could be called.',"tdomf"),$filename,$linenum); ?></b>
      <?php _e('This may be due to...','tdomf'); ?>
      <ul>
      <?php if ( !defined('WP_USE_THEMES') || !constant('WP_USE_THEMES') ) { ?>
        <li><?php _e("Another plugin inserting HTML before TDOMF's get_header action is activated. You can confirm this by disabling all your other plugins and checking if this error is still reported.","tdomf"); ?></li>
      <?php } ?>
        <li><?php _e('Your current wordpress theme inserting HTML before calling the template tag "get_header". This may be as simple as a blank new line. You can confirm this by using the default or classic Wordpress theme and seeing if this error appears. You can also check your theme where it calls "get_header".',"tdomf"); ?></li>
      </ul>
      </font></p>
    <?php 
    }
    tdomf_log_message("Headers are already sent before TDOMF could call session_start in file $filename on line $linenum",TDOMF_LOG_ERROR);
  }
}
// Depending on "get_header" was a nightmare. "template_redirect" is called 
// before any theme file is excuted, so technically it should work with *any*
// theme. But if themes aren't enabled, fall back to "get_header"?
if ( defined('WP_USE_THEMES') && constant('WP_USE_THEMES') ) {
   add_action("template_redirect","tdomf_start_session");
} else {
   add_action("get_header","tdomf_start_session");
}
//
// Add session_start to admin menus where we allow logged in users to submit!
//
add_action("load-users_page_tdomf_your_submissions","tdomf_start_session");
add_action("load-profile_page_tdomf_your_submissions","tdomf_start_session");

////////////////////////////////////////////////////////////////////////////////
// While you can modify the URL of an attachment to a post, you can't modify
// the URL to the thumbnail (if avaliable). Instead it tries to generate it by
// modifying the basename of the attachment URL and the filename! Bah. So have
// use filters to grab the right thumbnail!
//
function tdomf_upload_attachment_thumb_url($url,$post_ID) {
   $post_ID = intval($post_ID);
   if ( !$post =& get_post( $post_ID ) ) {
      return $url;
   }
   $parent_ID = $post->post_parent;
   $file_ID = $post->menu_order;
   if( !$thumb_path = get_post_meta($parent_ID,TDOMF_KEY_DOWNLOAD_THUMB.$file_ID,true)) {
      return $url;
   }
   return get_bloginfo('wpurl').'/?tdomf_download='.$parent_ID.'&id='.$file_ID.'&thumb';
}
add_filter( 'wp_get_attachment_thumb_url', 'tdomf_upload_attachment_thumb_url', 10, 2);

if ( ! function_exists('wp_notify_postauthor') ) {
    ////////////////////////////////////////////////////////////////////////////
    // There isn't, currently, a nice way to do this. You can't, on the fly, 
    // modify the "postauthor" email address, so must wrap this function and 
    // modify it this way...
    //
    function wp_notify_postauthor($comment_id, $comment_type='') {
        $comment = get_comment($comment_id);
        $post    = get_post($comment->comment_post_ID);
        $user    = get_userdata( $post->post_author );
    
        if ('' == $user->user_email) return false; // If there's no email to send the comment to
        
        $comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
    
        $blogname = get_option('blogname');
    
        // This little bit of code checks if the comment belongs to a post
        // submitted via TDOMF. If the author is set to the default TDOMF author
        // or the TDOMF submitter author does not have spam/delete rights on the
        // comment, then redirect email to author
        //
        $user_email = $user->user_email;
        if(get_post_meta($comment->comment_post_ID, TDOMF_KEY_FLAG, true) != false) {
            // default tdomf author
            if($post->post_author == get_option(TDOMF_DEFAULT_AUTHOR)) {
                tdomf_log_message("wp_notify_postauthor: Comment $comment_id action email is destined for default author. Redirecting to admin.");
                $user_email = get_option('admin_email');
            } else {
                // user must have the edit post right to delete or spam a comment
                $user_role = new WP_User($post->post_author);  
                if(!$user_role->has_cap('edit_post',$comment->comment_post_ID)) {
                    tdomf_log_message("wp_notify_postauthor: Comment $comment_id action email is destined for tdomf submitter with incorrect rights. Redirecting to admin.");
                    $user_email = get_option('admin_email');
                } else {
                    #tdomf_log_message("wp_notify_postauthor: Comment $comment_id action email is destined for tdomf submitter with correct rights.", TDOMF_LOG_GOOD);
                }
            }
            $user_email = get_option('admin_email');
        } else {
            #tdomf_log_message("wp_notify_postauthor: Comment $comment_id action email for non tdomf mail.");
        }
        
        if ( empty( $comment_type ) ) $comment_type = 'comment';
    
        if ('comment' == $comment_type) {
            $notify_message  = sprintf( __('New comment on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
            $notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
            $notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
            $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
            $notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
            $notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
            $notify_message .= __('You can see all comments on this post here: ') . "\r\n";
            $subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
        } elseif ('trackback' == $comment_type) {
            $notify_message  = sprintf( __('New trackback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
            $notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
            $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
            $notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
            $notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
            $subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
        } elseif ('pingback' == $comment_type) {
            $notify_message  = sprintf( __('New pingback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
            $notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
            $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
            $notify_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
            $notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
            $subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
        }
        $notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
        if(function_exists('admin_url')) {
           $notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=cdc&c=$comment_id") ) . "\r\n";
           $notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=cdc&dt=spam&c=$comment_id") ) . "\r\n";
        } else {
           $notify_message .= sprintf( __('Delete it: %s'), get_bloginfo('wpurl').'/wp-admin/comment.php?action=cdc&c=$comment_id' ) . "\r\n";
           $notify_message .= sprintf( __('Spam it: %s'), get_bloginfo('wpurl').'/comment.php?action=cdc&dt=spam&c=$comment_id' ) . "\r\n";
        }
    
        $wp_email = 'wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
    
        if ( '' == $comment->comment_author ) {
            $from = "From: \"$blogname\" <$wp_email>";
            if ( '' != $comment->comment_author_email )
                $reply_to = "Reply-To: $comment->comment_author_email";
        } else {
            $from = "From: \"$comment->comment_author\" <$wp_email>";
            if ( '' != $comment->comment_author_email )
                $reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
        }
    
        $message_headers = "$from\n"
            . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
    
        if ( isset($reply_to) )
            $message_headers .= $reply_to . "\n";
    
        $notify_message = apply_filters('comment_notification_text', $notify_message, $comment_id);
        $subject = apply_filters('comment_notification_subject', $subject, $comment_id);
        $message_headers = apply_filters('comment_notification_headers', $message_headers, $comment_id);
    
        @wp_mail($user_email, $subject, $notify_message, $message_headers);
    
        return true;
    }
}

?>
