<?php // $Id$

// show the admin settings page
function constant_contact_registration_settings()
{
    // See if the user has posted us some information
    if(isset($_GET['updated'])):
		?>
		<div class="updated">
			<p><strong><?php _e('Your settings have been saved', 'constant_contact_api' ); ?></strong></p>
		</div>
		<?php
    endif;

    /**
     * Fetch full list of contact lists for various purposes
     */
    $all_contact_lists = constant_contact_get_lists();
		
?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.moreInfo').click(function() {
				$('#registrationScreenshots').slideToggle();
				return false;
			});
			
			$('.wrap form input[name=cc_register_page_method]').live('load click change', function() {
				updateListSelectionVisibility();	
			});
			
			function updateListSelectionVisibility() {
				if($('input[name=cc_register_page_method]:checked').val() == 'none') {
					$('.wrap form table.form-table tr').not('.alwaysshow').hide();
				} else {
					$('.wrap form table.form-table tr').not('.alwaysshow,.list_selection').show();
					
					if($('input[name=cc_register_page_method]:checked').val() == 'lists') {
						$('tr.list_selection').fadeIn('fast');
					} else {
						$('tr.list_selection').fadeOut('fast');
					}
				}
			}
			
			updateListSelectionVisibility();
		});
	</script>

	<div class="wrap">
		<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Registration &amp; User Profile Settings</h2>
			<?php constant_contact_admin_refresh(); ?>
	<form method="post" action="options.php">
	<?php settings_fields( 'constant-contact-registration' ); ?>
	<div class="alignright" style="width:510px; display:none;" id="registrationScreenshots">
		<h4 style="text-align:center;"><a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?action=register">Blog Registration</a> Form Screenshots</h4>
		<div class="alignleft" style="width:250px;"><img src="<?php echo CC_FILE_URL; ?>images/registration-form-before.jpg" alt="registration-form-before" width="250" height="299"/><p class="caption howto">User subscription method: <strong>Disabled</strong></p></div>
		<div class="alignright" style="width:250px;"><img src="<?php echo CC_FILE_URL; ?>images/screenshot-1.jpg" alt="screenshot-1" width="250" height="367"/><p class="howto">User subscription method: List Selection, List Selection Format: <strong>Checkboxes</strong></p></div>
	</div>
	<h3>Registration Screen and User Profile Settings</h3>
	<p class="description">Enabling this tool will add subscription options for logged-in users on your site in the WordPress <a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?action=register">registration</a> and user profile screens. <strong><a href="#registrationScreenshots" class="moreInfo">View screenshots</a></strong>.</p>
	<p><strong>Note:</strong> If new user registration is disabled for your WordPress installation ("Anyone can register" in <strong>Settings &gt; General</strong>)  then visitors to your site will not be able to subscribe with this  method, but it will still be available to logged-in users via their  personal profile options.</p>

	<table class="form-table widefat">
		<tr valign="top" class="alwaysshow">
			<th scope="row"><p><label for="cc_register_page_method_none"><span>User Subscription Method</span></label></th>
			<td>
				<p>
					<label for="cc_register_page_method_none" class="howto"><input <?php checked(!get_option('cc_register_page_method') || get_option('cc_register_page_method')=='none') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" id="cc_register_page_method_none" value="none" /> <span>Disabled</span></label>
					<label for="cc_register_page_method_checkbox" class="howto"><input <?php echo (get_option('cc_register_page_method')=='checkbox') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" id="cc_register_page_method_checkbox" value="checkbox" /> <span>Single Checkbox</span></label>
					<label for="cc_register_page_method_lists" class="howto"><input <?php echo (get_option('cc_register_page_method')=='lists') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" id="cc_register_page_method_lists" value="lists" /> <span>List Selection</span></label>
				</p>
			<p class="description">
				<strong>Single Checkbox</strong>: Shows users a checkbox which, if ticked, will automatically subscribe them to the lists you select below in the <strong>Active Contact Lists</strong> section.<br />
				<strong>List Selection</strong>: Displays the <strong>Active Contact Lists</strong> as a set of checkboxes/multi-select and lets the user decide which ones they want</p>
			</td>
		</tr>
		<tr valign="top" class="list_selection hide-if-js" <?php if(get_option('cc_register_page_method')!=='lists') { echo ' style="display:none;"';} ?>>
			<th scope="row"><p><label for="cc_list_selection_format_checkbox"><span>List Selection Format</span></label></th>
			<td>
				<p><label for="cc_list_selection_format_checkbox" class="howto"><input <?php echo (!get_option('cc_list_selection_format') || get_option('cc_list_selection_format')=='checkbox') ? 'checked="checked"':''; ?> type="radio" id="cc_list_selection_format_checkbox" name="cc_list_selection_format" value="checkbox" /> <span>Checkboxes</span></label>
				<label for="cc_list_selection_format_select" class="howto"><input <?php echo (get_option('cc_list_selection_format')=='select') ? 'checked="checked"':''; ?> type="radio" id="cc_list_selection_format_select" name="cc_list_selection_format" value="select" /> <span>Multi-Select</span></label>
				<label for="cc_list_selection_format_dropdown" class="howto"><input <?php echo (get_option('cc_list_selection_format')=='dropdown') ? 'checked="checked"':''; ?> type="radio" id="cc_list_selection_format_dropdown" name="cc_list_selection_format" value="dropdown" /> <span>Dropdown List</span></label></p>
				<p class="description">
					This controls how the contact lists are displayed on the registration screen and user profile settings if you use the <strong>List Selection</strong> method above. <br />
					<strong>Checkboxes</strong> will offer separate checkboxes.
					<strong>Multi-Select</strong> will offer the list as a multi-select drop-down. <br />
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="cc_default_opt_in"><span>Opt-in users by default?</span></label></th>
			<td>
			<p><label for="cc_default_opt_in"><input <?php echo (get_option('cc_default_opt_in')) ? 'checked="checked"':''; ?> type="checkbox" id="cc_default_opt_in" name="cc_default_opt_in" value="1" /> <span>Yes, subscribe my users by default.</span></label></p>
			<p class="description">
				This determines if the registration screen checkbox is checked by default or, if using the "List Selection" method, whether all lists will be pre-selected by default.
			</p>
			</td>
		</tr>
		<?php
		/**
		 * Contact Lists and Hidden Contact Lists options
		 * Only show them if we have lists from the API already
		 */
		if(is_array($all_contact_lists)):
			
			/**
			 * Get already-selected lists to pre-fill the selections
			 * Otherwise set it up as empty array.
			 */
			$active_lists = get_option('cc_lists');
			if (!is_array($active_lists))
				$active_lists = array();
	
			/**
			 * Get already-selected HIDDEN lists, oterwise set up empty array
			 */
			$hidden_lists = get_option('cc_exclude_lists');
			if (!is_array($hidden_lists))
				$hidden_lists = array();
	
			/**
			 * Output the lists of lists
			 */
			?>
	
			<tr valign="top">
				<th scope="row"><p><label><span>Active Contact Lists</span></label></th>
				<td>
				<p class="widefat" style="padding:5px;margin:0 0 5px 0">
				<?php
				// Loop through all lists and output them as checkboxes
				foreach($all_contact_lists as $key => $details):
	
					// Set up list_checked to contain checked=checked for lists that should be checked.
					$list_checked = '';
					if (in_array($details['id'], $active_lists))
						$list_checked = ' checked="checked"';
	
					// Echo the checkbox and label including the checked status determined above
					echo '<label for="cc_lists'.$details['id'].'">';
					echo '<input name="cc_lists[]" type="checkbox" value="'.$details['id'].'" '. $list_checked .' id="cc_lists'.$details['id'].'" /> ';
					echo $details['Name'] . '</label><br />';
				endforeach;
				?>
				</p>
				<p class="description">
					If you use the <strong>Single Checkbox</strong> method in the <strong>Register Page Subscribe Method</strong> option then users who check the box will be automatically subscribed to all contact lists chosen above.<br />
					If using the <strong>List Selection</strong> method then only the selected lists will be shown to the user to choose from.<br />
					<strong>If you do not select any lists above</strong> the user will be able to choose from all of your contact lists, apart from those set to be hidden using the setting below.
				</p>
				</td>
			</tr>
			<tr valign="top" class="list_selection hide-if-js">
				<th scope="row"><p><label><span>Hidden Contact Lists</span></label></th>
				<td>
				<p class="widefat" style="padding:5px;margin:0 0 5px 0">
	
				<?php
				foreach($all_contact_lists as $key => $details):
					// Set up list_checked to contain checked=checked for lists that should be checked.
					$list_checked = '';
					if (in_array($details['id'], $hidden_lists))
						$list_checked = ' checked="checked"';
	
					// Echo the checkbox and label including the checked status determined above
					echo '<label for="cc_exclude_lists'.$details['id'].'">';
					echo '<input name="cc_exclude_lists[]" type="checkbox" value="'.$details['id'].'" '. $list_checked .' id="cc_exclude_lists'.$details['id'].'" /> ';
					echo $details['Name'] . '</label><br />';
				endforeach;
				?>
				</p>
				<p class="description">
					When using the <strong>List Selection</strong> method you can  select contact lists in this setting to hide them from users. This  option has no effect when you are using the Single Checkbox subscribe method.			</p>
				</td>
			</tr>
			<?php
		endif;
		?>
		<tr valign="top">
			<th scope="row"><p><label for="cc_signup_title"><span>Signup Title</span></label></th>
			<td>
			<input type="text" name="cc_signup_title" id="cc_signup_title" value="<?php echo get_option('cc_signup_title'); ?>" size="50" />
			<p class="description">
				 Title for the signup form displayed on the registration screen and user profile settings if enabled.
			</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="cc_signup_description"><span><label for="cc_signup_description">Signup Description</label></span></label></th>
			<td>
			<textarea name="cc_signup_description" id="cc_signup_description" cols="50" rows="4"><?php echo get_option('cc_signup_description'); ?></textarea>
			<p class="description">
				Signup form description text displayed on the registration screen and user profile setting, if enabled. HTML allowed. Paragraphs will be added automatically like in posts.
			</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><p><label for="cc_extra_field_mappings"><span>Extra Field Mappings</span></label></p></th>
			<td>
			<textarea name="cc_extra_field_mappings" id="cc_extra_field_mappings" cols="50" rows="6" class="large-text code"><?php echo get_option('cc_extra_field_mappings'); ?></textarea>
			<?php if(function_exists('check_ccfg_compatibility') && check_ccfg_compatibility()) {?>
			<p class="description">Forms created using the Constant Contact widget support separate field mapping. This is for the registration form only.</p>
			<?php } ?>
			<p class="description">Specify the mappings for your extra fields, if these fields are found in your register form they will be sent to constant contact, you should define these in the format FirstName:ActualFieldname and separate with a comma. You will only need to change the second value to match your form fieldnames.</p><p class="description">Note: the fields are not automatically added; you must use another plugin such as <a href="http://wordpress.org/extend/plugins/register-plus/" target="_blank">Register Plus</a> to add the fields to your register page.
			</p>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
<?php
}
?>