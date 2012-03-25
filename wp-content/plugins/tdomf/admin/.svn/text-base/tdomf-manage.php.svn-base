<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

///////////////////////////////
// Manage Users and IPs Page //
///////////////////////////////

// Return count of submitted posts from specific user
//
function tdomf_get_users_submitted_posts_count($user_id = 0) {
  /*global $wpdb;
  $query = "SELECT count(ID) ";
  $query .= "FROM $wpdb->posts ";
  $query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
  $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
  $query .= "AND meta_value = '$user_id' ";
  return intval($wpdb->get_var( $query ));*/
  return tdomf_get_posts(array('count' => true,
                               'user_id' => $user_id));
}

// Return count of published posts from specific user
//
function tdomf_get_users_published_posts_count($user_id = 0) {
  /*global $wpdb;
	$query = "SELECT count(ID) ";
	$query .= "FROM $wpdb->posts ";
	$query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
    $query .= "AND meta_value = '$user_id' ";
     $query .= "AND post_status = 'publish' ";
	return intval($wpdb->get_var( $query ));*/
    return tdomf_get_posts(array('count' => true,
                                 'post_status' => array('publish','future'),
                                 'user_id' => $user_id));
}


// Grab a list of ips from where posts have been submitted
//
function tdomf_get_ips($offset = 0, $limit = 0) {
    global $wpdb;
    $query = "SELECT DISTINCT meta_value AS ip ";
    $query .= "FROM $wpdb->posts ";
    $query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_IP."' ";
    $query .= "ORDER BY meta_value DESC ";
    if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
    if($offset > 0) {
         $query .= "OFFSET $offset ";
    }           
    return $wpdb->get_results( $query );
}

// Get a count of ips
//
function tdomf_get_ips_count() {
    /*global $wpdb;
    $query = "SELECT DISTINCT count(meta_value) ";
    $query .= "FROM $wpdb->posts ";
    $query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_IP."' ";
    return intval($wpdb->get_var( $query ));*/
    return count(tdomf_get_ips());
}

// Get banned ips list
//
function tdomf_get_ips_banned($offset = 0, $limit = 0) {
    $banned_ips = get_option(TDOMF_BANNED_IPS);
    if($banned_ips == false) { $banned_ips = array(); }
    else { 
      $banned_ips = explode( ";", $banned_ips);
      sort($banned_ips);
      if($limit > 0 || $offset > 0) {
        if($limit <= 0) {
          $banned_ips = array_slice($banned_ips,$offset);
        } else {
          $banned_ips = array_slice($banned_ips,$offset,$limit);
        }
      }
    }
    return $banned_ips;
}

// Get count of banned ips
//
function tdomf_get_ips_banned_count() {
    return count(tdomf_get_ips_banned(0,0));
}

// Grab a list of user ids of users that have submitted a post
//
function tdomf_get_users_submitted($offset = 0, $limit = 0) {
    global $wpdb;
    $query = "SELECT DISTINCT ID AS user_id ";
    $query .= "FROM $wpdb->users ";
    $query .= "LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_FLAG."' ";
    $query .= "ORDER BY ID DESC  ";
    if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
    if($offset > 0) {
         $query .= "OFFSET $offset ";
    }       
    return $wpdb->get_results( $query );
}

// Get count of users who have submitted
//
function tdomf_get_users_submitted_count() {
    global $wpdb;
    $query = "SELECT DISTINCT count(ID) ";
    $query .= "FROM $wpdb->users ";
    $query .= "LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_FLAG."' ";
    return intval($wpdb->get_var( $query ));
}

// Grab banned suers
//
function tdomf_get_users_banned($offset = 0, $limit = 0) {
    global $wpdb;
    $query = "SELECT DISTINCT ID AS user_id, meta_value, meta_key ";
    $query .= "FROM $wpdb->users ";
    $query .= "LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) ";
    $query .= "WHERE meta_value = \"".TDOMF_USER_STATUS_BANNED."\" ";
    $query .= "AND meta_key = '".TDOMF_KEY_STATUS."' ";
    $query .= "ORDER BY ID DESC ";
    if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
    if($offset > 0) {
         $query .= "OFFSET $offset ";
    }       
    return $wpdb->get_results( $query );
}

