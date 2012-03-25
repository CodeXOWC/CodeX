<?php // $Id$

function constant_contact_enquque_core_styles() {
		
	// Keep this outside so that the navigation menu text is nowrap
	wp_enqueue_style('constant-contact-api-admin', plugins_url('constant-contact-api/admin/constant-contact-admin-css.css'), false, false, 'all');
	
	if(constant_contact_is_plugin_page()) {
		wp_enqueue_style('constant-contact-api-admin-qtip', plugins_url('constant-contact-api/css/jquery.qtip.min.css'), false, false, 'all');
	}
}

function constant_contact_enquque_core_scripts() {
	if(constant_contact_is_plugin_page()) {
		wp_enqueue_script('constant-contact-api-admin-page', plugins_url('constant-contact-api/js/admin-cc-page.js'), array('jquery'));
		wp_enqueue_script('constant-contact-api-admin-qtip', plugins_url('constant-contact-api/js/jquery.qtip.pack.js'), array('jquery', 'constant-contact-api-admin-page'));
	}
}

/**
 * Fetch array of all contact lists from the transient cache, checking API and updating cache if necessary or forced.
 *
 * Attempts to serve array from 'cc_lists_cache' transient. If nothing is there or $force_update, 
 * it first checks via the API (cc::get_all_lists()) and saves a new transient.
 *
 * @uses $cc::get_all_lists() if API needs to be checked. Avoid that function as it slows down the page
 * @global cc $cc
 * @param boolean $force_update If true the API will be queried even if there is a valid cache.
 * @return <type>
 */
function constant_contact_get_lists($force_update = false) {
	global $cc;

	if(!$force_update) {
		
		// First check the transient to see if it is still fresh enough
		$lists = get_transient('cc_lists_cache');
	
		// If it is an array and we are not forcing an update, return the data
		if (is_array($lists))
			return $lists;
	}
	/**
	 * Otherwise we need to fetch and save a fresh copy in our transient
	 */
	// Create the cc object if necessary
	if(!constant_contact_create_object()) { return false; };

	// Fetch the array of all lists using the API
	$lists = $cc->get_all_lists();

	// Save the array into the cc_lists_cache transient with a 12-hour expiration
	set_transient('cc_lists_cache', $lists, 60*60*12);

	return $lists;

}

/**
 * Fetch data array for a specific contact list from the transient cache.
 *
 * Avoids the extra API query in cc:get_list by using constant_contact_get_lists() and its transientn cache
 *
 * @param integer $id Numeric id of the list you want the data for.
 * @return <type>
 */
function constant_contact_get_list($id) {

	$lists = constant_contact_get_lists();

	foreach ($lists as $key => $details) :
		if ($details['id'] == $id)
			return $details;
	endforeach;
	
	return false;
}

/**
 * Format field mappings into array
 * @return <type>
 */
function constant_contact_build_field_mappings()
{
	if(isset($GLOBALS['cc_extra_field_mappings'])):
		return $GLOBALS['cc_extra_field_mappings'];
	endif;

	$mappings = get_option('cc_extra_field_mappings');
	$field_mappings = explode(',', $mappings);

	$GLOBALS['cc_extra_field_mappings'] = array();

	if($field_mappings):
	foreach($field_mappings as $mapping):
		$bits = explode(':', $mapping);

		if(is_array($bits) && isset($bits[0], $bits[1])):
			$GLOBALS['cc_extra_field_mappings'][trim($bits[0])] = trim($bits[1]);
		endif;
	endforeach;
	endif;

	return $GLOBALS['cc_extra_field_mappings'];
}


/**
 * This function determines what the last error was and returns a friendly error message
 *
 * @param <type> $status_code
 * @return boolean
 */
function constant_contact_last_error($status_code = 0)
{
	$last_error = false;
	$status_code = intval($status_code);

	if(!$status_code):
		return $last_error;
	endif;

	$last_error = 'Sorry there was a problem processing your request, the error given was: ';

	switch($status_code):
		case 400: /* Invalid Request */
			$last_error .= 'Invalid Request';
		break;
		case 401: /* Unauthorized */
			$last_error .= 'Unauthorized';
		break;
		case 404: /* Page Not Found */
			$last_error .= 'Page Not Found';
		break;
		case 409: /* Conflict */
			$last_error .= 'Conflict';
		break;
		case 415: /* Unsupported Media Type */
			$last_error .= 'Unsupported Media Type';
		break;
		case 500: /* Internal Server Error */
			$last_error .= 'Internal Server Error';
		break;
		default: /* Unknown Error */
			$last_error .= 'Unknown Error';
		break;
	endswitch;

	return $last_error;
}


/**
 * Create and validate a cc object as defined in class.cc.php
 *
 * Saves to global $cc if the object is created successfully.
 *
 * Makes a request to Constant Contact servers to test the connection and ensure validity of the object.
 *
 * Should only be run ONCE PER PAGELOAD because the global $cc can be used afterwards
 * and creating extra $cc objects wastes resources and slows down pageload. Should not
 * be run on any pages where queries to the API are not explicitly needed.
 *
 * Formerly returned the $cc object and was used all over by various functions. This
 * caused many unnecessary connections, so global $cc is now used instead.
 *
 * Still returns the $cc object if it is valid for backwards-compatibility.
 * 
 * @return cc
 */
