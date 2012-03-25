<?php

add_action('plugins_loaded', 'ccStats_set_globals');

function ccStats_set_globals() {
	global $ccStats_ga_profile_id, $ccStats_ga_token;
	$ccStats_ga_token = get_option('ccStats_ga_token');
	$ccStats_ga_profile_id = get_option('ccStats_ga_profile_id');
}

function ccStats_admin_init() {

	if(!constant_contact_create_object()) { return; }
	
	if (!defined('PLUGINDIR')) {
		define('PLUGINDIR','wp-content/plugins');
	}
	
	global $ccStats_page, $pagenow, $cc;
	$ccStats_page = null;
	
	include_once(CC_FILE_PATH.'admin/analytics-settings.php');
		
	register_setting('constant-analytics', 'constant_contact_analytics');
	
	if (isset($_GET['page']) && $_GET['page'] == 'constant-analytics.php') {
		$ccStats_page = (
			$pagenow == 'admin.php' ? 'settings' : (
				$pagenow == 'index.php' ? 'dashboard' : '' )
		);
	}
	
	if ($ccStats_page == 'dashboard') {
		header('X-UA-Compatible: IE=7');	// ask ie8 to behave like ie7 for the sake of vml
	}

	if ($ccStats_page == 'dashboard') {
		wp_enqueue_script('jquery');
		wp_enqueue_script('ccStatsdatecoolite', plugin_dir_url(__FILE__).'js/date-coolite.js', array('jquery'));
		wp_enqueue_script('ccStatsdate', plugin_dir_url(__FILE__).'js/date.js', array('jquery', 'ccStatsdatecoolite'));
		wp_enqueue_script('ccStatsdatePicker', plugin_dir_url(__FILE__).'js/jquery.datePicker.js', array('jquery', 'ccStatsdatecoolite', 'ccStatsdate'));
		wp_enqueue_script('ccStatsdatePickerMultiMonth', plugin_dir_url(__FILE__).'js/jquery.datePickerMultiMonth.js', array('jquery', 'ccStatsdatecoolite', 'ccStatsdate', 'ccStatsdatePicker'));
		wp_enqueue_script('ccStats', plugin_dir_url(__FILE__).'js/constant-analytics.js', array('ccStatsdatePickerMultiMonth'));
		wp_enqueue_script('google_jsapi', 'http://www.google.com/jsapi');
	}
	
	if (!empty($ccStats_page)) {
		wp_enqueue_style('ccStats', plugin_dir_url(__FILE__).'css/ccStats.css');
	}
}
add_action('admin_init', 'ccStats_admin_init');


