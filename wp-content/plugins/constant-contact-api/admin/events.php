<?php

add_action('wp_dashboard_setup', 'constant_contact_dashboard_setup');

function constant_contact_dashboard_setup() {
	wp_add_dashboard_widget( 'constant_contact_events_dashboard', __( 'Constant Contact Events','constant-contact-api'), 'constant_contact_events_dashboard' );
}

function constant_contact_events_dashboard_make_table($title = 'Events', $events = array()) {
	global $cc;
	
	if(!constant_contact_create_object()) { return false; }

	?>
	<p style="color: #777;font-family: Georgia, 'Times New Roman', 'Bitstream Charter', Times, serif;font-size: 13px;font-style: italic;padding: 0;margin: 15px 0 0 0;"><?php echo $title; ?></p>
	<table class="widefat fixed" cellspacing="0" style="border:0px;">
			<thead>
				<tr>
					<td style="text-align:left; padding:8px 0!important; font-weight:bold;" id="title" class="manage-column column-name" style="">Event Name</td>
					<td style="text-align:center; padding:8px 0!important; font-weight:bold;" id="registered" class="manage-column column-name" style=""># Registered</td>
					<td style="text-align:center; padding:8px 0!important; font-weight:bold;" id="cancelled" class="manage-column column-name" style=""># Cancelled</td>
					<td style="text-align:left; padding:8px 0!important; font-weight:bold;" id="details" class="manage-column column-name" style="">Last Registrant</td>
				</tr>
			</thead>
			<tbody>
			<?php
			if(empty($events)) {?>
				<tr><td colspan="6">
				<h3>No events found&hellip;</h3>
				</td></tr></table>
			<?php
				return;
			}
			foreach($events as $id => $v) {					
				$v = $cc->get_event($id); // The cancelled registrants count won't work otherwise...
			?>
			<tr class='author-self status-inherit' valign="top">
				<td class="column-title" style="padding:8px 0;">
					<a href="<?php echo add_query_arg('id', $v['id'], remove_query_arg('refresh', admin_url('admin.php?page=constant-contact-events'))); ?>" style="display:inline;white-space: nowrap; width: 100%; overflow: hidden; text-overflow: ellipsis; font-weight:bold;" title="<?php echo esc_html($v['Name']).' - '.esc_html($v['Title']); ?>"><?php echo esc_html($v['Name']); ?></a>
				</td>
				<td class="column-date" style="padding:8px 0; text-align:center;">
					<a href="<?php echo add_query_arg('id', $v['id'], remove_query_arg('refresh', admin_url('admin.php?page=constant-contact-events#registrants'))); ?>" style="display:block; width:100%; line-height:1.4;"><?php echo_if_not_empty((int)$v['Registered'],0); ?></a>
				</td>
				<td class="column-date" style="padding:8px 0; text-align:center;">
					<a href="<?php echo add_query_arg('id', $v['id'], remove_query_arg('refresh', admin_url('admin.php?page=constant-contact-events#cancelled'))); ?>" style="display:block; width:100%; line-height:1.4;"><?php echo_if_not_empty((int)$v['CancelledCount'],0); ?></a>
				</td>
				<td class="column-date" style="padding:8px 0; text-align:left;">
					<?php echo constant_contact_latest_registrant($id); ?>
				</td>
			</tr>
<?php } ?>
		</table>
	<?php
}

function constant_contact_events_dashboard() {
	global $cc;
	
	if(!constant_contact_create_object()) { return false; }
	
	$_events = $cc->get_events();

	if(!empty($_events) && is_array($_events)) {
		$draft = $active = array();
		foreach($_events as $k => $v) {
			if($v['Status'] == 'ACTIVE') {
				$active[$v['id']] = $v;
			} elseif($v['Status'] == 'DRAFT') {
				$draft[$v['id']] = $v;
			}
		}
		if(!empty($active)) { constant_contact_events_dashboard_make_table(__('Active Events','constant-contact-api'), $active); }
		if(!empty($draft)) { constant_contact_events_dashboard_make_table(__('Draft Events','constant-contact-api'), $draft); }
	?>		
		<p class="textright">
	        <a class="button" href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>">View All Events</a>
	    </p>
<?php
	} else {
?>
	<p style='font-size:12px;'><?php _e(sprintf("You don't have any events. Did you know that Constant Contact offers %sEvent Marketing%s?", '<a href="http://conta.cc/hB5lnC" title="Learn more about Constant Contact Event Marketing">', '</a>'), 'constant_contact_api'); ?></p>
<?php
	}
	return true;
}

		
/**
 * event log submenu page callback function
 * 
 * @global <type> $cc
 * @return <type> 
 */
