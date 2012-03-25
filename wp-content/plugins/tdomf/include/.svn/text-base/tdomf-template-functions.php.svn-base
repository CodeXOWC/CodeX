<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

///////////////////////////////////////
// Template Tags and other functions //
///////////////////////////////////////

/////////////////////////////////////////////
// Check if current user can access the form!
//
function tdomf_can_current_user_see_form($form_id = 1, $post_id = false) {
  
  // Cheat! If the message is NULL, no error was generated. This function is
  // kept much more up to date than this one, so all good.
  
  $message = tdomf_check_permissions_form($form_id,$post_id);
  if($message == NULL) {
      return true;
  }
  return false;
}

//////////////////////////////////////
// Get the form 
//
function tdomf_get_the_form($form_id = 1,$post_id = false) {
  return tdomf_generate_form($form_id,false,$post_id);
}

//////////////////////////////////////
// Display the Form
//
function tdomf_the_form($form_id = 1,$post_id = false) {
  echo tdomf_get_the_form($form_id,$post_id);
}

//////////////////////////////////////
// Get the submitter of the post
//
function tdomf_get_the_submitter($post_id = 0){
  global $post;
  if($post_id == 0 && isset($post)) { $post_id = $post->ID; }
  else if($post_id == 0){ return ""; }

  $flag = get_post_meta($post_id, TDOMF_KEY_FLAG, true);
  if(!empty($flag)) {
     $submitter_user_id = get_post_meta($post_id, TDOMF_KEY_USER_ID, true);
     if(!empty($submitter_user_id) && $submitter_user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
        $user = get_userdata($submitter_user_id);
        if(isset($user)) {
          $retValue = "";
          // bit of a crappy hack to make sure that if it's only "http://" it isn't printed
          $web_url = trim($user->user_url);
          if(strlen($web_url) < 8 || strpos($web_url, "http://", 0) !== 0 ) {
            $web_url = "";
          }
          if(!empty($web_url)) {
            $retValue .= "<a href=\"$web_url\" rel=\"nofollow\">";
          }
          $retValue .= $user->display_name;
          if(!empty($web_url)) {
            $retValue .= "</a>";
          }
          return $retValue;
        } else {
          #return "{ ERROR: bad submitter id for this post }";
          return "";
        }
     } else {
        $submitter_web = get_post_meta($post_id, TDOMF_KEY_WEB, true);
        $submitter_name = get_post_meta($post_id, TDOMF_KEY_NAME, true);
        if(empty($submitter_name)) {
          #return "{ ERROR: no submitter name set for this post }";
          return "";
        } else {
          $retValue = "";
          $web_url = trim($submitter_web);
          if(strlen($web_url) < 8 || strpos($web_url, "http://") !== 0) {
            $web_url = "";
          }
          if(!empty($web_url)) {
            $retValue .= "<a href=\"$web_url\" rel=\"nofollow\">";
          }
          $retValue .= $submitter_name;
          if(!empty($web_url)) {
            $retValue .= "</a>";
          }
          return $retValue;
        }
     }
  }
  else {
    return "";
  }
}

//////////////////////////////////////
// Display the Submitter of the post
//
function tdomf_the_submitter($post_id = 0){
  echo tdomf_get_the_submitter($post_id);
}

////////////////////////////////////////////////////////////////////////
// Display the email address of the submitter (must be used in the loop)
//
function tdomf_the_submitter_email() {
  echo tdomf_get_the_submitter_email();
}

////////////////////////////////////////////////////////////////////////
// Get the email address of the submitter (must be used in the loop)
//
function tdomf_get_the_submitter_email() {
   global $post, $authordata;
   $email = strtolower(get_the_author_email());
   $flag = get_post_meta($post->ID, TDOMF_KEY_FLAG, true);
   if($flag != false && !empty($flag)) {
     $submitter_user_id = get_post_meta($post->ID, TDOMF_KEY_USER_ID, true);
     if($submitter_user_id != false && !empty($submitter_user_id) && $submitter_user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
        $submitter_data = get_userdata($submitter_user_id);
        $email = strtolower($submitter_data->user_email);  
     } else {
        $email = strtolower(get_post_meta($post->ID, TDOMF_KEY_EMAIL, true));
     }
   } 
   return $email;
}


