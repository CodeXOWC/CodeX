<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/////////////////////////
// Admin Overview Page //
/////////////////////////

function tdomf_overview_admin_head() {
   global $wp_version;
   if(tdomf_wp25() && preg_match('/page=tdo-mini-forms/',$_SERVER['REQUEST_URI'])) {
   ?>
   <style type="text/css">
      #zeitgeist {
	background: #eee;
	border: 1px solid #c5c5c5;
	float: right;
	font-size: 90%;
	margin-bottom: .5em;
	margin-left: 1em;
	margin-top: .5em;
	padding: 1em;
	width: 40%;
      }
      #zeitgeist h2, fieldset legend a {
	background: none;
}

/** html*/ 
#zeitgeist h2 {
	padding-top: 10px;
  width: 100%;
}

#zeitgeist h3 {
	border-bottom: 1px solid #ccc;
	font-size: 16px;
	margin: 1em 0 0;
}

#zeitgeist h3 cite {
	font-size: 12px;
	font-style: normal;
}

#zeitgeist li, #zeitgeist p {
	margin: .2em 0;
}

#zeitgeist ul {
	margin: 0 0 .3em .6em;
	padding: 0 0 0 .6em;
}
   </style>
   <?php
   }
}
add_action( 'admin_head', 'tdomf_overview_admin_head' );

// Return a count of posts from unregistered users
//
function tdomf_get_unregistered_users_posts_count() {
  global $wpdb;
  // This function doesn't work yet...
  $def_aut = get_option(TDOMF_DEFAULT_AUTHOR);
  if($def_aut != false) {
  	$query = "SELECT count(ID) ";
    $query .= "FROM $wpdb->posts ";
    $query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    $query .= "WHERE meta_key = '".TDOMF_KEY_FLAG."' ";
    $query .= "WHERE post_author = '$def_aut' ";
    $query .= "OR post_author = '0' ";
    return intval($wpdb->get_var( $query ));
  }
  return 0;
}

