<?php 
/*
* Template Name: Managment Team
*/
get_header(); ?>




 <!-- - 1 - Section with metrics info - -->
    <section class="manag-team">

        <div class="manag-team__header">
            <ul class="manag-team__breadcrumbs breadcrumbs">
               	<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
            </ul>
            <h1 class="manag-team__title"><?php the_title(); ?></h1>
        </div>

        <div class="manag-team__content">

            <h2 class="title_ship">Global Leadership</h2>
            <ul class="manag-team__content-box">

            <?php
	        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	        $custom_args = array(
	            'post_type' =>  'team_init',
                'membercat' => 'global-leadership',
	            'posts_per_page' => 12,
	            'paged' => $paged,
	            'post_status'=>'publish',
	            // 'order_by' => 'menu_order',
	            'order' => 'ASC'
	        );
	        $custom_query = new WP_Query( $custom_args ); ?>

	        <?php if ( $custom_query->have_posts() ) : ?>

	            <!-- the loop -->
	            <?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>

               <li class="manag-team__content-item">
                    <div class="manag-team__content-item-img manag_team-img1" style="background: #ffffff url(<?php the_post_thumbnail_url(); ?>) center center no-repeat; background-size: cover;"></div>
                    <div class="manag-team__content-item__box">
                        <h3 class="manag-team__content-item__title"><?php the_title(); ?></h3>
                        <p class="manag-team__content-item__text"><?php the_field('profession'); ?></p>
                        <a href="<?php echo get_the_permalink(); ?>" class="manag-team__content-item__link">View More</a>
                    </div>
                </li>

            	<?php endwhile; ?>

            </ul>

            <?php wp_reset_postdata(); ?>

            <?php else:  ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>

        </div>

        <div class="manag-team__content">
        
            <h2 class="title_ship">Regional Leadership</h2>
            <ul class="manag-team__content-box">

            <?php
            $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
            $custom_args = array(
                'post_type' =>  'team_init',
                'membercat' => 'regional-leadership',
                'posts_per_page' => 12,
                'paged' => $paged,
                'post_status'=>'publish',
                // 'order_by' => 'menu_order',
                'order' => 'ASC'
            );
            $custom_query = new WP_Query( $custom_args ); ?>

            <?php if ( $custom_query->have_posts() ) : ?>

                <!-- the loop -->
                <?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>

                <li class="manag-team__content-item">
                    <div class="manag-team__content-item-img manag_team-img1" style="background: #ffffff url(<?php the_post_thumbnail_url(); ?>) center center no-repeat; background-size: cover;"></div>
                    <div class="manag-team__content-item__box">
                        <h3 class="manag-team__content-item__title"><?php the_title(); ?></h3>
                        <p class="manag-team__content-item__text"><?php the_field('profession'); ?></p>
                        <a href="<?php echo get_the_permalink(); ?>" class="manag-team__content-item__link">View More</a>
                    </div>
                </li>

                <?php endwhile; ?>

            </ul>

        	<!-- <div class="manag-team__content-pagination">
                    <?php
                      /*if (function_exists(pagination)) {
                        pagination($custom_query->max_num_pages,"",$paged);
                      }*/
                    ?>
                </div> -->

	        <?php wp_reset_postdata(); ?>

	        <?php else:  ?>
	        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	        <?php endif; ?>

            <!-- <div class="manag-team__content-pagination">
                <nav class="pagination">
                    <a href="#" class="prev page-numbers">&larr;</a>
            
                    | <a href="#" class="page-numbers">1</a> |
                    <a href="#" class="page-numbers current">2</a> |
                    <a href="#" class="page-numbers">3</a> |
                    <span class="page-skip">...</span>
                    | <a href="#" class="page-numbers">20</a> |
            
                    <a href="#" class="next page-numbers">&rarr;</a>
                </nav>
            </div> -->

        </div>

        <div class="manag-team__content">
        
            <h2 class="title_ship">Functional Leads</h2>
            <ul class="manag-team__content-box">

            <?php
            $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
            $custom_args = array(
                'post_type' =>  'team_init',
                'membercat' => 'functional-leads',
                'posts_per_page' => 12,
                'paged' => $paged,
                'post_status'=>'publish',
                'order' => 'ASC'
            );
            $custom_query = new WP_Query( $custom_args ); ?>

            <?php if ( $custom_query->have_posts() ) : ?>

                <!-- the loop -->
                <?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>

                <li class="manag-team__content-item">
                    <div class="manag-team__content-item-img manag_team-img1" style="background: #ffffff url(<?php the_post_thumbnail_url(); ?>) center center no-repeat; background-size: cover;"></div>
                    <div class="manag-team__content-item__box">
                        <h3 class="manag-team__content-item__title"><?php the_title(); ?></h3>
                        <p class="manag-team__content-item__text"><?php the_field('profession'); ?></p>
                        <a href="<?php echo get_the_permalink(); ?>" class="manag-team__content-item__link">View More</a>
                    </div>
                </li>

                <?php endwhile; ?>

            </ul>

            <!-- <div class="manag-team__content-pagination">
                    <?php
                      /*if (function_exists(pagination)) {
                        pagination($custom_query->max_num_pages,"",$paged);
                      }*/
                    ?>
                </div> -->

            <?php wp_reset_postdata(); ?>

            <?php else:  ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>

            <!-- <div class="manag-team__content-pagination">
                <nav class="pagination">
                    <a href="#" class="prev page-numbers">&larr;</a>
            
                    | <a href="#" class="page-numbers">1</a> |
                    <a href="#" class="page-numbers current">2</a> |
                    <a href="#" class="page-numbers">3</a> |
                    <span class="page-skip">...</span>
                    | <a href="#" class="page-numbers">20</a> |
            
                    <a href="#" class="next page-numbers">&rarr;</a>
                </nav>
            </div> -->

        </div>


    </section>

    <!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

<?php get_footer(); ?>