// Get number of banned users
//
function tdomf_get_users_banned_count() {
    global $wpdb;
    $query = "SELECT DISTINCT count(ID) ";
    $query .= "FROM $wpdb->users ";
    $query .= "LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_STATUS."' ";
    $query .= "AND meta_value = '".TDOMF_USER_STATUS_BANNED."' ";
    return intval($wpdb->get_var( $query ));
}

// Get trusted users
//
function tdomf_get_users_trusted($offset = 0, $limit = 0) {
    global $wpdb;
    $query = "SELECT DISTINCT ID AS user_id ";
    $query .= "FROM $wpdb->users ";
    $query .= "LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_STATUS."' ";
    $query .= "AND meta_value = '".TDOMF_USER_STATUS_TRUSTED."' ";
    $query .= "ORDER BY ID DESC ";
    if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
    if($offset > 0) {
         $query .= "OFFSET $offset ";
    }    
    return $wpdb->get_results( $query );
}

// Get count of trusted users
//
function tdomf_get_users_trusted_count() {
    global $wpdb;
    $query = "SELECT DISTINCT count(ID) ";
    $query .= "FROM $wpdb->users ";
    $query .= "LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_STATUS."' ";
    $query .= "AND meta_value = '".TDOMF_USER_STATUS_TRUSTED."' ";
    return intval($wpdb->get_var( $query ));
}

