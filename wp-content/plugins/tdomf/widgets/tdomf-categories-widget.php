<?php
/*
Name: "Categories"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: This widget allows users to select categories for their submissions
Version: 0.7
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

# TODO: size of list option
# TODO: multiple default categories option
# TODO: include only categories option

/////////////////////////////////////
// How many widgets do you want?
//
function tdomf_widget_categories_number_bottom($form_id,$mode){
  if(strpos($mode,'new-post') !== false) {
    $form_id = tdomf_edit_form_form_id();
    $count = tdomf_get_option_widget('tdomf_categories_widget_count',$form_id);
    if($count <= 0){ $count = 1; } 
    $max = tdomf_get_option_form(TDOMF_OPTION_WIDGET_INSTANCES,$form_id);
    if($max == false){ $max = 9; }
    if($count <= 0){ $count = 1; }
    if($count > ($max+1)){ $count = ($max+1); }
    
    if($max > 1) {
    ?>
    <div class="wrap">
      <form method="post">
        <h2><?php _e("Categories Widgets","tdomf"); ?></h2>
        <p style="line-height: 30px;"><?php _e("How many Categories widgets would you like?","tdomf"); ?>
        <select id="tdomf-widget-categories-number" name="tdomf-widget-categories-number" value="<?php echo $count; ?>">
        <?php for($i = 1; $i < ($max+1); $i++) { ?>
          <option value="<?php echo $i; ?>" <?php if($i == $count) { ?> selected="selected" <?php } ?>><?php echo $i; ?></option>
        <?php } ?>
        </select>
        <span class="submit">
          <input type="submit" value="Save" id="tdomf-widget-categories-number-submit" name="tdomf-widget-categories-number-submit" />
        </span>
        </p>
      </form>
    </div>
    <?php 
    }
  }
}
add_action('tdomf_widget_page_bottom','tdomf_widget_categories_number_bottom',10,2);

// TODO: Update multi-widget init

function tdomf_widget_categories_handle_number($form_id,$mode) {
  if(tdomf_form_exists($form_id) && strpos($mode,'new-post') !== false) {   
     if (isset($_POST['tdomf-widget-categories-number-submit']) ) {
       $count = $_POST['tdomf-widget-categories-number'];
       if($count > 0){ tdomf_set_option_widget('tdomf_categories_widget_count',$count,$form_id); }
     }
  }
}
#add_action('tdomf_widget_page_top','tdomf_widget_categories_handle_number',10,2);
add_action('tdomf_control_form_start','tdomf_widget_categories_handle_number',10,2);

///////////////////////////////////////
// Initilise multiple category widgets!
//
function tdomf_widget_categories_init($form_id,$mode){
  if(tdomf_form_exists($form_id) && strpos($mode,'new-post') !== false) {   
     $count = tdomf_get_option_widget('tdomf_categories_widget_count',$form_id);
     $max = tdomf_get_option_form(TDOMF_OPTION_WIDGET_INSTANCES,$form_id);
     if($max <= 1){ $count = 1; }
     else if($count > ($max+1)){ $count = $max + 1; }
     
     tdomf_register_form_widget("categories","Categories 1", 'tdomf_widget_categories', array(), 1);
     tdomf_register_form_widget_hack("categories","Categories 1", 'tdomf_widget_categories', array(), 1);
     tdomf_register_form_widget_control("categories", "Categories 1",'tdomf_widget_categories_control', 370, 610, array(), 1);
     tdomf_register_form_widget_preview("categories", "Categories 1",'tdomf_widget_categories_preview', array(), 1);
     tdomf_register_form_widget_preview_hack("categories", "Categories 1",'tdomf_widget_categories_preview_hack', array(), 1);
     tdomf_register_form_widget_post("categories", "Categories 1",'tdomf_widget_categories_post', array(), 1);
     tdomf_register_form_widget_adminemail("categories", "Categories 1",'tdomf_widget_categories_adminemail', array(), 1);
     tdomf_register_form_widget_admin_error("categories", "Categories 1",'tdomf_widget_categories_admin_error', array(), 1);
     
     for($i = 2; $i <= $count; $i++) {
       tdomf_register_form_widget("categories-$i","Categories $i", 'tdomf_widget_categories', array(), $i);
       tdomf_register_form_widget_hack("categories-$i","Categories $i", 'tdomf_widget_categories', array(), $i);       
       tdomf_register_form_widget_control("categories-$i", "Categories $i",'tdomf_widget_categories_control', 370, 610, array(), $i);
       tdomf_register_form_widget_preview("categories-$i", "Categories $i",'tdomf_widget_categories_preview', array(), $i);
       tdomf_register_form_widget_preview_hack("categories-$i", "Categories $i",'tdomf_widget_categories_preview_hack', array(), $i);
       tdomf_register_form_widget_post("categories-$i", "Categories $i",'tdomf_widget_categories_post', array(), $i);
       tdomf_register_form_widget_adminemail("categories-$i", "Categories $i",'tdomf_widget_categories_adminemail', array(), $i);
       tdomf_register_form_widget_admin_error("categories-$i", "Categories $i",'tdomf_widget_categories_admin_error', array(), $i);
     }
  }
}
add_action('tdomf_create_post_start','tdomf_widget_categories_init',10,2);
add_action('tdomf_generate_form_start','tdomf_widget_categories_init',10,2);
add_action('tdomf_preview_form_start','tdomf_widget_categories_init',10,2);
add_action('tdomf_control_form_start','tdomf_widget_categories_init',10,2);
add_action('tdomf_widget_page_top','tdomf_widget_categories_init',10,2);

// Get Options for this widget
//
function tdomf_widget_categories_get_options($number = 1,$form_id = 1) {
  $postfix = "";
  if($number != 1){ $postfix = "_$number"; }
  $options = tdomf_get_option_widget('tdomf_categories_widget'.$postfix,$form_id);
    if($options == false) {
       $options = array();
       $options['title'] = '';
       $options['overwrite'] = false;
       $options['display'] = "dropdown";
       $options['multi'] = false;
       $options['hierarchical'] = true;
       $options['include'] = "";
       $options['exclude'] = "";
       $options['order'] = 'asc';
       $options['orderby'] = 'ID';
    }
    if(!isset($options['order'])) { $options['order'] = 'asc'; }
    if(!isset($options['orderby'])) { $options['order'] = 'ID'; }
  return $options;
}


//////////////////////////////
// Display widget
//
function tdomf_widget_categories($args,$params) {
  $number = 1;
  if(is_array($params) && count($params) >= 1){
     $number = $params[0];
  }
  $options = tdomf_widget_categories_get_options($number,$args['tdomf_form_id']);
  $postfix = "";
  if($number != 1){ $postfix = "-$number"; }
  
  $defcat = tdomf_get_option_form(TDOMF_DEFAULT_CATEGORY,$args['tdomf_form_id']);
  if(isset($args["categories$postfix"])) {
    $defcat = $args["categories$postfix"];
  } else if(isset($options['include']) && !empty($options['include'])) {
    $includes = explode(',',$options['include']);
    if(!empty($includes)){
        $defcat = $includes[0];
    }
  } else {
    // check if defcat is in the exclude list:
    $excludes = explode(',',$options['exclude']);
    if(in_array($defcat,$excludes)) {
        // need to pick a "new" default
        $cats = get_categories(array( 'exclude' => $options['exclude'],
                                      'hide_empty' => false ));
        if(!empty($cats)) {
            $defcat = $cats[0]->term_id;
        } # else ERROR: no categories to select from! 
    }
  }

  extract($args);
 
  $hack = false;
  if(strpos($mode,'-hack') !== false) {
     $hack = true;
  } 
  
  $output  = $before_widget;  

  if($hack) {
      $output .= "\t\t<?php \$defcat = $defcat; ";
      $output .= "if(isset(\$post_args['categories$postfix'])) { ";
      $output .= "\$defcat = \$post_args['categories$postfix']; } ?>\n";
  }
  
  if(!empty($options['title'])) {
    $output .= $before_title.$options['title'].$after_title;
  }
  
  $name = "categories$postfix";
  if($options['multi']) {
    $name = 'categories'.$postfix.'[]';
  }

  # no point putting a label as we can't use "id" on ul or li or combobox
  $output .= "\t\t".__('Select a category:','tdomf')." \n";
   
  $excludes = $options['exclude'];
  if(!empty($options['include'])) {
      $includes = explode(',',trim($options['include']));
      $excludes = "";
      $cats = get_categories(array('hide_empty' => false, 
                                   'order'      => $options['order'],
                                   'orderby'    => $options['orderby'] ));
      foreach($cats as $cat) {
          if(!in_array($cat->term_id,$includes)) {
              $excludes .= $cat->term_id . ",";
          }
      }
  }
  
  $catargs = array( 'exclude'          => $excludes,
                    'hide_empty'       => false, 
                    'hierarchical'     => $options['hierarchical'], 
                    'echo'             => false,
                    'name'             => $name,
                    'class'            => "tdomf_categories$postfix",
                    'multiple'         => $options['multi'],
                    'selected'         => $defcat,
                    'mode'             => $mode,
                    'hack'             => $hack,
                    'order'            => $options['order'],
                    'orderby'          => $options['orderby'] );
    
  if($options['display'] == "dropdown" ) {
      
    $catargs['size'] = 1;
    $output .= tdomf_dropdown_categories($catargs);
    
  } else if($options['display'] == "checkbox") {
    
    $output .= "\t\t<br/>\n";
    $output .= "\t\t<ul class='tdomf_category_checklist' >\n";
    $catargs['class'] = 'tdomf_categorychecklist';
    if($options['multi'] && !is_array($defcat)) { $defcat = array( $defcat ); }
    $output .= tdomf_category_checklist(0, 0, $defcat, $catargs);
    $output .= "\t\t</ul>\n";
    
  } else { # list
    
    $output .= "\t\t<br/>\n";
    $catargs['size'] = 5;
    $output .= tdomf_dropdown_categories($catargs);
    
  }
  
  $output .= $after_widget;
  return $output;
}
  
///////////////////////////////////////////////////
// A stripped-down clone of wp_category_checklist from WP includes/template.php, with return instead of echo and the extra parameter for excluded cats
//
function tdomf_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false,  $catargs) {
	$walker = new tdomf_Walker_Category_Checklist;
	$descendants_and_self = (int) $descendants_and_self;

	$args = array();

	#$args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
	$categories = get_categories($catargs);

    $args['selected_cats'] = $selected_cats;
    if($catargs['multi'] && !is_array($selected_cats)) {
        $args['selected_cats'] = array($selected_cats);
    }
    $args['class'] = $catargs['class'];
    $args['name'] = $catargs['name'];
    $args['hack'] = $catargs['hack'];
    $args['multiple'] = $catargs['multiple'];
    
    if ( $catargs['hierarchical'] )
        $depth = 0;  // Walk the full depth.
    else
      $depth = -1; // Flat.
    
	$args = array($categories, $depth, $args);
	$output = call_user_func_array(array(&$walker, 'walk'), $args);

	return $output;
}

class tdomf_Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", ($depth+4));
		$output .= "\n$indent<ul class='tdomf_category_children'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", ($depth+4));
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);

        $indent = str_repeat("\t", ($depth+4));
        $output .= "$indent<li>\n";
        if($multiple) {
            $_name = str_replace("[]","",$name);
            $output .= "$indent<input value=\"" . $category->term_id . "\" type=\"checkbox\" name=\"".$name."\""; /*id='".$name."'";*/
            if($hack) {
                // default
                if(in_array( $category->term_id, $selected_cats)) {
                    $output .= "<?php if( !isset( \$post_args['$_name'] ) || in_array( $category->term_id, \$post_args['$_name'] ) ) { ?> checked=\"checked\" <?php } ?>";
                } else {
                    $output .= "<?php if( isset( \$post_args['$_name'] ) && in_array( $category->term_id, \$post_args['$_name'] ) ) { ?> checked=\"checked\" <?php } ?>";
                }
            } else {
                $output .= (in_array( $category->term_id, $selected_cats ) ? ' checked="checked"' : "" );
            }
            $output .= '/> ' . wp_specialchars( apply_filters('the_category', $category->name ));
        } else {
            $output .= "$indent<input value=\"" . $category->term_id . "\" type=\"radio\" name=\"".$name."\""; /*id='".$name."'";*/
            if($hack) {
                // default
                if($category->term_id == $selected_cats) {
                    $output .= "<?php if( !isset(\$post_args['$name']) || $category->term_id == \$post_args['$name'] ) { ?> checked=\"checked\" <?php } ?>";
                }
                else {
                    $output .= "<?php if( isset(\$post_args['$name']) && $category->term_id == \$post_args['$name'] ) { ?> checked=\"checked\" <?php } ?>";
                }
            } else {
                $output .= ( $category->term_id == $selected_cats ? ' checked="checked"' : "" );
            }
            $output .= '/> ' . wp_specialchars( apply_filters('the_category', $category->name ));
        }
        $output .= "\n";
	}

    
    
	function end_el(&$output, $category, $depth, $args) {
        $indent = str_repeat("\t", ($depth+4));
		$output .= "$indent</li>\n";
	}
}

