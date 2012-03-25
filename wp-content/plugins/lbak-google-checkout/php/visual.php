<?php
/*
 * Function to add things to the dashboard.
 */

function lbakgc_dashboard_setup() {

}

/*
 * Adding the settings menu to the admin page.
 */

function lbakgc_admin_menu() {
    $page = add_submenu_page('tools.php', 'LBAK Google Checkout Options',
                    'Google Checkout', 'manage_options', 'lbakgc', 'lbakgc_menu_options');
    add_action('admin_print_scripts-' . $page, 'lbakgc_add_scripts');
}

/*
 * Function that executes when the user clicks on the admin menu link. Put all
 * settings and such in here.
 */

function lbakgc_menu_options() {
    //Check that the user is able to view this page.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'lbakgc'));
    }

    //Declare global variables.
    global $wpdb;

    //Get lbakgc options.
    $options = lbakgc_get_options();
?>

    <div class="wrap">
        <h2>LBAK Google Checkout</h2>
        <div id="navigation">
            <a class="button-secondary" href="?page=lbakgc">Settings</a>
            <a class="button-secondary" href="?page=lbakgc&step=addproducts">Add Products</a>
            <a class="button-secondary" href="?page=lbakgc&step=viewproducts">View Products</a>
            <a class="button-secondary" href="?page=lbakgc&step=help">Help/FAQ</a>
            <a class="button-secondary" href="http://donate.lbak.co.uk/" target="_blank">Donate <3</a>
        </div>
        <br />
    <?php
    switch ($_GET['step']) {
        case 'help':
            require_once 'admin/help.php';
            break;
        case '':
        case 'settings':
            require_once 'admin/settings.php';
            break;
        case 'addproducts':
            require_once 'admin/addproducts.php';
            break;
        case 'deleteproduct':
            if ($_GET['confirm'] == 1) {
                require_once 'admin/deleteproduct.php';
                echo lbakgc_delete_product($_GET['id']);
            } else {
                echo '<div class="error" style="padding: 5px;">Are you sure you want to delete the product "' . lbakgc_get_product_name($_GET['id']) . '"?
                        <a href="?page=lbakgc&step=deleteproduct&id=' . $_GET['id'] . '&confirm=1" class="button-primary">Yes</a>
                        <a href="?page=lbakgc&step=viewproducts" class="button-secondary">No</a></div>';
                break;
            }
        case 'editproduct':
            require_once 'admin/editproduct.php';
        //No break.
        case 'viewproducts':
            require_once 'admin/viewproducts.php';
            break;
    } //end switch
    ?>
</div> <!-- CLOSE DIV CLASS WRAP -->
<?php
}

/*
 * Generates the pagination based on the amount of results and the place of
 * the table. $place is used for deciding how many rows get shown, thus how
 * many pages there are.
 */

function lbakgc_do_pagination($no_of_results, $options = null) {
    if ($options == null) {
        $options = lbakgc_get_options();
    }

    $pages = ceil($no_of_results / 20);

    if ($pages < 2) {
        return null;
    }

    $start = max(1, lbakgc_page_var() - 3);
    $end = min($pages, lbakgc_page_var() + 3);

    $result .= '<div class="lbakgc_pages">';
    $result .= 'Showing results ' . ((lbakgc_page_var() - 1) * 20 + 1) . '
        - ' . (lbakgc_page_var() * 20) . ' of ' . $no_of_results . '<br /><br />';

    $uri = preg_replace('/&lbakgc_page=[0-9]+/', '', $_SERVER['QUERY_STRING']);

    if ($start != 1) {
        if ($uri == '') {
            $page_uri = '?lbakgc_page=1';
        } else {
            $page_uri = '?' . $uri . '&lbakgc_page=1';
        }
        $result .= '<span class="lbakgc_page"><a href="' . $page_uri . '">&nbsp;1&nbsp;</a></span> ... ';
    }

    for ($page = $start; $page <= $end; $page++) {
        if ($uri == '') {
            $page_uri = '?lbakgc_page=' . $page;
        } else {
            $page_uri = '?' . $uri . '&lbakgc_page=' . $page;
        }

        if ($page == lbakgc_page_var()) {
            $result .= '<span class="lbakgc_page_selected"><a href="' . $page_uri . '">&nbsp;' . $page . '&nbsp;</a></span>';
        } else {
            $result .= '<span class="lbakgc_page"><a href="' . $page_uri . '">&nbsp;' . $page . '&nbsp;</a></span>';
        }
    }

    if ($end != $pages) {
        if ($uri == '') {
            $page_uri = '?lbakgc_page=' . $pages;
        } else {
            $page_uri = '?' . $uri . '&lbakgc_page=' . $pages;
        }
        $result .= ' ... <span class="lbakgc_page"><a href="' . $page_uri . '">&nbsp;' . $pages . '&nbsp;</a></span>';
    }

    $result .= '</div>';

    return $result;
}

/*
 * Get the variable used in pagination.
 */

function lbakgc_page_var() {
    if (!isset($_GET['lbakgc_page'])) {
        return 1;
    } else {
        return abs($_GET['lbakgc_page']);
    }
}

/*
 * Function to add the correct preceeding symbol to currency values.
 */

function lbakgc_parse_currency($number, $options = null) {
    if ($options == null) {
        $options = lbakgc_get_options();
    }
    if ($number) {
        if ($options['currency'] == 'GBP') {
            return '&#163;' . $number;
        } else if ($options['currency'] == 'USD') {
            return '&#36;' . $number;
        } else {
            return $number;
        }
    } else {
        return null;
    }
}

/*
 * Makes an image smaller based on its url. Nothing fancy, just CSS.
 */

