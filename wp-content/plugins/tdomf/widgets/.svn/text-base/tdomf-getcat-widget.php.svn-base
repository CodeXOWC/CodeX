<?php
/*
Name: "Set Category from get variables"
URI: http://thedeadone.net/forum/viewthread/TDOMF-Hacks/4/Bypass-Default-Category/20/
Description: Change category of post based on URI of forum. Make sure this is the last widget!
Version: 0.4
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

// Usage: Add widget to bottom of form. Then when you link to the page with the form add "?tdcat="
//        and the id of the category you want the post added to. If there is already a "?" in the URL,
//        use "&tdcat=" instead.

// TODO: Control UI

global $tdomf_getcat_var_name;
$tdomf_getcat_var_name = 'tdcat';
global $tdomf_getcat_overwrite;
$tdomf_getcat_overwrite = true;

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

// Make sure this widget is at the bottom of the form so it is processed last

// This is what is displayed on the forum.
// It is just a hidden value taken from either the URL of the page or from existing input variables
//
function tdomf_widget_getcat($args) {
  global $tdomf_getcat_var_name;
  extract($args);

  $getcat = tdomf_get_option_form(TDOMF_DEFAULT_CATEGORY,$tdomf_form_id);
  if(isset($_GET[$tdomf_getcat_var_name])) {
  	$getcat = intval($_GET[$tdomf_getcat_var_name]);
  } else if(isset($args[$tdomf_getcat_var_name])) {
  	$getcat = intval($args[$tdomf_getcat_var_name]);
  }

  return "<div><input type='hidden' name='$tdomf_getcat_var_name' id='$tdomf_getcat_var_name' value='$getcat' /></div>";
}
tdomf_register_form_widget("getcat", __("Set Category from GET variables","tdomf"), 'tdomf_widget_getcat', array("new-post"));

// This is processed once the post is saved.
//
function tdomf_widget_getcat_post($args,$params) {
  global $tdomf_getcat_overwrite,$tdomf_getcat_var_name;
  extract($args);

  if(isset($args[$tdomf_getcat_var_name])) {
    // Overwrite existing post categories
    //
    if($tdomf_getcat_overwrite) {
      $post_cats = array( $args[$tdomf_getcat_var_name] );
    } else {
      // Append to existing categories
      //
      // Grab existing data
      $post = wp_get_single_post($post_ID, ARRAY_A);
      $current_cats = $post['post_category'];
      // Now merge existing categories with new category
      $post_cats = array_merge( $current_cats, array( $args[$tdomf_getcat_var_name] ) );
    }
    // Update categories on post
    $post = array (
      "ID"            => $post_ID,
      "post_category" => $post_cats,
    );
    wp_update_post($post);
  }
  
  // no errors so return NULL
  return NULL;
}
tdomf_register_form_widget_post("getcat", __("Set Category from GET variables","tdomf"),'tdomf_widget_getcat_post',true,array("new-post"));

function tdomf_widget_getcat_hack($args) {
  global $tdomf_getcat_var_name;
  extract($args);

  $getcat = tdomf_get_option_form(TDOMF_DEFAULT_CATEGORY,$tdomf_form_id);
  $output  = "\t\t<?php \$getcat = $getcat;\n";
  $output .= "\t\tif(isset(\$_GET['$tdomf_getcat_var_name'])) {\n";
  $output .= "\t\t\t\$getcat = intval(\$_GET['$tdomf_getcat_var_name']);\n";
  $output .= "\t\t} else if(isset(\$post_args['$tdomf_getcat_var_name'])) {\n";
  $output .= "\t\t\$getcat = intval(\$post_args['$tdomf_getcat_var_name']); } ?>\n";
  
  $output .= "\t\t<div><input type='hidden' name='$tdomf_getcat_var_name' id='$tdomf_getcat_var_name' value='";
  $output .= "<?php echo \$getcat; ?>' /></div>\n";
  
  return $output;
}
tdomf_register_form_widget_hack("getcat", __("Set Category from GET variables","tdomf"), 'tdomf_widget_getcat_hack', array("new-post"));

?>