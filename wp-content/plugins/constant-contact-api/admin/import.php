<?php
/**
 * Import submenu page callback function
 *
 * @global <type> $cc
 * @return <type>
 */
function constant_contact_import()
{
	global $cc;

	// Create the CC api object for use in this page.
	if(!constant_contact_create_object()) { return false; }

	$errors = false;
	$success = false;
	
	// view all lists
	$_lists = constant_contact_get_lists(isset($_REQUEST['refresh_lists']));
	$lists = array();
	
	if($_lists):
	foreach($_lists as $k => $v):
		$lists[$v['id']] = $v['Name'];
	endforeach;
	endif;
	
	if(isset($_POST['submit'])):
		$lists = (isset($_POST['cc_lists'])) ? $_POST['cc_lists'] : array();
		
		if(trim($_FILES['importfile']['tmp_name']) != '' && is_uploaded_file($_FILES['importfile']['tmp_name'])):
			
			$status = $cc->create_contacts($_FILES['importfile']['tmp_name'], $lists);
			if($status):
				$success = __("<strong>Import success:</strong> The imported contact data has been sent to the constant contact API and will be processed shortly, the ID for this activity is <code>$status</code>. <a href='?page=constant-contact-activities&id=$status' class='action button'>View Activity</a>",'constant-contact-api');
			else:
				$errors[] = __('Your subscribers could not be imported: ' . constant_contact_last_error($cc->http_response_code),'constant-contact-api');
			endif;
		else:
#			print_r($_POST);
			if(empty($_POST['file'])) {
				$errors[] = __('You did not select a file to upload!','constant-contact-api');
			} else {
				$errors[] = __('We could not recognise the file you uploaded','constant-contact-api');
			}
		endif;
	endif;
	  
?>

	<div class="wrap">
	<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Import Contacts</h2>
	<?php
	if($success):
	?>
		<div id="message" class="updated">
			<p><?php echo $success; ?></p>
		</div>
	<?php
	
	elseif($errors):
	?>
		<div class="error">
		<h3><?php _e("Error"); ?></h3>
		<ul>
			<?php 
			foreach ($errors as $error):
				echo "<li>".$error."</li>";
			endforeach;
			?>
		</ul>
		<br />
		</div>
	<?php
	endif;
	?>
	<p class="alignright"><label class="howto" for="refresh_lists"><span>Are the displayed lists inaccurate?</span> <a href="<?php echo add_query_arg('refresh', 'lists'); ?>" class="button-secondary action" id="refresh_lists">Refresh Lists</a></label></p>
<div class="clear"></div>
<form name="import" id="import" method="post" action="<?php echo remove_query_arg('refresh_lists'); ?>" enctype="multipart/form-data">
<div id="poststuff" class="metabox-holder">
<div id="post-body">
<div id="post-body-content">

<div id="linkadvanceddiv" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class='hndle'><span>Options</span></h3>
<div class="inside">
<table class="form-table" cellspacing="0">
	<tr class="form-field">
		<th valign="top"  scope="row"><p><label for="importfile">CSV or TXT file</label></p><p><span class="description howto">Upload a CSV or TXT file containing your subscribers</span></p></th>
		<td><p><input type="file" name="importfile" class="code" id="importfile" size="50" value="" style="width: 95%" />
		<span class="description"><br />See <a href="http://constantcontact.custhelp.com/cgi-bin/constantcontact.cfg/php/enduser/std_adp.php?p_faqid=2523" target="_blank">this page</a> for help with formatting the file. You can also refer to the sample file (<a href="<?php echo CC_FILE_URL.'email-import-sample.txt';?>" target="_blank">email-import-sample.txt</a>) included in the plugin.</span></p>
		</td>
	</tr>
	<?php
		$selected_lists = get_option('cc_lists');
		$selected_lists = (!is_array($selected_lists)) ? array() : $selected_lists;
		$lists = constant_contact_get_lists();
		?>

		<tr valign="top">
			<th scope="row"><p><label>Contact Lists</label></p><p><span class="description howto">Select the contact lists the imported subscribers will be added to.</span></p></th>
			<td>
			<?php
			if($lists):
			echo '<ul class="categorychecklist">';
			foreach($lists as $k => $v):
				if(in_array($v['id'], $selected_lists)):
					echo '<li><label for="cc_lists_'.$v['id'].'"><input class="menu-item-checkbox" id="cc_lists_'.$v['id'].'" name="cc_lists[]" type="checkbox" checked="checked" value="'.$v['id'].'" /> '.$v['Name'].'</label></li>';
				else:
					echo '<li><label for="cc_lists_'.$v['id'].'"><input class="menu-item-checkbox" id="cc_lists_'.$v['id'].'" name="cc_lists[]" type="checkbox" value="'.$v['id'].'" /> '.$v['Name'].'</label></li>';
				endif;
			endforeach;
			echo '</ul>';
			endif;
			?>
			</td>
		</tr>
</table>
</div>
</div>
</div>

<input type="hidden" name="import" value="1" />
<p class="submit">
<input type="submit" name="submit" class="button-primary" value="<?php _e('Import Subscribers') ?>" />
</p>

</div>
</div>
</form>
</div>

<?php } ?>