// Show the page
//
function tdomf_overview_menu()  {
	global $wpdb,$wp_roles;

    // Initilise the plugin for the first time here. This gets called when you click the TDOMF button in the menu.
    // Doing it here means you can delete all the options!
    tdomf_init();

	// get feed_messages
	require_once(ABSPATH . WPINC . '/rss.php');
  
  if(!isset($wp_roles)) {
  	$wp_roles = new WP_Roles();
  }
  $roles = $wp_roles->role_objects;

?>
  <div class="wrap">
    <h2><?php _e('Welcome to TDO Mini Forms', 'tdomf') ?></h2>

    <div id="zeitgeist">

        <?php $features = tdomf_new_features(); 
              if($features) { ?>
                <h2><?php printf(__("New Features in %s for you","tdomf"),TDOMF_VERSION); ?></h2>
                <?php echo $features; ?>
        <?php } ?>
    
    	  <h2><?php _e('Latest Activity', 'tdomf') ?></h2>
        
    	  <h3><?php _e('Log', 'tdomf') ?><?php if(current_user_can('manage_options')) { ?><a href="admin.php?page=tdomf_show_log_menu" title="Full Log...">&raquo;</a><?php } ?></h3>

    	  <p><?php echo tdomf_get_log(5); ?></p>

        <?php if(tdomf_is_moderation_in_use()) { ?>

          <?php $posts = tdomf_get_unmoderated_posts(0,10);
          if(!empty($posts)) { ?>
            
        	  <h3><?php _e('Latest Submissions', 'tdomf'); ?><?php if(current_user_can('edit_others_posts')) { ?><a href="admin.php?page=tdomf_show_mod_posts_menu&f=0" title="<?php _e("Moderate Submissions...","tdomf"); ?>">&raquo;</a><?php } ?></h3>

          <ul>

              
                <?php foreach($posts as $p) { 
                       echo tdomf_get_post_list_line($p); 
                    } } ?>
    	  </ul>

    	  <?php } ?>

          <?php if(get_option(TDOMF_OPTION_SPAM)) { ?>
              <?php $spam_count = tdomf_get_spam_posts_count(); 
              if($spam_count > 0) { ?>
                  <h3><?php printf(__('There are %d spam submissions','tdomf'),$spam_count); ?><?php if(current_user_can('edit_others_posts')) { ?><a href="admin.php?page=tdomf_show_mod_posts_menu&f=3" title="<?php _e("Moderate Spam...","tdomf"); ?>">&raquo;</a><?php } ?></h3>
              <?php } ?>
          <?php } ?>
          
          <?php $posts = tdomf_get_published_posts(0,10);
                if(!empty($posts)) { ?>

    	  <h3><?php _e('Latest Approved Submissions', 'tdomf'); ?><?php if(current_user_can('edit_others_posts')) { ?><a href="admin.php?page=tdomf_show_mod_posts_menu&f=1" title="Moderate Posts...">&raquo;</a><?php } ?></h3>

    	  <ul>
                  
                  
              <?php	foreach($posts as $p) { 
                       echo tdomf_get_post_list_line($p); 
                    } 
                } ?>
    	  </ul>

          

    	  <h3><?php _e('Stats', 'tdomf'); ?></h3>

          <?php $stat_sub_ever  = get_option(TDOMF_STAT_SUBMITTED);
                $stat_edit_ever = get_option(TDOMF_STAT_EDITED);
                $stat_unmod     = tdomf_get_unmoderated_posts_count();
                $stat_edit_unmod  = tdomf_get_edits(array('state' => 'unapproved', 'count' => true, 'unique_post_ids' => true));
                $stat_sub_cur   = tdomf_get_submitted_posts_count();
                $stat_edit_cur  = tdomf_get_edits(array('count' => true, 'unique_post_ids' => true));
                $stat_mod       = $stat_sub_cur - $stat_unmod;
                $stat_edit_mod  = tdomf_get_edits(array('state' => 'approved', 'count' => true, 'unique_post_ids' => true)); 
                $stat_spam      = get_option(TDOMF_STAT_SPAM); ?>

          <?php if(get_option(TDOMF_OPTION_SPAM)) { ?>
              <p><?php printf(__("You are using version %s (build %d) of the TDO Mini Forms plugin. There has been %d posts (or pages) submitted, %d edits submitted, %d posts approved and %d edits approved. %d spam submissions have been caught by Akismet","tdomf"),TDOMF_VERSION,get_option(TDOMF_VERSION_CURRENT),$stat_sub_ever,$stat_edit_ever,$stat_mod,$stat_edit_mod,$stat_spam); ?>
          <?php } else { ?>
              <p><?php printf(__("You are using version %s (build %d) of the TDO Mini Forms plugin. There has been %d posts (or pages) submitted, %d edits submitted, %d posts approved and %d edits approved.","tdomf"),TDOMF_VERSION,get_option(TDOMF_VERSION_CURRENT),$stat_sub_ever,$stat_edit_ever,$stat_mod,$stat_edit_mod); ?>
          <?php } ?>
        
        <?php $rss = fetch_rss('http://thedeadone.net/forum/?cat=6&feed=rss');
               if ( isset($rss->items) && 0 != count($rss->items) ) {
                 $rss->items = array_slice($rss->items, 0, 5); 
                 echo "<h3>".__('Latest Support Forum Topics','tdomf')."</h3><ul>";
                 foreach ($rss->items as $item) { ?>
                 <li><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a></li>
        <?php    }
                 echo "</ul>";
              } ?>
        
    </div>

    <?php echo "<p>".$message = tdomf_get_error_messages()."</p>";  ?>

    <table style="margin:0px;padding:0px">
      <tr>
        <td>
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post" >
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" style="border:0px;" >
          <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
          <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
          </form>
        </td><td>
          <?php printf(__("[<a href='%s'>My Amazon Wishlist</a>]","tdomf"),"http://www.amazon.co.uk/gp/registry/23S7OL9W6Q4JT"); ?>
        </td>
        <td>
          <?php printf(__("[<a href='%s'>Rate TDO-Mini-Forms on Wordpress.org!</a>]","tdomf"),"http://wordpress.org/extend/plugins/tdo-mini-forms/#rate-response"); ?>
        </td>
      </tr>
    </table>
    
    <p><?php _e("Use these links to get started:","tdomf"); ?></p>

    <ul>
      <li><a href="admin.php?page=tdomf_show_options_menu"><?php _e("Configure TDO Mini Forms","tdomf"); ?></a></li>
      <li><a href="admin.php?page=tdomf_show_form_menu"><?php _e("Form Widgets","tdomf"); ?></a></li>
      <?php if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) { ?>
          <?php if(current_user_can('edit_users')) { ?>
                <li><a href="users.php?page=tdomf_your_submissions"><?php _e("Your Submissions Page","tdomf"); ?></a></li>
          <?php } else { ?>
                <li><a href="profile.php?page=tdomf_your_submissions"><?php _e("Your Submissions Page","tdomf"); ?></a></li>
          <?php } ?>
      <?php } ?>
      <?php if(current_user_can('manage_options')) { ?>
      <li><a href="<?php echo TDOMF_URLPATH; ?>tdomf-phpinfo.php"><?php _e("phpinfo()","tdomf"); ?></a></li>
      <li><a href="admin.php?page=<?php echo TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR; ?>tdomf-info.php"><?php _e("tdomfinfo()","tdomf"); ?></a></li>
      <?php } ?>
    </ul>

