<?php

  // Load up Wordpress
  //
  $wp_load = realpath("../../../wp-load.php");
  if(!file_exists($wp_load)) {
      $wp_config = realpath("../../../wp-config.php");
      if (!file_exists($wp_config)) {
          exit("Can't find wp-config.php or wp-load.php");
      } else {
          require_once($wp_config);
      }
  } else {
      require_once($wp_load);
  }
  global $wpdb, $current_user;
  
  // Only show PHP info if this is being called from an adminstrator account
  // - do not want to allow everyone to see it!
  if(current_user_can('manage_options')) {
      phpinfo();
  }
?>
