<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/* BIG TODO: Optimisations: use cache to prevent multiple queries on the same data */
  

function tdomf_db_create_tables() {
  global $wpdb,$wp_roles, $table_prefix;
  $table_form_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $table_widget_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
  $table_session_name = $wpdb->prefix . TDOMF_DB_TABLE_SESSIONS;
  $table_edit_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;

  if($wpdb->get_var("show tables like '$table_form_name'") != $table_form_name) {
    
     tdomf_log_message("$table_form_name does not exist. Will create it now...");
    
     $sql = "CREATE TABLE " . $table_form_name . " (
               form_id      bigint(20)   NOT NULL auto_increment,
               form_name    varchar(255) default NULL,
               form_options longtext,
               PRIMARY KEY  (form_id)
             );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      $error = $wpdb->last_error;
      
      // Now double check the table is created!
      //
      if($wpdb->get_var("show tables like '$table_form_name'") == $table_form_name) {
        
        if(get_option(TDOMF_VERSION_CURRENT) != false) {
          // we are importing...
          tdomf_log_message("$table_form_name created successfully. Importing default form now...",TDOMF_LOG_GOOD);
          
          // New form options
          //
          $form_name = $wpdb->escape(__('Default Form','tdomf'));
          //
          $form_options = array( TDOMF_OPTION_DESCRIPTION => __('Imported from default form','tdomf'),
                                 TDOMF_OPTION_CREATEDPAGES => false,
                                 TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS => true,
                                 TDOMF_OPTION_WIDGET_INSTANCES => 10,
                                 TDOMF_OPTION_ALLOW_PUBLISH => true,
                                 TDOMF_OPTION_PUBLISH_NO_MOD => true);

          //
          // Import from existing options
          //
          $form_options[TDOMF_ACCESS_ROLES] = get_option(TDOMF_ACCESS_ROLES);
          $form_options[TDOMF_NOTIFY_ROLES] = get_option(TDOMF_NOTIFY_ROLES);
          $form_options[TDOMF_DEFAULT_CATEGORY] = get_option(TDOMF_DEFAULT_CATEGORY);
          $form_options[TDOMF_OPTION_MODERATION] = get_option(TDOMF_OPTION_MODERATION);
          $form_options[TDOMF_OPTION_ALLOW_EVERYONE] = get_option(TDOMF_OPTION_ALLOW_EVERYONE);
          $form_options[TDOMF_OPTION_PREVIEW] = get_option(TDOMF_OPTION_PREVIEW);
          $form_options[TDOMF_OPTION_FROM_EMAIL] = get_option(TDOMF_OPTION_FROM_EMAIL);
          $form_options[TDOMF_OPTION_FORM_ORDER] = get_option(TDOMF_OPTION_FORM_ORDER);
          
          // Prepare for SQL 
          $form_options = maybe_serialize($form_options);
          
          // Now insert default form into table!
          $sql = "INSERT INTO $table_form_name" .
                "(form_name, form_options) " .
                "VALUES ('$form_name','".$wpdb->escape($form_options)."')";
          if($wpdb->query( $sql )) {
            
            tdomf_log_message("default form imported successfully into db table $table_form_name!",TDOMF_LOG_GOOD);
            
            //
            // Everything went well so we can get rid of the old options now!
            //
            delete_option(TDOMF_ACCESS_ROLES);
            delete_option(TDOMF_NOTIFY_ROLES);
            delete_option(TDOMF_DEFAULT_CATEGORY);
            delete_option(TDOMF_OPTION_MODERATION);
            delete_option(TDOMF_OPTION_ALLOW_EVERYONE);
            delete_option(TDOMF_OPTION_PREVIEW);
            delete_option(TDOMF_OPTION_FROM_EMAIL);
            delete_option(TDOMF_OPTION_FORM_ORDER);
  
            // Update capablities!
            //
            
            tdomf_log_message("Attempting to update '".TDOMF_CAPABILITY_CAN_SEE_FORM."' user capability to '".TDOMF_CAPABILITY_CAN_SEE_FORM."_1' ...",TDOMF_LOG_GOOD);
            
            if(!isset($wp_roles)) {
               $wp_roles = new WP_Roles();
            }
            $roles = $wp_roles->role_objects;
            foreach($roles as $role) {
              if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM])){
                 $role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM);
                 $role->add_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_1');
              }
            }
            
            // We could attempt to update posts... but we're not going to.
            
          } else {
            tdomf_log_message("Failed to import default form into $table_form_name!",TDOMF_LOG_ERROR);
          }
        } else {
          tdomf_log_message("$table_form_name created successfully. Creating default form now...",TDOMF_LOG_GOOD);
          tdomf_create_form('Default Form');
        }
      } else {
         tdomf_log_message("Can't find db table $table_form_name! Table not created.",TDOMF_LOG_ERROR);
      }
  }
  
  
  if($wpdb->get_var("show tables like '$table_widget_name'") != $table_widget_name) {
    
    tdomf_log_message("$table_widget_name does not exist. Will create it now...");
    
     $sql = "CREATE TABLE " . $table_widget_name . " (
               id             bigint(20)   NOT NULL auto_increment,
               form_id        bigint(20)   NOT NULL default '0',
               widget_key     varchar(255) default NULL,
               widget_value   longtext,
               PRIMARY KEY    (id),
               KEY form_id    (form_id),
               KEY widget_key (widget_key)
             );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      $error = $wpdb->last_error;
      
      if($wpdb->get_var("show tables like '$table_widget_name'") == $table_widget_name) {
        
        // default form id
        $form_id = 1;

        // don't import if table prefix is "tdomf_"...
        //
        if(get_option(TDOMF_VERSION_CURRENT) != false && $table_prefix != "tdomf_") {
          
          // we are importing...
          tdomf_log_message("$table_widget_name created successfully. Importing widget settings for default form...");
        
          // non-widget-able options
          $non_widget_options = array( TDOMF_DEFAULT_AUTHOR, 
                                       TDOMF_AUTO_FIX_AUTHOR,
                                       TDOMF_BANNED_IPS,
                                       TDOMF_VERSION_CURRENT,
                                       TDOMF_OPTION_AUTHOR_THEME_HACK,
                                       TDOMF_OPTION_ADD_SUBMITTER,
                                       TDOMF_STAT_SUBMITTED,
                                       TDOMF_OPTION_DISABLE_ERROR_MESSAGES,
                                       TDOMF_OPTION_EXTRA_LOG_MESSAGES,
                                       TDOMF_OPTION_YOUR_SUBMISSIONS,
                                       TDOMF_OPTION_CREATEDUSERS,
                                       TDOMF_LOG);
          
          // scan for widget options
          $alloptions = wp_load_alloptions();
          foreach($alloptions as $id => $val) {
            if(!in_array($id,$non_widget_options) && preg_match('#^tdomf_.+#',$id)) {
              
              $widget_key = $wpdb->escape($id);
              $widget_value = $wpdb->escape(maybe_serialize(get_option($id)));
              
              // Now insert into widget table
              $sql = "INSERT INTO $table_widget_name" .
                     "(form_id, widget_key, widget_value) " .
                     "VALUES ('$form_id','$widget_key','$widget_value')";
              if($wpdb->query( $sql )) {
                 tdomf_log_message("Imported widget option $id into $table_widget_name!",TDOMF_LOG_GOOD);
                 delete_option($id);
              } else {
                 tdomf_log_message("Failed to import widget option $id into db table $table_widget_name!",TDOMF_LOG_ERROR);
              }
            }
          }
        } else {
          tdomf_log_message("$table_widget_name created successfully.");
        }
    } else {
      tdomf_log_message("Can't find db table $table_widget_name! Table not created.",TDOMF_LOG_ERROR);
    }
  }
  
  if($wpdb->get_var("show tables like '$table_session_name'") != $table_session_name 
     && get_option(TDOMF_OPTION_FORM_DATA_METHOD) == "db" ) {
    
     tdomf_log_message("$table_session_name does not exist. Will create it now...");
    
     $sql = "CREATE TABLE " . $table_session_name . " (
               session_key       varchar(255) NOT NULL,
               session_data      longtext,
               session_timestamp int(11),
               PRIMARY KEY  (session_key)
             );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      $error = $wpdb->last_error;
      
      if($wpdb->get_var("show tables like '$table_session_name'") == $table_session_name) {
          tdomf_log_message("$table_session_name created successfully.",TDOMF_LOG_GOOD);
      } else {
          tdomf_log_message("Can't find db table $table_session_name! Table not created.",TDOMF_LOG_ERROR);
      }
  }
     
  if($wpdb->get_var("show tables like '$table_edit_name'") != $table_edit_name ) {
      
      tdomf_log_message("$table_edit_name does not exist. Will create it now...");
      
      $sql = "CREATE TABLE " . $table_edit_name . " (
               edit_id              bigint(20)   NOT NULL auto_increment,
               post_id              bigint(20)   NOT NULL default '0',
               form_id              bigint(20)   NOT NULL default '0',
               date                 datetime     NOT NULL default '0000-00-00 00:00:00',
               date_gmt             datetime     NOT NULL default '0000-00-00 00:00:00',
               revision_id          int(11)      NOT NULL default '0',
               current_revision_id  int(11)      NOT NULL default '0',
               user_id              bigint(20)   NOT NULL default '0',
               ip                   varchar(100) NOT NULL default '0',
               state                varchar(20)  NOT NULL default 'unapproved',
               data                 longtext,
               PRIMARY KEY          (edit_id),
               KEY post_id          (post_id),
               KEY form_id          (form_id)
             );";
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      $error = $wpdb->last_error;
      
      if($wpdb->get_var("show tables like '$table_edit_name'") == $table_edit_name) {
          tdomf_log_message("$table_edit_name created successfully.",TDOMF_LOG_GOOD);
      } else {
          tdomf_log_message("Can't find db table $table_edit_name! Table not created: SQL Error: $error",TDOMF_LOG_ERROR);
      }
  }
  
  return true;
}

