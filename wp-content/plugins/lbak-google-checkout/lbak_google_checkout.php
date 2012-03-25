<?php
/*
    Plugin Name: LBAK Google Checkout
    Plugin URI: http://wordpress.org/extend/plugins/lbak-google-checkout/
    Donate link: http://donate.lbak.co.uk/
    Description: An easy to use plugin that integrates Google Checkout into your blog.
    Author: Sam Rose
    Version: 1.3.4
    Author URI: http://lbak.co.uk/
*/

/*
    LBAK Google Checkout Copyright (C) 2010  Sam Rose  (email : samwho@lbak.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU GPL v2.

    This plugin is distributed without any warranty. The plugin author will not
    take any responsibility for the data or actions of this software. The
    plugin author will attempt to support the plugin as much as possible but
    there are no guarentees of support or help from the author.
*/

/*
 * Get the base directory for this current plugin.
*/
function lbakgc_get_base_url() {
    return WP_PLUGIN_URL. '/'. basename(dirname(__FILE__));
}

function lbakgc_get_version() {
    return '1.3.4';
}

require_once 'php/upgrades.php';
require_once 'php/housekeeping.php';
require_once 'php/visual.php';
require_once 'php/main.php';

add_action('wp_dashboard_setup', 'lbakgc_dashboard_setup');
add_action('admin_menu', 'lbakgc_admin_menu');
add_action('wp_head', 'lbakgc_add_header');
add_action('admin_head', 'lbakgc_add_header');
add_filter('the_content', 'lbakgc_parse_shortcode');
register_activation_hook(__FILE__, 'lbakgc_activation_setup');
register_uninstall_hook(__FILE__, 'lbakgc_uninstall');
register_deactivation_hook(__FILE__, 'lbakgc_deactivate');

// i18n
$plugin_dir = basename(dirname(__FILE__));
$languages_dir = $plugin_dir.'/languages';
load_plugin_textdomain( 'lbakgc', WP_PLUGIN_DIR.'/'.$languages_dir,
        $languages_dir );

?>