function constant_contact_create_object($force_new = false)
{
	global $cc, $cc_create_count;
	
	if(empty($cc_create_count)) { $cc_create_count = 1; } else { $cc_create_count++; }
	
	// If there is already the object, leave it alone
	if(!$force_new && is_object($cc)) { return $cc; }
	
	// If the settings have been updated, force new session
	if(is_admin() && current_user_can('manage_options') && 
		(
			isset($_POST['cc_password']) ||
			(isset($_GET['page']) && $_GET['page'] == 'constant-contact-api' && isset($_GET['settings-updated']))
		)
	) {
		$force_new = true;
	}
	
	// If a new object is forced, reset things...
	if($force_new) {
		if(isset($_SESSION['ccObject'])) { unset($_SESSION['ccObject']); }
		delete_transient('cc_object');
		$cc = false;
	}
	
	//
	// Try to get the cached object
	//
	
	// If the object's been stored in the session
	if(!$force_new && session_id() && !empty($_SESSION['ccObject'])) {
		#r($_SESSION['ccObject']);
		require_once CC_FILE_PATH . 'class.cc.php';
		$cc = maybe_unserialize($_SESSION['ccObject']);
		if(is_object($cc)) {
			return $cc;
		}
	}

	// If the cc object has been stored as a transient
	if(!$force_new && $trans_api = get_transient('cc_object')) {
		require_once CC_FILE_PATH . 'class.cc.php';
		$cc = maybe_unserialize($trans_api);
		
		// If there is a problem with $cc show an error
		if(!is_object($cc)) {
			constant_contact_admin_credentials_error();
			return false;
		} else {
			return $cc;
		}
	}
	
	
	// 
	// Get a new object
	//

	// Get the username and password
	$username = trim(rtrim(get_option('cc_username')));
	$password =  trim(rtrim(get_option('cc_password')));

	// If either are missing always return false, they are mandatory
	if(!$username || !$password):
		// issues an error using wp system
		return false;
	endif;
	
	// Include the class definition file
	require_once CC_FILE_PATH . 'class.cc.php';

	// Create a new instance
	$new_cc = new cc($username, $password);
	
	
	/**
	 * Test the instance to make sure it is valid
	 */
	// Run a generic check to see if we can connect
	if(is_object($new_cc) && $new_cc->get_service_description()) {
		// If we have connected copy the object into the global
		$cc = $new_cc;
		
		$_SESSION['ccObject'] = maybe_serialize($cc);
		
		// Save some processing time!
		set_transient('cc_object', maybe_serialize($cc), 60*60*12);
		
		// If there is a problem with $cc show an error
		if(!is_object($cc)) {
			return false;
		} else {
			return $cc;
		}
		
	// Otherwise, if there is a response code, deal with the connection error
	} elseif(is_object($new_cc) AND isset($new_cc->http_response_code)) {

		$error = $new_cc->http_get_response_code_error($new_cc->http_response_code);

		// if we get an unauthorized 401 error code reset the username and password
		// if we don't do this the CC account will be temporarily blocked eventually
		if(is_admin() && !isset($_POST['error_displayed']) && did_action('admin_head')) { // error_diplayed for showing only once, it was showing twice
		// did_action('admin_head') prevents from showing before content and screwing up saving post data
			$_POST['error_displayed'] = true;
			echo "<div id='constant-contact-warning' class='error'>
				".wpautop($error)."
			</div>";
		}

	} // if http_response_code

	return false;
}

/**
 * Display an error about Constant Contact credentials to show in admin when
 * we are unable to build the $cc object
 */
function constant_contact_admin_credentials_error() {
	return '<div class="error"><p>There was a problem with your <a href="' . admin_url('admin.php?page=constant-contact-api') . '">Constant Contact settings</a>, please ensure that you have entered a valid username and password.</p></div>';
}

function constant_contact_urlencode_array($args) {
	if(!is_array($args)) return false;
  $c = 0;
  $out = '';
  foreach($args as $name => $value)
  {
    if($c++ != 0) $out .= '&';
    $out .= urlencode("$name").'=';
    if(is_array($value))
    {
      $out .= constant_contact_urlencode_array($value);
    }else{
      $out .= urlencode("$value");
    }
  }
  return $out;
}


add_shortcode('constantcontactapi', 'constant_contact_signup_form_shortcode');
function constant_contact_signup_form_shortcode($atts, $content=null) {
	$atts = shortcode_atts( array(
		'before' => null,
		'after' => null,
		'formid' => 0,
		'redirect_url' => false,
		'lists' => array(),
		'title' => '',
		'exclude_lists' => array(),
		'description' => '',
		'show_list_selection' => false,
		'list_selection_title' => 'Add me to these lists:',
		'list_selection_format' => 'checkbox'
	), $atts );
	
	return constant_contact_public_signup_form($atts, false);
};


// Added for people experiencing Array at the top of their forms
add_filter('constant_contact_form', 'constant_contact_form_remove_array_text');

function constant_contact_form_remove_array_text($form = null) {
	return str_replace('Array()','', str_replace('Array ()','', str_replace('Array','', str_replace('array()','', str_replace('array','', $form))))); 
}

/**
 * HTML Signup form to be used in widget and shortcode
 *
 * Based on original widget code but broken out to be used in shortcode and
 * any other place where non-logged-in users will be signing up.
 *
 * @param <type> $args
 */