///////////////////////////////////////////////////
// Display and handle content widget control panel 
//
function tdomf_widget_categories_control($form_id,$params) {
  $number = 1;
  if(is_array($params) && count($params) >= 1){
     $number = $params[0];
  }
  $options = tdomf_widget_categories_get_options($number,$form_id);
  $postfix1 = "";
  $postfix2 = "";
  if($number != 1){ 
    $postfix1 = "-$number"; 
    $postfix2 = "_$number";
  }
  // Store settings for this widget
    if ( $_POST["categories$postfix1-submit"] ) {
       $newoptions['title'] = strip_tags($_POST["categories$postfix1-title"]);
       $newoptions['overwrite'] = isset($_POST["categories$postfix1-overwrite"]);
       $newoptions['multi'] = isset($_POST["categories$postfix1-multi"]);
       $newoptions['hierarchical'] = isset($_POST["categories$postfix1-hierarchical"]);
       $newoptions['include'] = str_replace(' ', '', strip_tags($_POST["categories$postfix1-include"]));
       $newoptions['exclude'] = str_replace(' ', '',strip_tags($_POST["categories$postfix1-exclude"]));
       $newoptions['display'] = $_POST["categories$postfix1-display"];
       $newoptions['order'] = $_POST["categories$postfix1-order"];
       $newoptions['orderby'] = $_POST["categories$postfix1-orderby"];
     if ( $options != $newoptions ) {
        $options = $newoptions;
        tdomf_set_option_widget('tdomf_categories_widget'.$postfix2, $options,$form_id);
        
     }
  }

   // Display control panel for this widget
   
        ?>
<div>

<p>A number of fields are disabled as they have not yet been implemented. They will be implemented in later releases of TDO Mini Forms</p>

<label for="categories<?php echo $postfix1; ?>-title" >
<?php _e("Title:","tdomf"); ?><br/></label>
<input type="text" size="40" id="categories<?php echo $postfix1; ?>-title" name="categories<?php echo $postfix1; ?>-title" value="<?php echo htmlentities($options['title'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/><br/>

<input type="checkbox" name="categories<?php echo $postfix1; ?>-overwrite" id="categories<?php echo $postfix1; ?>-overwrite" <?php if($options['overwrite']) { ?> checked <?php } ?> />
<label for="categories<?php echo $postfix1; ?>-overwrite">
<?php _e("Overwrite Default Categories","tdomf"); ?></label>
<br/><br/>

<input type="checkbox" name="categories<?php echo $postfix1; ?>-multi" id="categories<?php echo $postfix1; ?>-multi" <?php if($options['multi']) { ?> checked <?php } ?> />
<label for="categories<?php echo $postfix1; ?>-multi">
<?php _e("Allow users to select more than one category","tdomf"); ?>
</label>
<br/><br/>

<input type="checkbox" name="categories<?php echo $postfix1; ?>-hierarchical" id="categories<?php echo $postfix1; ?>-hierarchical" <?php if($options['hierarchical']) { ?> checked <?php } ?> />
<label for="categories<?php echo $postfix1; ?>-hierarchical">
<?php _e("Display categories in hierarchical mode","tdomf"); ?>
</label>
<br/><br/>

<label for="categories<?php echo $postfix1; ?>-include" >
<?php _e("List of categories to include (leave blank for all) (separate multiple categories with commas: 0,2,3) (overwrites exclude setting)","tdomf"); ?><br/></label>
<input type="text" size="40" id="categories<?php echo $postfix1; ?>-include" name="categories<?php echo $postfix1; ?>-include" value="<?php echo htmlentities($options['include'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/><br/>

<label for="categories<?php echo $postfix1; ?>-exclude" >
<?php _e("List of categories to exclude (separate multiple categories with commas: 0,2,3)","tdomf"); ?><br/></label>
<input type="text" size="40" id="categories<?php echo $postfix1; ?>-exclude" name="categories<?php echo $postfix1; ?>-exclude" value="<?php echo htmlentities($options['exclude'],ENT_QUOTES,get_bloginfo('charset')); ?>" />
<br/><br/>

<label for="categories<?php echo $postfix1; ?>-orderby" >
<?php _e("How the list of categories should be ordered",'tdomf'); ?><br/></label>
<input type="radio" name="categories<?php echo $postfix1; ?>-orderby" id="categories<?php echo $postfix1; ?>-orderby" value="ID" <?php if($options['orderby'] == 'ID'){ ?> checked <?php } ?>><?php _e("ID","tdomf"); ?><br>
<input type="radio" name="categories<?php echo $postfix1; ?>-orderby" id="categories<?php echo $postfix1; ?>-orderby" value="name" <?php if($options['orderby'] == 'name'){ ?> checked <?php } ?>><?php _e("Name","tdomf"); ?><br>
<br/>

<label for="categories<?php echo $postfix1; ?>-order" >
<?php _e("How the list of categories should be sorted",'tdomf'); ?><br/></label>
<input type="radio" name="categories<?php echo $postfix1; ?>-order" id="categories<?php echo $postfix1; ?>-order" value="asc" <?php if($options['order'] == 'asc'){ ?> checked <?php } ?>><?php _e("Ascending order","tdomf"); ?><br>
<input type="radio" name="categories<?php echo $postfix1; ?>-order" id="categories<?php echo $postfix1; ?>-order" value="desc" <?php if($options['order'] == 'desc'){ ?> checked <?php } ?>><?php _e("Descending order","tdomf"); ?><br>
<br/>

<label for"categories<?php echo $postfix1; ?>-display">
<?php _e("Display categtories as:","tdomf"); ?><br/></label>
<input type="radio" name="categories<?php echo $postfix1; ?>-display" id="categories<?php echo $postfix1; ?>-display" value="dropdown" <?php if($options['display'] == 'dropdown'){ ?> checked <?php } ?>><?php _e("Dropdown","tdomf"); ?><br>
<input type="radio" name="categories<?php echo $postfix1; ?>-display" id="categories<?php echo $postfix1; ?>-display" value="list" <?php if($options['display'] == 'list'){ ?> checked <?php } ?>><?php _e("List","tdomf"); ?><br>
<input type="radio" name="categories<?php echo $postfix1; ?>-display" id="categories<?php echo $postfix1; ?>-display" value="checkbox" <?php if($options['display'] == 'checkbox'){ ?> checked <?php } ?>><?php _e("Checkboxes","tdomf"); ?><br>
<br/><br/>

</div>
        <?php 
}