// Show page
//
function tdomf_show_manage_user_menu() {
  global $wp_roles;
 
  $limit = 15;
  if(isset($_REQUEST['limit'])){ $limit = intval($_REQUEST['limit']); }
  $offset = 0;
  if(isset($_REQUEST['offset'])){ $offset = intval($_REQUEST['offset']); }
  
  if(isset($_REQUEST['f']) && $_REQUEST['f'] == "1") {
      $users = tdomf_get_users_banned($offset,$limit);
      $max = tdomf_get_users_banned_count();
   } else if(isset($_REQUEST['f']) && $_REQUEST['f'] == "2") {
      $users = tdomf_get_users_trusted($offset,$limit);
      $max = tdomf_get_users_trusted_count();
   } else {
      $users = tdomf_get_users_submitted($offset,$limit);
      $max = tdomf_get_users_submitted_count();
   }

   ?>

   <div class="wrap">

   <?php if(isset($_REQUEST['f']) && $_REQUEST['f'] == "1") { ?>
       <h2><?php if($offset > 0) { _e("Previous Banned Users","tdomf"); }
               else { printf(__('Last %d Banned Users', 'tdomf'),$limit); } ?></h2>
   <?php } else if(isset($_REQUEST['f']) && $_REQUEST['f'] == "2") { ?>
       <h2><?php if($offset > 0) { _e("Previous Trusted Users","tdomf"); }
               else { printf(__('Last %d Trusted Users', 'tdomf'),$limit); } ?></h2>
   <?php } else { ?>
       <h2><?php if($offset > 0) { _e("Previous Submitters","tdomf"); }
            else { printf(__('Last %d Submitters', 'tdomf'),$limit); } ?></h2>
   <?php } ?>

    <p><?php _e("You can ban (and un-ban) any registered user. This means the user cannot use the forms. It has no impact on anything else. i.e. they can still read, comment and post. You can also make a user \"trusted\". This means that anything they submit using the form is automatically published.","tdomf"); ?></p>

    <p><a href="admin.php?page=tdomf_show_manage_menu&mode=ip"><?php _e("Manage Submitter IPs &raquo;","tdomf"); ?></a>
    
   <form method="post" action="admin.php?page=tdomf_show_manage_menu" id="filterusers" name="filterusers" >
   <fieldset>
	  <b><?php _e("Filter Users","tdomf"); ?></b>
      <select name="f">
      <option value="0" <?php if(!isset($_REQUEST['f']) || (isset($_REQUEST['f']) && $_REQUEST['f'] == "0")){ ?> selected <?php } ?>><?php _e("All Submitters","tdomf"); ?>
        <option value="1" <?php if(isset($_REQUEST['f']) && $_REQUEST['f'] == "1"){ ?> selected <?php } ?>><?php _e("Banned Users","tdomf"); ?>
        <option value="2" <?php if(isset($_REQUEST['f']) && $_REQUEST['f'] == "2"){ ?> selected <?php } ?>><?php _e("Trusted Users","tdomf"); ?>
      </select>
      <input type="submit" name="submit" value="Show" />
   </fieldset>
   </form>

   <br/>

   <?php if(count($users) <= 0) { _e("There are no users to moderate with this filter.","tdomf"); }
         else { ?>

<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}
//-->
</script>


   <form method="post" action="admin.php?page=tdomf_show_manage_menu" id="moderateusers" name="moderateusers" >

   <table class="widefat">
   <tr>
    <th scope="col" style="text-align: center"><input type="checkbox" onclick="checkAll(document.getElementById('moderateusers'));" /></th>

    <th scope="col"><?php _e("ID","tdomf"); ?></th>
	<th scope="col"><?php _e("Login","tdomf"); ?></th>
	<th scope="col"><?php _e("Display Name","tdomf"); ?></th>
	<th scope="col"><?php _e("Role","tdomf"); ?></th>
	<th scope="col"><?php _e("Status","tdomf"); ?></th>
    <th scope="col"><?php _e("Submitted<br/>Approved/Total","tdomf"); ?></th>
    <th scope="col"><?php _e("Contributed<br/>Approved/Total","tdomf"); ?></th>
    <th scope="col" colspan="2" style="text-align: center">Actions</th>
   </tr>

   <?php $i = 0;
         foreach($users as $u) {
           
         $i++;
		 if(($i%2) == 0) { ?>
		  <tr id='x' class=''>
	     <?php } else { ?>
		  <tr id='x' class='alternate'>
         <?php } ?>

               <?php $u = get_userdata($u->user_id); ?>

               <td><input type="checkbox" name="moderateusers[]" value="<?php echo $u->ID; ?>" /></td>
               <th scope="row"><?php echo $u->ID; ?></th>
		       <td><a href="edit.php?author=<?php echo $u->ID; ?>" ><?php echo $u->user_login; ?></a></td>
		       <td><a href="user-edit.php?user_id=<?php echo $u->ID ?>" ><?php echo $u->display_name; ?></a></td>

               <td>
		       <?php if (!isset($wp_roles)) { $wp_roles = new WP_Roles(); }
			         $roles = $wp_roles->role_objects;
			         $userrole = new WP_User($u->ID);
			         foreach($roles as $role) {
                        if($userrole->has_cap($role->name)) {
                            if(function_exists('translate_with_context')) {
                                $user_role = translate_with_context($wp_roles->role_names[$role->name]);
                            } else { 
                                $user_role = $wp_roles->role_names[$role->name]; 
                            }
                           break;
                        }
                     } ?>
		       <?php echo $user_role; ?>
		       </td>

		       <td>
		       <?php if($userrole->has_cap('edit_others_posts')){
		       	        _e("N/A","tdomf");
		             } else if(!get_usermeta($u->ID,TDOMF_KEY_STATUS)) {
		                _e("-","tdomf");
		             } else { _e(get_usermeta($u->ID,TDOMF_KEY_STATUS),"tdomf"); } ?>
		       </td>

             <td>
             <a href="<?php tdomf_get_mod_posts_url(array('echo' => true, 'user_id' => $u->ID)); ?>">
             <?php echo tdomf_get_users_published_posts_count($u->ID); ?>/<?php echo tdomf_get_users_submitted_posts_count($u->ID); ?>
             </a>
             </td>

             <td><?php echo tdomf_get_edits(array('user_id' => $u->ID, 'count' => true, 'state' => 'approved')); ?>/<?php echo tdomf_get_edits(array('user_id' => $u->ID, 'count' => true)); ?></td>
                 
           <?php if(isset($_REQUEST['f'])) { $farg = "&f=".$_REQUEST['f']; } ?>                 
                 
		       <td>
		       <?php if($userrole->has_cap('edit_others_posts')){ ?> N/A
		       <?php } else if(get_usermeta($u->ID,TDOMF_KEY_STATUS) == TDOMF_USER_STATUS_BANNED) { ?>
             <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_manage_menu&action=reset&user=$u->ID$farg&offset=$offset&limit=$limit",'tdomf-reset-user_'.$u->ID); ?>"><?php _e("Un-ban","tdomf"); ?></a>
		       <?php } else { ?>
             <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_manage_menu&action=ban&user=$u->ID$farg&offset=$offset&limit=$limit",'tdomf-ban-user_'.$u->ID); ?>"><?php _e("Ban","tdomf"); ?></a>
           <?php } ?>
		       </td>

		       <td>
		       <?php if($userrole->has_cap('edit_others_posts')){ ?> N/A
		       <?php } else if(get_usermeta($u->ID,TDOMF_KEY_STATUS) == TDOMF_USER_STATUS_TRUSTED) { ?>
             <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_manage_menu&action=reset&user=$u->ID$farg&offset=$offset&limit=$limit",'tdomf-reset-user_'.$u->ID); ?>"><?php _e("Un-trust","tdomf"); ?></a>
		       <?php } else { ?>
             <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_manage_menu&action=trust&user=$u->ID$farg&offset=$offset&limit=$limit",'tdomf-trust-user_'.$u->ID); ?>"><?php _e("Trust","tdomf"); ?></a>
           <?php } ?>
		       </td>

           </tr>

         <?php } ?>

   </table>

   <?php $farg = "0"; if(isset($_REQUEST['f'])) { $farg = $_REQUEST['f']; } ?>

   <input type="hidden" name="limit" id="limit" value="<?php echo $limit; ?>" />
   <input type="hidden" name="offset" id="offset" value="<?php echo $offset; ?>" />
   <input type="hidden" name="f" id="f" value="<?php echo $farg; ?>" />
   <?php if(function_exists('wp_nonce_field')){ wp_nonce_field('tdomf-manage-bulk_users'); } ?>

   <p class="submit">
    <input type="submit" name="ban_button" class="delete" value="<?php _e("Ban"); ?>" onclick="var numchecked = getNumChecked(document.getElementById('moderateusers')); if(numchecked < 1) { alert('Please select some users to ban'); return false } return confirm('You are about to ban ' + numchecked + ' users \n  \'Cancel\' to stop, \'OK\' to ban.')" />
    <input type="submit" name="trust_button" value="<?php _e("Trust"); ?>" onclick="var numchecked = getNumChecked(document.getElementById('moderateusers')); if(numchecked < 1) { alert('Please select some users to trust'); return false } return confirm('You are about to trust ' + numchecked + ' users \n  \'Cancel\' to stop, \'OK\' to trust')" />
    <input type="submit" name="clear_button" value="<?php _e("Reset"); ?>" onclick="var numchecked = getNumChecked(document.getElementById('moderateusers')); if(numchecked < 1) { alert('Please select some users to reset'); return false } return confirm('You are about to reset the status of ' + numchecked + ' users \n  \'Cancel\' to stop, \'OK\' to reset')" />
   </p>

   </form>

   <br/><br/>

   <div class="navigation">
   <?php if(($max - ($offset + $limit)) > 0 ) { ?>
      <div class="alignleft"><a href="admin.php?page=tdomf_show_manage_menu&offset=<?php echo $offset + $limit; ?><?php if(isset($_REQUEST['f'])) { echo "&f=".$_REQUEST['f']; } ?>">&laquo; <?php _e("Previous Entries","tdomf"); ?></a></div>
   <?php } ?>

   <?php if($offset > 0){ ?>
      <div class="alignright"><a href="admin.php?page=tdomf_show_manage_menu&offset=<?php echo $offset - $limit; ?><?php if(isset($_REQUEST['f'])) { echo "&f=".$_REQUEST['f']; } ?>"><?php _e("Next Entries","tdomf"); ?> &raquo;</a></div>
   <?php } ?>
   </div>

   <br/><br/>

   <?php } ?>

   </div> <!-- wrap -->

   <?php
}