function tdomf_db_delete_tables() {
  global $wpdb;
  
  $table_form_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $table_widget_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
  $table_session_name = $wpdb->prefix . TDOMF_DB_TABLE_SESSIONS;
  $table_edit_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  
  if($wpdb->get_var("show tables like '$table_form_name'") == $table_form_name) {
      tdomf_log_message("Deleting db table $table_form_name...");
      $sql = "DROP TABLE IF EXISTS " . $table_form_name . ";";
      if($wpdb->query($sql)) {
        tdomf_log_message("Db table $table_form_name deleted!");
      }
  }
  if($wpdb->get_var("show tables like '$table_widget_name'") == $table_widget_name) {
      tdomf_log_message("Deleting db table $table_widget_name...");
      $sql = "DROP TABLE IF EXISTS " . $table_widget_name . ";";
      if($wpdb->query($sql)) {
        tdomf_log_message("Db table $table_widget_name deleted!");
      }
  }
  if($wpdb->get_var("show tables like '$table_session_name'") == $table_session_name) {
      tdomf_log_message("Deleting db table $table_session_name...");
      $sql = "DROP TABLE IF EXISTS " . $table_session_name . ";";
      if($wpdb->query($sql)) {
        tdomf_log_message("Db table $table_session_name deleted!");
      }
  }   
  if($wpdb->get_var("show tables like '$table_edit_name'") == $table_edit_name) {
      tdomf_log_message("Deleting db table $table_edit_name...");
      $sql = "DROP TABLE IF EXISTS " . $table_edit_name . ";";
      if($wpdb->query($sql)) {
        tdomf_log_message("Db table $table_edit_name deleted!");
      }
  }   
  return false;
}

