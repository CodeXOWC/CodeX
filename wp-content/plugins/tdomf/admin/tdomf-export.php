<?php

// These admin functions allow the import and export of forms!

function tdomf_export_form(){
   $form_id = $_GET['tdomf_export'];
   
   $ok = true;
   
   if(!tdomf_form_exists($form_id)) {
       $ok = false; 
   }
   
   if(!current_user_can('manage_options')) {
       $ok = false;
   }
   
   if($ok) {
       check_admin_referer('tdomf-export-'.$form_id);
   }

   if($ok) {
       $form_data = array();
       $form_data['options'] = tdomf_get_options_form($form_id);
       $form_data['options'][TDOMF_OPTION_NAME] = tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id);
       $form_data['widgets'] = tdomf_get_widgets_form($form_id);
       $form_data['caps'] = array();
       if(!isset($wp_roles)) {
          $wp_roles = new WP_Roles();
       }
       $roles = $wp_roles->role_objects;
       foreach($roles as $role) {
          if(isset($role->capabilities[TDOMF_CAPABILITY_CAN_SEE_FORM.'_'.$form_id])){
              $form_data['caps'][] = $role->name;
          }
       }
       $form_export = serialize($form_data);
   }
   
   @ignore_user_abort();
   @set_time_limit(600);
   
   if($ok) {
       header("Content-Type: text/plain");
       header("Content-Disposition: attachment; filename=\"form_export_$form_id.txt\"");
       header("Content-Length: " . strlen($form_export));
       sleep(1);
       echo $form_export;
   } else { 
       header("HTTP/1.0 404 Not Found");
   }
   
   exit();
}
if(isset($_GET['tdomf_export'])) { 
  add_action('init', 'tdomf_export_form');
}

function tdomf_import_form_from_file()
{
   $form_id = $_REQUEST['form_id'];
   $ok = true;
   $message = false;
   
   if(!tdomf_form_exists($form_id)) {
       tdomf_log_message("tdomf_import_form_from_file: bad form id: $form_id",TDOMF_LOG_ERROR );
       $ok = false; 
   }
   
   if(!current_user_can('manage_options')) {
       $ok = false;
   }

   if($ok) {
       check_admin_referer('tdomf-import-'.$form_id);
   }
   
   if($ok) {
       if(isset($_FILES["import_file"])) {
           $thefile = $_FILES["import_file"]; # tmp_name, name, error, size, type
           if(@is_uploaded_file($thefile['tmp_name'])) {
               tdomf_log_message("Import File Found" );
               $fh = @fopen($thefile['tmp_name'], 'r');
               if($fh != false)
               {
                  $form_import = fread($fh, filesize($thefile['tmp_name']));
                  fclose($fh);
               } else {
                   tdomf_log_message("Error opening file!" );
                   $message = __("Error importing form","tdomf",TDOMF_LOG_ERROR);
                   $ok = false;
               }
               @unlink($thefile['tmp_name']);
           } else {
               tdomf_log_message("Error uploading file! <pre>" . var_export($_FILES["import_file"],true) . "</pre>",TDOMF_LOG_ERROR );
               $message = __("Error importing form","tdomf");
               $ok = false;
           }
       } else {
           tdomf_log_message("Error no 'import_file' value: <pre>" . var_export($_FILES,true) . "</pre>", TDOMF_LOG_ERROR );
           $message = __("Error importing form","tdomf",TDOMF_LOG_ERROR);
           $ok = false;
       }
       
       /*$fh = @fopen('/storage/home/associat/c/cammy/form_export_1.txt', 'r');
       if($fh != false)
       {
          #$form_import = fread($fh, filesize('/storage/home/associat/c/cammy/form_export_1.txt') + 100);
          while (!feof($fh)) {
              $form_import .= fread($fh, 8192);
          }
          fclose($fh);
          tdomf_log_message("Seralized form data: <pre>" . htmlentities($form_import) . "</pre>");
       } else {
           tdomf_log_message("Error opening file!" );
           $message = __("Error importing form","tdomf",TDOMF_LOG_ERROR);
           $ok = false;
       }*/
   }
   
   if($ok) {
       #error_reporting(E_ALL);
       $form_data = unserialize($form_import);
       if(is_array($form_data)) {
             tdomf_import_form($form_id,$form_data['options'],$form_data['widgets'],$form_data['caps']);
             tdomf_log_message("Form import succeeded <pre>" . htmlentities(var_export($form_data,true)) . "</pre>",TDOMF_LOG_GOOD);
             $message = __("Form import successful","tdomf");
         } else {
             if($form_data == false) {
                 tdomf_log_message("Form import failed. Couldn't unserialize data: <pre>" . htmlentities($form_import) . "</pre>",TDOMF_LOG_ERROR);
                 $message = __("Failed to unserialize form data: Form import failed","tdomf");                 
             } else {
                 tdomf_log_message("Form import failed: Data invalid: <pre>" . htmlentities(var_export($form_data,true)) . "</pre>",TDOMF_LOG_ERROR);
                 $message = __("Form import failed","tdomf");
             }
             $ok = false;
         }
   }
   return $message;
}

?>
