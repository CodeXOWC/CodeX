<?php

// Disable revisions *before* everything else
//
define('WP_POST_REVISIONS', false);

//////////////////////////
// Inline upload!       //
//////////////////////////

// 1. User uploads files to a temporary area. Files will be deleted within an
//    hour if not "claimed"
// 2. User submits post.
// 3. Widget copies the files from a temporary area to their proper location and
//    updates post with info about claimed files.
//
// * If post is deleted, files are automatically deleted
// * No direct links to files are exposed (as long as the admins specify a
//   location not directly exposed to the web)

// Session start
//
#if (!isset($_SESSION)) session_start();

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

// enable all PHP errors
//
if(get_option(TDOMF_OPTION_EXTRA_LOG_MESSAGES) && !get_option(TDOMF_OPTION_DISABLE_ERROR_MESSAGES)) {
  error_reporting(E_ALL);
}

// loading text domain for language translation
//
load_plugin_textdomain('tdomf',PLUGINDIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER);

// Get and verify Form Id
//
if(isset($_REQUEST['tdomf_form_id'])){
  $form_id = intval($_REQUEST['tdomf_form_id']);
} else {
  tdomf_log_message("Inline Upload Form: No form id found!",TDOMF_LOG_BAD);
  exit("TDOMF: No Form ID found");
}
if(!tdomf_form_exists($form_id)) {
  tdomf_log_message("Inline Upload Form: A form id of a non-existant form used $form_id!",TDOMF_LOG_BAD);
  #unset($form_data['tdomf_form_id']);
  #tdomf_save_form_data($form_id,$form_data);
  exit("TDOMF: Bad Form ID");
}
// Get widget instance id
$index = '';
if(isset($_REQUEST['index'])) {
    $index = $_REQUEST['index'];
}

// kick of any widgets taht care!
//
do_action('tdomf_upload_inline_form_start',$form_id,tdomf_generate_default_form_mode($form_id));

// Get form data
//
$form_data = tdomf_get_form_data($form_id);
// is form good?
$all_good = true; 

// First pass security check
//
$tdomf_verify = get_option(TDOMF_OPTION_VERIFICATION_METHOD);
if($tdomf_verify == false || $tdomf_verify == 'default') {
  if(isset($form_data['tdomf_upload_key_'.$form_id.'_'.$index]) && isset($_POST['tdomf_upload_key_'.$form_id.'_'.$index]) && $form_data['tdomf_upload_key_'.$form_id.'_'.$index] != $_POST['tdomf_upload_key_'.$form_id.'_'.$index]){
     #tdomf_log_message_extra("Upload form submitted with bad key from ".$_SERVER['REMOTE_ADDR']." !",TDOMF_LOG_BAD);
     unset($form_data['tdomf_upload_key_'.$form_id.'_'.$index]); // prevents any "operations" on uploads
     $all_good = false;
     #exit("TDOMF: Bad data submitted");
  }
} else if($tdomf_verify == 'wordpress_nonce') {
  if(!isset($_POST['tdomf_upload_key_'.$form_id.'_'.$index]) || !wp_verify_nonce($_POST['tdomf_upload_key_'.$form_id.'_'.$index],'tdomf-form-upload-'.$form_id.'-'.$index)) {
    unset($form_data['tdomf_upload_key_'.$form_id.'_'.$index]);
    $all_good = false;
  }
}

// URL for this form
$tdomf_upload_inline_url = TDOMF_URLPATH . 'tdomf-upload-inline.php';

// Permissions check
//
if(!tdomf_can_current_user_see_form($form_id)) {
  tdomf_log_message("Someone with no permissions tried to access the inline-uplaod form!",TDOMF_LOG_BAD);
  unset($form_data['tdomf_upload_key_'.$form_id.'_'.$index]);
  $all_good = false;
  tdomf_save_form_data($form_id,$form_data);
  exit("TDOMF: Bad permissions");
}

