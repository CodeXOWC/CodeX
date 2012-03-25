<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

/////////////////////////////////////////////////////////////
// Code for the tdomf edit form menu (with drag and drop!) //
/////////////////////////////////////////////////////////////

// Hacked the drag and drop from the widget admin menu in WP2.2!

// Only load edit scripts as needed!
//
function tdomf_load_edit_form_scripts() {
  // Need these scripts for drag and drop but only on this page, not every page!
  if(tdomf_wp30())
  {
      wp_enqueue_script( 'jquery-ui-core' );
      wp_enqueue_script( 'jquery-ui-draggable' );
      wp_enqueue_script( 'jquery-ui-sortable' );
  }
  else
  {
      wp_enqueue_script( 'interface' );
  }
}
add_action("load-".sanitize_title(__('TDO Mini Forms', 'tdomf'))."_page_tdomf_show_form_menu","tdomf_load_edit_form_scripts");


// Stuff to do in the header of the page
//
function tdomf_form_admin_head() {
   global $tdomf_form_widgets, $tdomf_form_widgets_control;
   $form_id = tdomf_edit_form_form_id();
   $mode = tdomf_generate_default_form_mode($form_id);
   do_action('tdomf_control_form_start',$form_id,$mode);
   $widgets = tdomf_filter_widgets($mode,$tdomf_form_widgets);
   $widgets_control = tdomf_filter_widgets($mode,$tdomf_form_widgets_control);
   if(preg_match('/tdomf_show_form_menu/',$_SERVER['REQUEST_URI'])) {
?>
   <?php if(tdomf_wp23() && function_exists('wp_admin_css')) {
            wp_admin_css( 'css/widgets' ); 
         } else if(!tdomf_wp25()) { 
            // pre-Wordpress 2.3
            ?>
            <link rel="stylesheet" href="widgets.css?version=<?php bloginfo('version'); ?>" type="text/css" />
         <?php } else { ?>
            <style type='text/css' >
            body {
	height: 100%;
}

#sbadmin #zones {
	-moz-user-select: none;
	-khtml-user-select: none;
	user-select: none;
}

#sbreset {
	float: left;
	margin: 1px 0;
}

.dropzone {
	border: 1px solid #bbb;
	float: left;
	margin-right: 10px;
	padding: 5px;
	background-color: #f0f8ff;
}

.dropzone h3 {
	text-align: center;
	color: #333;
}

.dropzone input {
	display: none;
}

.dropzone ul {
	float: left;
	list-style-type: none;
	width: 240px;
	margin: 0;
	min-height: 200px;
	padding: 0;
	display: block;
}

* .module {
	width: 238px;
	padding: 0;
	margin: 5px 0;
	cursor: move;
	display: block;
	border: 1px solid #ccc;
	background-color: #fbfbfb;
	position: relative;
	text-align: left;
	line-height: 25px;
}

* .handle {
	display: block;
	width: 216px;
	padding: 0 10px;
	position: relative;
	border-top: 1px solid #f2f2f2;
	border-right: 1px solid #e8e8e8;
	border-bottom: 1px solid #e8e8e8;
	border-left: 1px solid #f2f2f2;
}

* .popper {
	margin: 0;
	display: inline;
	position: absolute;
	top: 3px;
	right: 3px;
	overflow: hidden;
	text-align: center;
	height: 16px;
	font-size: 18px;
	line-height: 14px;
	cursor: pointer;
	padding: 0 3px 1px;
	border-top: 4px solid #6da6d1;
	background: url( ../images/fade-butt.png ) -5px 0px;
}

* html .popper {
	padding: 1px 6px 0;
	font-size: 16px;
}

#sbadmin p.submit {
	padding-right: 10px;
	clear: left;
}

.placemat {
	cursor: default;
	margin: 0;
	padding: 0;
	position: relative;
}

.placemat h4 {
	text-align: center;
}

.placemat span {
	background-color: #ffe;
	border: 1px solid #ccc;
	padding: 0 10px 10px;
	position: absolute;
	text-align: justify;
}

