<?php
/*
Name: "Subcribe to Comments"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: Change comment, ping and trackback settings (or let users decide)
Version: 1
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/* Is there an issue if the author can already publish?
 *
 * if submitter is author then they will recieve notifications anyway...
 * if 1 user can publish and user can log in
 * if > 1 user who can publish and TDOMF_AUTO_FIX_AUTHOR is set
 */

# don't activate this widget... unless subscribe-to-comments widget is enabled
#
if(function_exists('sg_subscribe_start')) {

   // function not used but might be handy to have around
   //
  function tdomf_widget_subscribe_to_comments_comment_status($form_id) {
      $comment_status = true;
      if(get_option('default_comment_status') == 'closed'){
          $comment_status = false;
      }
      $widgets_in_use = tdomf_get_widget_order($form_id);
      if(in_array("comments",$widgets_in_use)) {
          $options = tdomf_widget_comments_get_options($form_id);
          if($options['user-comments']) {
              $comment_status = true;
          } else if($options['overwrite']) {
              $comment_status = $options['comments'];
          }
      }
      return $comment_status;
  }
    
  function tdomf_widget_subscribe_to_comments($args) {
    $options = tdomf_widget_subscribe_to_comments_get_options($args['tdomf_form_id']);
    $output = "";
    
    extract($args);
    
    $output = $before_widget;

    if(!empty($options['title'])) {
        $output .= $before_title;
        $output .= $options['title'];
        $output .= $after_title;
    }
    
    // Check if values set in cookie
    $sc_email = $subscribe_to_comments_email;
    if(!isset($subscribe_to_comments_email)) {
        // grab comment email
        if(isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
            $sc_email = $_COOKIE['comment_author_email_' . COOKIEHASH];
        } else if(isset($_COOKIE['tdomf_subscribe_to_comments_widget_email'])) {
            $sc_email = $_COOKIE['tdomf_subscribe_to_comments_widget_email'];
        }
    }
  
    // the notifyme function is useful here too!
    $show_email_input = tdomf_widget_notifyme_show_email_input($tdomf_form_id);

    if(!$options['always_subscribe']) {
        $output .= "<input type='checkbox' name='subscribe_to_comments' id='subscribe_to_comments'";
        if(isset($subscribe_to_comments)) $output .= " checked "; 
        $output .= " /><label for=subscribe_to_comments'>".__('Notify me of followup comments via e-mail','tdomf')."</label>";
    } else {
        $output .= __('You will be automatically notified via email of followup comments to your submission. You will be able to unsubscribe after the post is published.','tdomf');
    }
    
    if($show_email_input || $options['show_email_field']) {
        $output .=  "<br/><label for='subscribe_to_comments_email'";
        if($options['always_subscribe']) {
            $output .= " class='required'>".__("Email for comment subscription (required):","tdomf");
        } else {
            $output .= ">".__("Email for comment subscription:","tdomf");
        }
        $output .=  ' <input type="text" value="'.htmlentities($sc_email,ENT_QUOTES).'" name="subscribe_to_comments_email" id="subscribe_to_comments_email" size="40" /></label>';
    }
  
    $output .= $after_widget;

    return $output;
  }
  tdomf_register_form_widget('subscribe_to_comments',__('Subscribe to Comments',"tdomf"), 'tdomf_widget_subscribe_to_comments');
 
  function tdomf_widget_subscribe_to_comments_hack($args) {
    $options = tdomf_widget_subscribe_to_comments_get_options($args['tdomf_form_id']);
    $output = "";
    extract($args);
    
    $output .= $before_widget;
    
    if(!empty($options['title'])) {
        $output .= $before_title;
        $output .= $options['title'];
        $output .= $after_title;
    }

    if(!$options['always_subscribe']) {
        $output .= "\t\t<input type='checkbox' name='subscribe_to_comments' id='subscribe_to_comments'";
        $output .= "<?php if(isset(\$subscribe_to_comments)) { ?> checked <?php } ?>"; 
        $output .= "/><label for=subscribe_to_comments'>".__('Notify me of followup comments via e-mail','tdomf')."</label>";
    } else {
        $output .= __('You will be automatically notified via email of followup comments to your submission. You will be able to unsubscribe after the post is published.','tdomf');
    }
    
    if(!$options['show_email_field']) {
        $output .= "\t\t<?php if(tdomf_widget_notifyme_show_email_input($tdomf_form_id)) { ?>\n";
    } 
    
    $output .=  "\t\t\t<?php \$sc_email = \$subscribe_to_comments_email;\n";
    $output .= "\t\t\tif(!isset(\$subscribe_to_comments_email)) {\n";
    $output .= "\t\t\t\tif(isset(\$_COOKIE['comment_author_email_".COOKIEHASH."'])) {\n";
    $output .= "\t\t\t\t\t\$sc_email = \$_COOKIE['comment_author_email_".COOKIEHASH."'];\n";
    $output .= "\t\t\t\t} else if(isset(\$_COOKIE['tdomf_subscribe_to_comments_widget_email'])) {\n";
    $output .= "\t\t\t\t\t\$sc_email = \$_COOKIE['tdomf_subscribe_to_comments_widget_email'];\n";
    $output .= "\t\t\t } } ?>\n";

    $output .=  "\t\t\t<br/><label for='subscribe_to_comments_email'";
    if($options['always_subscribe']) {
        $output .= " class='required'>".__("Email for comment subscription (required):","tdomf");
    } else {
        $output .= ">".__("Email for comment subscription:","tdomf");
    }
    $output .=  ' <input type="text" value="<?php echo htmlentities($sc_email,ENT_QUOTES) ?>" name="subscribe_to_comments_email" id="subscribe_to_comments_email" size="40" /></label>';
        
    if(!$options['show_email_field']) {
        $output .= "\n\t\t<?php } ?>";
    } 
    
    $output .= $after_widget;
    
    return $output;
    }
  tdomf_register_form_widget_hack('subscribe_to_comments',__('Subscribe to Comments',"tdomf"), 'tdomf_widget_subscribe_to_comments_hack');
  
  
function tdomf_widget_subscribe_to_comments_validate($args,$preview) {
  $options = tdomf_widget_subscribe_to_comments_get_options($args['tdomf_form_id']);
  extract($args);
  if(!$preview) {
    if((tdomf_widget_notifyme_show_email_input($tdomf_form_id) || $options['show_email_field']) && ($options['always_subscribe'] || $subscribe_to_comments)) {
      if(isset($subscribe_to_comments_email) && !tdomf_check_email_address($subscribe_to_comments_email)) {
        return $before_widget.__("You must specify a valid email address to send the comment notifications to.","tdomf").$after_widget;
      }
    }
  }
  return NULL;
}
tdomf_register_form_widget_validate('subscribe_to_comments',__('Subscribe to Comments',"tdomf"), 'tdomf_widget_subscribe_to_comments_validate');
  
function tdomf_widget_subscribe_to_comments_post($args) {
    global $current_user,$sg_subscribe;
    $options = tdomf_widget_subscribe_to_comments_get_options($args['tdomf_form_id']);
    get_currentuserinfo();
     extract($args);
    if($options['always_subscribe'] || $subscribe_to_comments) {
    if(!isset($subscribe_to_comments_email)) {
      if(is_user_logged_in() && tdomf_check_email_address($current_user->user_email)) {
        $subscribe_to_comments_email = $current_user->user_email;
      } else if(isset($whoami_email)) {
        $subscribe_to_comments_email = $whoami_email;
      } else {
        tdomf_log_message("Could not find a email address to use for comment subscribption!",TDOMF_LOG_ERROR);
      }
    }
    
    /* This method doesn't work because the post must be published...
    sg_subscribe_start();
    if(is_user_logged_in()) {
        $sg_subscribe->solo_subscribe("",$post_ID);
    } else {
        $sg_subscribe->solo_subscribe($subscribe_to_comments_email,$post_ID);
    }
    if(isset($sg_subscribe->errors['solo_subscribe'])) {
        if(count($sg_subscribe->errors['solo_subscribe']) > 1) {
            foreach($sg_subscribe->errors['solo_subscribe'] as $err) {
                $errors = $err . "<br/>";
            }
            return $error;
        } else {
            return $sg_subscribe->errors['solo_subscribe'][0];
        }
    }*/
    
    // this is how subscibe to email works
    add_post_meta($post_ID, '_sg_subscribe-to-comments', $subscribe_to_comments_email);
    // set comment email so that you can "unsubscribe"
    setcookie('comment_author_email_' . COOKIEHASH, $subscribe_to_comments_email, time() + 30000000, COOKIEPATH);

    setcookie('tdomf_subscribe_to_comments_widget_email',$subscribe_to_comments_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
  }
  return NULL;
}
tdomf_register_form_widget_post('subscribe_to_comments',__('Subscribe to Comments',"tdomf"), 'tdomf_widget_subscribe_to_comments_post');

    function tdomf_widget_subscribe_to_comments_get_options($form_id) {
        $options = tdomf_get_option_widget('tdomf_subscribe_to_comments_widget',$form_id);
        if($options == false) {
           $options = array();
           $options['title'] = "";
           $options['show_email_field'] = false;
           $options['always_subscribe'] = false;
        }
      return $options;
    }
  
  function tdomf_widget_subscribe_to_comments_control($form_id) {
      $options = tdomf_widget_subscribe_to_comments_get_options($form_id);
  
  // Store settings for this widget
    if ( $_POST['subscribe_to_comments-submit'] ) {
     $newoptions['title'] = strip_tags(stripslashes($_POST['subscribe_to_comments-title']));
     $newoptions['show_email_field'] = isset($_POST['subscribe_to_comments-show_email_field']);
     $newoptions['always_subscribe'] = isset($_POST['subscribe_to_comments-always_subscribe']);
     if ( $options != $newoptions ) {
        $options = $newoptions;
        tdomf_set_option_widget('tdomf_subscribe_to_comments_widget', $options,$form_id);
     }
  }

   // Display control panel for this widget
  
  extract($options);

        ?>
<div>

<small><?php _e('This widget uses the <a href="http://txfx.net/code/wordpress/subscribe-to-comments/">Subscribe to Comments plugin</a> (version 2.1 at least) to automatically subscribe submitters to comments.','tdomf'); ?></small>
<br/><br/>

<label for="subscribe_to_comments-title" style="line-height:35px;"><?php _e("Title: ","tdomf"); ?>
<input type="textfield" id="subscribe_to_comments-title" name="subscribe_to_comments-title" value="<?php echo htmlentities($options['title'],ENT_QUOTES,get_bloginfo('charset')); ?>" /></label>

<br/>

<input type="checkbox" name="subscribe_to_comments-show_email_field" id="subscribe_to_comments-show_email_field" <?php if($options['show_email_field']) echo "checked"; ?> >
<label for="subscribe_to_comments-show_email_field" style="line-height:35px;"><?php _e("Always show an Email Field","tdomf"); ?></label><br/>
<small><?php _e('Only matters to non-registered users. If the Who Am I widget is used with the email field set to required, this widget will use that field, otherwise it\'ll display a email field for the user to subscribe to. Checking this option will force the email field to always be visible.','tdomf'); ?></small>
<br/>

<input type="checkbox" name="subscribe_to_comments-always_subscribe" id="subscribe_to_comments-always_subscribe" <?php if($options['always_subscribe']) echo "checked"; ?> >
<label for="subscribe_to_comments-always_subscribe" style="line-height:35px;"><?php _e("Always Subscribe Submitter","tdomf"); ?></label><br/>
<small><?php _e('Enabling this option means that the submitter (as long as we have a valid email address) will automatically be subscribed to comments. Otherwise they will have a choice.','tdomf'); ?></small>

</div>
        <?php
}
tdomf_register_form_widget_control('subscribe_to_comments',__('Subscribe to Comments',"tdomf"), 'tdomf_widget_subscribe_to_comments_control', 500, 400);

function tdomf_widget_subscribe_to_comments_admin_error($form_id) {
  if(tdomf_widget_subscribe_to_comments_comment_status($form_id) == false) {
      $output .= __('<b>Warning</b>: You are using the "Subscribe to Comments" widget but comments seem to be disabled for this form!','tdomf');
  }
  return $output;
}
tdomf_register_form_widget_admin_error('subscribe_to_comments',__('Subscribe to Comments',"tdomf"), 'tdomf_widget_subscribe_to_comments_admin_error');

} /* if subscribe to comments plugin installed */


?>