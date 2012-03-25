<?php

function replace_calendar_styles() {
	wp_dequeue_style('ai1ec-general');
	wp_dequeue_style('ai1ec-calendar');
}

function replace_calendar_scripts() {
	wp_deregister_script('modernizr.custom.78720');
}

add_action('wp_print_styles', 'replace_calendar_styles');
add_action('wp_print_scripts', 'replace_calendar_scripts', 100);