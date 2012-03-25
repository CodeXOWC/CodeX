<?php

// campaigns
function constant_contact_campaigns()
{
	global $cc;
	
	if(!constant_contact_create_object()) { echo '<div id="message" class="error"><p>Campaigns Not Available. Check your '.admin_url('admin.php?page=constant-contact-api').' API settings.</p></div>'; return; }
	
	$campaigns = array();
	if(isset($_GET['id'])):
		$id = htmlentities($_GET['id']);
		$campaign = $cc->get_campaign($id);
		
		if(!$campaign):
			echo '<p>Campaign Not Found</p></div>';
		endif;
		
		
		// display single activity
		?>
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Campaigns - <?php echo $id; ?></h2>
			<?php constant_contact_admin_refresh(); ?>
			
			<h3>Campaign Stats:</h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name">Sent</th>
					<th scope="col" class="column-title">Opens</th>
					<th scope="col" class="column-title">Clicks</th>
					<th scope="col" class="column-title">Bounces</th>
					<th scope="col" class="column-title">Forwards</th>
					<th scope="col" class="column-title">OptOuts</th>
					<th scope="col" class="column-title">Spam Reports</th>
				</thead>
				<tbody>
					<tr valign="top">
				<?php
				$html = '';
					$cols = array('Sent', 'Opens', 'Clicks', 'Bounces', 'Forwards', 'OptOuts', 'SpamReports');
					foreach($cols as $col) {
						$html .= '<td>'.htmlentities($campaign[$col]).'</td>';
					}
					echo $html;
				?>	</tr>
				</tbody>
			</table>
			<h3>Campaign Details:</h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name">Name</th>
					<th scope="col" class="column-title">Data</th>
				</thead>
				<tbody>
				<?php
				$html = $alt = '';
					foreach($campaign as $id => $v) {
						if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
						if(!is_array($v)) {
							$id = preg_replace('/([A-Z])/',' $1', $id);
							$html .= '<tr class="'.$alt.'"><th scope="row" valign="top" class="column-name">'.$id.'</th><td>'.htmlentities($v).'</td></tr>';
						}
					}
					echo $html;
				?>
				</tbody>
			</table>
			<p class="submit"><a href="<?php echo admin_url('admin.php?page=constant-contact-campaigns'); ?>" class="button-primary">Return to Campaigns</a></p>
		</div>
	<?php 
	else:
		$_campaigns = $cc->get_campaigns();
		if($_campaigns):
		foreach($_campaigns as $k => $v):
			$campaigns[$v['id']] = $v;
		endforeach;
		endif;
		
		// display all campaigns
		?>

		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Campaigns</h2>
			<?php constant_contact_admin_refresh(); ?>

		<?php if(!$_campaigns): ?>
			<div id="message" class="updated"><p>No Campaigns Found</p></div>
		<?php
			return;
		endif;
		?>
		<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style="">Name</th>
					<th scope="col" id="date" class="manage-column column-name" style="">Type</th>
					<th scope="col" id="status" class="manage-column column-name" style="">Last Edited</th>
					<th scope="col" id="id" class="manage-column column-name" style="">Status</th>
					<th scope="col" id="view" class="manage-column column-name" style="">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
	
		<?php
			$alt = $html = '';
			foreach($campaigns as $id => $v) {
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				$html .= '
				<tr class="'.$alt.'">
					<td class="column-name">'.htmlentities($v['Name']).'</td>
					<td class="column-name">'.htmlentities($v['Status']).'</td>
					<td class="column-name">'.date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['Date'])).'</td>
					<td class="column-name">'.htmlentities($v['id']).'</td>
					<td class="column-name"><a href="'.admin_url('admin.php?page=constant-contact-campaigns&id='.$v['id']).'">View Campaign Details</a></td>
				</tr>';
			}
			echo $html;
		?>
			</tbody>
		</table>
		<?php
	endif;
	
}
?>