function constant_contact_public_signup_form($args, $echo = true) {

	$output = $error_output = $success = $haserror = $listoutput = $hiddenlistoutput = '';
	$defaultArgs = array(
		'before' => null,
		'after' => null,
		'formid' => 0,
		'redirect_url' => false,
		'lists' => array(),
		'title' => '',
		'exclude_lists' => array(),
		'description' => '',
		'show_list_selection' => false,
		'list_selection_title' => 'Add me to these lists:',
		'list_selection_format' => 'checkbox'
	);
	$args = wp_parse_args($args, $defaultArgs);
	
	$unique_id = sha1(implode('||', $args));

	extract($args, EXTR_SKIP);

	/**
	 * Make it possible to call using shortcode comma separated values. eg: lists=1,2,3
	 */
	if(is_string($lists)) { $lists = explode(',', $lists); }
	
	/**
	 * Prepare the set of contact lists that will be shown to the user
	 */
	// $lists_to_show: array which will hold data about lists to display if necessary
	$lists_to_show = array();

	/**
	 * Get array of IDs of contact lists chosen in the "Active Contact Lists" option
	 * If this is empty we show all available lists, otherwise only show these.
	 */
	$cc_lists = $lists;

	/**
	 * Get array of IDs of "Hidden Contact Lists" from options
	 * Never show any of these lists regardless of 'cc_widget_lists' option
	 */
	if (!is_array($exclude_lists)) $exclude_lists = array();

	// Loop through array of all cached contact lists
	foreach (constant_contact_get_lists() as $key => $details) :

		// If we have a specific set of lists to show and this list isn't in it then skip it
		if (is_array($cc_lists) AND count($cc_lists) AND !in_array($details['id'], $cc_lists))
			continue;

		// If we have a hidden list array and this list IS in it, then skip it.
		if (is_array($exclude_lists) AND count($exclude_lists) AND in_array($details['id'], $exclude_lists))
			continue;

		// Success: add this list to the lists_to_show array
		$lists_to_show[$details['id']] = $details;
	endforeach;

	if($formid !== '' && function_exists('constant_contact_retrieve_form')) {
		$force = (isset($_REQUEST['cache']) || (isset($_REQUEST['uniqueformid']) && $_REQUEST['uniqueformid'] === $unique_id)) ? true : false;
		$form = constant_contact_retrieve_form($formid, $force, $unique_id);
	}
	
	// If the form returns an error, we want to get out of here!
	if(empty($form) || is_wp_error($form)) { return false; }
	
	// Modify lists with this filter
	$lists_to_show = apply_filters('constant_contact_form_designer_lists', apply_filters('constant_contact_form_designer_lists_'.$formid, $lists_to_show));
	
	/**
	 * Display errors or Success message if the form was submitted.
	 */
	/**
	 * Success message: If no errors AND signup was successful show the success message
	 */ 
	if(isset($_GET['cc_success'])) {
		$success = '<p class="success cc_success">Success, you have been subscribed.</p>';
		$success = apply_filters('constant_contact_form_success', $success);
		
		$form = str_replace('<!-- %%SUCCESS%% -->', $success, $form);
		$form = preg_replace('/\%\%(.*?)\%\%/ism', '', $form);
		
		return $form;
	} 
	 // Display errors if they exist in the cc_errors global
	else if(isset($GLOBALS['cc_errors_'.$unique_id])) {
		$errors = false; $haserror = ' has_errors';
		$errors = $GLOBALS['cc_errors_'.$unique_id];
		// Remove errors from the global so we dont' show them twice by accident
		unset($GLOBALS['cc_errors_'.$unique_id]);

		// Set up error display
		$error_output .= '<div id="constant-contact-signup-errors" class="error">';
		$error_output .= '<ul>';
		foreach ($errors as $e) {
			if(is_array($e)) { $error_output .= '<li><label for="'.$e[1].'">'.$e[0].'</label></li>'; }
			else { $error_output .= '<li>'.$e.'</li>'; }
		}
		$error_output .= '</ul>';
		$error_output .= '</div>';

		// Filter output so text can be modified by plugins/themes
		$error_output = apply_filters('constant_contact_form_errors', $error_output);
	} // end if(isset($GLOBALS['cc_errors_'.$unique_id]))
	
	$form = str_replace('<!-- %%SUCCESS%% -->', '', $form);
	$form = str_replace('<!-- %%ERRORS%% -->', $error_output, $form);
	$form = str_replace('%%HASERROR%%', $haserror, $form);

	/**
	 * Show Description message if it was entered in options
	 */
	$widget_description = $description;
	if($widget_description) {
		// Format with wpautop() which adds <p> tags based on line returns similar to the post editor
		$widget_description = wpautop($widget_description);
		$output .= apply_filters('constant_contact_form_description', $widget_description);
	}
	
#	function kws_die() { die(); }
#	add_action('dynamic_sidebar', 'kws_die', 9999);
	
	/**
	 * Begin form output
	 */
	// Generate the current page url, removing the success _GET query arg if it exists
	$current_page_url = remove_query_arg('cc_success', constant_contact_current_page_url());
	#$output .= '<form action="'. $current_page_url .'" method="post" id="constant-contact-signup">';
	$form = str_replace('%%ACTION%%', $current_page_url, $form);

	/**
	 * If List Selection is active in options show the list of choices
	 */
	if($show_list_selection) {
		$listoutput = '';
		/**
		 * Show Multi-select format if it was chosen in options
		 */
		if($list_selection_format == 'select' || $list_selection_format == 'dropdown') {

			$listoutput .= '<label for="cc_newsletter_select">'.$list_selection_title .'</label>
			<div class="cc_newsletter kws_input_container input-text-wrap">
			<select name="cc_newsletter[]" id="cc_newsletter_select" ';
				if($list_selection_format !='dropdown') { $listoutput .= ' multiple size="5"'; }$listoutput .= '>';
				if($lists_to_show) {
					if($list_selection_format =='dropdown') { $listoutput .= '<option selected value="">Select a List</option>'; }
					foreach($lists_to_show as $k => $v) {
						if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])) {
							$listoutput .=  '<option selected value="'.$v['id'].'">'.$v['Name'].'</option>';
						} else {
							$listoutput .=  '<option value="'.$v['id'].'">'.$v['Name'].'</option>';
						}
					}
				} // end lists_to_show
			$listoutput .= '
			</select>
			</div>';

		/**
		 * Otherwise show the Checkbox format as long as there are lists to show
		 */
		} // endif get_option('cc_widget_list_selection_format') == 'select') 
		
		elseif(count($lists_to_show)) {

			$listoutput .=  $list_selection_title;
			$listoutput .=  '<div class="cc_newsletter kws_input_container input-text-wrap">';
			$listoutput .=  '<ul>';
			foreach($lists_to_show as $k => $v):
				if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])):
					$listoutput .=  '<li><label for="cc_newsletter-'.$v['id'].'"><input checked="checked" type="checkbox" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '</label></li>'; // ZK added label, ID, converted to <LI>
				else:
					$listoutput .=  '<li><label for="cc_newsletter-'.$v['id'].'"><input type="checkbox" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '</label></li>'; // ZK added label, ID
				endif;
			endforeach;
			$listoutput .=  '</ul>';
			$listoutput .=  '</div>';

		/**
		 * Otherwise show no lists. This means the user chose to auto-subscribe people to specific lists.
		 */
		} // end elseif(count($lists_to_show))
	
		
	} // endif(get_option('cc_widget_show_list_selection'))
	elseif(count($lists_to_show)) {
			foreach($lists_to_show as $k => $v):
				$hiddenlistoutput .=  '<input type="hidden" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" value="'.$v['id'].'" />';
			endforeach;
	}
	$form = str_replace('<!-- %%LISTSELECTION%% -->', $listoutput, $form);


	/**
	 * Finish form output including a hidden field for referrer and submit button
	 */
	$hiddenoutput = '
		<div>
			<input type="hidden" id="cc_redirect_url" name="cc_redirect_url" value="'. urlencode( $redirect_url ) .'" />
			<input type="hidden" id="cc_referral_url" name="cc_referral_url" value="'. urlencode( $current_page_url ) .'" />'.$hiddenlistoutput.'
				<input type="hidden" name="uniqueformid" value="'.$unique_id.'" />
		</div>';
