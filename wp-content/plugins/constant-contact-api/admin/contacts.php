<?php

// Contacts
function constant_contact_contacts()
{
	global $cc;
	
	if(!constant_contact_create_object()) { echo '<div id="message" class="error"><p>Contacts Not Available. Check your '.admin_url('admin.php?page=constant-contact-api').' plugin settings.</p></div>'; return; }
	
	$contacts = $notices = $errors = array();
	
	if(isset($_GET['add'])) {
	
		if(isset($_POST['add_contact']) && wp_verify_nonce($_POST['add_contact'],'cc_contact')) {
			#r($_POST);
			if(isset($_POST['EmailAddress']) && is_email($_POST['EmailAddress']) && isset($_POST['lists']) && !empty($_POST['lists'])) {
				
				if(isset($_POST['StateCode']) && $_POST['StateName']) {
					unset($_POST['StateCode']);
				}
				if(isset($_POST['CountryCode']) && strtolower($_POST['CountryCode'] == 'usa')) { 
					$_POST['CountryCode'] = 'us';
				}
				
				$cc->action_type = isset($_POST['OptInSource']) ? $_POST['OptInSource'] : $cc->action_type;
				
				unset($_POST['add_contact'], $_POST['_wp_http_referer'], $_POST['OptInSource']);
				
				
				$contact = $cc->create_contact($_POST['EmailAddress'], $_POST['lists'], $_POST);
				
			} elseif(isset($_POST['EmailAddress']) && !is_email($_POST['EmailAddress'])) {
				$errors['EmailAddress'] = 'Contacts require a valid <label for="'.sanitize_title('EmailAddress-field').'"><a>Email Address</a></label>.';
			} elseif(empty($_POST['lists'])) {
				$errors['lists'] = 'Please select at least one email List.';
			}
		}
		
		if(!empty($contact)) {
			$contact = $cc->xml_to_array($contact);
			$id = $cc->get_id_from_link($contact['entry']['id']);
			if(empty($id)) {
				$errors[] = 'There was a problem with the data posted; make sure the information is entered properly.';
			} else {
				echo '<div class="updated"><p>Success: Contact has been added. <a href="'.admin_url('admin.php?page=constant-contact-contacts&id='.$id).'">View Contact Details</a></p></div>';
				delete_transient('cc_contacts');
				$_POST = array();
			}
		}
		
		if(!empty($errors)) {
			echo '
			<div class="error wrap">
				<p><strong>'.__('The contact was not created because:', 'constant-contact-api').'</strong></p>
				<ul class="ul-disc">';
			foreach($errors as $error) {
				echo '
					<li>'.$error.'</li>';
			}
			echo '
				</ul>
				<p>'.__('Please review the form for <span style="color:red;">Errors in red</span>.','constant-contact-api').'</p>
			</div>';
		}
		
		
#		r($errors);
		
		$fields = constant_contact_signup_form_field_mapping();
		$fields['email_type'] = 'EmailType';
		$fields['note'] = 'Note';
		$fields['opt_in_source'] = 'OptInSource';
#		r($fields);
	?>		
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo remove_query_arg(array('add')); ?>">Contacts</a> &gt; Add New Contact</h2>
			
			
			<h3>Edit Contact Details:</h3>
			<form name="editcontact" id="editcontact" method="post" action="<?php echo remove_query_arg(array('delete', 'refresh'));?>">
				<table class="widefat form-table" cellspacing="0">
					<thead>
						<th scope="col" class="column-name">Name</th>
						<th scope="col" class="column-title">Data</th>
					</thead>
					<tbody>
					<?php
					$html = $alt = '';
					
					$noteditable = array('id', 'Status', 'Name', 'OptInTime', 'OptOutTime', 'OptOutReason', 'Confirmed', 'InsertTime', 'LastUpdateTime');
					
						foreach($fields as $v => $id) {
							$inputlength = 50;
							if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
								// Not editable
								if(preg_match('/Time/', $id)) { $v = date('jS F Y \- H:i', (int)$cc->convert_timestamp($v)); }
								if(is_array($v) && empty($v)) { $v = ''; }
								
								$idLabel = preg_replace('/([A-Z])/',' $1', $id);
								$idLabel = preg_replace('/Addr([0-9])/', 'Address $1', $idLabel);
								$idLabel = preg_replace('/Field([0-9])/', 'Field $1', $idLabel);
								
								$v = htmlentities($v);
								
								$options = $help = '';
								
								switch($id) {
									
									case "EmailAddress":
										$inputlength = 80;
										break;
									case "StateCode": 
										$inputlength = 2;
										$help = 'Must be a valid US/Canada State Code and must be consistent with CountryCode. See <a href="https://ui.constantcontact.com/CCSubscriberAddFileFormat.jsp#states" target="_blank">https://ui.constantcontact.com/CCSubscriberAddFileFormat.jsp#states</a>';
										break;
									case "CountryCode":
										$inputlength = 2;
										$help = 'Must be a valid two character, lower case, Country Code. See: <a style="text-decoration: none;" rel="nofollow" href="http://constantcontact.custhelp.com/cgi-bin/constantcontact.cfg/php/enduser/std_adp.php?p_faqid=3614" target="_blank">http://constantcontact.custhelp.com/cgi-bin/consta<wbr>ntcontact.cfg/php/enduser/std_adp.php?p_faqid=3614</a>';
										break;
									case "PostalCode":
									case "SubPostalCode":
										$inputlength = 25;
										break;
									case "Note":
										$inputlength = 500;
										break;
									case "EmailType":
										$options = array('HTML', 'Text');
										break;
									case "OptInSource":
									case "OptOutSource":
										$options = array('ACTION_BY_CUSTOMER', 'ACTION_BY_CONTACT');
										$help = 'If the contact added themselves to the list or excplicitly requested to be added to the list, choose "ACTION_BY_CONTACT." Otherwise, choose "ACTION_BY_CUSTOMER"';
										break;	
								}
								if(!empty($errors) && isset($_POST["{$id}"]) && strlen($_POST["{$id}"]) > $inputlength) {
									$errors["{$id}"] = true;
									$help = '<p class="howto error" style="color:red;">The maximum length of this field is '.$inputlength.' characters. You have entered '.strlen($_POST["{$id}"]).' characters.</p>';
								}
								if(!empty($help)) {
									$help = '<span class="howto">'.$help.'</span>';
								}
								
								if(in_array($id, $noteditable)) {
									$content = $v.'<input type="hidden" name="'.$id.'" value="'.$v.'" />';
									$idLabel .= '<span class="description"><small>'.__(' (Not editable)','constant-contact-api').'</small></span>';
								} else {
									$content = '';
									$value = isset($_POST["{$id}"]) ? $_POST["{$id}"] : '';
									$style = '';
									if(isset($errors["{$id}"])) {
										$style = ' style="background-color: #FFEBE8; border: 1px solid #C00;"';
									}
									if(is_array($options)) {
										$content .= '<ul>';
										$default = true;
										foreach($options as $option) {
											$content .= '<li><label for="'.sanitize_title($id.$option).'"><input type="radio" name="'.$id.'" id="'.sanitize_title($id.$option).'" value="'.$option.'" '.checked(true, $default, false).' /> <span>'.$option.'</span></label></li>';
											$default = false;
										}
										$content .= '</ul>'.$help;
										$inputLengthWarning = '';
									} else {
										$content = '<input type="text" id="'.sanitize_title($id.'-field').'" name="'.$id.'" maxlength="'.$inputlength.'" value="'.$value.'" class="widefat"'.$style.' />'.$help;
										$inputLengthWarning = '<span class="howto">'.$inputlength.' character limit</span>';
									}								
								}
								
								#$v = is_email($v) ? "<a href='mailto:{$v}'>{$v}</a>" : $v; 
								$html .= '<tr class="'.$alt.'"><th scope="row" valign="top" class="column-name"><strong>'.$idLabel.'</strong></th><td>'.$content.$inputLengthWarning.'</td></tr>';
							} 
							$lists = constant_contact_get_lists();
							
							$alt = '';
							$listhtml = '';
							foreach($lists as $key => $details) {
								$listhtml .= '
									<li>
										<label for="cc_lists'.$details['id'].'">
												<input name="lists[]" type="checkbox" value="'.$details['id'].'"'.checked('true', $details['OptInDefault'], false).' id="cc_lists'.$details['id'].'" /> ' . $details['Name'] . '
										</label>
									</li>';
							}
								
								
								
								$html .= '<tr class="'.$alt.'">
											<th scope="row" valign="top" class="column-name">Lists</th>
											<td>
													<ul>
														'.$listhtml.'
													</ul>
												
											</td>
										</tr>';
						
						echo $html;
					?>
					</tbody>
				</table>
				<p class="submit">
					<?php wp_nonce_field( 'cc_contact', 'add_contact'); ?>
					<input type="submit" value="Add Contact" class="button-primary" /> <a href="<?php echo remove_query_arg('add'); ?>" class="button-secondary alignright">Cancel &amp; Return to Contacts Page</a>
				</p>
			</form>
		</div>
<?php
	}
	else if(isset($_GET['id']) && is_numeric($_GET['id']) && !isset($_GET['delete'])) {
	
			
	
#	r($Contacts);
	
		if(isset($_POST['edit_contact']) && wp_verify_nonce($_POST['edit_contact'],'cc_contact')) {
			#r($_POST);
			
			if(empty($_POST['id']) || !is_numeric($_POST['id']) || empty($_POST['EmailAddress']) || (isset($_POST['EmailAddress']) && !is_email($_POST['EmailAddress']))) {
				if(isset($_POST['EmailAddress']) && !is_email($_POST['EmailAddress'])) {
					$errors[] = __('You must provide a valid email address for the contact.','constant-contact-api');
				}
				 
				return false; 
			} else {
			
				@constant_contact_reset_lists_transients();
								
				delete_transient('cccon'.sha1($_POST['id']));
				$additional_fields = $_POST;
				unset($additional_fields['lists'], $additional_fields['edit_contact'], $additional_fields['_wp_http_referer'], $additional_fields['LastUpdateTime'], $additional_fields['Confirmed'], $additional_fields['InsertTime']);
				$updated = $cc->update_contact($_POST['id'], $_POST['EmailAddress'], $_POST['lists'], $additional_fields);
				if($updated) {
					$notices[] = '
					<div class="updated">
						<h4>'.__('The contact has been successfully updated.','constant-contact-api').'</h4>
						<p class="submit"><a href="'.remove_query_arg(array('edit', 'id')).'" class="button-primary">'.__('Return to Contact List','constant-contact-api').'</a> <a href="'.remove_query_arg('edit').'" class="button-secondary action">'.__('Return to Contact','constant-contact-api').'</a></p>
					</div>';
				} else {
					$errors[] = __('The update failed. This may be because of a Constant Contact server issue.','constant-contact-api');
				}
			}
			
		}
	
		$id = htmlentities($_GET['id']);
		$contact = $cc->get_contact($id);
		
		if(!empty($notices)) {
			foreach($notices as $notice) {
				echo $notice;
			}
		}
		
		if(!empty($errors)) {
			echo '<div class="error">';
			foreach($errors as $error) {
				echo "<p>{$error}</p>";
			}
			echo '</div>';
		}
		
		if(isset($_GET['edit'])) {
		?>
		<div class="wrap nosubsub">
			
			<?php 
			if(!empty($contact['Name'])) { $contactTitle = ": &ldquo;{$contact['Name']}&rdquo;";  } else { $contactTitle = " #{$id}"; }
			?>
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo admin_url('admin.php?page=constant-contact-contacts'); ?>">Contacts</a> &gt; <a href="<?php echo remove_query_arg('edit'); ?>">Contact<?php echo $contactTitle; ?></a> &gt; Edit Contact</h2>
			<?php constant_contact_admin_refresh(); ?>
			
			<h3>Edit Contact Details:</h3>
			<form name="editcontact" id="editcontact" method="post" action="<?php echo remove_query_arg(array('add', 'delete', 'refresh_lists'));?>">
				<table class="widefat form-table" cellspacing="0">
					<thead>
						<th scope="col" class="column-name">Name</th>
						<th scope="col" class="column-title">Data</th>
					</thead>
					<tbody>
					<?php
			if(!$contact) {
				echo '<tr><td><p>Contact Not Found</p></td></tr>';
			} else {
					$html = $alt = '';
					
					$noteditable = array('id', 'Status', 'Name', 'OptInTime', 'OptOutTime', 'OptOutReason', 'Confirmed', 'InsertTime', 'LastUpdateTime');
						foreach($contact as $id => $v) {
							if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
							if($id == 'lists') {
								$lists = constant_contact_get_lists();
								$alt = '';
								$listhtml = '';
								foreach($lists as $key => $details) {
									$list_checked = in_array($details['id'], $v) ? ' checked="checked"' : '';
									$listhtml .= '
										<li>
											<label for="cc_lists'.$details['id'].'">
													<input name="lists[]" type="checkbox" value="'.$details['id'].'" '. $list_checked .' id="cc_lists'.$details['id'].'" /> ' . $details['Name'] . '
											</label>
										</li>';
								}
								
								
								
								$html .= '<tr class="'.$alt.'">
											<th scope="row" valign="top" class="column-name">'.ucwords($id).'</th>
											<td>
													<ul>
														'.$listhtml.'
													</ul>
												
											</td>
										</tr>';
							} else {
								$inputlength = 50;
								// Not editable
								if(preg_match('/Time/', $id)) { $v = date('jS F Y \- H:i', (int)$cc->convert_timestamp($v)); }
								if(is_array($v) && empty($v)) { $v = ''; }
								
								$idLabel = preg_replace('/([A-Z])/',' $1', $id);
								$idLabel = preg_replace('/Addr([0-9])/', 'Address $1', $idLabel);
								$idLabel = preg_replace('/Field([0-9])/', 'Field $1', $idLabel);
								
								$v = htmlentities($v);
								
								$options = $help = '';
								
								switch($id) {
									
									case "StateCode": 
										$inputlength = 2;
										$help = 'Must be a valid US/Canada State Code and must be consistent with CountryCode. See <a href="https://ui.constantcontact.com/CCSubscriberAddFileFormat.jsp#states" target="_blank">https://ui.constantcontact.com/CCSubscriberAddFileFormat.jsp#states</a>';
										break;
									case "CountryCode":
										$inputlength = 2;
										$help = 'Must be a valid two character, lower case, Country Code. See: <a style="text-decoration: none;" rel="nofollow" href="http://constantcontact.custhelp.com/cgi-bin/constantcontact.cfg/php/enduser/std_adp.php?p_faqid=3614" target="_blank">http://constantcontact.custhelp.com/cgi-bin/consta<wbr>ntcontact.cfg/php/enduser/std_adp.php?p_faqid=3614</a>';
										break;
									case "PostalCode":
									case "SubPostalCode":
										$inputlength = 25;
										break;
									case "Note":
										$inputlength = 500;
										break;
									case "EmailType":
										$options = array('HTML', 'Text');
										break;
									case "OptInSource":
									case "OptOutSource":
										$options = array('ACTION_BY_CUSTOMER', 'ACTION_BY_CONTACT');
										break;
								}
								
								if(!empty($errors) && isset($_POST["{$id}"]) && strlen($_POST["{$id}"]) > $inputlength) {
									$help = '<p class="howto error" style="color:red;">The maximum length of this field is '.$inputlength.' characters. You have entered '.strlen($_POST["{$id}"]).' characters.</p>';
								}
								
								if(!empty($help)) {
									$help = '<span class="howto">'.$help.'</span>';
								}
								
								if(in_array($id, $noteditable)) {
									$content = $v.'<input type="hidden" name="'.$id.'" value="'.$v.'" />';
									$idLabel .= '<span class="description"><small>'.__(' (Not editable)','constant-contact-api').'</small></span>';
								} else {
									$content = '';
									if(is_array($options)) {
										$content .= '<ul>';
										foreach($options as $option) {
											$content .= '<li><label for="'.sanitize_title($id.$option).'"><input type="radio" name="'.$id.'" id="'.sanitize_title($id.$option).'" value="'.$option.'" '.checked($option, $v, false).' /> <span>'.$option.'</span></label>'.$help.'</li>';
										}
										$content .= '</ul>';
									} else {
										$content = '<input type="text" name="'.$id.'" maxlength="'.$inputlength.'" value="'.$v.'" class="widefat" />'.$help;
									}
								}
								
								#$v = is_email($v) ? "<a href='mailto:{$v}'>{$v}</a>" : $v; 
								$html .= '<tr class="'.$alt.'"><th scope="row" valign="top" class="column-name">'.$idLabel.'</th><td>'.$content.'</td></tr>';
							} 
						}
						echo $html;
			} // contact not found logic
					?>
					</tbody>
				</table>
				<p class="submit">
					<?php wp_nonce_field( 'cc_contact', 'edit_contact'); ?>
					<input type="submit" value="Save Updated Contact Information" class="button-primary" /> <a href="<?php echo remove_query_arg('edit'); ?>" class="button-secondary alignright">Cancel &amp; Return to Contact Details</a>
				</p>
			</form>
		</div>
		<?php
		
		} else {
		
		#$contact_events = $cc->get_contact_events($id); 
		#r($contact_events);
		// display single contact
		?>
		<div class="wrap nosubsub">
			<?php 
			if(!empty($contact['Name'])) { $contactTitle = ": &ldquo;{$contact['Name']}&rdquo;";  } else { $contactTitle = " #{$id}"; }
			?>
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo admin_url('admin.php?page=constant-contact-contacts'); ?>">Contacts</a> &gt; Contact<?php echo $contactTitle; ?></h2>
			
			<?php constant_contact_admin_refresh(); ?>
			
			<h3>Contact Details (<a href="<?php echo add_query_arg('edit', true); ?>">Edit</a>)</h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name">Name</th>
					<th scope="col" class="column-title">Data</th>
				</thead>
				<tbody>
				<?php
			if(!$contact) {
				echo '<tr><td><p>Contact Not Found</p></td></tr>';
			} else {
				$html = $alt = '';
					foreach($contact as $id => $v) {
						if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
						if(!is_array($v)) {
							if(preg_match('/Time/', $id)) { $v = date('jS F Y \- H:i', (int)$cc->convert_timestamp($v)); }
							$id = preg_replace('/([A-Z])/',' $1', $id);
							$id = preg_replace('/Addr([0-9])/', 'Address $1', $id);
							$id = preg_replace('/Field([0-9])/', 'Field $1', $id);
							$v = htmlentities($v);
							$v = is_email($v) ? "<a href='mailto:{$v}'>{$v}</a>" : $v; 
							$html .= '<tr class="'.$alt.'"><th scope="row" valign="top" class="column-name">'.$id.'</th><td>'.$v.'</td></tr>';
						} elseif($id == 'lists') {
							$html .= '<tr class="'.$alt.'"><th scope="row" valign="top" class="column-name">'.ucwords($id).'</th><td>';
							if(!empty($v) && is_array($v)) {
								$listhtml = '';
								foreach($v as $list) {
									$list = $cc->get_list($list);
									$listhtml .= '<li>'.$list['Name'].'</li>';
								}
								if(!empty($listhtml)) { $html .= '<ul class="ul-disc">'.$listhtml.'</ul>'; }
							}
							$html .= '</td></tr>';
						}
					}
					echo $html;
				}
				?>
				</tbody>
			</table>
			<p class="submit"><a href="<?php echo admin_url('admin.php?page=constant-contact-contacts'); ?>" class="button-primary">Return to Contacts</a> <a href="<?php echo add_query_arg('edit', true); ?>" class="button-primary alignright" style="margin:0 0 0 8px;">Edit this Contact</a></p>
	<?php
/*
	// For future versions that use the official CTCT API 
	flush();
		@require_once CC_FILE_PATH . 'ctctWrapper.php';
		$Contacts = new ContactsCollection;
		$CContact = new Contact;
		$CContact->setLink('/ws/customers/303zachary/contacts/'.$_GET['id']);
		$CContact->setId($_GET['id']);
		$Contacts->listContactEvents($CContact, 'opens');
		$Contacts->listContactEvents($CContact, 'clicks');
		$Contacts->listContactEvents($CContact, 'bounces');
		$Contacts->listContactEvents($CContact, 'optOuts');
		$Contacts->listContactEvents($CContact, 'forwards');
		$Contacts->listContactEvents($CContact, 'sends');
		
		
		$Bounces = $CContact->getBounces();
		$Clicks = $CContact->getClicks();
		$OptOuts = $CContact->getOptOuts();
		$Forwards = $CContact->getForwards();
		$Opens = $CContact->getOpens();
		$Sends = $CContact->getSends();
		
		$Actions = array("Bounce" => $Bounces, 'Click' => $Clicks, 'OptOuts' => $OptOuts, 'Forwards' => $Forwards, 'Open' => $Opens, 'Send' => $Sends);		
		
		echo '<h2>Contact History</h2>';
		
		$SendsCount = sizeof($Sends);
		$OpensCount = sizeof($Opens);
		$OpensPer = round(($OpensCount/$SendsCount) * 100, 2);
		$BouncesCount = sizeof($Bounces);
		$BouncesPer = round(($BouncesCount/$SendsCount) * 100, 2);
		$ClicksCount = sizeof($Clicks);
		$ClicksPer = round(($ClicksCount/$SendsCount) * 100, 2);
		$ForwardsCount = count($Forwards);
		$ForwardsPer = round(($ForwardsCount/$SendsCount) * 100, 2);
#		$OptOuts = $CContact->getOptOuts();
#		

$out = <<<EOD

	<h3>This user has been sent $SendsCount emails.</h3>
	
	<ul class="ul-disc">
		<li>The user opened $OpensPer% of the sent emails ($OpensCount opened).</li>
		<li>The user clicked through $ClicksPer% of the sent emails ($ClicksCount clicks).</li>
		<li>The user forwarded $ClicksPer% of the sent emails ($ForwardsCount forwards).</li>
		<li>$BouncesPer% of the emails sent to this user have bounced ($BouncesCount bounces).</li>
	</ul>

EOD;
		echo $out;
		foreach($Actions as $Name => $Value) {
			if(empty($Value)) { continue; }
?>
			<h3><?php echo $Name; ?> History</h3>
			
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name">Name</th>
					<th scope="col" class="column-title">Time</th>
					<th scope="col" class="column-title">Description</th>
				</thead>
				<tbody>
				<?php
#				$html = $alt = '';
					
				
					foreach($Value as $key => $value) {
						$campaignID = preg_replace('/(?:.*?)\/campaigns\/([0-9]+)/ism', '$1', $value['CampaignLink']);
						echo '<td><a href="'.admin_url('admin.php?page=constant-contact-campaigns&id='.$campaignID).'">Campaign #'.$campaignID.'</a></td>';
						echo '<td>'.date('jS F Y \- H:i', (int)$cc->convert_timestamp($value['EventTime'])).'</td>';
						$value['Description'] = isset($value['Description']) ? $value['Description'] : '';
						echo '<td>'.$value['Description'].'</td>';
					}
					#echo $html;
				?>
				</tbody>
			</table>
	<?php 
		}
*/
	?>
		</div>
	<?php
	}
	} else {
		#r(@$Contacts->listContacts());
		?>
		<div class="wrap nosubsub">
		<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Contacts <a href="<?php echo add_query_arg(array('add' => 1), remove_query_arg('refresh')); ?>" class="button add-new-h2" title="Add New Contact">Add New</a></h2>
		<?php 
		
		if(isset($_GET['delete'])) {
		
			$deleted = $cc->delete_contact($_GET['id']);			
		
			if($deleted) {
				echo '<div class="updated">
						<h4>'.__('The contact has been successfully deleted.').'</h4>
					</div>';
				$_GET['refresh'] = 'contacts'; // Re-load contact lists.
			} else {
				$error = '';
				if(isset($cc->last_error)) {
					$error = ': <strong>'.$cc->last_error.'</strong>';
				}
				echo '<div class="error"><p>There was an error deleting the contact'.$error.'</p></div>';
			}
		}
		
		// Instead of having to fetch the whole list before knowing if there's a transient or not,
		// let's just see if a valid timeout is set.
		$contacts_timeout = get_option( '_transient_timeout_cc_contacts' );
		if(isset($_GET['refresh']) || $contacts_timeout  < time() || $contacts_timeout === false) {
			$loaded = isset($_GET['refresh']) ? 're-loaded' : 'loaded';
			echo '<div class="updated" id="cc_contact_list_being_updated"><p>'.sprintf(__('Please be patient; your contacts are being %s. This may take a couple of minutes. Once loaded,   contacts will be stored for 6 hours.', 'constant-contact-api'), $loaded).'</p></div>';
		}
		
		flush();
		
		$allcontacts = $contacts = $cc->get_all_contacts();
		
		// Hide the notice and pretend we're using AJAX
		echo '
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#cc_contact_list_being_updated").delay(300).fadeOut("slow");
			});
		</script>';