////////////////////////
// Preview categories
//
function tdomf_widget_categories_preview($args,$params) {
  
  // TODO: message will be displayed as many times as there is category widgets
  
  $number = 1;
  if(is_array($params) && count($params) >= 1){
     $number = $params[0];
  }
  $options = tdomf_widget_categories_get_options($number,$args['tdomf_form_id']);
  $postfix1 = "";
  if($number != 1){ 
    $postfix1 = "-$number"; 
  }
  
  extract($args);
  $output  = $before_widget;
  $cat_string = "";
  
  if(isset($args["categories$postfix1"])) {
      if($options['multi'] && is_array($args["categories$postfix1"])) {
        foreach($args["categories$postfix1"] as $cat) {
          $cat_string .= get_cat_name($cat).", ";
        }       
      } else {
        $cat_string .= get_cat_name($args["categories$postfix1"]);
      }
  } else {
      $cat_string .= __("No categories selected.", "tdomf");
  }
  
  $output .= sprintf(__("<b>This post will be categorized under</b>:<br/>%s","tdomf"), $cat_string);
  $output .= $after_widget;
  return $output;
}

////////////////////////
// Preview categories (Hacked)
//
function tdomf_widget_categories_preview_hack($args,$params) {
  
  $number = 1;
  if(is_array($params) && count($params) >= 1){
     $number = $params[0];
  }
  $options = tdomf_widget_categories_get_options($number,$args['tdomf_form_id']);
  $postfix1 = "";
  if($number != 1){ 
    $postfix1 = "-$number"; 
  }
  
  extract($args);
  $output  = $before_widget;
  
  $cat_string = "";
  if($options['multi']) {
    $cat_string .= "<?php if(isset(\$post_args['categories$postfix1']) && is_array(\$post_args['categories$postfix1'])) { ?>\n";
    $cat_string .= "\t\t<?php foreach(\$post_args['categories$postfix1'] as \$cat) { ?>\n";
    $cat_string .= "\t\t\t<?php echo get_cat_name(\$cat); ?>,\n";
    $cat_string .= "\t\t<?php } ?>";
  } else {
    $cat_string .= "<?php if(isset(\$post_args['categories$postfix1'])) { ?>\n";
    $cat_string .= "\t\t<?php echo get_cat_name(\$post_args['categories$postfix1']); ?>\n";
  }  
  $cat_string .= "<?php } else { ?>\n\t";
  $cat_string .= __("No categories selected.", "tdomf");
  $cat_string .= "\n<?php } ?>";
  
  $output .= sprintf(__("\t<b>This post will be categorized under</b>:\n\t<br/>\n%s","tdomf"), $cat_string);
  $output .= $after_widget;
  return $output;
}

