<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

function tdomf_load_options_admin_scripts() {
    /* for tabs */
    wp_enqueue_script( 'jquery-ui-tabs' );
}
add_action("load-".sanitize_title(__('TDO Mini Forms', 'tdomf'))."_page_tdomf_show_options_menu","tdomf_load_options_admin_scripts");

function tdomf_options_admin_head() {
    global $wp_version;
    /* add style options and start tabs for options page */
    if(preg_match('/tdomf_show_options_menu/',$_SERVER['REQUEST_URI'])) { ?>
           
           <style>
            .ui-tabs-nav {
                /*resets*/margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none;
                float: left;
                position: relative;
                z-index: 1;
                border-right: 1px solid #d3d3d3;
                bottom: -1px;
            }
            .ui-tabs-nav li {
                /*resets*/margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none;
                float: left;
                border: 1px solid #d3d3d3;
                border-right: none;
            }
            .ui-tabs-nav li a {
                /*resets*/margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none;
                float: left;
                font-weight: bold;
                text-decoration: none;
                padding: .5em 1.7em;
                color: #555555;
                background: #e6e6e6;
            }
            .ui-tabs-nav li a:hover {
                background: #dadada;
                color: #212121;
            }
            .ui-tabs-nav li.ui-tabs-selected {
                border-bottom-color: #ffffff;
            }
            .ui-tabs-nav li.ui-tabs-selected a, .ui-tabs-nav li.ui-tabs-selected a:hover {
                background: #ffffff;
                color: #222222;
            }
            .ui-tabs-panel {
                /*resets*/margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none;
                clear:left;
                border: 1px solid #d3d3d3;
                background: #ffffff;
                color: #222222;
                padding: 1.5em 1.7em;	
            }
            .ui-tabs-hide {
                display: none;
            }
            .ui-tabs-nav li.ui-tabs-disabled a, .ui-tabs-nav li.ui-tabs-disabled a:hover {
                color: grey;
                background: lightgrey;
                text-decoration:line-through;
            }
            #access_caps_list {
             overflow: scroll;
             height: 200px;
            }
            
            </style>
           
           <script>
           function init_tdomf_tabs() {
              jQuery(document).ready(function(){
                   <?php if(version_compare($wp_version,"2.8-beta2",">=")) { ?>
                      jQuery("#options_tabs").tabs();
                   <?php } else { ?>   
                      jQuery("#options_tabs > ul").tabs();
                   <?php } ?>                      
              });
           }
           init_tdomf_tabs();
           </script>
           
    <?php }
}
add_action( 'admin_head', 'tdomf_options_admin_head' );

function tdomf_handle_spam_options_actions($form_id = false){
    $message = '';
    
    if($form_id) {
        
        $tdomf_spam_overwrite = isset($_POST['tdomf_spam_overwrite']);
        tdomf_set_option_form(TDOMF_OPTION_SPAM_OVERWRITE,$tdomf_spam_overwrite,$form_id);
        
        if($tdomf_spam_overwrite) {
           
            $tdomf_spam = isset($_POST['tdomf_spam']);
            tdomf_set_option_form(TDOMF_OPTION_SPAM,$tdomf_spam,$form_id);
            
            /*
            if($tdomf_spam) {
                $tdomf_spam_akismet_key = $_POST['tdomf_spam_akismet_key'];
                $tdomf_spam_akismet_key_prev = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY,$form_id);
                if(tdomf_get_option_form(TDOMF_OPTION_SPAM_AKISMET_KEY_PREV,$form_id) == false || $tdomf_spam_akismet_key_prev != $tdomf_spam_akismet_key) {
                    if(TDOMF_DEBUG_FAKE_SPAM || (!empty($tdomf_spam_akismet_key) && tdomf_akismet_key_verify($tdomf_spam_akismet_key))){
                       tdomf_set_option_form(TDOMF_OPTION_SPAM_AKISMET_KEY,$tdomf_spam_akismet_key,$form_id);
                       tdomf_set_option_form(TDOMF_OPTION_SPAM_AKISMET_KEY_PREV,$tdomf_spam_akismet_key_prev,$form_id);
                    } else {
                      $message .= "<font color='red'>".sprintf(__("The key: %s has not been recognised by akismet. Spam protection has been disabled.","tdomf"),$tdomf_spam_akismet_key)."</font><br/>";
                      tdomf_set_option_form(TDOMF_OPTION_SPAM,false,$form_id);
                      // reset overwrite
                      $tdomf_spam = false;
                      tdomf_set_option_form(TDOMF_OPTION_SPAM_OVERWRITE,false,$form_id);
                    }
                }
            }
            */
            
            if($tdomf_spam) {
                
                /*
                $tdomf_spam_notify = $_POST['tdomf_spam_notify'];
                tdomf_set_option_form(TDOMF_OPTION_SPAM_NOTIFY,$tdomf_spam_notify,$form_id);
            
                $tdomf_spam_auto_delete = $_POST['tdomf_spam_auto_delete'];
                if($tdomf_spam_auto_delete == "month") {
                    tdomf_set_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE,true,$form_id);
                    tdomf_set_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,false,$form_id);
                } else if($tdomf_spam_auto_delete == "now") {
                    tdomf_set_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE,false,$form_id);
                    tdomf_set_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,true,$form_id);
                } else {
                    tdomf_set_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE,false,$form_id);
                    tdomf_set_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,false,$form_id);
                }
                */
                
                $tdomf_nospam_author = isset($_POST['tdomf_nospam_author']);
                tdomf_set_option_form(TDOMF_OPTION_NOSPAM_AUTHOR,$tdomf_nospam_author,$form_id);
                $tdomf_nospam_trusted = isset($_POST['tdomf_nospam_trusted']);
                tdomf_set_option_form(TDOMF_OPTION_NOSPAM_TRUSTED,$tdomf_nospam_trusted,$form_id);
                $tdomf_nospam_publish = isset($_POST['tdomf_nospam_publish']);
                tdomf_set_option_form(TDOMF_OPTION_NOSPAM_PUBLISH,$tdomf_nospam_publish,$form_id);
                $tdomf_nospam_user = isset($_POST['tdomf_nospam_user']);
                tdomf_set_option_form(TDOMF_OPTION_NOSPAM_USER,$tdomf_nospam_user,$form_id);
            }
        
        }
    } else {
        $tdomf_spam = isset($_POST['tdomf_spam']);
        update_option(TDOMF_OPTION_SPAM,$tdomf_spam);
        
        // allow the akismet key to be set independantly of the tdomf_spam option
        // because tdomf_spam is disabled if you don't have a key
        
        $tdomf_spam_akismet_key = $_POST['tdomf_spam_akismet_key'];
        $tdomf_spam_akismet_key_prev = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
        if(get_option(TDOMF_OPTION_SPAM_AKISMET_KEY_PREV) == false || $tdomf_spam_akismet_key_prev != $tdomf_spam_akismet_key) {
            if(TDOMF_DEBUG_FAKE_SPAM || (!empty($tdomf_spam_akismet_key) && tdomf_akismet_key_verify($tdomf_spam_akismet_key))){
               update_option(TDOMF_OPTION_SPAM_AKISMET_KEY,$tdomf_spam_akismet_key);
               update_option(TDOMF_OPTION_SPAM_AKISMET_KEY_PREV,$tdomf_spam_akismet_key_prev);
            } else {
              $message .= "<font color='red'>".sprintf(__("The key: %s has not been recognised by akismet. Spam protection has been disabled.","tdomf"),$tdomf_spam_akismet_key)."</font><br/>";
              update_option(TDOMF_OPTION_SPAM,false);
              $tdomf_spam = false;
            }
        }
        
        if($tdomf_spam) {
            
            $tdomf_spam_notify = $_POST['tdomf_spam_notify'];
            update_option(TDOMF_OPTION_SPAM_NOTIFY,$tdomf_spam_notify);
        
            $tdomf_spam_auto_delete = $_POST['tdomf_spam_auto_delete'];
            if($tdomf_spam_auto_delete == "month") {
                update_option(TDOMF_OPTION_SPAM_AUTO_DELETE,true);
                update_option(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,false);
            } else if($tdomf_spam_auto_delete == "now") {
                update_option(TDOMF_OPTION_SPAM_AUTO_DELETE,false);
                update_option(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,true);
            } else {
                update_option(TDOMF_OPTION_SPAM_AUTO_DELETE,false);
                update_option(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,false);
            }
            
            $tdomf_nospam_author = isset($_POST['tdomf_nospam_author']);
            update_option(TDOMF_OPTION_NOSPAM_AUTHOR,$tdomf_nospam_author);
            $tdomf_nospam_trusted = isset($_POST['tdomf_nospam_trusted']);
            update_option(TDOMF_OPTION_NOSPAM_TRUSTED,$tdomf_nospam_trusted );
            $tdomf_nospam_publish = isset($_POST['tdomf_nospam_publish']);
            update_option(TDOMF_OPTION_NOSPAM_PUBLISH,$tdomf_nospam_publish);
            $tdomf_nospam_user = isset($_POST['tdomf_nospam_user']);
            update_option(TDOMF_OPTION_NOSPAM_USER,$tdomf_nospam_user);
        }
    }
    
    return $message;
}

