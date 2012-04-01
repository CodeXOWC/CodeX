
  </div><!-- /#wrap -->

  <?php roots_footer_before(); ?>
  <footer id="content-info" class="<?php echo WRAP_CLASSES; ?>" role="contentinfo">
    <?php roots_footer_inside(); ?>
    <?php dynamic_sidebar('roots-footer'); ?>
    <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new Roots_Navbar_Nav_Walker(), 'menu_class' => 'bottom_nav')); ?>
  </footer>
  <?php roots_footer_after(); ?>

  <?php wp_footer(); ?>
  <?php roots_footer(); ?>
  
<script type="text/javascript">
$('#social_icons a').tooltip();
</script>

</body>
</html>