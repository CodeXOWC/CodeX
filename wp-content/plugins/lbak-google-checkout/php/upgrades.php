<?php
/*
 * This file's purpose is to hold all of the functions that are designed as
 * one off fixes in version upgrades. Introduced in v1.3.
 */

/*
 * This function is designed to run a callback function is the user is
 * upgrading to the specified version and only if they are upgrading, never
 * if they are just reactivating after deactivating.
 *
 * For this to work you need to call it in the lbakgc_activation_setup()
 * function in /php/housekeeping.php just after the current options
 * are drawn from the database. It WILL NOT WORK anywhere else.
 */
function lbakgc_upgrade_fix($version, $callback) {
    ///Get the options and check if they existed. Return false if they didn't.
    $options = lbakgc_get_options();
    if (!$options) {
        return false;
    }

    /*
     * If the current stored version number is not equal to the version passed
     * and the version in the lbakgc_get_verion() function IS equal to the
     * passed verion number then execute the callback. Otherwise return false.
     *
     * This method ensures that, provided the lbakgc_get_verion() function gets
     * updated properly, this function will ONLY run on update activations.
     */

    if ($options['version'] != $version && lbakgc_get_version() == $version) {
        return call_user_func($callback);
    }
    else {
        return false;
    }
}
?>