function tdomf_show_spam_options($form_id = false){
  
  $spam_enabled = true;
  $tdomf_spam_global = get_option(TDOMF_OPTION_SPAM);
  $tdomf_spam_akismet_key_global = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
  if(!$tdomf_spam_global || !$tdomf_spam_akismet_key_global || empty($tdomf_spam_akismet_key_global)) {
      $spam_enabled = false;
  }

  $tdomf_spam_overwrite = false;
  if($form_id != false && tdomf_get_option_form(TDOMF_OPTION_SPAM_OVERWRITE,$form_id)) {
      $tdomf_spam_overwrite = true;
  }
  
  if($tdomf_spam_overwrite) {
      $tdomf_spam = tdomf_get_option_form(TDOMF_OPTION_SPAM,$form_id);
      $tdomf_spam_akismet_key = tdomf_get_option_form(TDOMF_OPTION_SPAM_AKISMET_KEY,$form_id);
      if($tdomf_spam_akismet_key == false || empty($tdomf_spam_akismet_key)) {
        $tdomf_spam_akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
        if($tdomf_spam_akismet_key == false || empty($tdomf_spam_akismet_key)) {
            $tdomf_spam_akismet_key = get_option('wordpress_api_key');
        }
      }
      $tdomf_spam_notify = tdomf_get_option_form(TDOMF_OPTION_SPAM_NOTIFY,$form_id);
      $tdomf_spam_auto_delete = tdomf_get_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE,$form_id); 
      $tdomf_spam_auto_delete_now = tdomf_get_option_form(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW,$form_id);
      $tdomf_nospam_user = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_USER,$form_id);
      $tdomf_nospam_author = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_AUTHOR,$form_id);
      $tdomf_nospam_trusted = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_TRUSTED,$form_id);
      $tdomf_nospam_publish = tdomf_get_option_form(TDOMF_OPTION_NOSPAM_PUBLISH,$form_id);
  } else {
      $tdomf_spam = get_option(TDOMF_OPTION_SPAM);
      $tdomf_spam_akismet_key = get_option(TDOMF_OPTION_SPAM_AKISMET_KEY);
      if($tdomf_spam_akismet_key == false || empty($tdomf_spam_akismet_key)) {
        $tdomf_spam_akismet_key = get_option('wordpress_api_key');
      }
      $tdomf_spam_notify = get_option(TDOMF_OPTION_SPAM_NOTIFY);
      $tdomf_spam_auto_delete = get_option(TDOMF_OPTION_SPAM_AUTO_DELETE); 
      $tdomf_spam_auto_delete_now = get_option(TDOMF_OPTION_SPAM_AUTO_DELETE_NOW);
      $tdomf_nospam_user = get_option(TDOMF_OPTION_NOSPAM_USER);
      $tdomf_nospam_author = get_option(TDOMF_OPTION_NOSPAM_AUTHOR);
      $tdomf_nospam_trusted = get_option(TDOMF_OPTION_NOSPAM_TRUSTED);
      $tdomf_nospam_publish = get_option(TDOMF_OPTION_NOSPAM_PUBLISH);
  }
  
  ?>
  
  <?php if($form_id && !$tdomf_spam_global) { ?>
      <p><?php _e('Spam protection has been disabled. It can be re-enabled on the general options page','tdomf'); ?></p>
  <?php } else if ($form_id && (!$tdomf_spam_akismet_key_global || empty($tdomf_spam_akismet_key_global))) { ?>
      <p><?php _e('There is no valid Akismet key set. You must set a valid Akismet key on the general options page','tdomf'); ?></p>
  <?php } ?>

  <script type="text/javascript">
 //<![CDATA[      
  function tdomf_enable_spam_options() {
    var flag = document.getElementById("tdomf_spam").checked;
    <?php if(!$form_id) { ?>
    /*document.getElementById("tdomf_spam_akismet_key").disabled = !flag;*/
    document.getElementById("tdomf_spam_notify_live").disabled = !flag;
    document.getElementById("tdomf_spam_notify_none").disabled = !flag;
    document.getElementById("tdomf_spam_auto_delete_manual").disabled = !flag;
    document.getElementById("tdomf_spam_auto_delete_month").disabled = !flag;
    /*document.getElementById("tdomf_spam_auto_delete_now").disabled = !flag;*/
    <?php } ?>
    document.getElementById("tdomf_nospam_author").disabled = !flag;
    document.getElementById("tdomf_nospam_trusted").disabled = !flag;
    document.getElementById("tdomf_nospam_publish").disabled = !flag;
    document.getElementById("tdomf_nospam_user").disabled = !flag;
  }
 <?php if($form_id) { ?> 
  function tdomf_enable_spam_overwrite() {
    var flag = document.getElementById("tdomf_spam_overwrite").checked;
    document.getElementById("tdomf_spam").disabled = !flag;
    if(flag) {
        tdomf_enable_spam_options();
    } else {
        document.getElementById("tdomf_nospam_author").disabled = !flag;
        document.getElementById("tdomf_nospam_trusted").disabled = !flag;
        document.getElementById("tdomf_nospam_publish").disabled = !flag;
        document.getElementById("tdomf_nospam_user").disabled = !flag;
    }
  }
  <?php } ?>
  //-->
  </script>
  
  <?php if($form_id) { ?>
  <p>
  <b><?php _e("Overwrite Global Spam Settings"); ?></b>
    <input type="checkbox" name="tdomf_spam_overwrite" id="tdomf_spam_overwrite"  
           <?php if($tdomf_spam_overwrite) echo "checked"; ?> 
           <?php if(!$spam_enabled) { echo "disabled"; } else { ?>
           onChange="tdomf_enable_spam_overwrite();" <?php } ?> >
  </p>
  
  <p>
  
  <b><?php _e("Enable Spam Protection ","tdomf"); ?></b>
    <input type="checkbox" name="tdomf_spam" id="tdomf_spam"  
        <?php if($tdomf_spam) echo "checked"; ?>
        <?php if(!$spam_enabled) { echo "disabled"; } else { ?>
        onChange="tdomf_enable_spam_options();" <?php } ?> >
  </p>
 
  <?php } ?>
  
  <?php if(!$form_id) { ?>

  <b><?php _e("Enable Spam Protection ","tdomf"); ?></b>
    <input type="checkbox" name="tdomf_spam" id="tdomf_spam"  
        <?php if($tdomf_spam) echo "checked"; ?>
        <?php if(empty($tdomf_spam_akismet_key)) { echo "disabled"; } else { ?>
        onChange="tdomf_enable_spam_options();" <?php } ?> >
  </p>
      
  <p>
  <b><?php _e("Your Akismet Key","tdomf"); ?></b>
    <input type="text" name="tdomf_spam_akismet_key" id="tdomf_spam_akismet_key" size="8" value="<?php echo $tdomf_spam_akismet_key; ?>" />
  </p>
  
  <p>
  <input type="radio" name="tdomf_spam_notify" id="tdomf_spam_notify_live" value="live"<?php if($tdomf_spam_notify == "live"){ ?> checked <?php } ?>>
  <?php _e("Recieve normal moderation emails for suspected spam submissions","tdomf"); ?>
  <br/>
  
  <input type="radio" name="tdomf_spam_notify" id="tdomf_spam_notify_none" value="none"<?php if($tdomf_spam_notify == "none" || $tdomf_spam_notify == false){ ?> checked <?php } ?>>
  <?php _e("Recieve no notification of spam submissions","tdomf"); ?>
  <br/>
  </p>
 
  <p>
  <input type="radio" name="tdomf_spam_auto_delete" id="tdomf_spam_auto_delete_manual" value="manual"<?php if(!$tdomf_spam_auto_delete_now && !$tdomf_spam_auto_delete) { ?> checked <?php } ?>>
  <?php _e("Manually manage spam","tdomf"); ?>
  <br/>         
  <input type="radio" name="tdomf_spam_auto_delete" id="tdomf_spam_auto_delete_month" value="month"<?php if($tdomf_spam_auto_delete){ ?> checked <?php } ?>>
  <?php _e("Automatically Delete Spam older than a month ","tdomf"); ?>
  <!-- <br/>
  <input type="radio" name="tdomf_spam_auto_delete" value="now" id="tdomf_spam_auto_delete_now" <?php if($tdomf_spam_auto_delete_now) { ?> checked <?php } ?>>
  <?php _e("Automatically Delete Spam when found","tdomf"); ?> --> 
  </p>         
  
  <?php } ?>
  
  <p>
  <input type="checkbox" name="tdomf_nospam_user" id="tdomf_nospam_user" 
        <?php if($tdomf_nospam_user) { ?> checked <?php } ?>
        <?php if(!$spam_enabled) { ?> disabled <?php } ?>>
  <?php _e("Do not check for spam if contributer or submitter is a registered user","tdomf"); ?>
  <br/>
  <input type="checkbox" name="tdomf_nospam_author" id="tdomf_nospam_author" 
        <?php if($tdomf_nospam_author) { ?> checked <?php } ?>
        <?php if(!$spam_enabled) { ?> disabled <?php } ?>>
  <?php _e("Do not check for spam if contributer is author or submitter (registered users only)","tdomf"); ?>
  <br/>
  <input type="checkbox" name="tdomf_nospam_trusted" id="tdomf_nospam_trusted"
        <?php if($tdomf_nospam_trusted) { ?> checked <?php } ?>
        <?php if(!$spam_enabled) { ?> disabled <?php } ?>>
  <?php _e("Do not check for spam if contributer or submitter is a trusted user","tdomf"); ?>
  <br/>
  <input type="checkbox" name="tdomf_nospam_publish" id="tdomf_nospam_publish"
        <?php if($tdomf_nospam_publish) { ?> checked <?php } ?>
        <?php if(!$spam_enabled) { ?> disabled <?php } ?>>
  <?php _e("Do not check for spam if contributer or submitter is a user with publish capabilities","tdomf"); ?>
  </p>

  <?php if($spam_enabled) { ?>
  <script type="text/javascript">
 //<![CDATA[          
  tdomf_enable_spam_options();
  <?php if($form_id) { ?>
  tdomf_enable_spam_overwrite();
  <?php } ?>
  //-->
  </script>
  <?php } ?>
