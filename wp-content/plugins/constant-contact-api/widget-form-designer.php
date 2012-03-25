<?php // $Id$
/**
 * constant_contact_form_widget Class
 */


class constant_contact_form_widget extends WP_Widget {

    /** constructor */
    function constant_contact_form_widget()
	{
		if(!defined('CC_FILE_PATH')) {
			return false;
		}
		$this->debug = false;
		
		require_once CC_FILE_PATH . 'form-designer-functions.php';

		$widget_options = array(
			'description' => 'Displays a Constant Contact signup form to your visitors',
			'classname' => 'constant-contact-form',
		);
		$control_options = array('width'=>690); // Min-width of widgets config with expanded sidebar
        parent::WP_Widget(false, $name = 'Constant Contact Form Designer', $widget_options, $control_options);	
        
        add_action('wp_print_styles', array(&$this, 'print_styles'));
    }
	
	
	function update( $new_instance, $old_instance ) {
		delete_transient('cc_api');
		return $new_instance;
	}

	function print_styles() {
			if(is_admin()) { return; }
			$settings = $this->get_settings();
			$usedStyles = array();
			foreach($settings as $instance) {
				extract($instance);
				if(!isset($instance['formid']) || in_array($formid, $usedStyles) || $formid === 0) { continue; }
				$usedStyles[] = $formid; // We don't need to echo the same styles twice
			}
	}
	
   /** @see WP_Widget::widget */
    function widget($args = array(), $instance = array(), $echo = true)
	{

		$form = constant_contact_public_signup_form($instance, false);
		
		if(!$form) {
			if((is_user_logged_in() && current_user_can('install_plugins'))) {
				_e('<div style="background-color: #FFEBE8; padding:10px 10px 0 10px; font-size:110%; border:3px solid #c00; margin:10px 0;"><h3><strong>Admin-only Notice</strong></h3><p>The Form Designer is not working because of server configuration issues.</p><p>Contact your web host and request that they "whitelist your domain for ModSecurity"</p></div>');
			} else {
				_e('<!-- Form triggered error. Log in and refresh to see additional information. -->');
			}
			return false;
		}
		
		$output = '';
		
		/**
		 * Extract $args array into individual variables
		 */
    		extract( $args );
    	
    	/**
		 * Extract $instance array into individual variables
		 */
    		extract( $instance );

		/**
		 * Prepare the widget title and description
		 */
		$widget_title = empty($title) ? '' : apply_filters('widget_title', $title);

		/**
		 * Begin HTML output of widget
		 */
		$output .= (isset($before_widget)) ? $before_widget : '';
		$output .= (isset($before_title, $after_title)) ? $before_title : '<h2>';
		$output .= (isset($widget_title)) ? $widget_title : '';
		$output .= (isset($after_title, $before_title)) ? $after_title : '</h2>';
	
		$output .= apply_filters('cc_widget_description', $description);
		
		/**
		 * Display the public signup form
		 * Pass in widget $args, they should match the ones expected by constant_contact_public_signup_form()
		 */
		
		$output .= $form;
		

		$output .= (isset($after_widget)) ? $after_widget : '';

		
		echo $output;
    }
	
	
	function r($content, $kill = false) {
		echo '<pre>'.print_r($content,true).'</pre>';
		if($kill) { die(); }
	}
	
	function get_value($field, $instance) {
		if (isset ( $instance[$field])) { return esc_attr( $instance[$field] );}
		return false;
	}
	
	function get_form_list_select($instance) {
		$forms = get_option('cc_form_design');
		
		$output = '';
		$output .= '<select name="'.$this->get_field_name('formid').'" id="'.$this->get_field_id('formid').'">';
		$output .= '<option value="">Select a Form Design</option>';
#		$output .= '<optgroup label="Preset">';
#		$output .= '<option value="11111"'.selected($this->get_value('formid', $instance), '11111', false).'>No Design / Only Email</option>';
#		$output .= '<option value="22222"'.selected($this->get_value('formid', $instance), '22222', false).'>No Design / Name &amp; Email</option>';
		if(!empty($forms)) {
#			$output .= '<optgroup label="Your Custom Forms">';
			$previous_names = array();
			foreach($forms as $form) {
				
				$name = isset($form['form-name']) ? $form['form-name'] : 'Form '+$key;
				
				$form['truncated_name'] = stripcslashes(trim( wp_html_excerpt( $name, 50 ) ));
				if ( isset($form['form-name']) && $form['truncated_name'] != $form['form-name'])
					$form['truncated_name'] .= '&hellip;';
				
				if(!in_array(sanitize_key( $name ), $previous_names)) { 
					$previous_names[] = sanitize_key( $name );
				} else {
					$namekey = sanitize_key( $name );
					$previous_names[$namekey] = isset($previous_names[$namekey]) ? ($previous_names[$namekey] + 1) : 1;
					$form['truncated_name'] .= ' ('.$previous_names[$namekey].')';
				}
				
				if(!empty($form)) {
					$output .= "<option value=\"{$form['cc-form-id']}\"".selected($this->get_value('formid', $instance), $form['cc-form-id'], false).">{$form['truncated_name']}</option>";	
				}
			}
		}
#		$output .= '</optgroup>';
		$output .= '</select>';
		
		return $output;
	}
	
