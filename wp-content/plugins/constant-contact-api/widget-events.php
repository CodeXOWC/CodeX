<?php


function constant_contact_load_events_widget()
{
	register_widget( 'constant_contact_events_widget' );
	
	// Check if form designer has called it yet...
	if(!function_exists('constant_contact_admin_widget_scripts')) {
		function constant_contact_admin_widget_scripts_events() {
			global $pagenow;
			if($pagenow == 'widgets.php') {
				wp_enqueue_script( 'admin-cc-widget', plugin_dir_url(__FILE__).'js/admin-cc-widget.js' );
			}
		}
		constant_contact_admin_widget_scripts_events();
	} else {
		constant_contact_admin_widget_scripts();
	}
	
}

add_filter('cc_event_enddate', 'cc_event_date');
add_filter('cc_event_startdate', 'cc_event_date');

function cc_event_date($Date = null) {
	 return constant_contact_event_date($Date);
}

class constant_contact_events_widget extends WP_Widget {

    /** constructor */
    function constant_contact_events_widget()
	{
		$widget_options = array(
			'description' => 'Displays a Constant Contact events widget listing upcoming events',
			'classname' => 'constant-contact-events',
		);
		$control_options = array('width'=>690); // Min-width of widgets config with expanded sidebar
        parent::WP_Widget(false, $name = 'Constant Contact Events', $widget_options, $control_options);	
		if (is_active_widget(false, false, $this->id_base, true) ) {
        	add_action('wp_print_styles', array(&$this, 'styles'));
        }
    }
	
	function styles() {
		if(isset($this->number) && is_integer($this->number)) {
			$settings = $this->get_settings();
			if((!empty($settings["{$this->number}"]['style']) && isset($settings["{$this->number}"]['widgethasbeensaved'])) || !isset($settings["{$this->number}"]['widgethasbeensaved'])) {
				wp_register_style( 'cc_events', plugin_dir_url(__FILE__).'css/events.css');
				wp_enqueue_style('cc_events');
			}
		}
	}
	
		
   /** @see WP_Widget::widget */
    function widget($args = array(), $instance = array())
	{
		global $cc; 
		if(!constant_contact_create_object()) { return false; }
		$output = '';
		extract($instance);
		$widget_title = $title; 
		$widget_description = $description;
        extract( $args );
        
		$output .= (isset($before_widget)) ? $before_widget : '';
		if(!empty($widget_title)) {
			$output .= (isset($before_title, $after_title)) ? $before_title : '<h2>';
			$output .= (isset($widget_title)) ? $widget_title : '';
			$output .= (isset($after_title, $before_title)) ? $after_title : '</h2>';
		}
		$output .= (!empty($widget_description)) ? "\n\t".'<div class="cc_event_description">'."\n\t\t".wpautop(wptexturize($widget_description)).'</div>' : '';
		
		$output .= constant_contact_get_events_output($instance, true);
		
		$output .= (isset($after_widget)) ? $after_widget : ''; 
		
		// Modify the output by calling add_filter('constant_contact_event_widget', 'your_function');
		// Passes the output to the function, needs return $output coming from the function.
		$output = apply_filters('constant_contact_event_widget_ouput', $output);
			
		echo $output;
		
		return;
    }
	
	function update($new, $old) {
		$new['widgethasbeensaved'] = true;
		return $new;
	}
	
	function r($print = null, $die = false) {
		echo '<pre>';
		print_r($print);
		echo '</pre>';
		if($die) { die(); }
		return;
	}
	
	function get_value($field, $instance) {
		if (isset ( $instance[$field])) { return esc_attr( $instance[$field] );}
		return '';
	}
		
