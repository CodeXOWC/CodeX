<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/*define('SCRIPT_DEBUG', true);*/

function tdomf_load_form_options_admin_scripts() {
    /* for tabs */
    wp_enqueue_script( 'jquery-ui-tabs' );
}
add_action("load-".sanitize_title(__('TDO Mini Forms', 'tdomf'))."_page_tdomf_show_form_options_menu","tdomf_load_form_options_admin_scripts");

function tdomf_form_options_admin_head() {
    global $wp_version;
    /* add style options and start tabs for options page */
    if(preg_match('/tdomf_show_form_options_menu/',$_SERVER['REQUEST_URI'])) { ?>
           
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
            .ui-tabs-nav li.ui-tabs-disabled a, .ui-tabs-nav li.ui-tabs-disabled a:hover, li.ui-state-disabled {
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
           jQuery(document).ready(function(){
               <?php if(version_compare($wp_version,"2.8-beta2",">=")) { ?>
                   jQuery("#form_options_tabs").tabs();
                   jQuery("#options_access_tabs").tabs();
               <?php } else { ?>
                   jQuery("#form_options_tabs > ul").tabs();
                   jQuery("#options_access_tabs > ul").tabs();
               <?php } ?>
           });
           </script>
           
    <?php }
}
add_action( 'admin_head', 'tdomf_form_options_admin_head' );

  /**
   * get an array with all capabilities
   * copied from role-manager 2 plugin
   */
function tdomf_get_all_caps() {
    global $wp_roles;
    
    // Get Role List
    foreach($wp_roles->role_objects as $key => $role) {
      foreach($role->capabilities as $cap => $grant) {
        $capnames[$cap] = $cap;
        //$this->debug('grant', ($role->capabilities));
      }
    }
    
    $capnames = apply_filters('capabilities_list', $capnames);
    if(!is_array($capnames)) $capnames = array();
    $capnames = array_unique($capnames);
    sort($capnames);

    //Filter out the level_x caps, they're obsolete
    $capnames = array_diff($capnames, array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5',
        'level_6', 'level_7', 'level_8', 'level_9', 'level_10'));
    
    //Filter out roles
      foreach ($wp_roles->get_names() as $role) {
        $key = array_search($role, $capnames);
        if ($key !== false && $key !== null) { //array_search() returns null if not found in 4.1
          unset($capnames[$key]);
        }
      }
      
      // this cap is used seperately 
      unset($capnames['publish_post']);
      
      // filter out tdomf caps that were added
      foreach($capnames as $key => $cap) {
          if(substr($cap,intval(TDOMF_CAPABILITY_CAN_SEE_FORM),strlen(TDOMF_CAPABILITY_CAN_SEE_FORM)) == TDOMF_CAPABILITY_CAN_SEE_FORM) {
              unset($capnames[$key]);
          }
      }
      
    return $capnames;
  }