// Show Manage IP page
//
function tdomf_show_manage_ip_menu() {
  
  $limit = 15;
  if(isset($_REQUEST['limit'])){ $limit = intval($_REQUEST['limit']); }
  $offset = 0;
  if(isset($_REQUEST['offset'])){ $offset = intval($_REQUEST['offset']); }
  
  if(isset($_REQUEST['f']) && $_REQUEST['f'] == "1") {
      $ips = tdomf_get_ips_banned($offset,$limit);
      $max = tdomf_get_ips_banned_count();
   } else {
      $ips = tdomf_get_ips($offset,$limit);
      $max = tdomf_get_ips_count();
   }

   // for checking if an ip is banned!
   $banned_ips = tdomf_get_ips_banned();
   
   ?>

   <div class="wrap">

   <?php if(isset($_REQUEST['f']) && $_REQUEST['f'] == "1") { ?>
       <h2><?php if($offset > 0) { _e("Previous Banned IPs","tdomf"); }
               else { printf(__('Last %d Banned IPs', 'tdomf'),$limit); } ?></h2>
   <?php } else { ?>
       <h2><?php if($offset > 0) { _e("Previous IPs","tdomf"); }
            else { printf(__('Last %d IPs', 'tdomf'),$limit); } ?></h2>
   <?php } ?>

    <p><?php _e("You can ban an IP address. This means no-one with this IP address can use the form. It has no impact on anything else. i.e. they can still read, comment and post.","tdomf"); ?></p>

    <p><a href="admin.php?page=tdomf_show_manage_menu"><?php _e("&laquo; Manage Users","tdomf"); ?></a></p>
    
   <form method="post" action="admin.php?page=tdomf_show_manage_menu&mode=ip" id="filterips" name="filterips" >
   <fieldset>
	  <b><?php _e("Filter Users","tdomf"); ?></b>
      <select name="f">
      <option value="0" <?php if(!isset($_REQUEST['f']) || (isset($_REQUEST['f']) && $_REQUEST['f'] == "0")){ ?> selected <?php } ?>><?php _e("All IPs of submitters","tdomf"); ?>
        <option value="1" <?php if(isset($_REQUEST['f']) && $_REQUEST['f'] == "1"){ ?> selected <?php } ?>><?php _e("Banned IPs","tdomf"); ?>
      </select>
      <input type="submit" name="submit" value="Show" />
   </fieldset>
   </form>

   <br/>

   <?php if(count($ips) <= 0) { _e("There are no ips to moderate with this filter.","tdomf"); }
         else { ?>

<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}
//-->
</script>


   <form method="post" action="admin.php?page=tdomf_show_manage_menu&mode=ip" id="moderateips" name="moderateips" >

   <table class="widefat">
   <tr>
    <th scope="col" style="text-align: center"><input type="checkbox" onclick="checkAll(document.getElementById('moderateips'));" /></th>

    <th scope="col"><?php _e("IP","tdomf"); ?></th>
	  <th scope="col"><?php _e("Status","tdomf"); ?></th>
    <th scope="col" colspan="1" style="text-align: center">Actions</th>
   </tr>
   
   <?php $i = 0;
         foreach($ips as $ip) {
         
           if(!is_string($ip)) {
             $ip = $ip->ip;
           }
           
           if(!empty($ip)) {
           
         $i++;
		 if(($i%2) == 0) { ?>
		  <tr id='x' class=''>
	     <?php } else { ?>
		  <tr id='x' class='alternate'>
         <?php } ?>

               <td><input type="checkbox" name="moderateips[]" value="<?php echo $ip; ?>" /></td>
               
               <th scope="row">
               <a href="<?php tdomf_get_mod_posts_url(array('echo' => true, 'ip' => $ip)); ?>">
               <?php echo $ip; ?>
               </a>
               </th>
               
		       <td><?php if(in_array($ip,$banned_ips)) { _e("Banned","tdomf"); } else { _e("Normal","tdomf"); } ?></td>

           <?php if(isset($_REQUEST['f'])) { $farg = "&f=".$_REQUEST['f']; } ?>                 

           <td>
           <?php if(in_array($ip,$banned_ips)) { ?>
             <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_manage_menu&mode=ip&action=reset&ip=$ip$farg&offset=$offset&limit=$limit",'tdomf-reset-ip_'.$ip); ?>"><?php _e("Un-ban","tdomf"); ?></a>
           <?php } else { ?>
             <a href="<?php echo wp_nonce_url("admin.php?page=tdomf_show_manage_menu&mode=ip&action=ban&ip=$ip$farg&offset=$offset&limit=$limit",'tdomf-ban-ip_'.$ip); ?>"><?php _e("Ban","tdomf"); ?></a>
           <?php } ?>
           </td>
           
           </tr>

           <?php } ?>
           
         <?php } ?>

   </table>

   <?php $farg = "0"; if(isset($_REQUEST['f'])) { $farg = $_REQUEST['f']; } ?>

   <input type="hidden" name="limit" id="limit" value="<?php echo $limit; ?>" />
   <input type="hidden" name="offset" id="offset" value="<?php echo $offset; ?>" />
   <input type="hidden" name="f" id="f" value="<?php echo $farg; ?>" />
   <?php if(function_exists('wp_nonce_field')){ wp_nonce_field('tdomf-manage-bulk_ips'); } ?>

   <p class="submit">
    <?php if(!isset($_REQUEST['f']) || $_REQUEST['f'] == "0") { ?>
       <input type="submit" name="ban_button" class="delete" value="<?php _e("Ban"); ?>" onclick="var numchecked = getNumChecked(document.getElementById('moderateips')); if(numchecked < 1) { alert('Please select some ips to ban'); return false } return confirm('You are about to ban ' + numchecked + ' ips \n  \'Cancel\' to stop, \'OK\' to ban.')" />
    <?php } ?>
    <input type="submit" name="clear_button" value="<?php _e("Reset"); ?>" onclick="var numchecked = getNumChecked(document.getElementById('moderateips')); if(numchecked < 1) { alert('Please select some ips to reset'); return false } return confirm('You are about to reset the status of ' + numchecked + ' ips \n  \'Cancel\' to stop, \'OK\' to reset')" />
   </p>

   </form>

   <br/><br/>

   <div class="navigation">
   <?php if(($max - ($offset + $limit)) > 0 ) { ?>
      <div class="alignleft"><a href="admin.php?page=tdomf_show_manage_menu&mode=ip&offset=<?php echo $offset + $limit; ?><?php if(isset($_REQUEST['f'])) { echo "&f=".$_REQUEST['f']; } ?>">&laquo; <?php _e("Previous Entries","tdomf"); ?></a></div>
   <?php } ?>

   <?php if($offset > 0){ ?>
      <div class="alignright"><a href="admin.php?page=tdomf_show_manage_menu&mode=ip&offset=<?php echo $offset - $limit; ?><?php if(isset($_REQUEST['f'])) { echo "&f=".$_REQUEST['f']; } ?>"><?php _e("Next Entries","tdomf"); ?> &raquo;</a></div>
   <?php } ?>
   </div>

   <br/><br/>

   <?php } ?>

   </div> <!-- wrap -->

   <?php
}

