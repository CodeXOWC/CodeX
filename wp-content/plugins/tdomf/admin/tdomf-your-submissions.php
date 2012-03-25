<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/////////////////////////////////
// User "Your Submission" page //
/////////////////////////////////

// Grab a list of published submitted posts for user
//
function tdomf_get_user_published_posts($user_id = 0, $offset = 0, $limit = 0) {
  /*global $wpdb;
	$query = "SELECT ID, post_title, meta_value, post_status, post_modified_gmt, post_modified, post_date, post_date_gmt ";
	$query .= "FROM $wpdb->posts ";
	$query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
   $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
   $query .= "AND post_status = 'publish' ";
   $query .= "AND meta_value = '$user_id' ";
   	$query .= "ORDER BY ID DESC ";
   if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
      if($offset > 0) {
         $query .= "OFFSET $offset ";
   }
	return $wpdb->get_results( $query );*/
    return tdomf_get_posts(array('limit' => $limit,
                                 'offset' => $offset,
                                 'user_id' => $user_id,
                                 'post_status' => array('publish')));
}

function tdomf_get_user_scheduled_posts($user_id = 0, $offset = 0, $limit = 0) {
  /*global $wpdb;
	$query = "SELECT ID, post_title, meta_value, post_status, post_modified_gmt, post_modified, post_date, post_date_gmt ";
	$query .= "FROM $wpdb->posts ";
	$query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
   $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
   $query .= "AND post_status = 'future' ";
   $query .= "AND meta_value = '$user_id' ";
   	$query .= "ORDER BY ID DESC ";
   if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
      if($offset > 0) {
         $query .= "OFFSET $offset ";
   }
	return $wpdb->get_results( $query );*/
    return tdomf_get_posts(array('limit' => $limit,
                                 'offset' => $offset,
                                 'post_status' => array('future'),
                                 'user_id' => $user_id ));
}

// Grab a list of unmoderated submitted posts for user
//
function tdomf_get_user_draft_posts($user_id = 0, $offset = 0, $limit = 0) {
  /*global $wpdb;
	$query = "SELECT ID, post_title, meta_value, post_status  ";
	$query .= "FROM $wpdb->posts ";
	$query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
   $query .= "WHERE meta_key = '".TDOMF_KEY_USER_ID."' ";
   $query .= "AND post_status = 'draft' ";
   $query .= "AND meta_value = '$user_id' ";
   	$query .= "ORDER BY ID DESC ";
   if($limit > 0) {
         $query .= "LIMIT $limit ";
      }
      if($offset > 0) {
         $query .= "OFFSET $offset ";
   }
	return $wpdb->get_results( $query );*/
    return tdomf_get_posts(array('limit' => $limit,
                                 'offset' => $offset,
                                 'user_id' => $user_id,
                                 'post_status' => array('draft')));
}

function tdomf_get_post_time( $d = 'U', $gmt = false, $post ) { // returns timestamp
	if ( $gmt )
		$time = $post->post_date_gmt;
	else
		$time = $post->post_date;

	$time = mysql2date($d, $time);
	return apply_filters('get_the_time', $time, $d, $gmt);
}

// include form style sheet on page
//
function tdomf_show_your_submissions_admin_head() {
   if(preg_match('/page=tdomf_your_submissions/',$_SERVER['REQUEST_URI'])) {
   ?>
   <link rel="stylesheet" href="<?php echo TDOMF_URLPATH; ?>tdomf-style-form.css" type="text/css" media="screen" />
   <?php
   }
}
add_action( 'admin_head', 'tdomf_show_your_submissions_admin_head' );

