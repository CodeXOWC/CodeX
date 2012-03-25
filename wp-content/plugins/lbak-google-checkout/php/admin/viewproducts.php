<?php

function lbakgc_admin_list_products() {
    //Check that the user is able to view this page.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'lbakgc'));
    }

    //Declare global variables.
    global $wpdb;

    //Get lbakgc options.
    $options = lbakgc_get_options();

    $query = 'SELECT * FROM `' . $options['product_table_name'] . '`
        LIMIT 20
        OFFSET ' . ((lbakgc_page_var()) - 1) * 20;

    $rows = $wpdb->get_results($query);

    $return = '<table class="widefat">
        <thead>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Image</th>
        <th>Category</th>
        <th>Price</th>
        <th>Shipping</th>
        <th>Extra Info</th>
        <th>User Input</th>
        <th>Custom Dropdown</th>
        <th>In Stock?</th>
        <th>Custom HTML?</th>
        <th>Options</th>
        </thead>';
    foreach ($rows as $row) {
        $row = lbakgc_stripslashes_product($row);
        $return .= '<tr>';
        $return .= '<td>' . $row->product_id . '</td>';
        $return .= '<td>' . $row->product_name . '</td>';
        $return .= '<td>' . $row->product_description . '</td>';
        $return .= '<td>' . lbakgc_img_thumb($row->product_image) . '</td>';
        $return .= '<td>' . $row->product_category . '</td>';
        if (is_array(unserialize($row->product_price))) {
            $return .= '<td>';
            foreach (unserialize($row->product_price) as $data) {
                $return .= $data['description'] . ' - ' . lbakgc_parse_currency($data['price']) . '<br />';
            }
            $return .= '</td>';
        } else {
            $return .= '<td>' . lbakgc_parse_currency($row->product_price, $options) . '</td>';
        }
        $return .= '<td>' . lbakgc_parse_currency($row->product_shipping, $options) . '</td>';
        $return .= '<td>' . $row->product_extra . '</td>';
        $return .= '<td>'.$row->user_input.'</td>';
        $custom_dropdown = unserialize($row->custom_dropdown);
        if ($custom_dropdown) {
            $return .= '<td><b>' . $custom_dropdown[0] . '</b><br />';
            for ($i = 1; $i < sizeof($custom_dropdown); $i++) {
                $return .= $custom_dropdown[$i].'<br />';
            }
            $return .= '</td>';
        }
        else {
            $return .= '<td></td>';
        }
        $return .= '<td>' . ($row->in_stock ? '<span style="color: green;">Yes.</span>' : '<span style="color: red;">No.</span>') . '</td>';
        $return .= '<td>' . ($row->use_custom ? '<span style="color: green;">Yes.</span>' : '<span style="color: red;">No.</span>') . '</td>';
        $return .= '<td><a href="tools.php?page=lbakgc&step=editproduct&id=' . $row->product_id . '">Edit</a> |
            <a href="tools.php?page=lbakgc&step=deleteproduct&id=' . $row->product_id . '">Delete</a> |
            <a href="javascript:displayShortcode(' . $row->product_id . ');">Get Shortcode</a></td>';
        $return .= '</tr>';
    }
    $return .= '</table>';

    $no_of_results = $wpdb->get_var('SELECT COUNT(*) FROM `' . $options['product_table_name'] . '`');

    $return .= '<br />' . lbakgc_do_pagination($no_of_results, $options);

    return $return;
}
?>

<div id="poststuff" class="ui-sortable meta-box-sortable" style="float:left; width: 100%;">
    <div class="postbox">
        <h3><?php _e('View Products', 'lbakgc'); ?></h3>
        <div class="inside">
            <?php
            echo lbakgc_admin_list_products();
            ?>
        </div>
    </div>
</div>