function tdomf_get_sessions() {
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_SESSIONS;
  if($wpdb->get_var("show tables like '$table_name'") ==  $table_name) {
      tdomf_session_cleanup();
      $query = "SELECT * 
                FROM $table_name 
                ORDER BY session_key ASC";
      return $wpdb->get_results($query);
  } 
  return false;
}

function tdomf_session_start() {
   tdomf_session_cleanup();
   if(!isset($_COOKIE['tdomf_'.COOKIEHASH])) {
      #$session_key = tdomf_random_string(15);
      $session_key = uniqid(tdomf_random_string(3));
      return setcookie('tdomf_'.COOKIEHASH, $session_key, 0, COOKIEPATH, COOKIE_DOMAIN);
   }
   return true;
}

function tdomf_session_set($key=0,$data) {
  global $wpdb;
  
  // grab session key
  //
  if($key == 0 && !isset($_COOKIE['tdomf_'.COOKIEHASH])) {
     return false; 
  } else if($key == 0) {
     $key = $_COOKIE['tdomf_'.COOKIEHASH];
  }
 
  // session exists?
  //
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_SESSIONS;
  $query = "SELECT * 
            FROM $table_name 
            WHERE session_key = '" .$wpdb->escape($key)."'";
  $session = $wpdb->get_row( $query );

  if(!is_array($data)) {
    tdomf_log_message("Bad data in session, reseting to empty array!",TDOMF_LOG_ERROR);
    $data = array();
  }
  
  $data = maybe_serialize($data);   
  $ts = time();

  // if option doesn't exist - add
  //
  if($session == NULL) {
    $query = "INSERT INTO $table_name" .
             "(session_key, session_data, session_timestamp) " .
              "VALUES ('".$wpdb->escape($key)."',
                       '" .$wpdb->escape($data)."',
                       ".$wpdb->escape($ts).")";
    $retValue = $wpdb->query($query);
    return $retValue;
  } else {
    // if option does exist - check if it has changed
    //
    $current_data = maybe_unserialize($session->session_data);
    if($current_data != $data) {
        // it's changed! So update
      //
      $query = "UPDATE $table_name 
                SET session_data = '".$wpdb->escape($data)."',  
                    session_timestamp = $ts  
                WHERE session_key = '" .$wpdb->escape($key)."'";
      $retValue = $wpdb->query($query);
      return $retValue;
    }
  }
  return false;
}

function tdomf_session_get($key=0) {
  global $wpdb;
  
  // grab session key
  //
  if($key == 0 && !isset($_COOKIE['tdomf_'.COOKIEHASH])) {
     tdomf_log_message_extra("No cookie present");
     return false; 
  } else if($key == 0) {
     $key = $_COOKIE['tdomf_'.COOKIEHASH];
  }
   
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_SESSIONS;
  $query = "SELECT * 
            FROM $table_name 
            WHERE session_key = '" .$wpdb->escape($key)."'";
  $retValue = $wpdb->get_row( $query );
  if($retValue == null) {
      tdomf_log_message_extra("Cookie found but no session data! Deleting cookie key.",TDOMF_LOG_ERROR);
      // delete cookie (it's invalid)
      @setcookie ('tdomf_'.COOKIEHASH, "", time()-60000);
      return false;
  }
  return maybe_unserialize($retValue->session_data);
}

function tdomf_session_cleanup() {
   global $wpdb;
   $table_name = $wpdb->prefix . TDOMF_DB_TABLE_SESSIONS;
   $cutoff = time() - (60*60*24);
   $query = "DELETE FROM $table_name 
             WHERE session_timestamp <= " . $cutoff;
   return $wpdb->query( $query );
}

