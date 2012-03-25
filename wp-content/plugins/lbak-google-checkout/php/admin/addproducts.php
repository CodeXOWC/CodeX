<?php
if (isset($_POST['add_product_submit'])) {
    $data = array();
    $format = array();

    $data['product_name'] = $_POST['product_name'];
    $format[] = '%s';
    $data['multiple_pricings'] = $_POST['multiple_pricings'];
    $format[] = '%d';
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
    }
    else {
        $data['custom_dropdown'] = "";
        $format[] = '%s';
    }

    $data['product_image'] = $_POST['product_image'];
    $format[] = '%s';
    $data['product_description'] = $_POST['product_description'];
    $format[] = '%s';
    $data['product_extra'] = $_POST['product_extra'];
    $format[] = '%s';
    $data['product_category'] = $_POST['product_category'];
    $format[] = '%s';
    $data['in_stock'] = $_POST['in_stock'];
    $format[] = '%d';
    $data['product_shipping'] = $_POST['product_shipping'];
    $format[] = '%s';
    $data['use_custom'] = $_POST['use_custom'];
    $format[] = '%d';
    $data['custom'] = $_POST['custom'];
    $format[] = '%s';
    $data['user_input'] = $_POST['user_input'];
    $format[] = '%s';

    if ($wpdb->insert($options['product_table_name'], $data, $format)) {
        echo '<div class="updated">Product "' . $data['product_name'] . '" added!</div>';
    }
    else {
        lbakgc_log('Failed to add product.', null, 'urgent');
        echo '<div class="error">Failed to add product "' . $data['product_name'] . '"
            to the database. Please try again. If this problem persists please
            send an email to samwho@lbak.co.uk with as much detail as you can.</div>';
    }
}
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
<div id="poststuff" class="ui-sortable meta-box-sortable" style="width: 100%; float: left;">
    <div class="postbox">
        <h3><?php _e('About this page', 'lbakgc'); ?></h3>
        <div class="inside">
            <p>
<?php _e('To add a product to your database of products
    simply fill out the form below. If you do not require
    one of the fields, feel free to leave it blank.<br /><br />
    <b>Using the Custom HTML Option:</b> If you want to
    completely customise your product box you are welcome to
    tick the "Use Custom HTML?" option. Upon doing this a box
    will appear for you to edit the HTML appearance of your product
    yourself. Doing this requires a solid working knowledge of
    the Google Checkout API, HTML and CSS. It is advised that you
    <a href="http://code.google.com/apis/checkout/developer/Google_Checkout_Shopping_Cart_Annotating_Products.html" target="_blank">
    read this</a> before using the Custom HTML option.
    You can still edit the values of the form boxes (name, description
    etc.) for the purposes of your product list in the admin
    menu but these will not be displayed when the product is
    displayed on your site.<br /><br />
    <b>Multiple Pricings: </b> If you want to have more than
    one price for a product, perhaps you offer it in different
    sizes, you can use the new multiple pricings option. Note
    that it won\'t preview unless you add at least 2 options.
    If you leave either of the Desc or Price fields blank, that
    entry will not be saved.', 'lbakgc'); ?>
            </p>
        </div>
    </div>
</div>
<div id="poststuff" class="ui-sortable meta-box-sortable" style="width: 50%; float: left;">
    <div class="postbox" id="lbakgc_settings">
        <h3><?php _e('Add a Product', 'lbakgc'); ?></h3>
        <div class="inside">
            <form action="?page=lbakgc&step=addproducts" method="post" name="add_product">
                <table class="widefat">
                    <thead>
                    <th><?php _e('Option', 'lbakgc'); ?></th>
                    <th><?php _e('Value', 'lbakgc'); ?></th>
                    </thead>
                    <tr>
                        <td>
<?php _e('Name', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_name" id="product_name"
                                   onkeyup="updateProductPreview(document.forms.add_product)"/>
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
                                      onkeyup="updateProductPreview(document.forms.add_product)"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Category', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_category" id="product_category"
                                   onkeyup="updateProductPreview(document.forms.add_product)"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Image (URL)', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_image" id="product_image"
                                   onkeyup="updateProductPreview(document.forms.add_product)" />
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
                                      onkeyup="updateProductPreview(document.forms.add_product)"></textarea>
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
                                      onkeyup="updateProductPreview(document.forms.add_product)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br />
                            <a class="button-primary"
                                    href="javascript:addDropdown('document.forms.add_product')"
                                    id="add_dropdown_button"><?php _e('Add custom dropdown', 'lbakgc'); ?></a>
                            <br />
                            <br />
                            <?php _e('<a href="#" class="lbakgc_help" title="Adding a custom
                                dropdown box gives you the ability to allow users to
                                specify an extra option such as colour or men\'s/women\'s">(What\'s this?)</a>', 'lbakgc'); ?>
                        </td>
                        <td id="custom_dropdown">

                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Use multiple pricings?', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="multiple_pricings" value="0" />
                            <input type="checkbox" name="multiple_pricings" id="multiple_pricings" value="1"
                                   onclick="toggleMultiplePricings('document.forms.add_product'), updateProductPreview(document.forms.add_product)" />
                        </td>
                    </tr>
                    <tr id="price">
                        <td>
<?php _e('Price (in ' . $options['currency'] . ')', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_price" id="product_price"
                                   onkeyup="updateProductPreview(document.forms.add_product)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Shipping (in ' . $options['currency'] . ')', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="text" name="product_shipping" id="product_shipping"
                                   onkeyup="updateProductPreview(document.forms.add_product)" />
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('In Stock?', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="in_stock" value="0" />
                            <input type="checkbox" name="in_stock" id="in_stock" value="1"
                                   onclick="updateProductPreview(document.forms.add_product)" checked/>
                        </td>
                    </tr>
                    <tr>
                        <td>
<?php _e('Use Custom HTML?', 'lbakgc'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="use_custom" value="0" />
                            <input type="checkbox" name="use_custom" id="use_custom" value="1"
                                   onclick="toggleCheckbox(document.forms.add_product, 'custom_html');"/>
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
                                      onkeyup="updateProductPreview(document.forms.add_product)"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td>
                            <input type="submit" name="add_product_submit"
                                   class="button-primary" value="<?php _e('Add', 'lbakgc'); ?>" />
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
            <p>
<?php _e('Start filling out the form to view a real-time product preview.', 'lbakgc'); ?>
            </p>
            <div id="product_preview">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    toggleCheckbox(document.forms.add_product, 'custom_html');
</script>