<p><?php _e('Need help with TDO Mini Forms? Please see the <a href="http://thedeadone.net/forum">support forums on thedeadone.net</a> or the <a href="http://wordpress.org/tags/tdo-mini-forms">support forums on wordpress.org</a>.',"tdomf"); ?></p>

    <h3><?php _e('Welcome', 'tdomf') ?></h3>

    <p>
    <?php _e("TDO Mini Forms plugin allows you to provide a form to your readers and users so that they can submit posts to your blog, even if they don't have rights to do so. You can control what type of users, such as unregistered users and subscribers, can access and use the form. Posts are submitted as draft so that you can approve them before they are published. (You can optionally turn this off so that submissions are automatically published). As of version 0.7, you can now also customise the form using widgets.","tdomf"); ?>
    </p>
        
    <div id="devnews">
    <h3><?php _e('Latest TDO Mini Forms News!',  'tdomf') ?></h3>

    <?php
      $rss = fetch_rss('http://thedeadone.net/tag/tdomf/feed');

      if ( isset($rss->items) && 0 != count($rss->items) )
      {
        $rss->items = array_slice($rss->items, 0, 4);
        foreach ($rss->items as $item)
        {
        ?>
          <h4><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a> &#8212; <?php echo human_time_diff(strtotime($item['pubdate'], time())); ?></h4>
          <p><?php echo '<strong>'.date("F, jS", strtotime($item['pubdate'])).'</strong> - '.$item['description']; ?></p>
        <?php
        }
      }
      else
      {
        ?>
        <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">thedeadone.net</a> to check for updates.', 'tdomf'), 'http://thedeadone.net/index.php?tag=tdomf') ?></p>
        <?php
      }
    ?>
    </div>
    <br style="clear: both" />
   </div>
    <?php
}

function tdomf_get_post_list_line($p) {
  $form_id = get_post_meta($p->ID, TDOMF_KEY_FORM_ID, true);
  $submitter = get_post_meta($p->ID, TDOMF_KEY_NAME, true);
  if($form_id == false || !tdomf_form_exists($form_id)) {
    if($submitter == false || empty($submitter)) {
      return "<li>".sprintf(__("<a href=\"%s\">\"%s\"</a>","tdomf"),get_permalink($p->ID),$p->post_title)."</li>";
    } else {
       return "<li>".sprintf(__("<a href=\"%s\">\"%s\"</a> submitted by %s","tdomf"),get_permalink($p->ID),$p->post_title,$submitter)."</li>";
    }
  } else if($submitter == false || empty($submitter)) {
    return "<li>".sprintf(__("<a href=\"%s\">\"%s\"</a> using form %d","tdomf"),get_permalink($p->ID),$p->post_title, $form_id)."</li>";
  }
  return "<li>".sprintf(__("<a href=\"%s\">\"%s\"</a> submitted by %s using form %d","tdomf"),get_permalink($p->ID),$p->post_title,$submitter,$form_id)."</li>";
}