    /** @see WP_Widget::form */
  /** @see WP_Widget::form */
    function form($instance)
	{
	
		extract($instance);
		
		@include_once('functions.php');
		$cc = constant_contact_create_object();
		
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
	?>
	<h3>Constant Contact Event Widget Settings</h3>
	<p class="howto">Note: only active events will be displayed. Completed or cancelled events will not be shown.</p>
	<a name="widget"></a>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('title');?>"><span>Widget Title</span></label></p></th>
			<td>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php echo $title; ?>" size="50" />
			<p class="description">The title text for the this widget.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('description');?>"><span>Widget Description</span></label></p></th>
			<td>
			<textarea class="widefat" name="<?php echo $this->get_field_name('description');?>" id="<?php echo $this->get_field_id('description');?>" cols="50" rows="4"><?php echo $description; ?></textarea>
			<p class="description">The description text displayed in the sidebar widget before the events. HTML allowed. Paragraphs will be added automatically like in posts.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('limit');?>"><span><span title="Number">#</span> of Events Shown</span></label></p></th>
			<td>
				<?php $limit = isset($limit) ? $limit : null; ?>
				<select class="select" name="<?php echo $this->get_field_name('limit');?>" id="<?php echo $this->get_field_id('limit');?>">
					<option value="1"<?php selected($limit, 1); ?>>1</option>
					<option value="2"<?php selected($limit, 2); ?>>2</option>
					<option value="3"<?php selected($limit, 3); selected($limit, null) ?>>3</option>
					<option value="4"<?php selected($limit, 4); ?>>4</option>
					<option value="5"<?php selected($limit, 5); ?>>5</option>
					<option value="6"<?php selected($limit, 6); ?>>6</option>
					<option value="7"<?php selected($limit, 7); ?>>7</option>
					<option value="8"<?php selected($limit, 8); ?>>8</option>
					<option value="9"<?php selected($limit, 9); ?>>9</option>
					<option value="10"<?php selected($limit, 10); ?>>10</option>
				</select>
			
			<?php echo $description; ?></textarea>
			<p class="description">The number of events to show at once.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label><span><?php _e('Display Options', 'constantcontactapi'); ?></span></label></p></th>
			<td>
			<?php
				$fields = array(
					array(
						'id' => 'showdescription',
						'label' => 'Show event Description',
						'default' => true
					),
					array(
						'id' => 'datetime',
						'label' => 'Show event Date & Time',
						'default' => true
					),
					array(
						'id' => 'location',
						'label' => 'Show event Location',
						'default' => false
					),
					array(
						'id' => 'map',
						'label' => 'Show map link for Location (if Location is shown)',
						'default' => false
					),
					array(
						'id' => 'calendar',
						'label' => 'Show "Add to Calendar" link',
						'default' => false
					),					
					array(
						'id' => 'directtoregistration',
						'label' => 'Link directly to registration page, rather than event homepage',
						'default' => false
					),
					array(
						'id' => 'newwindow',
						'label' => 'Open event links in a new window',
						'default' => false
					),
					array(
						'id' => 'style',
						'label' => '<strong>Use plugin styles</strong>. Disable if you want to use your own styles (CSS)',
						'default' => true
					)
				);
				foreach($fields as $field) {
					if(!isset(${$field['id']}) && !isset($instance['widgethasbeensaved'])) { 
						${$field['id']} = $field['default']; 
					} elseif(!isset(${$field['id']}) && isset($instance['widgethasbeensaved'])) { 
						${$field['id']} = false; 
					}
				?>
				<p>
					<input type="hidden" name="<?php echo $this->get_field_name($field['id']);?>" value="0" />
					<label class="howto" for="<?php echo $this->get_field_id($field['id']);?>"><input <?php checked(${$field['id']}, 1); ?> type="checkbox" class="checkbox" name="<?php echo $this->get_field_name($field['id']);?>" value="1" id="<?php echo $this->get_field_id($field['id']);?>" /> <span><?php echo wptexturize($field['label'], false); ?></span></label>
				</p>
				<?php } ?>
			</td>
		</tr>
	</table>
	<?php
    }

} // class constant_contact_api_widget

if(!function_exists('tempty')) {
	function tempty($val) { 
		$val = trim($val);
    	return empty($val) && $val !== 0; 
	}
}
	
?>