#			$submit_button = '<input type="submit" name="constant-contact-signup-submit" value="Signup" class="button submit" />';
			// Filter output of submit button so it can be modified by themes/plugins
#			$output .= apply_filters('constant_contact_form_submit', $submit_button);
#			$hiddenoutput .= '
#		</div>';
	$form = str_replace('<!-- %%HIDDEN%% -->', $hiddenoutput, $form);
	
	// Modify the output by calling add_filter('constant_contact_form', 'your_function');
	$output = apply_filters('constant_contact_form', $form);

	/**
	 * Echo the output if $args['echo'] is true
	 */
	if ($echo) {
		echo $output;
	}
	
	/**
	 * Otherwise return the $output
	 */
	return $output;
}



function constant_contact_signup_form_field_mapping() {
	$signup_form_field_mapping = array('email_address'=>'EmailAddress','first_name' => 'FirstName','last_name' => 'LastName','middle_name' => 'MiddleName','company_name' => 'CompanyName','job_title' => 'JobTitle','home_number' => 'HomePhone','work_number' => 'WorkPhone','address_line_1' => 'Addr1','address_line_2' => 'Addr2','address_line_3' => 'Addr3','city_name' => 'City','state_code' => 'StateCode','state_name' => 'StateName','country_code' => 'CountryCode','zip_code' => 'PostalCode','sub_zip_code' => 'SubPostalCode','custom_field_1' => 'CustomField1','custom_field_2' => 'CustomField2','custom_field_3' => 'CustomField3','custom_field_4' => 'CustomField4','custom_field_5' => 'CustomField5','custom_field_6' => 'CustomField6','custom_field_7' => 'CustomField7','custom_field_8' => 'CustomField8','custom_field_9' => 'CustomField9','custom_field_10' => 'CustomField10','custom_field_11' => 'CustomField11','custom_field_12' => 'CustomField12','custom_field_13' => 'CustomField13','custom_field_14' => 'CustomField14', 'custom_field_15' => 'CustomField15');
	return $signup_form_field_mapping;
}

/**
 * Manage the results of signup forms submissions from the widget or shortcode.
 *
 * @global cc $cc
 * @return <type>
 */
