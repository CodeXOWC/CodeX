<?php
//
//  class-ai1ec-settings.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Settings class
 *
 * @package Models
 * @author The Seed Studio
 **/
class Ai1ec_Settings {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * calendar_page_id class variable
	 *
	 * @var int
	 **/
	var $calendar_page_id;

	/**
	 * default_calendar_view class variable
	 *
	 * @var string
	 **/
	var $default_calendar_view;

	/**
	 * week_start_day class variable
	 *
	 * @var int
	 **/
	var $week_start_day;

	/**
	 * agenda_events_per_page class variable
	 *
	 * @var int
	 **/
	var $agenda_events_per_page;

	/**
	 * calendar_css_selector class variable
	 *
	 * @var string
	 **/
	var $calendar_css_selector;

	/**
	 * include_events_in_rss class variable
	 *
	 * @var bool
	 **/
	var $include_events_in_rss;

	/**
	 * allow_publish_to_facebook class variable
	 *
	 * @var bool
	 **/
	var $allow_publish_to_facebook;

	/**
	 * facebook_credentials class variable
	 *
	 * @var array
	 **/
	var $facebook_credentials;

	/**
	 * user_role_can_create_event class variable
	 *
	 * @var bool
	 **/
	var $user_role_can_create_event;

	/**
	 * cron_freq class variable
	 *
	 * Cron frequency
	 *
	 * @var string
	 **/
	var $cron_freq;
	
	/**
	 * timezone class variable
	 *
	 * @var string
	 **/
	var $timezone;

	/**
	 * exclude_from_search class variable
	 *
	 * Whether to exclude events from search results
	 * @var bool
	 **/
	var $exclude_from_search;

	/**
	 * show_publish_button class variable
	 *
	 * Display publish button at the bottom of the
	 * submission form
	 *
	 * @var bool
	 **/
	var $show_publish_button;
	
	/**
	 * hide_maps_until_clicked class variable
	 *
	 * When this setting is on, instead of showing the Google Map, 
	 * show a dotted-line box containing the text "Click to view map", 
	 * and when clicked, this box is replaced by the Google Map.
	 *
	 * @var bool
	 **/
	var $hide_maps_until_clicked;
	
	/**
	 * agenda_events_expanded class variable
	 *
	 * When this setting is on, events are expanded
	 * in agenda view
	 *
	 * @var bool
	 **/
	var $agenda_events_expanded;

	/**
	 * show_create_event_button class variable
	 *
	 * Display "Post Your Event" button on the calendar page for those users with
	 * the privilege.
	 *
	 * @var bool
	 **/
	var $show_create_event_button;

	/**
	 * turn_off_subscription_buttons class variable
	 *
	 * Hides "Subscribe"/"Add to Calendar" and same Google buttons in calendar and
	 * single event views
	 *
	 * @var bool
	 **/
	var $turn_off_subscription_buttons;

	/**
	 * inject_categories class variable
	 *
	 * Include Event Categories as part of the output of the wp_list_categories()
	 * template tag.
	 *
	 * @var bool
	 **/
	var $inject_categories;

	/**
	 * input_date_format class variable
	 *
	 * Date format used for date input. For supported formats
	 * @see jquery.calendrical.js
	 *
	 * @var string
	 **/
	var $input_date_format;
	
	/**
	 * input_24h_time class variable
	 *
	 * Use 24h time in time pickers. 
	 *
	 * @var bool
	 **/
	var $input_24h_time;

	/**
	 * settings_page class variable
	 *
	 * Stores a reference to the settings page added using
	 * add_submenu_page function
	 *
	 * @var object
	 **/
	var $settings_page;
	
	/**
	 * geo_region_biasing class variable
	 *
	 * If set to true the ISO-3166 part of the configured
	 * locale in WordPress is going to be used to bias the
	 * geo autocomplete plugin towards a specific region.
	 *
	 * @var bool
	 **/
	var $geo_region_biasing;

	/**
	 * __construct function
	 *
	 * Default constructor
	 **/
	private function __construct() {
		$this->set_defaults(); // set default settings
 	}

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance()
 	{
		if( self::$_instance === NULL ) {
			// get the settings from the database
			self::$_instance = get_option( 'ai1ec_settings' );

			// if there are no settings in the database
			// save default values for the settings
			if( ! self::$_instance ) {
				self::$_instance = new self();
				delete_option( 'ai1ec_settings' );
				add_option( 'ai1ec_settings', self::$_instance );
			} else {
				self::$_instance->set_defaults(); // set default settings
			}
		}

		return self::$_instance;
	}

	/**
	 * save function
	 *
	 * Save settings to the database.
	 *
	 * @return void
	 **/
	function save() {
		update_option( 'ai1ec_settings', $this );
		update_option( 'start_of_week', $this->week_start_day );
		update_option( 'ai1ec_cron_version', get_option( 'ai1ec_cron_version' ) + 1 );
		update_option( 'timezone_string', $this->timezone );
	}

