<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

////////////////////////////////////////////////////
// Code for the tdomf edit panel on the post page //
////////////////////////////////////////////////////

# @TODO: nonce support

// Grab a list of user ids of all users, to use in the drop-down menu
//
function tdomf_get_all_users() {
    global $wpdb;
    $query = "SELECT * ";
    $query .= "FROM $wpdb->users ";
    $query .= "ORDER BY ID DESC";
    return $wpdb->get_results( $query );
}

// Grab a list of user ids of all users, to use in the drop-down menu
//
function tdomf_get_all_users_count() {
    global $wpdb;
    $query = "SELECT count(ID) ";
    $query .= "FROM $wpdb->users ";
    $query .= "ORDER BY ID DESC";
    return intval($wpdb->get_var( $query ));
}

// Add the sidebar panel
//
function tdomf_edit_post_panel_admin_head() {
  global $post;
  // don't show on new post/page
  if(is_object($post) && $post->ID > 0) {
      $edit_count = tdomf_get_edits(array('post_id' => $post->ID, 'count' => true));
      // Wordpress 2.5 introduced add_meta_box
      if(function_exists('add_meta_box')) {
         add_meta_box(
              'tdomf',
              __('TDO Mini Forms', 'tdomf'),
              'tdomf_show_edit_post_panel',
              'post' );
         add_meta_box(
              'tdomf',
              __('TDO Mini Forms', 'tdomf'),
              'tdomf_show_edit_post_panel',
              'page' );
         if($edit_count > 0) {
             add_meta_box(
                  'tdomf_revisions',
                  __('TDO Mini Forms Revisions', 'tdomf'),
                  'tdomf_show_edit_post_revision_panel',
                  'post' );
             add_meta_box(
                  'tdomf_revisions',
                  __('TDO Mini Forms Revisions', 'tdomf'),
                  'tdomf_show_edit_post_revision_panel',
                  'page' );       
         }
      } else {
         add_action('dbx_post_sidebar', 'tdomf_show_edit_post_panel');
         add_action('dbx_page_sidebar', 'tdomf_show_edit_post_panel');
         if($edit_count > 0) {
             add_action('dbx_post_sidebar', 'tdomf_show_edit_post_revision_panel');
             add_action('dbx_page_sidebar', 'tdomf_show_edit_post_revision_panel');
         }
      }
  }
}
add_action( 'admin_head', 'tdomf_edit_post_panel_admin_head' );


function tdomf_show_edit_post_revision_panel() {
    global $post;
    
    // don't show on new post
    if($post->ID > 0) {
        $edits = tdomf_get_edits(array('post_id' => $post->ID));
        if(count($edits) > 0) {
            echo "<ul class='post-revisions'>\n";
             foreach($edits as $edit) {
                echo "<li>";
                
                // actual revision
                if($edit->revision_id != 0) {
                    #echo '<a href="'.get_bloginfo('wpurl').'/wp-admin/revision.php?revision='.$edit->revision_id.'">';
                    echo '<a href="admin.php?page='.TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR.'tdomf-revision.php&edit='.$edit->edit_id.'">';
                }
                echo mysql2date(__('d F, Y @ H:i'), $edit->date_gmt);
                if($edit->revision_id != 0) {
                    echo '</a>';
                }     
                
                // status
                if($edit->state == 'unapproved') {
                      _e(' [Pending]',"tdomf");
                } else if($edit->state == 'spam') {
                      _e(' [Spam]',"tdomf");
                }
                
                // user  
                echo _e(' by ','tdomf');
                $name = __("N/A","tdomf");
                if(isset($edit->data[TDOMF_KEY_NAME])) {
                   $name = $ledit->data[TDOMF_KEY_NAME];
                }
                $email = __("N/A","tdomf");
                if(isset($edit->data[TDOMF_KEY_EMAIL])) {
                   $email = $edit->data[TDOMF_KEY_EMAIL];
                }
                  
                if($edit->user_id != 0) { ?>
                 <a href="user-edit.php?user_id=<?php echo $edit->user_id;?>" class="edit">
                 <?php $u = get_userdata($edit->user_id);
                       echo $u->user_login; ?></a>
                 <?php } else if(!empty($name) && !empty($email)) {
                       echo $name." (".$email.")";
                       } else if(!empty($name)) {
                   echo $name;
                 } else if(!empty($email)) {
                   echo $email;
                 } else {
                   _e("N/A","tdomf");
                 }
                
                // form
                if(tdomf_form_exists($edit->form_id) != false) {
                 $form_edit_url = "admin.php?page=tdomf_show_form_options_menu&form=$edit->form_id";
                 $form_name = tdomf_get_option_form(TDOMF_OPTION_NAME,$edit->form_id);
                 _e(' using ','tdomf');
                 echo '<a href="'.$form_edit_url.'">'.sprintf(__('Form #%d: %s','tdomf'),$edit->form_id,$form_name).'</a>';
                }
                
                // ip
                echo ' ('.$edit->ip.')'; 
                
                echo "</li>";
            }
            echo "</ul>\n";
        }
    }
}