function constant_contact_handle_public_signup_form() {
	global $cc;

	/**
	 * Check that the form was submitted and we have an email value, otherwise return false
	 */
	
	if(!isset($_POST['constant-contact-signup-submit'], $_POST['fields']['email_address'])) {
		return false;
	}
	
	$form_id = isset($_POST['uniqueformid']) ? esc_html($_POST['uniqueformid']) : 0;

	// Create the cc object if necessary
	if(!constant_contact_create_object()) { return false;}

	/**
	 * $errors array - this will contain any errors we want to add to our global for showing to the user
	 */
	$errors = array();

	/**
	 * $fields - Contains extra meta fields about this subscriber to send to the API
	 */
	$fields = array();
	
	$signup_form_field_mapping = constant_contact_signup_form_field_mapping();

	foreach($_POST['fields'] as $key => $field) {
	
		$value = isset($field['value']) ? esc_attr($field['value']) : '';
		
		// If the field is required...
		if(isset($field['req']) && $field['req'] == 1) {
			if(tempty($value)) {
				if(isset($field['label']) && !empty($field['label'])) {
					$errors[] = array('Please enter your '.$field['label'], $key);
				} else {
					$errors[] = array('Please enter all required fields', $key);
				}
			}
		}
		
		if($key == 'email_address' && (!is_email($field['value']) || !constant_contact_domain_exists($field['value']))) {
			$errors[] = array('Please enter a valid email address', 'constant-contact-api');
		}
		
		if(isset($signup_form_field_mapping[$key])) { $fields[$signup_form_field_mapping[$key]] = $value; }
		
	}
	
	if(isset($fields['StateCode']) && $fields['StateName']) {
		unset($fields['StateCode']);
	}
	if(isset($fields['CountryCode']) && strtolower($fields['CountryCode'] == 'usa')) { 
		$fields['CountryCode'] = 'us';
	}
	
	/**
	 * If we have registered errors then return them now and exit
	 */
	if($errors):
		$GLOBALS['cc_errors_'.$form_id] = $errors;
		return;
	endif;

	// URL to send user to upon successful subscription
	$redirect_to = isset($_POST['cc_redirect_url']) ? urldecode($_POST['cc_redirect_url']) : false; // Added logic and urldecode in 2.1.3

	/**
	 * Determine $subscribe_lists - flat array of IDs of lists that we will subscribe this user to
	 */
	$subscribe_lists = array();

	/**
	 * Get the full list of lists from the API (no transient cache)
	 */
	$all_lists = constant_contact_get_lists() ;

	if (isset($_POST['cc_newsletter']) AND is_array($_POST['cc_newsletter'])) :
		$selected_lists = $_POST['cc_newsletter'];

		/**
		 * Validate the choices in case the form showed invalid lists
		 * Add each one to $list_ids if it is valid
		 */
		foreach ($selected_lists as $list_id) :
			// For each selected list, loop through all_lists to see if tehre is a matching one
			foreach ($all_lists as $key => $details) :
				if ($details['id'] == $list_id)
					$subscribe_lists[] = $list_id;
			endforeach;
		endforeach;
	endif;

	/**
	 * If we have nothing in $list_id's return an error and exit
	 */
	if(empty($subscribe_lists)):
		$GLOBALS['cc_errors_'.$form_id][] = 'Please select at least 1 list.';
		return;
	endif;

	/**
	 * Connect to CC API and add/update the email address with the new subscriptions
	 */
	$cc->set_action_type('contact'); /* important, tell CC that the contact made this action */
	$contact = $cc->query_contacts($fields['EmailAddress']);
	
	if($contact):
		$contact = $cc->get_contact($contact['id']);
		$status = $cc->update_contact($contact['id'], $fields['EmailAddress'], $subscribe_lists, $fields);
	else:
		$status = $cc->create_contact($fields['EmailAddress'], $subscribe_lists, $fields);
	endif;
	
	/**
	 * If the call was unsuccessful show a generic error. 
	 */
	if(!$status):
		$GLOBALS['cc_errors_'.$form_id][] = 'Sorry there was a problem, please try again later';
		return;
	elseif($redirect_to):
		$redirect_to = apply_filters('constant_contact_add_success_param', true) ? add_query_arg('cc_success', true, $redirect_to) : $redirect_to;
		header("Location: {$redirect_to}");
		exit;
	else:
		$url = add_query_arg('cc_success', true, urldecode($_POST['cc_referral_url']));
		header("Location: {$url}");
		exit;
	endif;

	// return false so we display no errors when viewing the form
	// the script should not get this far
	return false;
}


function constant_contact_domain_exists($email,$record = 'MX') {
	if(apply_filters('constant_contact_validate_email_domain', 1)) {
		list($user,$domain) = split('@',$email);
		return checkdnsrr($domain,$record);
	}
	return true;
}

/*
* From http://www.webcheatsheet.com/PHP/get_current_page_url.php
*/

