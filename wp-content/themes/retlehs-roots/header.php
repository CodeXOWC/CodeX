<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>

  <meta name="viewport" content="width=device-width">

  <?php roots_stylesheets(); ?>

  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600|Inika:400,700' rel='stylesheet' type='text/css'>
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
          <a class="brand" href="<?php echo home_url(); ?>/"><img src="/img/logo.png" alt="Minnesota Coalition for the Homeless" /></a>
          <a class="espanol" href="">Se Habla Español</a>
          <div id="social_icons">            
          	<a href="/index.php?p=386" rel="tooltip" title="Subscribe To Email"><img src="/img/subscribe-email.png" alt="" id="subscribe_email" /></a>
            <a href="http://www.facebook.com/HomelessCoalition" target="_blank" rel="tooltip" title="Facebook"><img src="/img/facebook-icon.png" alt="" id="facebook_icon" /></a>
            <a href="https://twitter.com/#!/MNHomelessCo" target="_blank" rel="tooltip" title="Twitter"><img src="/img/twitter-icon.png" alt="" id="twitter_icon" /></a>
            <a href="http://www.youtube.com/user/MNCoalition4Homeless" target="_blank" rel="tooltip" title="YouTube"><img src="/img/you-tube-icon.png" alt="" id="youtube_icon" /></a>
          </div>
          <form role="search" method="get" id="searchform" class="form-search <?php if (is_404() || !have_posts()) { ?> well <?php } ?>" action="<?php echo home_url('/'); ?>">
            <label class="visuallyhidden" for="s"><?php _e('Search for:', 'roots'); ?></label>
            <input type="text" value="" name="s" id="s" class="search-query" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
            <input type="submit" id="searchsubmit" value="<?php _e('Search', 'roots'); ?>" class="btn">
          </form>
          <!--ul class="main_nav">
            <li><a href="#"><?php echo __('Home')?></a></li>
              <li><a href="index.php?page_id=12" class="parent"><?php echo __('About Us')?></a>
                <ul>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                 </ul>
              </li>
              <li><a href="#" class="parent"><?php echo __('Homelessness')?></a>
                <ul>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                    <li><a href="index.php?page_id=12">Navigation</a></li>
                 </ul>
              </li>
              <li><a href="index.php?page_id=12"><?php echo __('Donate')?></a></li>
              <li><a href="index.php?page_id=12"><?php echo _e('In The News')?></a></li>
              <li><a href="index.php?page_id=12"><?php echo __('Get Involved')?></a></li>
              <li><a href="index.php?page_id=12"><?php echo __('Current Campaigns')?></a></li>
           </ul-->
            <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new Roots_Navbar_Nav_Walker(), 'menu_class' => 'main_nav')); ?>

            <?php if( is_front_page() ) : ?>
            <h2 id="tagline">We mobilize communities to create solutions that will end homelessness and bring Minnesota home.</h2>
            <?php endif;?>
        </div>
      </div>
    </header>
  <?php roots_header_after(); ?>

  <?php if( is_front_page() ) : ?>
  <section id="campaign_box">
    <ul class="span12">
      <li class="campaign1 campaign-blue">
        <a href="/campaigns/mfip/">
            <span class="vertical-box">
              <strong>MFIP</strong>
              <small>
                .......................<br />
                learn more
              </small>
            </span>
        </a>
      </li>
      <li class="campaign2 campaign-orange">
        <a href="/campaigns/visible-child/">
            <span class="vertical-box">
              <strong>Visible Child</strong>
              <small>
                .......................<br />
                learn more
              </small>
            </span>
        </a>
      </li>
      <li class="campaign3 campaign-purple">
        <a href="/campaigns/resolutions/">
            <span class="vertical-box">
              <strong>Resolutions</strong>
              <small>
                .......................<br />
                learn more
              </small>
            </span>
        </a>
      </li>
      <li class="campaign4 campaign-green">
        <a href="/campaigns/homes-for-all/">
            <span class="vertical-box">
              <strong>Homes for All</strong>
              <small>
                .......................<br />
                learn more
              </small>
            </span>
        </a>
      </li>
    </ul>
  </section>
  <?php endif;?>

  <?php roots_wrap_before(); ?>
  <div id="wrap" class="<?php echo WRAP_CLASSES; ?>" role="document">
