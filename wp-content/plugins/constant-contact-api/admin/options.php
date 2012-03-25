<?php // $Id$

// Hook settings registration function
add_action( 'admin_init', 'constant_contact_register_settings' );

// Register the settings we need on the settings page
function constant_contact_register_settings()
{
	$group = 'constant-contact';
	
	register_setting('constant-contact', 'cc_username');
	register_setting('constant-contact', 'cc_password');
	register_setting('constant-contact', 'cc_uninstall_method');
	register_setting('constant-contact-registration', 'cc_lists');
	register_setting('constant-contact-registration', 'cc_exclude_lists');
	register_setting('constant-contact-registration', 'cc_signup_title');
	register_setting('constant-contact-registration', 'cc_signup_description');
	register_setting('constant-contact-registration', 'cc_extra_field_mappings');
	register_setting('constant-contact-registration', 'cc_register_page_method');
	register_setting('constant-contact-registration', 'cc_default_opt_in');
	register_setting('constant-contact-registration', 'cc_list_selection_format');
}

// show the admin settings page
function constant_contact_settings()
{
    // See if the user has posted us some information
    if(isset($_GET['updated'])):
		?>
		<div class="updated">
			<p><strong><?php _e('Your settings have been saved', 'mt_trans_domain' ); ?></strong></p>
		</div>
		<?php
    endif;

    /**
     * Fetch full list of contact lists for various purposes
     */
    $all_contact_lists = constant_contact_get_lists(true);
	
?>

	<div class="wrap">
	
		<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <?php _e('Settings', 'constant-contact-api'); ?></h2>
	<?php 	/**
	 * Show the account status message
	 */
	 
	 $cc = constant_contact_create_object(isset($_POST['cc_username']));
	 ?>
	 	
	<?php if($cc && is_object($cc)) { ?>
	<div class="widefat">
	
<?php 
		constant_contact_plugin_page_list(false);
	} ?>

	<form method="post" action="options.php">
	<?php settings_fields( 'constant-contact' ); ?>
	 <?php wp_nonce_field('constant_contact','update_cc_options'); ?>

	<h3>Account Details</h3>

	<?php
    if ($cc && is_object($cc) && get_option('cc_password') && get_option('cc_username')) {
    	
	    echo "<div id='message' class='updated'><p>".__('Your username and password seem to be working.', 'constant-contact-api')."</p></div>";
    }
	?>
	<table class="form-table widefat">
	<tr>
		<th scope="row"><p><label for="cc_username"><span><?php _e('Constant Contact Username', 'constant-contact-api'); ?></span></label></p></th>
		<td>
		<input type="text" name="cc_username" id="cc_username" value="<?php echo get_option('cc_username'); ?>" autocomplete="off" size="50" />
		</td>
	</tr>
	<tr>
		<th scope="row"><p><label for="cc_password"><span><?php _e('Constant Contact Password', 'constant-contact-api'); ?></span></label></th>
		<td>
		<input type="password" name="cc_password" id="cc_password" value="<?php echo get_option('cc_password'); ?>" autocomplete="off" size="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><p><label for="cc_uninstall_method_keep"><span><?php _e('Uninstall Method', 'constant-contact-api'); ?></span></label></th>
		<td>
		<p><label for="cc_uninstall_method_keep" class="howto"><input <?php echo (!get_option('cc_uninstall_method') || get_option('cc_uninstall_method')=='keep') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" id="cc_uninstall_method_keep" value="keep" /> <span><?php _e(sprintf('%sKeep data in database%s, will be there if re-activated', '<strong>', '</strong>'), 'constant-contact-api'); ?></span></label>
		<label for="cc_uninstall_method_remove" class="howto"><input <?php echo (get_option('cc_uninstall_method')=='remove') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" id="cc_uninstall_method_remove" value="remove" /> <span><?php _e(sprintf('%sRemove all data%s stored in database', '<strong>', '</strong>'), 'constant-contact-api'); ?></span></label></p>
		<p class="description"><?php _e('When you deactivate the plugin you can keep your username and password or remove them, if your upgrading you should keep them but if your completely removing the plugin you should remove them, no other settings will be kept.', 'constant-contact-api'); ?></p>
		</td>
	</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'constant-contact-api') ?>" />
	</p>
	</form>
	</div>
<?php
}
?>