<?php }

function tdomf_show_options_menu() {
    
  tdomf_handle_options_actions();
    
  ?> 
  <div class="wrap">
    
    <h2><?php _e('General Options', 'tdomf') ?></h2>

    <br/>
    
    <form method="post" action="admin.php?page=tdomf_show_options_menu">

    <?php if(function_exists('wp_nonce_field')){ wp_nonce_field('tdomf-options-save'); } ?>

    <div id="options_tabs" class="tabs">
    <ul>
        <li><a href="#opt_general"><span><?php _e('General','tdomf'); ?></span></a></li>
        <li><a href="#opt_new"><span><?php _e('Submissions','tdomf'); ?></span></a></li>
        <li><a href="#opt_form"><span><?php _e('Form Session Management','tdomf'); ?></span></a></li>
        <li><a href="#opt_spam"><span><?php _e('Spam Protection','tdomf'); ?></span></a></li>
        <li><a href="#opt_ui"><span><?php _e('User Interface','tdomf'); ?></span></a></li>
        <li><a href="#opt_debug"><span><?php _e('Debug','tdomf'); ?></span></a></li>
    </ul>
    
    <div id="opt_general" class="tabs">

	<p><?php _e("You <b>must</b> pick a default user to be used as the \"author\" of the post. This user cannot be able to publish or edit posts.","tdomf"); ?>
	  <br/><br/>

    <?php // update created users list (in case a user has been deleted)
      $created_users = get_option(TDOMF_OPTION_CREATEDUSERS);
      if($created_users != false) {
        $updated_created_users = array();
        foreach($created_users as $created_user) {
          if(get_userdata($created_user)){
            $updated_created_users[] = $created_user;
          }
        }
        update_option(TDOMF_OPTION_CREATEDUSERS,$updated_created_users);
      } ?>
    
	  <?php $def_aut = get_option(TDOMF_DEFAULT_AUTHOR);
           $def_aut_bad = false; ?>

	 <b><?php _e("Default Author","tdomf"); ?></b>
     <?php if(tdomf_get_all_users_count() < TDOMF_MAX_USERS_TO_DISPLAY) { ?>
    <select id="tdomf_def_user" name="tdomf_def_user">
    <?php $users = tdomf_get_all_users();
          $cnt_users = 0;
          foreach($users as $user) {
            $status = get_usermeta($user->ID,TDOMF_KEY_STATUS);
            $user_obj = new WP_User($user->ID);
            if($user->ID == $def_aut || (!$user_obj->has_cap("publish_posts"))) {
               $cnt_users++;
               ?>
              <option value="<?php echo $user->ID; ?>" <?php if($user->ID == $def_aut) { ?> selected <?php } ?> ><?php if($user_obj->has_cap("publish_posts")) {?><font color="red"><?php }?><?php echo $user->user_login; ?><?php if(!empty($status) && $status == TDOMF_USER_STATUS_BANNED) { ?> (Banned User) <?php } ?><?php if($user_obj->has_cap("publish_posts")) { $def_aut_bad = true; ?> (Error) </font><?php }?></option>
          <?php } } ?>
    </select>
     <?php } else {
         $def_aut_username = "";
         $cnt_users = 0;
         if($def_aut != false) {
             $user_obj = new WP_User($def_aut);
             $cnt_users = 1; // at least
             if($user_obj->has_cap("publish_posts")) { $def_aut_bad; }
             $def_aut_username = $user_obj->user_login;
         }
         ?>
         <input type="text" name="tdomf_def_user" id="tdomf_def_user" size="20" value="<?php echo htmlentities($def_aut_username,ENT_QUOTES,get_bloginfo('charset')); ?>" />
     <?php } ?>

    <?php if($def_aut_bad || $cnt_users <= 0) { ?>

    <?php $create_user_link = "admin.php?page=tdomf_show_options_menu&action=create_dummy_user";
	      if(function_exists('wp_nonce_url')){
	          $create_user_link = wp_nonce_url($create_user_link, 'tdomf-create-dummy-user');
          } ?>

    <br/><br/>          
          
    <a href="<?php echo $create_user_link; ?>">Create a dummy user &raquo;</a>
    <?php } ?>

    </p>

	<p>
	<?php _e('You can have the user automatically changed to "trusted" after a configurable number of approved submissions and/or contributions. Setting it the value to 0, means that a registered user is automatically trusted. Setting it to -1, disables the feature. A trusted user can still be banned. This only counts for submitters or contributors who register with your blog and submit using a user account.',"tdomf"); ?> <?php printf(__('You can change a users status (to/from trusted or banned) using the <a href="%s">Manage</a> menu',"tdomf"),"admin.php?page=tdomf_show_manage_menu"); ?>
	</p>

	<p>
	<b><?php _e("Auto Trust Submitter Count","tdomf"); ?></b>
	<input type="text" name="tdomf_trust_count" id="tdomf_trust_count" size="3" value="<?php echo htmlentities(get_option(TDOMF_OPTION_TRUST_COUNT),ENT_QUOTES,get_bloginfo('charset')); ?>" />
	</p>

    <p>
    <?php _e('When a user logs into Wordpress, they can access a "Your Submissions" page which contains a copy of the form. You can disable this page by disabling this option.','tdomf'); ?>
    </p>
     
    <?php $your_submissions = get_option(TDOMF_OPTION_YOUR_SUBMISSIONS); ?>

	</p>
	<b><?php _e("Enable 'Your Submissions' page ","tdomf"); ?></b>
	<input type="checkbox" name="tdomf_your_submissions" id="tdomf_your_submissions"  <?php if($your_submissions) echo "checked"; ?> >
	</p>
    
    
    </div> <!-- /opt_general -->
    
    <div id="opt_new" class="tabs">
    
    <p>
	<?php _e("If an entry is submitted by a subscriber and is published using the normal wordpress interface, the author can be changed to the person who published it, not submitted. Select this option if you want this to be automatically corrected. This problem only occurs on blogs that have more than one user who can publish.","tdomf"); ?>
	<br/><br/>

	<?php $fix_aut = get_option(TDOMF_AUTO_FIX_AUTHOR); ?>

	<b><?php _e("Auto-correct Author","tdomf"); ?></b>
	<input type="checkbox" name="tdomf_autocorrect_author" id="tdomf_autocorrect_author"  	<?php if($fix_aut) echo "checked"; ?> >
	</p>
    
	<p>
	<?php _e('If your theme displays the author of a post, you can automatically have it display the submitter info instead, if avaliable. It is recommended to use the "Who Am I" widget to get the full benefit of this option. The default and classic themes in Wordpress do not display the author of a post.',"tdomf"); ?>
    </p>

    <?php $on_author_theme_hack = get_option(TDOMF_OPTION_AUTHOR_THEME_HACK); ?>

	</p>
	<b><?php _e("Use submitter info for author in your theme","tdomf"); ?></b>
	<input type="checkbox" name="tdomf_author_theme_hack" id="tdomf_author_theme_hack"  <?php if($on_author_theme_hack) echo "checked"; ?> >
	</p>    
    
	<p>
	<?php _e('You can automatically add submitter info to the end of a post. This works on all themes.',"tdomf"); ?>
    </p>

    <?php $on_add_submitter = get_option(TDOMF_OPTION_ADD_SUBMITTER); ?>

	</p>
	<b><?php _e("Add submitter to end of post","tdomf"); ?></b>
	<input type="checkbox" name="tdomf_add_submitter" id="tdomf_add_submitter"  <?php if($on_add_submitter) echo "checked"; ?> >
	</p>
    
    </div><!-- /opt_new -->
    
    <div id="opt_form" class="tabs">
    
    <h3><?php _e('Form Verification Options',"tdomf"); ?></h3>
    
  <?php $tdomf_verify = get_option(TDOMF_OPTION_VERIFICATION_METHOD); ?>
  
  <p>
	<?php _e('You can use these options to set how a submission is verified as coming from a form created by TDOMF. You shouldn\'t need to modify these settings unless you are having a problem with "Bad Data" or invalid session keys',"tdomf"); ?>
	</p>

  <p>
  <input type="radio" name="tdomf_verify" value="default"<?php if($tdomf_verify == "default" || $tdomf_verify == false){ ?> checked <?php } ?>> 
  <?php _e('Use TDO-Mini-Forms internal Method',"tdomf"); ?>
  <br>

  <?php if(function_exists('wp_nonce_field')){ ?>  
  <input type="radio" name="tdomf_verify" value="wordpress_nonce"<?php if($tdomf_verify == "wordpress_nonce"){ ?> checked <?php } ?>>
  <?php _e("Use Wordpress nonce Method","tdomf"); ?>
  <br>
  <?php } ?>
  
  <input type="radio" name="tdomf_verify" value="none"<?php if($tdomf_verify == "none"){ ?> checked <?php } ?>>
  <?php if($tdomf_verify == "none"){ ?><font color="red"><?php } ?>
  <?php _e("Disable Verification (not recommended)","tdomf"); ?>
  <?php if($tdomf_verify == "none"){ ?></font><?php } ?>
  </p>
  
  <h3><?php _e('Form Session Data',"tdomf"); ?></h3>
    
  <?php $tdomf_form_data = get_option(TDOMF_OPTION_FORM_DATA_METHOD); ?>
  
  <p>
	<?php _e('The original and default method for moving data around for a form in use, uses <code>$_SESSION</code>. However this does not work on every platform, specifically if <code>register_globals</code> is enabled. The alternative method, using a database, should work in all cases as long as the user accepts the cookie. You shouldn\'t need to modify these settings unless you are having a problem with "Bad Data" or register_global.',"tdomf"); ?>
	</p>

  <p>
  <input type="radio" name="tdomf_form_data" value="session"<?php if($tdomf_form_data == "session" || $tdomf_form_data == false){ ?> checked <?php } ?><?php if(ini_get('register_globals')) { ?> disabled <?php } ?>> 
  <?php if(ini_get('register_globals')) { ?><del><?php } ?>
  <?php _e('Use <code>$_SESSION</code> to handle from session data (may not work on all host configurations)',"tdomf"); ?>
  <?php if(ini_get('register_globals')) { ?></del><?php } ?>
  <br>

  <input type="radio" name="tdomf_form_data" value="db"<?php if($tdomf_form_data == "db"){ ?> checked <?php } ?>>
  <?php _e("Use database (and cookie) to store session data (should work in all cases)","tdomf"); ?>
  <br>
  
  </p>
    
    </div> <!-- /opt_form -->
    
    <div id="opt_spam" class="tabs">
    
    <p>
    <?php printf(__('You can enable spam protection for new submissions and edits. 
                     The online service Akismet is used to identify if a submission or contribution is spam or not. 
                     You can moderate spam from the <a href="%s">Moderation</a> screen.
                     Some of these options can be overwritten on a per-form basis.',"tdomf"),"admin.php?page=tdomf_show_mod_posts_menu&show=spam&mode=list"); ?>
    </p>
    
    <?php tdomf_show_spam_options(); ?>
          
    </div> <!-- /opt_spam -->
    
    <div id="opt_ui" class="tabs">
    
        <?php if(tdomf_wp25()) { ?>
  
	<p>
	<?php _e('You can limit or increase the max size of the control form of a widget in the Form Widget screen. A value of 0 disables this feature.',"tdomf"); ?>
	</p>

	<p>
	<b><?php _e("Max Widget Width","tdomf"); ?></b>
	<input type="text" name="widget_max_width" id="widget_max_width" size="3" value="<?php echo intval(get_option(TDOMF_OPTION_WIDGET_MAX_WIDTH)); ?>" />
	</p>

  <p>
	<b><?php _e("Max Widget Height","tdomf"); ?></b>
	<input type="text" name="widget_max_height" id="widget_max_height" size="3" value="<?php echo intval(get_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT)); ?>" />
	</p>
      
  <?php } ?>
    
    <?php $tdomf_mod_show_links = get_option(TDOMF_OPTION_MOD_SHOW_LINKS); ?>
  
    <b><?php _e("Do not 'auto-hide' links on moderation screen","tdomf"); ?></b>
	        <input type="checkbox" name="tdomf_mod_show_links" id="tdomf_mod_show_links" <?php if($tdomf_mod_show_links) echo "checked"; ?> >
          </p>
  
    </div> <!-- /opt_ui -->
    
    <div id="opt_debug" class="tabs">
    
  <p>
  <?php _e('You can disable the display of errors to the user when they use this form. This does not stop errors being reported to the log or enable forms to be submitted with "Bad Data"','tdomf'); ?>
  </p>
  
  <?php $disable_errors = get_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES); ?>

	</p>
	<b><?php _e("Disable error messages being show to user","tdomf"); ?></b>
	<input type="checkbox" name="tdomf_disable_errors" id="tdomf_disable_errors"  <?php if($disable_errors) echo "checked"; ?> >
	</p>
  
  <p>
  <?php _e('You can enable extra debugs messages to aid in debugging problems. If you enable "Error Messages" this will also turn on extra PHP error checking.','tdomf'); ?>
  </p>
  
  <?php $extra_log = get_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES); ?>

	</p>
	<b><?php _e("Enable extra log messages ","tdomf"); ?></b>
	<input type="checkbox" name="tdomf_extra_log" id="tdomf_extra_log"  <?php if($extra_log) echo "checked"; ?> >
	</p>
    
        
	<p>
	<?php _e('Limit the number of lines in your tdomf log. A value of 0 disables the stored log.',"tdomf"); ?>
	</p>

	<p>
	<b><?php _e("Max Lines in Log","tdomf"); ?></b>
	<input type="text" name="tdomf_log_max_size" id="tdomf_log_max_size" size="4" value="<?php echo htmlentities(get_option(TDOMF_OPTION_LOG_MAX_SIZE),ENT_QUOTES,get_bloginfo('charset')); ?>" />
	</p>
    
    </div> <!-- /opt_debug -->
    
    </div> <!-- /tabs -->

    <br/>
    
    <table border="0"><tr>

    <td>
    <input type="hidden" name="save_settings" value="0" />
    <input type="submit" name="tdomf_save_button" id="tdomf_save_button" value="<?php _e("Save","tdomf"); ?> &raquo;" />
	</form>
    </td>

    <td>
    <form method="post" action="admin.php?page=tdomf_show_options_menu">
    <input type="submit" name="refresh" value="Refresh" />
    </form>
    </td>

    </tr></table>

   </div> 
   <?php
}