////////////////////////
// Add categories to the post
//
function tdomf_widget_categories_post($args,$params) {
  $number = 1;
  if(is_array($params) && count($params) >= 1){
     $number = $params[0];
  }
  $options = tdomf_widget_categories_get_options($number,$args['tdomf_form_id']);
  $postfix1 = "";
  if($number != 1){ 
    $postfix1 = "-$number"; 
  }
  extract($args);

  // Grab existing data
  $post = wp_get_single_post($post_ID, ARRAY_A);
  $current_cats = $post['post_category'];
  
  // multiple selection
  $incoming_cats = array();
  if($options['multi'] && is_array($args["categories$postfix1"])) {
    $incoming_cats = $args["categories$postfix1"];
  } else {
    $incoming_cats = array( $args["categories$postfix1"] );
  }
  
  // Overwrite or append!
  if($options['overwrite']) {
    $post_cats = $incoming_cats;
  } else {
    $post_cats = array_merge( $current_cats, $incoming_cats );
  }

  // Update categories
  $post = array (
    "ID"            => $post_ID,
    "post_category" => $post_cats,
  );
  wp_update_post($post);
 
  return NULL;
}

///////////////////////////////////////////////////////////
// Show what categories are on the post to admins for moderating
//
function tdomf_widget_categories_adminemail($args,$params) {
  extract($args);

  // TODO: message will be displayed as many times as there is category widgets
  
  $cats_str = "";
  $cats = wp_get_post_categories($post_ID);
  foreach($cats as $cat) {
    $cats_str .= get_cat_name($cat);
  }
    
  extract($args);
  $output  = $before_widget;
  $output .= sprintf(__("This post will be categorized under:\r\n%s","tdomf"), $cats_str);  
  $output .= $after_widget;
  return $output;
}