// Show the page!
//
function tdomf_show_your_submissions_menu() {
  global $current_user;

  // how many of the recently published/approved entries to see
  //
  $limit = 10;
  
  get_currentuserinfo();
  
  $tdomf_flag = get_usermeta($current_user->ID,TDOMF_KEY_FLAG);
  $sub_total = tdomf_get_users_submitted_posts_count($current_user->ID);
  $app_total = tdomf_get_users_published_posts_count($current_user->ID);
  $user_status = get_usermeta($current_user->ID,TDOMF_KEY_STATUS);
  $app_posts = tdomf_get_user_published_posts($current_user->ID,0,$limit);
  $mod_posts = tdomf_get_user_draft_posts($current_user->ID);
  $mod_total = count($mod_posts);
  $fut_posts = tdomf_get_user_scheduled_posts($current_user->ID);
  $fut_total = count($fut_posts);
  $unapp_edits = tdomf_get_edits(array('state' => 'unapproved', 'unique_post_ids' => true, 'user_id' => $current_user->ID));
  $app_edits = tdomf_get_edits(array('state' => 'approved', 'unique_post_ids' => true, 'user_id' => $current_user->ID, 'limit' => $limit));
  
  ?>

  <div class="wrap">
    <h2><?php _e('Your Submissions', 'tdomf') ?></h2>
    
    <?php if(in_array($_REQUEST['REMOTE_ADDR'],tdomf_get_ips_banned())) { ?>
      <?php printf(__("You are logged on from the banned IP %s. If this is in error please contact the <a href='mailto:%s'>admins</a>.","tdomf"),$_SERVER['REMOTE_ADDR'],get_bloginfo('admin_email')); ?>
    <?php } else if($user_status == TDOMF_USER_STATUS_BANNED) { ?>
      <?php printf(__("You are banned from using this functionality on this site. If this is in error please contact the <a href='mailto:%s'>admins</a>.","tdomf"),get_bloginfo('admin_email')); ?>
    <?php } else { ?>

      <p>
      <?php if($user_status == TDOMF_USER_STATUS_TRUSTED) { ?>
        <?php printf(__("Good to see you again <b>%s</b>! ","tdomf"),$current_user->display_name); ?>
      <?php } else if($tdomf_flag) { ?>
        <?php printf(__("Welcome back <b>%s</b>!","tdomf"),$current_user->display_name); ?>
      <?php } else { ?>
        <?php printf(__("Welcome <b>%s</b>.","tdomf"),$current_user->display_name); ?>
      <?php } ?>
      </p>
      
      <p><?php printf(__("From here you can submit posts to the %s using the form below and check on the status of your submissions.","tdomf"),get_bloginfo()); ?></p>
      
      <?php if(current_user_can('edit_others_posts') || current_user_can('manage_options')) { ?>
      <ul>
      <?php if(current_user_can('manage_options')) { ?>
      <li><a href="admin.php?page=tdomf_show_options_menu"><?php _e("Configure Options","tdomf"); ?></a></li>
      <li><a href="admin.php?page=tdomf_show_form_menu"><?php _e("Modify Form","tdomf"); ?></a></li>
      <?php } ?>
      <li><a href="admin.php?page=tdomf_show_mod_posts_menu"><?php _e("Moderate Submissions","tdomf"); ?></a></li>
      </ul>
      <?php } ?>

    <?php if($tdomf_flag && ($sub_total > 0 || $app_total > 0)) { ?>
        
        <?php if($fut_total > 0) { ?>
            <h3><?php printf(__('Your Next %d Scheduled Submissions','tdomf'),$fut_total); ?></h3>
            <ul>
         <?php foreach($fut_posts as $p) { ?>
          <li>
          <?php $t_time = get_the_time(__('Y/m/d g:i:s A'));
                $m_time = $p->post_date;
                $time = tdomf_get_post_time('G', true, $p);
                if ( ( abs(time() - $time) ) < 86400 ) {
                    $h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
                } else {
                    $h_time = mysql2date(__('Y/m/d'), $m_time);
                } ?>
                <?php printf(__("<a href='%s'>%s</a> will be published %s","tdomf"),get_permalink($p->ID),$p->post_title,"<abbr title='$t_time'>$h_time</abbr>"); ?>
          </li>
         <?php } ?>
    	  </ul>
        <?php } ?>
        
       <?php if($app_total > 0) { ?>
         <h3><?php printf(__('Your Last %d Published Submissions','tdomf'),($app_total < 5) ? $app_total : 5 ); ?></h3>
         <ul>
         <?php foreach($app_posts as $p) { ?>
          <li>
          <?php $t_time = get_the_time(__('Y/m/d g:i:s A'));
                $m_time = $p->post_date;
                $time = tdomf_get_post_time('G', true, $p);
                if ( ( abs(time() - $time) ) < 86400 ) {
                    $h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
                } else {
                    $h_time = mysql2date(__('Y/m/d'), $m_time);
                } ?>
                <?php printf(__("<a href='%s'>%s</a> approved %s","tdomf"),get_permalink($p->ID),$p->post_title,"<abbr title='$t_time'>$h_time</abbr>"); ?>
          </li>
         <?php } ?>
    	  </ul>
       <?php } ?>
       
       <?php if(($mod_total)> 0) { ?>
         <h3><?php _e('Your Sumissions awaiting Moderation','tdomf'); ?></h3>
         <ul>
         <?php foreach($mod_posts as $p) { ?>
          <li>"<?php echo $p->post_title; ?>"</li>
         <?php } ?>
    	  </ul>
       <?php } ?>
    <?php } ?>      
      
    <?php if(!empty($app_edits)) {
            $num = number_format_i18n(count($app_edits));
            $text = __ngettext( 'Your Last Approved Contribution', 'Your Last %d Approved Contributions', count($app_edits) );
            ?>
        <h3><?php printf($text, count($app_edits)); ?></h3>
        <ul>
        <?php foreach($app_edits as $app_edit) { ?>
            <li>
            <?php $edit = tdomf_get_edits(array('state' => 'approved', 'post_id' =>$app_edit->post_id, 'user_id' => $current_user->ID, 'limit' => 1));
                  $edit = $edit[0];
                  $t_time = get_the_time(__('Y/m/d g:i:s A'));
                  $h_time = mysql2date(__('Y/m/d'), $edit->date);
                  $post = get_post($app_edit->post_id);
                  printf(__("<a href='%s'>%s</a> edited %s","tdomf"),get_permalink($app_edit->post_id),$post->post_title,"<abbr title='$t_time'>$h_time</abbr>"); ?>
            </li>
        <?php } ?>
        </ul>
    <?php } ?>
    
    <?php if(!empty($unapp_edits)) {
            $num = number_format_i18n(count($unapp_edits));
            $text = __ngettext( 'Your Contribution awaiting Moderation', 'Your Contributions awaiting Moderation', count($unapp_edits) );
            ?>
        <h3><?php printf($text, count($unapp_edits)); ?></h3>
        <ul>
        <?php foreach($unapp_edits as $unapp_edit) { ?>
            <li>
            <?php $edit = tdomf_get_edits(array('state' => 'unapproved', 'post_id' =>$unapp_edit->post_id, 'user_id' => $current_user->ID, 'limit' => 1));
                  $edit = $edit[0];
                  $t_time = get_the_time(__('Y/m/d g:i:s A'));
                  $h_time = mysql2date(__('Y/m/d'), $edit->date);
                  $post = get_post($unapp_edit->post_id);
                  printf(__("<a href='%s'>%s</a> edited %s","tdomf"),get_permalink($unapp_edit->post_id),$post->post_title,"<abbr title='$t_time'>$h_time</abbr>"); ?>
            </li>
        <?php } ?>
        </ul>
    <?php } ?>
    
     </div>
      
     <!-- Form formatting -->     
     <style>
     .tdomf_form {
     }
     .tdomf_form fieldset legend {
       #border-bottom: 1px dotted black;
       font-weight: bold;
       padding: 0px;
       margin: 0px;
       padding-bottom: 10px;
     }
     .tdomf_form_preview {
       border: 1px dotted black;
       padding: 5px;
       margin: 5px;
       margin-bottom: 20px;
     }
     .tdomf_form_preview p {
       margin-left: 15px;
     }
     .tdomf_form .required {
       color: red;
     }
     .tdomf_form fieldset {
       margin-bottom: 10px;
       border: 0;
     }
     </style>
      
    <?php $form_ids = tdomf_get_form_ids(); 
          if(!empty($form_ids)) {
            foreach($form_ids as $form_id) { 
              if(tdomf_get_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$form_id->form_id)) {
                  $edit = tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id->form_id); 
                  $post_id = false;
                  if(isset($_REQUEST['tdomf_post_id'])) { $post_id = intval($_REQUEST['tdomf_post_id']); }
                  $good = true;
                  if($edit && tdomf_check_permissions_form($form_id->form_id, $post_id) != NULL) {
                      $good = false;
                  }
                  if($good) { ?>
     <div class="wrap">
        <h2><?php echo tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id->form_id); ?></h2>
        <p><?php echo tdomf_get_option_form(TDOMF_OPTION_DESCRIPTION,$form_id->form_id); ?></p>
        <?php echo tdomf_generate_form($form_id->form_id); ?>
        <br/><br/>
     </div>
          <?php } } }
          }?>
    <?php } ?>
    
  </div>

  <p><center><?php _e('Powered by the <a href="http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/">TDO Mini Forms Plugin.','tdomf'); ?></a></center></p>
  
<?php
}
?>