function ccStats_admin_head() {
	global $ccStats_page, $ccStats_ga_token, $is_IE;
	if (!empty($ccStats_page)) {
		if(!empty($is_IE)) {
			echo '
				<style> v\:* { behavior: url(#default#VML); } </style>
				<xml:namespace ns="urn:schemas-microsoft-com:vml" prefix="v" >
			';
			echo '
				<!--[if IE]>
					<link rel="stylesheet" href="'.site_url('?ccStats_action=admin_css_ie').'" type="text/css" media="screen" charset="utf-8" />
				<![endif]-->
			';
		}
		if ($ccStats_page == 'dashboard') {
			echo '
				<script type="text/javascript">
					if (typeof google !== \'undefined\') {
						google.load("gdata", "1");
						google.load("visualization", "1", {"packages": ["areachart", "table", "piechart", "imagesparkline", "geomap", "columnchart"]});
					}
				</script>
			';
		}
	}

}
add_action('admin_head', 'ccStats_admin_head');


$ccStats_ga_token = get_option('ccStats_ga_token');
$ccStats_ga_profile_id = get_option('ccStats_ga_profile_id');

function ccStats_warn_on_plugin_page($plugin_file) {
	return;
	if (strpos($plugin_file, 'constant-analytics.php')) {
		global $cc, $ccStats_ga_token;
		$ga_setup = (isset($ccStats_ga_token) && !empty($ccStats_ga_token));
		$message = '';
		if (!$cc && !$ga_setup) {
			$message = '<strong>Note</strong>: Constant Analytics requires account authentication to work. <a href="'.admin_url('admin.php?page=constant-analytics').'">Go here to set everything up</a>, then start analyticalizing!';
		}
		else if (!$cc) {
			$message = '<strong>Note</strong>: You <em>could</em> be doing more with Constant Analytics <a href="'.admin_url('admin.php?page=constant-analytics').'">Log in or set up your Constant Contact account</a>!';
		}
		else if (!$ga_setup) {
			$message = '<strong>Note</strong>: Constant Analytics has to hook up to your Google Analytics account before it can do anything! <a href="'.admin_url('admin.php?page=constant-analytics').'constant-analytics.php">Start the authorization process here</a>!';
		}
		if (!empty($message)) {
			print('
				<tr class="plugin-update-tr">
					<td colspan="5" class="plugin-update">
						<div class="update-message">
						'.$message.'
						</div>
					</td>
				</tr>
			');
		}
	}
}
add_action('after_plugin_row', 'ccStats_warn_on_plugin_page');

// returns false only when we're not using our own MCAPI, 
// and the existing version is < 2.1.
function ccStats_MCAPI_is_compatible() {
	if (class_exists('MCAPI')) {
		$api = new MCAPI(null, null);
		return version_compare($api->version, '1.2', '>=');
	}
	return true;
}

function ccStats_troubleshoot_message($error = '') {
	$result = '';
	if (!empty($error)) {
		$result .= '<p>The error message was: <span style="color:red;">'.htmlspecialchars($error).'</span>.</p>';
	}
	$result .= '
		<p>If you\'re having trouble getting up and running, please leave a message on the <a href="http://wordpress.org/tags/constant-contact-api?forum_id=10">plugin support forum</a></p>';
	return $result;
}

function ccStats_check_config() {
	$curl_has_ssl = false;
	$php_has_ssl = false;
	$curl_exists = function_exists('curl_version');
	if ($curl_exists) {
		$curl_info = curl_version();
		if (isset($curl_info['protocols'])) {
			$curl_has_ssl = in_array('https', $curl_info['protocols']);
		}
		else {
			$curl_has_ssl = !empty($curl_info['ssl_version']);
		}
	}
	if (function_exists('stream_get_wrappers')) {
		$php_has_ssl = in_array('https', stream_get_wrappers());
	}
	return compact('curl_has_ssl', 'php_has_ssl', 'curl_exists');
}

function ccStats_warning_box($message, $errors, $extra) {
	echo '
		<div class="error ccStats-warning">
			<h3>'.$message.'</h3>
	';
	if (!empty($errors)) {
		echo '
			<p>The error message was: <span style="color:#900;">'.htmlspecialchars($errors).'</span>.</p>
		';
	}
	
	echo $extra;
	
	echo ccStats_troubleshoot_message();
	echo '</div>';
}

function ccStats_config_warnings() {
	$config_status = ccStats_check_config();
	$config_warning = '';

	if ($config_status['curl_exists'] && !$config_status['curl_has_ssl']) {
		$config_warning .= '<li>The version of cURL running on this server does not support SSL.</li>';
	}
	else if (!$config_status['curl_exists'] && !$config_status['php_has_ssl']) {
		$config_warning .= '<li>The version of PHP running on this server does not support SSL.</li>';
	}

	if (!empty($config_warning)) {
		$config_warning = '
			<p>We just asked your server about a few things and there\'s a chance you\'ll have problems using Constant Analytics.</p>
			<ul>
				'.$config_warning.'
			</ul>
			<p>Constant Analytics requires an SSL-enabled transport to work with Google Analytics. You may wish to contact your hosting service or server administrator to ensure that this is possible on your configuration.</p>
		';
	}
	return $config_warning;
}

function ccStats_show_ga_auth_error($message, $errors = '') {
	$config_warnings = ccStats_config_warnings();
	ccStats_warning_box($message, $errors, $config_warnings);
}

function ccStats_process_all_results_for_email($all_results) {
	foreach($all_results as $filter => $results) {
		if($filter == 'email') { continue; }
		foreach($results as $key => $result) {
			if(isset($result['dimensions']['source'])) {
				if(preg_match('/(\.?mail\.|gmail|email)/ism', $result['dimensions']['source'])) {
					$result['dimensions']['medium'] = 'email';
					$all_results['email'][] = $result;
					if($filter == '*') {
						$all_results[$filter][$key]['dimensions']['medium'] = 'email';
					} else {
						unset($all_results[$filter][$key]);
					}
				}
			}
		}
	}
	return $all_results;
}

function ccStats_request_handler() {
	if (!empty($_GET['ccStats_action']) && current_user_can('manage_options')) {
		switch ($_GET['ccStats_action']) {

			case 'admin_css_ie':
				header('Content-type: text/css');
				require('css/ccStats-ie.css');
				die();
			break;
			case 'capture_ga_token':
				$args = array();
				parse_str($_SERVER['QUERY_STRING'], $args);

				$token = NULL;
				if (isset($args['token'])) {
					$wp_http = ccStats_get_wp_http();
					$request_args = array(
						'method' => 'GET',
						'headers' => ccStats_get_authsub_headers($args['token']),
						'sslverify' => false
					);
					$response = $wp_http->request(
						'https://www.google.com/accounts/AuthSubSessionToken',
						$request_args
					);

					$error_messages = array();
					if (is_wp_error($response)) {
						// couldn't connect
						$error_messages = $response->get_error_messages();
					}
					else if (is_array($response)) {
						$matches = array();
						$found = preg_match('/Token=(.*)/', $response['body'], $matches);
						if ($found) {
							$token = $matches[1];
							$result = update_option('ccStats_ga_token', $token);
						}
						else {
							// connected, but no token in response. 
							$error_messages = array($repsonse['body']);
						}
					}
				}
				
				if (!$token) {
					if (count($error_messages)) {
						$capture_errors .= implode("\n", $error_messages);
					}
					else {
						$capture_errors = 'unknown error';
					}
					$q = http_build_query(array(
						'ccStats_ga_token_capture_errors' => $capture_errors
					));
				}
				else {
					delete_option('ccStats_ga_profile_id');
					$q = http_build_query(array(
						'updated' => true
					));
				}
				wp_redirect(admin_url('admin.php?page=constant-analytics&'.$q));
			break;
			case 'get_wp_posts':
				header('Content-type: text/javascript');
				
				$start = (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $_GET['start_date']) ? $_GET['start_date'] : '0000-00-00');
				$end = (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $_GET['end_date']) ? $_GET['end_date'] : '0000-00-00');
				
				$transient_title = 'gwppo'.sha1($ccStats_ga_profile_id.$start.$end.implode('_',$_GET));
				$results = get_transient($transient_title);
				if($results && (!isset($_GET['refresh']))) { 
					die(cf_json_encode(array(
						'success' => true,
						'data' => $results,
						'cached' => false
					))); 
				}
				
				add_filter('posts_where', create_function(
					'$where', 
					'return $where." AND post_date >= \''.$start.'\' AND post_date < \''.$end.'\'";'
				));
				$results = query_posts('post_status=publish&posts_per_page=999');
				
				set_transient($transient_title, $results, 60*60);
				
				die(cf_json_encode(array(
					'success' => true,
					'data' => $results,
					'cached' => false
				)));
			break;
			case 'get_cc_data':
				header('Content-type: text/javascript');
				$cc = constant_contact_create_object();
				
				if(!isset($_GET['data_type'])) { break; }
				
				switch ($_GET['data_type']) {
					case 'campaigns':
						$start = $end = '';
						if(isset($_GET['start_date'])) { 
							$start = explode('-', $_GET['start_date']);
							$start = mktime(0,0,0, (int)$start[1], (int)$start[2], (int)$start[0]);
						}
						if(isset($_GET['end_date'])) {
							$end = explode('-', $_GET['end_date']);
							$end = mktime(0,0,0, (int)$end[1], (int)$end[2], (int)$end[0]);
						}
						
						$results = array();
						$Sent = $cc->query_campaigns();
						foreach($Sent as $sent) {
							$time = $cc->convert_timestamp($sent['Date']);
							
							if($time > $start && $time < $end) {
								$results[] = array(
									'send_time' => date('Y-m-d', $time),
									'title' => ($sent['Name'] == 'null') ? '' : $sent['Name'],
									'archive_url' => admin_url('admin.php?page=constant-contact-campaigns&id='.$sent['id'])
								);
							}
						}
						
						if (!empty($results)) {
							die(cf_json_encode(array(
								'success' => true,
								'data' => $results,
								'cached' => false
							)));
						}
						else if (empty($cc->last_error)) {
							die(cf_json_encode(array(
								'success' => true,
								'data' => $results,
								'cached' => false
							)));
						}
						else {
							die(cf_json_encode(array(
								'success' => false,
								'error' => $cc->last_error
							)));
						}
					break;
				}
			break;
			case 'get_ga_data':
				global $ccStats_ga_token, $ccStats_ga_profile_id;
				
				$parameters = array(
					'start-date' => $_GET['start_date'],
					'end-date' => $_GET['end_date'],
					'sort' => 'ga:date',
					'ids' => 'ga:'.$ccStats_ga_profile_id
				);
				
				// split up top referrals by filtering on each medium in turn
				if ($_GET['data_type'] == 'top_referrals') {
					$requests = array(
						'referral' => null,
						'organic' => null,
						'email' => null,
						'cpc' => null,
						'*' => null
					);
					$parameters['dimensions'] = 'ga:medium,ga:source';
					$parameters['metrics'] = 'ga:visits,ga:timeOnSite,ga:pageviews';
					$parameters['sort'] = '-ga:visits';

					$all_results = array();

					foreach ($requests as $filter => $request) {
						$transient_title = 'ggad'.sha1($ccStats_ga_profile_id.$filter.implode('_', $parameters).implode('_',$_GET));
						$results = get_transient($transient_title);
						if($results && (!isset($_GET['refresh']))) { $all_results[$filter] = maybe_unserialize($results); continue; }
						
						$p = ($filter == '*' ? array('max-results' => 200) : array('filters' => 'ga:medium=='.$filter, 'max-results' => 200));
						$requests[$filter] = $request = ccStats_get_wp_http();
						$results = $request->request(
							'https://www.google.com/analytics/feeds/data?'.http_build_query(array_merge(
								$parameters,
								$p
							)),
							array(
								'headers' => ccStats_get_authsub_headers(),
								'timeout' => 30,
								'sslverify' => false
							)
						);
						set_transient($transient_title, maybe_serialize($results), 60*60*6);
						$all_results[$filter] = $results;
					}

					foreach ($all_results as $filter => $results) {
						if (is_wp_error($results)) {
							header('Content-type: text/javascript');
							die(cf_json_encode(array(
								'success' => false,
								'error' => implode('<br/>', $results->get_error_messages())
							)));
						}
						if (substr($results['response']['code'], 0, 1) == '2') {
							$all_results[$filter] = ccStats_reportObjectMapper($results['body']);
						}
						else {
							header('Content-type: text/javascript');
							die(cf_json_encode(array(
								'success' => false,
								'error' => $results['body']
							)));
						}
					}
					
					$all_results = ccStats_process_all_results_for_email($all_results);
					
					if(isset($_GET['email_only'])) {
						$all_results = $all_results['email'];	
					}
					
					header('Content-type: text/javascript');
					die(cf_json_encode(array(
						'success' => true,
						'data' => $all_results,
						'cached' => false
					)));

				}
				else {
					switch ($_GET['data_type']) {
						case 'visits':
							$parameters['dimensions'] = 'ga:date,ga:medium';
							$parameters['metrics'] = 'ga:visits,ga:bounces,ga:entrances,ga:pageviews,ga:newVisits,ga:timeOnSite';
							//$parameters['filters'] = 'ga:medium==referral,ga:medium==organic,ga:medium==email,ga:medium==cpc';
							//$parameters['sort'] = '-ga:visits';
						break;
						case 'geo':
							$parameters['dimensions'] = 'ga:country';
							$parameters['metrics'] = 'ga:visits';
							$parameters['sort'] = '-ga:visits';
						break;
						case 'top_referrals':
							$parameters['dimensions'] = 'ga:medium,ga:source';
							$parameters['metrics'] = 'ga:visits,ga:timeOnSite,ga:pageviews';
							$parameters['sort'] = '-ga:visits';
							$parameters['filters'] = 'ga:medium==referral,ga:medium==organic,ga:medium==email,ga:medium==cpc';
						break;
						case 'referral_media':
							$parameters['dimensions'] = 'ga:medium';
							$parameters['metrics'] = 'ga:visits';
							$parameters['sort'] = '-ga:visits';
						break;
						case 'top_content':
							$parameters['dimensions'] = 'ga:pagePath';
							$parameters['metrics'] = 'ga:pageviews,ga:uniquePageviews,ga:timeOnPage,ga:exits';
							$parameters['sort'] = '-ga:pageviews';
						break;
						case 'keywords':
							$parameters['dimensions'] = 'ga:keyword';
							$parameters['metrics'] = 'ga:pageviews,ga:uniquePageviews,ga:timeOnPage,ga:exits';
							$parameters['sort'] = '-ga:pageviews';
							$parameters['filters'] = 'ga:source=='.$_GET['source_name'];
						break;
						case 'referral_paths':
							$parameters['dimensions'] = 'ga:source,ga:referralPath';
							$parameters['metrics'] = 'ga:pageviews,ga:uniquePageviews,ga:timeOnPage,ga:exits';
							$parameters['sort'] = '-ga:pageviews';
							$parameters['filters'] = 'ga:source=='.$_GET['source_name'];
						break;
						case 'email_referrals':
							$parameters['dimensions'] = 'ga:campaign';
							$parameters['metrics'] = 'ga:pageviews,ga:uniquePageviews,ga:timeOnPage,ga:exits';
							$parameters['sort'] = '-ga:pageviews';
							$parameters['filters'] = 'ga:medium==email';
						break;
						default:
						break;
					}
					
					$transient_title = 'ggad'.sha1($ccStats_ga_profile_id.implode('_',$parameters).implode('_',$_GET));
					$results = get_transient($transient_title);
					if($results && (!isset($_GET['refresh']))) {
						$result = maybe_unserialize($results); 
					} else {
						$wp_http = ccStats_get_wp_http();
						$url = 'https://www.google.com/analytics/feeds/data?'.http_build_query($parameters);
						$request_args = array(
							'headers' => ccStats_get_authsub_headers(),
							'timeout' => 10,
							'sslverify' => false
						);
						$result = $wp_http->request(
							$url,
							$request_args
						);
					}					
				}


				if (is_wp_error($result)) {
					header('Content-type: text/javascript');
					die(cf_json_encode(array(
						'success' => false,
						'error' => implode('<br/>', $result->get_error_messages())
					)));
				}

				if (substr($result['response']['code'], 0, 1) == '2') {
					
					set_transient($transient_title, maybe_serialize($result), 60*60*6);
					
					$result = ccStats_reportObjectMapper($result['body']);
					
					if(empty($result)) {
						$_GET['data_type'] = 'top_referrals';
						$_GET['email_only'] = true;
						ccStats_request_handler();
					}
#					$all_results = ccStats_process_all_results_for_email($all_results);
						
					header('Content-type: text/javascript');
					die(cf_json_encode(array(
						'success' => true,
						'data' => $result,
						'cached' => false
					)));
				}
				else {
					header('Content-type: text/javascript');
					die(cf_json_encode(array(
						'success' => false,
						'error' => $result['body']
					)));
				}
			break;
		}
	}
	if (!empty($_POST['ccStats_action']) && current_user_can('manage_options')) {
		ccStats_check_nonce($_POST['ccStats_nonce'], $_POST['ccStats_action']);
		switch ($_POST['ccStats_action']) {
			case 'revoke_ga_token':
				global $ccStats_ga_token;
				$wp_http = ccStats_get_wp_http();
				$request_args = array(
					'headers' => ccStats_get_authsub_headers(),
					'sslverify' => false
				);
				$response = $wp_http->request(
					'https://www.google.com/accounts/AuthSubRevokeToken',
					$request_args
				);
				if ($response['response']['code'] == 200) {
					delete_option('ccStats_ga_token');
					delete_option('ccStats_ga_profile_id');
					wp_redirect(admin_url('admin.php?page=constant-analytics&update=true'));
				}
				else if ($response['response']['code'] == 403) {
					wp_redirect(add_query_arg('ccStats_revoke_token_chicken_and_egg', $response['response']['code'].': '.$response['response']['message'], admin_url('admin.php?page=constant-analytics')));
				}
				else {
					if (is_wp_error($response)) {
						$errors = $response->get_error_messages();
					}
					else {
						$errors = array($response['response']['code'].': '.$response['response']['message']);
					}
					wp_redirect(admin_url('admin.php?page=constant-analytics&'.http_build_query(array(
						'ccStats_error' => implode("\n", $errors)
					))));
				}
			break;
			case 'forget_ga_token':
				delete_option('ccStats_ga_token');
				delete_option('ccStats_ga_profile_id');
				wp_redirect(admin_url('admin.php?page=constant-analytics&update=true'));
			break;
			case 'set_ga_profile_id':
				$result = update_option('ccStats_ga_profile_id', $_POST['profile_id']);
				wp_redirect(admin_url('admin.php?page=constant-analytics&updated=true'));
			break;	
		}
		die();
	}
}
add_action('init', 'ccStats_request_handler');


function ccStats_check_nonce($nonce, $action_name) {
	if (wp_verify_nonce($nonce, $action_name) === false) {
		wp_die('The page with the command you submitted has expired. Please try again.');
	}
}
function ccStats_create_nonce($action_name) {
	return wp_create_nonce($action_name);
}


/**
 * Work around a bug in WP 2.7's implementation of WP_Http running on cURL.
 */
function ccStats_get_authsub_headers($token = null) {
	global $wp_version, $ccStats_ga_token;
	static $use_assoc = null;
	if (is_null($use_assoc)) {
		if (version_compare($wp_version, '2.8', '<')) {
			$use_assoc = false;
		}
		else {
			$use_assoc = true;
		}
	}
	$token = (is_null($token) ? $ccStats_ga_token : $token);
	if (!$use_assoc) {
		return array('Authorization: AuthSub token="'.$token.'"');
	}
	return array('Authorization' => 'AuthSub token="'.$token.'"');
}

function ccStats_admin_menu() {
	
	if(!constant_contact_create_object()) { return; }
	
	if (current_user_can('manage_options')) {
		add_dashboard_page(
			__('Dashboard', 'constantanalytics'),
			__('Constant Analytics', 'constantanalytics'),
			'manage_options',
			basename(__FILE__),
			'ccStats_dashboard'
		);
	}
}
add_action('admin_menu', 'ccStats_admin_menu');

function ccStats_plugin_action_links($links, $file) {
	$plugin_file = basename(__FILE__);
	if (basename($file) == $plugin_file) {
		$settings_link = '<a href="admin.php?page=constant-analytics">'.__('Settings', 'constant-contact-api').'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'ccStats_plugin_action_links', 10, 2);

function ccStats_dashboard() {
	global $cc;
	
	include_once(CC_FILE_PATH.'admin/analytics-dashboard.php');
}


/**
 * Adapted from: 
 * 
 * GAPI - Google Analytics PHP Interface
 * http://code.google.com/p/gapi-google-analytics-php-interface/
 * @copyright Stig Manning 2009
 * @author Stig Manning <stig@sdm.co.nz>
 * @version 1.3
 */
function ccStats_reportObjectMapper($xml_string) {
	$xml = simplexml_load_string($xml_string);


	$results = null;
	$results = array();
	
	$report_root_parameters = array();
	$report_aggregate_metrics = array();
	
	//Load root parameters
	
	$report_root_parameters['updated'] = strval($xml->updated);
	$report_root_parameters['generator'] = strval($xml->generator);
	$report_root_parameters['generatorVersion'] = strval($xml->generator->attributes());
	
	$open_search_results = $xml->children('http://a9.com/-/spec/opensearchrss/1.0/');
	
	foreach($open_search_results as $key => $open_search_result) {
		$report_root_parameters[$key] = intval($open_search_result);
	}
	
	$google_results = $xml->children('http://schemas.google.com/analytics/2009');

	foreach($google_results->dataSource->property as $property_attributes) {
		$attr = $property_attributes->attributes();
		$report_root_parameters[str_replace('ga:','',$attr->name)] = strval($attr->value);
	}
	
	$report_root_parameters['startDate'] = strval($google_results->startDate);
	$report_root_parameters['endDate'] = strval($google_results->endDate);
	
	//Load result aggregate metrics
	
	foreach($google_results->aggregates->metric as $aggregate_metric) {
		$attr = $aggregate_metric->attributes();
		$metric_value = strval($attr->value);
		$name = $attr->name;
		//Check for float, or value with scientific notation
		if(preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/',$metric_value)) {
			$report_aggregate_metrics[str_replace('ga:','',$name)] = floatval($metric_value);
		}
		else {
			$report_aggregate_metrics[str_replace('ga:','',$name)] = intval($metric_value);
		}
	}
	
	//Load result entries
	
	foreach($xml->entry as $entry) {
		$metrics = array();
		$children = $entry->children('http://schemas.google.com/analytics/2009');
		foreach($children->metric as $metric) {
			$attr = $metric->attributes(); 
			$metric_value = strval($attr->value);
			$name = $attr->name;
			
			//Check for float, or value with scientific notation
			if(preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/',$metric_value)) {
				$metrics[str_replace('ga:','',$name)] = floatval($metric_value);
			}
			else {
				$metrics[str_replace('ga:','',$name)] = intval($metric_value);
			}
		}
		
		$dimensions = array();
		$children = $entry->children('http://schemas.google.com/analytics/2009');
		foreach($children->dimension as $dimension) {
			$attr = $dimension->attributes();
			$dimensions[str_replace('ga:','',$attr->name)] = strval($attr->value);
		}
		
		$results[] = array('metrics' => $metrics, 'dimensions' => $dimensions);
	}
		
	return $results;
}

if (!function_exists('get_snoopy')) {
	function get_snoopy() {
		include_once(ABSPATH.'/wp-includes/class-snoopy.php');
		return new Snoopy;
	}
}

function ccStats_get_wp_http() {
	if (!class_exists('WP_Http')) {
		include_once(ABSPATH.WPINC.'/class-http.php');
	}
	return new WP_Http();
}

/**
 * JSON ENCODE for PHP < 5.2.0
 * Checks if json_encode is not available and defines json_encode
 * to use php_json_encode in its stead
 * Works on iteratable objects as well - stdClass is iteratable, so all WP objects are gonna be iteratable
 */ 
if(!function_exists('cf_json_encode')) {
	function cf_json_encode($data) {
		if(function_exists('json_encode')) { return json_encode($data); }
		else { return cfjson_encode($data); }
	}
	
	function cfjson_encode_string($str) {
		if(is_bool($str)) { 
			return $str ? 'true' : 'false'; 
		}
	
		return str_replace(
			array(
				'"'
				, '/'
				, "\n"
				, "\r"
			)
			, array(
				'\"'
				, '\/'
				, '\n'
				, '\r'
			)
			, $str
		);
	}

	function cfjson_encode($arr) {
		$json_str = '';
		if (is_array($arr)) {
			$pure_array = true;
			$array_length = count($arr);
			for ( $i = 0; $i < $array_length ; $i++) {
				if (!isset($arr[$i])) {
					$pure_array = false;
					break;
				}
			}
			if ($pure_array) {
				$json_str = '[';
				$temp = array();
				for ($i=0; $i < $array_length; $i++) {
					$temp[] = sprintf("%s", cfjson_encode($arr[$i]));
				}
				$json_str .= implode(',', $temp);
				$json_str .="]";
			}
			else {
				$json_str = '{';
				$temp = array();
				foreach ($arr as $key => $value) {
					$temp[] = sprintf("\"%s\":%s", $key, cfjson_encode($value));
				}
				$json_str .= implode(',', $temp);
				$json_str .= '}';
			}
		}
		else if (is_object($arr)) {
			$json_str = '{';
			$temp = array();
			foreach ($arr as $k => $v) {
				$temp[] = '"'.$k.'":'.cfjson_encode($v);
			}
			$json_str .= implode(',', $temp);
			$json_str .= '}';
		}
		else if (is_string($arr)) {
			$json_str = '"'. cfjson_encode_string($arr) . '"';
		}
		else if (is_numeric($arr)) {
			$json_str = $arr;
		}
		else if (is_bool($arr)) {
			$json_str = $arr ? 'true' : 'false';
		}
		else {
			$json_str = '"'. cfjson_encode_string($arr) . '"';
		}
		return $json_str;
	}
}

?>