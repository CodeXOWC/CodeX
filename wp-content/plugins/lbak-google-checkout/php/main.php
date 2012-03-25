<?php
function lbakgc_add_header() {
    //Fetch options from the wordpress database.
    $options = lbakgc_get_options();

    if ($options['sandbox'] != false && $options['sandbox_merchant_id'] != 'unset') {
        echo '<script id="googlecart-script" type="text/javascript"
                  src="http://checkout.google.com/seller/gsc/v2/cart.js?mid='.$options['sandbox_merchant_id'].'"
                  currency="'.$options['currency'].'"
                  highlight-time="'.$options['highlight-time'].'"
                  highlight-color="'.$options['highlight-color'].'"
                  cart-opening-time="'.$options['cart-opening-time'].'"
                  hide-cart-when-empty="'.$options['hide-cart-when-empty'].'"
                  close-cart-when-click-away="'.$options['close-cart-when-click-away'].'"
                  post-cart-to-sandbox="true">
              </script>';
    }
    else if ($options['merchant_id'] != 'unset') {
        echo '<script id="googlecart-script" type="text/javascript"
                  src="http://checkout.google.com/seller/gsc/v2/cart.js?mid='.$options['merchant_id'].'"
                  currency="'.$options['currency'].'"
                  highlight-time="'.$options['highlight-time'].'"
                  highlight-color="'.$options['highlight-color'].'"
                  cart-opening-time="'.$options['cart-opening-time'].'"
                  hide-cart-when-empty="'.$options['hide-cart-when-empty'].'"
                  close-cart-when-click-away="'.$options['close-cart-when-click-away'].'"
                  post-cart-to-sandbox="false">
              </script>';
    }

    echo '<link rel="stylesheet" type="text/css"
        href="'.lbakgc_get_base_url().'/css/googlecheckout.php?iw='.urlencode($options['image-width']).'
            &ih='.urlencode($options['image-height']).'&pw='.urlencode($options['product-width']).'&ph='.urlencode($options['product-height']).'
                &tc='.urlencode($options['title-colour']).'" />';
    echo '<link rel="stylesheet" type="text/css" href="'.lbakgc_get_base_url().'/css/wp_head.css" />';
}

function lbakgc_add_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('lbakgc_admin_script', lbakgc_get_base_url() . '/js/admin_page.js');
    wp_enqueue_script('jquery-tooltip', 'http://cdn.jquerytools.org/1.2.4/jquery.tools.min.js');
}