// Widget in use check
//
if((empty($index) && !in_array("upload-files",tdomf_get_widget_order($form_id))) ||
   (!empty($index) && !in_array("upload-files".$index,tdomf_get_widget_order($form_id)))) {
  unset($form_data['tdomf_upload_key_'.$form_id.'_'.$index]);
  tdomf_save_form_data($form_id,$form_data);
  exit("TDOMF: Upload feature not yet enabled");
}

// Grab options for uploads
//
#$options = tdomf_widget_upload_get_options($form_id);
global $tdomf_widget_uploadfiles;
$options = $tdomf_widget_uploadfiles->getOptions($form_id,$index);

// Placeholder for error messages
//
$errors = "";

// Files recorded in session
//
$sessioncount = 0;
$mysessionfiles = array();

// Files uploaded now
//
$myfiles = array();
$count = 0;

// Double check files in $_SESSION!
//
if(isset($form_data['uploadfiles_'.$form_id.'_'.$index])) {
  $sessioncount = 0;
  $mysessionfiles = $form_data['uploadfiles_'.$form_id.'_'.$index];
  for($i =  0; $i < $options['max']; $i++) {
    if(/*isset($mysessionfiles[$i]['path']) &&*/ !file_exists($mysessionfiles[$i]['path'])) {
      unset($mysessionfiles[$i]);
    } else {
      $sessioncount++;
    }
  }
}

// Allowed file extensions (used when file is uploaded and in javascript)
//
$allowed_exts = explode(" ",strtolower($options['types']));

// Only do actions if key is good!
//
if($all_good) {

  // Delete files at user request
  //
  if(isset($_POST['tdomf_upload_inline_delete_all_'.$form_id.'_'.$index])) {
    for($i =  0; $i < $options['max']; $i++) {
      tdomf_delete_tmp_file($mysessionfiles[$i]['path']);
    }
    $mysessionfiles = array();
    $sessioncount = 0;
    unset($form_data['uploadfiles_'.$form_id.'_'.$index]);
  }

  // Only worry about uploaded files if the upload secruity key is good
  //
  else if(isset($_POST['tdomf_upload_inline_submit_'.$form_id.'_'.$index])) {

    // Move the uploaded file to the temp storage path
    //
    for($i =  0; $i < $options['max']; $i++) {
      $upload_temp_file_name = $_FILES["uploadfile".$form_id.'_'.$index."_".$i]['tmp_name'];
      $upload_file_name = $_FILES["uploadfile".$form_id.'_'.$index."_".$i]['name'];
      $upload_error = $_FILES["uploadfile".$form_id.'_'.$index."_".$i]['error'];
      $upload_size = $_FILES["uploadfile".$form_id.'_'.$index."_".$i]['size'];
      $upload_type = $_FILES["uploadfile".$form_id.'_'.$index."_".$i]['type'];
      if(is_uploaded_file($upload_temp_file_name)) {
        // double check file extension
        $ext = strtolower(strrchr($upload_file_name,"."));
        if(in_array($ext,$allowed_exts)) {
          $storagepath = tdomf_create_tmp_storage_path($form_id,$index);
          $uploaded_file = $storagepath.DIRECTORY_SEPARATOR.$upload_file_name;
          tdomf_log_message_extra("Saving uploaded file to $uploaded_file");
          // Save the file
          if(move_uploaded_file($upload_temp_file_name,$uploaded_file)) {
            $uploaded_file = realpath($uploaded_file);
            // Remember the file
            $myfiles[$i] = array( "name" => $upload_file_name, "path" => $uploaded_file, "size" => $upload_size, "type" => $upload_type );
            $count++;
            tdomf_log_message("File $upload_file_name saved to tmp area as $uploaded_file. It has a size of $upload_size and type of $upload_type" );
            // within an hour, delete the file if not claimed!
            wp_schedule_single_event( time() + TDOMF_UPLOAD_TIMEOUT, 'tdomf_delete_tmp_file_hook', array($uploaded_file) );
          } else {
            tdomf_log_message("move_uploaded_file failed!");
            $errors .= sprintf(__("Could not move uploaded file %s to storage area!<br/>","tdomf"),$upload_file_name);
          }
        } else {
          tdomf_log_message("file $upload_file_name uploaded with bad extension: $ext");
          $errors .= sprintf(__("Files with %s extensions are forbidden.<br/>","tdomf"),$ext);
        }
      } else if($upload_error != 0 && !empty($upload_file_name)){
        tdomf_log_message("There was a reported error $upload_error with the uploaded file!");
        switch($upload_error) {
          case 1 :
            $errors .= sprintf(__("Sorry but %s was too big. It exceeded the server configuration.<br/>","tdomf"),$upload_file_name);
            break;
          case 2:
            $errors .= sprintf(__("Sorry but %s was too big. It was greater than %s. It exceeded the configured maximum.<br/>","tdomf"),$upload_file_name,tdomf_filesize_format($options['size']));
            break;
          case 3:
            $errors .= sprintf(__("Sorry but only part of %s was uploaded.<br/>","tdomf"),$upload_file_name);
            break;
          case 4:
            $errors .= __("Sorry file does not exist.<br/>","tdomf");
            break;
          default;
            $errors .= sprintf(__("Upload of %s failed for an unknown reason. (%s)<br/>","tdomf"),$upload_file_name,$upload_error);
            break;
        }
      } else {
        tdomf_log_message_extra("No file here",TDOMF_LOG_ERROR);
      }
    }
    // Store in session!
    $mysessionfiles = array_merge($myfiles, $mysessionfiles);
    $form_data['uploadfiles_'.$form_id.'_'.$index] = $mysessionfiles;
    // Recount
    $sessioncount = 0;
    for($i =  0; $i < $options['max']; $i++) {
      if(/*isset($mysessionfiles[$i]['path']) &&*/ file_exists($mysessionfiles[$i]['path'])) {
        $sessioncount++;
      }
    }
  }
}