function constant_contact_events()
{
	global $cc;
	
	// Create the CC api object for use in this page. 
	if(!constant_contact_create_object()) { return false; }

	$events = array();
	
	// Single registrant view
	if(isset($_GET['id']) && isset($_GET['registrant'])) {
		$id = htmlentities($_GET['id']);
		$regid = htmlentities($_GET['registrant']);
		$v = $cc->get_event_registrant($id, $regid);
		$event = $cc->get_event($id);
		extract($v);
		unset($BusinessInformation['Label']);
		unset($PersonalInformation['Label']);

		if(!empty($v) && current_user_can('list_users')) { 
		?>
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a>  <a href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>">Events</a> &gt;<?php echo_if_not_empty($event['Title'],'',' <a href="'.admin_url('admin.php?page=constant-contact-events&id='.$event['id']).'">&ldquo;'.$event['Title'].'&rdquo;</a> &gt;') ?> Registrant: <?php echo "{$LastName}, {$FirstName}"; ?></h2>
			<?php 
			constant_contact_admin_refresh(); 
			?>
			<h3>Registration Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					#					constant_contact_plugin_page_list();
					$PaymentType = !empty($PaymentType) ? $PaymentType : '';
					$CurrencyType = !empty($CurrencyType) ? $CurrencyType : '';
					$RegistrationInformation = array('Registration Status'=>$RegistrationStatus, 'Registration Date' => date('jS F Y \- H:i', (int)$cc->convert_timestamp($RegistrationDate)),'Guest Count'=>get_if_not_empty($GuestCount,1),'Payment Status'=>$PaymentStatus,'Order Amount'=>$OrderAmount,'Currency Type'=>$CurrencyType,'Payment Type' => $PaymentType);
					$reg = '';
					foreach($RegistrationInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',$key);
						$reg .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					
					if(!empty($Costs)) {
						$reg .= '<tr><th scope="row" id="costs" class="manage-column column-name" style=""><span>Summary of Costs</span></th><td><ul class="ul-disc">';
					
						foreach($Costs as $v) {
							extract($v);
							$reg .= "<li><strong>{$FeeType}</strong>: {$Count} Guest(s) x {$Rate} = {$Total}</li>";
						}
						$reg .= '</ul></td></tr>';
					}
					echo $reg;
					?>
				</tbody>
			</table>
			
			<h3>Personal Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$per = '';
					
					$OtherPersonalInformation['LastName'] = $LastName;
					$OtherPersonalInformation['FirstName'] = $FirstName;
					$OtherPersonalInformation['EmailAddress'] = get_if_not_empty($EmailAddress,'', "<a href='mailto:{$EmailAddress}'>{$EmailAddress}</a>");
					foreach($OtherPersonalInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',str_replace('Address', 'Address ',$key));
						$per .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					foreach($PersonalInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',str_replace('Address', 'Address ',$key));
						$per .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					echo $per;
					?>
				</tbody>
			</table>
			
			
			<h3>Business Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$bus = '';
					foreach($BusinessInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',str_replace('Address', 'Address ',$key));
						$bus .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					echo $bus;
					?>
				</tbody>
			</table>
			
			<?php if(!empty($CustomInformation1)) {?>
			<h3>Custom Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$cus = ''; $cusnum = 0;
					foreach($CustomInformation1['CustomField'] as $key => $v) {
						$cusnum++; $vA = '';
						if($key == 'Question') {
							$cus .= '<tr><th scope="row" id="'.sanitize_title($key.$cusnum).'" class="manage-column column-name" style=""><span>'.$v.'</span></th>';
						} elseif($key == 'Answers') {
							if(is_array($v)) {
								foreach($v as $li) {
									if(preg_match('/^20[0-9]{2}\-/', $li)) {
										$li = date('jS F Y \- H:i', (int)$cc->convert_timestamp($li));
									}
									$vA .= '<li>'.$li.'</li>';
								}
								$v = "<ul class='ul-disc'>{$vA}</ul>";
							}
							$cus .= '<td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
						}
					}
					foreach($CustomInformation2['CustomField'] as $key => $v) {
						$cusnum++;
						if($key == 'Question') {
							$cus .= '<tr><th scope="row" id="'.sanitize_title($key.$cusnum).'" class="manage-column column-name" style=""><span>'.$v.'</span></th>';
						} elseif($key == 'Answers') {
							if(is_array($v)) {
								foreach($v as $li) {
									if(preg_match('/^20[0-9]{2}\-/', $li)) {
										$li = date('jS F Y \- H:i', (int)$cc->convert_timestamp($li));
									}
									$vA .= '<li>'.$li.'</li>';
								}
								$v = "<ul class='ul-disc'>{$vA}</ul>";
							}
							$cus .= '<td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
						}					}
					echo $cus;
					?>
				</tbody>
			</table>
	<?php	} ?>
		<p class="submit"><a href="<?php echo remove_query_arg(array('registrant', 'refresh')); ?>" class="button-primary">Return to Event</a> <a href="<?php echo add_query_arg('refresh', 'registrant'); ?>" class="button-secondary alignright" title="Registrant data is stored for 1 hour. Refresh data now.">Refresh Registrant</a></p>
		</div>
	<?php 
		}
	} elseif(isset($_GET['id'])) {
		$id = htmlentities($_GET['id']);
		$v = $cc->get_event($id);
		if((int)$cc->convert_timestamp($v['EndDate']) > time()) { $completed = false; }
		else { $completed = true; }
	?>
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>">Events</a> &gt; Event<?php echo_if_not_empty($v['Title'],'',': &ldquo;'.$v['Title'].'&rdquo;'); ?></h2>			
			<?php constant_contact_admin_refresh();  ?>
			<h3>Event Stats:</h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name">Registered</th>
					<th scope="col" class="column-title"><?php if($completed) { echo 'Attended'; } else { echo 'Attending'; }?></th>
					<th scope="col" class="column-title">Cancelled</th>
				</thead>
				<tbody>
					<tr valign="top">
				<?php
				$html = '';
					$cols = array('Registered', 'AttendedCount', 'CancelledCount');
					foreach($cols as $col) {
						$html .= '<td>'.htmlentities($v["{$col}"]).'</td>';
					}
					echo $html;
				?>	</tr>
				</tbody>
			</table>
		<h3>Event Details:</h3>
		<table class="form-table widefat" cellspacing="0">
			<?php
			if(!$v) {
				echo '<tbody><tr><td><p>Event Not Found</p></td></tr></tbody></table><p class="submit"><a href="'.admin_url('admin.php?page=constant-contact-events').'" class="button-primary">Return to Events</a></p></div>';
				return;
			}
			$html = '';
			
			$dateformat = 'jS F Y \- H:i:s';
			
			$v['StartDate'] = (int)$cc->convert_timestamp($v['StartDate']);
			$v['EndDate'] = (int)$cc->convert_timestamp($v['EndDate']);
			
			?>
			<tbody>
				<tr><th scope="row" id="name" class="manage-column column-name" style="">Name</th><td><?php echo $v['Name']; ?></td></tr>
				<tr class="alt"><th scope="row" id="description" class="manage-column column-name" style="">Description</th><td><?php echo_if_not_empty($v['Description']); ?></td></tr>
				<tr><th scope="row" id="title" class="manage-column column-name" style="">Title</th><td><?php echo $v['Title']; ?></td></tr>
				<tr class="alt"><th scope="row" id="created" class="manage-column column-name" style="">Created</th><td><?php echo date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['CreatedDate'])); ?></td></tr>
				<tr><th scope="row" id="status" class="manage-column column-name" style="">Status</th><td><?php echo $v['Status']; ?></td></tr>
				<tr class="alt"><th scope="row" id="type" class="manage-column column-name" style="">Type</th><td><?php echo $v['EventType']; ?></td></tr>
				<tr><th scope="row" id="start" class="manage-column column-name" style="">Start</th><td><?php echo (!empty($v['StartDate']) ? date('jS F Y \- H:i', $v['StartDate']) : 'None'); ?></td></tr>
				<tr><th scope="row" id="end" class="manage-column column-name" style="">End</th><td><?php echo (!empty($v['EndDate']) ? date('jS F Y \- H:i', $v['EndDate']) : 'None'); ?></td></tr>
				<tr><th scope="row" id="registrationurl" class="manage-column column-name" style="">Registration URL</th><td><?php echo_if_not_empty($v['RegistrationURL'], '', '<a href="'.$v['RegistrationURL'].'">'.$v['RegistrationURL'].'</a>'); ?></td></tr>
				<tr class="alt"><th scope="row" id="location" class="manage-column column-name" style="">Location</th><td><?php echo constant_contact_create_location($v['EventLocation']); ?></td></tr>
<!--
				<tr><th scope="row" id="registered" class="manage-column column-name" style="">Registered</th><td><?php echo_if_not_empty($v['Registered'], 0); ?></td></tr>
				<tr class="alt"><th scope="row" id="attending" class="manage-column column-name" style=""><?php if($completed) { echo 'Attended'; } else { echo 'Attending'; }?></th><td><?php echo_if_not_empty($v['Attending'], 0); ?></td></tr>
				<tr><th scope="row" id="cancelled" class="manage-column column-name" style="">Cancelled</th><td><?php echo_if_not_empty($v['Cancelled'],0); ?></td></tr>
-->
		<?php
			$types = '';
			foreach($v['RegistrationTypes']['RegistrationType'] as $k => $type) {
				if($k == 'EventFees') { continue; }
				$k = preg_replace('/(?!^)[[:upper:]]/',' \0',$k);

				if(is_array($type)) {
					$list = '';
					foreach($type as $key => $t) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',$key);
						$list .= '<li><strong>'.$key.'</strong>'.$t.'</li>';
					}
					$type = '<ul>'.$list.'</ul>';
				} 

				$types .= '<li><strong>'.$k.'</strong>: '.$type.'</li>';
			}
			$types = '<ul>'.$types.'</ul>';
			
			if(!empty($v['RegistrationTypes']['RegistrationType']['EventFees'])) {
			$fees = '';
			foreach($v['RegistrationTypes']['RegistrationType']['EventFees']['EventFee'] as $k => $fee) {
				$k = preg_replace('/(?!^)[[:upper:]]/',' \0',$k);

				if(is_array($fee)) {
					$list = '';
					foreach($fee as $key => $t) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',$key);
						$list .= '<li><strong>'.$key.'</strong>: '.$t.'</li>';
					}
					$fee = '<ul>'.$list.'</ul>';
				} 

				$fees .= '<li>'.$fee.'</li>';
			}
				$fees = '<ol class="ol-decimal">'.$fees.'</ol>';
			} else {
				$fees = 'Free';
			}
		?>
			<tr><th scope="row" id="cancelled" class="manage-column column-name" style="">Registration Types</th><td><?php echo $types; ?></td></tr>
			<tr><th scope="row" id="cancelled" class="manage-column column-name" style="">Event Fees</th><td><?php echo $fees; ?></td></tr>
			</tbody>
		</table>
		<p class="submit"><a href="<?php echo remove_query_arg(array('id','refresh')); ?>" class="button-primary">Return to Events</a>
		
		<?php
		
		$_registrants = $cc->get_event_registrants($id);
		$_cancelled = array();
#		print_r($_registrants);

		if(!empty($_registrants) && current_user_can('list_users')) { 
		?>
		<h2>Registrants</h2>
		<h3>Registered</h3>
		<table class="widefat form-table" cellspacing="0" id="registrants">
			<thead>
				<tr>
					<th scope="col" id="registrant_lastname" class="manage-column column-name" style="">Last Name</th>
					<th scope="col" id="registrant_firstname" class="manage-column column-name" style="">First Name</th>
					<th scope="col" id="registrant_email" class="manage-column column-name" style="">Email</th>
					<th scope="col" id="registrant_date" class="manage-column column-name" style="">Registration Date</th>
					<th scope="col" id="registrant_guestcount" class="manage-column column-name" style="">Guest Count</th>
					<th scope="col" id="registrant_paymentstatus" class="manage-column column-name" style="">Payment Status</th>
					<th scope="col" id="registrant_details" class="manage-column column-name" style="">Details</th>
			</thead>
			<tbody>
				<?php 
				$alt = '';
				foreach($_registrants as $v) { 
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				if($v['RegistrationStatus'] == 'CANCELLED') {
					$_cancelled[] = $v;
					continue;
				}
				?>
				<tr <?php echo $alt; ?>>
					<td><?php echo $v['LastName']; ?></td>
					<td><?php echo $v['FirstName']; ?></td>
					<td><?php echo_if_not_empty($v['EmailAddress'],'', "<a href='mailto:{$v['EmailAddress']}'>{$v['EmailAddress']}</a>"); ?></td>
<!-- 					<td><?php echo $v['RegistrationStatus']; ?></td> -->
					<td><?php echo_if_not_empty($v['RegistrationDate'], 'None', date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['RegistrationDate']))); ?></td>
					<td><?php echo_if_not_empty($v['GuestCount'],1); ?></td>
					<td><?php echo $v['PaymentStatus']; ?></td>
					<td><a href="<?php echo add_query_arg('registrant', $v['id'], remove_query_arg('refresh')); ?>">View Details</a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<p class="submit"><a href="<?php echo add_query_arg('refresh', 'registrants').'#registrants'; ?>" class="button-secondary alignright" title="Event registrants data is stored for 1 hour. Refresh data now.">Refresh Registrants</a></p>
		<?php } 
		
		if(!empty($_cancelled) && current_user_can('list_users')) { 
		?>
		<h3>Cancelled</h3>
		<table class="widefat form-table" cellspacing="0" id="cancelled">
			<thead>
				<tr>
					<th scope="col" id="registrant_lastname" class="manage-column column-name" style="">Last Name</th>
					<th scope="col" id="registrant_firstname" class="manage-column column-name" style="">First Name</th>
					<th scope="col" id="registrant_email" class="manage-column column-name" style="">Email</th>
					<th scope="col" id="registrant_date" class="manage-column column-name" style="">Registration Date</th>
					<th scope="col" id="registrant_guestcount" class="manage-column column-name" style="">Guest Count</th>
					<th scope="col" id="registrant_paymentstatus" class="manage-column column-name" style="">Payment Status</th>
					<th scope="col" id="registrant_details" class="manage-column column-name" style="">Details</th>
			</thead>
			<tbody>
				<?php 
				$alt = '';
				foreach($_cancelled as $v) { 
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				?>
				<tr <?php echo $alt; ?>>
					<td><?php echo $v['LastName']; ?></td>
					<td><?php echo $v['FirstName']; ?></td>
					<td><?php echo_if_not_empty($v['EmailAddress'],'', "<a href='mailto:{$v['EmailAddress']}'>{$v['EmailAddress']}</a>"); ?></td>
					<td><?php echo_if_not_empty($v['RegistrationDate'], 'None', date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['RegistrationDate']))); ?></td>
					<td><?php echo_if_not_empty($v['GuestCount'],1); ?></td>
					<td><?php echo $v['PaymentStatus']; ?></td>
					<td><a href="<?php echo add_query_arg('registrant', $v['id'], remove_query_arg('refresh')); ?>">View Details</a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<p class="submit"><a href="<?php echo add_query_arg('refresh', 'registrants').'#registrants'; ?>" class="button-secondary alignright" title="Event registrants data is stored for 1 hour. Refresh data now.">Refresh Cancelled</a></p>
		<?php } ?>
	</div>