function tdomf_set_option_widget($key,$value,$form_id = 1) {
  global $wpdb;

  // check if option exists!
  //
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
  $query = "SELECT widget_value 
            FROM $table_name 
            WHERE widget_key = '" .$wpdb->escape($key)."'
                  AND form_id = '".$wpdb->escape($form_id)."'";
  $option = $wpdb->get_row( $query );

  // if option doesn't exist - add
  //
  if($option == NULL) {
    $value = maybe_serialize($value);    
    $query = "INSERT INTO $table_name" .
             "(form_id, widget_key, widget_value) " .
              "VALUES ('".$wpdb->escape($form_id)."',
                       '" .$wpdb->escape($key)."',
                       '".$wpdb->escape($value)."')";
    return $wpdb->query($query);
  } else {
    // if option does exist - check if it has changed
    //
    $current_value = maybe_unserialize($option->widget_value);
    if($current_value != $value) {
      $value = maybe_serialize($value);
      // it's changed! So update
      //
      $query = "UPDATE $table_name 
                SET widget_value = '".$wpdb->escape($value)."' 
                 WHERE widget_key = '" .$wpdb->escape($key)."'
                       AND form_id = '".$wpdb->escape($form_id)."'";
      return $wpdb->query($query);
    }
  }
  return false;
}

function tdomf_set_option_form($key,$value,$form_id = 1) {
  #tdomf_log_message("tdomf_set_option_form for $key");
  if($key == TDOMF_OPTION_NAME) {
    global $wpdb;
    $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
    $query = "UPDATE $table_name
              SET form_name = '".$wpdb->escape($value)."'
              WHERE form_id = '".$wpdb->escape($form_id)."'";
    return $wpdb->query($query);
  } else {
    #tdomf_log_message("tdomf_set_option_form: value: <pre>" . htmlentities($value,ENT_COMPAT,get_bloginfo('charset')) . "</pre>");
    $options = array( $key => $value);
    #tdomf_log_message("tdomf_set_option_form: value: <pre>" . htmlentities(var_export($options,true),ENT_COMPAT,get_bloginfo('charset')) . "</pre>");
    return tdomf_set_options_form($options,$form_id);
  }
}

function tdomf_delete_widgets($form_id) {
  if(tdomf_form_exists($form_id))
  {
    global $wpdb;
    $table_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
    $query = "DELETE FROM $table_name
              WHERE form_id = '".$wpdb->escape($form_id)."'";
    $wpdb->query($query);
  }
}

function tdomf_delete_form($form_id) {
  if(tdomf_form_exists($form_id))
  {
    global $wpdb,$wp_roles;

    // Delete pages created with this form
    //
    $pages = tdomf_get_option_form(TDOMF_OPTION_CREATEDPAGES,$form_id);
    if($pages != false) {
       foreach($pages as $page_id) {
          if(get_permalink($page_id) != false) {
                wp_delete_post($page_id);
          }
        }
    }
    
    // Delete form options
    //
    $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
    $query = "DELETE FROM $table_name
              WHERE form_id = '".$wpdb->escape($form_id)."'";
    $wpdb->query($query);
    
    // Delete widget options
    //
    $table_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
    $query = "DELETE FROM $table_name
              WHERE form_id = '".$wpdb->escape($form_id)."'";
    $wpdb->query($query);
    
    // Remove capablitiies from roles
    //
    $roles = $wp_roles->role_objects;
    foreach($roles as $role) {
     if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])){
       $role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id);
     }
    }
    
    return true;
  }
  return false;
}

function tdomf_create_form($form_name = '',$options = array()) {
  global $wpdb,$wp_roles;
  $defaults = array( TDOMF_OPTION_DESCRIPTION => '',
                     TDOMF_OPTION_CREATEDPAGES => false,
                     TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS => true,
                     TDOMF_ACCESS_ROLES => false,
                     TDOMF_NOTIFY_ROLES => false,
                     TDOMF_DEFAULT_CATEGORY => 0,
                     TDOMF_OPTION_MODERATION => true,
                     TDOMF_OPTION_ALLOW_EVERYONE => true,
                     TDOMF_OPTION_PREVIEW => true,
                     TDOMF_OPTION_FROM_EMAIL => '',
                     TDOMF_OPTION_FORM_ORDER => false,
                     TDOMF_OPTION_WIDGET_INSTANCES => 10,
                     TDOMF_OPTION_ALLOW_PUBLISH => true,
                     TDOMF_OPTION_PUBLISH_NO_MOD => true);
  $options = wp_parse_args($options,$defaults);
  $options = maybe_serialize($options);
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $sql = "INSERT INTO $table_name " .
         "(form_name, form_options) " .
         "VALUES ('$form_name','".$wpdb->escape($options)."')";
  $result = $wpdb->query( $sql );
  return $wpdb->insert_id;
}

