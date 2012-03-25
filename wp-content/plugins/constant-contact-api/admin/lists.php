<?php

// Retrofitted to make much more WordPressy 10/27/2010 by katzwebdesign

// lists
function constant_contact_lists()
{
	global $cc;

	// Create the CC api object for use in this page.
	constant_contact_create_object();

	$lists = array();
	
	if(isset($_GET['add'])):
		// add List
		
		$list_name = '';
		$sort_order = 99;
		
		?>
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo remove_query_arg(array('view', 'refresh','edit', 'add'));?>">Lists</a> &gt; Add New List</h2>
			
			<form name="addlist" id="addlist" method="post" action="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh'));?>">
			<input type="hidden" name="add" value="1" />
			<div id="poststuff" class="metabox-holder">
			<div id="post-body">
			<div id="post-body-content">
			
			<div id="linkadvanceddiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Options</span></h3>
			<div class="inside">
			
			<table class="form-table" cellspacing="0">
				<tr>
					<th valign="top"  scope="row"><p><label for="link_image"><span>List Name</span></label></p></th>
					<td>
						<input type="text" name="list_name" value="<?php echo $list_name; ?>" size="50" />
					</td>
				</tr>
				<tr>
					<th valign="top"  scope="row"><p><label for="link_image"><span>Sort Order</span></label></p></th>
					<td>
						<input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="10" />
					</td>
				</tr>
			</table>
			</div>
			</div>
			</div>
			
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php _e('Create List') ?>" />
				<a href="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh'));?>" class="button-secondary">Cancel</a>
			</p>
			
			</div>
			</div>
			</form>

		</div>
		
		<?php
	
	elseif(isset($_GET['view'])):
		$id = (int) $_GET['view'];
		$contacts = $cc->get_all_list_members($id);
		$list = constant_contact_get_list($id);
		
		if($list['ShortName'] !== $list['Name']) {
			$displayname = '<abbr title="'.$list['Name'].'">'.$list['ShortName'].'</abbr>';
		} else {
			$displayname = $list['Name'];
		}
		?>
		<div class="wrap nosubsub">
			
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo remove_query_arg(array('view','add','edit', 'refresh'));?>">Lists</a> &gt; List: &ldquo;<?php echo $displayname; ?>&rdquo; Contacts</h2>
			<?php constant_contact_admin_refresh('contacts'); ?>
						
		<table class="form-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Email Address'); ?></th>
					<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Name'); ?></th>
					<th scope="col" id="view" class="manage-column column-author" style="">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$alt = $html = '';
		if(empty($contacts)) {
			$html = '<tr><td><p>Contact List Not Found</p></td></tr>';
		} else {
			foreach($contacts as $id => $v) {
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				$v['Name'] = (!empty($v['Name']) && !is_array($v['Name']) && $v['Name'] !== 'Array') ? htmlentities($v['Name']) : '';
				$v['OptInTime'] = empty($v['OptInTime']) ? '' : date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['OptInTime']));
				$v['OptInTime'] = (empty($v['OptInTime']) && !empty($v['OptOutTime'])) ? '<del style="color:red;" title="Opted Out of Receiving Emails on '.date('m/d/Y', (int)$cc->convert_timestamp($v['OptOutTime'])).'">'.date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['OptOutTime'])).'</del>' : $v['OptInTime'];
				$html .= '
				<tr class="'.$alt.'">
					<td class="column-name">'.htmlentities($v['EmailAddress']).'</td>
					<td class="column-name">'.htmlentities($v['Name']).'</td>
					<td class="column-name">
						<a href="'.admin_url('admin.php?page=constant-contact-contacts&id='.$v['id']).'" title="'.__('View contact details','constant-contact-api').'">'.__('View','constant-contact-api').'</a> | 
						<a href="'.admin_url('admin.php?page=constant-contact-contacts&id='.$v['id']).'&edit=true" title="'.__('Edit this contact','constant-contact-api').'">'.__('Edit','constant-contact-api').'</a>
					
					</td>
				</tr>';
			}
		}
		echo $html;
		?>
		</tbody>
		</table>
		<p class="submit"><a href="<?php echo add_query_arg('refresh', 'list'); ?>" class="button-secondary alignright" title="List data is stored for 1 hour. Refresh data now.">Refresh List Contacts</a></p>
		<?php
		return;
		
	elseif(isset($_GET['edit'])):
		// edit list
		
		$id = (int) $_GET['edit'];
		$list = constant_contact_get_list($id);
		
		if(!$list):
			return '<p>Contact List Not Found</p></div>';
		endif;
		
		$list_name = $list['Name'];
		$sort_order = $list['SortOrder'];
			
		if($list['ShortName'] !== $list['Name']) {
			$displayname = '<abbr title="'.$list['Name'].'">'.$list['ShortName'].'</abbr>';
		} else {
			$displayname = $list['Name'];
		}
		?>
		<div class="wrap nosubsub">
			
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo remove_query_arg(array('view', 'refresh', 'id', 'edit'));?>">Lists</a> &gt; Edit List: &ldquo;<?php echo $displayname; ?>&rdquo;</h2>
			
			<form name="editlist" id="editlist" method="post" action="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh'));?>">
			<input type="hidden" name="edit" value="<?php echo $id; ?>" />
			<div id="poststuff" class="metabox-holder">
			<div id="post-body">
			<div id="post-body-content">
			
			<div id="linkadvanceddiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Options</span></h3>
			<div class="inside">
			
			<table class="form-table" cellspacing="0">
				<tr>
					<th valign="top"  scope="row"><label for="link_image">List Name</label></th>
					<td>
						<input type="text" name="list_name" value="<?php echo $list_name; ?>" size="50" />
					</td>
				</tr>
				<tr>
					<th valign="top"  scope="row"><label for="link_image">Sort Order</label></th>
					<td>
						<input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="10" />
					</td>
				</tr>
			</table>
			</div>
			</div>
			</div>
			
			<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Save List') ?>" />
			<a href="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh'));?>" class="button-secondary">Cancel</a>
			</p>
			
			</div>
			</div>
			</form>

		</div>
		
		<?php
	
	else:
		$force = false;
		if(isset($_REQUEST['delete'])):
		// delete list		
			$id = (int) $_REQUEST['delete'];
			$list = constant_contact_get_list($id);
			
			if(!$list):
				_e('<div id="message" class="error"><p><strong>Failed to delete contact list:</strong> Contact List Not Found</p></div>');
			else:			
				$status = $cc->delete_list($id);
				
				if($status):
					_e('<div id="message" class="updated"><p>The contact list <strong>has been deleted</strong>.</p></div>');
				else:
					_e('<div id="message" class="error"><p><strong>Failed to delete contact list:</strong> ' .  constant_contact_last_error($cc->http_response_code).'</p></div>');
				endif;
			endif;
		endif;
		
		if(isset($_POST['edit'])):
			$list_name = $_POST['list_name'];
			$sort_order = (int)$_POST['sort_order'];
			$id = (int) $_POST['edit'];
			
			$status = $cc->update_list($id, $list_name, 'false', $sort_order);
			
			if($status):
				_e('<div id="message" class="updated"><p>The contact list '.$list_name.' <strong>has been edited</strong>.</p></div>');
			else:
				_e('<div id="message" class="error"><p>Failed to edit contact list: ' .  constant_contact_last_error($cc->http_response_code).'</p></div>');
			endif;
		endif;
		
		if(isset($_POST['add'])):
			$list_name = $_POST['list_name'];
			$sort_order = $_POST['sort_order'];
			
			$status = $cc->create_list($list_name, 'false', $sort_order);
			
			if($status):
				_e('<div id="message" class="updated"><p>The contact list '.$list_name.' <strong>has been created</strong>.</p></div>');
			else:
				_e('<div id="message" class="error"><p><strong>Failed to create contact list:</strong> ' .  constant_contact_last_error($cc->http_response_code).'</p></div>');
			endif;
		endif;
		
		// If you've changed your lists, let's get rid of the cached version.
		if(isset($_POST['add']) || isset($_POST['edit']) || isset($_POST['delete']) || (isset($_GET['refresh']) && $_GET['refresh'] == 'lists')) { $force = true; }
		
		// view all lists
		$_lists = constant_contact_get_lists($force);
			
		if($_lists):
		foreach($_lists as $k => $v):
			$lists[$v['id']] = $v;
		endforeach;
		endif;
		
		if(!$_lists):
			return '<p>No Contact Lists Found</p>';
		endif;
		
		// display all lists
		?>

		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Lists <a href="<?php echo add_query_arg('add',1,remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" class="button add-new-h2" title="Add New List">Add New</a></h2>
			<?php constant_contact_admin_refresh(); ?>

			<table class="post fixed widefat" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" id="url" class="manage-column column-name">Name</th>
						<th scope="col" id="name" class="manage-column column-id">ID</th>
						<th scope="col" id="sort-order" class="manage-column column-id">Sort Order</th>
						<th scope="col" id="visible" class="manage-column column-id">View</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$alt ='';
					foreach($lists as $id => $v):
					if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
					?>
					<tr class="<?php echo $alt; ?>">
						<td class="column-title post-title page-title">
							<strong><a href="<?php echo add_query_arg(array('edit'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" class="row-title"><?php echo $v['Name']; ?></a></strong>
							<div class="row-actions">
								<span class="edit"><a href="<?php echo add_query_arg(array('edit'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" title="Edit this item">Edit</a> | <a href="<?php echo add_query_arg(array('view'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" title="View this lists' contacts">View Contacts</a></span></div>
								<?php /* <span class="trash"><a onclick="return confirm('Really delete this contact list? This will permanently remove the list from Constant Contact.');" href="<?php echo add_query_arg(array('delete'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" class="submitdelete">Delete</a> */ ?>
						</td>
						<td class="column-id">
							<?php echo $v['id']; ?>
						</td>
						<td class="column-id">
							<?php echo $v['SortOrder']; ?>
						</td>
						<td class="column-id" style="vertical-align:middle;">
							<a href="<?php echo add_query_arg(array('view'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh'))); ?>" class="button" title="View contacts for &ldquo;<?php echo esc_js($v['Name']); ?>&rdquo;">View Contacts</a>
						</td>
					</tr>
					<?php
					endforeach;
				?>
				</tbody>
			</table>
		</div>
		<?php
	endif;
	
}
?>