<?php if(tdomf_wp30()) { ?>
#zones {
    float: left;
}
#palette {
    min-height: 50px;
}
<?php } ?>

#palettediv {
	border: 1px solid #bbb;
	background-color: #f0f8ff;
	height:auto;
<?php if(tdomf_wp30()) { ?>
	float: left;
	padding: 10px;
	padding-right: 20px;
	margin-right: 50px;
<?php } else { ?>
	margin-top: 10px;
	padding-bottom: 10px;
<?php } ?>	
}


#palettediv:after, #zones:after, .dropzone:after {
	content: ".";
	display: block;
	height: 0;
	clear: both;
	visibility: hidden;
}

#palettediv, #zones, .dropzone {
	display: block;
	min-height: 1px;
}

* html #palettediv, * html #zones, * html .dropzone {
	height: 1%;
}

#palettediv h3 {
	text-align: center;
	color: #333;
	min-height: 1px;
}

#palettediv ul {
	padding: 0 0 0 10px;
}

<?php if(!tdomf_wp30()) { ?>
#palettediv .module {
	margin-right: 10px;
	float: left;
	width: 120px;
}

#palettediv .handle {
	height: 40px;
	font-size: 90%;
	width: 110px;
	padding: 0 5px;
}
<?php } ?>

#palettediv .popper {
	visibility: hidden;
}

* html #palettediv ul {
	margin: 0;
	padding: 0 0 0 10px;
}

#controls {
	height: 0px;
}

.control {
	position: absolute;
	display: block;
	background: #f9fcfe;
	padding: 0;
}

.controlhandle {
	cursor: move;
	background-color: #6da6d1;
	border-bottom: 2px solid #448abd;
	color: #333;
	display: block;
	margin: 0 0 5px;
	padding: 4px;
	font-size: 120%;
}

.controlcloser {
	cursor: pointer;
	font-size: 120%;
	display: block;
	position: absolute;
	top: 2px;
	right: 8px;
	padding: 0 3px;
	font-weight: bold;
}

.controlform {
	margin: 20px 30px;
  overflow: auto;
  <?php if(intval(get_option(TDOMF_OPTION_WIDGET_MAX_WIDTH)) != 0) { ?>
  width: <?php echo intval(get_option(TDOMF_OPTION_WIDGET_MAX_WIDTH)); ?>px;
  <?php } ?>
  <?php if(intval(get_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT)) != 0) { ?>
  height: <?php echo intval(get_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT)); ?>px;
  <?php } ?>
}

.controlform p {
	text-align: center;
}

.control .checkbox {
	border: none;
	background: transparent;
}

.hidden {
	display: none;
}

#tdomf_shadow {
	background: black;
	display: none;
	position: absolute;
	top: 0px;
	left: 0px;
	width: 100%;
}

#dragHelper {
	position: absolute;
}

