<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>


        <?php get_template_part('loop', 'single'); ?>

        <?php if ($post->post_type === 'campaigns') { ?>

        <h2>Take Action!</h2>

        <ul class="campaign-list">
          <li><a href="http://www.gis.leg.mn/OpenLayers/districts/">Contact Your Legislator</a></li>
          <li><a href="/documents/2012/03/ten-steps-for-submitting-a-letter-to-the-editor.docx">Contact Your Local Newspaper</a></li>
        </ul>

        <h2>Track Legislation</h2>

        <ul class="campaign-list">
          <li><a href="">Link 1</a></li>
          <li><a href="">Link 1</a></li>
          <li><a href="">Link 1</a></li>
        </ul>

        <?php

          $campaign_posts = get_the_category($post->ID);

          foreach ( $campaign_posts as $category) {

            $args = array(
              'category_name'   => $post->slug,
              'orderby'         => 'date',
              'order'           => 'ASC',
              'post_status'     => 'publish' );

            $related_posts = new WP_Query( $args );

            if ($related_posts->have_posts()) {
              echo '<h2>Recent News</h2>';
              echo '<ul class="campaign-list">';

              while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>

                <li>
                <h3><a class="single-title" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?> </a></h3>
                <p class="date"><?=date('F d, Y', strtotime($post->post_date))?></p>
                <p><?=$post->post_content?></p>
                </li>

              <?php
              endwhile;

              echo '</ul>';
            }
            wp_reset_postdata();
          } ?>

          <h2>Featured Story</h2>
          <p><a href="">Want to share your story? Email Us!</a></p>
          <p>Lorem ipsum dolor sit vamet, consectetur adipiscing elit. Morbi pellentesque arcu eget lectus ultricies volutpat. Vivamus acLorem ipsum dolor sit vamet, consectetur adipiscing elit. Morbi pellentesque arcu eget lectus ultricies volutpat. Vivamus acLorem ipsum dolor sit vamet, consectetur adipiscing elit. Morbi pellentesque arcu eget lectus ultricies volutpat. Vivamus ac</p>
          <p>Lorem ipsum dolor sit vamet, consectetur adipiscing elit. Morbi pellentesque arcu eget lectus ultricies volutpat. Vivamus ac</p>

          <h2>Campaign Resources</h2>
          <p>Subscribe to Receive Updates to This Campaign</p>
          <p>Lorem ipsum dolor sit vamet, consectetur adipiscing elit. Morbi pellentesque arcu eget lectus ultricies volutpat. Vivamus ac nulla non purus</p>
          <p>Lorem ipsum dolor sit vamet, consectetur adipiscing elit. Morbi pellentesque arcu eget lectus ultricies volutpat. Vivamus ac nulla non purus</p>

        <?php } ?>
        <?php roots_loop_after(); ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    <?php roots_sidebar_before(); ?>
      <aside id="sidebar" class="<?php echo SIDEBAR_CLASSES; ?>" role="complementary">
      <?php roots_sidebar_inside_before(); ?>
        <?php get_sidebar(); ?>
      <?php roots_sidebar_inside_after(); ?>
      </aside><!-- /#sidebar -->
    <?php roots_sidebar_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>