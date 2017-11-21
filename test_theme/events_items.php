<?php 
/*
* Template Name: Events Items
*/
get_header(); ?>

	<!-- - 1 - Section with metrics info - -->
    <section class="events">
        <h1 class="events-header"><?php the_title(); ?></h1>
        <div class="events-wrapper" style="background-image: url(<?php the_field('bg_img_ew'); ?>);">
            <div class="events-content">
                <h2 class="events-title" style="color:<?php the_field('color_title_ew');?>"><?php the_field('title_ew'); ?></h2>
                <p class="events-text" style="color:<?php the_field('color_text_ew');?>"><?php the_field('text_ew'); ?></p>
            </div>           
        </div>
    </section>


	<!-- - 2 - Section with partners info - -->
    <section class="events__main">

        <div class="events__main-content">

    	<?php
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $custom_args = array(
            'post_type' =>  array('events_init'),
            'posts_per_page' => 20,
            'paged' => $paged,
            'post_status'=>'publish',
            // 'order_by' => 'menu_order',
            'order' => 'DESC'
        );
        $custom_query = new WP_Query( $custom_args ); ?>

        <?php if ( $custom_query->have_posts() ) : ?>

            <!-- the loop -->
            <?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>

                    <?php  
                    /*echo '<pre>';
                    var_dump($custom_args); 
                    var_dump($custom_args['post_type'][0]); 
                    echo '</pre>';
                    exit;*/ ?>

                <div class="events__main-item events__main-item-e">
                    <div class="events__main-item-img-box">
                        <!-- <img src="img/events_6.jpg" class="events__main-item-img" alt="events5"> -->
                        <?php the_post_thumbnail('full', array('class' => 'events__main-item-img')); ?>   
                    </div>
                    <div class="events__main-item-text-box">
                        <h2 class="events__main-item-text-title"><?php the_title();?></h2>
                        <div class="events__main-item-text-date">
                            <span style="color:<?php the_field('color_date_ew');?>"><?php the_field('date'); ?></span>
                            <span style="color:<?php the_field('color_loc_ew');?>"><?php the_field('location'); ?></span>
                        </div>
                            <?php 
                                // $content = the_content();
                                // echo mb_strimwidth($content, 0, 400, '...');
                                the_excerpt();
                            ?>
                        <a href="<?php echo get_the_permalink(); ?>" class="events__main-item-text-button">Continue Reading</a>
                    </div>
                </div>

            <?php endwhile; ?>

        </div><!-- //events__main-content -->

        <div class="events__main-pagination">
            <?php
              if (function_exists(pagination)) {
                pagination($custom_query->max_num_pages,"",$paged);
              }
            ?>
        </div>

        <?php wp_reset_postdata(); ?>

        <?php else:  ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>

    </section>


	<!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>




<?php get_footer(); ?>