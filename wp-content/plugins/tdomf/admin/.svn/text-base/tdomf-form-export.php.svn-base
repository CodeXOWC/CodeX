<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

function tdomf_show_form_export($form_id) {
  if(!tdomf_form_exists($form_id)) { ?>
    <div><font color="red"><?php printf(__("Form id %d does not exist!","tdomf"),$form_id); ?></font></div>
  <?php } else { ?>
    
    <div class="wrap">
    
    <h2><?php printf(__("Export and Import Form %d Configuration:\"%s\"","tdomf"),$form_id,tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id)); ?></h2>
    
    <?php tdomf_forms_under_title_toolbar($form_id, 'tdomf_show_form_export_menu'); ?>
      
     <?php $export_url = get_bloginfo('wpurl')."?tdomf_export=$form_id";
           $export_url = wp_nonce_url($export_url,'tdomf-export-'.$form_id);?>
    
     <p>
        <?php printf(__('To export the configuration of this file, just <a href="%s">save this link</a>. To import, just use the form below to select a previousily exported file and click "Import"',"tdomf"),$export_url); ?>     </p>
     </p>
    
     <form enctype="multipart/form-data" method="post" action="admin.php?page=tdomf_show_form_export_menu&form=<?php echo $form_id; ?>">
        <label for="import_file"><b><?php _e("Form saved configuration to import: "); ?></b></label>
        <!-- <input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
        <input type="hidden" name='form_id' id='form_id' value='<?php echo $form_id; ?>'>
        <input type='file' name='import_file' id='import_file' size='30' />
        <input type="submit" name="tdomf_import" id="tdomf_import" value="<?php _e("Import","tdomf"); ?>" />
        <?php wp_nonce_field('tdomf-import-'.$form_id); ?>
     </form>
     
  </div> <!-- wrap -->
  
  <?php }
}

// Display the menu to configure options for this plugin
//
function tdomf_show_form_export_menu() {
  global $wpdb, $wp_roles;

  $form_id = tdomf_get_first_form_id();
  $new_form_id = tdomf_handle_form_export_actions();
  if($new_form_id != false) {
      $form_id = $new_form_id;
  } else if(isset($_REQUEST['form'])) {
      $form_id = intval($_REQUEST['form']);
  }

  tdomf_forms_top_toolbar($form_id, 'tdomf_show_form_export_menu');
  tdomf_show_form_export(intval($form_id));
}


// Handle actions for this form
//
function tdomf_handle_form_export_actions() {
   global $wpdb, $wp_roles;

   $message = "";
   $retValue = false;
   
  if(isset($_REQUEST['tdomf_import'])) {
     
     $import_message = tdomf_import_form_from_file();
     if($import_message != false) { $message .= $import_message . '<br/>'; }

  }

   // Warnings

   $message .= tdomf_get_error_messages(false);

   if(!empty($message)) { ?>
   <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
   <?php }
   
   return $retValue;
}

?>
