<?php
if ($_GET['step'] != 'deleteproduct') {
    lbakgc_edit_product($_GET['id']);
}

/*
 * This function is currently really messy but it handles everything to do
 * with editing products.
 */

function lbakgc_edit_product($product_id) {
    //Check that the user is able to view this page.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.',
                        'lbakgc'));
    }

    global $wpdb;
    $options = lbakgc_get_options();
    $product_id = intval($product_id);

    if (isset($_POST['edit_product_submit'])) {
        $data = array();
        $format = array();
        $where = array();
        $where_format = array();

        $data['product_name'] = $_POST['product_name'];
        $format[] = '%s';
        if (is_array($_POST['product_price'])) {
            $counter = 0;
            foreach ($_POST['product_price'] as $price) {
                $data['product_price'][$counter]['price'] = $price;
                $counter++;
            }
            $counter = 0;
            foreach ($_POST['product_price_desc'] as $description) {
                $data['product_price'][$counter]['description'] = $description;
                $counter++;
            }
            foreach ($data['product_price'] as $k => $item) {
                if ($item['price'] && $item['description']) {

                } else {
                    unset($data['product_price'][$k]);
                }
            }
            $data['product_price'] = serialize($data['product_price']);
            $format[] = '%s';
        } else {
            $data['product_price'] = $_POST['product_price'];
            $format[] = '%s';
        }

        if ($_POST['custom_dropdown_name']) {
            $data['custom_dropdown'][] = $_POST['custom_dropdown_name'];
            foreach ($_POST['custom_dropdown_option'] as $v) {
                if ($v) {
                    $data['custom_dropdown'][] = $v;
                }
            }
            $data['custom_dropdown'] = serialize($data['custom_dropdown']);
            $format[] = '%s';
        } else {
            $data['custom_dropdown'] = "";
            $format[] = '%s';
        }

        $data['product_shipping'] = $_POST['product_shipping'];
        $format[] = '%s';
        $data['product_extra'] = $_POST['product_extra'];
        $format[] = '%s';
        $data['in_stock'] = $_POST['in_stock'];
        $format[] = '%d';
        $data['product_description'] = $_POST['product_description'];
        $format[] = '%s';
        $data['product_image'] = $_POST['product_image'];
        $format[] = '%s';
        $data['product_category'] = $_POST['product_category'];
        $format[] = '%s';
        $data['use_custom'] = $_POST['use_custom'];
        $format[] = '%d';
        $data['custom'] = $_POST['custom'];
        $format[] = '%s';
        $data['user_input'] = $_POST['user_input'];
        $format[] = '%s';

        $where['product_id'] = $product_id;
        $where_format[] = '%d';

        if ($wpdb->update($options['product_table_name'], $data, $where, $format, $where_format)) {
            echo '<div class="updated">Updated product "' . $_POST['product_name'] . '"!</div>';
        } else {
            lbakgc_log('Failed to edit product.', null, 'urgent');
            echo '<div class="error">Failed to update product "' . $data['product_name'] . '"
            to the database. Please try again. If this problem persists please
            send an email to samwho@lbak.co.uk with as much detail as you can. Note:
            This message also displays if you hit "Edit" without making any changes.</div>';
        }
    } else {
        $row = lbakgc_stripslashes_product($wpdb->get_row('SELECT * FROM `' . $options['product_table_name'] . '` WHERE `product_id`=' . $product_id));
?>
        <noscript>
            <div class="error">
<?php _e('You are viewing this page without Javascript enabled. This page relies
        heavily on the use of Javascript to function. Attempting to use this
        page without Javascript may have unexpected results and is generally
        not encouraged. Please switch to a browser that has Javascript enabled
        or enable Javascript in this browser to enjoy full functionality on
        this page.', 'lbakgc'); ?>
    </div>
</noscript>
<div id="poststuff" class="ui-sortable meta-box-sortable" style="width: 50%; float: left;">
    <div class="postbox">
        <h3><?php _e('Edit a Product', 'lbakgc'); ?></h3>
        <div class="inside">
            <form action="?page=lbakgc&step=editproduct&id=<?php echo $product_id; ?>" method="post" name="edit_product">
                <table class="widefat">
                    <thead>
                    <th>Option</th>
                    <th>Value</th>
                    </thead>
                    <tr>
                        <td>
<?php _e('Name', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_name" id="product_name"
                                   value="<?php echo $row->product_name; ?>" onkeyup="updateProductPreview(document.forms.edit_product)"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Description', 'lbakgc'); ?>
                        </td>
                        <td>
                            <textarea name="product_description"
                                      id="product_description"
                                      cols="20"
                                      rows="3"
                                      onkeyup="updateProductPreview(document.forms.edit_product)"><?php echo $row->product_description; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Category', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_category" id="product_category"
                                   value="<?php echo $row->product_category; ?>" onkeyup="updateProductPreview(document.forms.edit_product)"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Image (URL)', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_image" id="product_image"
                                   value="<?php echo $row->product_image; ?>" onkeyup="updateProductPreview(document.forms.edit_product)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Extra Info', 'lbakgc'); ?>
                        </td>
                        <td>
                            <textarea name="product_extra"
                                      id="product_extra"
                                      cols="20"
                                      rows="3"
                                      onkeyup="updateProductPreview(document.forms.edit_product)"><?php echo $row->product_extra; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('User Input <a href="#" class="lbakgc_help" title="The user input field is intended to
    allow your users to add in something personal to their purchase. This is a
    good field to use for engravings or personal greetings, for example. If you
    don\'t think you need this field, you can leave it blank.">(What\'s this?)</a>', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="user_input"
                                   id="user_input"
                                   onkeyup="updateProductPreview(document.forms.edit_product)"
                                   value="<?php echo $row->user_input; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <?php if (!$row->custom_dropdown) { ?>
                        <td>
                            <br />
                            <a class="button-primary"
                               href="javascript:addDropdown('document.forms.edit_product')"
                               id="add_dropdown_button"><?php _e('Add custom dropdown', 'lbakgc'); ?></a>
                            <br />
                            <br />
<?php _e('<a href="#" class="lbakgc_help" title="Adding a custom
                                dropdown box gives you the ability to allow users to
                                specify an extra option such as colour or men\'s/women\'s">(What\'s this?)</a>', 'lbakgc'); ?>
                        </td>
                        <td id="custom_dropdown">

                        </td>
                        <?php
                        }
                        else {
                            $custom_dropdown = unserialize($row->custom_dropdown);
                            ?>
                            <td>
                            <br />
                            <a class="button-primary"
                               href="javascript:removeDropdown('document.forms.edit_product')"
                               id="add_dropdown_button"><?php _e('Remove custom dropdown', 'lbakgc'); ?></a>
                            <br />
                            <br />
<?php _e('<a href="#" class="lbakgc_help" title="Adding a custom
                                dropdown box gives you the ability to allow users to
                                specify an extra option such as colour or men\'s/women\'s">(What\'s this?)</a>', 'lbakgc'); ?>
                            </td>
                            <td>
                                <div id="custom_dropdown">
                            <?php
                            _e('Title<br /><input type="text" name="custom_dropdown_name"
                                value="'.$custom_dropdown[0].'" /><br />Options<br />
                                    <div id="custom_dropdown_options">', 'lbakgc');
                            for ($i = 1; $i < sizeof($custom_dropdown); $i++) {
                                echo '<input type="text" name="custom_dropdown_option[]"
                                    value="'.$custom_dropdown[$i].'" /><br />';
                            }
                            echo "</div><br /><a class='button-primary'
                                href='javascript:addDropdownOption(\"document.forms.edit_product\")'>
                                Add option</a><br /><br />";
                            ?>
                                </div>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>
<?php _e('Use multiple pricings?', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="multiple_pricings" value="0" />
                            <input type="checkbox" name="multiple_pricings" id="multiple_pricings" value="1"
                                   onclick="toggleMultiplePricings('document.forms.edit_product'), updateProductPreview(document.forms.edit_product)"
<?php echo $row->multiple_pricings ? 'checked' : '' ?>/>
                        </td>
                    </tr>
                    <tr id="price">
                        <td>
<?php _e('Price (in ' . $options['currency'] . ')', 'lbakgc'); ?>
                        </td>
                        <td>
<?php
                            if (is_array(unserialize($row->product_price))) {
                                echo '<div id="prices">';
                                foreach (unserialize($row->product_price) as $data) {
                                    echo '<div>Desc: <input type="text" name="product_price_desc[]"
                                            id="desc' . $count . '" value="' . $data['description'] . '" />
                                                Price: <input type="text" name="product_price[]"
                                            id="price' . $count . '" value="' . $data['price'] . '" /></div>';
                                }
                                echo '</div><br /><a href="javascript:addPriceOption(\'document.forms.edit_product\');" class="button-primary">Add option</a><br /><br />';
                            } else {
?>
                                <input type="text" name="product_price" id="product_price"
                                       value="<?php echo $row->product_price; ?>" onkeyup="updateProductPreview(document.forms.edit_product)" />
<?php
                            }
?>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Shipping (in ' . $options['currency'] . ')', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_shipping" id="product_shipping"
                                   value="<?php echo $row->product_shipping; ?>" onkeyup="updateProductPreview(document.forms.edit_product)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('In Stock?', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="in_stock" value="0" />
                            <input type="checkbox" name="in_stock" id="in_stock" value="1"
                                   onclick="updateProductPreview(document.forms.edit_product)" <?php echo $row->in_stock ? 'checked' : ''; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Use Custom HTML?', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="use_custom" value="0" />
                            <input type="checkbox" name="use_custom" id="use_custom" value="1"
                                   onclick="toggleCheckbox(document.forms.edit_product, 'custom_html');"
<?php echo $row->use_custom ? 'checked' : ''; ?>/>
                        </td>
                    </tr>
                    <tr id="custom_html" style="display: none;">
                        <td>
<?php _e('Custom HTML', 'lbakgc'); ?>
                        </td>
                        <td>
                            <textarea name="custom"
                                      id="custom"
                                      cols="40"
                                      rows="7"
                                      onkeyup="updateProductPreview(document.forms.edit_product)"><?php echo stripslashes($row->custom); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td>
                            <input type="submit" name="edit_product_submit"
                                   class="button-primary" value="Edit" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div id="poststuff" class="ui-sortable meta-box-sortable" style="width: auto; max-width: 49%; float: left;">
    <div class="postbox" id="lbakgc_settings">
        <h3><?php _e('Product Preview', 'lbakgc'); ?></h3>
        <div class="inside">
            <div id="product_preview">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    toggleCheckbox(document.forms.edit_product, 'custom_html');
</script>
<?php
                        }
                    }
?>
