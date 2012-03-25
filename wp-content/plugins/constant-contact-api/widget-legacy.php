<?php // $Id$
/**
 * constant_contact_api_widget Class
 */

function constant_contact_load_legacy_widget()
{
	register_widget( 'constant_contact_api_widget' );
	
	// Check if form designer has called it yet...
	if(!function_exists('constant_contact_admin_widget_scripts')) {
		function constant_contact_admin_widget_scripts_legacy() {
			global $pagenow;
			if($pagenow == 'widgets.php') {
				wp_enqueue_script( 'admin-cc-widget', plugin_dir_url(__FILE__).'js/admin-cc-widget.js' );
			}
		}
		constant_contact_admin_widget_scripts_legacy();
	} else {
		constant_contact_admin_widget_scripts();
	}
	
}

class constant_contact_api_widget extends WP_Widget {

    /** constructor */
    function constant_contact_api_widget()
	{
		$widget_options = array(
			'description' => 'Displays a Constant Contact signup form to your visitors',
			'classname' => 'constant-contact-signup',
		);
		$control_options = array('width'=>690); // Min-width of widgets config with expanded sidebar
        parent::WP_Widget(false, $name = 'Constant Contact Signup', $widget_options, $control_options);	
    }
	
	function parse_args($instance) {
		$defaults = array(
			'show_firstname' => false, 
			'show_lastname' => false, 
			'description' => '',
			'title' => '', 
			'list_selection_title' => '', 
			'list_selection_format' => '',  
			'show_list_selection' => false,
			'lists' => array(), 
			'redirect_url' => '',
			'exclude_lists' => array() 
		);	
		
		if(!isset($instance['widgethasbeensaved'])) {
			$defaults = array(
				'show_firstname' => get_option('cc_widget_show_firstname'), 
				'show_lastname' => get_option('cc_widget_show_lastname'), 
				'description' => get_option('cc_widget_description'),
				'title' => get_option('cc_signup_widget_title'), 
				'list_selection_title' => get_option('cc_widget_list_selection_title'), 
				'list_selection_format' => get_option('cc_widget_list_selection_format'),  
				'show_list_selection' => get_option('cc_widget_show_list_selection'),
				'redirect_url' => get_option('cc_widget_redirect_url')
			);
		}
		
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		return $instance;
	}
	

