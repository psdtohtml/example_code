<?php 
/*
* Template Name: About us
*/
get_header(); ?>


 	<!-- - 1 - Section with metrics info - -->
    <section class="about-us__metric-wrapper">
        <h1 class="about-us__metric-header"><?php the_title(); ?></h1>
        <div class="about-us__metric" style="background: url(<?php the_field('bg_img_about'); ?>) center center no-repeat; background-size: cover">
            <div class="about-us__metric-content">
                <h2 class="about-us__metric-title" style="color: <?php the_field('color_title_about'); ?>;"><?php the_field('title_about'); ?></h2>
                <p class="about-us__metric-text" style="color: <?php the_field('color_text_about'); ?>;"><?php the_field('text_about'); ?></p>
                <?php if( get_field('link_url_about') ): ?>                       
                    <a href="<?php the_field('link_url_about'); ?>" class="about-us__metric-more" style="color: <?php the_field('color_link_url_about'); ?>;">Learn more</a>
                <?php endif; ?>
            </div>

            <ul class="about-us__metric-menu">
                <li><a href="<?php the_field('managment_team'); ?>">Managment Team</a></li>
                <li><a href="<?php the_field('news_about'); ?>">News</a></li>
                <li><a href="<?php the_field('careers_about'); ?>">Careers</a></li>
                <li><a href="<?php the_field('contact_us_about'); ?>">Contact Us</a></li>
            </ul>
        </div>
    </section>


	<!-- - 2 - Section with partners info - -->
    <section class="about-us__main content">
		
	    <?php while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->

	            <?php the_content(); ?> <!-- Page Content -->

	    <?php
	    endwhile; //resetting the page loop
	    wp_reset_query(); //resetting the page query
	    ?>
	</section>


	<!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

<?php get_footer(); ?>