function tdomf_show_form_options($form_id) {
  global $wp_version;
  if(!tdomf_form_exists($form_id)) { ?>
    <div class="wrap"><font color="red"><?php printf(__("Form id %d does not exist!","tdomf"),$form_id); ?></font></div>
  <?php } else { ?>
    
    <?php $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id);
          $updated_pages = false;
          if($pages != false) {
            $updated_pages = array();
            foreach($pages as $page_id) {
              if(get_permalink($page_id) != false) {
                $updated_pages[] = $page_id; 
              }
            }
            if(count($updated_pages) == 0) { $updated_pages = false; }
            tdomf_set_option_form(TDOMF_OPTION_CREATEDPAGES,$updated_pages,$form_id);
          } ?>

    
    <div class="wrap">
    
    <h2><?php printf(__("Form %d Options","tdomf"),$form_id); ?></h2>
    
    <?php tdomf_forms_under_title_toolbar($form_id); ?>
     
          <?php if($updated_pages == false) { ?>
          
             <?php $create_form_link = "admin.php?page=tdomf_show_form_options_menu&action=create_form_page&form=$form_id";
          if(function_exists('wp_nonce_url')){
          	$create_form_link = wp_nonce_url($create_form_link, 'tdomf-create-form-page');
          } ?>
    <p><a href="<?php echo $create_form_link; ?>"><?php _e("Create a page with this form automatically &raquo;","tdomf"); ?></a></p>
          <?php } ?>
          
    <?php if(tdomf_wp23()) { ?>
          <p><a href="admin.php?page=tdomf_show_form_menu&form=<?php echo $form_id; ?>"><?php printf(__("Widgets for Form %d &raquo;","tdomf"),$form_id); ?></a></p>
    <?php } ?>
    
    <br/>
    
    <form method="post" action="admin.php?page=tdomf_show_form_options_menu&form=<?php echo $form_id; ?>">

    <input type="hidden" id="tdomf_form_id" name="tdomf_form_id" value="<?php echo $form_id; ?>" />
    <?php if(function_exists('wp_nonce_field')){ wp_nonce_field('tdomf-options-save'); } ?>
    
    <div id="form_options_tabs" class="tabs">
    <ul>
        <li><a href="#form_gen"><span><?php _e('General','tdomf'); ?></span></a></li>
        <li><a href="#form_access"><span><?php _e('Access Control','tdomf'); ?></span></a></li>
        <li><a href="#form_new"><span><?php _e('Submitting','tdomf'); ?></span></a></li>
        <li><a href="#form_edit"><span><?php _e('Editing','tdomf'); ?></span></a></li>
        <li><a href="#form_moderation"><span><?php _e('Moderation','tdomf'); ?></span></a></li>
        <li><a href="#form_throttling"><span><?php _e('Throttling','tdomf'); ?></span></a></li>
        <li><a href="#form_spam"><span><?php _e('Spam Protection','tdomf'); ?></span></a></li>
    </ul>

    <div id="form_gen">

<?php $form_name = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id); ?>
    <p>
        <label for="tdomf_form_name">
            <?php _e("Form Name:","tdomf"); ?>
        </label>
        <input type="text" name="tdomf_form_name" id="tdomf_form_name" value="<?php if($form_name) { echo htmlentities(stripslashes($form_name),ENT_QUOTES,get_bloginfo('charset')); } ?>" />
	</p>    
     
   <?php $form_descp = tdomf_get_option_form(TDOMF_OPTION_DESCRIPTION,$form_id); ?>
   <p>
        <label for="tdomf_form_descp">   
            <?php _e('Form Description',"tdomf"); ?><br/>
        </label>
        <textarea cols="80" rows="3" name="tdomf_form_descp" id="tdomf_form_descp"><?php if($form_descp) { echo htmlentities(stripslashes($form_descp),ENT_NOQUOTES,get_bloginfo('charset')); } ?></textarea>
   </p>

    <script type="text/javascript">
         //<![CDATA[
        
          function tdomf_enable_use_type_posts() {
            var flag = document.getElementById("tdomf_use_type_posts").checked;
            var flag_edit = document.getElementById("tdomf_mode_edit").checked;
            var flag_new = document.getElementById("tdomf_mode_new").checked;
            if(flag_new) {
                document.getElementById("tdomf_def_cat").disabled = !flag;
            }
            if(flag_edit) {
                //document.getElementById("tdomf_edit_cat_only").disabled = !flag;
                document.getElementById("tdomf_edit_cats").disabled = !flag;
            }
          }
          
          function tdomf_enable_edit() {
            var flag = !document.getElementById("tdomf_mode_edit").checked;
            var flag_posts = document.getElementById("tdomf_use_type_posts").checked;
            
            // disable 'new' options
            
            var flag_posts = document.getElementById("tdomf_use_type_posts").checked;
            if(flag_posts) {
                document.getElementById("tdomf_def_cat").disabled = !flag;
            }
            document.getElementById("tdomf_queue_period").disabled = !flag;
            document.getElementById("tdomf_queue_on_all").disabled = !flag;

            // re-enable 'edit' options
            
            document.getElementById("tdomf_edit_tdomf_only").disabled = flag;
            if(flag_posts) {
                //document.getElementById("tdomf_edit_cat_only").disabled = flag;
                document.getElementById("tdomf_edit_cats").disabled = flag;
            }
            
            document.getElementById("tdomf_ajax_edit").disabled = flag;
            document.getElementById("tdomf_add_edit_link_none").disabled = flag;
            <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?> 
                document.getElementById("tdomf_add_edit_link_your_submissions").disabled = flag;
            <?php } ?>
            document.getElementById("tdomf_add_edit_link_custom").disabled = flag;
            document.getElementById("tdomf_add_edit_link_custom_url").disabled = flag;
            
            document.getElementById("tdomf_auto_edit_link_none").disabled = flag;
            <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?> 
                document.getElementById("tdomf_auto_edit_link_your_submissions").disabled = flag;
            <?php } ?>
            document.getElementById("tdomf_auto_edit_link_custom").disabled = flag;
            document.getElementById("tdomf_auto_edit_link_custom_url").disabled = flag;
            
            document.getElementById("tdomf_author_edit").disabled = flag;
            document.getElementById("tdomf_time_edit").disabled = flag;

            // enable / disable tabs
            
            <?php if(version_compare($wp_version,"2.8-beta2",">=")) { ?>
            var selected = jQuery("#form_options_tabs").data('selected.tabs');
            if(!flag) {
                jQuery("#form_options_tabs").tabs("enable",3);
                if(selected == 1) {
                    jQuery("#form_options_tabs").tabs("select",3);
                }
                jQuery("#form_options_tabs").tabs("disable",2);
            } else {
                jQuery("#form_options_tabs").tabs("enable",2);
                if(selected == 2) {
                    jQuery("#form_options_tabs").tabs("select",2);
                }
                jQuery("#form_options_tabs").tabs("disable",3);
            }
            <?php } else { ?>
            var selected = jQuery("#form_options_tabs > ul").data('selected.tabs');                
            if(!flag) {
                jQuery("#form_options_tabs > ul").tabs("enable",3);
                if(selected == 1) {
                    jQuery("#form_options_tabs > ul").tabs("select",3);
                }
                jQuery("#form_options_tabs > ul").tabs("disable",2);
            } else {
                jQuery("#form_options_tabs > ul").tabs("enable",2);
                if(selected == 2) {
                    jQuery("#form_options_tabs > ul").tabs("select",2);
                }
                jQuery("#form_options_tabs > ul").tabs("disable",3);
            }
            <?php } ?>
          }
          
          function tdomf_enable_new() {
            var flag = !document.getElementById("tdomf_mode_new").checked;
            
            // re-enable 'new' options
            
            var flag_posts = document.getElementById("tdomf_use_type_posts").checked;
            if(flag_posts) {
                document.getElementById("tdomf_def_cat").disabled = flag;
            }
            document.getElementById("tdomf_queue_period").disabled = flag;
            document.getElementById("tdomf_queue_on_all").disabled = flag;            
            
            // disable 'edit' options
            
            document.getElementById("tdomf_edit_tdomf_only").disabled = !flag;
            if(flag_posts) {
                //document.getElementById("tdomf_edit_cat_only").disabled = !flag;
                document.getElementById("tdomf_edit_cats").disabled = !flag;
            }
            
            document.getElementById("tdomf_ajax_edit").disabled = flag;
            document.getElementById("tdomf_add_edit_link_none").disabled = flag;
            <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?> 
                document.getElementById("tdomf_add_edit_link_your_submissions").disabled = flag;
            <?php } ?>
            document.getElementById("tdomf_add_edit_link_custom").disabled = flag;
            document.getElementById("tdomf_add_edit_link_custom_url").disabled = flag;
            
            document.getElementById("tdomf_auto_edit_link_none").disabled = flag;
            <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?> 
                document.getElementById("tdomf_auto_edit_link_your_submissions").disabled = flag;
            <?php } ?>
            document.getElementById("tdomf_auto_edit_link_custom").disabled = flag;
            document.getElementById("tdomf_auto_edit_link_custom_url").disabled = flag;
            
            document.getElementById("tdomf_author_edit").disabled = !flag;
            document.getElementById("tdomf_time_edit").disabled = !flag;
            
            // enable / disable tabs
            
            <?php if(version_compare($wp_version,"2.8-beta2",">=")) { ?>
            var selected = jQuery("#form_options_tabs").data('selected.tabs');
            if(!flag) {
                jQuery("#form_options_tabs").tabs("enable",2);
                if(selected == 2) {
                    jQuery("#form_options_tabs").tabs("select",2);
                }
                jQuery("#form_options_tabs").tabs("disable",3);
            } else {
                jQuery("#form_options_tabs").tabs("enable",3);
                if(selected == 1) {
                    jQuery("#form_options_tabs").tabs("select",3);
                }
                jQuery("#form_options_tabs").tabs("disable",2);
            }                
            <?php } else { ?>
            var selected = jQuery("#form_options_tabs > ul").data('selected.tabs');
            if(!flag) {
                jQuery("#form_options_tabs > ul").tabs("enable",2);
                if(selected == 2) {
                    jQuery("#form_options_tabs > ul").tabs("select",2);
                }
                jQuery("#form_options_tabs > ul").tabs("disable",3);
            } else {
                jQuery("#form_options_tabs > ul").tabs("enable",3);
                if(selected == 1) {
                    jQuery("#form_options_tabs > ul").tabs("select",3);
                }
                jQuery("#form_options_tabs > ul").tabs("disable",2);
            }
            <?php } ?>
          }
    //-->
    </script>
   
    <?php $use_page = tdomf_get_option_form(TDOMF_OPTION_SUBMIT_PAGE,$form_id); ?>
    <?php $edit_form = tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id); ?>
    
    
    <script type="text/javascript">
         //<![CDATA[
         jQuery(document).ready(function(){
         <?php if(!$edit_form) { ?>
           <?php if(version_compare($wp_version,"2.8-beta2",">=")) { ?>
           jQuery("#form_options_tabs").tabs("disable",3);               
           <?php } else { ?>
           jQuery("#form_options_tabs > ul").tabs("disable",3);
           <?php } ?>
         <?php } else { ?>
           <?php if(version_compare($wp_version,"2.8-beta2",">=")) { ?>
           jQuery("#form_options_tabs").tabs("disable",2);               
           <?php } else { ?>
           jQuery("#form_options_tabs > ul").tabs("disable",2);
           <?php } ?>
         <?php } ?>
         });
    //-->
    </script>
    
	<p>
        <?php _e('This form will be used to','tdomf'); ?>
        <input type="radio" name="tdomf_mode" id="tdomf_mode_new" value="new" <?php if(!$edit_form) echo "checked"; ?> onChange="tdomf_enable_new();" ><?php _e('submit new or','tdomf'); ?>
        <input type="radio" name="tdomf_mode" id="tdomf_mode_edit" value="edit" <?php if($edit_form) echo "checked"; ?> onChange="tdomf_enable_edit();" ><?php _e('edit existing ','tdomf'); ?>
        <input type="radio" name="tdomf_use_type" id="tdomf_use_type_posts" value="post" <?php if(!$use_page) echo "checked"; ?> onChange="tdomf_enable_use_type_posts();" ><?php _e('Posts or','tdomf'); ?>
        <input type="radio" name="tdomf_use_type" id="tdomf_use_type_pages" value="page" <?php if($use_page) echo "checked"; ?> onChange="tdomf_enable_use_type_posts();" ><?php _e('Pages','tdomf'); ?>
    </p>   

     <p>
    </p>  
    
    <p>
        <?php $on_preview = tdomf_get_option_form(TDOMF_OPTION_PREVIEW,$form_id); ?>
        <input type="checkbox" name="tdomf_preview" id="tdomf_preview"  <?php if($on_preview) echo "checked"; ?> >
        <label for="tdomf_include_sub">
            <?php _e("Enable Preview","tdomf"); ?>
        </label>
        <br/>
        
        <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
            <?php $inc_sub = tdomf_get_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$form_id); ?>
            <input type="checkbox" name="tdomf_include_sub" id="tdomf_include_sub" <?php if($inc_sub) echo "checked"; ?> >
            <label for="tdomf_include_sub">
               <?php _e("Include on 'Your Submissions' page","tdomf"); ?>
            </label>
            <br/>
        <?php } ?>
        
        <?php $ajax = tdomf_get_option_form(TDOMF_OPTION_AJAX,$form_id); ?>
        <input type="checkbox" name="tdomf_ajax" id="tdomf_ajax"  <?php if($ajax) echo "checked"; ?> >
        <label for="tdomf_ajax">
            <?php _e("Enable AJAX","tdomf"); ?>
        </label>
    </p>
        
    <?php $from_email = tdomf_get_option_form(TDOMF_OPTION_FROM_EMAIL,$form_id); ?>
    <p>
        <label for="tdomf_from_email">
	        <?php _e("'From Email' Address to use in notifications to users. Leave blank for Wordpress default.","tdomf"); ?><br/>
        </label>
        <input type="text" name="tdomf_from_email" id="tdomf_from_email" size="80" value="<?php if($from_email) { echo htmlentities($from_email,ENT_QUOTES,get_bloginfo('charset')); } ?>" >
	</p>
  
    <?php $widget_count = tdomf_get_option_form(TDOMF_OPTION_WIDGET_INSTANCES,$form_id);
        if($widget_count == false) { $widget_count = 9; } ?>
	<p>
        <label for="tdomf_widget_count">
            <?php _e("Number of multiple-instances Widgets to have on Form Widgets page:","tdomf"); ?>
        </label>
        <input type="text" name="tdomf_widget_count" id="tdomf_widget_count" size="3" value="<?php echo htmlentities(strval($widget_count),ENT_QUOTES,get_bloginfo('charset')); ?>" />
	</p>
    
    </div><!-- /form_gen -->
    
    <div id="form_access">
    
    <p><?php _e("You can control access to the form based on user roles, capabilities or by specific users. You can chose \"Unregistered Users\" if you want anyone to be able to access the form, including visitors to your site that do not have user accounts. The old behaviour of TDO Mini Forms allowed any user with the ability to publish posts automatic access to the form. This behaviour can now be turned off or on as required.","tdomf"); ?></p>
   
	<?php if (!isset($wp_roles)) { $wp_roles = new WP_Roles(); }
	       $roles = $wp_roles->role_objects;
          $access_roles = array();
          $publish_roles = array();
          foreach($roles as $role) {
             if(!isset($role->capabilities['publish_posts'])) {
                if($role->name != get_option('default_role')) {
                   array_push($access_roles,$role->name);
                } else {
                   $def_role = $role->name;
                }
             } else {
                 array_push($publish_roles,$role->name);
             }
          }
          rsort($access_roles);
          rsort($publish_roles);
          
          $caps = tdomf_get_all_caps();

          $can_reg = get_option('users_can_register');
          
          ?>


          
          <script type="text/javascript">
         //<![CDATA[
          function tdomf_unreg_user() {
            var flag = document.getElementById("tdomf_special_access_anyone").checked;
            var flag2 = document.getElementById("tdomf_user_publish_override").checked;
            <?php if(isset($def_role)) {?>
            document.getElementById("tdomf_access_<?php echo $def_role; ?>").disabled = flag;
            document.getElementById("tdomf_access_<?php echo $def_role; ?>").checked = flag;
            <?php } ?>
            <?php foreach($access_roles as $role) { ?>
            document.getElementById("tdomf_access_<?php echo $role; ?>").disabled = flag;
            document.getElementById("tdomf_access_<?php echo $role; ?>").checked = flag;
            <?php } ?>
            <?php foreach($caps as $cap) { ?>
            document.getElementById("tdomf_access_caps_<?php echo $cap; ?>").disabled = flag;
            document.getElementById("tdomf_access_caps_<?php echo $cap; ?>").checked = flag;
            <?php } ?>
            document.getElementById("tdomf_access_users_list").disabled = flag;
            if(flag) {
               document.getElementById("tdomf_access_users_list").value = "";
            }
            if(!flag2) {
            <?php foreach($publish_roles as $role) { ?>
            document.getElementById("tdomf_access_<?php echo $role; ?>").disabled = flag;
            document.getElementById("tdomf_access_<?php echo $role; ?>").checked = flag;
            <?php } ?>
            }
           }
           <?php if(isset($def_role) && $can_reg) { ?>
           function tdomf_def_role() {
              var flag = document.getElementById("tdomf_access_<?php echo $def_role; ?>").checked;
              var flag2 = document.getElementById("tdomf_user_publish_override").checked;
              <?php foreach($access_roles as $role) { ?>
               document.getElementById("tdomf_access_<?php echo $role; ?>").checked = flag;
              <?php } ?>
                 if(!flag2) {
                 <?php foreach($publish_roles as $role) { ?>
                    document.getElementById("tdomf_access_<?php echo $role; ?>").checked = flag;
                 <?php } ?>
                 }
              <?php foreach($caps as $cap) { ?>
             document.getElementById("tdomf_access_caps_<?php echo $cap; ?>").disabled = flag;
             document.getElementById("tdomf_access_caps_<?php echo $cap; ?>").checked = flag;
             <?php } ?>
              <?php foreach($access_roles as $role) { ?>
              document.getElementById("tdomf_access_<?php echo $role; ?>").disabled = flag;
              <?php } ?>
              if(!flag2) {
              <?php foreach($publish_roles as $role) { ?>
                 document.getElementById("tdomf_access_<?php echo $role; ?>").disabled = flag;
              <?php } ?>
              }
             document.getElementById("tdomf_access_users_list").disabled = flag;
            if(flag) {
               document.getElementById("tdomf_access_users_list").value = "";
            }
           }
           <?php } ?>
           
           function tdomf_publish_user() {
            var flag = document.getElementById("tdomf_user_publish_override").checked;
            <?php if(isset($def_role) && $can_reg) { ?>
            var flag2 = document.getElementById("tdomf_access_<?php echo $def_role; ?>").checked;
            if(!flag2) {
            <?php } ?>
                <?php foreach($publish_roles as $role) { ?>
                document.getElementById("tdomf_access_<?php echo $role; ?>").checked = flag;
                document.getElementById("tdomf_access_<?php echo $role; ?>").disabled = flag;
                <?php } ?>

            <?php if(isset($def_role) && $can_reg) { ?>
            }
            <?php } ?>
           }
           //-->
           </script>

           <p>
           
          <label for="tdomf_special_access_anyone">
   <input value="tdomf_special_access_anyone" type="checkbox" name="tdomf_special_access_anyone" id="tdomf_special_access_anyone" <?php if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) != false) { ?>checked<?php } ?> onClick="tdomf_unreg_user();" />
   <?php _e("Unregistered Users (i.e. everyone)","tdomf"); ?>
           </label>
           
           <br/>

           <?php $author_edit = tdomf_get_option_form(TDOMF_OPTION_ALLOW_AUTHOR,$form_id); ?>
           
           <input type="checkbox" name="tdomf_author_edit" id="tdomf_author_edit" <?php if(!$edit_form){ echo 'disabled'; } ?> <?php if($author_edit){ echo 'checked'; } ?> />
          <label for="tdomf_author_edit">
          <?php _e("Original Submitter (registered users only)","tdomf"); ?>
          </label>   
           
          <br/>
          
          <?php $can_publish = tdomf_get_option_form(TDOMF_OPTION_ALLOW_PUBLISH,$form_id); ?>
          
          <input type="checkbox" 
                 name="tdomf_user_publish_override" id="tdomf_user_publish_override"
                 <?php if($can_publish) { ?> checked <?php } ?>
                 onClick="tdomf_publish_user();" />
          <label for="tdomf_user_publish_override">
          <?php _e("Users with rights to publish posts.","tdomf"); ?>
          </label>   

           </p>
          
           <div id="options_access_tabs" class="tabs">
              <ul>
                <li><a href="#access_roles"><span><?php _e('Roles','tdomf'); ?></span></a></li>
                <li><a href="#access_caps"><span><?php _e('Capabilities','tdomf'); ?></span></a></li>
                <li><a href="#access_users"><span><?php _e('Specific Users','tdomf'); ?></span></a></li>
              </ul>
           
           <div id="access_roles">
           <p><?php _e('Select roles that can access the form. If you allow free user registration and pick the default role, this means that a user must just be logged in to access the form.','tdomf'); ?></p>
           
           <p>
          <?php if(isset($def_role)) { ?>
             <label for="tdomf_access_<?php echo ($def_role); ?>">
             <input value="tdomf_access_<?php echo ($def_role); ?>" type="checkbox"
                    name="tdomf_access_<?php echo ($def_role); ?>" id="tdomf_access_<?php echo ($def_role); ?>"  
                    <?php if(isset($wp_roles->role_objects[$def_role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])) { ?> checked <?php } ?> 
                    onClick="tdomf_def_role()" 
                    <?php if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) != false) { ?> checked disabled <?php } ?> />
             <?php if(function_exists('translate_with_context')) {
                   $role_name = translate_with_context($wp_roles->role_names[$def_role]);
                   } else { $role_name = $wp_roles->role_names[$def_role]; } ?>
             <?php echo $role_name." ".__("(newly registered users)"); ?>
             </label><br/>
          <?php } ?>

          <?php foreach($access_roles as $role) { ?>
             <label for="tdomf_access_<?php echo ($role); ?>">
             <input value="tdomf_access_<?php echo ($role); ?>" type="checkbox" 
                    name="tdomf_access_<?php echo ($role); ?>" id="tdomf_access_<?php echo ($role); ?>" 
                    <?php if(isset($wp_roles->role_objects[$role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])) { ?> checked <?php } ?>
                    <?php if(isset($def_role) && isset($wp_roles->role_objects[$def_role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id]) && $can_reg) { ?> checked disabled <?php } ?>
                    <?php if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) != false) { ?> checked disabled <?php } ?> />
             <?php if(function_exists('translate_with_context')) {
                   echo translate_with_context($wp_roles->role_names[$role]);
                   } else { echo $wp_roles->role_names[$role]; } ?>
             </label><br/>
          <?php } ?>
          
          <?php foreach($publish_roles as $role) { ?>
             <label for="tdomf_access_<?php echo ($role); ?>">
             <input value="tdomf_access_<?php echo ($role); ?>" type="checkbox" 
                    name="tdomf_access_<?php echo ($role); ?>" id="tdomf_access_<?php echo ($role); ?>"
                    <?php if($can_publish) { ?> checked disabled <?php } ?>
                    <?php if(isset($wp_roles->role_objects[$role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])) { ?> checked <?php } ?>
                    <?php if(isset($def_role) && isset($wp_roles->role_objects[$def_role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id]) && $can_reg) { ?> checked disabled <?php } ?>
                    <?php if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) != false) { ?> checked disabled <?php } ?> />
             <?php if(function_exists('translate_with_context')) {
                   printf(__('%s (can publish posts)','tdomf'), translate_with_context($wp_roles->role_names[$role]));
                   } else { printf(__('%s (can publish posts)','tdomf'),$wp_roles->role_names[$role]); } ?>
             </label><br/>
          <?php } ?>
          </p>
          </div> <!-- access_roles -->
           
          <div id="access_caps">
          <p><?php _e('Capabilities are specific access rights. Roles are groupings of capabilities. Individual users can be given individual capabilities outside their assigned Role using external plugins. You can optionally select additional capabilities that give access to the form.','tdomf'); ?></p>
          
          <?php $access_caps = tdomf_get_option_form(TDOMF_OPTION_ALLOW_CAPS,$form_id);
                if($access_caps == false) { $access_caps = array(); } ?>
          
          <div id="access_caps_list"><p>
          <?php foreach($caps as $cap) { ?>
             <input value="tdomf_access_caps_<?php echo ($cap); ?>" type="checkbox" 
                    name="tdomf_access_caps_<?php echo ($cap); ?>" id="tdomf_access_caps_<?php echo ($cap); ?>"
                    <?php if(isset($def_role) && isset($wp_roles->role_objects[$def_role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id]) && $can_reg) { ?> checked disabled <?php } ?>
                    <?php if(in_array($cap,$access_caps)) { ?> checked <?php } ?>
                    <?php if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) != false) { ?> checked disabled <?php } ?> />
             <label for="tdomf_access_caps_<?php echo ($cap); ?>">
             <?php if(function_exists('translate_with_context')) {
                   echo translate_with_context($cap);
                   } else { echo $cap; } ?>
             </label><br/>
          <?php } ?>
          </p></div> <!-- access_caps_list -->
          
          </div> <!-- access_caps -->
          
          <div id="access_users">
          
          <?php $allow_users = tdomf_get_option_form(TDOMF_OPTION_ALLOW_USERS,$form_id); 
                $tdomf_access_users_list = "";
                if(is_array($allow_users)) {
                    $tdomf_access_users_list = array();
                    foreach( $allow_users as $allow_user ) {
                        $allow_user = get_userdata($allow_user);
                        $tdomf_access_users_list[] = $allow_user->user_login;
                    }
                    sort($tdomf_access_users_list);
                    $tdomf_access_users_list = join(' ', $tdomf_access_users_list);
                } ?>
          
          <p><?php _e('You can specify additional specific users who can access the form. Just list their login names seperated by spaces in the box provide','tdomf'); ?></p>
          
          <textarea cols="80" rows="3" 
                    name="tdomf_access_users_list" id="tdomf_access_users_list"
                    <?php if(isset($def_role) && isset($wp_roles->role_objects[$def_role]->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id]) && $can_reg) { ?> checked disabled /></textarea>
                    <?php } else if(tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) != false) { ?> checked disabled /></textarea> 
                    <?php } else { ?> /><?php echo $tdomf_access_users_list; ?></textarea><?php } ?>
          
          </div> <!-- access_users -->
          
       </div> <!-- options_access_tabs -->
           
       <p>
       
          <?php $allow_time = tdomf_get_option_form(TDOMF_OPTION_ALLOW_TIME,$form_id); 
                if($allow_time === false || $allow_time < 0) { $allow_time = ""; } ?>
       
          <label for="tdomf_time_edit">
          <?php _e('Allow post or page to be edited for '); ?>
          <input type="text" name="tdomf_time_edit" id="tdomf_time_edit" size="5" value="<?php echo $allow_time; ?>" <?php if(!$edit_form){ ?>disabled <?php } ?> />
          <?php _e(' Seconds (1 hour = 3600 seconds) after post is published.'); ?>
          </label>

       </p>
    
    </div> <!-- form_access -->

    <div id="form_new">
    
    <?php $def_cat = tdomf_get_option_form(TDOMF_DEFAULT_CATEGORY,$form_id); ?>
    <p>
       <label for="tdomf_def_cat">
	       <?php _e("Default Category of new submissions","tdomf"); ?>
       </label>
	   <SELECT NAME="tdomf_def_cat" id="tdomf_def_cat" <?php if($use_page){ ?>disabled<?php } ?> >
	   <?php $cats = get_categories("get=all");
        if(!empty($cats)) {
           foreach($cats as $c) {
             if($c->term_id == $def_cat ) {
               echo "<OPTION VALUE=\"$c->term_id\" selected>$c->category_nicename\n";
             } else {
               echo "<OPTION VALUE=\"$c->term_id\">$c->category_nicename\n";
             }
          }
        }?>
        </select>
    </p>
    
	<p>
	<?php _e('You can set submissions from this form that are published/approved to be queued before appearing on the site. Just set the period of time between each post and TDOMF will schedule approved submissions from this form. A value of 0 or -1 disables this option. You can also choose to schedule posts after any post in the system or just TDOMF posts.',"tdomf"); ?>
	</p>
    
    <?php $tdomf_queue_on_all = tdomf_get_option_form(TDOMF_OPTION_QUEUE_ON_ALL,$form_id); ?>
        <input type="checkbox" name="tdomf_queue_on_all" id="tdomf_queue_on_all"  <?php if($tdomf_queue_on_all) echo "checked"; ?> >
        <label for="tdomf_queue_on_all">
            <?php _e("Queue after all posts (i.e. not just posts submitted to TDOMF)","tdomf"); ?>
        </label>
    
    <?php $tdomf_queue_period = intval(tdomf_get_option_form(TDOMF_OPTION_QUEUE_PERIOD,$form_id)); ?>
	<p>
	<input type="text" name="tdomf_queue_period" id="tdomf_queue_period" size="5" value="<?php echo htmlentities($tdomf_queue_period,ENT_QUOTES,get_bloginfo('charset')); ?>" />
    <?php _e("Seconds (1 day = 86400 seconds)","tdomf"); ?>
	</p>
    
    </div> <!-- form_new -->
    
    <div id="form_edit">
    
       <p>
        <?php $edit_restrict_tdomf = tdomf_get_option_form(TDOMF_OPTION_EDIT_RESTRICT_TDOMF,$form_id); ?>
        <input type="checkbox" name="tdomf_edit_tdomf_only" id="tdomf_edit_tdomf_only" <?php if($edit_restrict_tdomf){ ?>checked<?php }?> >
        <label for="tdomf_edit_tdomf_only"><?php _e("Restrict editing to posts or pages that were submitted by TDO Mini Forms","tdomf"); ?></label>
        
        <br/>

        <?php $edit_restrict_cats = tdomf_get_option_form(TDOMF_OPTION_EDIT_RESTRICT_CATS,$form_id); 
              if(!is_array($edit_restrict_cats)){ $edit_restrict_cats = ""; }
              else { $edit_restrict_cats = join(",",$edit_restrict_cats); } ?>        
                
        <?php $edit_page_form = tdomf_get_option_form(TDOMF_OPTION_EDIT_PAGE_FORM,$form_id); ?>
        <input type="checkbox" name="tdomf_edit_page_form" id="tdomf_edit_page_form" <?php if($edit_page_form){ ?>checked<?php } ?> >
        <label for="tdomf_edit_tdomf_only"><?php _e("Allow editing of pages (or posts) that contain TDO-Mini-Form forms","tdomf"); ?></label>
        
        <br/><br/>
        
               
        <label for="tdomf_edit_cats">
        <?php _e("Restrict editing to posts in these categories only:","tdomf"); ?><br/>
        <small><?php _e("(List category ids seperated by a comma)","tdomf"); ?><br/></small>
        </label>
        <input type="text" name="tdomf_edit_cats" id="tdomf_edit_cats" size="80" value="<?php echo $edit_restrict_cats; ?>" <?php if($use_page){ ?>disabled<?php } ?> />

        <br/><br/>
        
        <?php $ajax_edit = tdomf_get_option_form(TDOMF_OPTION_AJAX_EDIT,$form_id); ?>
        <input type="checkbox" name="tdomf_ajax_edit" id="tdomf_ajax_edit" <?php if($ajax_edit){ ?>checked<?php }?> >
        <label for="tdomf_ajax_edit"><?php _e("Inline Editing (requires javascript in browser, will fall back to one of the options below)","tdomf"); ?></label>
        <br/>
        
        <?php $add_edit_link = tdomf_get_option_form(TDOMF_OPTION_ADD_EDIT_LINK,$form_id); 
              if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS) == false && $add_edit_link == 'your_submissions') { $add_edit_link = 'none'; } 
              if($updated_pages == false && $auto_edit_link == 'page') { $add_edit_link = 'none'; }?>
        
        <input type="radio" name="tdomf_add_edit_link" id="tdomf_add_edit_link_none" value="none" <?php if(!$add_edit_link || $add_edit_link == 'none') { ?>checked<?php } ?> >
        <label for="tdomf_add_edit_link_none">
            <?php _e('Do not add a link to edit form at end of post','tdomf'); ?>
        </label>
        <br/>
        <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
            <input type="radio" name="tdomf_add_edit_link" id="tdomf_add_edit_link_your_submissions" value="your_submissions" <?php if($add_edit_link == 'your_submissions') { ?>checked<?php } ?>>
            <label for="tdomf_add_edit_link_your_submissions">
                <?php _e('Add a link to Form on Your Submissions at end of post','tdomf'); ?>
            </label>
            <br/>
        <?php } ?>
        <?php if($updated_pages != false) { ?>
            <input type="radio" name="tdomf_add_edit_link" id="tdomf_add_edit_link_page" value="page" <?php if($add_edit_link == 'page') { ?>checked<?php } ?>>
            <label for="tdomf_add_edit_link_page">
                <?php _e('Add a link to Page created for Form at end of post','tdomf'); ?>
            </label>
            <br/>
        <?php } ?>
        <input type="radio" name="tdomf_add_edit_link" id="tdomf_add_edit_link_custom" value="custom" 
            <?php if($add_edit_link !== false && $add_edit_link != 'none' && $add_edit_link != 'page' && $add_edit_link != 'your_submissions') { ?>checked<?php } ?>>
        <label for="tdomf_add_edit_link_custom">
            <?php _e('Add a link to a Custom URL at the end of post','tdomf'); ?>
        </label>
        <br/>
        <input type="text" name="tdomf_add_edit_link_custom_url" id="tdomf_add_edit_link_custom_url" size="80" 
            <?php if($add_edit_link !== false && $add_edit_link != 'none' && $add_edit_link != 'page' && $add_edit_link != 'your_submissions') { ?>value="<?php echo $add_edit_link; ?>" <?php } else { ?>value="http://" <?php } ?>/>

            <br/><br/>
        
        <p>
        <?php _e('If your theme supports it, the edit link can be automatically updated to point to a TDOMF form instead of Wordpress\' backend. But TDOMF cannot change the permissions under which it shows. I.e. This edit link will appear according to Wordpress access rules and not the rules you set here.','tdomf'); ?>
        </p>
        
        <?php $auto_edit_link = tdomf_get_option_form(TDOMF_OPTION_AUTO_EDIT_LINK,$form_id); 
              if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS) == false && $auto_edit_link == 'your_submissions') { $auto_edit_link = 'none'; } 
              if($updated_pages == false && $auto_edit_link == 'page') { $auto_edit_link = 'none'; } ?>
        
        <input type="radio" name="tdomf_auto_edit_link" id="tdomf_auto_edit_link_none" value="none" <?php if(!$auto_edit_link || $auto_edit_link == 'none') { ?>checked<?php } ?>>
        <label for="tdomf_auto_edit_link_none">
            <?php _e('Do not auto-modify \'Edit Post\' link','tdomf'); ?>
        </label>
        <br/>
        <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
            <input type="radio" name="tdomf_auto_edit_link" id="tdomf_auto_edit_link_your_submissions" value="your_submissions" <?php if($auto_edit_link == 'your_submissions') { ?>checked<?php } ?>>
            <label for="tdomf_auto_edit_link_your_submissions">
                <?php _e('Auto-modify \'Edit Post\' link (if avaliable in theme) to point to Form on Your Submissions','tdomf'); ?>
            </label>
            <br/>
        <?php } ?>
        <?php if($updated_pages != false) { ?>
            <input type="radio" name="tdomf_auto_edit_link" id="tdomf_auto_edit_link_page" value="page" <?php if($auto_edit_link == 'page') { ?>checked<?php } ?>>
            <label for="tdomf_auto_edit_link_page">
                <?php _e('Auto-modify \'Edit Post\' link (if avaliable in theme) to point to Page created for Form','tdomf'); ?>
            </label>
            <br/>
        <?php } ?>
        <input type="radio" name="tdomf_auto_edit_link" id="tdomf_auto_edit_link_custom" value="custom" 
            <?php if($auto_edit_link !== false && $auto_edit_link != 'none' && $auto_edit_link != 'page' && $auto_edit_link != 'your_submissions') { ?>checked<?php } ?>>
        <label for="tdomf_auto_edit_link_custom">
            <?php _e('Auto-modify \'Edit Post\' link (if avaliable in theme) to point to a custom URL','tdomf'); ?>
        </label>
        <br/>
        <input type="text" name="tdomf_auto_edit_link_custom_url" id="tdomf_auto_edit_link_custom_url" size="80"
            <?php if($auto_edit_link !== false && $auto_edit_link != 'none' && $auto_edit_link != 'page' && $auto_edit_link != 'your_submissions') { ?>value="<?php echo $auto_edit_link; ?>" <?php } else { ?>value="http://" <?php } ?>/>

        <br/>
        
       </p>
    
    </div> <!-- form_edit -->
    
    <div id="form_moderation">

    <?php $on_mod = tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id); ?>
	
    <p>
        <input type="checkbox" name="tdomf_moderation" id="tdomf_moderation"  	<?php if($on_mod) echo "checked"; ?> >
        <label for="tdomf_moderation">
            <?php _e("Enable Moderation","tdomf"); ?>
        </label>
    </p>
    
    <p>
    
    <?php $user_publish_auto = tdomf_get_option_form(TDOMF_OPTION_PUBLISH_NO_MOD,$form_id); ?>
    <input type="checkbox" name="tdomf_user_publish_auto" id="tdomf_user_publish_auto" <?php if($user_publish_auto) { ?> checked <?php } ?> />
    <label for="tdomf_user_publish_auto">
        <?php _e("Users with publish rights will have their posts automatically published","tdomf"); ?><br/>
    </label>
    
    <?php $redirect = tdomf_get_option_form(TDOMF_OPTION_REDIRECT,$form_id); ?>
    <input type="checkbox" name="tdomf_redirect" id="tdomf_redirect" <?php if($redirect) echo "checked"; ?> >
    <label for="tdomf_redirect">
        <?php _e("If automatically published, redirect to Published Post","tdomf"); ?><br/>
    </label>
    
    <?php $mod_email_on_pub = tdomf_get_option_form(TDOMF_OPTION_MOD_EMAIL_ON_PUB,$form_id); ?>
    <input type="checkbox" name="tdomf_mod_email_on_pub" id="tdomf_mod_email_on_pub" <?php if($mod_email_on_pub) echo "checked"; ?> >
    <label for="tdomf_mod_email_on_pub">
        <?php _e("Send Moderation Email even for automatically Published Post","tdomf"); ?><br/>
    </label>
    
	</p>

    <p>
    <b><?php _e('Send Moderation Email Notification to:','tdomf'); ?></b><br/><br/>

	 <?php $notify_roles = tdomf_get_option_form(TDOMF_NOTIFY_ROLES,$form_id);
	       if($notify_roles != false) { $notify_roles = explode(';', $notify_roles); }  
           $admin_emails = tdomf_get_option_form(TDOMF_OPTION_ADMIN_EMAILS,$form_id); ?>

	 <?php foreach($roles as $role) {
           if(isset($role->capabilities['edit_others_posts'])
	           && isset($role->capabilities['publish_posts'])) { ?>
		     <input value="tdomf_notify_<?php echo ($role->name); ?>" type="checkbox" name="tdomf_notify_<?php echo ($role->name); ?>" id="tdomf_notify_<?php echo ($role->name); ?>" <?php if($notify_roles != false && in_array($role->name,$notify_roles)) { ?>checked<?php } ?> />
             <label for="tdomf_notify_<?php echo ($role->name); ?>">
          <?php if(function_exists('translate_with_context')) {
                   echo translate_with_context($wp_roles->role_names[$role->name]);
                   } else { echo $wp_roles->role_names[$role->name]; } ?>
          <br/>
		     </label>
		     <?php
		  }
	       } ?>
         <br/>

    <?php _e("Specific Email Addresses (seperate with spaces)","tdomf"); ?></b>
	<input type="text" name="tdomf_admin_emails" id="tdomf_admin_emails" size="80" value="<?php if($admin_emails) { echo htmlentities(stripslashes($admin_emails),ENT_QUOTES,get_bloginfo('charset')); } ?>" />
         
	 </p>

    
    </div> <!-- form_moderation -->
    
    <div id="form_throttling">

	<p>
	<?php _e('You can add rules to throttle input based on registered user accounts and/or IP addresses. Make sure you save any form configuration changes to your form before adding or removing any throttling rules.',"tdomf"); ?>
	</p>
   
    <?php printf(__("<table border=\"0\">
                     <tr><td>Only %s submissions/contributions per</td>
                     <td>%s</td>
                     <td>%s(optionally) per %s Seconds (1 hour = 3600 seconds)</td>
                     <td>%s</td>
                     </tr>
                     </table>","tdomf"),
                     '<input type="text" name="tdomf_throttle_rule_count" id="tdomf_throttle_rule_count" size="3" value="10" /> 
                      <select id="tdomf_throttle_rule_sub_type" name="tdomf_throttle_rule_sub_type" >
                      <option value="unapproved" selected />'.__("unapproved","tdomf").'
                      <option value="any" />'.__("any","tdomf").'
                      </select>',
                     '<input type="radio" name="tdomf_throttle_rule_user_type" id="tdomf_throttle_rule_user_type" value="user" />'.__("registered user","tdomf").'<br/>
                      <input type="radio" name="tdomf_throttle_rule_user_type" id="tdomf_throttle_rule_user_type" value="ip" checked />'.__("IP","tdomf"),
                     '<input type="checkbox" name="tdomf_throttle_rule_opt1" id="tdomf_throttle_rule_opt1" checked >',
                     '<input type="text" name="tdomf_throttle_rule_time" id="tdomf_throttle_rule_time" size="3" value="3600" />',
                     '<input type="submit" name="tdomf_add_throttle_rule" id="tdomf_add_throttle_rule" value="'.__("Add","tdomf").' &raquo;">'); ?>

    <?php $throttle_rules = tdomf_get_option_form(TDOMF_OPTION_THROTTLE_RULES,$form_id); 
          if(is_array($throttle_rules) && !empty($throttle_rules)) { ?>
    
    <p><b><?php _e("Current Throttle Rules","tdomf"); ?></b>
    <ul>
    <?php  foreach($throttle_rules as $id => $throttle_rule) {
             $option_string = "";
             if($throttle_rule['opt1']) {
                 $option_string = sprintf(__("per %s Seconds","tdomf"),$throttle_rule['time']);
             }
        ?>
        <li>
        <?php printf(__("(%d) Only %d %s submissions/contributions per %s %s","tdomf"),$id,$throttle_rule['count'],$throttle_rule['sub_type'],$throttle_rule['type'],$option_string); ?>
        <input type="submit" name="tdomf_remove_throttle_rule_<?php echo $id; ?>" id="tdomf_remove_throttle_rule_<?php echo $id; ?>" value="<?php _e("Remove","tdomf"); ?> &raquo;">
        </li>
    <?php } ?>
    </ul>
    </p>
    
          <?php } else { ?>
              <p><b><?php _e("No Throttling Rules currently set.","tdomf"); ?></b></p>
          <?php } ?>
        
    </div> <!-- form_throttling -->
    
    <div id="form_spam">
    
    <?php tdomf_show_spam_options($form_id); ?>
    
    </div> <!-- form_spam -->
    
    <br/>
    
  <table border="0"><tr>

    <td>
    <input type="hidden" name="save_settings" value="0" />
    <input type="submit" name="tdomf_save_button" id="tdomf_save_button" value="<?php _e("Save","tdomf"); ?> &raquo;" />
	</form>
    </td>

    <td>
    <form method="post" action="admin.php?page=tdomf_show_form_options_menu&form=<?php echo $form_id; ?>">
    <input type="submit" name="refresh" value="Refresh" />
    </form>
    </td>

    </tr></table>

    </div> <!-- wrap -->
    
  <?php }
}