function tdomf_dashboard_status() {

    $published_sub_count = tdomf_get_published_posts_count();
    $approved_edits_count = tdomf_get_edits(array('state' => 'approved', 'count' => true));  
    $scheduled_sub_count = tdomf_get_queued_posts_count();
    $spam_edits_count = tdomf_get_edits(array('state' => 'spam', 'count' => true, 'unique_post_ids' => true));
    $pending_edits_count = tdomf_get_edits(array('state' => 'unapproved', 'count' => true, 'unique_post_ids' => true));
    $pending_sub_count = tdomf_get_unmoderated_posts_count();
    $spam_sub_count = tdomf_get_spam_posts_count();
            
    echo '<tr>';

    $num = number_format_i18n($published_sub_count);
    $text = __ngettext( 'Approved Submission', 'Approved Submissions', $published_sub_count );
    $url = tdomf_get_mod_posts_url(array('show' => 'all'));
    echo '<td class="b b_approved"><a href="' . $url .'">' . $num . '</a></td>';
    echo '<td class="first t posts"><a class="approved" href="' . $url .'">' . $text . '</a></td>';

    $num = number_format_i18n($approved_edits_count);
    $text = __ngettext( 'Approved Contribution', 'Approved Contributions', $approved_edits_count );
    $url = tdomf_get_mod_posts_url(array('show' => 'approved_edits'));
    echo '<td class="b b_approved"><a href="' . $url .'">' . $num . '</a></td>';
    echo '<td class="first t posts"><a class="approved" href="' . $url .'">' . $text . '</a></td>';
    
    echo '</tr><tr>';
   
    if($scheduled_sub_count > 0) {
        $num = number_format_i18n($scheduled_sub_count);
        $text = __ngettext( 'Scheduled Submission', 'Scheduled Submissions', $scheduled_sub_count );
        $url = tdomf_get_mod_posts_url(array('show' => 'scheduled'));
        echo '<td class="b posts"><a href="' . $url .'">' . $num . '</a></td>';
        echo '<td class="first t posts"><a href="' . $url .'">' . $text . '</a></td>';
        echo '</tr><tr>';
    }
    
    if(get_option(TDOMF_OPTION_SPAM) && ($spam_edits_count > 0 || $spam_sub_count > 0)) {

        $num = number_format_i18n($pending_sub_count);
        $text = __ngettext( 'Pending Submission', 'Pending Submissions', $pending_sub_count );
        $url = tdomf_get_mod_posts_url(array('show' => 'pending_submissions'));
        echo '<td class="b b-waiting"><a class="waiting" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
        echo '<td class="first t"><a class="waiting" href="' . $url .'">' . $text . '</a></td>';
       
        $num = number_format_i18n($spam_sub_count);
        $text = __ngettext( 'Spam Submission', 'Spam Submissions', $spam_sub_count );
        $url = tdomf_get_mod_posts_url(array('show' => 'spam_submissions'));
        echo '<td class="b b-spam"><a class="waiting" href="' . $url .'"><span class=\'spam-count\'>' . $num . '</span></a></td>';
        echo '<td class="last t"><a class="spam" href="' . $url .'">' . $text . '</a></td>';
    
        echo '</tr><tr>';
        
        $num = number_format_i18n($pending_edits_count);
        $text = __ngettext( 'Pending Contribution', 'Pending Contributions', $pending_edits_count );
        echo '<td class="b b-waiting"><a class="waiting" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
        echo '<td class="first t"><a class="waiting" href="' . $url .'">' . $text . '</a></td>';
    
        $num = number_format_i18n($spam_edits_count);
        $text = __ngettext( 'Spam Contribution', 'Spam Contributions', $spam_edits_count );
        $url = tdomf_get_mod_posts_url(array('show' => 'spam_edits'));
        echo '<td class="b b-waiting"><a class="waiting" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
        echo '<td class="first t"><a class="waiting" href="' . $url .'">' . $text . '</a></td>';
    } else {
        $num = number_format_i18n($pending_sub_count);
        $url = tdomf_get_mod_posts_url(array('show' => 'pending_submissions'));
        $text = __ngettext( 'Pending Submission', 'Pending Submissions', $pending_sub_count );
        echo '<td class="b b-waiting"><a class="waiting" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
        echo '<td class="first t"><a class="waiting" href="' . $url .'">' . $text . '</a></td>';

        $num = number_format_i18n($pending_edits_count);
        $url = tdomf_get_mod_posts_url(array('show' => 'pending_edits'));
        $text = __ngettext( 'Pending Contribution', 'Pending Contributions', $pending_edits_count );
        echo '<td class="b b-waiting"><a class="waiting" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
        echo '<td class="last t"><a class="waiting" href="' . $url .'">' . $text . '</a></td>';

    }
    
    echo '</tr>';
}
add_action('right_now_table_end','tdomf_dashboard_status');

function tdomf_overview_please_upgrade() {
    $ver_cur = get_option(TDOMF_VERSION_CURRENT);
    if($ver_cur != false && $ver_cur != TDOMF_BUILD) { ?>
        <div id="message" class="updated" ><p>
        <?php printf(__('You\'ve recently upgraded TDO Mini Forms. To finalise the upgrade process, <a href="%s">please visit the overview page</a>. Thank you.','tdomf'),"admin.php?page=tdo-mini-forms"); ?>
        </p></div>
    <?php }
}
add_action( 'admin_notices', 'tdomf_overview_please_upgrade' );

?>