	/**
	 * set_defaults function
	 *
	 * Set default values for settings.
	 *
	 * @return void
	 **/
	function set_defaults() {
		$defaults = array(
			'calendar_page_id'              => 0,
			'default_calendar_view'         => 'month',
			'calendar_css_selector'         => '',
			'week_start_day'                => get_option( 'start_of_week' ),
			'agenda_events_per_page'        => get_option( 'posts_per_page' ),
			'agenda_events_expanded'        => false,
			'include_events_in_rss'         => false,
			'allow_publish_to_facebook'     => false,
			'facebook_credentials'          => null,
			'user_role_can_create_event'    => null,
			'show_publish_button'           => false,
			'hide_maps_until_clicked'       => false,
			'exclude_from_search'           => false,
			'show_create_event_button'      => false,
			'turn_off_subscription_buttons' => false,
			'inject_categories'             => false,
			'input_date_format'             => 'def',
			'input_24h_time'                => false,
			'cron_freq'                     => 'daily',
			'timezone'                      => get_option( 'timezone_string' ),
			'geo_region_biasing'            => false
		);

		foreach( $defaults as $key => $default ) {
			if( ! isset( $this->$key ) )
				$this->$key = $default;
		}
	}

	/**
	 * update function
	 *
	 * Updates field values with corresponding values found in $params
	 * associative array.
	 *
	 * @param array $params
	 *
	 * @return void
	 **/
	function update( $params ) {
		$this->update_page( 'calendar_page_id', $params );
		if( isset( $params['default_calendar_view']         ) ) $this->default_calendar_view          = $params['default_calendar_view'];
		if( isset( $params['calendar_css_selector']         ) ) $this->calendar_css_selector          = $params['calendar_css_selector'];
		if( isset( $params['week_start_day']                ) ) $this->week_start_day                 = $params['week_start_day'];
		if( isset( $params['agenda_events_per_page']        ) ) $this->agenda_events_per_page         = intval( $params['agenda_events_per_page'] );
		if( isset( $params['cron_freq']                     ) ) $this->cron_freq                      = $params['cron_freq'];
		if( isset( $params['input_date_format']             ) ) $this->input_date_format              = $params['input_date_format'];
		if( isset( $params['allow_events_posting_facebook'] ) ) $this->allow_events_posting_facebook  = $params['allow_events_posting_facebook'];
		if( isset( $params['facebook_credentials']          ) ) $this->facebook_credentials           = $params['facebook_credentials'];
		if( isset( $params['user_role_can_create_event']    ) ) $this->user_role_can_create_event     = $params['user_role_can_create_event'];
		if( isset( $params['timezone']                      ) ) $this->timezone                       = $params['timezone'];
		if( $this->agenda_events_per_page <= 0                ) $this->agenda_events_per_page         = 1;
		
		// checkboxes
		$this->agenda_events_expanded        = ( isset( $params['agenda_events_expanded'] ) )        ? true : false;
		$this->include_events_in_rss         = ( isset( $params['include_events_in_rss'] ) )         ? true : false;
		$this->show_publish_button           = ( isset( $params['show_publish_button'] ) )           ? true : false;
		$this->hide_maps_until_clicked       = ( isset( $params['hide_maps_until_clicked'] ) )       ? true : false;
		$this->exclude_from_search           = ( isset( $params['exclude_from_search'] ) )           ? true : false;
		$this->show_create_event_button      = ( isset( $params['show_create_event_button'] ) )      ? true : false;
		$this->turn_off_subscription_buttons = ( isset( $params['turn_off_subscription_buttons'] ) ) ? true : false;
		$this->inject_categories             = ( isset( $params['inject_categories'] ) )             ? true : false;
		$this->input_24h_time                = ( isset( $params['input_24h_time'] ) )                ? true : false;
		$this->geo_region_biasing            = ( isset( $params['geo_region_biasing'] ) )            ? true : false;
	}

	/**
	 * update_page function
	 *
	 * Update page for the calendar with the one specified by the drop-down box.
	 * If the value is not numeric, user chose to auto-create a new page,
	 * therefore do so.
	 *
	 * @param string $field_name
	 * @param array $params
	 *
	 * @return void
	 **/
	function update_page( $field_name, &$params ) {
		if( ! is_numeric( $params[$field_name] ) &&
		    preg_match( '#^__auto_page:(.*?)$#', $params[$field_name], $matches ) )
	 	{
			$this->$field_name = $params[$field_name] = $this->auto_add_page( $matches[1] );
		} else {
			$this->$field_name = (int) $params[$field_name];
		}
	}

	/**
	 * auto_add_page function
	 *
	 * Auto-create a WordPress page with given name for use by this plugin.
	 *
	 * @param string page_name
	 *
	 * @return int the new page's ID.
	 **/
	function auto_add_page( $page_name ) {
		return wp_insert_post(
			array(
				'post_title' 			=> $page_name,
				'post_type' 			=> 'page',
				'post_status' 		=> 'publish',
				'comment_status' 	=> 'closed'
			)
		);
	}

}
// END class
