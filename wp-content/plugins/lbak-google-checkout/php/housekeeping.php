<?php
function lbakgc_activation_setup() {
    //Get database object.
    global $wpdb;

    //Check for current options.
    $cur_options = lbakgc_get_options();


    //If the plugin is already installed.
    if ($cur_options) {
        $options_exist = true;
    }
    else {
        $options_exist = false;
    }

    //Declare table name.
    $product_table_name = $wpdb->prefix . "lbakgc_product_table";

    //Check table is not in use.
    if($wpdb->get_var("SHOW TABLES LIKE '$product_table_name'") != $product_table_name) {
        $db_product_table_exists = true;
    }
    else {
        $db_product_table_exists = false;
    }

    $create_product_table_sql = "
        CREATE TABLE $product_table_name (
            product_id int NOT NULL AUTO_INCREMENT,
            product_name text NOT NULL,
            product_price text NOT NULL,
            product_image text NOT NULL,
            product_description text NOT NULL,
            product_extra text NOT NULL,
            product_shipping text NOT NULL,
            product_category text NOT NULL,
            user_input text NOT NULL,
            multiple_pricings tinyint NOT NULL,
            in_stock tinyint NOT NULL,
            use_custom tinyint NOT NULL,
            custom text NOT NULL,
            custom_dropdown text NOT NULL,
            use_color tinyint NOT NULL,
            product_color text NOT NULL,
            PRIMARY KEY (product_id),
            KEY category (product_category(12)),
            KEY price (product_price(4))
        );
            ";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    if (function_exists('dbDelta')) {
        dbDelta($create_product_table_sql);
    }
    else {
        lbakgc_log('No dbDelta function detected.', __FILE__.':'.__LINE__, 'urgent');
        if ($db_product_table_exists) {
            //This is a temporary solution to the dbDelta function not existing.
            $wpdb->query("DROP TABLE $product_table_name");
        }
        $wpdb->query($create_product_table_sql);
    }

    $option_default = array(
        'version' => lbakgc_get_version(),
        'product_table_name' => $product_table_name,
        'productWeightUnits' => 'KG', //Google has this for some reason
        'merchant_id' => 'unset',
        'sandbox' => '',
        'sandbox_merchant_id' => 'unset',
        'currency' => 'USD',
        'highlight-time' => '5000',
        'highlight-color' => '#FF7878',
        'cart-opening-time' => '500',
        'hide-cart-when-empty' => 'true',
        'close-cart-when-click-away' => 'false',
        'image-width' => '200px',
        'image-height' => '200px',
        'product-width' => '100%',
        'product-height' => '100%',
        'title-colour' => '#ff8800',
        'log' => true
    );

    //BEGIN OPTION INITIALISATION LOOP
    $options = array();
    foreach ($option_default as $k => $v) {
        if ($options_exist) {
            //if the options are already set
            if (!isset($cur_options[$k])) {
                //Check if the option the loop is on exists
                $options[$k] = $v;
                //Set it to a default value if it doesn't
            }
            else {
                //set it to the old value if it does already exist
                $options[$k] = $cur_options[$k];
            }
        }
        else {
            //if option s aren't set
            $options[$k] = $v;
        }
    }
    //END OPTION INITIALISATION LOOP

    //Some options housekeeping...
    $options['version'] = $option_default['version'];

    if ($options_exist) {
        lbakgc_delete_options();
    }

    //Add AND update to account for the plugin already being there.
    add_option('lbakgc_options', null, null, 'no');
    lbakgc_update_options($options);
    lbakgc_log('Plugin activated.');
}

function lbakgc_uninstall() {
    global $wpdb;

    $options = lbakgc_get_options();

    if ($options['product_table_name']) {
        //Drop the product table.
        $wpdb->query('DROP TABLE `' . $options['product_table_name'] . '`');
    }
    else {
        //Table name option not defined... No idea why this would happen
        //but it can't hurt to have it checked :p
        trigger_error(__("Could not delete LBAK Google Checkout database.", 'lbakgc'), E_USER_ERROR);

    }

    //Erase options field in the settings table.
    lbakgc_delete_options();
    lbakgc_log('Plugin uninstalled.');
}

function lbakgc_deactivate() {
    lbakgc_log('Plugin deactivated.');
}

/*
 * Functions to get, delete and update the lbakgc options.
 */
function lbakgc_get_options() {
    global $lbakgc_options;
    if (!isset($lbakgc_options)) {
        $lbakgc_options = get_option('lbakgc_options');
    }
    return $lbakgc_options;
}
function lbakgc_update_options($options) {
    global $lbakgc_options;
    update_option('lbakgc_options', $options);
    $lbakgc_options = $options;
    return $lbakgc_options;
}
function lbakgc_delete_options() {
    global $lbakgc_options;
    unset($lbakgc_options);
    return delete_option('lbakgc_options');
}
?>