//		echo '<style type="text/css"> #cc_contact_list_being_updated { display:none; } </style>';
		flush();
		#r($contacts);
		
		$page_size = apply_filters('constant_contact_admin_page_size', 50);
		$startpage = 1;
		$page_index = empty($_GET["page_num"]) ? $startpage -1 : intval($_GET["page_num"]) - 1;
		$first_item_index = $page_index * $page_size;
		
		$Active = $DoNotMail = array();
		
		foreach($allcontacts as $k => $v) {
			if(!isset($v['OptOutTime'])) {
				$Active[] = $v;
			} else {
				$DoNotMail[] = $v;
			}
		}
		
		if(empty($_GET['contact_status'])) {
			$contact_count = sizeof($allcontacts);
		} elseif($_GET['contact_status'] == 'donotmail') {
			$contact_count = sizeof($DoNotMail);
			$contacts = $DoNotMail;
		} elseif($_GET['contact_status'] == 'active') {
			$contact_count = sizeof($Active);
			$contacts = $Active;
		}
		
		$contacts = array_slice($contacts, $first_item_index, $page_size, true);
		
		$page_links = array(
			'base' =>  remove_query_arg(array('page_num', 'refresh')).'%_%',
			'format' => '&page_num=%#%',
#			'add_args' => $args,
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => ceil( $contact_count / $page_size),
			'current' => $page_index + 1,
			'show_all' => false
		);
				
		$page_links = apply_filters('constant_contact_admin_pagination', $page_links);
		
		$page_links = paginate_links($page_links);
		
		
		// display all contacts
		
		if($contact_count == 0) { $first_item_index--; } ?>
				<div class="tablenav alignright" style="padding-left:2em;">
					<div class="tablenav-pages">
						<span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "constant-contact-api"), $first_item_index + 1, ($first_item_index + $page_size) > $contact_count ? $contact_count : $first_item_index + $page_size , $contact_count) ?></span>
						<?php if($page_links){ echo $page_links; } ?>
					</div>
				</div>
				<?php 
		constant_contact_admin_refresh(); ?>

		<?php
		if(empty($contacts)):
			echo '<div id="message" class="updated"><p>No Contacts Found</p></div>';
			return;
		endif;
		?>
		<?php
			
