<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/////////////////////////////
// Widgets for your Theme! //
/////////////////////////////

function tdomf_get_latest_submissions_post_list_line($p) {
  $submitter = get_post_meta($p->ID, TDOMF_KEY_NAME, true);
    if($submitter == false || empty($submitter)) {
      return "<li>".sprintf(__("<a href=\"%s\">\"%s\"</a>","tdomf"),get_permalink($p->ID),$p->post_title)."</li>";
    } 
    return "<li>".sprintf(__("<a href=\"%s\">\"%s\"</a> submitted by %s","tdomf"),get_permalink($p->ID),$p->post_title,$submitter)."</li>";
}

function tdomf_theme_widgets_init() {
  if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
    return;
  
  function tdomf_theme_widget_form($args,$params) {
    extract($args);
    $form_id = $params;
    if(!tdomf_form_exists($form_id)) {
       $form_id = tdomf_get_first_form_id();
    }
    echo $before_widget;
    echo $before_title;
    echo tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id);
    echo $after_title;
    tdomf_the_form($form_id);
    echo "<br/><br/>\n";
    echo $after_widget;
  }
  $form_ids = tdomf_get_form_ids();
  foreach($form_ids as $form_id) {
    register_sidebar_widget("TDOMF Form " . $form_id->form_id, 'tdomf_theme_widget_form', $form_id->form_id);
  }
  
  function tdomf_theme_widget_admin($args) {
    if(current_user_can('manage_options') || current_user_can('edit_others_posts')) {
      extract($args);

      $errors = tdomf_get_error_messages();
      if(trim($errors) != "") {
        echo $before_widget;
        echo $before_title.__("TDOMF Errors","tdomf").$after_title;
        echo "<p>$errors</p>";
        echo $after_widget;
      }

      $options = get_option('tdomf_theme_widget_admin');
      if($options == false) {
        $log = 5;
        $mod = 5;
      } else {
        $log = $options['log'];
        $mod = $options['mod'];
      }

      if($log > 0) {
        echo $before_widget;
        echo $before_title;
        _e('TDOMF Log', 'tdomf'); 
        if(current_user_can('manage_options')) { 
          echo "<a href=\"".get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_log_menu\" title=\"Full Log...\">&raquo;</a>";
        }
        echo $after_title;
        echo '<p>'.tdomf_get_log($log).'</p>';
        echo $after_widget;
      }
      
      if($mod > 0) {
        $posts = tdomf_get_unmoderated_posts(0,$mod);
        if(!empty($posts)) {
          echo $before_widget;
          echo $before_title;
          printf(__('Awaiting Approval (%d)', 'tdomf'),tdomf_get_unmoderated_posts_count());
          if(current_user_can('edit_others_posts')) { 
            echo "<a href=\"".get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&f=0\" title=\"Moderate Submissions...\">&raquo;</a>";
          }
          echo $after_title;
          echo '<ul>';
          foreach($posts as $p) {
            echo tdomf_get_post_list_line($p);
          }
          echo '</ul>';
          echo $after_widget;
        }
      }
      
      
      if(get_option(TDOMF_OPTION_SPAM)) {
         $spam_count = tdomf_get_spam_posts_count(); 
         if($spam_count > 0) {
             echo $before_widget;
             echo $before_title;
             printf(__('Spam Queue (%d)', 'tdomf'),$spam_count);
             if(current_user_can('edit_others_posts')) {
                echo '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&f=3" title="'.__("Moderate Spam...","tdomf").'">&raquo;</a>';
             }
             echo $after_title;
             echo $after_widget;
         }
      }
      
      echo $before_widget;
      echo $before_title;
      _e('TDOMF Admin Links', 'tdomf'); 
      echo $after_title;
      echo "<ul>";
      if($mod <= 0 && tdomf_is_moderation_in_use()) {
        echo "<li>";
        printf(__("<a href=\"%s\">Moderate (%d)</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_mod_posts_menu&f=0",tdomf_get_unmoderated_posts_count());
        echo "</li>";
      }
      echo "<li>";
      printf(__("<a href=\"%s\">Configure</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu");
      echo "</li>";
      echo "<li>";
      printf(__("<a href=\"%s\">Manage</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_manage_menu");
      echo "</li>";
      echo "<li>";
      printf(__("<a href=\"%s\">Create Form</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_form_menu");
      echo "</li>";
      if($log <= 0) {
        echo "<li>";
        printf(__("<a href=\"%s\">Log</a>","tdomf"),get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_log_menu");
        echo "</li>";
      }
      echo "</ul>";
      echo $after_widget;
    }
  }

  function tdomf_theme_widget($args) {
    extract($args);
    $options = get_option('tdomf_theme_widget');
    if($options == false) {
      $title = 'Recent Submissions';
      $mod = 5;
    } else {
      $title = $options['title'];
      $mod = $options['mod'];
    }
    
    $posts = tdomf_get_published_posts(0,$mod);
    if(!empty($posts)) {
      echo $before_widget;
      if($title) {
        echo $before_title;
        echo $title;
        echo $after_title;
      }
      echo "<ul>";
      foreach($posts as $p) { 
         #echo "<li><a href=\"".get_permalink($p->ID)."\">".$p->post_title."</a> from ".get_post_meta($p->ID, TDOMF_KEY_NAME, true)."</li>";
         echo tdomf_get_latest_submissions_post_list_line($p);
      }
      echo "</ul>";
      echo $after_widget;
    }
  }
  
  function tdomf_theme_widget_control() {
    $options = get_option('tdomf_theme_widget');
  
    if ( $_POST['tdomf-mod'] ) {
      $newoptions['title'] = htmlentities(strip_tags($_POST['tdomf-title']));
      $newoptions['mod'] = intval($_POST['tdomf-mod']);
        if ( $options != $newoptions ) {
          $options = $newoptions;
          update_option('tdomf_theme_widget', $options);
        }
    }
    
    if($options == false) {
      $title = 'Recent Submissions';
      $mod = 5;
    } else {
      $title = $options['title'];
      $mod = $options['mod'];
    }
  
  ?>
  <div>
  
  <label for="tdomf-title">
  Title
  <input type="text" id="tdomf-title" name="tdomf-title" value="<?php echo htmlentities($title,ENT_QUOTES); ?>" size="20" />
  </label>
  <br/><br/>
  <label for="tdomf-mod">
  Number of posts to show:
  <input type="text" id="tdomf-mod" name="tdomf-mod" value="<?php echo htmlentities($mod,ENT_QUOTES); ?>" size="2" />
  </label>
  
  </div>
  <?php
  }
  
  function tdomf_theme_widget_admin_control() {
    $options = get_option('tdomf_theme_widget_admin');
  
    if ( $_POST['tdomf-admin-info-log'] ) {
      $newoptions['log'] = intval($_POST['tdomf-admin-info-log']);
      $newoptions['mod'] = intval($_POST['tdomf-admin-info-mod']);
        if ( $options != $newoptions ) {
          $options = $newoptions;
          update_option('tdomf_theme_widget_admin', $options);
        }
    }
    
    if($options == false) {
      $log = 5;
      $mod = 5;
    } else {
      $log = $options['log'];
      $mod = $options['mod'];
    }  
  ?>
  <div>
  
  <label for="tdomf-admin-info-log">
  Number of log lines to show:
  <input type="text" id="tdomf-admin-info-log" name="tdomf-admin-info-log" value="<?php echo htmlentities($log,ENT_QUOTES); ?>" size="2" />
  </label>
  <br/><br/>
  <label for="tdomf-admin-info-mod">
  Number of posts to show:
  <input type="text" id="tdomf-admin-info-mod" name="tdomf-admin-info-mod" value="<?php echo htmlentities($mod,ENT_QUOTES); ?>" size="2" />
  </label>
  
  </div>
  <?php
  }

  function tdomf_theme_widget_submitters_control() {
    $options = get_option('tdomf_theme_widget_submitters');
  
    if ( $_POST['tdomf_theme_widget_submitters-title'] ) {
      $newoptions['title'] = $_POST['tdomf_theme_widget_submitters-title'];
      $newoptions['count'] = intval($_POST['tdomf_theme_widget_submitters-count']);
      $newoptions['use_subs'] = isset($_POST['tdomf_theme_widget_submitters-use_subs']);
      $newoptions['use_reg'] = isset($_POST['tdomf_theme_widget_submitters-use_reg']);
      $newoptions['use_link'] = isset($_POST['tdomf_theme_widget_submitters-use_link']);
      $newoptions['avatar'] = isset($_POST['tdomf_theme_widget_submitters-avatar']);
      $newoptions['avatar_size'] = intval($_POST['tdomf_theme_widget_submitters-avatar_size']);
      $newoptions['avatar_default'] = $_POST['tdomf_theme_widget_submitters-avatar_default'];
        if ( $options != $newoptions ) {
          $options = $newoptions;
          update_option('tdomf_theme_widget_submitters', $options);
        }
    }
    
    $title = 'Top Submitters';
    $count = 5;
    $use_subs = true;
    $use_reg = true;
    $use_link = false;
    $avatar = false;
    $avatar_size = 25;
    $avatar_default = "";        
    if($options != false) {
      $title = $options['title'];
      $count = $options['count'];
      $use_subs = $options['use_subs'];
      $use_reg = $options['use_reg'];
      $use_link = $options['use_link'];
      if(isset($options['avatar'])) {
          $avatar = $options['avatar'];
          $avatar_size = $options['avatar_size'];
          $avatar_default = $options['avatar_default'];
      }
    }
  ?>
  <div>
  
  <label for="tdomf_theme_widget_submitters-title">
  <?php _e("Title","tdomf"); ?>
  <input type="text" id="tdomf_theme_widget_submitters-title" name="tdomf_theme_widget_submitters-title" value="<?php echo htmlentities($title,ENT_QUOTES); ?>" size="20" />
  </label>
  <br/><br/>
  <?php _e("How many to show:","tdomf"); ?>
  <input type="text" id="tdomf_theme_widget_submitters-count" name="tdomf_theme_widget_submitters-count" value="<?php echo htmlentities($count,ENT_QUOTES); ?>" size="2" />
  </label>
  <br/><br/>
  <?php _e("Include Unregistered Users:","tdomf"); ?>
  <input type="checkbox" id="tdomf_theme_widget_submitters-use_subs" name="tdomf_theme_widget_submitters-use_subs" <?php if($use_subs) { ?>checked<?php } ?> />
  </label>
  <br/><br/>
  <?php _e("Include Registered Users:","tdomf"); ?>
  <input type="checkbox" id="tdomf_theme_widget_submitters-use_reg" name="tdomf_theme_widget_submitters-use_reg" <?php if($use_reg) { ?>checked<?php } ?> />
  </label>
  <br/><br/>
  <?php _e("Use link to author posts before profile URL","tdomf"); ?>
  <input type="checkbox" id="tdomf_theme_widget_submitters-use_link" name="tdomf_theme_widget_submitters-use_link" <?php if($use_link) { ?>checked<?php } ?> />
  </label>

  <?php if(function_exists('get_avatar')) { ?>
  <br/><br/>
  <?php _e("Enable Avatars","tdomf"); ?>
  <input type="checkbox" id="tdomf_theme_widget_submitters-avatar" name="tdomf_theme_widget_submitters-avatar" <?php if($avatar) { ?>checked<?php } ?> />
  </label>
  <br/><br/>
  <?php _e("Default Size:","tdomf"); ?>
  <input type="text" id="tdomf_theme_widget_submitters-avatar_size" name="tdomf_theme_widget_submitters-avatar_size" value="<?php echo htmlentities($avatar_size,ENT_QUOTES); ?>" size="3" />
  </label>
  <br/><br/>
  <?php _e("Default URL (can leave blank):","tdomf"); ?>
  <input type="text" id="tdomf_theme_widget_submitters-avatar_default" name="tdomf_theme_widget_submitters-avatar_default" value="<?php echo htmlentities($avatar_default,ENT_QUOTES); ?>" size="20" />
  </label>
  <?php } ?>
  
  </div>
  <?php
  }
  
  function tdomf_theme_widget_submitters($args) {
    extract($args);
    $options = get_option('tdomf_theme_widget_submitters');

   $title = 'Top Submitters';
   $count = 5;
   $use_subs = true;
   $use_reg = true;
   $use_link = false;
   $avatar = false;
   $avatar_size = 25;
   $avatar_default = "";        
   if($options != false) {
      $title = $options['title'];
      $count = $options['count'];
      $use_subs = $options['use_subs'];
      $use_reg = $options['use_reg'];
      $use_link = $options['use_link'];
       if(isset($options['avatar'])) {
          $avatar = $options['avatar'];
          $avatar_size = $options['avatar_size'];
          $avatar_default = $options['avatar_default'];
      }     
    }
    
    $posts = tdomf_get_published_posts();
    $users = array();
    $subs = array();
    foreach($posts as $p) {
        if(($user_id = get_post_meta($p->ID,TDOMF_KEY_USER_ID,true))) {
            if($use_reg) {
                if(get_usermeta($user_id,TDOMF_KEY_STATUS) != TDOMF_USER_STATUS_BANNED) {
                    $user = get_userdata($user_id);
                    if(isset($user)) {
                        $id = intval($user_id);
                        if(!isset($users[$id])) { 
                            $u = array();
                            $u['web'] = trim($user->user_url);
                            if(strlen($u['web']) < 8 || strpos($u['web'], "http://", 0) !== 0 ) {
                                $u['web'] = false;
                            }
                            if(function_exists('get_avatar')) { 
                                $u['avatar'] = get_avatar($id, $avatar_size, $avatar_default);
                            }
                            $u['name'] = $user->display_name;
                            $u['link'] = get_author_posts_url($user_id);
                            $u['count'] = 1;
                            $users[$id] = $u;
                        } else {
                            $users[$id]['count']++;
                        }
                    }
                }
            }
        } else {
            if($use_subs) {
                // all theses are optional, but in terms of priority: 
                // 1. email, 2. name, 3. web (but not that both name and web can be
                // faked by another submitter
                
                $sub = array();
                
                $sub['count'] = 1;
                
                $sub['email'] = false;
                $sub['avatar'] = "";
                if(get_post_meta($p->ID,TDOMF_KEY_EMAIL)) {
                    // use lowercase to avoid case-sensitive misses
                    $sub['email'] = strtolower(trim(get_post_meta($p->ID,TDOMF_KEY_EMAIL,true)));
                    if(function_exists('get_avatar')) { 
                        $sub['avatar'] = get_avatar($sub['email'], $avatar_size, $avatar_default);
                    }
                }
                
                $sub['name'] = false;
                if(get_post_meta($p->ID,TDOMF_KEY_NAME)) {
                    // keep case 
                    $sub['name'] = trim(get_post_meta($p->ID,TDOMF_KEY_NAME,true));
                }
                
                $sub['web'] = false;
                if(get_post_meta($p->ID,TDOMF_KEY_WEB)) {
                    // use lowercase to avoid case-sensitive misses
                    $sub['web'] = strtolower(get_post_meta($p->ID,TDOMF_KEY_WEB,true));
                }
                
                $hit = false;            
                foreach($subs as $s) {
                    if($sub['email'] != false && $s['email'] != false && $s['email'] == $sub['email']) {
                        // we scored a hit
                        $s['name'] = $sub['name'];
                        $s['web'] = $sub['web'];
                        $s['count']++;
                        $hit = true;
                        break;
                    }
                    
                    if($sub['name'] != false && $s['name'] != false && $s['name'] == $sub['name']) {
                        // we scored a hit
    
                        // no email set so this looks good!
                        if($s['email'] == false && $sub['email'] == false) {
                            $s['web'] = $sub['web'];
                            $s['count']++;
                        }
                        $hit = true;
                        break;
                    }
                    
                    if($sub['web'] != false && $s['web'] != false && $s['web'] == $sub['web']) {
                        // we scored a hit
    
                        // no email set so this looks good!
                        if($s['email'] == false && $sub['email'] == false && $s['name'] == false && $sub['name'] == false) {
                            $s['count']++;
                        }
                        $hit = true;
                        break;
                    }
                }
                if(!$hit) {
                    $subs[] = $sub;
                }
            }
        }
    }
    
    $list = array();
    if(!empty($users) && !empty($subs)) {
      $list = array_merge($users, $subs);
   } else if(!empty($users)) {
      $list = $users;
   } else if(!empty($subs)) {
      $list = $subs;
   } 
    
   if(!empty($list)) {
       if(!function_exists("tdomf_theme_widget_submitters_cmp")){
         function tdomf_theme_widget_submitters_cmp($a,$b) {
            if($a['count'] == $b['count']) { return 0; }
            return ($a['count'] > $b['count']) ? -1 : +1;
         }
       }
       usort($list,"tdomf_theme_widget_submitters_cmp");
       $list = array_slice($list,0,$count);
   }
   
    if(!empty($list)) {
      echo $before_widget;
      if($title) {
        echo $before_title;
        echo $title;
        echo $after_title;
      }
      echo "<ul>";
      foreach($list as $l) {
          echo "<li>";
          if($use_link) {
              if(isset($l['link'])) {
                  echo "<a href=\"".$l['link']."\">";
              } else if($l['web'] != false) {
                  echo "<a href=\"".$l['web']."\">";
              }
          } else {
              if($l['web'] != false) {
                  echo "<a href=\"".$l['web']."\">";
              } else if(isset($l['link'])) {
                  echo "<a href=\"".$l['link']."\">";
              }
          }
          if($avatar && isset($l['avatar'])) { echo " ".$l['avatar']." "; }
          echo $l['name']." (".$l['count'].")";
          if(isset($l['link']) || ($l['web'] != false)) {
              echo "</a>";
          }
          echo "</li>";
      }
      echo "</ul>";
      echo $after_widget;
    }
  }
  
  register_sidebar_widget('TDOMF Admin Info', 'tdomf_theme_widget_admin');
  register_widget_control('TDOMF Admin Info', 'tdomf_theme_widget_admin_control', 220, 100);
  register_sidebar_widget('TDOMF Recent Submissions', 'tdomf_theme_widget');
  register_widget_control('TDOMF Recent Submissions', 'tdomf_theme_widget_control', 220, 100);
  register_sidebar_widget('TDOMF Top Submitters', 'tdomf_theme_widget_submitters');
  register_widget_control('TDOMF Top Submitters', 'tdomf_theme_widget_submitters_control');
}
add_action('plugins_loaded', 'tdomf_theme_widgets_init');
?>