function constant_contact_current_page_url() {
	 $pageURL = 'http';
	 if (isset($_SERVER["HTTPS"]) AND ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return remove_query_arg('cc_success',$pageURL);
}

// Add the Settings link on the Plugins page
function constant_contact_settings_link( $links, $file ) {
	if ( $file == 'constant-contact-api/constant-contact-api.php' ) {
	    $settings_link = '<a href="' . admin_url( 'options-general.php?page=constant-contact-api' ) . '">' . __('Settings', 'constant-contact-api') . '</a>';
	    array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

function constant_contact_create_checkbox($id, $name, $value, $label='') {
		return "<label for='$id'>$label<input id='$id' name='$id\[\]' type='checkbox' value='$value' />$name</label>\n";
}

function constant_contact_create_option($name, $value) {
	return "<option value='$value'>$name</option>\n";
}
     
function constant_contact_make_textfield($initiated = false, $required=false, $default, $setting = '', $fieldid = '', $fieldname='', $title = '') {
	
	if(!$initiated || ($required && empty($setting))) { $setting = $default; }
    
	$input = '
	<p>
		<label for="'.$fieldid.'">'.__($title,'constant-contact-api').'
		<input type="text" class="widefat" id="'.$fieldid.'" name="'.$fieldname.'" value="'.$setting.'"/>
		</label>
	</p>';
	
	echo $input;
}
function constant_contact_make_textarea($initiated = false, $required=false, $default, $setting = '', $fieldid = '', $fieldname='', $title = '') {
	
	if(!$initiated || ($required && empty($setting))) { $setting = $default; }
    
	$input = '
	<p>
		<label for="'.$fieldid.'">'.__($title,'constant-contact-api').'
		<textarea class="widefat" id="'.$fieldid.'" name="'.$fieldname.'" cols="40" rows="5">'.$setting.'</textarea>
		</label>
	</p>';
	
	echo $input;
}
    
function constant_contact_make_checkbox($setting = '', $fieldid = '', $fieldname='', $title = '', $value = 'yes', $checked = false, $disabled = false) {
	echo constant_contact_get_checkbox($setting, $fieldid, $fieldname, $title, $value, $checked,$disabled);
}
function constant_contact_get_checkbox($setting = '', $fieldid = '', $fieldname='', $title = '', $value = 'yes', $checked = false, $disabled = false) {
	$checkbox = '
		<input type="checkbox" id="'.$fieldid.'" name="'.$fieldname.'" value="'.$value.'"';
			if($checked || !empty($setting)) { $checkbox .= ' checked="checked"'; }
			if($disabled)  { $checkbox .= ' disabled="disabled"';}
			$checkbox .= ' class="checkbox" />
		<label for="'.$fieldid.'">'.__($title,'constant-contact-api').'</label>';
    return $checkbox;
}

// Added in version 1.2 instead of storing in options table - this caches the list results
// and uses it if available instead of re-fetching and processing the feed
// from constant contact. Saves much time.
function constant_contact_get_transient($key = false) {
	   if(!isset($_REQUEST['cache'])) {
		$transient = get_transient('cc_api');
		
		if(!$key && !empty($transient)) { return $transient; } elseif(empty($transient)) { return false; }
		
		// If there is a cache, use it. Storing in an array allows all reports to be in one cache key
		// Could not be the best way. Email info@katzwebservices.com if you know how to improve!
		if(!empty($transient[$key])) {
			return apply_filters('cc_api', maybe_unserialize(maybe_unserialize($transient[$key])));
		}
		return false;
	} else {
		return false;
	}
}


function constant_contact_set_transient($key = false, $data, $time = 172800) {
	// $time = 48 hours. If you want to modify it you can.
	$time = apply_filters('cc_api_cache_time', $time);
	
	if(!is_admin()) {
		$transient = get_transient('cc_api');
		if(!empty($key)) {
			$transient[$key] = $data;				
			// Set a cached version of the table so it'll be faster next time. Expires every 12 hours
			set_transient('cc_api',$transient, $time);
		}
	}
	
	return $data;
}

function constant_contact_create_location($v = array()) {
	if(empty($v)) { return ''; }
	foreach($v as $key=> $l)  { $v[$key] = (string)get_if_not_empty($l, ''); }
	extract($v);
	$Location = @get_if_not_empty($Location,'', "{$Location}<br />");
	$LocationForMap = @get_if_not_empty($LocationForMap,'', "<br />{$LocationForMap}");
	$Address1 = @get_if_not_empty($Address1, '', $Address1.'<br />');
	$Address2 = @get_if_not_empty($Address2, '', $Address2.'<br />');
	$Address3 = @get_if_not_empty($Address3, '', $Address3.'<br />');
	$City = @get_if_not_empty($City,'', "{$City}, ");
	$State = @get_if_not_empty($State,'', "{$State} ");
	$Country = @get_if_not_empty($Country,'', "<br />{$Country}");
	return apply_filters('constant_contact_create_location', "{$Location}{$Address1}{$Address2}{$Address3}{$City}{$State}{$PostalCode}{$Country}");
}

function constant_contact_event_date($value = null) {
	global $cc;
	$timestamp = (int)$cc->convert_timestamp($value);
	return sprintf(__('%1$s at %2$s','constant-contact-api'), date_i18n(get_option('date_format'), $timestamp, true), date_i18n(get_option('time_format'), $timestamp, true));
}


function constant_contact_get_active_events() {
	global $cc;
	
	if(!constant_contact_create_object()) { return false; }
	
	$_events = $cc->get_events();

	if(!empty($_events) && is_array($_events)) {
		$draft = $active = array();
		foreach($_events as $k => $v) {
			if(isset($v['Status']) && $v['Status'] == 'ACTIVE') {
				$active[$v['id']] = $v;
			}
		}
	}

	return $active;

}

function constant_contact_events_print_widget_styles() {
	add_action('wp_print_footer_scripts', 'constant_contact_events_widget_styles_echo');
}

if(!function_exists('constant_contact_events_widget_styles_echo')) {
	function constant_contact_events_widget_styles_echo() {
		if(!wp_style_is('cc_events', 'done')) {
			echo "\n".'<link href="'.plugin_dir_url(__FILE__).'css/events.css'.'" type="text/css" media="all" rel="stylesheet" />'."\n";
		}
	}
}

// Create events shortcode
add_shortcode('ccevents', 'constant_contact_get_events_output');
add_shortcode('constantcontactevents', 'constant_contact_get_events_output');
		
function constant_contact_get_events_output($args = array(), $sidebar = false) {
	global $cc;
	$class = 'cc_event';
	
	if(!constant_contact_create_object()) { return false; }
	
	$output = $oddeven = '';

	$settings = shortcode_atts(array('title' => '', 'description' => '', 'limit' => 3, 'showdescription' => true, 'datetime' => true, 'location' => false, 'calendar' => false, 'directtoregistration' => false, 'style' => true, 'id' => false, 'newwindow' => false, 'map' => false), $args);
	
	foreach($settings as $key => $arg) {
		if(strtolower($arg) == 'false' || empty($arg)) {
			$settings["{$key}"] = false;
		}
	}
	
	extract( $settings );
	
	if(!empty($style)) { constant_contact_events_print_widget_styles(); }
	
	if($id === false) {
		$events = constant_contact_get_active_events();
		$class .= ' multiple_events';
	} else {
		$class .= ' single_event';
		$events = array($cc->get_event($id));
	}
	
	$class .= ($sidebar) ? ' cc_event_sidebar' : ' cc_event_shortcode';

		if(!empty($events)) {
			
			$startOut = $descriptionOut = $dateOut = $calendarOut = $locationOut = $titleOut = $endOut = '';
			
			$i = 0;
			foreach($events as $event) {
				if($i >= $limit) { continue; }
				$oddeven = ($oddeven == ' even') ? ' odd' : ' even'; 
				
				$event = $cc->get_event($event['id'], 60*60*24);
				
				if(empty($event)) { continue; }
				
				extract($event);
				
				if(!empty($directtoregistration)) {
					$link = str_replace('/register/event?', '/register/eventReg?', $RegistrationURL);
				} else {
					$link = str_replace('https://', 'http://', $RegistrationURL);
				}
				
				$linkTitle = apply_filters('cc_event_linktitle', sprintf( __('View event details for "%s"','constant-contact-api'), $Title));
				if(!empty($linkTitle)) { $linkTitle = ' title="'.esc_html($linkTitle).'"'; }
				
				$class = apply_filters('cc_event_class', $class);
				$target = $newwindow ? apply_filters('cc_event_new_window', ' target="_blank"') : '';
				$startOut = '
				<dl class="'.$class.$oddeven.'">';
					$titleOut = '
					<dt class="cc_event_title"><a'.$target.' href="'.$link.'"'.$linkTitle.'>'.$Title.'</a></dt>';
					if(!empty($showdescription) && !empty($Description)) {
					$descriptionOut = '
					<dd class="cc_event_description">'.wpautop($Description).'</dd>';
					}
					if(!empty($datetime)) {
					$dateOut = '
					<dt class="cc_event_startdate_dt">'.apply_filters('cc_event_startdate_dt', __('Start: ','constant-contact-api')).'</dt>
						<dd class="cc_event_startdate_dd">'.apply_filters('cc_event_startdate', $StartDate).'</dd>
					<dt class="cc_event_enddate_dt">'.apply_filters('cc_event_enddate_dt', __('End: ','constant-contact-api')).'</dt>
						<dd class="cc_event_enddate_dd">'.apply_filters('cc_event_enddate', $EndDate).'</dd>
						';
					}
					if(!empty($calendar)) {
						$link = str_replace('/register/event?', '/register/addtocalendar?', $RegistrationURL);
						$linkTitle = apply_filters('cc_event_linktitle', sprintf( __('Add "%s" to your calendar','constant-contact-api'), $Title));
						if(!empty($linkTitle)) { $linkTitle = ' title="'.esc_html($linkTitle).'"'; }
						$calendarOut = '
					<dd class="cc_event_calendar"><a'.$target.' href="'.$link.'"'.$linkTitle.'>'.__('Add to Calendar','constant-contact-api').'</a></dd>
						';
					}
					if(!empty($location)) {
						$locationText = constant_contact_create_location($EventLocation);
						if($map) {
							if(isset($EventLocation['Location'])) { $EventLocation['NewLocation'] = '('.$EventLocation['Location'].')'; unset($EventLocation['Location']); }
							#if(isset($EventLocation['Address3'])) { unset($EventLocation['Address3']); }
							$locationformap = trim(constant_contact_create_location($EventLocation));
							$address_qs = str_replace("<br />", ", ", $locationformap.'<br />'.$EventLocation['NewLocation']); //replacing <br/> with spaces
							$address_qs = urlencode($address_qs);
							$locationText .= "<br/>".apply_filters('cc_event_map_link', "<a href='http://maps.google.com/maps?q=$address_qs'".$target." class='cc_event_map_link'>".__('Map Location','constant-contact-api')."</a>", $EventLocation, $address_qs);
						}
						
						$locationOut = '
					<dt class="cc_event_location cc_event_location_dt">'.apply_filters('cc_event_location_dt', __('Location: ','constant-contact-api')).'</dt>
						<dd class="cc_event_location_dd cc_event_location">'.apply_filters('cc_event_location', $locationText).'</dd>';
					}
					
				$endOut = '
				</dl>';
				
				$output .= apply_filters('cc_event_output_single', $startOut.$titleOut.$descriptionOut.$dateOut.$calendarOut.$locationOut.$endOut, array('start'=>$startOut,'title'=>$titleOut,'description'=>$descriptionOut,'date'=>$dateOut,'calendar'=>$calendarOut,'location' => $locationOut, 'end'=>$endOut));
				
				$i++;	
			}
			
		} else {
			$output = apply_filters('cc_event_no_events_text', '<p>'.__('There are currently no events.','constant-contact-api').'</p>');
		}
	$output = apply_filters('cc_event_output', $output);
	return $output;
}

function constant_contact_latest_registrant($id, $showcancelled = false) {
	global $cc;
	
	if(!constant_contact_create_object()) { return false; }
	
	$_registrants = $cc->get_event_registrants($id);
	
	foreach($_registrants as $key => $reg) {
		$latest = 0;
		if(isset($reg['RegistrationStatus']) && strtolower($reg['RegistrationStatus']) !== 'cancelled') {
			$timestamp = (int)$cc->convert_timestamp($reg['RegistrationDate']);
			$_registrants[$key]['RegistrationTimestamp'] = $timestamp;
			
			if($timestamp > $latest) { $latest = $timestamp; }
			
		} else {
			unset($_registrants[$key]);
		}
	}
	if(empty($timestamp)) {
		return __('N/A','constant-contact-api');
	} else {
		return sprintf(__('%1$s at %2$s','constant-contact-api'), date_i18n(get_option('date_format'), $timestamp, true), date_i18n(get_option('time_format'), $timestamp, true));
	}
}

function constant_contact_reset_lists_transients() {
	$lists = constant_contact_get_lists();
				
	foreach($lists as $key => $details) {
		delete_transient('listmembers'.$details['id']);	
	}
	return;
}

function constant_contact_admin_refresh($name = '') {
		$matches = array();
	if(!empty($name)) {
		$matches[1] = $name;
	} else{ 
		if(isset($_GET['page'])) {
			preg_match('/constant\-contact\-(.*)/ism', $_GET['page'], $matches);
		}
	}
	if(empty($matches[1])) { return; }
	if(isset($_GET['id'])) { 
		$single= true; 
		if(substr($matches[1], -3, 3) == 'ies') {
			$matches[1] = substr($matches[1], 0, (strlen($matches[1]) -3));
			$matches[1] .= 'y';
		}else if(substr($matches[1], -1, 1) == 's') {
			$matches[1] = substr($matches[1], 0, (strlen($matches[1]) -1));
		}
	} else { $single = false; }
	?>
	
	<p class="alignright"><label class="howto" for="refresh_<?php echo $matches[1]; ?>"><span><?php echo $single ? 'Is' : 'Are'; ?> the displayed <?php echo $matches[1]; ?> inaccurate?</span> <a href="<?php echo add_query_arg('refresh', $matches[1], remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" class="button-secondary action" id="refresh_<?php echo $matches[1]; ?>">Refresh <?php echo ucwords($matches[1]); ?></a></label></p>
	<?php
	
}

function constant_contact_plugin_page_list($hide = true) {
	?>
	<div class="wrap constant_contact_plugin_page_list <?php if($hide !== false) { echo ' cc_hidden'; } ?>" style="padding-bottom:10px; background:white;<?php if($hide !== false) { echo ' display:none;'; } ?>">
		<h2>Plugin Pages</h2>
		<h3>Plugin Configuration</h3>
		<ul class="ul-disc">
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-registration'); ?>">Registration &amp; Profile</a> - Configure plugin settings for adding newletter signup capabilities to the WordPress registration form.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-analytics'); ?>">Constant Analytics Settings</a> - Configure Google Analytics reports.</li>
			<?php if(defined('CC_FORM_GEN_PATH')) { ?>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-forms'); ?>">Form Designer</a> - Design a signup form from the ground up.</li>
			<?php } ?>
		</ul>
		<h3>Account Actions</h3>
		<ul class="ul-disc">
			<li><a href="<?php echo admin_url('index.php?page=constant-analytics.php'); ?>">Constant Analytics</a> - View Google Analytics and Constant Contact data directly in your dashboard.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-contacts'); ?>">Contacts</a> - View, add, edit and delete your contacts.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-lists'); ?>">Lists</a> - Add, remove, and edit your contact lists.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>">Events</a> - View your Constant Contact Event Marketing data: events and registrant information.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-import'); ?>">Import</a> - Import contacts into your choice of user lists.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-export'); ?>">Export</a> - Export contacts to <code>.csv</code> and <code>.txt</code> format.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-activities'); ?>">Activities</a> - View your account's recent activity, including: sent campaigns, exports, and imports.</li>
			<li><a href="<?php echo admin_url('admin.php?page=constant-contact-campaigns'); ?>">Campaigns</a> - View details of your sent &amp; draft email campaigns, <strong>including email campaign stats</strong> such as # Sent, Opens, Clicks, Bounces, OptOuts, and Spam Reports.</li>
		</ul>
	</div>
<?php
}