#		if(!empty($Active) || !empty($Draft) || !empty($Cancelled) || !empty($Complete)) {
					echo '<ul class="subsubsub">';
						if(!isset($_GET['contact_status']) || $_GET['contact_status'] == 'all') { $class = ' class="current"'; } else { $class = '';}
						echo '<li><a href="'.remove_query_arg(array('refresh','contact_status','page_num')).'"'.$class.'>All <span class="count">('.count($allcontacts).')</span></a></li>';
						if(!empty($Active)) {
							if(isset($_GET['contact_status']) && $_GET['contact_status'] == 'active') { $class = ' class="current"'; } else { $class = '';}
							echo '<li>| <a href="'.add_query_arg('contact_status', 'active',remove_query_arg(array('refresh', 'page_num'))).'"'.$class.'>Active <span class="count">('.count($Active).')</span></a></li>';
						}
						if(!empty($DoNotMail)) {
							if(isset($_GET['contact_status']) && $_GET['contact_status'] == 'donotmail') { $contacts = $DoNotMail; $class = ' class="current"'; } else { $class = '';}
							echo '<li>| <a href="'.add_query_arg('contact_status', 'donotmail',remove_query_arg(array('refresh', 'page_num'))).'"'.$class.'>Do Not Mail <span class="count">('.count($DoNotMail).')</span></a></li>';
						}
					echo '
					</ul>';
