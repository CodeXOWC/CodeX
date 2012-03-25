<?php
function lbakgc_delete_product($product_id) {
    //Check that the user is able to view this page.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'lbakgc'));
    }

    global $wpdb;
    $product_id = intval($product_id);
    $options = lbakgc_get_options();

    $product_name = lbakgc_get_product_name($product_id, $options);
    if ($wpdb->query('DELETE FROM `' . $options['product_table_name'] . '` WHERE `product_id`=' . $product_id)) {
        return '<div class="updated">Deleted product "' . $product_name . '".</div>';
    }
    else {
        lbakgc_log('Failed to delete product.', null, 'urgent');
        return '<div class="error">Failed to delete product "' . $product_name . '".
            Please try again. If this problem persists, please send an email
            to samwho@lbak.co.uk with as much information as you can.</div>';
    }
}
?>