    /** @see WP_Widget::form */
    function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'show_firstname' => 1, 'show_lastname' => 1, 'description' => false, 'title' => 'Sign Up for Our Newsletter', 'list_selection_title' => 'Sign me up for:', 'list_selection_format' => 'checkbox', 'formid' => 0, 'show_list_selection' => false, 'lists' => array(), 'exclude_lists' => array() ) );
		
#		$this->r(array($this, $instance));
		@include_once('functions-form.php');
		$cc = constant_contact_create_object();
		
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
	?>
	<?php 
	if(!get_option('cc_form_design')) {
	?>
	<h2>You're in the right spot, but&hellip;</h2>
	<h3>You must create a form on the <a href="<?php echo admin_url('admin.php?page=constant-contact-forms'); ?>">Form Design page</a> first.</h3>
	<p class="description">This widget displays forms created on the <a href="<?php echo admin_url('admin.php?page=constant-contact-forms'); ?>">Form Design page</a>. Go there, create a form, then come back here.</p>
	<?php
	return;
	} 
	?>
	<h3>Signup Widget Settings</h3>
	<a name="widget"></a>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('title');?>"><span>Signup Widget Title</span></label></p></th>
			<td>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php echo $title; ?>" size="50" />
			<p class="description">The title text for the this widget.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('description');?>"><span>Signup Widget Description</span></label></p></th>
			<td>
			<textarea class="widefat" name="<?php echo $this->get_field_name('description');?>" id="<?php echo $this->get_field_id('description');?>" cols="50" rows="4"><?php echo $description; ?></textarea>
			<p class="description">The description text displayed in the sidebar widget before the form. HTML allowed. Paragraphs will be added automatically like in posts.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('formid');?>"><span>Form Fields &amp; Design</span></label></p></th>
			<td>
			<?php echo $this->get_form_list_select($instance); ?>
			<p class="description">Create your form on the <a href="<?php echo admin_url('admin.php?page=constant-contact-forms'); ?>">Form Design page</a>, then select it here.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('redirect_url');?>"><span>Signup Widget Thanks Page</span></label></p></th>
			<td>
			<input type="text" class="widefat code" name="<?php echo $this->get_field_name('redirect_url');?>"  id="<?php echo $this->get_field_id('redirect_url');?>" value="<?php echo $this->get_value('redirect_url', $instance); ?>" size="50" />
			<p class="description">Enter a url above to redirect new registrants to a thank you page upon successfully submitting the signup form. Use the full URL/address including <strong>http://</strong> Leave this blank for no redirection (page will reload with success message inside widget).</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('show_list_selection');?>"><span>Show List Selection?</span></label></p></th>
			<td>
			<label for="<?php echo $this->get_field_id('show_list_selection');?>" class="howto"><input <?php checked($instance['show_list_selection']) ?> type="checkbox" name="<?php echo $this->get_field_name('show_list_selection');?>" id="<?php echo $this->get_field_id('show_list_selection');?>" class="list-selection" value="1" /> <span>Yes, let visitors choose which contact lists they want to subscribe to.</span></label>
			<p class="description">This will let users see the various lists ticked in the <strong>Active Contact Lists</strong> option below. 	<strong>If this is not checked</strong> they will automatically be subscribed to all <strong>Active Contact Lists</strong>.</p>
			</td>
		</tr>
<?php
	
		$selected_lists = (!is_array($instance['lists'])) ? array() : $instance['lists'];
		$exclude_lists = (!is_array($instance['exclude_lists'])) ? array() : $instance['exclude_lists'];
		
#		$lists_all = constant_contact_get_transient('lists_all');
	
		$lists_all = constant_contact_get_lists(isset($_REQUEST['fetch_lists']));
/*
		if(empty($lists_all)) {
			$lists_all = $cc->get_all_lists();
			constant_contact_set_transient('lists_all', $lists_all);
		}
*/
		$hidecss = isset($instance['show_list_selection']) ? ' style="display:none;"' : '';

		?>

		<tr valign="top" class="contact-lists">
			<th scope="row"><p><label><span>Contact Lists</span></label></p><p><a href="<?php echo admin_url('widgets.php?fetch_lists=true'); ?>" class="button">Refresh Lists</a></p></th>
			<td>
			<?php
			if($lists_all):