function tdomf_dropdown_categories($args = '') {
  $defaults = array(
    'show_option_all' => '', 
    'show_option_none' => '',
    'orderby' => 'ID', 
    'order' => 'ASC',
    'show_last_update' => 0, 
    'show_count' => 0,
    'hide_empty' => false, 
    'child_of' => 0,
    'exclude' => '', 
    'echo' => false,
    'selected' => 0, 
    'hierarchical' => true,
    'multiple' => false,
    'size' => 5,
    'name' => 'tdomf_cats[]', 
    'class' => 'tdomf_cats',
    'width' => '',
    'mode'  => false,
    'hack'  => false
  );
  
  $defaults['selected'] = ( is_category() ) ? get_query_var('cat') : 0;
  
  $r = wp_parse_args( $args, $defaults );
  $r['include_last_update_time'] = $r['show_last_update'];
  extract( $r );
  
  $categories = get_categories($r);
  
  $output = '';
  if ( ! empty($categories) ) {
    
    if($size > count($categories)) { $size = count($categories); }
    
    $output = "\t\t<select name='$name' class='$class' size='$size' ";
    if($width != '') { $output .= "style='width:$width; '"; }
    if($multiple) { $output .= "multiple='multiple' "; }
    $output .= " >\n";
    
    if ( $show_option_all ) {
      $show_option_all = apply_filters('list_cats', $show_option_all);
      $output .= "\t\t\t<option value='0'>$show_option_all</option>\n";
    }
    
    if ( $show_option_none) {
      $show_option_none = apply_filters('list_cats', $show_option_none);
      $output .= "\t\t\t<option value='-1'>$show_option_none</option>\n";
    }
    
    if ( $hierarchical )
      $depth = 0;  // Walk the full depth.
    else
      $depth = -1; // Flat.
    
    $output .= tdomf_walk_category_dropdown_tree($categories, $depth, $r);
    $output .= "\t\t</select>\n";
  }
  
  $output = apply_filters('wp_dropdown_cats', $output);
  
  if ( $echo )
  echo $output;
  
  return $output;
}

