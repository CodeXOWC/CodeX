<?php
/*
Name: "Tags"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: This widget allows users to add tags to their submissions
Version: 3
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

#TODO: Allow user to specify size and overwrite default tags
#TODO: Option to use a "tag cloud", check boxes and/or dropdown/radio buttons to select tags

// Only enable this widget if tags are avaliable in the installation
//
// TDOMF is only meant to support WP2.3... but ppl still ask for 2.2 support
// so this function call will disable this widget in 2.3
//
if(function_exists('wp_set_post_tags')) {

  function tdomf_widget_tags_options($form_id) {
        $options = tdomf_get_option_widget('tdomf_tags_widget',$form_id);
        if($options == false) {
           $options = array();
           $options['title'] = "";
           $options['required'] = false;
           $options['default'] = '';
           $options['user'] = true;
        }
      return $options;
  }

  
  //////////////////////////////
  // Add text field to display tags.
  //
  function tdomf_widget_tags($args) {
    extract($args);
    $options =  tdomf_widget_tags_options($tdomf_form_id);
    $output = "";
    if($options['user']) {
        $output  = $before_widget;
        if($options['title'] != "") {
            $output .= $before_title.$options['title'].$after_title;
        }
        if($options['required']) {
            $output .= '<label for="tags" class="required" >';
        } else {
            $output .= '<label for="tags" >';
        }
        $output .= __("Tags (separate multiple tags with commas: cats, pet food, dogs):","tdomf");
        $output .= '</label>';
        $output .= '<br/><input type="text" id="tags" name="tags" size="60" value="'.htmlentities($tags,ENT_QUOTES,get_bloginfo('charset')).'" />';
        $output .= $after_widget;
    }
    return $output;
  }
  tdomf_register_form_widget('tags',__('Tags','tdomf'), 'tdomf_widget_tags', array("new-post"));  
  
  ////////////////////////
  // Add tags to the post
  //
  function tdomf_widget_tags_post($args) {
    extract($args);
    $options =  tdomf_widget_tags_options($tdomf_form_id);
    
    $tagslist = '';
    if($options['default']) {
        $tagslist = strip_tags($options['default']);
    }
    
    if(isset($tags) && !empty($tags)) {
        if(!empty($tagslist)) { $tagslist .= ','; }
        $tagslist .= strip_tags($tags);
    }

    if(!empty($tagslist)) {
       # set last var to true to just append
       wp_set_post_tags($post_ID, strip_tags($tagslist),false);
    }
    
    return NULL;
  }
  tdomf_register_form_widget_post('tags',__('Tags','tdomf'), 'tdomf_widget_tags_post', array("new-post"));
  
  //////////////////////////////
  // Control options
  //
  function tdomf_widget_tags_control($form_id) {
    $options =  tdomf_widget_tags_options($form_id);
  
  // Store settings for this widget
    if ( $_POST['tags-submit'] ) {
     $newoptions['title'] = strip_tags(stripslashes($_POST['tags-title']));
     $newoptions['required'] = isset($_POST['tags-required']);
     $newoptions['user'] = isset($_POST['tags-user']);
     $newoptions['default'] = $_POST['tags-default'];
     if ( $options != $newoptions ) {
        $options = $newoptions;
        tdomf_set_option_widget('tdomf_tags_widget', $options,$form_id);
     }
  }

   // Display control panel for this widget
  
  extract($options);

        ?>
<div>

<label for="tags-title" style="line-height:35px;"><?php _e("Title: ","tdomf"); ?></label>
<input type="textfield" id="tags-title" name="tags-title" value="<?php echo htmlentities($options['title'],ENT_QUOTES,get_bloginfo('charset')); ?>" /></label>
<br/>

<input type="checkbox" name="tags-user" id="tags-user" <?php if($options['user']) echo "checked"; ?> >
<label for="tags-user" style="line-height:35px;"><?php _e("Allow the submitter to add tags to the submission","tdomf"); ?></label>
<br/>

<input type="checkbox" name="tags-required" id="tags-required" <?php if($options['required']) echo "checked"; ?> >
<label for="tags-required" style="line-height:35px;"><?php _e("The submitter must supply at least one tag","tdomf"); ?></label>
<br/>

<label for="tags-title" style="line-height:35px;"><?php _e("Default Tags: ","tdomf"); ?></label><br/>
<small><?php _e("These tags will be added to any post submitted using this form. Separate multiple tags with commas: cats, pet food, dogs","tdomf"); ?></small><br/>
<input type="textfield" id="tags-default" name="tags-default" size='40' value="<?php echo htmlentities($options['default'],ENT_QUOTES,get_bloginfo('charset')); ?>" />

</div>
        <?php
  }
  tdomf_register_form_widget_control('tags',__('Tags','tdomf'), 'tdomf_widget_tags_control', 500, 300, array("new-post"));
  
  ////////////////////////
  // Preview tags
  //
  function tdomf_widget_tags_preview($args) {
    extract($args);
    $options =  tdomf_widget_tags_options($tdomf_form_id);
    if($options['user']) {
        if(isset($tags) && !empty($tags)) {
          $output  = $before_widget;
          if($options['title'] != '') {
              $output .= $before_title.$options['title'].$after_title;
          }
          $output .= sprintf(__("<b>Post will be sumbmitted with these tags</b>:<br/>%s","tdomf"), strip_tags($tags));  
          $output .= $after_widget;
          return $output;
        }
    }
    return "";
  }
  tdomf_register_form_widget_preview('tags',__('Tags','tdomf'), 'tdomf_widget_tags_preview', array("new-post"));
  
  ////////////////////////
  // Hack the Preview tags
  //
  function tdomf_widget_tags_preview_hack($args) {
    extract($args);
    $options =  tdomf_widget_tags_options($tdomf_form_id);
    $output = "";
    if($options['user']) {
        $output  = $before_widget;
        $output .= "\t<?php \$tags = strip_tags(trim(\$tags));\n"; 
        $output .= "\tif(!empty(\$tags)) { ?>\n";
        if($options['title'] != '') {
            $output .= $before_title.$options['title'].$after_title;
        }
        $output .= "\t\t<b>".__("Post will be submitted with these tags:","tdomf")."</b>\n\t\t<br/>\n\t\t<?php echo \$tags; ?>\n";
        $output .= "\t<?php } ?>";
        $output .= $after_widget;
    }
    return $output;
  }
  tdomf_register_form_widget_preview_hack('tags',__('Tags','tdomf'), 'tdomf_widget_tags_preview_hack', array("new-post"));
  
  ///////////////////////////////////////////////////////////
  // Show what tags are on the post to admins for moderating
  //
  function tdomf_widget_tags_adminemail($args) {
    extract($args);
  
    $tags = wp_get_post_tags($post_ID);
    
    if(!empty($tags)) {
      $output  = $before_widget;
      $output .= __("Post tagged with\r\n","tdomf");
      foreach($tags as $tag) {
        $output .= $tag->name.", ";
      }
      $output .= $after_widget;
      return $output;
    }
    
    return "";
  }
  tdomf_register_form_widget_adminemail('tags',__('Tags','tdomf'), 'tdomf_widget_tags_adminemail', array("new-post"));

  function tdomf_widget_tags_hack($args) {
    extract($args);
    $options =  tdomf_widget_tags_options($tdomf_form_id);
    $output = "";
    if($options['user']) {
        $output  = $before_widget;  
        if($options['title'] != '') {
            $output .= $before_title.$options['title'].$after_title;
        }
        if($options['required']) {
            $output .= "\t\t".'<label for="tags" class="required">';
        } else {
            $output .= "\t\t".'<label for="tags" >';
        }
        $output .= "\n\t\t".__("Tags (separate multiple tags with commas: cats, pet food, dogs):","tdomf");
        $output .= '</label>';
        $output .= "\n\t\t<br/>\n\t\t".'<input type="text" id="tags" name="tags" size="60" value="';
        $output .= "<?php echo htmlentities(\$tags,ENT_QUOTES,get_bloginfo('charset')); ?>".'" />';
        $output .= $after_widget;
    }
    return $output;
    }
  tdomf_register_form_widget_hack('tags',__('Tags','tdomf'), 'tdomf_widget_tags_hack', array("new-post"));

  ///////////////////////////////////////
  // Validate required tags
  //
  function tdomf_widget_tags_validate($args,$preview) {
      extract($args);
      $options = tdomf_widget_tags_options($tdomf_form_id);
      $output = "";

      if($options['user'] && $options['required']) {
          if(empty($tags)) {
              $output .= __('You must specify at least one tag.','tdomf');
          }
      }
      
      // return output if any
      if($output != "") {
        return $before_widget.$output.$after_widget;
      } 
      return NULL;
  }
  tdomf_register_form_widget_validate('tags',__('Tags','tdomf'), 'tdomf_widget_tags_validate');


}

?>