function tdomf_create_edit($post_id,$form_id,$revision_id=0,$current_revision_id=0,$edit_user_id=0,$edit_user_ip=0,$edit_state='unapproved',$edit_data=array()) {
  global $wpdb;
  $date = current_time('mysql');
  $date_gmt = get_gmt_from_date($date);
  $edit_data = maybe_serialize($edit_data);
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;
  $sql = "INSERT INTO $table_name " .
         "(post_id, form_id, date, date_gmt, revision_id, current_revision_id, user_id, ip, state, data) " .
         "VALUES ('$post_id','$form_id','$date','$date_gmt','$revision_id','$current_revision_id','$edit_user_id','$edit_user_ip','".$wpdb->escape($edit_state)."','".$wpdb->escape($edit_data)."')";
  $result = $wpdb->query( $sql );
  $error = $wpdb->last_error;
  
  if($wpdb->insert_id > 0) {
      $edit = array( "post_id" => $post_id,
                     "form_id" => $form_id,
                     "date" => $date,
                     "date_gmt" => $date_gmt,
                     "revision_id" => $revision_id,
                     "user_id" => $edit_user_id,
                     "ip" => $edit_user_ip,
                     "state" => $edit_state,
                     "data" => maybe_unserialize($edit_data) );
      $key = "tdomf_edit_" . $wpdb->insert_id;
      wp_cache_set($key,$edit);
  } else {
      tdomf_log_message("Error attempting to copy in edit data to db. Last SQL Error: $error",TDOMF_LOG_ERROR);
  }
  
  return $wpdb->insert_id;
}

function tdomf_delete_edits($edit_ids) {
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;
  $query  = "DELETE FROM $table_name ";
  $query .= "WHERE edit_id IN (".implode(",",$edit_ids).")";
  foreach($edit_ids as $edit_id) {
     $key = "tdomf_edit_" . $edit_id;            
     wp_cache_delete($key);
  }
  $returnVal = $wpdb->query($query);
  return $returnVal;
}

function tdomf_get_edits($args) {
    global $wpdb;
    
    $defaults = array('post_id' => false,
                      'limit' => false,
                      'sort' => 'DESC',
                      'state' => false,
                      'count' => false,
                      'unique_post_ids' => false,
                      'offset' => false,
                      'ip' => false,
                      'user_id' => false,
                      'and_cond' => true,
                      'time_diff' => false,
                      'older_than' => false,
                      'form_id' => false,
                      'revision_id' => false);
    $args = wp_parse_args($args, $defaults);
    extract($args);
    
    #if(!isset($post_id) || !is_int($post_id)) {
    #    return false;
    #}
    
    if($sort != false && $sort != 'DESC' && $sort != 'ASC') {
        return false;
    }
    
    $table_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;
    
    # select
    
    $query = "SELECT ";
    if($unique_post_ids) {
        $query .= "DISTINCT(post_id) ";
    } else if($count) { # can't do distinct and count together
        $query .= "COUNT(edit_id) ";
    } else {
        $query .= "* ";        
    }
    
    # from
    
    $query .= "FROM $table_name ";
    
    # where conditions

    $where_conditions = "";
    
    if($post_id != false && $post_id != 0) { 
       $where_conditions .= "post_id = '".$wpdb->escape($post_id)."' ";
    } 
    
    if($state != false && !empty($state)) {
        if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "state = '".$wpdb->escape($state)."' ";
    } 
    
    if($ip != false && !empty($ip)) {
        if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "ip = '".$wpdb->escape($ip)."' ";
    } 
    
    if($user_id != false && !empty($user_id)) {
        if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "user_id = '".$wpdb->escape($user_id)."' ";
    }
    
    if($form_id != false && !empty($form_id)) {
        if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "form_id = '".intval($form_id)."' ";
    }
    
    if($revision_id != false && !empty($revision_id)) {
        if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "revision_id = '".intval($revision_id)."' ";
    }
    
    if($time_diff != false && !empty($time_diff)) {
         if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "date > '".$time_diff."' ";
    } 

    if($older_than != false && !empty($older_than)) {
         if(!empty($where_conditions)) {
            if($and_cond) {
                $where_conditions .= ' AND ';
            } else {
                $where_conditions .= ' OR ';
            }
        }
        $where_conditions .= "date < '".$older_than."' ";
    }
    
    if(!empty($where_conditions)) {
        $query .= "WHERE ".$where_conditions;
    }
    
    # order by X limit Y
    
    if(!$count) {
        if($sort) {
           $query .= 'ORDER BY ';
           if($unique_post_ids) {
               $query .= 'post_id ';
           } else {
               $query .= 'edit_id ';
           }
           $query .= $wpdb->escape($sort).' ';
        }
    }

    if($limit) {
           $query .= 'LIMIT '.intval($limit).' ';
    } 

    if($offset) {
           $query .= 'OFFSET '.intval($offset).' ';
    } 

    #tdomf_log_message( $query );
    #$wpdb->show_errors = true;
    if($count) {
       if($unique_post_ids) {
           $edits = $wpdb->get_results( $query );
           $edits = count($edits);
       } else {
           $edits = intval($wpdb->get_var( $query ));
       }
    } else {
       $edits = $wpdb->get_results( $query );
    }
    return $edits;
}

function tdomf_get_edit($edit_id) {
  global $wpdb;
  $key = "tdomf_edit_" . $edit_id;
  $edit_cache = wp_cache_get($key);
  if($edit_cache == false || !isset($edit_cache['post_id'])) {
      $table_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;
      $query = "SELECT * 
                FROM $table_name 
                WHERE edit_id = '" .$wpdb->escape($edit_id)."'";
      $edit = $wpdb->get_row( $query );
      if($edit != NULL) {
         $edit_cache = array( "edit_id" => $edit->edit_id,
                              "post_id" => $edit->post_id,
                              "form_id" => $edit->form_id,
                              "date" => $edit->date,
                              "date_gmt" => $edit->date_gmt,
                              "revision_id" => $edit->revision_id,
                              "current_revision_id" => $edit->current_revision_id,
                              "user_id" => $edit->user_id,
                              "ip" => $edit->ip,
                              "state" => $edit->state,
                              "data" => maybe_unserialize($edit->data) );
          wp_cache_set($key,$edit_cache);
          return (object)$edit_cache;
      }
      return (object)array();
  }
  return (object)$edit_cache;  
}