#dragHelper li.module {
	display: block;
	float: left;
}
            </style>
         <?php } ?>
   
        <!--[if IE 7]>
        <style type="text/css">
                #palette { float: <?php echo ( get_bloginfo( 'text_direction' ) == 'rtl' ) ? 'right' : 'left'; ?>; }
        </style>
        <![endif]-->
   <script type="text/javascript">
   // <![CDATA[
	var cols = ['tdomf_form-1'];
	var widgets = [<?php foreach($widgets as $id => $w) { ?>'<?php echo $id; ?>',<?php } ?> ];
	var controldims = new Array;
  <?php $max_w = intval(get_option(TDOMF_OPTION_WIDGET_MAX_WIDTH));
        $max_h = intval(get_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT)); ?>
	<?php foreach($widgets_control as $id => $w) { ?>
      controldims['#<?php echo $id; ?>control'] = new Array;
      <?php if($max_w > 0) { ?>
        controldims['#<?php echo $id; ?>control']['width'] = <?php /*if($max_w < intval($w['width'])){*/ echo ($max_w + 40); /*} else { echo $w['width']; }*/ ?>;
      <?php } else { ?>
        controldims['#<?php echo $id; ?>control']['width'] = <?php echo $w['width']; ?>;
      <?php } ?>   
      <?php if($max_h > 0) { ?>
        controldims['#<?php echo $id; ?>control']['height'] = <?php /*if($max_h < intval($w['height'])){*/ echo ($max_h + 60); /*} else { echo $w['height']; }*/ ?>;
      <?php } else { ?>
        controldims['#<?php echo $id; ?>control']['height'] = <?php echo $w['height']; ?>;
      <?php } ?>
	<?php } ?>

      function initWidgets() {
        <?php foreach($widgets_control as $id => $w) { ?>
          jQuery('#<?php echo $id; ?>popper').click(function() {popControl('#<?php echo $id; ?>control');});
          jQuery('#<?php echo $id; ?>closer').click(function() {unpopControl('#<?php echo $id; ?>control');});
<?php if(tdomf_wp30()) { ?>		  
          jQuery('#<?php echo $id; ?>control').draggable({handle: '.controlhandle', zIndex: 1000});		  
<?php } else { ?>
          jQuery('#<?php echo $id; ?>control').Draggable({handle: '.controlhandle', zIndex: 1000});
<?php } ?>
          if ( true && window.opera )
            jQuery('#<?php echo $id; ?>control').css('border','1px solid #bbb');
        <?php } ?>

        jQuery('#tdomf_shadow').css('opacity','0');
        jQuery(widgets).each(function(o) {o='#widgetprefix-'+o; jQuery(o).css('position','relative');} );
	}
	function resetDroppableHeights() {
		var max = 6;
		jQuery.map(cols, function(o) {
			var c = jQuery('#' + o + ' li').length;
			if ( c > max ) max = c;
		});
		var maxheight = 35 * ( max + 1);
		jQuery.map(cols, function(o) {
			height = 0 == jQuery('#' + o + ' li').length ? maxheight - jQuery('#' + o + 'placemat').height() : maxheight;
			jQuery('#' + o).height(height);
		});
	}
	function maxHeight(elm) {
		htmlheight = document.body.parentNode.clientHeight;
		bodyheight = document.body.clientHeight;
		var height = htmlheight > bodyheight ? htmlheight : bodyheight;
		jQuery(elm).height(height);
	}
	function getViewportDims() {
		var x,y;
		if (self.innerHeight) { // all except Explorer
			x = self.innerWidth;
			y = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
			x = document.documentElement.clientWidth;
			y = document.documentElement.clientHeight;
		} else if (document.body) { // other Explorers
			x = document.body.clientWidth;
			y = document.body.clientHeight;
		}
		return new Array(x,y);
	}
	function dragChange(o) {
		var p = getViewportDims();
		var screenWidth = p[0];
		var screenHeight = p[1];
		var elWidth = parseInt( jQuery(o).css('width') );
		var elHeight = parseInt( jQuery(o).css('height') );
		var elLeft = parseInt( jQuery(o).css('left') );
		var elTop = parseInt( jQuery(o).css('top') );
		if ( screenWidth < ( parseInt(elLeft) + parseInt(elWidth) ) )
			jQuery(o).css('left', ( screenWidth - elWidth ) + 'px' );
		if ( screenHeight < ( parseInt(elTop) + parseInt(elHeight) ) )
			jQuery(o).css('top', ( screenHeight - elHeight ) + 'px' );
		if ( elLeft < 1 )
			jQuery(o).css('left', '1px');
		if ( elTop < 1 )
			jQuery(o).css('top', '1px');
	}
	function popControl(elm) {
		var x = ( document.body.clientWidth - controldims[elm]['width'] ) / 2;
		/*var y = ( document.body.parentNode.clientHeight - controldims[elm]['height'] ) / 2;*/
        var y = ( (topOffset = ( document.body.parentNode.clientHeight - controldims[elm]['height'] ) / 2) && (topOffset > 0)) ? topOffset : 0 ;
		jQuery(elm).css({display: 'block', width: controldims[elm]['width'] + 'px', height: controldims[elm]['height'] + 'px', position: 'absolute', right: x + 'px', top: y + 'px', zIndex: '1000' });
		jQuery(elm).attr('class','control');
		jQuery('#tdomf_shadow').click(function() {unpopControl(elm);});
		window.onresize = function(){maxHeight('#tdomf_shadow');dragChange(elm);};
		popShadow();
	}
	function popShadow() {
		maxHeight('#tdomf_shadow');
		jQuery('#tdomf_shadow').css({zIndex: '999', display: 'block'});
		jQuery('#tdomf_shadow').fadeTo('fast', 0.2);
	}
	function unpopShadow() {
		jQuery('#tdomf_shadow').fadeOut('fast', function() {jQuery('#tdomf_shadow').hide()});
	}
	function unpopControl(el) {
		jQuery(el).attr('class','hidden');
		jQuery(el).hide();
		unpopShadow();
	}
	function serializeAll() {
<?php if(tdomf_wp30()) { ?>	
    var serial1 = jQuery('ul#tdomf_form-1').sortable( "serialize", { key: "tdomf_form-1[]", expression: "widgetprefix\-(.+)" } );
	jQuery('#tdomf_form-1order').attr('value',serial1);	
<?php } else { ?>		
			var serial1 = jQuery.SortSerialize('tdomf_form-1');
		jQuery('#tdomf_form-1order').attr('value',serial1.hash.replace(/widgetprefix-/g, ''));
<?php } ?>
	}
	function updateAll() {
		jQuery.map(cols, function(o) {
			if ( jQuery('#' + o + ' li').length )
				jQuery('#'+o+'placemat span.handle').hide();
			else
				jQuery('#'+o+'placemat span.handle').show();
		});
		resetDroppableHeights();
	}
	jQuery(document).ready( function() {
		updateAll();
		initWidgets();
	});