   /** @see WP_Widget::widget */
    function widget($args = array(), $instance = array())
	{
		$currentform = (isset($_POST['uniqueformid']) && $this->id === $_POST['uniqueformid']) ? true : false;
		
		$instance = $this->parse_args($instance);
		
		extract($instance);
		
		$output = '';
		$errors = false;
		if(isset($GLOBALS['cc_errors_'.$this->id])):
			$errors = $GLOBALS['cc_errors_'.$this->id];
			unset($GLOBALS['cc_errors_'.$this->id]);
		endif;
				
		$cc = constant_contact_create_object();
			
		if(!is_object($cc)):
			return;
		endif;
		
		$widget_title = $title; #apply_filters('widget_title', get_option('cc_signup_widget_title'));
		$widget_description = $description; //get_option('cc_signup_widget_description');
        extract( $args );
        ?>
			<?php $output .= (isset($before_widget)) ? $before_widget : ''; ?>
			<?php $output .= (isset($before_title, $after_title)) ? $before_title : '<h2>'; ?>
			<?php $output .= (isset($widget_title)) ? $widget_title : ''; ?>
			<?php $output .= (isset($after_title, $before_title)) ? $after_title : '</h2>'; ?>
			
			<?php
			if(!empty($errors)):
				$error_output = '';
				$error_output .= '<div id="constant-contact-signup-errors">';
				$error_output .= '<ul>';
				foreach ($errors as $e) { 
					if(is_array($e)) { $error_output .= '<li><label for="'.$e[1].'">'.$e[0].'</label></li>'; } 
					else { $error_output .= '<li>'.$e.'</li>'; }
				}
				$error_output .= '</ul>';
				$error_output .= '</div>';
				$output .= apply_filters('constant_contact_form_errors', $error_output);
			elseif(isset($_GET['cc_success'])):
				$success = '<p>Success, you have been subscribed.</p>';
				$output .= apply_filters('constant_contact_form_success', $success);
				$output .= (isset($after_widget)) ? $after_widget : ''; 
				$output = apply_filters('constant_contact_form', $output);
				echo $output;
				return;
			elseif($widget_description):
				$widget_description = wpautop($widget_description);
				$output .= apply_filters('constant_contact_form_description', $widget_description);
			endif;
			$output .=
			'<form action="'.constant_contact_current_page_url().'" method="post" id="constant-contact-signup" name="constant-contact-signup">';
			
			if($show_firstname):
				$output .='
					<label for="cc_firstname">'.__('First Name:','constant-contact-api').'</label>
					<div class="input-text-wrap">
						<input type="text" name="fields[first_name][value]" id="cc_firstname" value="'; 
						$output .= ($currentform && isset($_POST['fields']['first_name']['value'])) ? htmlentities($_POST['fields']['first_name']['value']) : '';
						$output .= '" />
					</div>';
				endif;
				
				if($show_lastname):
				$output .='
					<label for="cc_lastname">'.__('Last Name:','constant-contact-api').'</label>
					<div class="input-text-wrap">
						<input type="text" name="fields[last_name][value]" id="cc_lastname" value="';
						$output .= ($currentform && isset($_POST['fields']['last_name']['value'])) ? htmlentities($_POST['fields']['last_name']['value']) : '';
						$output .= '" />
					</div>';
				endif;
				
				$output .= '
				<label for="cc_email">'.__('Email:','constant-contact-api').'</label>
				<div class="input-text-wrap">
					<input type="text" name="fields[email_address][value]" id="cc_email" value="';
					$output .= ($currentform && isset($_POST['fields']['email_address']['value'])) ? htmlentities($_POST['fields']['email_address']['value']) : '';
				$output .= '" />
				</div>';
				
				$hide_lists_output = '';
				
				if($show_list_selection) {
					if($list_selection_format != 'checkbox') {
						$output .= '<label for="cc_newsletter_select">'.$list_selection_title.'</label>
						<div class="input-text-wrap">';
						if($list_selection_format == 'select') {
							$output .= '<select name="cc_newsletter[]" id="cc_newsletter_select"  multiple size="5">';
						} else {
							$output .= '<select name="cc_newsletter[]" id="cc_newsletter_select">';
						}
						if(!empty($showlists)) {
							foreach($showlists as $k => $v) {
								if($currentform && isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])) {
									$output .=  '<option selected value="'.$v['id'].'">'.$v['ShortName'].'</option>';
								} else {
									$output .=  '<option value="'.$v['id'].'">'.$v['ShortName'].'</option>';
								}
							}
						}
						$output .= '
						</select>
						</div>';
					} else {
						$output .=  $list_selection_title;
						$output .=  '<div class="input-text-wrap">';
						$output .=  '<ul>'."\n";
						if(!empty($showlists)) {
							foreach($showlists as $k => $v) {
								if($currentform && isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])) {
									$output .=  '<li><label for="cc_newsletter-'.$v['id'].'"><input checked="checked" type="checkbox" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '</label></li>'."\n"; // ZK added label, ID, converted to <LI>
								} else {
									$output .=  '<li><label for="cc_newsletter-'.$v['id'].'"><input type="checkbox" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '</label></li>'."\n"; // ZK added label, ID
								}
							}
						}
						$output .=  '</ul>'."\n";
						$output .=  '</div>'."\n";
					}
				} else {
					if(!empty($showlists)){
						foreach($showlists as $k => $v) {
							$hide_lists_output .= '<input type="hidden" name="cc_newsletter[]" value="'.$v['id'].'" />'."\n";
						}
					}
				} // end if show list selection
				
				if(!empty($hidelists)) {
					foreach($hidelists as $k => $v) {
						if(in_array($v['id'], $lists)) {
							$hide_lists_output .= '<input type="hidden" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" value="'.$v['id'].'" />'."\n";
						}
					}
				}
				
				$output .= '
				<div>
					'.$hide_lists_output.'
					<input type="hidden" id="cc_referral_url" name="cc_referral_url" value="'.urlencode(constant_contact_current_page_url()).'" />
					<input type="hidden" id="cc_redirect_url" name="cc_redirect_url" value="'.apply_filters('constant_contact_form_redirect_url', urlencode($redirect_url)).'" />
					';
					$submit_button = '<input type="submit" name="constant-contact-signup-submit" value="Signup" class="button submit" />';
					$output .= apply_filters('constant_contact_form_submit', $submit_button);
					$output .= '
					<input type="hidden" name="uniqueformid" value="'.$this->id.'" />
				</div>
			</form>';
			$output .= (isset($after_widget)) ? $after_widget : ''; 
			
			// Modify the output by calling add_filter('constant_contact_form', 'your_function');
			// Passes the output to the function, needs return $output coming from the function.
			$output = apply_filters('constant_contact_form', $output);
			
			echo $output;
    }
	
	function update($new, $old) {
		extract($new);
		$cc = constant_contact_create_object();
		
		$show_lists = $hide_lists = array();
		
		if(!empty($lists) && is_array($lists)) {
			// show only the lists they have selected
			foreach($lists as $id):
				if(!in_array($id, $exclude_lists)) {
					$show_lists[$id] = constant_contact_get_list($id);
				} else {
					$hide_lists[$id] = constant_contact_get_list($id);
				}
			endforeach;
		} else {
			// show all lists and exclude any have may have selected
			$lists = constant_contact_get_lists(isset($_REQUEST['fetch_lists']));
			
			if($lists):
				foreach($lists as $k => $v):
					if(!in_array($v['id'], $exclude_lists)) {
						$show_lists[$v['id']] = constant_contact_get_list($v['id']);
					} else {
						$hide_lists[$v['id']] = constant_contact_get_list($v['id']);
					}
				endforeach;
			endif;
		}
		$new['showlists'] = $show_lists;
		$new['hidelists'] = $hide_lists;
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
		$instance = $this->parse_args($instance);

		extract($instance);
		
		@include_once('functions.php');
		$cc = constant_contact_create_object();
		
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
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
			<th scope="row"><p><label><span>Show First Name?</span></label></p></th>
			<td>
				<p><label class="howto" for="<?php echo $this->get_field_id('show_firstname');?>"><input <?php checked($show_firstname, 1); ?> type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('show_firstname');?>" value="1" id="<?php echo $this->get_field_id('show_firstname');?>" /> <span>Show first name input field</span></label></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label><span>Show Last Name?</span></label></p></th>
			<td>
				<p><label class="howto" for="<?php echo $this->get_field_id('show_lastname');?>"><input <?php checked($show_lastname, 1); ?> type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('show_lastname');?>" value="1" id="<?php echo $this->get_field_id('show_lastname');?>" /> <span>Show last name input field</span></label></p>
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
			<th scope="row"><p><label for="<?php echo $this->get_field_id('redirect_url');?>"><span>Signup Widget Thanks Page</span></label></p></th>
			<td>
			<input type="text" class="widefat code" name="<?php echo $this->get_field_name('redirect_url');?>"  id="<?php echo $this->get_field_id('redirect_url');?>" value="<?php echo $this->get_value('redirect_url', $instance); ?>" size="50" />
			<p class="description">Enter a url above to redirect new registrants to a thank you page upon successfully submitting the signup form. Use the full URL/address including <strong>http://</strong> Leave this blank for no redirection (page will reload with success message inside widget).</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="<?php echo $this->get_field_id('show_list_selection');?>"><span>Show List Selection?</span></label></p></th>
			<td>
			<label for="<?php echo $this->get_field_id('show_list_selection');?>" class="howto"><input <?php checked($show_list_selection) ?> type="checkbox" name="<?php echo $this->get_field_name('show_list_selection');?>" id="<?php echo $this->get_field_id('show_list_selection');?>" class="list-selection" value="1" /> <span>Yes, let visitors choose which contact lists they want to subscribe to.</span></label>
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
		$hidecss = !$show_list_selection ? ' style="display:none;"' : '';

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
			<div class="moreInfo" id="listTypeInfo" style="display:none;">
				<ul class="howto" style="list-style:disc outside!important; display:list-item!important;">
					<li><strong>Checkboxes</strong> displays a list of checkboxes, like the lists above and below</li>
					<li><strong>Dropdown List</strong> displays the list as a multi-select drop-down.<br />Example: <select><?php echo $selectList; ?></select></li>
					<li><strong>Multi-Select</strong> displays the list as a multi-select drop-down.<br />Example: <select multiple="multiple" size="4" style="height:5em!important;"><?php echo $selectList; ?></select></li>
				</ul>
			</div>
			</td>
		</tr>
		<tr valign="top" class="list-selection contact-lists-hide"<?php echo $hidecss; ?>>
			<th scope="row"><p><label><span>Hide Contact Lists from User Selection</span></label></p></th>
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

if(!function_exists('tempty')) {
	function tempty($val) { 
		$val = trim($val);
    	return empty($val) && $val !== 0; 
	}
}
	
?>