//////////////////////////////////////
// Modify the_author template tag with user
//
function tdomf_author_filter($author=''){
   if(get_option(TDOMF_OPTION_AUTHOR_THEME_HACK)) {
	   $submitter = tdomf_get_the_submitter();
	   if($submitter != "") {
		return $submitter;
	   }
   }
   return $author;
}
add_filter('the_author', 'tdomf_author_filter');

//////////////////////////////////////
// Add submitter info to end of content
//
function tdomf_content_submitter_filter($content=''){
   if(get_option(TDOMF_OPTION_ADD_SUBMITTER)) {
	   $submitter = tdomf_get_the_submitter();
	   if($submitter != "") {
		return $content."<p>".sprintf(__("This post was submitted by %s.","tdomf"),$submitter)."</p>";
	   }
   }
   return $content;
}
add_filter('the_content', 'tdomf_content_submitter_filter');

//////////////////////////////////////
// Add TDOMF stylesheet link to template
//
function tdomf_stylesheet(){
   ?>
   <link rel="stylesheet" href="<?php echo TDOMF_URLPATH; ?>tdomf-style-form.css" type="text/css" media="screen" />
   <?php
}
add_action('wp_head','tdomf_stylesheet');

///////////////////////////////////
// Add TDOMF admin buttons to post
//
function tdomf_content_adminbuttons_filter($content=''){
  global $post;
  $post_ID = 0;
  if(isset($post)) { $post_ID = $post->ID; }
  else if($post_ID == 0){ return $content; }

  // use some form of the form_id
  $form_id = get_post_meta($post_ID,TDOMF_KEY_FORM_ID,true);
   if($form_id == false || !tdomf_form_exists($form_id)){
     $form_id = tdomf_get_first_form_id();
   }
  
   if(get_post_meta($post_ID,TDOMF_KEY_FLAG,true) 
   && $post->post_status == 'draft'
   && current_user_can('publish_posts')) {
     
       $output = "<p>";
   
       $queue = intval(tdomf_get_option_form(TDOMF_OPTION_QUEUE_PERIOD,$form_id));
       if($queue > 0) { $queue = true; } else { $queue = false; }
   
       if($queue) {
           $publishnow_link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&action=publish&post=$post_ID&nofuture=1";
           $publishnow_link = wp_nonce_url($publishnow_link,'tdomf-publish_'.$post_ID);
       }
       
       $publish_link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&action=publish&post=$post_ID";
       $publish_link = wp_nonce_url($publish_link,'tdomf-publish_'.$post_ID);

       $delete_link = get_bloginfo('wpurl')."/wp-admin/post.php?action=delete&post=$post_ID";
       $delete_link = wp_nonce_url($delete_link,'delete-post_'.$post_ID);
       
       if($queue) {
           $output .= sprintf(__('[<a href="%s">Publish Now</a>] [<a href="%s">Add to Queue</a>] [<a href="%s">Delete</a>]',"tdomf"),$publishnow_link, $publish_link,$delete_link);
       } else {
           $output .= sprintf(__('[<a href="%s">Publish</a>] [<a href="%s">Delete</a>]',"tdomf"),$publish_link,$delete_link);
       }
       
       if(get_option(TDOMF_OPTION_SPAM)) {
           $spam_link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&action=spamit&post=$post_ID";
           $spam_link = wp_nonce_url($spam_link,'tdomf-spamit_'.$post_ID);
         
           $ham_link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&action=hamit&post=$post_ID";
           $ham_link = wp_nonce_url($ham_link,'tdomf-hamit_'.$post_ID);
           
            if(get_post_meta($post_ID, TDOMF_KEY_SPAM)) {
                 $output .= sprintf(__(' [<a href="%s">Not Spam</a>]',"tdomf"),$ham_link);
            } else {
                 return $content.sprintf(__(' [<a href="%s">Spam</a>]',"tdomf"),$spam_link);
            }
       } 
       
       $output .= '</p>';
       
       return $content.$output;
   }
   return $content;
}
add_filter('the_content', 'tdomf_content_adminbuttons_filter');