// Generate a dummy user
//
function tdomf_create_dummy_user() {
   $rand_username = "tdomf_".tdomf_random_string(5);
   $rand_password = tdomf_random_string(8);
   tdomf_log_message("Attempting to create dummy user $rand_username");
   $user_id = wp_create_user($rand_username,$rand_password);
   $user = new WP_User($user_id);
   if($user->has_cap("publish_posts")) {
      $user->remove_cap("publish_posts");
   }

   $users = get_option(TDOMF_OPTION_CREATEDUSERS);
   if($users == false) {
     $users = array( $user_id );
     add_option(TDOMF_OPTION_CREATEDUSERS,$users);
   } else {
     $users = array_merge( $users, array( $user_id ) );
     update_option(TDOMF_OPTION_CREATEDUSERS,$users);
   }
   
   update_option(TDOMF_DEFAULT_AUTHOR,$user_id);
   tdomf_log_message("Dummy user created for default author, user id = $user_id");
   return $user_id;
}

// Handle actions for this form
//
function tdomf_handle_options_actions() {
   global $wpdb, $wp_roles;

   $message = "";
   $retValue = false;
   
  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'create_dummy_user') {
     check_admin_referer('tdomf-create-dummy-user');
     tdomf_create_dummy_user();
     $message = "Dummy user created for Default Author!<br/>";
  } else if(isset($_REQUEST['save_settings']) && !isset($_REQUEST['tdomf_form_id'])) {

      check_admin_referer('tdomf-options-save');

      // Default Author

      $def_aut = $_POST['tdomf_def_user'];
      if(!empty($def_aut) && !is_numeric($def_aut)) {
          if(($userdata = get_userdatabylogin($def_aut)) != false) {
              $def_aut = $userdata->ID;
          } else { 
              $message .= "<font color='red'>".sprintf(__("The user %s is not a valid user and cannot be used for Default Author","tdomf"),$def_aut)."</font><br/>";
              $def_aut = false;              
          }
      }
      update_option(TDOMF_DEFAULT_AUTHOR,$def_aut);

      // Author and Submitter fix

      $fix_aut = false;
      if(isset($_POST['tdomf_autocorrect_author'])) { $fix_aut = true; }
      update_option(TDOMF_AUTO_FIX_AUTHOR,$fix_aut);

      //Auto Trust Submitter Count

      $cnt = -1;
      if(isset($_POST['tdomf_trust_count']) 
       && !empty($_POST['tdomf_trust_count']) 
       && is_numeric($_POST['tdomf_trust_count'])){ 
         $cnt = intval($_POST['tdomf_trust_count']);
      }
      update_option(TDOMF_OPTION_TRUST_COUNT,$cnt);

      //Author theme hack

      $author_theme_hack = false;
      if(isset($_POST['tdomf_author_theme_hack'])) { $author_theme_hack = true; }
      update_option(TDOMF_OPTION_AUTHOR_THEME_HACK,$author_theme_hack);

      //Add submitter info

      $add_submitter = false;
      if(isset($_POST['tdomf_add_submitter'])) { $add_submitter = true; }
      update_option(TDOMF_OPTION_ADD_SUBMITTER,$add_submitter);

      //disable errors
      
      $disable_errors = false;
      if(isset($_POST['tdomf_disable_errors'])) { $disable_errors = true; }
      update_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES,$disable_errors);
      
      // extra log messages
      
      $extra_log = false;
      if(isset($_POST['tdomf_extra_log'])) { $extra_log = true; }
      update_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES,$extra_log);
      
      // your submissions
      
      $your_submissions = false;
      if(isset($_POST['tdomf_your_submissions'])) { $your_submissions = true; }
      update_option(TDOMF_OPTION_YOUR_SUBMISSIONS,$your_submissions);

      // default widget max sizes
      
      if(tdomf_wp25()) {
        
        $widget_max_width = intval($_POST['widget_max_width']);
        update_option(TDOMF_OPTION_WIDGET_MAX_WIDTH,$widget_max_width);
        
        $widget_max_height = intval($_POST['widget_max_height']);
        update_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT,$widget_max_height);
        
      }
      
      // verification method
      
      $tdomf_verify = $_POST['tdomf_verify'];
      update_option(TDOMF_OPTION_VERIFICATION_METHOD,$tdomf_verify);
      
      $tdomf_form_data = $_POST['tdomf_form_data'];
      update_option(TDOMF_OPTION_FORM_DATA_METHOD,$tdomf_form_data);
      
      // Show links on moderation screen
      
      $tdomf_mod_show_links = isset($_POST['tdomf_mod_show_links']);
      update_option(TDOMF_OPTION_MOD_SHOW_LINKS,$tdomf_mod_show_links);
      
      
      // spam options
      
      $message .= tdomf_handle_spam_options_actions();
           

      // log options
      
      $tdomf_log_max_size = intval($_POST['tdomf_log_max_size']);
      update_option(TDOMF_OPTION_LOG_MAX_SIZE,$tdomf_log_max_size);
      
      $message .= "Options Saved!<br/>";
      tdomf_log_message("Options Saved");
      
  } 
  
   // Warnings

   $message .= tdomf_get_error_messages(false);

   if(!empty($message)) { ?>
   <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
   <?php }
   
   return $retValue;
}