// Show the manage menu
//
function tdomf_show_manage_menu() {
   global $wp_roles;

   tdomf_manage_handler();

   if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "ip") {
     tdomf_show_manage_ip_menu();
   } else {
     tdomf_show_manage_user_menu();
   }

}

// Handle operations for this form
//
function tdomf_manage_handler() {
   global $wp_roles;

   $message = "";

   if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "ip") {
     if(isset($_REQUEST['ban_button'])) { 
        check_admin_referer('tdomf-manage-bulk_ips');
        $ips = $_REQUEST['moderateips'];
        if(!empty($ips)) {
           $list = "";
           $banned_ips = get_option(TDOMF_BANNED_IPS);
           $banned_ips_array = array();
           if($banned_ips == false) {
              $banned_ips = "";
           } else {
              $banned_ips_array = explode( ";", $banned_ips);
           }
           foreach($ips as $ip) {
             if(!in_array($ip,$banned_ips_array)) {
                $list .= $ip.", ";
                $banned_ips .= $ip.";";
             }
           }
           update_option(TDOMF_BANNED_IPS,$banned_ips);
           if($list != "" ){
              tdomf_log_message("Banned $list IPs");
              $message = sprintf(__("Banned IPs: %s","tdomf"),$list);
           }
        }
     } else if(isset($_REQUEST['clear_button'])) {
        check_admin_referer('tdomf-manage-bulk_ips');
        $ips = $_REQUEST['moderateips'];
        if(!empty($ips)) {
           $banned_ips = get_option(TDOMF_BANNED_IPS);
           if($banned_ips) {
             $banned_ips = explode( ";", $banned_ips);
             $updated_banned_ips = "";
             foreach($banned_ips as $banned_ip) {
                if(!in_array($banned_ip,$ips) && !empty($banned_ip) ) {
                   $updated_banned_ips .= $banned_ip.";";
                }
              }
              update_option(TDOMF_BANNED_IPS,$updated_banned_ips);
             $list = implode(",",$ips);
             if($list != "" ){
                tdomf_log_message("Reset $list IPs");
                $message = sprintf(__("Reset IPs: %s","tdomf"),$list);
             }           
           }
        }
     } else if(isset($_REQUEST['ip'])) {
        $ip = $_REQUEST['ip'];
        switch($_REQUEST['action']) {
           case "ban":
              check_admin_referer('tdomf-ban-ip_'.$ip);
              $banned_ips = get_option(TDOMF_BANNED_IPS);
              if($banned_ips == false) {
                $banned_ips = $ip.";";
              } else {
                if(!in_array($ip,explode( ";", $banned_ips))) {
                   $banned_ips .= $ip.";";
                }
              }
              tdomf_log_message("Banned ip $ip");
              $message = sprintf(__("IP %s banned","tdomf"),$ip);
              update_option(TDOMF_BANNED_IPS,$banned_ips);
           break;
           case "reset":
              check_admin_referer('tdomf-reset-ip_'.$ip);
              $banned_ips = get_option(TDOMF_BANNED_IPS);
              if($banned_ips == false) { $banned_ips = array(); }
              else { $banned_ips = explode( ";", $banned_ips); }
              $updated_banned_ips = "";
              foreach($banned_ips as $banned_ip) {
                if($banned_ip != $ip && !empty($banned_ip) ) {
                   $updated_banned_ips .= $banned_ip.";";
                }
               }
               update_option(TDOMF_BANNED_IPS,$updated_banned_ips); 
               tdomf_log_message("Un-Banned ip $ip");
              $message = sprintf(__("IP %s is un-banned","tdomf"),$ip);
           break;
           default:
              tdomf_log_message("Should not happen. Unknown action for manage ip: $action!",TDOMF_LOG_ERROR);
             break;
        }
     }     
   } else {
     if(isset($_REQUEST['ban_button'])) {
        check_admin_referer('tdomf-manage-bulk_users');
        $users = $_REQUEST['moderateusers'];
        if(!empty($users)) {
           $list = "";
           foreach($users as $u) {
              $userrole = new WP_User($u);
              if(!$userrole->has_cap('edit_others_posts')) {
                 update_usermeta($u, TDOMF_KEY_FLAG, true);
                 update_usermeta($u, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_BANNED);
                 $list .= $u.",";
              }
           }
           if($list != "" ){
              tdomf_log_message("Banned $list users");
              $message = sprintf(__("Banned users: %s","tdomf"),$list);
           }
        }
     } else if(isset($_REQUEST['clear_button'])) {
        check_admin_referer('tdomf-manage-bulk_users');
        $users = $_REQUEST['moderateusers'];
        if(!empty($users)) {
           $list = "";
           foreach($users as $u) {
              update_usermeta($u, TDOMF_KEY_FLAG, true);
              update_usermeta($u, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_OK);
              $list .= $u.",";
           }
           if($list != "" ){
              tdomf_log_message("Reset $list users");
              $message = sprintf(__("Reset %s","tdomf"),$list);
           }
        }
     } else if(isset($_REQUEST['trust_button'])) {
        check_admin_referer('tdomf-manage-bulk_users');
        $users = $_REQUEST['moderateusers'];
        if(!empty($users)) {
           $list = "";
           foreach($users as $u) {
              $userrole = new WP_User($u);
              if(!$userrole->has_cap('edit_others_posts')) {
                 update_usermeta($u, TDOMF_KEY_FLAG, true);
                 update_usermeta($u, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_TRUSTED);
                 $list .= $u.",";
              }
           }
           if($list != "" ){
              tdomf_log_message("Set $list users to trusted");
              $message = sprintf(__("Trusted users: %s","tdomf"),$list);
           }
        }
     } else if(isset($_REQUEST['user'])) {
        $user = $_REQUEST['user'];
        $userrole = new WP_User($user);
        switch($_REQUEST['action']) {
           case "ban":
              if(!$userrole->has_cap('edit_others_posts')) {
                 check_admin_referer('tdomf-ban-user_'.$user);
                 update_usermeta($user, TDOMF_KEY_FLAG, true);
                 update_usermeta($user, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_BANNED);
                 tdomf_log_message("Banned user $user");
                 $message = sprintf(__("User %d banned","tdomf"),$user);
              }
           break;
           case "trust":
              if(!$userrole->has_cap('edit_others_posts')) {
                 check_admin_referer('tdomf-trust-user_'.$user);
                 update_usermeta($user, TDOMF_KEY_FLAG, true);
                 update_usermeta($user, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_TRUSTED);
                 tdomf_log_message("Trusted user $user");
                 $message = sprintf(__("User %d trusted","tdomf"),$user);
              }
           break;
           case "reset":
              check_admin_referer('tdomf-reset-user_'.$user);
              update_usermeta($user, TDOMF_KEY_FLAG, true);
              update_usermeta($user, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_OK);
              tdomf_log_message("Reset user $user");
              $message = sprintf(__("User %d reset","tdomf"),$user);
           break;
           default:
              tdomf_log_message("Should not happen. Unknown action for manage users: $action!",TDOMF_LOG_ERROR);
             break;
        }
     }
   }
   
   if(!empty($message)) { ?>
      <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
   <?php }
}