<?php	
	} else {
		
		$_events = $cc->get_events();
		
		if(!empty($_events)):
		foreach($_events as $k => $v):
			$events[$v['id']] = $v;
		endforeach;
		endif;
		// display all events
		?>

		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Events</h2>
			<?php constant_contact_admin_refresh(); ?>
			
			<?php if(empty($_events)) {?>	
				<style type="text/css">
				#free_trial {
				background: url(<?php echo CC_FILE_URL; ?>admin/images/btn_free_trial_green.png) no-repeat 0px 0px;
				margin: 0px 5px 0px 0px;
				width: 246px;
				}
				a#free_trial,
				a#see_how {
					display:block;
					text-indent:-9999px;
					overflow:hidden;
					float:left;
					height: 80px;
				}
				
				a#free_trial:hover,
				a#see_how:hover {
					background-position: 0px -102px;
				}
				#see_how {
					background: url(<?php echo CC_FILE_URL; ?>admin/images/btn_see_how_blue.png) no-repeat 0px 0px;
					margin: 0px 10px 0px 0px;
					width: 216px;
				}
				</style>
				
				<div class="widefat">
				<div class="wrap"  style="padding:10px;"><h2 class="clear">Did you know that Constant Contact offers <a href="http://conta.cc/hB5lnC" title="Learn more about Constant Contact Event Marketing">Event Marketing</a>?</h2>
				<a id="see_how" href="http://conta.cc/eIt0gy" target="winHTML">See How it Works!</a>
				<a id="free_trial" href="http://conta.cc/guwuYh">Start Your Free Trial</a>
				<ul class="ul-disc clear">
					<li>Affordable, priced for small business, discount for nonprofits. <a href="http://conta.cc/guwuYh">Start for FREE!</a></li>
					<li>Easy-to-use tools and templates for online event registration and promotion</li>
					<li>Professional &#8212; you, and your events, look professional</li>
					<li>Secure credit card processing &#8212; collect event fees securely with PayPal processing</li>
					<li>Facebook, Twitter links make it easy to promote your events online</li>
					<li>Track and see results with detailed reports on invitations, payments, RSVP's, <a href="http://conta.cc/f62LG7">and more</a></li>
				</ul></div>
				</div>
			<?php 
			
			constant_contact_events_list_make_table($_events, __('Events','constant-contact-api'));
			
			} else { 

				$Active = $Complete = $Draft = $Cancelled = array();
				foreach($events as $id => $v) {
					$v['Status'] = ucwords(strtolower(esc_html($v['Status'])));
					${$v['Status']}[] = $v;
				}

				if(!empty($Active) || !empty($Draft) || !empty($Cancelled) || !empty($Complete)) {
					echo '<ul class="subsubsub">';
						if(!isset($_GET['event_status']) || $_GET['event_status'] == 'all') { $class = ' class="current"'; } else { $class = '';}
						echo '<li><a href="'.remove_query_arg(array('refresh','event_status')).'"'.$class.'>All <span class="count">('.count($events).')</span></a></li>';
						if(!empty($Active)) {
							if(isset($_GET['event_status']) && $_GET['event_status'] == 'active') { $class = ' class="current"'; } else { $class = '';}
							echo '<li>| <a href="'.add_query_arg('event_status', 'active',remove_query_arg('refresh')).'"'.$class.'>Active <span class="count">('.count($Active).')</span></a></li>';
						}
						if(!empty($Draft)) {
							if(isset($_GET['event_status']) && $_GET['event_status'] == 'draft') { $class = ' class="current"'; } else { $class = '';}
							echo '<li>| <a href="'.add_query_arg('event_status', 'draft',remove_query_arg('refresh')).'"'.$class.'>Draft <span class="count">('.count($Draft).')</span></a></li>';
						}
						if(!empty($Complete)) {
							if(isset($_GET['event_status']) && $_GET['event_status'] == 'complete') { $class = ' class="current"'; } else { $class = '';}
							echo '<li>| <a href="'.add_query_arg('event_status', 'complete',remove_query_arg('refresh')).'"'.$class.'>Complete <span class="count">('.count($Complete).')</span></a></li>';
						}
						if(!empty($Cancelled)) {
							if(isset($_GET['event_status']) && $_GET['event_status'] == 'cancelled') { $class = ' class="current"'; } else { $class = '';}
							echo '<li>| <a href="'.add_query_arg('event_status', 'cancelled',remove_query_arg('refresh')).'"'.$class.'>Cancelled <span class="count">('.count($Cancelled).')</span></a></li>';
						}
					echo '
					</ul>';
				}
				
				if(!isset($_GET['event_status']) || $_GET['event_status'] == 'all') { constant_contact_events_list_make_table($events); }
				
				if(!empty($Active) && isset($_GET['event_status']) && $_GET['event_status'] == 'active') { constant_contact_events_list_make_table($Active, __('Active','constant-contact-api')); }
				if(!empty($Draft) && isset($_GET['event_status']) && $_GET['event_status'] == 'draft') { constant_contact_events_list_make_table($Draft, __('Draft','constant-contact-api')); }
				if(!empty($Cancelled) && isset($_GET['event_status']) && $_GET['event_status'] == 'cancelled') { constant_contact_events_list_make_table($Cancelled, __('Cancelled','constant-contact-api')); }
				if(!empty($Complete) && isset($_GET['event_status']) && $_GET['event_status'] == 'complete') { constant_contact_events_list_make_table($Complete, __('Completed','constant-contact-api')); }
					
?>			
	<?php } // end if empty $_events ?>
	</div>
	<?php }
}

