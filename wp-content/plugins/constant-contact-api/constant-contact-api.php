<?php
/*
Plugin Name: Constant Contact API
Plugin URI: http://integrationservic.es/constant-contact/wordpress-plugin.php
Description: Powerfully integrates <a href="http://conta.cc/bRojlN" target="_blank">Constant Contact</a> into your WordPress website.
Author: Katz Web Services, Inc. & James Benson
Version: 2.3.6
Author URI: http://www.katzwebservices.com
*/

add_action('plugins_loaded', 'constant_contact_setup_plugin', 1);

function constant_contact_setup_plugin() {
	
	// For language internationalization
	load_plugin_textdomain( 'constant-contact-api', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	define('CC_FILE_PATH', dirname(__FILE__) . '/'); // The full path to this file
	define('CC_FILE_URL', plugin_dir_url(__FILE__)); // @ Added 2.0 The full URL to this file
	
	// To store the object in a session, start a session if not initiated already.
	if(!session_id()) { session_start(); }

	require_once CC_FILE_PATH . 'functions.php';
	require_once CC_FILE_PATH . 'user.php';
	require_once CC_FILE_PATH . 'widget-legacy.php';
	require_once CC_FILE_PATH . 'widget-events.php'; // Added 2.2
	
	// load admin only files
	if(is_admin()) {
		require_once CC_FILE_PATH . 'admin/install.php';
		require_once CC_FILE_PATH . 'admin/menu.php';
		require_once CC_FILE_PATH . 'admin/options.php';
		require_once CC_FILE_PATH . 'admin/import.php';
		require_once CC_FILE_PATH . 'admin/export.php';
		require_once CC_FILE_PATH . 'admin/lists.php';
		require_once CC_FILE_PATH . 'admin/activities.php';
		require_once CC_FILE_PATH . 'admin/registration.php'; // Added 2.0
		require_once CC_FILE_PATH . 'admin/campaigns.php'; // Added 2.0
		require_once CC_FILE_PATH . 'admin/events.php'; // Added 2.1
		require_once CC_FILE_PATH . 'admin/contacts.php'; // Added 2.3
		require_once CC_FILE_PATH . 'constant-analytics.php'; // Added 2.3
		
		// register admin menu action
		add_action('admin_menu', 'constant_contact_admin_menu');

		// register user delete action
		add_action('delete_user', 'constant_contact_delete_user');

		// register the install / uninstall hooks
		register_activation_hook( __FILE__, 'constant_contact_activate' );
		register_deactivation_hook( __FILE__, 'constant_contact_deactivate' );

		// Add the handy Settings link on the plugins page
		add_filter( 'plugin_action_links', 'constant_contact_settings_link', 10, 2 );
		
		add_action('admin_footer', 'constant_contact_plugin_page_list');
		
		add_action('admin_print_scripts', 'constant_contact_enquque_core_scripts');
		add_action('admin_print_styles', 'constant_contact_enquque_core_styles');
	}
	
	// register legacy widget
	add_action('widgets_init', 'constant_contact_load_legacy_widget');
	add_action('widgets_init', 'constant_contact_load_events_widget'); // Added 2.2

	// register post handlers
	add_action('init', 'constant_contact_handle_public_signup_form');
	
	// register user update action
	add_action('profile_update', 'constant_contact_profile_update');

	// register show user update form action
	add_action('show_user_profile', 'constant_contact_show_user_profile');

	// register user registration action
	add_action('register_post', 'constant_contact_register_post', 10, 3);
	add_filter('wpmu_signup_user_notification', 'constant_contact_register_post_multisite'); // For multisite

	// register show user register form action
	add_action('signup_extra_fields', 'constant_contact_register_form'); // For multisite
	add_action('register_form', 'constant_contact_register_form');
}
	
?>