function constant_contact_generate_google_profiles() {
	global $ccStats_ga_token;
	
	list($profiles, $profile_options, $ccStats_ga_profile_id) = array(array(), array(),array());
	
	if (!empty($ccStats_ga_token)) {
		$url = 'https://www.google.com/analytics/feeds/accounts/default';
					
		$wp_http = ccStats_get_wp_http();
		$request_args = array(
			'headers' => ccStats_get_authsub_headers(),
			'sslverify' => false
		);
		$result = $wp_http->request(
			$url,
			$request_args
		);

		if (is_wp_error($result)) {
			$connection_errors = $result->get_error_messages();
		}
		else {
			$http_code = $result['response']['code'];
			$ga_auth_error = '';
			if ($http_code != 200) {
				$ga_auth_error = $result['response']['code'].': '.$result['response']['message'];
				//$ga_auth_error = $result['body'];
			}
			else {
				$xml = simplexml_load_string($result['body']);

				$profiles = array();
				foreach($xml->entry as $entry) {
					$properties = array();
					$children = $entry->children('http://schemas.google.com/analytics/2009');
					foreach($children->property as $property) {
						$attr = $property->attributes();
						$properties[str_replace('ga:', '', $attr->name)] = strval($attr->value);
					}
					$properties['title'] = strval($entry->title);
					$properties['updated'] = strval($entry->updated);
					$profiles[$properties['profileId']] = $properties;
				}
				if (count($profiles)) {
					global $ccStats_ga_profile_id;
					if (empty($ccStats_ga_profile_id)) {
						$ccStats_ga_profile_id = $properties['profileId'];	// just use the last one
						update_option('ccStats_ga_profile_id', $ccStats_ga_profile_id);
					}
					if (count($profiles) > 1) {
						$profile_options = array();
						foreach ($profiles as $id => $profile) {
							$profile_options[] = '<option value="'.$id.'"'.($ccStats_ga_profile_id == $id ? 'selected="selected"' : '').'>'.$profile['title'].'</option>';
						}
					}
				}
			}
		}
	}
	return array($profiles, $profile_options, $ccStats_ga_profile_id);
}

function constant_contact_google_authentication_url() {
	return 'https://www.google.com/accounts/AuthSubRequest?'.http_build_query(array(
		'next' => admin_url('admin.php?ccStats_action=capture_ga_token'),
		'scope' => 'https://www.google.com/analytics/feeds/',
		'secure' => is_ssl(),
		'session' => 1
	));
}

function constant_contact_is_plugin_page() {
	global $page_hook,$current_screen;
#	print_r($GLOBALS);
	if(preg_match('/constant-(contact|analytics)/ism', $current_screen->id)) {
		return true;
	} 
	return false;	
}

function get_if_not_empty($check = null, $empty = '', $echo = false) {
	if(!isset($check) || (empty($check) && $check !== 0)) { return $empty; }
	if(!$echo) { return $check; }
	return $echo;
}

function echo_if_not_empty($check = null, $empty = '', $echo = false) {
	echo get_if_not_empty($check, $empty, $echo);
}

?>