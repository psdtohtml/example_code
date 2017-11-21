<?php 
/*
* Template Name: Higher Education
*/
get_header(); ?>


	<!-- - 1 - Section with metrics info - -->

    <section class="public-sector__education">

        <div class="public-sector__education-header">
            <!-- <ul class="public-sector__education-breadcrumbs breadcrumbs">
                <li><a href="#">Public Sector</a></li>
                <li><span>Higher Education</span></li>
            </ul> -->
            <ul class="public-sector__education-breadcrumbs breadcrumbs">
            	<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
            </ul>
            <h1 class="public-sector__education-title"><?php the_title(); ?></h1>
        </div>

        <div class="public-sector__education-service" style="background: url(<?php the_field('bg_img_he'); ?>) center center no-repeat; background-size: cover">
            <div class="public-sector__education-wrapper">
                <h2 class="public-sector__education-info" style="color:<?php the_field('color_title_he');?>"><?php the_field('title_he'); ?></h2>
                <?php if( have_rows('list_item_he') ): ?>
					<ol>
					    <?php while ( have_rows('list_item_he') ) : the_row(); ?>

							<li><p style="color:<?php the_field('color_text_he');?>"><?php the_sub_field('item_he'); ?></p></li>
						
						<?php endwhile; ?>
					</ol>
				<?php endif; ?>
            </div>
        </div>

    </section>

	 <!-- - 2 - Section with partners info - -->
    <section class="public-sector__education-main clients-more__clients content">
		
	    <?php while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->

	            <?php the_content(); ?> <!-- Page Content -->

	    <?php
	    endwhile; //resetting the page loop
	    wp_reset_query(); //resetting the page query
	    ?>

        <ul class="public-sector__education-main-clients">

            <?php if( have_rows('block_list_hg') ):
                while ( have_rows('block_list_hg') ) : the_row(); ?>

                <li class="public-sector__education-main-item">
                    <div class="public-sector__education-main-item__box">
                        <h4 class="public-sector__education-main-item__title"><?php the_sub_field('title_he'); ?></h4>
                        <a href="<?php the_sub_field('link_url_he'); ?>" class="public-sector__education-main-item__link">Read more</a>
                    </div>
                    <div class="public-sector__education-main-item__img public-sector__education-img2" style="background: #ffffff url(<?php the_sub_field('img_he'); ?>) center center no-repeat; background-size: cover;"></div>
                </li>

            <?php endwhile;
            endif; ?>

        </ul>



        <h3><?php the_field('title_logos'); ?></h3>
        
        <?php query_posts('post_type=clients_init&clientcat=higher-education&post_status=publish'); ?>

        <?php if( have_posts() ): ?>
        <?php $counter = 1; ?>

            <div class="clients-more__clients-slider">

                <?php while( have_posts() ): the_post(); ?>

                    <div class="clients-more__clients-slider__item"><a href="<?php the_field('link_client'); ?>" class="brand_logo_<?php echo $counter; ?>"></a>

                         <style>
                            .brand_logo_<?php echo $counter; ?> {
                                background: url(<?php the_field('image_clogo'); ?>) center center no-repeat; background-size: contain;
                            }
                            .brand_logo_<?php echo $counter; ?>:hover {
                                background: #0099cc url(<?php the_field('image_clogo_hover'); ?>) center center no-repeat; background-size: contain;
                            }
                        </style>
                    </div>

                   

                <?php $counter++; // add one per row ?> 
                <?php endwhile; ?>
            </div>
        <?php wp_reset_postdata(); ?>

        <?php else:  ?>
        <p><?php _e( 'Sorry, no clients logos matched your criteria.' ); ?></p>
        <?php endif; ?>

        <style>
            .public-sector__main li a {
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