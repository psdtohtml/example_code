<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bugzzy
 */

get_header(); ?>

	

	<!-- - 1 - Section with metrics info - -->
    <section class="clients-more">

        <div class="clients-more__header">
            <ul class="clients-more__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/clients-page/">Clients</a></li>
                <li><span><?php the_title(); ?></span></li>
            </ul>
            <h1 class="clients-more__title"><?php the_title(); ?></h1>
        </div>

        <div class="clients-more__wrapper" style="background: url(<?php echo get_the_post_thumbnail_url(); ?>) center center no-repeat; background-size: cover;">

        <?php if( get_field('file_pdf_client') ): ?>
            <a href="<?php the_field('file_pdf_client'); ?>" class="clients-more__document" download></a>            
        <?php endif; ?>
            
        </div>

    </section>

    <!-- - 2 - Section with partners info - -->
    <section class="clients-more__content content">

		<?php while ( have_posts() ) : the_post(); ?>           
	            
	            <?php the_content(); ?>
    	        
		<?php 
			// If comments are open or we have at least one comment, load up the comment template.
			/*if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;*/

		endwhile; // End of the loop.
		?>
      

    </section>



    <?php

    $flag = false;
    while ( have_rows('testimonials_clnt_post') ) : the_row(); 
        if (get_sub_field('text') or get_sub_field('title') or get_sub_field('author')){
            $flag = true;
        }
    endwhile;

    if ($flag) { ?>

        <section class="clients-more__testimonials" style="background: rgba(0, 0, 0, 0.6) url(<?php the_field('bg_testim_client'); ?>) no-repeat center center; background-size: cover;">
            <h2 class="clients-more__testimonials-title">Testimonials</h2>
            <div class="clients-more__testimonials-slider-box">          

                <?php if( have_rows('testimonials_clnt_post') ):
                while ( have_rows('testimonials_clnt_post') ) : the_row(); ?>  

                    <div class="clients-more__testimonials-slider-item">
                        <p><?php the_sub_field('text'); ?></p>
                        <h3><?php the_sub_field('title'); ?></h3>
                        <h4><?php the_sub_field('author'); ?></h4>
                    </div>

                <?php endwhile;
                endif; ?>

            </div>
        </section>

    <?php } ?>        

    

    <section class="clients-more__clients">

        <h3><?php the_field('title_logos'); ?></h3>

        <?php query_posts('post_type=clients_init&numberposts=-1&post_status=publish'); ?>

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
            .clients-more__clients-slider__item > a {
                -webkit-transition: all .2s;
                -moz-transition: all .2s;
                -o-transition: all .2s;
                transition: all .2s;
            }
        </style>




    </section>
	


    <!-- - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>




<?php get_footer(); ?>