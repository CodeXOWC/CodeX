<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

///////////////////
// Uinstall Page //
///////////////////

// grab a list of all posts
//
function tdomf_get_all_posts() {
  global $wpdb;
	$query = "SELECT ID ";
	$query .= "FROM $wpdb->posts ";
	$query .= "ORDER BY ID DESC";
	return $wpdb->get_results( $query );
}

/////////////////////////////////
// Delete pre-configured options
//
function tdomf_reset_options() {
  global $wpdb, $wp_roles, $table_prefix;
   
  echo "<span style='width:200px;'>";
  _e("Deleting Options... ","tdomf");
  echo "</span>";
  
  // This includes v0.6 options!
  //
  delete_option(TDOMF_ACCESS_LEVEL);
  delete_option(TDOMF_NOTIFY_LEVEL);
  delete_option(TDOMF_ACCESS_ROLES);
  delete_option(TDOMF_NOTIFY_ROLES);
  delete_option(TDOMF_DEFAULT_CATEGORY);
  delete_option(TDOMF_DEFAULT_AUTHOR);
  delete_option(TDOMF_AUTO_FIX_AUTHOR);
  delete_option(TDOMF_BANNED_IPS);
  delete_option(TDOMF_VERSION_CURRENT);
  delete_option(TDOMF_OPTION_MODERATION);
  delete_option(TDOMF_OPTION_TRUST_COUNT);
  delete_option(TDOMF_OPTION_ALLOW_EVERYONE);
  delete_option(TDOMF_OPTION_AJAX);
  delete_option(TDOMF_OPTION_PREVIEW);
  delete_option(TDOMF_OPTION_FROM_EMAIL);
  delete_option(TDOMF_OPTION_AUTHOR_THEME_HACK);
  delete_option(TDOMF_OPTION_ADD_SUBMITTER);
  delete_option(TDOMF_OPTION_FORM_ORDER);  
  delete_option(TDOMF_STAT_SUBMITTED);
  delete_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES);
  delete_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES);
  delete_option(TDOMF_OPTION_YOUR_SUBMISSIONS);
  delete_option(TDOMF_OPTION_NAME);
  delete_option(TDOMF_OPTION_DESCRIPTION);
  delete_option(TDOMF_OPTION_CREATEDPAGES);
  delete_option(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS);
  delete_option(TDOMF_OPTION_CREATEDUSERS);
  delete_option(TDOMF_OPTION_WIDGET_INSTANCES);
  delete_option(TDOMF_LOG);
  delete_option(TDOMF_OPTION_WIDGET_MAX_WIDTH);
  delete_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT);
  delete_option(TDOMF_OPTION_VERIFICATION_METHOD);
  delete_option(TDOMF_OPTION_FORM_DATA_METHOD);
  delete_option(TDOMF_VERSION_LAST);
  
  echo "<span style='color:green;'>";
  _e("DONE","tdomf");  
  echo "</span><br/>";
  
  echo "<span style='width:200px;'>";
  _e("Resetting role capabilities... ","tdomf");
  echo "</span>";
  if(!isset($wp_roles)) {
    $wp_roles = new WP_Roles();
  }
  $roles = $wp_roles->role_objects;
  $form_ids = tdomf_get_form_ids();
  foreach($roles as $role) {
     if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_1'])){
       $role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_1');
     }
     foreach($form_ids as $f) {
       if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$f->form_id])){
          $role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$f->form_id);
       }
     }
  }
  
  echo "<span style='color:green;'>";
  _e("DONE","tdomf"); 
  echo "</span><br/>";
  
  echo "<span style='width:200px;'>";
  _e("Deleting Widget Options (or at least the ones I can find!)... ","tdomf");
  echo "</span>";
  // Danger will robinson! If the table prefix is "tdomf_", you may end up
  // deleting critical Wordpress core options!
  if($table_prefix != "tdomf_") {
    $alloptions = wp_load_alloptions();
    foreach($alloptions as $id => $val) {
      if(preg_match('#^tdomf_.+#',$id)) {
        delete_option($id);
        echo "<!-- $id -->";
      }
    }
    echo "<span style='color:green;'>";
    _e("DONE","tdomf");
  } else {
    echo "<span style='color:red;'>";
    _e("FAIL","tdomf");
  }
  echo "</span><br/>";
  
  echo "<span style='width:200px;'>";
  _e("Deleting Database Tables... ","tdomf");
  echo "</span>";
  tdomf_db_delete_tables();
  echo "<span style='color:green;'>";
    _e("DONE","tdomf");
  echo "</span><br/>";
  
}