function tdomf_get_state_edit($edit_id) {
  $edit = tdomf_get_edit($edit_id);
  return $edit->state;
}

function tdomf_set_state_edit($edit_state,$edit_id) {
  global $wpdb;
  #tdomf_log_message("Updating state of edit $edit_id to $edit_state");
  $returnVal = false;
  $key = "tdomf_edit_" . $edit_id;
  $edit_cache = wp_cache_get($key);
  $writedb = true;
  if($edit_cache != false && is_array($edit_cache) && isset($edit_cache['state'])) {
      #tdomf_log_message("There is a cache for this edit: $edit_id",TDOMF_LOG_GOOD); 
      if($edit_cache['state'] == $edit_state) {
          tdomf_log_message("State does not need to be updated for $edit_id. It is already at " . $edit_state,TDOMF_LOG_GOOD);
          $writedb = false;
          $returnVal = true;
      }
  }
  if($writedb) {
      #tdomf_log_message("Writing new state for $edit_id to db",TDOMF_LOG_GOOD);
      $table_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;
      $query = "UPDATE $table_name 
                SET state = '".$wpdb->escape($edit_state)."'
                WHERE edit_id = '".$wpdb->escape($edit_id)."'";
      $returnVal = $wpdb->query($query);                
  }
  if($returnVal && $writedb && is_array($edit_cache)) {
      #tdomf_log_message("Updating cache for $edit_id",TDOMF_LOG_GOOD);
      $edit_cache['state'] = $edit_state;
      wp_cache_set($key,$edit_cache);
      #tdomf_log_message("$edit_id Cache: <pre>" . var_export($edit_cache,true) . "</pre>");
  }
  return $returnVal;
}

function tdomf_get_data_edit($edit_id) {
  $edit = tdomf_get_edit($edit_id);
  return maybe_unserialize($edit->data);
}

function tdomf_set_data_edit($edit_data,$edit_id) {
  global $wpdb;
  $returnVal = false;
  $key = "tdomf_edit_" . $edit_id;
  $edit_cache = wp_cache_get($key);
  $writedb = true;
  if($edit_cache != false && is_array($edit_cache) && isset($edit_cache['data'])) {
      if($edit_cache['data'] == $edit_data) {
          #tdomf_log_message("Data does not need to be updated for $edit_id. It is already at <pre>" . var_export($edit_state,true) . "</pre>",TDOMF_LOG_GOOD);
          $writedb = false;
          $returnVal = true;
      }
  }
  if($writedb) {
      #tdomf_log_message("Writing new data for $edit_id to db",TDOMF_LOG_GOOD);
      $table_name = $wpdb->prefix . TDOMF_DB_TABLE_EDITS;
      $query = "UPDATE $table_name 
                SET data = '".$wpdb->escape(maybe_serialize($edit_data))."'
                WHERE edit_id = '".$wpdb->escape($edit_id)."'";
      $returnVal = $wpdb->query($query);                
  }
  if($returnVal && $writedb && is_array($edit_cache)) {
      #tdomf_log_message("Updating cache for $edit_id",TDOMF_LOG_GOOD);
      $edit_cache['data'] = $edit_data;
      wp_cache_set($key,$edit_cache);
      #tdomf_log_message("$edit_id Cache: <pre>" . var_export($edit_cache,true) . "</pre>");
  }
  return $returnVal;
}

function tdomf_import_form($form_id,$options,$widgets,$caps) {
  global $wp_roles, $wpdb;
  
  foreach($options as $option_name => $option_value) {
      if($option_name != TDOMF_OPTION_CREATEDPAGES) {
          tdomf_set_option_form($option_name,$option_value,$form_id);
      }
  }
  
  foreach($widgets as $widget) {
     tdomf_set_option_widget($widget->widget_key,maybe_unserialize($widget->widget_value),$form_id);
  }

  if(!isset($wp_roles)) {
      $wp_roles = new WP_Roles();
  }
  $roles = $wp_roles->role_objects;
  foreach($roles as $role) {
      if(in_array($role->name,$caps)){
         $role->add_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id);
      } else {
         $role->remove_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id);
     }
  }
}

function tdomf_copy_form($form_id) {
  global $wp_roles, $wpdb;

  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
  
  // Copy form options
  //
  $form_name = sprintf(__("Copy of %s","tdomf"),tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id));
  $form_to_copy_options = tdomf_get_options_form($form_id);
  if(empty($form_to_copy_options)) {
    return 0;
  }
  $options = wp_parse_args($options,$form_to_copy_options);
  $copied_form_id = tdomf_create_form($form_name,$options);

  // Reset the "created pages" option
  //
  tdomf_set_option_form(TDOMF_OPTION_CREATEDPAGES,false,$copied_form_id);
  
  //Copy widget options
  //
  $query = "SELECT * 
            FROM $table_name 
            WHERE form_id = '".$wpdb->escape($form_id)."'";
  $widgets = $wpdb->get_results( $query );
  foreach($widgets as $widget) {
    tdomf_set_option_widget($widget->widget_key,maybe_unserialize($widget->widget_value),$copied_form_id);
  }

  // Copy capablities
  //
  if($copied_form_id != 0) {
    if(!isset($wp_roles)) {
       $wp_roles = new WP_Roles();
    }
    $roles = $wp_roles->role_objects;
    foreach($roles as $role) {
       if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])){
          $role->add_cap(TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$copied_form_id);
       }
    }
  }
  return $copied_form_id;
}