function tdomf_forms_top_toolbar($form_id_in=false, $current='tdomf_show_form_options_menu') {
    $form_ids = tdomf_get_form_ids();
    $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id_in);
    if(tdomf_wp23()) { ?>
        <div class="wrap">
    
    <?php if(!empty($form_ids)) {
            foreach($form_ids as $form_id) { ?>
              <?php if($form_id->form_id == $form_id_in) { ?>
                <b>
              <?php } else { ?>
                <a href="admin.php?page=tdomf_show_form_options_menu&form=<?php echo $form_id->form_id; ?>">
              <?php } ?>
              <?php printf(__("Form %d","tdomf"),$form_id->form_id); ?><?php if($form_id->form_id == $form_id_in) { ?></b><?php } else {?></a><?php } ?>
                 |
            <?php }
          }
    ?>
    
    <?php if(function_exists('wp_nonce_url')) { ?>
   <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&new", 'tdomf-new-form'); ?>">
          <?php _e("New Form &raquo;","tdomf"); ?></a>
    <?php } else { ?>
      <a href="admin.php?page=tdomf_show_form_options_menu&new"><?php _e("New Form &raquo;","tdomf"); ?></a>
    <?php } ?>
    </div> <!-- wrap -->

    <div class="wrap">
    <?php if(function_exists('wp_nonce_url')) { ?>
       <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&delete=$form_id", 'tdomf-delete-form-'.$form_id); ?>">
          <?php _e("Delete","tdomf"); ?></a> |
       <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&copy=$form_id&form=$form_id", 'tdomf-copy-form-'.$form_id); ?>">
          <?php _e("Copy","tdomf"); ?></a> | 
    <?php } else { ?>
       <a href="admin.php?page=tdomf_show_form_options_menu&delete=<?php echo $form_id; ?>"><?php _e("Delete","tdomf"); ?></a> |
       <a href="admin.php?page=tdomf_show_form_options_menu&copy=<?php echo $form_id; ?>"><?php _e("Copy","tdomf"); ?></a> | 
    <?php } ?>
    <?php if($pages != false) { ?>
      <a href="<?php echo get_permalink($pages[0]); ?>" title="<?php _e("Live on your blog!","tdomf"); ?>" ><?php _e("View &raquo;","tdomf"); ?></a> |
    <?php } ?>
    <?php if(tdomf_get_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$form_id) && get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
        <?php if(current_user_can('edit_users')) { ?>
                <a href="users.php?page=tdomf_your_submissions#tdomf_form<?php echo $form_id; ?>" title="<?php _e("Included on the 'Your Submissions' page!",'tdomf'); ?>" >
        <?php } else { ?>
                <a href="profile.php?page=tdomf_your_submissions#tdomf_form<?php echo $form_id; ?>" title="<?php _e("Included on the 'Your Submissions' page!",'tdomf'); ?>" >
          <?php } ?>
      <?php _e("View &raquo;","tdomf"); ?></a>
    <?php } ?>
    </div> <!-- wrap -->
    
    <?php 
    } else if(tdomf_wp27()) { /* do nothing */
    } else if(tdomf_wp25()) { /* do nothing */ }
}

