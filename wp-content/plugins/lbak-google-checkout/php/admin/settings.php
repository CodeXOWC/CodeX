<?php
if (isset($_POST['settings_submit'])) {
    $options['merchant_id'] = $wpdb->escape($_POST['merchant_id']);
    $options['currency'] = $wpdb->escape($_POST['currency']);
    $options['sandbox'] = $wpdb->escape($_POST['sandbox']);
    $options['sandbox_merchant_id'] = $wpdb->escape($_POST['sandbox_merchant_id']);
    $options['highlight-time'] = $wpdb->escape($_POST['highlight-time']);
    $options['highlight-color'] = $wpdb->escape($_POST['highlight-color']);
    $options['cart-opening-time'] = intval($_POST['cart-opening-time']);
    $options['hide-cart-when-empty'] = $wpdb->escape($_POST['hide-cart-when-empty']);
    $options['close-cart-when-click-away'] = $wpdb->escape($_POST['close-cart-when-click-away']);
    $options['image-width'] = $wpdb->escape($_POST['image-width']);
    $options['image-height'] = $wpdb->escape($_POST['image-height']);
    $options['product-width'] = $wpdb->escape($_POST['product-width']);
    $options['product-height'] = $wpdb->escape($_POST['product-height']);
    $options['title-colour'] = $wpdb->escape($_POST['title-colour']);
    $options['log'] = $wpdb->escape($_POST['log']);

    lbakgc_update_options($options);
    echo '<div class="updated">LBAK Google Checkout settings updated!
        Note that changes to the cart will not take effect on this
        current page as the cart was loaded before the options were
        updated. Please refresh the page or navigate to another
        page to see the changes.</div>';
}
if ($options['sandbox']) {
    $sandbox_checked = 'checked';
}
?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="lbakgc_settings">
        <h3><?php _e('LBAK Google Checkout Settings', 'lbakgc'); ?></h3>
        <div class="inside">
            <form action="?page=lbakgc" method="post">
                <table class="widefat">
                    <thead>
                    <th><?php _e('Setting', 'lbakgc'); ?></th>
                    <th><?php _e('Options', 'lbakgc'); ?></th>
                    <th><?php _e('Description', 'lbakgc'); ?></th>
                    </thead>
                    <tr>
                        <td>
                            <label for="sandbox">
                                <b><?php _e('Sandbox Mode', 'lbakgc'); ?></b>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="sandbox" value="0" />
                            <input type="checkbox" name="sandbox" id="sandbox"
                                   <?php echo $sandbox_checked; ?> />
                        </td>
                        <td>
                            <p>
                                <?php _e('Check this to use Google Checkout\'s
                                        Sandbox mode. This allows you to test out
                                        your Google Checkout setup. Click
                                        <a href="http://code.google.com/apis/checkout/developer/Google_Checkout_Basic_HTML_Sandbox.html">here</a> for more info.
                                        If you are uncertain then I would recommend
                                        either thoroughly reading the previous link, asking
                                        someone who knows how to use Google Checkout
                                        sandbox to help you or jusst leave it off.', 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <b><?php _e('Send usage data?', 'lbakgc'); ?></b>
                           </td>
                           <td>
                               <input type="hidden" name="log"
                                      value="0" />
                               <input type="checkbox"
                                      name="log" value="1" <?php echo $options['log'] ? 'checked' : ''; ?> />
                           </td>
                           <td>
                               <p>
                                <?php _e('If this is checked you will be opted
                                    in to sending usage statistics and error
                                    reports to the LBAK server for the developers
                                    to use to help improve this plugin. If it
                                    is not checked, you will not send data to
                                    our servers. For more information on this,
                                    please see the FAQ.', 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="sandbox_merchant_id">
                                   <b><?php _e('Sandbox Merchant ID', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="sandbox_merchant_id" id="sandbox_merchant_id"
                                      value="<?php echo $options['sandbox_merchant_id']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This is the Merchant ID for your merchant
                                    sandbox account. Use this only in conjunction with
                                    sandbox mode. If you do not use sandbox mode at all
                                    you can leave this blank.', 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="merchant_id">
                                   <b><?php _e('Merchant ID', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="merchant_id" id="merchant_id"
                                      value="<?php echo $options['merchant_id']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('Your Merchant ID can be found
                                    in Google Checkout\'s Settings tab under the
                                    "Preferences" heading on the left. It\'s a
                                    long number, ~15 digits.', 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="currency">
                                   <b><?php _e('Currency', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="currency" id="currency"
                                      value="<?php echo $options['currency']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This is the currency in which you want
                                    to take payments. This needs to correspond
                                    with the currency setting in your Google
                                    checkout account. If you are based in the UK
                                    it will be "GBP" and if you are in the US
                                    it will be "USD" (both without quotes).'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="highlight-time">
                                   <b><?php _e('Cart Highlight Time', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="highlight-time" id="highlight-time"
                                      value="<?php echo $options['highlight-time']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This is the time (in miliseconds)
                                    that an item should be highlighted for after
                                    being added to the cart.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="highlight-color">
                                   <b><?php _e('Cart Highlight Color', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="highlight-color" id="highlight-color"
                                      value="<?php echo $options['highlight-color']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This is the color that items added to the cart
                                    will be highlighted in.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="cart-opening-time">
                                   <b><?php _e('Cart Opening Time', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="cart-opening-time" id="cart-opening-time"
                                      value="<?php echo $options['cart-opening-time']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This is the time (in miliseconds)
                                    that it will take the cart to expand and
                                    collapse.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="hide-cart-when-empty">
                                   <b><?php _e('Hide Cart When Empty?', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="hide-cart-when-empty" id="hide-cart-when-empty"
                                      value="<?php echo $options['hide-cart-when-empty']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This variable specifies whether or not the
                                    cart will show up when there are no items in it.
                                    Please set to either "true" or "false".'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="close-cart-when-click-away">
                                   <b><?php _e('Close Cart When Click Away?', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="close-cart-when-click-away" id="close-cart-when-click-away"
                                      value="<?php echo $options['close-cart-when-click-away']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('This variable specifies whether or not the
                                    cart will disappear when you click off it.
                                    Please set to either "true" or "false".'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="image-width">
                                   <b><?php _e('Image width', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="image-width" id="image-width"
                                      value="<?php echo $options['image-width']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('The max width to use for
                                    images displayed in products.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="image-height">
                                   <b><?php _e('Image height', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="image-height" id="image-height"
                                      value="<?php echo $options['image-height']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('The max height to use for
                                    images displayed in products.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="product-width">
                                   <b><?php _e('Product width', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="product-width" id="product-width"
                                      value="<?php echo $options['product-width']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('The max width to use for
                                    products displayed on your site.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="product-height">
                                   <b><?php _e('Product height', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="product-height" id="product-height"
                                      value="<?php echo $options['product-height']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('The max height to use for
                                    products displayed on your site.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="title-colour">
                                   <b><?php _e('Product name colour', 'lbakgc'); ?></b>
                               </label>
                           </td>
                           <td>
                               <input type="text" name="title-colour" id="title-colour"
                                      value="<?php echo $options['title-colour']; ?>" />
                           </td>
                           <td>
                               <p>
                                <?php _e('The colour of the title text when
                                    displaying your products.'
                                           , 'lbakgc'); ?>
                               </p>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="settings_submit">
                                   <b>Submit</b>
                               </label>
                           </td>
                           <td>
                               <input type="submit" name="settings_submit" id="settings_submit"
                                      value="Submit" class="button-primary" />
                           </td>
                           <td>
                               <p>
                                <?php _e('Save these options.', 'lbakgc'); ?>
                            </p>
                        </td>
                    </tr>
                    <thead>
                    <th>Setting</th>
                    <th>Options</th>
                    <th>Description</th>
                    </thead>
                </table>
            </form>
        </div>
    </div>
</div>