// Create new security key
//
unset($form_data['tdomf_upload_key_'.$form_id.'_'.$index]);
tdomf_save_form_data($form_id,$form_data);
$form_data = tdomf_get_form_data($form_id);
//
if($tdomf_verify == 'wordpress_nonce' && function_exists('wp_create_nonce')) {
  $nonce_string = wp_create_nonce( 'tdomf-form-upload-'.$form_id.'-'.$index );
  $form_data["tdomf_upload_key_".$form_id.'_'.$index] = $nonce_string;
} else if($tdomf_verify == 'none') {
    // do nothing! Bad :(
} else {
  $upload_key = tdomf_random_string(100);
  $form_data["tdomf_upload_key_".$form_id.'_'.$index] = $upload_key;
}
//
tdomf_save_form_data($form_id,$form_data);
$form_data = tdomf_get_form_data($form_id);

// Now the fun bit, the actually form!
//
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!-- <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" /> -->
<?php tdomf_stylesheet(); ?>
<script type="text/javascript">
// <![CDATA[
function endsWith(str,ends){
   var startPos = str.length - ends.length;
   if (startPos < 0) {
      return false;
   }
   return (str.lastIndexOf(ends, startPos) == startPos);
}
function validateFile(id,msg) {
  var e1 = document.getElementById(id);
  if(e1 != null) {
    var f = e1.value.toLowerCase();
    if(f.length > 0) {
      <?php foreach($allowed_exts as $e) {
        if(!empty($e)) { ?>
          if(endsWith(f,"<?php echo $e; ?>")) { return true; }
      <?php } } ?>
    } else {
      // Nothing to validate so okay
      return true;
    }
    if(msg) {
      alert("<?php printf(__("The file must be of type %s!","tdomf"),$options['types']); ?>");
    }
    return false;
  }
  // Nothing to validate so okay
  return true;
}
function validateForm() {
  <?php for($i =  0, $j = 0; $i < $options['max']; $i++) { ?>
  if(!validateFile('uploadfile<?php echo $form_id; ?>_<?php echo $i; ?>'),false) {
    var f = document.getElementById('uploadfile<?php echo $form_id; ?>_<?php echo $i; ?>').value;
    alert( "<?php printf(__('File %s has a bad extension and cannot be upload!','tdomf'),'" + f + "'); ?>" );
    return false;
  }
  <?php } ?>
  return true;
}
// ]]>
</script>
</head>
<body>