function tdomf_forms_under_title_toolbar($form_id_in=false, $current='tdomf_show_form_options_menu') {
    $form_ids = tdomf_get_form_ids();
    $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id_in);
    if(tdomf_wp23()) { /* do nothing */
    } else if(tdomf_wp27()) { ?>

        <ul class="subsubsub"> 
            <li> <a <?php if($current == 'tdomf_show_form_options_menu') { ?>class="current" <?php } ?>href="admin.php?page=tdomf_show_form_options_menu&form=<?php echo $form_id_in; ?>"><?php printf(__("Options","tdomf"),$form_id_in); ?></a> |</li>
            <li> <a <?php if($current == 'tdomf_show_form_menu') { ?>class="current" <?php } ?>href="admin.php?page=tdomf_show_form_menu&form=<?php echo $form_id_in; ?>"><?php printf(__("Create","tdomf"),$form_id_in); ?></a> |</li>
            <li> <a <?php if($current == 'tdomf_show_form_hacker' && !isset($_REQUEST['text'])) { ?>class="current" <?php } ?>href="admin.php?page=tdomf_show_form_hacker&form=<?php echo $form_id_in; ?>"><?php printf(__("Hack","tdomf"),$form_id_in); ?></a> |</li>
            <li> <a <?php if($current == 'tdomf_show_form_hacker' &&  isset($_REQUEST['text'])) { ?>class="current" <?php } ?>href="admin.php?page=tdomf_show_form_hacker&text&form=<?php echo $form_id_in; ?>"><?php printf(__("Messages","tdomf"),$form_id_in); ?></a> |</li>
            <li> <a <?php if($current == 'tdomf_show_form_export_menu') { ?>class="current" <?php } ?>href="admin.php?page=tdomf_show_form_export_menu&form=<?php echo $form_id_in; ?>"><?php printf(__("Export","tdomf"),$form_id_in); ?></a> |</li>
            <?php if($pages != false && is_array($pages)) { ?>          
                <li><a href="<?php echo get_permalink($pages[0]); ?>" title="<?php echo htmlentities(_e("Live on your blog!","tdomf"),ENT_QUOTES); ?>" ><?php _e("View Page &raquo;","tdomf"); ?></a> |</li>
            <?php } ?>
            <?php if(tdomf_get_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$form_id_in) && get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
                <li><a href="users.php?page=tdomf_your_submissions#tdomf_form<?php echo $form_id_in; ?>" title="<?php _e("Included on the 'Your Submissions' page!",'tdomf'); ?>" >
                <?php _e("View on 'Your Submissions' &raquo;","tdomf"); ?></a> |</li>
            <?php } ?>
        </ul> 
          
      <div class="tablenav">
      
          <div class="alignleft">
            <a class="button" title="<?php echo htmlentities(__('Create a new form','tdomf'),ENT_QUOTES); ?>" href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&new", 'tdomf-new-form'); ?>">
            <?php _e("New","tdomf"); ?></a>
    
            <a class="button" title="<?php echo htmlentities(__('Delete this form','tdomf'),ENT_QUOTES); ?>" href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&delete=$form_id_in", 'tdomf-delete-form-'.$form_id_in); ?>">
            <?php _e("Delete","tdomf"); ?></a>
       
            <a class="button" title="<?php echo htmlentities(__('Make a copy of this form','tdomf'),ENT_QUOTES); ?>" href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&copy=$form_id_in&form=$form_id_in", 'tdomf-copy-form-'.$form_id_in); ?>">
            <?php _e("Copy","tdomf"); ?></a>
          </div> <!-- alignleft -->
      
          <?php if(!empty($form_ids)) { ?>
          
              <div class="alignright actions">
                <div class="tablenav-pages"><span class="displaying-num"><?php _e("Forms:",'tdomf'); ?></span>
                    <?php foreach($form_ids as $form_id) { ?>
                        <?php if($form_id->form_id == $form_id_in) { ?>
                            <span class='page-numbers current'><?php printf($form_id_in); ?></span> 
                        <?php } else { $form_name = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id->form_id); ?>
                            <a class='page-numbers' title='<?php echo htmlentities($form_name,ENT_QUOTES); ?>' href="admin.php?page=<?php echo $current; ?>&form=<?php echo $form_id->form_id; ?>">
                            <?php printf($form_id->form_id); ?></a>
                        <?php } ?>
                    <?php } ?>
                </div> <!-- alignleft actions -->
          <?php } ?>      
     </div> <!-- tablenav -->
      
         
     </div> <!-- wrap -->
     <div class="wrap">
     
    <?php } else if(tdomf_wp25()) { ?>
        
    <ul class="subsubsub">
       <?php if(!empty($form_ids)) {
            foreach($form_ids as $form_id) { ?>
                <li><a href="admin.php?page=tdomf_show_form_options_menu&form=<?php echo $form_id->form_id; ?>"<?php if($form_id->form_id == $form_id_in) { ?> class="current" <?php } ?>>
                <?php printf(__("Form %d","tdomf"),$form_id->form_id); ?></a> |</li>
            <?php }
          } ?>
        <?php if(function_exists('wp_nonce_url')) { ?>
        <li><a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&new", 'tdomf-new-form'); ?>">
          <?php _e("New Form &raquo;","tdomf"); ?></a></li>
    <?php } else { ?>
      <li><a href="admin.php?page=tdomf_show_form_options_menu&new"><?php _e("New Form &raquo;","tdomf"); ?></a></li>
    <?php } ?>
   </ul>
    
    <ul class="subsubsub">
    <?php if(function_exists('wp_nonce_url')) { ?>
       <li><a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&delete=$form_id", 'tdomf-delete-form-'.$form_id); ?>">
          <?php _e("Delete","tdomf"); ?></a> |</li>
       <li><a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_form_options_menu&copy=$form_id&form=$form_id", 'tdomf-copy-form-'.$form_id); ?>">
          <?php _e("Copy","tdomf"); ?></a> |</li> 
    <?php } else { ?>
       <li><a href="admin.php?page=tdomf_show_form_options_menu&delete=<?php echo $form_id; ?>"><?php _e("Delete","tdomf"); ?></a> |</li>
       <li><a href="admin.php?page=tdomf_show_form_options_menu&copy=<?php echo $form_id; ?>"><?php _e("Copy","tdomf"); ?></a> |</li> 
    <?php } ?>
    <?php if($pages != false) { ?>
      <li><a href="<?php echo get_permalink($pages[0]); ?>" title="<?php _e("Live on your blog!","tdomf"); ?>" ><?php _e("View Page &raquo;","tdomf"); ?></a> |</li>
    <?php } ?>
    <?php if(tdomf_get_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$form_id) && get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
      <li><a href="users.php?page=tdomf_your_submissions#tdomf_form<?php echo $form_id; ?>" title="<?php _e("Included on the 'Your Submissions' page!",'tdomf'); ?>" >
      <?php _e("View on 'Your Submissions' &raquo;","tdomf"); ?></a> |</li>
    <?php } ?>
     <li><a href="admin.php?page=tdomf_show_form_menu&form=<?php echo $form_id; ?>"><?php printf(__("Widgets &raquo;","tdomf"),$form_id); ?></a> |</li>
     <li><a href="admin.php?page=tdomf_show_form_hacker&form=<?php echo $form_id; ?>"><?php printf(__("Hack Form &raquo;","tdomf"),$form_id); ?></a></li>
    </ul>
    <?php }
}