// ]]>
</script>
<?php
   }
}
add_action( 'admin_head', 'tdomf_form_admin_head' );

function tdomf_edit_form_form_id() {
  if(isset($_REQUEST['form'])) {
    return intval($_REQUEST['form']);
  } else {
    return tdomf_get_first_form_id();
  }
  return false;
}

// Show the page!
//
function tdomf_show_form_menu() {
  global $wpdb, $wp_roles, $tdomf_form_widgets, $tdomf_form_widgets_control;
  
  tdomf_handle_editformmenu_actions();

  $form_ids = tdomf_get_form_ids();
  $form_id = tdomf_edit_form_form_id();
  
  tdomf_log_mem_usage(__FILE__,__LINE__);
  
  $widget_order = tdomf_get_option_form(TDOMF_OPTION_FORM_ORDER,$form_id);

  tdomf_log_mem_usage(__FILE__,__LINE__);
  
  $mode = tdomf_generate_default_form_mode($form_id);
  
  $widgets = tdomf_filter_widgets($mode,$tdomf_form_widgets);
  tdomf_log_mem_usage(__FILE__,__LINE__);
  $widgets_control = tdomf_filter_widgets($mode,$tdomf_form_widgets_control);
  tdomf_log_mem_usage(__FILE__,__LINE__);  
  
  do_action( 'tdomf_widget_page_top', $form_id, $mode );
  tdomf_log_mem_usage(__FILE__,__LINE__);
  
  ?>

   <?php $message = tdomf_get_error_messages(true,$form_id);
         if(!empty($message)) { ?>
         <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
        <?php } ?>

        <?php tdomf_forms_top_toolbar($form_id, 'tdomf_show_form_menu'); ?>
  
<div class="wrap">
		<h2><?php printf(__("Form Arrangement for Form %d: \"%s\"","tdomf"),$form_id,tdomf_get_option_form(TDOMF_OPTION_NAME,$form_id)); ?></h2>

        <?php tdomf_forms_under_title_toolbar($form_id, 'tdomf_show_form_menu'); ?>
    
		<p><?php _e('You can drag-drop, order and configure "widgets" for your form below. Most Widgets can be individually configured once you have dropped them on the form. Just click on the right-most icon on the Widget title. Widgets will be executed and displayed in order from top to bottom. If you wish to change the order they are displayed in but not executed in, please use the Form Hacker to do so.',"tdomf"); ?></p>

        <?php if(tdomf_get_option_form(TDOMF_OPTION_FORM_HACK,$form_id) != false) { ?>
            <p><font color='red'><?php _e('The Form Hacker has been set for this form. This means that any changes to widgets will not appear on the form until you reset the Form Hacker. Only the backend processing of the form will be affected by these changes.',"tdomf"); ?></font></p>
        <?php } ?>
        
		<form id="sbadmin" method="post" onsubmit="serializeAll();">
      
      <input type="hidden" id="tdomf-form-id" name="tdomf-form-id" value="<?php echo $form_id; ?>" />
    
			<p class="submit">
				<input type="submit" value="<?php _e("Save Changes &raquo;","tdomf"); ?>" />
			</p>
      
      <?php if(tdomf_wp25()) { ?>
      <br/><br/>
      <?php } ?>

<?php if(tdomf_wp30()) { ?>      
      <div id="palettediv">
        <h3>Available Widgets</h3>     
        <ul id="palette">
            <?php foreach($widgets as $id => $w) {
                    if ( !is_array( $widget_order ) || !in_array($id,$widget_order)) {?>
                        <li class="module" id="widgetprefix-<?php echo $id; ?>"><span class="handle"><?php echo $w['name']; ?> <?php if(isset($widgets_control[$id])) { ?><div class="popper" id="<?php echo $id; ?>popper" title="<?php _e("Configure","tdomf"); ?>">&#8801;</div><?php } ?></span></li>
            <?php } } ?>
        </ul> <!-- palette -->
      </div> <!-- palettediv -->      
<?php } ?>
      
      <div id="zones">
        <input type="hidden" id="tdomf_form-1order" name="tdomf_form-1order" value="" />
            <div class="dropzone">
                <h3>Your Form</h3>
                    <div id="tdomf_form-1placemat" class="placemat">
                        <span class="handle">
                            <h4>Default Form</h4>
                            <?php _e("Your form will be displayed using the default widget order. Dragging widgets into this box will replace the default with your customized form.","tdomf"); ?>
                            </span>
                    </div>

                    <ul id="tdomf_form-1">
                    <?php
                    if ( is_array( $widget_order ) ) {
                        foreach ( $widget_order as $id ) {
                            if(isset($widgets[$id]['name'])) { ?>
                            <li class="module" id="widgetprefix-<?php echo $id; ?>"><span class="handle"><?php echo $widgets[$id]['name']; ?> <?php if(isset($widgets_control[$id])) { ?><div class="popper" id="<?php echo $id; ?>popper" title="<?php _e("Configure","tdomf"); ?>">&#8801;</div><?php } ?></span></li>
                            <?php }
                        }
                    } ?>
                    </ul>
            </div> <!- dropzone ->
      </div> <!-- zones -->

<?php if(!tdomf_wp30()) { ?>
			<div id="palettediv">
				<h3>Available Widgets</h3>
				
				<ul id="palette">
        				
				<?php foreach($widgets as $id => $w) {
					if ( !is_array( $widget_order ) || !in_array($id,$widget_order)) {?>
					<li class="module" id="widgetprefix-<?php echo $id; ?>"><span class="handle"><?php echo $w['name']; ?> <?php if(isset($widgets_control[$id])) { ?><div class="popper" id="<?php echo $id; ?>popper" title="<?php _e("Configure","tdomf"); ?>">&#8801;</div><?php } ?></span></li>
				<?php } } ?>
				</ul>
			</div>
<?php } ?>

			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function(){
<?php if(tdomf_wp30()) { ?>				
								jQuery('ul#palette').sortable({
						accept: 'module', activeclass: 'activeDraggable', opacity: 0.8, revert: true, stop: updateAll, connectWith: 'ul#tdomf_form-1'
					});
								jQuery('ul#tdomf_form-1').sortable({
								        accept: 'module', activeclass: 'activeDraggable', opacity: 0.8, revert: true, stop: updateAll, connectWith: 'ul#palette'
					});
<?php } else { ?>	
								jQuery('ul#palette').Sortable({
						accept: 'module', activeclass: 'activeDraggable', opacity: 0.8, revert: true, onStop: updateAll
					});
								jQuery('ul#tdomf_form-1').Sortable({
						accept: 'module', activeclass: 'activeDraggable', opacity: 0.8, revert: true, onStop: updateAll
					});				
<?php } ?>
							});
			// ]]>
			</script>


			<p class="submit">
			<?php if(function_exists('wp_nonce_field')){ wp_nonce_field('tdomf-save-widget-order'); } ?>

            <input type="hidden" name="action" id="action" value="save_widget_order" />
				<input type="submit" value="<?php _e("Save Changes &raquo;","tdomf"); ?>" />
			</p>

			<div id="controls">
               <?php foreach($widgets_control as $id => $w) { ?>
			   <div class="hidden" id="<?php echo $id; ?>control">
				   <span class="controlhandle"><?php echo $widgets_control[$id]['name']; ?></span>
					<span id="<?php echo $id; ?>closer" class="controlcloser">&#215;</span>
					<div class="controlform">
						<?php call_user_func($w['cb'],$form_id,$widgets_control[$id]['params']); ?>
                  <input type="hidden" id="<?php echo $id; ?>-submit" name="<?php echo $id; ?>-submit" value="1" />
					</div>
				</div>
               <?php } ?>
         </form>

		<br class="clear" />
	</div>

	<div id="tdomf_shadow"> </div>

  <?php do_action( 'tdomf_widget_page_bottom', $form_id, $mode ); ?>
  
  <?php
}

// Handle actions
//
function tdomf_handle_editformmenu_actions() {

  tdomf_log_mem_usage(__FILE__,__LINE__);
    
 // get form id
  $form_id = false;
  if(isset($_REQUEST['tdomf-form-id'])) {
    $form_id = intval($_REQUEST['tdomf-form-id']);
  }
  
  #if (get_magic_quotes_gpc()) {
      if(!function_exists('stripslashes_array')) {
          function stripslashes_array($array) {
              return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
          }
      }
      #$_COOKIE = stripslashes_array($_COOKIE);
      #$_FILES = stripslashes_array($_FILES);
      $_GET = stripslashes_array($_GET);
      $_POST = stripslashes_array($_POST);
      $_REQUEST = stripslashes_array($_REQUEST);
      
      tdomf_log_mem_usage(__FILE__,__LINE__);
  #}

	if ( isset( $_POST['action'] ) && $form_id ) {
		switch( $_POST['action'] ) {
			case 'save_widget_order' :
			    check_admin_referer('tdomf-save-widget-order');
			    if(isset($_POST['tdomf_form-1order']) && !empty($_POST['tdomf_form-1order'])) {
					parse_str($_POST['tdomf_form-1order'],$widget_order);
					$widget_order = $widget_order['tdomf_form-1'];
	                tdomf_set_option_form(TDOMF_OPTION_FORM_ORDER,$widget_order,$form_id);
                    tdomf_log_mem_usage(__FILE__,__LINE__);
					tdomf_log_message_extra("Saved widget settings for form-$form_id: ".$_POST['tdomf_form-1order'],TDOMF_LOG_GOOD);
				} else {
					$widget_order = tdomf_get_form_widget_default_order($form_id);
					tdomf_set_option_form(TDOMF_OPTION_FORM_ORDER,false,$form_id);
                    tdomf_log_mem_usage(__FILE__,__LINE__);
					tdomf_log_message("Restored default settings for form-$form_id");
				}
        if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS) && tdomf_get_option_form(TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS,$form_id)) {
                ?> <div id="message" class="updated fade"><p><?php printf(__("Saved Settings. <a href='%s'>See your form &raquo</a>","tdomf"),"users.php?page=tdomf_your_submissions#tdomf_form$form_id"); ?></p></div> <?php
        } else {
                ?> <div id="message" class="updated fade"><p><?php _e("Saved Settings.","tdomf"); ?></p></div> <?php
        }
				break;
	 	}
	} else if( isset( $_POST['action'] ) ) {
    ?> <div id="message" class="updated fade"><p><font color='red'><?php _e("Please select a form to modify!","tdomf"); ?></font></p></div> <?php
  }
}

?>
