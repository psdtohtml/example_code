<?php 
/*
* Template Name: Clients
*/
get_header(); ?>

	<!-- - 1 - Section with metrics info - -->
    <section class="clients">
        <h1 class="clients-header"><?php the_title(); ?></h1>
        <div class="clients-wrapper" style="background: url(<?php the_field('bg_img_clnt'); ?>) center center no-repeat; background-size: cover">
            <div class="clients-content">
                <h2 class="clients-title" style="color:<?php the_field('color_title_clnt');?>"><?php the_field('title_clnt'); ?></h2>
                <p class="clients-text" style="color:<?php the_field('color_text_clnt');?>"><?php the_field('text_clnt'); ?></p>
                <?php if( get_field('link_url_clnt') ): ?>                       
                    <a href="<?php the_field('link_url_clnt'); ?>" class="clients-learn-more">Learn more</a>
                <?php endif; ?>
            </div>
            <ul class="clients-menu">
                <li><a href="<?php the_field('financ_clnt'); ?>">Finance</a></li>
                <li><a href="<?php the_field('human_resources_clnt'); ?>">Human Resources</a></li>
                <li><a href="<?php the_field('procurement_clnt'); ?>">Procurement</a></li>
                <li><a href="<?php the_field('information_technology_clnt'); ?>">Information Technology</a></li>
                <li><a href="<?php the_field('multi_functional_clnt'); ?>">Multi - functional</a></li>
                <li><a href="<?php the_field('public_sector_clnt'); ?>">Public Sector</a></li>
            </ul>
        </div>
    </section>

    <!-- - 2 - Section with partners info - -->
    <section class="clients__main content">

        <?php while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->

            <?php the_content(); ?> <!-- Page Content -->

	    <?php
	    endwhile; //resetting the page loop
	    wp_reset_query(); //resetting the page query
	    ?>

    </section>

    
    <section class="shared-services-more__clients">

        <h3><?php the_field('title_logos'); ?></h3>


        <?php query_posts('post_type=clients_init&numberposts=-1&post_status=publish'); ?>

        <?php if( have_posts() ): ?>
        <?php $counter = 1; ?>

            <div class="shared-services-more__clients-slider">

                <?php while( have_posts() ): the_post(); ?>

                    <div class="shared-services-more__clients-slider__item"><a href="<?php the_field('link_client'); ?>" class="brand_logo_<?php echo $counter; ?>"></a></div>
                    <style>
                        .brand_logo_<?php echo $counter; ?> {
                            background: url(<?php the_field('image_clogo'); ?>) center center no-repeat; background-size: contain;
                        }
                        .brand_logo_<?php echo $counter; ?>:hover {
                            background: #0099cc url(<?php the_field('image_clogo_hover'); ?>) center center no-repeat; background-size: contain;
                        }
                    </style>

                <?php $counter++; // add one per row ?> 
                <?php endwhile; ?>
            </div>
        <?php wp_reset_postdata(); ?>

        <?php else:  ?>
        <p><?php _e( 'Sorry, no clients logos matched your criteria.' ); ?></p>
        <?php endif; ?>

        <style>
            .shared-services-more__clients-slider__item a {
                -webkit-transition: all .2s;
                -moz-transition: all .2s;
                -o-transition: all .2s;
                transition: all .2s;
            }
        </style>

    </section>




	<!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

<?php get_footer(); ?>