#			$this->r($lists_all);
			$selectList = $checkList = '';
			foreach($lists_all as $k => $v):
				if(in_array($v['id'], $selected_lists) || sizeof($selected_lists) == 0 && $k == 0):
					$checkList .= '<label for="'.$this->get_field_id('lists_'.$v['id']).'"><input name="'.$this->get_field_name('lists').'[]" type="checkbox" checked="checked" value="'.$v['id'].'" id="'.$this->get_field_id('lists_'.$v['id']).'" /> '.$v['Name'].'</label><br />';
					$selectList .= '<option>'.$v['Name'].'</option>'; 
				else:
					$checkList .= '<label for="'.$this->get_field_id('lists_'.$v['id']).'"><input name="'.$this->get_field_name('lists').'[]" type="checkbox" value="'.$v['id'].'" id="'.$this->get_field_id('lists_'.$v['id']).'"  /> '.$v['Name'].'</label><br />';
					$selectList .= '<option>'.$v['Name'].'</option>'; 
				endif;
			endforeach;
			echo $checkList;
			endif;
			?>
			<p class="description">If you show the list selection you can select which lists are available above, alternatively if you disable the list selection you should select which lists the user is automatically subscribed to (if you show the list selection and don't select any lists above all lists will be available to the user including newly created ones).</p>
			</td>
		</tr>
		<tr valign="top" class="list-selection"<?php echo $hidecss; ?>>
			<th scope="row"><p><label for="<?php echo $this->get_field_id('list_selection_title');?>"><span>List Selection Title</span></label></p></th>
			<td>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('list_selection_title');?>" name="<?php echo $this->get_field_name('list_selection_title');?>" value="<?php echo $this->get_value('list_selection_title', $instance); ?>" size="50" />
			<p class="description">Label text displayed in widget just above the list selection  form.</p>
			</td>
		</tr>
		<tr valign="top" class="list-selection"<?php echo $hidecss; ?>>
			<th scope="row"><p><label for="<?php echo $this->get_field_id('list_selection_format');?>"><span>List Selection Format</span></label></p></th>
			<td>
			<label for="<?php echo $this->get_field_id('list_selection_format_checkbox'); ?>"><input <?php checked($instance['list_selection_format'], 'checkbox') ?> type="radio" name="<?php echo $this->get_field_name('list_selection_format'); ?>" value="checkbox" id="<?php echo $this->get_field_id('list_selection_format_checkbox'); ?>" /> Checkboxes</label>
			<label for="<?php echo $this->get_field_id('list_selection_format_dropdown'); ?>"><input <?php checked($instance['list_selection_format'], 'dropdown') ?> type="radio" name="<?php echo $this->get_field_name('list_selection_format'); ?>" value="dropdown" id="<?php echo $this->get_field_id('list_selection_format_dropdown'); ?>" /> Dropdown List</label>
			<label for="<?php echo $this->get_field_id('list_selection_format_select'); ?>"><input <?php checked($instance['list_selection_format'], 'select') ?> type="radio" name="<?php echo $this->get_field_name('list_selection_format'); ?>" value="select" id="<?php echo $this->get_field_id('list_selection_format_select'); ?>" /> Multi-Select</label>
			<p class="description">This controls what kind of list is shown. <a href="#listTypeInfo" class="moreInfo">More info</a></p>
			<div class="moreInfo" id="listTypeInfo">
				<ul class="howto" style="list-style:disc outside!important; display:list-item!important;">
					<li><strong>Checkboxes</strong> displays a list of checkboxes, like the lists above and below</li>
					<li><strong>Dropdown List</strong> displays the list as a multi-select drop-down.<br />Example: <select><?php echo $selectList; ?></select></li>
					<li><strong>Multi-Select</strong> displays the list as a multi-select drop-down.<br />Example: <select multiple="multiple" size="4" style="height:5em!important;"><?php echo $selectList; ?></select></li>
				</ul>
			</div>
			</td>
		</tr>
		<tr valign="top" class="list-selection contact-lists-hide"<?php echo $hidecss; ?>>
			<th scope="row"><p><label><span>Hide Contact Lists</span></label></p></th>
			<td>
			<?php
			if($lists_all):
			foreach($lists_all as $k => $v):
				if(in_array($v['id'], $exclude_lists)):
					echo '<label for="'.$this->get_field_id('exclude_lists_'.$v['id']).'"><input name="'.$this->get_field_name('exclude_lists').'[]" type="checkbox" checked="checked" value="'.$v['id'].'" id="'.$this->get_field_id('exclude_lists_'.$v['id']).'" /> '.$v['Name'].'</label><br />';
				else:
					echo '<label for="'.$this->get_field_id('exclude_lists_'.$v['id']).'"><input name="'.$this->get_field_name('exclude_lists').'[]" type="checkbox" value="'.$v['id'].'" id="'.$this->get_field_id('exclude_lists_'.$v['id']).'" /> '.$v['Name'].'</label><br />';
				endif;
			endforeach;
			endif;
			?>
			<p class="description">If you show the list selection you can select which lists to always exclude from the selection.</p>
			</td>
		</tr>
	</table>
	<?php
    }

} // class constant_contact_api_widget
	
?>