////////////////////////
// Add Edit link to post
//
function tdomf_content_editlink_filter($content=''){
  global $post;
  $post_ID = 0;
  if(isset($post)) { $post_ID = $post->ID; }
  else if($post_ID == 0){ return $content; }
  
  $output = '';  
  $ajax = false;
  $script = false;
  $forms = "";
  $form_ids = tdomf_get_form_ids();
  foreach($form_ids as $form_id) {
      if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id->form_id) && tdomf_check_permissions_form($form_id->form_id,$post_ID) == NULL) {
          $ajax_form = ( tdomf_get_option_form(TDOMF_OPTION_AJAX_EDIT,$form_id->form_id) && (is_single() || is_page()) );
          if($ajax_form) { $ajax = true; }
          $edit_link_style = tdomf_get_option_form(TDOMF_OPTION_ADD_EDIT_LINK,$form_id->form_id);
          if(($ajax_form || $edit_link_style != 'none') && $edit_link_style != false) {
              $form_tag = $form_id->form_id; 
              if($ajax_form) {
                  $form_tag = $form_id->form_id . '_' . $post_ID;
              }
              $js = "";
              if($ajax_form) {
                   $js = " onclick='tdomf_show_form$form_tag(); return false;'";
              }
              
              if($edit_link_style == 'page') {
                   $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id->form_id);
                   $url = get_permalink($pages[0]);
                   if(strpos($url,'?') !== false) {
                      $url .= '&tdomf_post_id='.$post_ID;
                   } else {
                      $url .= '?tdomf_post_id='.$post_ID;
                   }                   
              } else if($edit_link_style == 'your_submissions') {
                   $url = trailingslashit(get_bloginfo('wpurl')).'wp-admin/users.php?page=tdomf_your_submissions&tdomf_post_id='.$post_ID.'#tdomf_form'.$form_id->form_id.'_'.$post_ID;
              } else if($edit_link_style != 'none') {
                   $url = $edit_link_style;
                   if(strpos($url,'?') !== false) {
                      $url .= '&tdomf_post_id='.$post_ID;
                   } else {
                      $url = trailingslashit($url).'?tdomf_post_id='.$post_ID;
                   }
              }
              
              $output .= '<p><a href="'.$url.'"'.$js.'>'.tdomf_get_message_instance(TDOMF_OPTION_ADD_EDIT_LINK_TEXT,$form_id->form_id,false,$post_ID).'</a></p>';
          }
      }
  }
  if($ajax) {
      return "<div id='tdomf_inline_edit-$post_ID' name='tdomf_inline_edit-$post_ID'>".$content.$output."</div>";
  } 
  return $content.$output;
}
add_filter('the_content', 'tdomf_content_editlink_filter');

///////////////////////////////////////////////////////////////////////////////
// Add forms to end of post/page outside of div structure for AJAX inline
// editing on posts
//
function tdomf_ajaxeditforms_action() {
  global $post;
  $post_ID = 0;
  if(isset($post)) { $post_ID = $post->ID; }

  $forms = array();
  $form_ids = tdomf_get_form_ids();
  foreach($form_ids as $form_id) {
      if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id->form_id) && 
         tdomf_get_option_form(TDOMF_OPTION_AJAX_EDIT,$form_id->form_id) && 
         (is_page() || is_single())) {
         if(tdomf_check_permissions_form($form_id->form_id,$post_ID,false) == NULL) {
             $forms [] = array( 'name' => '#tdomf_form'.$form_id->form_id . '_' . $post_ID,
                                'form' => tdomf_get_the_form($form_id->form_id,$post_ID) );
         }
      }
  }
  if(!empty($forms)) {
      foreach($forms as $form) {
          echo $form['form'];
      }
  }
}
add_action('wp_footer', 'tdomf_ajaxeditforms_action');