function tdomf_set_options_form($options,$form_id = 1) {
  #tdomf_log_message("tdomf_set_options_form for form $form_id");
  global $wpdb;
  $defaults = tdomf_get_options_form($form_id);
  if(empty($defaults)) {
        #tdomf_log_message("tdomf_set_options_form: Constructing defaults");
        $defaults = array( TDOMF_OPTION_DESCRIPTION => '',
                           TDOMF_OPTION_CREATEDPAGES => false,
                           TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS => true,
                           TDOMF_ACCESS_ROLES => false,
                           TDOMF_NOTIFY_ROLES => false,
                           TDOMF_DEFAULT_CATEGORY => 0,
                           TDOMF_OPTION_MODERATION => true,
                           TDOMF_OPTION_ALLOW_EVERYONE => true,
                           TDOMF_OPTION_PREVIEW => true,
                           TDOMF_OPTION_FROM_EMAIL => '',
                           TDOMF_OPTION_FORM_ORDER => false);
  }
  #tdomf_log_message("tdomf_set_options_form: defaults: <pre>".htmlentities(var_export($defaults,true),ENT_COMPAT,get_bloginfo('charset'))."</pre>");
  #tdomf_log_message("tdomf_set_options_form: options: <pre>".htmlentities(var_export($options,true),ENT_COMPAT,get_bloginfo('charset'))."</pre>");
  #tdomf_log_message("tdomf_set_options_form: Preparing data");
  $options = wp_parse_args($options,$defaults);
  $options = maybe_serialize($options);
  #if(DB_CHARSET == 'utf8') {
  #    $options = utf8_encode($options);
  #}
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $query = "UPDATE $table_name 
            SET form_options = '".$wpdb->escape($options)."'
            WHERE form_id = '".$wpdb->escape($form_id)."'";
  #tdomf_log_message("tdomf_set_options_form: query: <pre>".htmlentities($query,ENT_COMPAT,get_bloginfo('charset'))."</pre>");
  return $wpdb->query($query);
}

function tdomf_get_option_widget($key,$form_id = 1) {
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
  $query = "SELECT widget_value 
            FROM $table_name 
            WHERE widget_key = '" .$wpdb->escape($key)."'
                  AND form_id = '".$wpdb->escape($form_id)."'";
  $option = $wpdb->get_row( $query );
  if($option != NULL) {
    return maybe_unserialize($option->widget_value);
  } else {
    $option = tdomf_get_option_form($key,$form_id);
    if($option != false) {
      return $option;
    } else {
      return false;
    }
  }
}

function tdomf_get_widgets_form($form_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_WIDGETS;
  $query = "SELECT * 
            FROM $table_name 
            WHERE form_id = '".$wpdb->escape($form_id)."'";
  return $wpdb->get_results( $query );
}

function tdomf_get_options_form($form_id = 1) {
  #tdomf_log_message("tdomf_get_options_form for from $form_id");
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $query = "SELECT form_options 
            FROM $table_name 
            WHERE form_id = '" .$wpdb->escape($form_id)."'";
  $options = $wpdb->get_row( $query );
  if($options == NULL) {
    return array();
  } else {
    return maybe_unserialize($options->form_options);
  }
  return false;
}

function tdomf_get_option_form($key,$form_id = 1) {
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  if($key == TDOMF_OPTION_NAME) {
    $query = "SELECT form_name 
              FROM $table_name 
              WHERE form_id = '" .$wpdb->escape($form_id)."'";
    return $wpdb->get_var( $query );
  } else {
    $options = tdomf_get_options_form($form_id);
    if(!empty($options) && isset($options[$key])) {
      return $options[$key];
    } else if(get_option($key) != false) {
      return get_option($key);
    }
  }
  return false;
}

function tdomf_get_first_form_id() {
  $form_ids = tdomf_get_form_ids();
  return $form_ids[0]->form_id;
}

function tdomf_get_form_ids(){
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $query = "SELECT form_id 
            FROM $table_name 
            ORDER BY form_id ASC";
  $result = $wpdb->get_results($query);
  return $result;
}

function tdomf_form_exists($form_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . TDOMF_DB_TABLE_FORMS;
  $query = "SELECT * 
            FROM $table_name 
            WHERE form_id = '" .$wpdb->escape($form_id)."'";
  $result = $wpdb->get_row( $query );
  if($result == NULL) {
    return false;
  }
  return true;
}

function tdomf_is_moderation_in_use(){
  // moderation is automatically enabled if spam protection turned on!
  if(get_option(TDOMF_OPTION_SPAM)) { return true; }
  
  $form_ids = tdomf_get_form_ids();
  $retValue = false;
  foreach($form_ids as $form_id) {
    if(tdomf_get_option_form(TDOMF_OPTION_MODERATION,$form_id->form_id)){
      $retValue = true;
      break;
    }
  }
  return $retValue;
}