<?php if($errors != "") { ?>
  <div class="tdomf_upload_inline_errors">
  <?php echo $errors; ?>
  </div>
<?php } ?>

<form name="tdomf_upload_inline_form" id="tdomf_upload_inline_form" enctype="multipart/form-data" method="post" action="<?php echo $tdomf_upload_inline_url; ?>"  >
  <?php if(isset($form_data['tdomf_upload_key_'.$form_id.'_'.$index])) { ?>
  <input type='hidden' id='tdomf_upload_key_<?php echo $form_id; ?>_<?php echo $index; ?>' name='tdomf_upload_key_<?php echo $form_id; ?>_<?php echo $index; ?>' value='<?php echo $form_data['tdomf_upload_key_'.$form_id.'_'.$index]; ?>' >
  <?php } ?>
  <input type='hidden' name='MAX_FILE_SIZE' value='<?php echo $options['size']; ?>' />
  <input type='hidden' id='tdomf_form_id' name='tdomf_form_id' value='<?php echo $form_id; ?>' />
  <input type='hidden' id='index' name='index' value='<?php echo $index; ?>' />
  <?php if($sessioncount > 0) { ?>
  <p><?php _e("Your files will be kept on the server for 1 hour. You must submit your post before then.","tdomf"); ?></p>
  <?php } ?>
  <?php if($sessioncount < $options['max']) { ?>
  <p><small>
  <?php printf(__("Max File Size: %s","tdomf"),tdomf_filesize_format($options['size'])); ?><br/>
  <?php printf(__("Allowable File Types: %s","tdomf"),$options['types']); ?><br/>
  </small></p>
  <?php } ?>
  <?php for($i =  0, $j = 0; $i < $options['max']; $i++) {
      if(isset($mysessionfiles[$i])) { ?>
        <input type='hidden' name='deletefile[]' value="<?php echo $i; ?>" />
        <?php printf(__("<i>%s</i> (%s) Uploaded","tdomf"),$mysessionfiles[$i]['name'],tdomf_filesize_format($mysessionfiles[$i]['size'])); ?>
        <br/>
    <?php } else {
      if(($sessioncount + $j) < $options['min']) { ?>
        <label for='uploadfile<?php echo $form_id; ?>_<?php echo $index; ?>_<?php echo $i; ?>' class='required'>
      <?php } else { ?>
        <label for='uploadfile<?php echo $form_id; ?>_<?php echo $index; ?>_<?php echo $i; ?>'>
      <?php } _e("Upload: ","tdomf"); $j++; ?>
      <input type='file' name='uploadfile<?php echo $form_id; ?>_<?php echo $index; ?>_<?php echo $i; ?>' id='uploadfile<?php echo $form_id; ?>_<?php echo $index; ?>_<?php echo $i; ?>' size='30' onChange="validateFile('uploadfile<?php echo $form_id; ?>_<?php echo $i; ?>',true);" /></label><br/>
  <?php } }?>
  <?php if($sessioncount < $options['max']) { ?>
  <input type="submit" id="tdomf_upload_inline_submit_<?php echo $form_id; ?>_<?php echo $index; ?>" name="tdomf_upload_inline_submit_<?php echo $form_id; ?>_<?php echo $index; ?>" value="<?php _e("Upload Now!","tdomf"); ?>" />
  <?php } ?>
  <?php if($sessioncount > 0) { ?>
  <input type="submit" id="tdomf_upload_inline_delete_all_<?php echo $form_id; ?>_<?php echo $index; ?>" name="tdomf_upload_inline_delete_all_<?php echo $form_id; ?>_<?php echo $index; ?>" value="<?php _e("Delete All!","tdomf"); ?>" />
  <?php } ?>
</form>

</body>