// Display the menu to configure options for this plugin
//
function tdomf_show_form_options_menu() {
  global $wpdb, $wp_roles;

  $form_id = tdomf_get_first_form_id();
  $new_form_id = tdomf_handle_form_options_actions();
  if($new_form_id != false) {
      $form_id = $new_form_id;
  } else if(isset($_REQUEST['form'])) {
      $form_id = intval($_REQUEST['form']);
  }

  tdomf_forms_top_toolbar(intval($form_id));
  tdomf_show_form_options(intval($form_id));
}


// Create a page with the form embedded
//
function tdomf_create_form_page($form_id = 1) {
   global $current_user;

   if(tdomf_form_exists($form_id)){
     
     $form_name = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id);
     if($form_name == false || empty($form_name)) {
       $form_name = __("Submit A Post","tdomf");
     }
     
     $post = array (
       "post_content"   => "[tdomf_form$form_id]",
       "post_title"     => $form_name,
       "post_author"    => $current_user->ID,
       "post_status"    => 'publish',
       "post_type"      => "page"
     );
     $post_ID = wp_insert_post($post);
  
     $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id);
     if($pages == false) {
       $pages = array( $post_ID );
     } else {
       $pages = array_merge( $pages, array( $post_ID ) );
     }
     tdomf_set_option_form(TDOMF_OPTION_CREATEDPAGES,$pages,$form_id);
     
     return $post_ID;
   }
   
   return false;
}