function tdomf_full_uninstall() {
  
  // Delete Posts
  //
   echo "<span style='width:200px;'>";
  _e("Removing posts submitted or managed by TDO Mini Forms (this may take a few minutes depending on the number of posts)... ","tdomf");
  echo "</span>";
  $posts = tdomf_get_all_posts();
  foreach($posts as $post) {
    delete_option(TDOMF_NOTIFY.$post->ID);
    $tdomf_flag = get_post_meta($post->ID, TDOMF_KEY_FLAG, true);
    if(!empty($tdomf_flag)){
       wp_delete_post($post->ID);
    }
  }
  echo "<span style='color:green;'>";
  _e("DONE","tdomf"); 
  echo "</span><br/>";
  
  // Delete Created Users
  //
   echo "<span style='width:200px;'>";
  _e("Attempting to delete any users created by TDOMF... ","tdomf");
  echo "</span>";
  $users = get_option(TDOMF_OPTION_CREATEDUSERS);
  if($users != false) {
    foreach($users as $u) {
      wp_delete_user($u);
    }
  }
  echo "<span style='color:green;'>";
  _e("DONE","tdomf"); 
  echo "</span><br/>";
  
  // Strip existing Users
  //
   echo "<span style='width:200px;'>";
  _e("Removing info from remaining users (this may take a few minutes depending on number of users)... ","tdomf");
  echo "</span>";
  $users = tdomf_get_all_users();
  foreach($users as $user) {
    delete_usermeta($user->ID, TDOMF_KEY_FLAG);
    delete_usermeta($user->ID, TDOMF_KEY_STATUS);
  }
  echo "<span style='color:green;'>";
  _e("DONE","tdomf"); 
  echo "</span><br/>";
  
  // Delete Forms
  //
  $form_ids = tdomf_get_form_ids();
  foreach($form_ids as $f) {
    echo "<span style='width:200px;'>";
    printf(__("Deleting Form %d... ","tdomf"),$f->form_id);
    echo "</span>";
    if(tdomf_delete_form($f->form_id)) {
      echo "<span style='color:green;'>";
     _e("DONE","tdomf");
    } else {
      echo "<span style='color:red;'>";
     _e("FAIL","tdomf");
    }
    echo "</span><br/>";
  }
  
  // Delete Options
  //
  tdomf_reset_options();
}

// Uninstall everything else!
//
function tdomf_uninstall() {
  tdomf_reset_options();
  
  echo "<span style='width:200px;'>";
  _e("Removing info from all users (this may take a few minutes depending on number of users)... ","tdomf");
  echo "</span>";
  $users = tdomf_get_all_users();
  foreach($users as $user) {
    delete_usermeta($user->ID, TDOMF_KEY_FLAG);
    delete_usermeta($user->ID, TDOMF_KEY_STATUS);
  }
  echo "<span style='color:green;'>";
  _e("DONE","tdomf"); 
  echo "</span><br/>";
  
  // This includes v0.6 options!
  //
  echo "<span style='width:200px;'>";
  _e("Removing info from all posts (this may take a few minutes depending on number of posts)... ","tdomf");
  echo "</span>";
  $posts = tdomf_get_all_posts();
  foreach($posts as $post) {
    delete_option(TDOMF_NOTIFY.$post->ID);
    delete_post_meta($post->ID, TDOMF_KEY_NOTIFY_EMAIL);
    delete_post_meta($post->ID, TDOMF_KEY_FLAG);
    delete_post_meta($post->ID, TDOMF_KEY_NAME);
    delete_post_meta($post->ID, TDOMF_KEY_EMAIL);
    delete_post_meta($post->ID, TDOMF_KEY_WEB);
    delete_post_meta($post->ID, TDOMF_KEY_IP);
    delete_post_meta($post->ID, TDOMF_KEY_USER_ID);
    delete_post_meta($post->ID, TDOMF_KEY_USER_NAME);
    delete_post_meta($post->ID, TDOMF_KEY_LOCK);
  }
  echo "<span style='color:green;'>";
  _e("DONE","tdomf"); 
  echo "</span><br/>";
}