// Check for error messages with options and return a message
//
function tdomf_get_error_messages($show_links=true, $form_id=0) {
  global $wpdb, $wp_roles;
  if(!isset($wp_roles)) {
  	$wp_roles = new WP_Roles();
  }
  $roles = $wp_roles->role_objects;
  $message = "";
  
  #if(ini_get('register_globals') && !TDOMF_HIDE_REGISTER_GLOBAL_ERROR){
  #  $message .= "<font color=\"red\"><strong>".__("ERROR: <em>register_globals</em> is enabled. This is a security risk and also prevents TDO Mini Forms from working.")."</strong></font>";
  #}
  
  if(version_compare("5.0.0",phpversion(),">"))
  {
    $message .= sprintf(__("Warning: You are currently using PHP version %s. It is strongly recommended to use PHP5 with TDO Mini Forms.","tdomf"),phpversion());
    $message .= "<br/>";
  }
  
  if(get_option(TDOMF_OPTION_VERIFICATION_METHOD) == 'none') {
    $message .= __("Warning: Form input verification is disabled. This is a potential security risk.","tdomf");
    $message .= "<br/>";
  }
  
  # Revisions disabled => editing won't work well
  
  if ( !constant('WP_POST_REVISIONS') ) {
      $form_ids = tdomf_get_form_ids();
      foreach($form_ids as $a_form_id) {
          if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$a_form_id->form_id)) {
              $message .= __("Error: Post Revisioning is disabled, post editing will not work correctly!","tdomf");
              $message .= "<br/>";
              break;
          }
      }
  }
  
    if(isset($_REQUEST['form']) || $form_id != 0) {
        if($form_id == 0)
        {
            $form_id = intval($_REQUEST['form']);
        }
        
        // permissions error
        
        if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) == false) {
            
            $caps = tdomf_get_option_form(TDOMF_OPTION_ALLOW_CAPS,$form_id);
            if(is_array($caps) && empty($caps)) { $caps = false; } 
            $users = tdomf_get_option_form(TDOMF_OPTION_ALLOW_USERS,$form_id);
            if(is_array($users) && empty($users)) { $users = false; }
            $publish = tdomf_get_option_form(TDOMF_OPTION_ALLOW_PUBLISH,$form_id);
            
            $role_count = 0;
            $role_publish_count = 0;
            foreach($roles as $role) {
                if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])){
                    $role_count++;
                    if(isset($role->capabilities['publish_posts'])) {
                        $role_publish_count++;
                    }
                }
            }
            
            // if nothing set
            
            if($role_count == 0 && $caps == false && $users == false && $publish == false) {
                if($show_links) {
                    $message .= "<font color=\"red\">".sprintf(__("<b>Warning</b>: No-one has been configured to be able to access the form! <a href=\"%s\">Configure on Options Page &raquo;</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_form_options_menu&form=$form_id")."</font><br/>";
                } else {
                    $message .= "<font color=\"red\">".__("<b>Warning</b>: No-one has been configured to be able to access the form!", "tdomf")."</font><br/>";
                }
                tdomf_log_message("No-one has been configured to access this form ($form_id)",TDOMF_LOG_BAD);
            } 
            
            // if only publish set

            else if($caps == false && $users == false && $role_count == $role_publish_count && $publish == false ) {
    
                if($show_links) {
                    $message .= "<font color=\"red\">".sprintf(__("<b>Warning</b>: Only users who can <i>already publish posts</i>, can see the form! <a href=\"%s\">Configure on Options Page &raquo;</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_form_options_menu&form=$form_id")."</font><br/>";
                } else {
                    $message .= "<font color=\"red\">".__("<b>Warning</b>: Only users who can <i>already publish posts</i>, can see this form!", "tdomf")."</font><br/>";
                }
                tdomf_log_message("Only users who can already publish can access the form ($form_id)",TDOMF_LOG_BAD);
            }
        }
   
        // form hacker modified
        
        $mode = tdomf_generate_default_form_mode($form_id) . '-hack';
        
        $curr_unmod_prev = trim(tdomf_preview_form(array('tdomf_form_id' => $form_id),$mode));
        $org_unmod_prev = trim(tdomf_get_option_form(TDOMF_OPTION_FORM_PREVIEW_HACK_ORIGINAL,$form_id));
        $hacked_prev = trim(tdomf_get_option_form(TDOMF_OPTION_FORM_PREVIEW_HACK,$form_id));
        if($hacked_prev != false && $curr_unmod_prev != $org_unmod_prev) {
            $message .= "<font color=\"red\">";
            $diffs = "admin.php?page=tdomf_show_form_hacker&form=$form_id&mode=$mode&diff&form2=cur&form1=org&type=preview";
            $form_hacker = "admin.php?page=tdomf_show_form_hacker&form=$form_id";
            $dismiss = wp_nonce_url("admin.php?page=tdomf_show_form_hacker&form=$form_id&dismiss&type=preview",'tdomf-form-hacker');
            $message .= sprintf(__("<b>Warning</b>: Form configuration has been changed that affect the preview output but Form Hacker has not been updated! <a href='%s'>Diff &raquo;</a> | <a href='%s'>Hack Form &raquo;</a> | <a href='%s'>Dismiss</a>","tdomf"),$diffs,$form_hacker,$dismiss);
            $message .= "</font><br/>";
        }
        
        $curr_unmod_form = trim(tdomf_generate_form($form_id,$mode));
        $org_unmod_form = trim(tdomf_get_option_form(TDOMF_OPTION_FORM_HACK_ORIGINAL,$form_id));
        $hacked_form = trim(tdomf_get_option_form(TDOMF_OPTION_FORM_HACK,$form_id));
        if($hacked_form != false && $curr_unmod_form != $org_unmod_form) {
            $message .= "<font color=\"red\">";
            $diffs = "admin.php?page=tdomf_show_form_hacker&form=$form_id&mode=$mode&diff&form2=cur&form1=org";
            $form_hacker = "admin.php?page=tdomf_show_form_hacker&form=$form_id";
            $dismiss = wp_nonce_url("admin.php?page=tdomf_show_form_hacker&form=$form_id&dismiss",'tdomf-form-hacker');
            $message .= sprintf(__("<b>Warning</b>: Form configuration has been changed that affect the generated form but Form Hacker has not been updated! <a href='%s'>Diff &raquo;</a> | <a href='%s'>Hack Form &raquo;</a> | <a href='%s'>Dismiss</a>","tdomf"),$diffs,$form_hacker,$dismiss);
            $message .= "</font><br/>";
        }
        
        // widget errors
        
        global $tdomf_form_widgets_admin_errors;
        $mode = "new-post";        
        if(tdomf_get_option_form(TDOMF_OPTION_SUBMIT_PAGE,$form_id)) {
            $mode = "new-page";
        }
        $uri = "admin.php?page=tdomf_show_form_menu&form=".$form_id;
        do_action('tdomf_control_form_start',$form_id,$mode);
        $widget_order = tdomf_get_widget_order($form_id);
        $widgets = tdomf_filter_widgets($mode, $tdomf_form_widgets_admin_errors);
        foreach($widget_order as $w) {
              if(isset($widgets[$w])) {
                  $widget_message = call_user_func($widgets[$w]['cb'],$form_id,$widgets[$w]['params']);
                  if(!empty($widget_message)) {
                      $message .= "<font color=\"red\">" . $widget_message . sprintf(__(" <a href='%s'>Fix &raquo;</a>","tdomf"),$uri)."</font><br/>";
                  }
              }
          }
          
         // @todo check that key is unique in custom fields
    }
        
    if(get_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES) && !get_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES)) {
         $message .= "<font color=\"red\">";
         if($show_links) {
             $message .= sprintf(__("<b>Warning:</b> You have enabled 'Extra Debug Messages' and disabled 'Disable Error Messages'. This invokes a special mode where all PHP errors are turned on. This can lead to unexpected problems and could be considered a security leak! <a href=\"%s\">Change on the Options Page &raquo;</a>", "tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu");
         } else {
             $message .= __("<b>Warning:</b> You have enabled 'Extra Debug Messages' and disabled 'Disable Error Messages'. This invokes a special mode where all PHP errors are turned on. This can lead to unexpected problems and could be considered a security leak! This should only be used for debugging purposes.","tdomf");
         }
         $message .= "</font><br/>";
    }
    
       $create_user_link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu&action=create_dummy_user";
	    if(function_exists('wp_nonce_url')){
	          $create_user_link = wp_nonce_url($create_user_link, 'tdomf-create-dummy-user');
    }
	  if(get_option(TDOMF_DEFAULT_AUTHOR) == false) {
	 	  $message .= "<font color=\"red\">".sprintf(__("<b>Error</b>: No default author set! <a href=\"%s\">Create dummy user for default author automatically &raquo;</a>","tdomf"),$create_user_link)."</font><br/>";
	 	  tdomf_log_message("Option Default Author not set!",TDOMF_LOG_BAD);
 	  } else {
          
    $def_aut = new WP_User(get_option(TDOMF_DEFAULT_AUTHOR));
    if(empty($def_aut->data->ID)) {
        // User does not exist! Deleting option
        delete_option(TDOMF_DEFAULT_AUTHOR);
        $message .= "<font color=\"red\">".sprintf(__("<b>Error</b>: Current Default Author does not exist! <a href=\"%s\">Create dummy user for default author automatically &raquo;</a>","tdomf"),$create_user_link)."</font><br/>";
	 	    tdomf_log_message("Current Default Author does not exist! Deleting option.",TDOMF_LOG_BAD);
      }      
 	  	if($def_aut->has_cap("publish_posts")) {
	 	  $message .= "<font color=\"red\">".sprintf(__("<b>Error</b>: Default author can publish posts. Default author should not be able to publish posts! <a href=\"%s\">Create a dummy user for default author automatically &raquo;</a>","tdomf"),$create_user_link)."</font><br/>";
	 	  tdomf_log_message("Option Default Author is set to an author who can publish posts.",TDOMF_LOG_BAD);
 	  	}
    }
    
    if(function_exists('wp_get_http'))
    {
        $post_uri = TDOMF_URLPATH.'tdomf-form-post.php';
        $headers = wp_get_http($post_uri,false,1);
        if($headers != false && $headers["response"] != '200')
        {
             $message .= "<font color=\"red\">";
             $message .= sprintf(__("<b>Error</b>: Got a %d error when checking <a href=\"%s\">%s</a>! This will prevent posts from being submitted. The permissions may be wrong on the tdo-mini-forms folder.","tdomf"),$headers["response"], $post_uri, $post_uri);
             $message .= "</font><br/>";
             tdomf_log_message("Did not receive a 200 response when checking $post_uri:<pre>".var_export($headers,true)."</pre>",TDOMF_LOG_ERROR);
        }

        $ajax_uri = TDOMF_URLPATH.'tdomf-form-ajax.php';
        $headers = wp_get_http($ajax_uri,false,1);
        if($headers != false && $headers["response"] != '200')
        {
             $message .= "<font color=\"red\">";
             $message .= sprintf(__("<b>Error</b>: Got a %d error when checking <a href=\"%s\">%s</a>! This will prevent forms that use AJAX from submitting posts. The permissions may be wrong on the tdo-mini-forms folder.","tdomf"),$headers["response"], $ajax_uri, $ajax_uri);
             $message .= "</font><br/>";
             tdomf_log_message("Did not receive a 200 response when checking $ajax_uri:<pre>".var_export($headers,true)."</pre>",TDOMF_LOG_ERROR);
        }
        
        $css_uri = TDOMF_URLPATH.'tdomf-style-form.css';
        $headers = wp_get_http($css_uri,false,1);
        if($headers != false && $headers["response"] != '200')
        {
             $message .= "<font color=\"red\">";
             $message .= sprintf(__("<b>Error</b>: Got a %d error when checking <a href=\"%s\">%s</a>! This will make your forms, by default, look very ugly. The permissions may be wrong on the tdo-mini-forms folder.","tdomf"),$headers["response"], $css_uri, $css_uri);
             $message .= "</font><br/>";
             tdomf_log_message("Did not receive a 200 response when checking $css_uri:<pre>".var_export($headers,true)."</pre>",TDOMF_LOG_ERROR);
        }
    }
    
    return $message;
}

?>