function tdomf_walk_category_dropdown_tree() {
	$walker = new tdomf_Walker_CategoryDropdown;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

class tdomf_Walker_CategoryDropdown extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_el($output, $category, $depth, $args) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$output .= "\t\t\t<option value=\"".$category->term_id."\"";
        if(!$args['hack']) {
            if( (is_array($args['selected']) && in_array($category->term_id, $args['selected'])) 
                || ( $category->term_id == $args['selected'] ) )
			    $output .= ' selected="selected"';
        } else {
            $output .= "<?php if( (is_array(\$defcat) && in_array($category->term_id, \$defcat)) 
            || ( $category->term_id == \$defcat ) ) { echo ' selected=\"selected\" '; } ?>\n";
        }
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>\n";

		return $output;
	}
}

function tdomf_widget_categories_admin_error($form_id,$params) {
    
  $number = 0;
  if(is_array($params) && count($params) >= 1){
     $number = $params[0];
  }
  $options = tdomf_widget_categories_get_options($number,$form_id);

  $output = "";  
  if(!empty($options['include'])) {
      $includes = explode(',',trim($options['include']));
      $cats = get_categories(array('hide_empty' => false ));
      $real_cats = array();
      foreach($cats as $cat) {
          if(in_array($cat->term_id,$includes)) {
              $real_cats[] = $cat->term_id;
          }
      }
      if(empty($real_cats)) {
          $output .= sprintf(__('<b>Error</b>: Widget "Categories #%d" categories listed in include field are not valid. No categories are selected!','tdomf'),$number);
      } else if(count($includes) != count($real_cats)){
          $not_real_cats = "";
          foreach($includes as $inc) {
              if(!in_array($inc,$real_cats)) {
                  $not_real_cats .= $inc . ", ";
              }
          }
          $output .= sprintf(__('<b>Warning</b>: Categories %s set as include categories in Widget "Categories #%d" are not valid categories!','tdomf'),$not_real_cats,$number);
      }
  } else {
      $excludes = explode(',',$options['exclude']);
      $cats = get_categories(array( 'exclude' => $options['exclude'],
                                    'hide_empty' => false ));
      if(empty($cats)) {
          $output .= sprintf(__('<b>Error</b>: Widget "Categories #%d" exclude field values means that there are not categories to select!','tdomf'),$number);
      }  
  }
  
  return $output;
}

?>