// Display a help page
//
function tdomf_show_uninstall_menu() {
  ?>

  <div class="wrap">

    <h2><?php _e('Uninstall TDO Mini Forms', 'tdomf') ?></h2>

    <?php $plugin_name = TDOMF_FOLDER.'/tdomf.php';
          $deactivate_url = wp_nonce_url("plugins.php?action=deactivate&plugin=$plugin_name","deactivate-plugin_$plugin_name"); ?>
                    
    <?php if(isset($_REQUEST['action'])) {
            $action = $_REQUEST['action']; ?>
            <p>
    <?php   if($action == "reset_options") {
              check_admin_referer('tdomf-reset-options');
              tdomf_reset_options();
              ?>
              <p><a href='<?php echo $deactivate_url; ?>' title='Deactivate TDO Mini Forms' class="delete">
              <?php _e("Final step to complete uninstall: Deactivate TDO Mini Forms Plugin","tdomf"); ?></a></p>
              <?php
            } else if($action == "uninstall") {
              check_admin_referer('tdomf-uninstall');
              tdomf_uninstall();
              ?>
              <p><a href='<?php echo $deactivate_url; ?>' title='Deactivate TDO Mini Forms' class="delete">
              <?php _e("Final step to complete uninstall: Deactivate TDO Mini Forms Plugin","tdomf"); ?></a></p>
              <?php
            } else if($action == "uninstall-all-1") {
              check_admin_referer('tdomf-uninstall-all-1');
              ?>
              <p><?php _e("You are about to do some potentially critical things to Wordpress. Please only proceed if you happy with the risks.","tdomf"); ?></p>
              <p><a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_uninstall_menu&action=uninstall-all-2",'tdomf-uninstall-all-2'); ?>" class='delete' ><?php _e("Are you really really sure you want to proceed?","tdomf"); ?></a></p>
              <?php
            } else if($action == "uninstall-all-2") {
              check_admin_referer('tdomf-uninstall-all-2');
              tdomf_full_uninstall();
              ?>
              <p><a href='<?php echo $deactivate_url; ?>' title='Deactivate TDO Mini Forms' class="delete">
              <?php _e("Final step to complete uninstall: Deactivate TDO Mini Forms Plugin","tdomf"); ?></a></p>
              <?php
            } ?>
            </p>  
    <?php } else { ?>
            
            <p><?php _e("From here you can uninstall and remove some or all of TDO Mini Form's options and information.","tdomf"); ?></p>
            </div>
            
            <div class="wrap">
            <p><?php _e("You can simply remove just the settings/options. This will preserve submitter information on posts and users if you re-enable TDO Mini Forms later.","tdomf"); ?></p>
            <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_uninstall_menu&action=reset_options",'tdomf-reset-options'); ?>" class='delete' ><?php _e("Remove Options","tdomf"); ?></a><br/>
            </div>
            
            <div class="wrap">
            <p><?php _e("This removes <i>nearly everything</i>. Any posts submitted, users created or pages created are not removed. However submitted posts are stripped of any information about TDO Mini Forms. If you re-enable TDO Mini Forms, posts previousily submitted will not turn up as submitted posts any more.","tdomf"); ?></p>
            <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_uninstall_menu&action=uninstall",'tdomf-uninstall'); ?>" class='delete' ><?php _e("Uninstall Nearly Everything!","tdomf"); ?></a>
            </div>
            
            <div class="wrap">
            <p><?php _e("This removes <b>everything</b>. It is advised to backup your database before proceeding as posts, pages and users will be deleted. All posts submitted by users using TDO Mini Forms will be deleted. Any users created by TDO Mini forms will be deleted. Any pages created by TDO Mini Forms will be deleted. All options and settings will be completely removed. It'll be like you never used TDO Mini Forms!","tdomf"); ?></p>
            <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_uninstall_menu&action=uninstall-all-1",'tdomf-uninstall-all-1'); ?>" class='delete' ><?php _e("Uninstall Everything!!!!","tdomf"); ?></a>
            </div>
            
    <?php } ?>
    
    <br/>

</div>

<?php
}
?>