function lbakgc_img_thumb($url, $width = 100, $height = 50, $product = false) {
    $class = $product ? 'class="product-image"' : '';
    return $url ? '<img ' . $class . ' src="' . $url . '" style="max-width: ' .
            $width . 'px; max-height=' . $height . 'px;" />' : null;
}

function lbakgc_get_product_name($product_id, $options = null) {
    global $wpdb;
    $product_id = intval($product_id);
    if ($options == null) {
        $options = lbakgc_get_options();
    }
    return $wpdb->get_var('SELECT `product_name` FROM `' . $options['product_table_name'] . '` WHERE `product_id`=' . $product_id);
}

function lbakgc_get_product_box($product_ids, $options = null, $style = null) {
    global $wpdb;
    $product_ids = intval($product_ids);
    if ($options == null) {
        $options = lbakgc_get_options();
    }

    $rows = $wpdb->get_results('SELECT * FROM `' . $options['product_table_name'] . '` WHERE `product_id` IN (' . $product_ids . ')');
    foreach ($rows as $row) {
        $return .= lbakgc_get_product_box_no_query($row, $options, $style);
    }
    return $return;
}

function lbakgc_get_product_box_no_query($row, $options, $style = null) {
    $row = lbakgc_stripslashes_product($row);
    if ($row->use_custom) {
        if ($row->in_stock) {
            if ($style != null) {
                $row->custom = preg_replace('/\<div class=\"product\">/i', '<div class="product" style="' . $style . '">', $row->custom);
            }
            return $row->custom;
        } else {
            return '';
        }
    } else {
        $image = $row->product_image ? '<img class="product-image" src="' . $row->product_image . '" />' : '';
        $name = $row->product_name ? '<div class="product_attribute"><span class="product_title"><span class="product-title">' . $row->product_name . '</span></span></div>' : '';
        $category = $row->product_category ? '<div class="product_attribute"><b>Category:</b> ' . $row->product_category . '</div>' : '';
        if (is_array(unserialize($row->product_price))) {
            $row->product_price = unserialize($row->product_price);
            $price = '<div class="product_attribute"><div style="display: none;" class="product-price">' . $row->product_price[0]['price'] . '</div><select class="product-attr-selection">';
            foreach ($row->product_price as $data) {
                $price .= '<option googlecart-set-product-price="' . $data['price'] . '">
                    ' . $data['description'] . ' - ' . lbakgc_parse_currency($data['price']) . '</option>';
            }
            $price .= '</select></div>';
        } else {
            $price = $row->product_price ? '<div class="product_attribute"><b>Price:</b> <span class="product-price">' . lbakgc_parse_currency($row->product_price, $options) . '</span></div>' : '';
        }

        if ($row->custom_dropdown) {
            $custom_dropdown_array = unserialize($row->custom_dropdown);
            $custom_dropdown = '<div class="product_attribute"><b>'.$custom_dropdown_array[0].':</b> <select class="product-attr-'.(str_replace(" ", "-", strtolower($custom_dropdown_array[0]))).'">';
            for ($i = 1; $i < sizeof($custom_dropdown_array); $i++) {
                $custom_dropdown .= '<option>'.$custom_dropdown_array[$i].'</option>';
            }
            $custom_dropdown .= '</select></div>';
        }
        else {
            $custom_dropdown = "";
        }
        $shipping = $row->product_shipping ? '<div class="product_attribute"><b>Shipping:</b> <span class="product-shipping">' . lbakgc_parse_currency($row->product_shipping, $options) . '</span></div>' : '';
        $description = $row->product_description ? '<div class="product_attribute"><b>Description:</b> <span class="product-attr-description">' . nl2br($row->product_description) . '</span></div>' : '';
        $extra = $row->product_extra ? '<div class="product_attribute"><b>Extra Info:</b> <span class="product-attr-extra">' . nl2br($row->product_extra) . '</span></div>' : '';
        $user_input = $row->user_input ? '<div class="product_attribute"><b>'.$row->user_input.':</b> <input type="text" class="product-attr-'.(str_replace(" ", "-", strtolower($row->user_input))).'" /></div>' : '';
        $button = '<div role="button" alt="Add to cart" tabindex="0" class="googlecart-add-button"></div>';
        if ($style != null) {
            $style = ' style="' . $style . '"';
        }
        if ($row->in_stock) {
            $return .= '
                <div class="product"' . $style . '>
                    ' . $image . '
                    <div class="product_info">
                        ' . $name . '
                        ' . $category . '
                        ' . $price . '
                        ' . $shipping . '
                        ' . $description . '
                        ' . $extra . '
                        ' . $user_input . '
                        ' . $custom_dropdown . '
                        ' . $button . '
                    </div>
                </div>
            ';
        } else {
            $return .= '
                <div class="product not_in_stock"' . $style . '>
                    ' . $image . '
                    <div class="product_info">
                        <b>NOT IN STOCK</b>
                        ' . $name . '
                        ' . $category . '
                        ' . $price . '
                        ' . $shipping . '
                        ' . $description . '
                        ' . $extra . '
                        ' . $user_input . '
                        ' . $custom_dropdown . '
                    </div>
                </div>
            ';
        }

        return $return;
    }
}

function lbakgc_stripslashes_product($row) {
    if (is_object($row)) {
        $row->product_name = stripslashes($row->product_name);
        $row->product_description = stripslashes($row->product_description);
        $row->product_extra = stripslashes($row->product_extra);
        $row->product_category = stripslashes($row->product_category);
        $row->custom = stripslashes($row->custom);
        $row->user_input = stripslashes($row->user_input);
        return $row;
    } else {
        return false;
    }
}
?>