// Set this user to trusted, if it makes sense
//
function tdomf_trust_user($user_id) 
{
    #tdomf_log_message("Check if user $user_id's status needs to be updated");
    if($user_id && $user_id != get_option(TDOMF_DEFAULT_AUTHOR)) {
        $trust_count = intval(get_option(TDOMF_OPTION_TRUST_COUNT));
        #tdomf_log_message("trust count = $trust_count");
        if($trust_count >= 0) {
           $user_status = get_usermeta($user_id,TDOMF_KEY_STATUS);
           $user_role = new WP_User($user_id);
           if($user_status != TDOMF_USER_STATUS_TRUSTED && !$user_role->has_cap("publish_posts")) {
               /** @todo bug: the counts here include posts that were automatically published, which isn't exactly correct, but it'll do. */
               $approved_submissions_count = tdomf_get_users_published_posts_count($user_id);
               #tdomf_log_message("User $user_id's approved submissions = $approved_submissions_count");
               $approved_edit_count = tdomf_get_edits(array('user_id' => $user_id, 'count' => true, 'state' => 'approved'));
               #tdomf_log_message("User $user_id's approved edits = $approved_edit_count");
               $approved_total = $approved_submissions_count + $approved_edit_count;
               // 0 is a valid trust count, means that at least one approved post makes the user truested
               if(($trust_count == 0 && $approved_total > 0) || ($trust_count > 0 && $trust_count <= $approved_total)) {
                   tdomf_log_message("User $user_id has $approved_submissions_count approved submissions and $approved_edit_count approved contributions. Automatically setting the user to trusted. Well done.",TDOMF_LOG_GOOD);
                   update_usermeta($user_id, TDOMF_KEY_FLAG, true);
                   update_usermeta($user_id, TDOMF_KEY_STATUS, TDOMF_USER_STATUS_TRUSTED);
               } else {
                   #tdomf_log_message("User $user_id's approved total $approved_total does hit trust count's threshold of $trust_count");
               }
           } else {
               #tdomf_log_message("User $user_id is already trusted (current status='$user_status') or can publish posts");
           }
        } else {
            #tdomf_log_message("trust count < 0, feature disabled");
        }
    } else {
        #tdomf_log_message("User $user_id is invalid or the default author", TDOMF_LOG_ERROR);
    }
}

// Auto-trust user
//
function tdomf_trust_user_publish_post_action($post_id) {
   global $wpdb;

   if(get_post_meta($post_id,TDOMF_KEY_FLAG,true)) {
       $user_id = get_post_meta($post_id, TDOMF_KEY_USER_ID, true);
       tdomf_trust_user($user_id);
   }
   
   // phew!
   
   return $post_id;
}
add_action('publish_post', 'tdomf_trust_user_publish_post_action');

?>
