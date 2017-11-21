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
    <section class="news-more">

        <div class="news-more__header">
        	<!-- <ul class="news-more__breadcrumbs breadcrumbs">
                               <?php //if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
                        </ul> -->
            <ul class="news-more__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/about-us/">About Us</a></li>
                <li><a href="/about-us/news/">News</a></li>
                <li><span><?php the_title(); ?></span></li>
            </ul>
      <!--      <h1 class="news-more__title">Management Team</h1>-->
        </div>
		<?php while ( have_posts() ) : the_post(); ?>
		        <div class="news-more__content content">

		            <h1><?php the_title(); ?></h1>
		            <span class="news-more__content-item-date"><?php the_time(' jS F Y'); ?></span>
		            
		            <?php the_content(); ?>

		        </div>

		<?php 
			// If comments are open or we have at least one comment, load up the comment template.
			/*if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;*/

		endwhile; // End of the loop.
		?>


    </section>
	


    <!-- - 2 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>




<?php get_footer(); ?>
