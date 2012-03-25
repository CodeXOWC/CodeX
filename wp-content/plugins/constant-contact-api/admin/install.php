<?php // $Id$

	// plugin is being activated
	function constant_contact_activate()
	{
		// list of all extra fields the API supports
		$constant_contact_extra_fields = array(
			'First Name', 
			'Middle Name', 
			'Last Name',
			'Job Title', 
			'Company Name',
			'Home Phone', 
			'Work Phone',
			'Addr1','Addr2','Addr3',
			'City',
			'State Code',
			'State Name',
			'Country Code',
			'Country Name',
			'Postal Code',
			'Sub Postal Code',
			'Note',
			'CustomField1',
			'CustomField2',
			'CustomField3',
			'CustomField4',
			'CustomField5',
			'CustomField6',
			'CustomField7',
			'CustomField8',
			'CustomField9',
			'CustomField10',
			'CustomField11',
			'CustomField12',
			'CustomField13',
			'CustomField14',
			'CustomField15',
		);
		
		// build the mappings
		$fields = array();
		foreach(str_replace(' ', '', $constant_contact_extra_fields) as $field):
			$fields[] = "$field:$field";
		endforeach;
		$mappings = implode(', ', $fields);
		
		// set default settings
		add_option('cc_extra_fields', $constant_contact_extra_fields, '', 'no');
		add_option('cc_extra_field_mappings', $mappings, '', 'no');
		add_option('cc_register_page_method', 'none', '', 'no');
		add_option('cc_default_opt_in', 1, '', 'no');
		add_option('cc_signup_title', __('Newsletter'), '', 'no');
		add_option('cc_signup_description', 'Subscribe to the Newsletter', '', 'no');
		add_option('cc_signup_widget_title', 'Newsletter', '', 'no');
		add_option('cc_signup_widget_description', 'Subscribe to the Newsletter', '', 'no');
		add_option('cc_list_selection_format','checkbox', '', 'no');
		add_option('cc_widget_show_list_selection',1, '', 'no');
		add_option('cc_uninstall_method','keep', '', 'no');
		add_option('cc_widget_show_firstname',1, '', 'no');
		add_option('cc_widget_show_lastname',1, '', 'no');
		add_option('cc_widget_list_selection_format','checkbox', '', 'no');
		add_option('cc_widget_list_selection_title', 'Contact Lists:', '', 'no');
		add_option('cc_use_legacy_widget', true, '', 'no');
		
		if(!get_option('cc_username') || !get_option('cc_password')):
			function constant_contact_warning() {
				echo "
				<div id='constant-contact-warning' class='updated fade'><p><strong>".__('The plugin is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your Constant Contact username and password</a> for it to work.','constant-contact-api'), "admin.php?page=constant-contact-settings")."</p></div>
				";
			}
			add_action('admin_notices', 'constant_contact_warning');
		endif;
	}
	
	// plugin is being deactivated
	function constant_contact_deactivate()
	{
		remove_action('widgets_init', 'constant_contact_load_widgets');
	
		// define options to delete
		$options = array(
			'cc_uninstall_method',
			'cc_extra_fields',
			'cc_extra_field_mappings',
			'cc_register_page_method',
			'cc_default_opt_in',
			'cc_signup_title',
			'cc_signup_description',
			'cc_signup_widget_title',
			'cc_signup_widget_description',
			'cc_lists',
			'cc_exclude_lists',
			'cc_list_selection_format',
			'cc_widget_show_firstname',
			'cc_widget_show_lastname',
			'cc_widget_show_list_selection',
			'cc_widget_lists',
			'cc_widget_exclude_lists',
			'cc_widget_show_list_selection',
			'cc_widget_list_selection_format',
			'cc_widget_redirect_url',
			'cc_widget_list_selection_title',
			'cc_use_legacy_widget'
		);
		
		if(get_option('cc_uninstall_method') == 'remove'):
			// remove username and password aswell
			if(isset($_SESSION['ccObject'])) { unset($_SESSION['ccObject']); }
			$options[] = 'cc_username';
			$options[] = 'cc_password';
			delete_transient('cc_object');
		endif;
		
		function deleteOptions($options)
		{
			$num = count($options);
		
			if (count($options) > 0)
			{
				foreach ($options as $option) {
					delete_option($option);
					unregister_setting('constant-contact', $option);
				}
			}
		}
		deleteOptions($options);
	}

?>