// Handle actions for this form
//
function tdomf_handle_form_options_actions() {
   global $wpdb, $wp_roles;

   $message = "";
   $retValue = false;
   
  if(!isset($wp_roles)) {
  	$wp_roles = new WP_Roles();
  }
  $roles = $wp_roles->role_objects;
  $caps = tdomf_get_all_caps();
  
  $remove_throttle_rule = false;
  $rule_id = 0;
  if(isset($_REQUEST['tdomf_form_id'])) {
      $form_id = intval($_REQUEST['tdomf_form_id']);
      $rules = tdomf_get_option_form(TDOMF_OPTION_THROTTLE_RULES,$form_id);
      if(is_array($rules)) {
          foreach($rules as $id => $r) {
              if(isset($_REQUEST["tdomf_remove_throttle_rule_$id"])) {
                  $remove_throttle_rule = true;
                  $rule_id = $id;
                  break;
              }
          }
      }
  }
  
  if($remove_throttle_rule) {
      check_admin_referer('tdomf-options-save');
      
      unset($rules[$rule_id]);
      tdomf_set_option_form(TDOMF_OPTION_THROTTLE_RULES,$rules,$form_id);
      
      $message .= "Throttle rule removed!<br/>";
      tdomf_log_message("Removed throttle rule");
      
  } else if(isset($_REQUEST['tdomf_add_throttle_rule'])) {
     
     check_admin_referer('tdomf-options-save');

     $form_id = intval($_REQUEST['tdomf_form_id']);
     
     $rule = array();
     $rule['sub_type'] = $_REQUEST['tdomf_throttle_rule_sub_type'];
     $rule['count'] = $_REQUEST['tdomf_throttle_rule_count'];
     $rule['type'] = $_REQUEST['tdomf_throttle_rule_user_type'];
     $rule['opt1'] = isset($_REQUEST['tdomf_throttle_rule_opt1']);
     $rule['time'] = intval($_REQUEST['tdomf_throttle_rule_time']);
                            
     $rules = tdomf_get_option_form(TDOMF_OPTION_THROTTLE_RULES,$form_id);
     if(!is_array($rules)) { $rules = array(); }
     $rules[] = $rule;
     tdomf_set_option_form(TDOMF_OPTION_THROTTLE_RULES,$rules,$form_id);
     
     $message .= "Throttle rule added!<br/>";
     tdomf_log_message("Added a new throttle rule: " . var_export($rule,true));
  
  } else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'create_form_page') {
     check_admin_referer('tdomf-create-form-page');
     $form_id = intval($_REQUEST['form']);
     $page_id = tdomf_create_form_page($form_id);
     $message = sprintf(__("A page with the form has been created. <a href='%s'>View page &raquo;</a><br/>","tdomf"),get_permalink($page_id));
  } else if(isset($_REQUEST['save_settings']) && isset($_REQUEST['tdomf_form_id'])) {
    
      check_admin_referer('tdomf-options-save');
    
      $form_id = intval($_REQUEST['tdomf_form_id']);
     
      // Edit or Submit
      
      $edit_form = false;
      if(isset($_REQUEST['tdomf_mode']) && $_REQUEST['tdomf_mode'] == "edit") {
          $edit_form = true;
      }
      tdomf_set_option_form(TDOMF_OPTION_FORM_EDIT,$edit_form,$form_id);
      
      // Allow pages with forms to be editted
      
      $edit_page_form = isset($_REQUEST['tdomf_edit_page_form']);
      tdomf_set_option_form(TDOMF_OPTION_EDIT_PAGE_FORM,$edit_page_form,$form_id);
      
      // Allow authors to edit
      
      $author_edit = false;
      if(isset($_REQUEST['tdomf_author_edit'])) {
          $author_edit = true;
      }
      tdomf_set_option_form(TDOMF_OPTION_ALLOW_AUTHOR,$author_edit,$form_id);
      
      // Edit post within X seconds of being published
      
      $time_edit = false;
      if(isset($_REQUEST['tdomf_time_edit'])) {
          $time_edit = intval($_REQUEST['tdomf_time_edit']);
          if($time_edit <= 0){ $time_edit = false; }
      }
      tdomf_set_option_form(TDOMF_OPTION_ALLOW_TIME,$time_edit,$form_id);
      
      // Who can access the form?

      if(isset($_REQUEST['tdomf_special_access_anyone']) && tdomf_get_option_form(TDOMF_OPTION_ALLOW_EVERYONE,$form_id) == false) {
         tdomf_set_option_form(TDOMF_OPTION_ALLOW_EVERYONE,true,$form_id);

         foreach($roles as $role) {
     	    // remove cap as it's not needed
		    if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])){
   				$role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id);
		    }
 	  	}
        
        tdomf_set_option_form(TDOMF_OPTION_ALLOW_CAPS,array(),$form_id);
        
      } else if(!isset($_REQUEST['tdomf_special_access_anyone'])){
          
         tdomf_set_option_form(TDOMF_OPTION_ALLOW_EVERYONE,false,$form_id);
         
         // add cap to right roles
         foreach($roles as $role) {
		    if(isset($_REQUEST["tdomf_access_".$role->name])){
				$role->add_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id);
		    } else if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])){
   				$role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id);
		    }
 	  	}
        
        // list caps that can access form
        $allow_caps = array();
        foreach($caps as $cap) {
            if(isset($_REQUEST['tdomf_access_caps_'.$cap])){
                $allow_caps[] = $cap; 
            }
        }
        tdomf_set_option_form(TDOMF_OPTION_ALLOW_CAPS,$allow_caps,$form_id);
        
        // convert user names to ids
        $allow_users = array();
        if(isset($_REQUEST['tdomf_access_users_list'])) {
           $user_names = trim($_REQUEST['tdomf_access_users_list']);
           if(!empty($user_names)) {
               $user_names = explode(' ',$user_names);
               foreach($user_names as $user_name) {
                   if(!empty($user_name)) {
                       if(($userdata = get_userdatabylogin($user_name)) != false) {
                           $allow_users[] = $userdata->ID;
                       } else {
                           $message .= "<font color='red'>".sprintf(__("$user_name is not a valid user name. Ignoring.<br/>","tdomf"),$form_id)."</font>";
                           tdomf_log_message("User login $user_name is not recognised by wordpress. Ignoring.",TDOMF_LOG_BAD);
                       }
                   }
               }
           }
        }
        tdomf_set_option_form(TDOMF_OPTION_ALLOW_USERS,$allow_users,$form_id);
      }
 
      tdomf_set_option_form(TDOMF_OPTION_ALLOW_PUBLISH,isset($_REQUEST['tdomf_user_publish_override']),$form_id);
      
      // Who gets notified?

      $notify_roles = "";
	  foreach($roles as $role) {
		if(isset($_REQUEST["tdomf_notify_".$role->name])){
			$notify_roles .= $role->name.";";
	    }
      }
      if(!empty($notify_roles)) {
        tdomf_set_option_form(TDOMF_NOTIFY_ROLES,$notify_roles,$form_id);
      } else {
        tdomf_set_option_form(TDOMF_NOTIFY_ROLES,false,$form_id);
      }
      
      $save = true;
      $tdomf_admin_emails = $_POST['tdomf_admin_emails'];
      $emails = explode(',',$tdomf_admin_emails);
      foreach($emails as $email) {
          if(!empty($email)) {
              if(!tdomf_check_email_address($email)) {
                  $message .= "<font color='red'>".sprintf(__("The email %s is not valid! Please update 'Who Gets Notified' with valid email addresses.","tdomf"),$email)."</font><br/>";
                  $save = false;
                  break;
              }
          }
      }
      if($save) { tdomf_set_option_form(TDOMF_OPTION_ADMIN_EMAILS,$tdomf_admin_emails,$form_id); }
      
      // Default Category

      $def_cat = $_POST['tdomf_def_cat'];
      tdomf_set_option_form(TDOMF_DEFAULT_CATEGORY,$def_cat,$form_id);

      // Restrict editing to posts submitted by tdomf
      
      $edit_restrict_tdomf = isset($_REQUEST['tdomf_edit_tdomf_only']);
      tdomf_set_option_form(TDOMF_OPTION_EDIT_RESTRICT_TDOMF,$edit_restrict_tdomf,$form_id);
      
      $edit_restrict_cats = explode(',',trim($_REQUEST['tdomf_edit_cats']));
      if(!empty($edit_restrict_cats)) {
          $cats = array();
          foreach($edit_restrict_cats as $cat) {
              $cat = intval(trim($cat));
              if($cat > 0) { $cats[] = $cat; }
          }
          $edit_restrict_cats = $cats;
      } else {
          $edit_restrict_cats = array();
      }
      tdomf_set_option_form(TDOMF_OPTION_EDIT_RESTRICT_CATS,$edit_restrict_cats,$form_id);
            
      // add edit link
      
      $add_edit_link = $_REQUEST['tdomf_add_edit_link'];
      if($add_edit_link == 'custom') { 
          $add_edit_link = $_REQUEST['tdomf_add_edit_link_custom_url'];
      }
      tdomf_set_option_form(TDOMF_OPTION_ADD_EDIT_LINK,$add_edit_link,$form_id);
           
      $ajax_edit = isset($_REQUEST['tdomf_ajax_edit']);
      tdomf_set_option_form(TDOMF_OPTION_AJAX_EDIT,$ajax_edit,$form_id);
      
      // auto modify edit link
      
      $auto_edit_link = $_REQUEST['tdomf_auto_edit_link'];
      if($auto_edit_link == 'custom') { 
          $auto_edit_link = $_REQUEST['tdomf_auto_edit_link_custom_url'];
      }
      tdomf_set_option_form(TDOMF_OPTION_AUTO_EDIT_LINK,$auto_edit_link,$form_id);
      
       //Turn On/Off Moderation

      $mod = false;
      if(isset($_POST['tdomf_moderation'])) { $mod = true; }
      tdomf_set_option_form(TDOMF_OPTION_MODERATION,$mod,$form_id);

      $tdomf_redirect = isset($_POST['tdomf_redirect']);
      tdomf_set_option_form(TDOMF_OPTION_REDIRECT,$tdomf_redirect,$form_id);
      
      //Preview

      $preview = false;
      if(isset($_POST['tdomf_preview'])) { $preview = true; }
      tdomf_set_option_form(TDOMF_OPTION_PREVIEW,$preview,$form_id);

      //From email

      if(trim($_POST['tdomf_from_email']) == "") {
       	tdomf_set_option_form(TDOMF_OPTION_FROM_EMAIL,false,$form_id);
       } else {
        tdomf_set_option_form(TDOMF_OPTION_FROM_EMAIL,$_POST['tdomf_from_email'],$form_id);
       }

       // Form name
       
       if(trim($_POST['tdomf_form_name']) == "") {
        tdomf_set_option_form(TDOMF_OPTION_NAME,"",$form_id);
       } else {
        tdomf_set_option_form(TDOMF_OPTION_NAME,strip_tags($_POST['tdomf_form_name']),$form_id);
       }
       
       // Form description
       
       if(trim($_POST['tdomf_form_descp']) == "") {
       	tdomf_set_option_form(TDOMF_OPTION_DESCRIPTION,false,$form_id);
       } else {
        tdomf_set_option_form(TDOMF_OPTION_DESCRIPTION,$_POST['tdomf_form_descp'],$form_id);
       }
       
       // Include on "your submissions" page
       //
       $include = false;
      if(isset($_POST['tdomf_include_sub'])) { $include = true; }
      tdomf_set_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$include,$form_id);
       
      if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS) && $include) {
        $message .= sprintf(__("Saved Options for Form %d. <a href='%s'>See your form &raquo</a>","tdomf"),$form_id,"users.php?page=tdomf_your_submissions#tdomf_form%d")."<br/>";
      } else {
        $message .= sprintf(__("Saved Options for Form %d.","tdomf"),$form_id)."<br/>";
      }
      
      // widget count
      //
      $widget_count = 10;
      if(isset($_POST['tdomf_widget_count'])) { $widget_count = intval($_POST['tdomf_widget_count']); }
      if($widget_count < 1){ $widget_count = 1; }
      tdomf_set_option_form(TDOMF_OPTION_WIDGET_INSTANCES,$widget_count,$form_id);
      
      //Submit page instead of post
      //
      $use_page = false;
      if(isset($_POST['tdomf_use_type']) && $_POST['tdomf_use_type'] == 'page') { $use_page = true; }
      tdomf_set_option_form(TDOMF_OPTION_SUBMIT_PAGE,$use_page,$form_id);

      // Queue period
      //
      $tdomf_queue_period = intval($_POST['tdomf_queue_period']);
      tdomf_set_option_form(TDOMF_OPTION_QUEUE_PERIOD,$tdomf_queue_period,$form_id);
      
      // Queue on all 
      //
      $tdomf_queue_on_all = isset($_POST['tdomf_queue_on_all']);
      tdomf_set_option_form(TDOMF_OPTION_QUEUE_ON_ALL,$tdomf_queue_on_all,$form_id);
      
      // ajax
      //
      $tdomf_ajax = isset($_POST['tdomf_ajax']);
      tdomf_set_option_form(TDOMF_OPTION_AJAX,$tdomf_ajax,$form_id);
      
      // Send moderation email even for published posts
      //
      $tdomf_mod_email_on_pub = isset($_POST['tdomf_mod_email_on_pub']);
      tdomf_set_option_form(TDOMF_OPTION_MOD_EMAIL_ON_PUB,$tdomf_mod_email_on_pub,$form_id);
      
      // Admin users auto-publish?
      //
      $tdomf_publish_no_mod = isset($_POST['tdomf_user_publish_auto']);
      tdomf_set_option_form(TDOMF_OPTION_PUBLISH_NO_MOD,$tdomf_publish_no_mod,$form_id);
      
      // Spam
      //
      $message .= tdomf_handle_spam_options_actions($form_id);
      
      tdomf_log_message("Options Saved for Form ID $form_id");
       
  } else if(isset($_REQUEST['delete'])) {
      
    $form_id = intval($_REQUEST['delete']);
    
    check_admin_referer('tdomf-delete-form-'.$form_id);
    
    if(tdomf_form_exists($form_id)) {
      $count_forms = count(tdomf_get_form_ids());
      if($count_forms > 1) {
        if(tdomf_delete_form($form_id)) {
           $message .= sprintf(__("Form %d deleted.<br/>","tdomf"),$form_id);
        } else {
          $message .= sprintf(__("Could not delete Form %d!<br/>","tdomf"),$form_id);
        }
      } else {
        $message .= sprintf(__("You cannot delete the last form! There must be at least one form in the system.<br/>","tdomf"),$form_id);
      }
    } else {
      $message .= sprintf(__("Form %d is not valid!<br/>","tdomf"),$form_id);
    }
  } else if(isset($_REQUEST['copy'])) {
    
    $form_id = intval($_REQUEST['copy']);
    
    check_admin_referer('tdomf-copy-form-'.$form_id);
    
    $copy_form_id = tdomf_copy_form($form_id);
   
    if($copy_form_id != 0) {
      $message .= sprintf(__("Form %d copied with id %d.<br/>","tdomf"),$form_id,$copy_form_id);
      $retValue = $copy_form_id;
    } else {
      $message .= sprintf(__("Failed to copy Form %d!<br/>","tdomf"),$form_id);
    }
        
  } else if(isset($_REQUEST['new'])) {
    
    check_admin_referer('tdomf-new-form');
    
    $form_id = tdomf_create_form(__('New Form','tdomf'),array());
   
    if($form_id != 0) {
      $message .= sprintf(__("New form created with %d.<br/>","tdomf"),$form_id);
      $retValue = $form_id;
    } else {
      $message .= __("Failed to create new Form!<br/>","tdomf");
    }
  }

   // Warnings

   $message .= tdomf_get_error_messages(false);

   if(!empty($message)) { ?>
   <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
   <?php }
   
   return $retValue;
}

?>