///////////////////////////////////////////////////////////////////////////////
// Add javascript and style settings to header for AJAX inline editing on posts
//
function tdomf_ajaxeditscripts_action() {
  global $post;
  $post_ID = 0;
  if(isset($post)) { $post_ID = $post->ID; }

  $active_form = false;
  $forms = array();
  $form_ids = tdomf_get_form_ids();
  foreach($form_ids as $form_id) {
      if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id->form_id) && 
         tdomf_get_option_form(TDOMF_OPTION_AJAX_EDIT,$form_id->form_id) && 
         (is_page() || is_single())) {
         if(tdomf_check_permissions_form($form_id->form_id,$post_ID,false) == NULL) {
             $form_tag = $form_id->form_id . '_' . $post_ID;
             
             /* 
              * I'm sure there are probably nicer ways of doing this and it may
              * not be practical to modify the post-tag like this, but it works
              */
             $code =  <<<EOT
   function tdomf_show_form$form_tag(){
      var post = document.getElementById('post-$post_ID');
      if(post != null) {
          var tag = '#post-$post_ID';
      } else {
          var tag = '#tdomf_inline_edit-$post_ID';
      }
      var msg = document.getElementById('tdomf_form${form_tag}_message');
      if(msg != null) {
          jQuery(tag).after( jQuery('#tdomf_form${form_tag}_message') ).remove();
          jQuery('#tdomf_form${form_tag}_message').after( jQuery('#tdomf_form$form_tag') );
          jQuery('#tdomf_form${form_tag}_message').before("<div id='" + tag + "'>");
          jQuery('#tdomf_form$form_tag').after("</div>");
          jQuery('#tdomf_form$form_tag').css("display", "block");      
      } else {                  
          jQuery(tag).after( jQuery('#tdomf_form$form_tag') ).remove();
          jQuery('#tdomf_form$form_tag').before("<div id='" + tag + "'>");
          jQuery('#tdomf_form$form_tag').after("</div>");
          jQuery('#tdomf_form$form_tag').css("display", "block");
      }      
   }
   
EOT;
              /* 
               * If form doesn't support AJAX, then we need to know if it is 
               * active and then to trick the javascript to show it! 
               */
              if(!$active_form && !tdomf_get_option_form(TDOMF_OPTION_AJAX,$form_id->form_id)) {
                  $form_data = tdomf_get_form_data($form_id->form_id);
                  if(!empty($form_data)) {
                      $active_form = true;
                      $code .= "\njQuery(document).ready( function() { tdomf_show_form$form_tag(); } );\n";
                  }
              }

              $forms [] = array( 'name' => '#tdomf_form'.$form_tag,
                                'code' => $code );
         }
      }
  }
  if(!empty($forms)) {
      echo "<script type='text/javascript' src='".get_bloginfo('wpurl')."/wp-includes/js/jquery/jquery.js'></script>";
      echo "<style>\n";
      foreach($forms as $form) {
          echo $form['name'] . "{ display: none; background-color: white; }\n";
      }
      echo "</style>\n";
      echo "<script type='text/javascript'>\n";
      foreach($forms as $form) {
          echo $form['code'];
      }
      echo "</script>\n";
  }
}
add_action('wp_head', 'tdomf_ajaxeditscripts_action');

/////////////////////////////
// Modify Edit link on theme
//
function tdomf_editpostlink_filter($url,$post_id){
  
  $form_ids = tdomf_get_form_ids();
  foreach($form_ids as $form_id) {
      if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id->form_id) && tdomf_check_permissions_form($form_id->form_id,$post_ID) == NULL) {
          $edit_link_style = tdomf_get_option_form(TDOMF_OPTION_AUTO_EDIT_LINK,$form_id->form_id);
          if($edit_link_style != 'none' && $edit_link_style != false) {
              if($edit_link_style == 'page') {
                   $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id->form_id);
                   $url = get_permalink($pages[0]);
                   if(strpos($url,'?') !== false) {
                      $url .= '&tdomf_post_id='.$post_ID;
                   } else {
                      $url .= '?tdomf_post_id='.$post_ID;
                   }                   
              } else if($edit_link_style == 'your_submissions') {
                   $url = trailingslashit(get_bloginfo('wpurl')).'wp-admin/users.php?page=tdomf_your_submissions&tdomf_post_id='.$post_ID.'#tdomf_form'.$form_id->form_id.'_'.$post_ID;
              } else if($edit_link_style != 'none') {
                   $url = $edit_link_style;
                   if(strpos($url,'?') !== false) {
                      $url .= '&tdomf_post_id='.$post_ID;
                   } else {
                      $url = trailingslashit($url).'?tdomf_post_id='.$post_ID;
                   }
              }
              // once we find one, use it!
              break;
          }
      }
  }
  return $url;
}
add_filter('get_edit_post_link', 'tdomf_editpostlink_filter', 10, 2);

//////////////////////////////////////////////////////////
// Is the current user the default user? (error checking)
//
function tdomf_current_user_default_author() {
    global $current_user;
    get_currentuserinfo();
    if(!is_user_logged_in()) { return false; }
    return ($current_user->ID == get_option(TDOMF_DEFAULT_AUTHOR));
}

//////////////////////////////
// Is the current user trusted
//
function tdomf_current_user_trusted() {
    global $current_user;
    get_currentuserinfo();
    if(!is_user_logged_in()) { return false; }
    return (TDOMF_USER_STATUS_TRUSTED == get_usermeta($current_user->ID,TDOMF_KEY_STATUS));
}


?>
