<?php
// $Id$

/**
 * Define default settings for the plugin
 * These are here purely for convience
 * They can all be overridden on the settings page
 */

// when the plugin is deactivated how should we treat the settings, remove or keep
define('CC_UNINSTALL_METHOD', 'keep');

// method to use for the register page, can be either checkbox or lists
define('CC_REGISTER_PAGE_METHOD', 'none');

// determines if we should opt-in users by default (register page)
define('CC_DEFAULT_OPT_IN', 1);

// determines if we should show the list selection in the widget signup form
define('CC_WIDGET_SHOW_LIST_SELECTION', 1);

// the title of the signup checkbox box or list selection
define('CC_SIGNUP_TITLE', 'Newsletter');

// the description of the signup checkbox box or list selection
define('CC_SIGNUP_DESCRIPTION', 'Subscribe to the Newsletter');

// the widget title of the signup checkbox box or list selection
define('CC_SIGNUP_WIDGET_TITLE', 'Newsletter');

// the widget description of the signup checkbox box or list selection
define('CC_SIGNUP_WIDGET_DESCRIPTION', 'Subscribe to the Newsletter');

// the widget title for the list selection
define('cc_widget_list_selection_title', 'Contact Lists:');

// The URL for the Constant Contact 60-day trial
define('CC_TRIAL_URL', 'http://bit.ly/cctrial');

// The format for the list selection form element, checkbox or select
define('CC_LIST_SELECTION_FORMAT', 'checkbox');

// should we show the firstname field in the signup widget
define('CC_WIDGET_SHOW_FIRSTNAME', 1);

// should we show the lastname field in the signup widget
define('CC_WIDGET_SHOW_LASTNAME', 1);

// The format for the list selection form element in the signup widget, checkbox or select
define('CC_WIDGET_LIST_SELECTION_FORMAT', 'checkbox');

// The full path to this file
define('CC_FILE_PATH', dirname(__FILE__) . '/');

// @ Added 2.0
// The full path to this file
define('CC_FILE_URL', plugin_dir_url(__FILE__));

// To store the object in a session.
if(!session_id()) {
	session_start();
}

?>