#				}
		?>
		<table class="form-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Email Address'); ?></th>
					<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Name'); ?></th>
					<th scope="col" id="status" class="manage-column column-name" style=""><?php _e('Opt-In/Opt-Out Time'); ?></th>
					<th scope="col" id="view" class="manage-column column-author" style="">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
<?php 		
/*
<script type="text/javascript">
	jQuery(document).ready(function($) { 
		
		$('a.delete').click(function(e) {
			return confirm('Do you want to delete this contact? This will remove the contact permanently from Constant Contact.');
		});
		
	});
</script>
*/

			$alt = $html = '';
			foreach($contacts as $id => $v) {
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				$v['Name'] = (!empty($v['Name']) && !is_array($v['Name']) && $v['Name'] !== 'Array') ? htmlentities($v['Name']) : '';
				$v['OptInTime'] = empty($v['OptInTime']) ? '' : date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['OptInTime']));
				$v['OptInTime'] = (empty($v['OptInTime']) && !empty($v['OptOutTime'])) ? '<span style="color:red;" title="Opted Out of Receiving Emails on '.date('m/d/Y', (int)$cc->convert_timestamp($v['OptOutTime'])).'">'.date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['OptOutTime'])).'</span>' : $v['OptInTime'];
				$html .= '
				<tr class="'.$alt.'">
					<td class="column-name">'.htmlentities($v['EmailAddress']).'</td>
					<td class="column-name">'.htmlentities($v['Name']).'</td>
					<td class="column-name">'.$v['OptInTime'].'</td>
					<td class="column-name">
						<a href="'.admin_url('admin.php?page=constant-contact-contacts&id='.$v['id']).'" title="'.__('View contact details','constant-contact-api').'">'.__('View','constant-contact-api').'</a> | 
						<a href="'.admin_url('admin.php?page=constant-contact-contacts&id='.$v['id']).'&edit=true" title="'.__('Edit this contact','constant-contact-api').'">'.__('Edit','constant-contact-api').'</a>
					</td>
				</tr>';
				// | <a href="'.admin_url('admin.php?page=constant-contact-contacts&id='.$v['id']).'&delete=true" title="'.__('Delete this contact').'" style="color:red;" class="delete">'.__('Delete').'</a>
			}
			echo $html;
		?>
			</tbody>
		</table>
		</div>
		<?php
	}
	
}
?>