function constant_contact_events_list_make_table($events = array(), $title = '') {
	global $cc;
	
	// Create the CC api object for use in this page. 
	if(!constant_contact_create_object()) { return false; }
?>
	<table class="post fixed widefat" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-title" style="">Name</th>
					<th scope="col" id="title" class="manage-column column-title" style="">Title</th>
					<th scope="col" id="eventid" class="manage-column column-id" style="">ID <span class="help cc_qtip" title="Use the ID inside the [ccevents] shortcode to display a single event in your post or page content; for example: [ccevents id='abc1244']" style="display:inline-block; background: url(<?php echo str_replace('/admin/', '/', plugin_dir_url(__FILE__)).'images/help.png'; ?>) left top no-repeat; width:16px; height:16px; overflow:hidden; text-indent:-99999px; text-align:left;">What is this for?</span></th>
					<?php if(!isset($_GET['event_status']) || $_GET['event_status'] == 'all') {?>
					<th scope="col" id="status" class="manage-column column-date" style="">Status</th>
					<?php } ?>
					<th scope="col" id="start" class="manage-column column-author" style="">Start</th>
					<th scope="col" id="registered" class="manage-column column-date" style=""># Registered</th>
					<!-- <th scope="col" id="cancelled" class="manage-column column-date" style=""># Cancelled</th> -->
				</tr>
			</thead>
			<tbody>
			<?php
			if(empty($events)) {?>
				<tr><td colspan="6">
				<h3>No events found&hellip;</h3>
				</td></tr></table>
			<?php
				return;
			}
			
				$alt = 'alt';
				foreach($events as $id => $v) {
					if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
					?>
					<tr id="link-2" valign="middle"  class="<?php echo $alt; ?>">
						<td class="column-title post-title" style="padding:8px;">
							<strong><a class="row-title" href="<?php echo add_query_arg('id', $v['id'], remove_query_arg('refresh')); ?>" title="<?php echo esc_html($v['Name']).' - '.esc_html($v['Title']); ?>"><?php echo esc_html($v['Name']); ?></a></strong>
						</td>
						<td class="column-title post-title" style="padding:8px;">
							<?php echo $v['Title']; ?>
						</td>
						<td class="column-title post-title" style="padding:8px;">
							<?php echo $v['id']; ?>
						</td>
				<?php if(!isset($_GET['event_status']) || $_GET['event_status'] == 'all') {?>
						<td class="column-role" style="padding:8px;">
							<?php echo ucwords(strtolower(esc_html($v['Status']))); ?>
						</td>
				<?php } ?>
						<td class="column-role" style="padding:8px;">
							<?php echo (isset($v['StartDate']) ? date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['StartDate'])) : 'None'); ?>
						</td>
						<td class="column-id" style="padding:8px;">
							<?php echo_if_not_empty($v['Registered'],0); ?>
						</td>
						<!--
						<td class="column-id" style="padding:8px;">
							<?php echo_if_not_empty($v['CancelledCount'],0); ?>
						</td>
						-->
					</tr>
					<?php
				}
			?>
			</tbody>
			</table>
<?php
}
?>