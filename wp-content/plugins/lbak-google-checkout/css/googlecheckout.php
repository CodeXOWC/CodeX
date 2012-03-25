<?php header('Content-type: text/css'); ?>
@charset "utf-8";
/* CSS Document */

.product {
    border: 1px solid #aaaaaa;
    padding: 5px;
    width: auto;
    text-align: center;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    overflow: auto;
    max-width: <?php echo $_GET['pw']; ?>;
    max-height: <?php echo $_GET['ph']; ?>;
    line-height: 140%;
    margin-top: 2px;
    margin-bottom: 2px;
}

.not_in_stock {
    opacity: 0.5;
    filter: alpha(opacity=50);
}

.product_title {
    font-weight: bold;
    font-size: 18px;
    color: <?php echo $_GET['tc'] ?>;
}

.product hr {
    border: 0px;
    background-color: #cccccc;
    height: 1px;
    margin: 2px;
    padding: 0;
}

.product_info {
    margin-top: 2px;
    border: 1px solid #cccccc;
    width: auto;
    text-align: center;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

.product img {
    border: 1px solid #cccccc;
    padding: 2px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    max-width: <?php echo $_GET['iw']; ?>;
    max-height: <?php echo $_GET['ih']; ?>;
    width: auto;
}

.product_attribute {
    padding: 2px;
}

.googlecart-add-button {
    margin: auto;
}