function tdomf_is_editing_in_use() {
  $form_ids = tdomf_get_form_ids();
  $retValue = false;
  foreach($form_ids as $form_id) {
      if(tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id)) {
          $retValue = true;
          break;
      }
  }
  return $retValue;
}

function tdomf_is_submission_in_use() {
  $form_ids = tdomf_get_form_ids();
  $retValue = false;
  foreach($form_ids as $form_id) {
      if(!tdomf_get_option_form(TDOMF_OPTION_FORM_EDIT,$form_id)) {
          $retValue = true;
          break;
      }
  }
  return $retValue;
}

function tdomf_get_posts($args) {
    global $wpdb;
    $defaults = array('count' => false,
                      'limit' => false,
                      'offset' => false,
                      'count' => false,
                      'form_id' => false,
                      'user_id' => false,
                      'post_status' => array(),
                      'spam' => false, 
                      'query' => false,
                      'nospam' => false,
                      'ip' => false);
    $args = wp_parse_args($args, $defaults);
    extract($args);
    
    // everything gets a little complex if we want to ignore/filter "spam" posts 
    // because they must have the tdomf flag and the spam flag in their Custom Fields.
    
    $special_conditions = false;
    if($spam || $nospam || $form_id || $user_id || $ip) {
        $special_conditions = true;
    }
    
    if($count) {
        if($special_conditions) {
            $sql_query = "SELECT count($wpdb->posts.ID) ";
        } else {
            $sql_query = 'SELECT count(ID) ';
        }
    } else {
        if($special_conditions) {
            $sql_query = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->postmeta.meta_value, $wpdb->posts.post_status ";
        } else {
            $sql_query = 'SELECT ID, post_title, post_status ';
        }
    }           

    $sql_query .= "FROM $wpdb->posts ";
    
    $sql_query .= "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    $sql_query_where = "WHERE ";
    if($special_conditions) {
        $andit = false;
        if($nospam || $spam) {
            $sql_query .= "LEFT JOIN $wpdb->postmeta tdopm ON $wpdb->posts.id =
                           tdopm.post_id AND tdopm.meta_key ='".TDOMF_KEY_SPAM."' ";
            if($nospam) {
               $sql_query_where .= "tdopm.post_id IS NULL ";
            } else {
               $sql_query_where .= "tdopm.post_id IS NOT NULL ";
            }
            $andit = true;
        }
        if($form_id) {
            $sql_query .= "LEFT JOIN $wpdb->postmeta tdopmf ON $wpdb->posts.id =
                           tdopmf.post_id AND tdopmf.meta_key ='".TDOMF_KEY_FORM_ID."' ";
            if($andit) { $sql_query_where .= "AND "; }
            $sql_query_where .= "tdopmf.post_id IS NOT NULL AND tdopmf.meta_value ='" . intval($form_id)."' ";
            $andit = true;
        }     
        if($user_id) {
            $sql_query .= "LEFT JOIN $wpdb->postmeta tdopmu ON $wpdb->posts.id =
                           tdopmu.post_id AND tdopmu.meta_key ='".TDOMF_KEY_USER_ID."' ";
            if($andit) { $sql_query_where .= "AND "; }
            $sql_query_where .= "tdopmu.post_id IS NOT NULL AND tdopmu.meta_value ='" . intval($user_id)."' ";
            $andit = true;
        }
        if($ip) {
            $sql_query .= "LEFT JOIN $wpdb->postmeta tdopmp ON $wpdb->posts.id =
                           tdopmp.post_id AND tdopmp.meta_key ='".TDOMF_KEY_IP."' ";
            if($andit) { $sql_query_where .= "AND "; }
            $sql_query_where .= "tdopmp.post_id IS NOT NULL AND tdopmp.meta_value ='$ip' ";
            $andit = true;
        }
        $sql_query_where .= "AND $wpdb->postmeta.meta_key='".TDOMF_KEY_FLAG."' ";
    } else {
        $sql_query_where .= "meta_key = '".TDOMF_KEY_FLAG."' ";
    }
    
    if(is_array($post_status) && !empty($post_status)) {
        $sql_query_where .= "AND ( ";
        $sql_query_status = array();
        foreach($post_status as $status) {
            $sql_query_status[] = " post_status = '$status' ";
        }
        $sql_query_where .= implode("OR", $sql_query_status);
        $sql_query_where .= ") ";
    }
    
    // now join the where conditions to the query
    
    $sql_query .= $sql_query_where;
    
    if($nospam) {
        $sql_query .= "ORDER BY $wpdb->posts.ID DESC ";
    } else {
        $sql_query .= "ORDER BY ID DESC ";
    }
    
    if($limit && $limit > 0) {
        $sql_query .= 'LIMIT ' . intval($limit) . ' ';
    }
    if($offset && $offset > 0) {
        $sql_query .= 'OFFSET ' . intval($offset) . ' ';
    }

    /*echo $sql_query."<br/>";*/  
    if($query) {
        return $sql_query;
    } else if($count) {
        return intval($wpdb->get_var($sql_query));
    }
    return $wpdb->get_results($sql_query);
}

?>
