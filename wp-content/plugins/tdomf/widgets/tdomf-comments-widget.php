<?php
/*
Name: "Comments"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: Change comment, ping and trackback settings (or let users decide)
Version: 1
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

  function tdomf_widget_comments($args) {
    $options = tdomf_widget_comments_get_options($args['tdomf_form_id']);
    $output = "";
    if($options['user-comments'] || $options['user-pings'])
    {
        extract($args);
        $output  = $before_widget;  
        
        if(!empty($options['title'])) {
            $output .= $before_title;
            $output .= $options['title'];
            $output .= $after_title;
        }
        
        if($options['user-comments']) {
            $defval = true;
            if($options['overwrite']) {
                $defval = $options['comments'];
            } else if(get_option('default_comment_status') == 'closed') {
                $defval = false;
            }
            if(isset($args["tdomf_key_$tdomf_form_id"])){
                $defval = isset($args["comments-user-comments"]);
            }
            
            $output .= '<input type="checkbox" name="comments-user-comments" id="comments-user-comments"';
            if($defval) {
                $output .= "checked";
            } 
            $output .= '>';
            $output .= '<label for="comments-user-comments" >';
            $output .= __("Allow Comments","tdomf");
            $output .= '</label>';
        }
        
        if($options['user-pings']) {
            $defval = true;
            if($options['overwrite']) {
                $defval = $options['pings'];
            } else if(get_option('default_ping_status') == 'closed') {
                $defval = false;
            }
            if(isset($args["tdomf_key_$tdomf_form_id"])){
                $defval = isset($args["comments-user-pings"]);
            }
            
            if($options['user-comments']) {
                $output .= "<br/>";
            }
            
            $output .= '<input type="checkbox" name="comments-user-pings" id="comments-user-pings"';
            if($defval) {
                $output .= "checked";
            } 
            $output .= '>';
            $output .= '<label for="comments-user-pings" >';
            $output .= __("Allow Pings and Trackbacks","tdomf");
            $output .= '</label>';
        }
        $output .= $after_widget;
    }
    return $output;
  }
  tdomf_register_form_widget('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments', $modes = array('new'));
 
  function tdomf_widget_comments_hack($args) {
    $options = tdomf_widget_comments_get_options($args['tdomf_form_id']);
    $output = "";
    if($options['user-comments'] || $options['user-pings'])
    {
        extract($args);
        $output  = $before_widget;  
        
        if(!empty($options['title'])) {
            $output .= $before_title;
            $output .= $options['title'];
            $output .= $after_title;
        }
        
        if($options['user-comments']) {
            
            $defval = true;
            if($options['overwrite']) {
                $defval = $options['comments'];
            } else if(get_option('default_comment_status') == 'closed') {
                $defval = false;
            }
            if($defval) {
                $output .= "\t\t<?php \$checked = 'checked';\n";  
            } else {
                $output .= "\t\t<?php \$checked = '';\n";
            }
            $output .= "\t\tif(isset(\$post_args['tdomf_key_$tdomf_form_id'])){\n";
            $output .= "\t\t\t\$checked = '';\n";
            $output .= "\t\t\tif(isset(\$post_args['comments-user-comments'])){\n";
            $output .= "\t\t\t\t\$checked = 'checked';\n";
            $output .= "\t\t} } ?>\n";
            
            $output .= "\t\t".'<input type="checkbox" name="comments-user-comments" id="comments-user-comments"';
            $output .= "<?php echo \$checked; ?>";
            $output .= '>';
            $output .= '<label for="comments-user-comments" >';
            $output .= __("Allow Comments","tdomf");
            $output .= "</label>";
        }
        
        if($options['user-pings']) {

            if($options['user-comments']) {
                $output .= "\n\t\t<br/>\n";
            }
            
            $defval = true;
            if($options['overwrite']) {
                $defval = $options['pings'];
            } else if(get_option('default_ping_status') == 'closed') {
                $defval = false;
            }
            if($defval) {
                $output .= "\t\t<?php \$checked = 'checked';\n";  
            } else {
                $output .= "\t\t<?php \$checked = '';\n";
            }
            $output .= "\t\tif(isset(\$post_args['tdomf_key_$tdomf_form_id'])){\n";
            $output .= "\t\t\t\$checked = '';\n";
            $output .= "\t\t\tif(isset(\$post_args['comments-user-pings'])){\n";
            $output .= "\t\t\t\t\$checked = 'checked';\n";
            $output .= "\t\t} } ?>\n";
            
            $output .= "\t\t".'<input type="checkbox" name="comments-user-pings" id="comments-user-pings"';
            $output .= "<?php echo \$checked; ?>";
            $output .= '>';
            $output .= '<label for="comments-user-pings" >';
            $output .= __("Allow Pings and Trackbacks","tdomf");
            $output .= "</label>";
        }
        $output .= $after_widget;
    }
    return $output;
    }
  tdomf_register_form_widget_hack('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments_hack', $modes = array('new'));
  
  function tdomf_widget_comments_preview($args) {
    extract($args);
    $options = tdomf_widget_comments_get_options($args['tdomf_form_id']);
    $output = "";
    if($options['user-comments'] || $options['user-pings'])
    {
        extract($args);
        $output  = $before_widget;  
        
        if(!empty($options['title'])) {
            $output .= $before_title;
            $output .= $options['title'];
            $output .= $after_title;
        }
        
        if($options['user-comments']) {
            if(isset($args["comments-user-comments"])) {
                $output .= __("Comments will be enabled for this submission","tdomf");
            } else {
                $output .= __("Comments will be disabled for this submission","tdomf");                
            }
        }
        
        if($options['user-pings']) {
            if($options['user-comments']) {
                $output .= "<br/>";
            }
            if(isset($args["comments-user-pings"])) {
                $output .= __("Pings and Trackbacks will be enabled for this submission","tdomf");
            } else {
                $output .= __("Pings and Trackbacks will be disabled for this submission","tdomf");                
            }
        }
        $output .= $after_widget;
    }
    return $output;
  }
  tdomf_register_form_widget_preview('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments_preview', $modes = array('new'));

  function tdomf_widget_comments_preview_hack($args) {
    extract($args);
    $options = tdomf_widget_comments_get_options($args['tdomf_form_id']);
    $output = "";
    if($options['user-comments'] || $options['user-pings'])
    {
        extract($args);
        $output  = $before_widget;  
        
        if(!empty($options['title'])) {
            $output .= $before_title;
            $output .= $options['title'];
            $output .= $after_title;
        }
        
        if($options['user-comments']) {
            $output .= "\n\t<?php if(isset(\$post_args['comments-user-comments'])) { ?>\n\t\t";
            $output .= __("Comments will be enabled for this submission","tdomf");
            $output .= "\n\t<?php } else { ?>\n\t\t";
            $output .= __("Comments will be disabled for this submission","tdomf");
            $output .= "\n\t<?php } ?>";
        }
        
        if($options['user-pings']) {
            if($options['user-comments']) {
                $output .= "\n\t\t<br/>";
            }
            $output .= "\n\t<?php if(isset(\$post_args['comments-user-pings'])) { ?>\n\t\t";
            $output .= __("Pings and Trackbacks will be enabled for this submission","tdomf");
            $output .= "\n\t<?php } else { ?>\n\t\t";
            $output .= __("Pings and Trackbacks will be disabled for this submission","tdomf");
            $output .= "\n\t<?php } ?>";
        }
        $output .= $after_widget;
    }
    return $output;
  }
  tdomf_register_form_widget_preview_hack('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments_preview_hack', $modes = array('new'));

  function tdomf_widget_comments_adminemail($args) {
    $options = tdomf_widget_comments_get_options($args['tdomf_form_id']);
    extract($args);

    $output = "";

    $post = wp_get_single_post($post_ID, ARRAY_A);

    if($post['comment_status'] == 'closed') {
        $output .= __("Comments off","tdomf");
    } else {
        $output .= __("Comments on","tdomf");
    }
    $output .= "\n";

    if($post['ping_status'] == 'closed') {
        $output .= __("Pings and Trackbacks off","tdomf");
    } else {
        $output .= __("Pings and Trackbacks on","tdomf");
    }

    $output .= $after_widget;

    return $output;
  }
  tdomf_register_form_widget_adminemail('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments_adminemail', $modes = array('new'));
  
  
  function tdomf_widget_comments_post($args) {
    $options = tdomf_widget_comments_get_options($args['tdomf_form_id']);
    extract($args);
    
    $comment_status = get_option('default_comment_status');
    $ping_status = get_option('default_ping_status'); 
    
    if($options['overwrite']) {
         $comment_status = 'closed';
         if($options['comments']) {
             $comment_status = 'open';
         }
         $ping_status = 'closed';
         if($options['pings']) {
             $ping_status = 'open';
         }
    }
    
    if($options['user-comments']) {
         $comment_status = 'closed';
         if(isset($args['comments-user-comments'])) {
             $comment_status = 'open';
         }
    }
    
    if($options['user-pings']) {
         $ping_status = 'closed';
         if(isset($args['comments-user-pings'])) {
             $ping_status = 'open';
         }
    }
    
    if($options['overwrite'] || $options['user-pings'] || $options['user-comments']) {
         $post = array (
             "ID"             => $post_ID,
             "comment_status" => $comment_status,
             "ping_status"    => $ping_status,
             );
         wp_update_post($post);
    }
    
    return NULL;
  }
  tdomf_register_form_widget_post('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments_post', $modes = array('new'));

    function tdomf_widget_comments_get_options($form_id) {
        $options = tdomf_get_option_widget('tdomf_comment_widget',$form_id);
        if($options == false) {
           $options = array();
           $options['title'] = "";
           $options['overwrite'] = false;
           $options['comments'] = get_option('default_comment_status');
           $options['pings'] = get_option('default_ping_status'); 
           $options['user-comments'] = false;
           $options['user-pings'] = false;
        }
      return $options;
    }
  
  function tdomf_widget_comments_control($form_id) {
      $options = tdomf_widget_comments_get_options($form_id);
  
  // Store settings for this widget
    if ( $_POST['excerpt-submit'] ) {
     $newoptions['title'] = strip_tags(stripslashes($_POST['comments-title']));
     $newoptions['overwrite'] = isset($_POST['comments-overwrite']);
     $newoptions['comments'] = isset($_POST['comments-comments']);
     $newoptions['pings'] = isset($_POST['comments-pings']); 
     $newoptions['user-comments'] = isset($_POST['comments-user-comments']);
     $newoptions['user-pings'] = isset($_POST['comments-user-pings']);
     if ( $options != $newoptions ) {
        $options = $newoptions;
        tdomf_set_option_widget('tdomf_comment_widget', $options,$form_id);
     }
  }

   // Display control panel for this widget
  
  extract($options);
  
        ?>
<div>
<label for="comments-title" style="line-height:35px;display:block;"><?php _e("Title: ","tdomf"); ?><input type="textfield" id="comments-title" name="comments-title" value="<?php echo htmlentities($options['title'],ENT_QUOTES,get_bloginfo('charset')); ?>" /></label>

<br/>

<label for="overwrite" style="line-height:35px;"><b><?php _e("Overwrite Default Settings","tdomf"); ?></b></label> 
<input type="checkbox" name="comments-overwrite" id="comments-overwrite" <?php if($options['overwrite']) echo "checked"; ?> >
<br/>
<small><?php _e("You can overwrite the default settings for comments pings for any submission from this form by enabling this option and setting defaults below","tdomf"); ?></small>
<br/>
<input type="checkbox" name="comments-comments" id="comments-comments" <?php if($options['comments']) echo "checked"; ?> >
<label for="overwrite" style="line-height:35px;"><?php _e("Allow Comments on Submission","tdomf"); ?></label> 
<br/>
<input type="checkbox" name="comments-pings" id="comments-pings" <?php if($options['pings']) echo "checked"; ?> >
<label for="overwrite" style="line-height:35px;"><?php _e("Allow Pings and Trackbacks on Submission","tdomf"); ?></label> 

<br/><br/>
<small><?php _e("Use these options to allow submitters to set if they want comments or pings on their submission. This will overwrite any other configuration.","tdomf"); ?></small>
<br/>
<input type="checkbox" name="comments-user-comments" id="comments-user-comments" <?php if($options['user-comments']) echo "checked"; ?> >
<label for="overwrite" style="line-height:35px;"><?php _e("Submitter can choose to allow Comments","tdomf"); ?></label> 
<br/>
<input type="checkbox" name="comments-user-pings" id="comments-user-pings" <?php if($options['user-pings']) echo "checked"; ?> >
<label for="overwrite" style="line-height:35px;"><?php _e("Submitter can choose to allow Pings and Trackbacks","tdomf"); ?></label>

</div>
        <?php 
}
tdomf_register_form_widget_control('comments',__('Comments Management',"tdomf"), 'tdomf_widget_comments_control', 700, 400, $modes = array('new'));
  
?>