/*
 * Replaces the [checkout] short codes with their appropriate form code.
*/
function lbakgc_parse_shortcode($the_content) {
    global $wpdb;
    $options = lbakgc_get_options();

    $regex_pattern = '/\[checkout(?:.*)\]/i';
    $regex_pattern_grouped = '/\[checkout(.*)\]/i';
    $matches = array();

    //Match all instances of [checkout] and store them in $matches.
    preg_match_all($regex_pattern, $the_content, $matches);

    //If there are matches, execute following loop.
    if (sizeof($matches[0]) > 0) {
        //For each of the matches (it's $matches[0] because of how preg_match_all works)
        foreach ($matches[0] as $match) {

            //Get the arguments for the checkout tag as an associative array.
            $args = lbakgc_process_shortcode(preg_replace($regex_pattern_grouped, '$1', $match));

            if (isset($args['price'])) {
                $price = '';
                foreach (explode(',', $args['price']) as $value) {
                    $value = str_replace('£', '', $value);
                    $value = str_replace('$', '', $value);
                    $value = trim($value);
                    if (substr($value, 0, 1) == '>') {
                        $price .= 'AND `product_price`>'.substr($value, 1).' ';
                    }
                    else if (substr($value, 0, 1) == '<') {
                        $price .= 'AND `product_price`<'.substr($value, 1).' ';
                    }
                    else if (substr($value, 0, 2) == '>=') {
                        $price .= 'AND `product_price`>='.substr($value, 2).' ';
                    }
                    else if (substr($value, 0, 2) == '<=') {
                        $price .= 'AND `product_price`<='.substr($value, 2).' ';
                    }
                    else {
                        if (is_array(($cost = explode('-', $value)))) {
                            $price .= 'AND `product_price` BETWEEN
                                '.$cost[0].' AND '.$cost[1].' ';
                        }
                        else {
                            $price .= 'AND `product_price`='.$value.' ';
                        }
                    }
                }
            }
            else {
                $price = 'AND 1 ';
            }
            $price = $wpdb->escape($price);

            if (isset($args['product'])) {
                if ($args['product'] == 'all') {
                    $products = $wpdb->get_results('SELECT * FROM `'.$options['product_table_name'].'`
                        WHERE 1 '.$price);
                }
                else {
                    $products = $wpdb->get_results('SELECT * FROM `'.$options['product_table_name'].'`
                        WHERE 1 AND `product_id` IN ('.$wpdb->escape($args['product']).') '.$price);
                }
                $replace = '';
                foreach ($products as $product) {
                    $replace .= lbakgc_get_product_box_no_query($product, $options, $args['style']);
                }
            }
            if (isset($args['category'])) {
                //Make sure all the category arguments are valid MySQL strings.
                $temp = array();
                foreach (explode(',', $args['category']) as $category) {
                    $temp[] = "'".$wpdb->escape(trim($category))."'";
                }
                $args['category'] = implode(', ', $temp);

                //Get the rows from the database.
                $products = $wpdb->get_results('SELECT * FROM `'.$options['product_table_name'].'`
                    WHERE 1 AND `product_category` IN ('.$args['category'].') '.$price);
                $replace = '';
                foreach ($products as $product) {
                    $replace .= lbakgc_get_product_box_no_query($product, $options, $args['style']);
                }
            }

            //Replace the content with the appropriate code.
            $the_content = preg_replace($regex_pattern, $replace, $the_content, 1);
        }
    }

    //Return the content. NECESSARY.
    return $the_content;
}

/*
 * This function turns the arguments of a [checkout] tag into an associative
 * array.
*/
function lbakgc_process_shortcode($shortcode) {
    $split = preg_split('/"(\ |^)/i', $shortcode);
    $return = array();
    for ($i = 0; $i < sizeof($split); $i++) {
        $kvpair = explode("=", $split[$i]);
        $return[trim($kvpair[0])] = str_replace('"', '', $kvpair[1]);
    }
    return $return;
}

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.
 *
 * Courtesy of:
 * http://nadeausoftware.com/articles/2007/06/php_tip_how_get_web_page_using_curl
 */
function lbakgc_get_web_page( $url ) {
    if (lbakgc_get_allow_url_fopen()) {
        return file_get_contents($url);
    }
    else if (lbakgc_get_curl()) {
        $options = array(
                CURLOPT_RETURNTRANSFER => true,     // return web page
                CURLOPT_HEADER         => false,    // don't return headers
                CURLOPT_FOLLOWLOCATION => true,     // follow redirects
                CURLOPT_ENCODING       => "",       // handle all encodings
                CURLOPT_USERAGENT      => "spider", // who am i
                CURLOPT_AUTOREFERER    => true,     // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                CURLOPT_TIMEOUT        => 120,      // timeout on response
                CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        if ($err != 0) {
            return false;
        }

        return $content;
    }
    else {
        return false;
    }
}

function lbakgc_get_curl() {
    if  (in_array('curl', get_loaded_extensions())) {
        return true;
    }
    else {
        return false;
    }
}
function lbakgc_get_allow_url_fopen() {
    $allow_url_fopen = ini_get("allow_url_fopen");
    if ($allow_url_fopen != "" && $allow_url_fopen != null) {
        return true;
    }
    else {
        return false;
    }
}

function lbakgc_log($message, $origin = null, $type = "message", $override = false) {
    // used to store logging info, since deprecated
}

/*
 * Formats the post variables like a query string (the get variables).
 */
function lbakgc_get_post_vars() {
    $postvars = "";
    foreach ($_POST as $v => $p) {
        if (is_array($p))
            $p = join(",", $p);
        $postvars .= "$v=$p&";
    }
    return $postvars;
}
?>