//
// Show the Edit Post Panel
//
function tdomf_show_edit_post_panel() {
  global $post;

  // don't show on new post
  if($post->ID > 0) {

  $can_edit = false;
  if(current_user_can('publish_posts')) {
    $can_edit = true;
  }

  $is_tdomf = false;
  $tdomf_flag = get_post_meta($post->ID, TDOMF_KEY_FLAG, true);
  if(!empty($tdomf_flag)) {
    $is_tdomf = true;
  }

  $locked = get_post_meta($post->ID, TDOMF_KEY_LOCK, true);
  
  $submitter_id = get_post_meta($post->ID, TDOMF_KEY_USER_ID, true);

  $submitter_ip = get_post_meta($post->ID, TDOMF_KEY_IP, true);

  $form_id  = get_post_meta($post->ID, TDOMF_KEY_FORM_ID, true);
  
  $is_spam = (get_option(TDOMF_OPTION_SPAM) && get_post_meta($post->ID, TDOMF_KEY_SPAM, true));

  // use JavaScript SACK library for AJAX
  wp_print_scripts( array( 'sack' ));

  // I could stick this AJAX call into the Admin header, however, I don't want
  // it hanging around on every admin page and potentially being called
  // accidentially from some other TDOMF page
?>
         <script type="text/javascript">
         //<![CDATA[
         function tdomf_ajax_edit_post( flag, is_user, user, name, email, web, locked )
         {
           var mysack = new sack( "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
           mysack.execute = 1;
           mysack.method = 'POST';
           mysack.setVar( "action", "tdomf_edit_post" );
           mysack.setVar( "post_ID", "<?php echo $post->ID; ?>" );
           mysack.setVar( "tdomf_flag", flag.checked );
           mysack.setVar( "tdomf_locked", locked.checked );
           if(is_user.checked) {
              mysack.setVar( "tdomf_user", user.value);
           } else {
              mysack.setVar( "tdomf_name", name.value );
              mysack.setVar( "tdomf_email", email.value );
              mysack.setVar( "tdomf_web", web.value );
           }
           mysack.encVar( "cookie", document.cookie, false );
           mysack.onError = function() { alert('<?php _e('AJAX error in looking up tdomf','tdomf'); ?>' )};
           mysack.runAJAX();

           return true;
         }

         function tdomf_update_panel() {
          <?php if($can_edit) { ?>
            var flag = document.getElementById("tdomf_flag").checked;
            if(flag) {
              //document.getElementById("tdomf_submitter").disabled = false;
              document.getElementById("tdomf_submitter_is_user").disabled = false;
              document.getElementById("tdomf_submitter_not_user").disabled = false;
              var is_user = document.getElementById("tdomf_submitter_is_user").checked;
              document.getElementById("tdomf_submitter_user").disabled = !is_user;
              document.getElementById("tdomf_submitter_name").disabled = is_user;
              document.getElementById("tdomf_submitter_email").disabled = is_user;
              document.getElementById("tdomf_submitter_web").disabled = is_user;
            } else {
              // disable everything
              //document.getElementById("tdomf_submitter").disabled = true;
              document.getElementById("tdomf_submitter_is_user").disabled = true;
              document.getElementById("tdomf_submitter_user").disabled = true;
              document.getElementById("tdomf_submitter_not_user").disabled = true;
              document.getElementById("tdomf_submitter_name").disabled = true;
              document.getElementById("tdomf_submitter_email").disabled = true;
              document.getElementById("tdomf_submitter_web").disabled = true;
            }
          <?php } else { ?>
            // nothing can be enabled
            //document.getElementById("tdomf_submitter").disabled = true;
            document.getElementById("tdomf_flag").disabled = true;
            document.getElementById("tdomf_submitter_is_user").disabled = true;
            document.getElementById("tdomf_submitter_user").disabled = true;
            document.getElementById("tdomf_submitter_not_user").disabled = true;
            document.getElementById("tdomf_submitter_name").disabled = true;
            document.getElementById("tdomf_submitter_email").disabled = true;
            document.getElementById("tdomf_submitter_web").disabled = true;
          <?php } ?>
        }
        //]]>
        </SCRIPT>

     <?php if(!function_exists('add_meta_box')) { ?>
        <fieldset class="dbx-box">
        <h3 id="posttdomf" class="dbx-handle"><?php _e('TDO Mini Forms', "tdomf"); ?></h3>
                <div class="dbx-content">
     <?php } ?>                
                <fieldset>
                
                <legend>
                <input id="tdomf_flag" type="checkbox" name="tdomf_flag" <?php if($tdomf_flag){ ?>checked<?php } ?> <?php if(!$can_edit){ ?> disabled <?php } ?> onClick="tdomf_update_panel();" />
                <label for="tdomf_flag"><?php _e("Include in TDO Mini Forms Moderation","tdomf"); ?></label>
                </legend>

                <br/>
                
                <input id="tdomf_locked" type="checkbox" name="tdomf_locked" <?php if($tdomf_locked){ ?>checked<?php } ?> <?php if(!$can_edit){ ?> disabled <?php } ?> onClick="tdomf_update_panel();" />
                <label for="tdomf_locked"><?php _e('Disable Editing by TDO Mini Form Forms','tdomf'); ?></label>
                
                <br/><br/>
                                
                <?php if(!empty($submitter_id) && $submitter_id == get_option(TDOMF_DEFAULT_AUTHOR)) { ?>
                  <span style="color:red;font-size:larger;"><?php _e('The submitter of this post is set as the "default user"! Please correct!','tdomf'); ?></span>
                  <br/><br/>
                <?php } ?>

                <label for="tdomf_submitter_is_user" class="selectit">
                <input id="tdomf_submitter_is_user" type="radio" name="tdomf_submitter" value="tdomf_submitter_is_user" <?php if(!empty($submitter_id)) { ?>checked<?php } ?> <?php if(!$can_edit || !$tdomf_flag){ ?> disabled <?php } ?> onChange="tdomf_update_panel();" />
                <?php _e('Submitter is an existing user','tdomf'); ?></label>

                <?php if(function_exists('add_meta_box')) { ?>
                  <br/><br/>
                <?php } ?>
                
                <?php if(tdomf_get_all_users_count() < TDOMF_MAX_USERS_TO_DISPLAY) { ?>
                <select id="tdomf_submitter_user" name="tdomf_submitter_user" <?php if(!$can_edit || !$tdomf_flag || empty($submitter_id)){ ?> disabled <?php } ?> onChange="tdomf_update_panel();" >
                <?php $users = tdomf_get_all_users();
                      foreach($users as $user) {
                        $status = get_usermeta($user->ID,TDOMF_KEY_STATUS);
                        if($user->ID == $submitter_id || $user->ID != get_option(TDOMF_DEFAULT_AUTHOR)) { ?>
                          <option value="<?php echo $user->ID; ?>" <?php if($user->ID == $submitter_id) { ?> selected <?php } ?> ><?php echo $user->user_login; ?><?php if($user->ID == get_option(TDOMF_DEFAULT_AUTHOR)) { _e("(Default User)","tdomf"); } ?><?php if(!empty($status) && $status == TDOMF_USER_STATUS_BANNED) { _e("(Banned User)","tdomf"); } ?></option>
                      <?php } } ?>
               </select>
                <?php } else {
                    $submitter_username = "";
                    if(!empty($submitter_id)) {
                        $user_obj = new WP_User($submitter_id);
                        $submitter_username = $user_obj->user_login;
                    }
                    ?>
                    <input type="text" 
                           name="tdomf_submitter_user" id="tdomf_submitter_user" 
                           size="20" 
                           value="<?php echo htmlentities($submitter_username,ENT_QUOTES,get_bloginfo('charset')); ?>" 
                           <?php if(!$can_edit || !$tdomf_flag){ ?> disabled <?php } ?> />
                <?php } ?>

                <br/><br/>

                <label for="tdomf_submitter_not_user" class="selectit">
                <input id="tdomf_submitter_not_user" type="radio" name="tdomf_submitter" value="tdomf_submitter_not_user" <?php if(empty($submitter_id)) { ?>checked<?php } ?> <?php if(!$can_edit || !$tdomf_flag){ ?> disabled <?php } ?> onChange="tdomf_update_panel();" />
                <?php _e("Submitter does not have a user account","tdomf"); ?></label>

                <?php if(function_exists('add_meta_box')) { ?>
                  <br/><br/>
                <?php } ?>

                <?php if(!function_exists('add_meta_box')) { ?>
                <label for="tdomf_submitter_name" class="selectit"><?php _e("Name","tdomf"); ?>
                <?php } ?>
                <input type="textfield" value="<?php echo htmlentities(get_post_meta($post->ID, TDOMF_KEY_NAME, true),ENT_QUOTES,get_bloginfo('charset')); ?>" name="tdomf_submitter_name" id="tdomf_submitter_name" onClick="tdomf_update_panel();" <?php if(!$can_edit || !$tdomf_flag || !empty($submitter_id)){ ?> disabled <?php } ?> />
                <?php if(function_exists('add_meta_box')) { ?>
                <label for="tdomf_submitter_name" class="selectit"><?php _e("Name","tdomf"); ?>
                <?php } ?>
                </label>

                <?php if(function_exists('add_meta_box')) { ?>
                  <br/><br/>
                <?php } ?>
                
                <?php if(!function_exists('add_meta_box')) { ?>
                <label for="tdomf_submitter_email" class="selectit"><?php _e("Email","tdomf"); ?>
                <?php } ?>
                <input type="textfield" value="<?php echo htmlentities(get_post_meta($post->ID, TDOMF_KEY_EMAIL, true),ENT_QUOTES,get_bloginfo('charset')); ?>" name="tdomf_submitter_email" id="tdomf_submitter_email" onClick="tdomf_update_panel();" <?php if(!$can_edit || !$tdomf_flag || !empty($submitter_id)){ ?> disabled <?php } ?> />
                <?php if(function_exists('add_meta_box')) { ?>
                <label for="tdomf_submitter_email" class="selectit"><?php _e("Email","tdomf"); ?>
                <?php } ?>
                </label>

                <?php if(function_exists('add_meta_box')) { ?>
                  <br/><br/>
                <?php } ?>
                
                <?php if(!function_exists('add_meta_box')) { ?>
                <label for="tdomf_submitter_web" class="selectit"><?php _e("Webpage","tdomf"); ?>
                <?php }?>
                <input type="textfield" value="<?php echo htmlentities(get_post_meta($post->ID, TDOMF_KEY_WEB, true),ENT_QUOTES,get_bloginfo('charset')); ?>" name="tdomf_submitter_web" id="tdomf_submitter_web" onClick="tdomf_update_panel();" <?php if(!$can_edit || !$tdomf_flag || !empty($submitter_id)){ ?> disabled <?php } ?> />
                <?php if(function_exists('add_meta_box')) { ?>
                <label for="tdomf_submitter_web" class="selectit"><?php _e("Webpage","tdomf"); ?>
                <?php }?>
                </label>

                <br/><br/>

                <?php if($is_spam) { ?>
                    <span style="color:red;font-size:larger;"><?php _e("Akismet thinks this submission is spam!",'tdomf'); ?></span>
                <?php } ?>
              
                <?php if(!empty($submitter_ip)) { ?>
                  <?php printf(__("This post was submitted from IP %s.","tdomf"),$submitter_ip); ?>
                <?php } else { ?>
                  <?php _e("No IP was recorded when this post was submitted.","tdomf"); ?>
                <?php } ?>
                <?php if($form_id != false && tdomf_form_exists($form_id)) {
                printf(__("Submitted from Form %d.","tdomf"),$form_id); } ?>
                </fieldset>

                 <p><input type="button" value="<?php _e("Update &raquo;","tdomf"); ?>" onclick="tdomf_ajax_edit_post(this.form.tdomf_flag, tdomf_submitter_is_user, tdomf_submitter_user, tdomf_submitter_name, tdomf_submitter_email, tdomf_submitter_web, this.form.tdomf_locked);" />

     <?php if(!function_exists('add_meta_box')) { ?>
                </div>
        </fieldset>
     <?php } ?>

<?php
}
}

// Add a handler for the AJAX
//
add_action('wp_ajax_tdomf_edit_post', 'tdomf_save_post');
//
// Handler for AJAX
//
function tdomf_save_post() {
    $post_id = (int) $_POST['post_ID'];
    
    if($_POST['tdomf_locked'] == "false") {
        delete_post_meta($post_id, TDOMF_KEY_LOCK);
        tdomf_log_message("Post $post_id is now set to unlocked. Post can be edited by valid TDO Mini Form forms.");
    } else {
        tdomf_log_message("Post $post_id is now set to locked. Post cannot be edited by any TDO Mini Form forms.");
        delete_post_meta($post_id, TDOMF_KEY_LOCK);
        add_post_meta($post_id, TDOMF_KEY_LOCK, true, true);
    }
    
    if($_POST['tdomf_flag'] == "false") {
      delete_post_meta($post_id, TDOMF_KEY_FLAG);
      tdomf_log_message("Removed post $post_id from TDOMF");
      die("alert('".sprintf(__('TDOMF: Post %d is no longer managed by TDOMF!','tdomf'),$post_id)."')");
    } else {
      add_post_meta($post_id, TDOMF_KEY_FLAG, true, true);
      if(isset($_POST["tdomf_user"])) {
          $user_id = $_POST["tdomf_user"];
          if(!empty($user_id) && !is_numeric($user_id)) {
              if(($userdata = get_userdatabylogin($user_id)) != false) {
                  $user_id = $userdata->ID;
              } else { 
                  die("alert('".sprintf(__("TDOMF: The user %s is not a valid user and cannot be used for Submitter","tdomf"),$user_id)."')");
              }
          }
         delete_post_meta($post_id, TDOMF_KEY_USER_ID);
         add_post_meta($post_id, TDOMF_KEY_USER_ID, $user_id, true);
         tdomf_log_message("Submitter info for post $post_id added");
         die("alert('".sprintf(__('TDOMF: TDO Mini Forms info for post %d updated','tdomf'),$post_id)."')");
      } else {
        // do this so that we *know* that submitter user is not used
        delete_post_meta($post_id, TDOMF_KEY_USER_ID);
        $name = $_POST["tdomf_name"];
        delete_post_meta($post_id, TDOMF_KEY_NAME);
        add_post_meta($post_id, TDOMF_KEY_NAME, $name, true);
        $email = $_POST["tdomf_email"];
        delete_post_meta($post_id, TDOMF_KEY_EMAIL);
        add_post_meta($post_id, TDOMF_KEY_EMAIL, $email, true);
        $web = $_POST["tdomf_web"];
        delete_post_meta($post_id, TDOMF_KEY_WEB);
        add_post_meta($post_id, TDOMF_KEY_WEB, $web, true);
        tdomf_log_message("Submitter info for post $post_id added");
        die("alert('".sprintf(__('TDOMF: TDO Mini Forms info for post %d updated','tdomf'),$post_id)."')");
      }
  }
  tdomf_log_message("Error captured in EditPostPanel:tdomf_save_post");
  die("alert('<?php _e('TDOMF: Error! Incomplete information provided!','tdomf'); ?>')");
}

?>
