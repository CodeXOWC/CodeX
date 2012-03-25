<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>

  <meta name="viewport" content="width=device-width">

  <?php roots_stylesheets(); ?>

  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,600|Inika:400,700' rel='stylesheet' type='text/css'>
  <script src="<?php echo get_template_directory_uri(); ?>/js/libs/modernizr-2.5.3.min.js"></script>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/libs/hoverIntent.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/libs/functions.js"></script>
  
  <?php roots_head(); ?>
  <?php wp_head(); ?>

</head>

<body <?php body_class(roots_body_class()); ?>>

  <!--[if lt IE 7]><p class="chromeframe">Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

  <?php roots_header_before(); ?>
    <header id="banner" class="navbar" role="banner">
      <?php roots_header_inside(); ?>
      <div class="navbar-inner">
        <div class="<?php echo WRAP_CLASSES; ?>">
         <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo home_url(); ?>/">
            <img src="/dropbox/wp/img/logo.png" alt="" />
          </a>
          <div id="social_icons">
          	<a href="#" rel="tooltip" title="Subscribe To Email"><img src="/dropbox/wp/img/subscribe-email.png" alt="" id="subscribe_email" /></a>
            <a href="http://www.facebook.com/HomelessCoalition" target="_blank" rel="tooltip" title="Facebook"><img src="/dropbox/wp/img/facebook-icon.png" alt="" id="facebook_icon" /></a>
            <a href="https://twitter.com/#!/MNHomelessCo" target="_blank" rel="tooltip" title="Twitter"><img src="/dropbox/wp/img/twitter-icon.png" alt="" id="twitter_icon" /></a>
            <a href="http://www.youtube.com/user/MNCoalition4Homeless" target="_blank" rel="tooltip" title="YouTube"><img src="/dropbox/wp/img/you-tube-icon.png" alt="" id="youtube_icon" /></a>
          </div>
          <form role="search" method="get" id="searchform" class="form-search <?php if (is_404() || !have_posts()) { ?> well <?php } ?>" action="<?php echo home_url('/'); ?>">
            <label class="visuallyhidden" for="s"><?php _e('Search for:', 'roots'); ?></label>
            <input type="text" value="" name="s" id="s" class="search-query" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
            <input type="submit" id="searchsubmit" value="<?php _e('Search', 'roots'); ?>" class="btn">
          </form>
          <ul id="main_nav">
            <li><a href="#">Home</a></li>
              <li><a href="#" class="parent">About Us</a>
                <ul>
                  <li><a href="#">Navigation</a></li>
                    <li><a href="#">Navigation</a></li>
                    <li><a href="#">Navigation</a></li>
                    <li><a href="#">Navigation</a></li>
                 </ul>
              </li>
              <li><a href="#" class="parent">Homelessness</a>
                <ul>
                  <li><a href="#">Navigation</a></li>
                    <li><a href="#">Navigation</a></li>
                    <li><a href="#">Navigation</a></li>
                    <li><a href="#">Navigation</a></li>
                 </ul>
              </li>
              <li><a href="#">Donate</a></li>
              <li><a href="#">In The News</a></li>
              <li><a href="#">Get Involved</a></li>
              <li><a href="#">Current Campaigns</a></li>
           </ul>
          <nav id="nav-main" class="nav-collapse" role="navigation">
            <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new Roots_Navbar_Nav_Walker(), 'menu_class' => 'nav')); ?>
          </nav>
        </div>
      </div>
    </header>
  <?php roots_header_after(); ?>

  <?php roots_wrap_before(); ?>
  <div id="wrap" class="<?php echo WRAP_CLASSES; ?>" role="document">