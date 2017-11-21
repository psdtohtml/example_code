<?php 
/*
* Template Name: Insights
*/
get_header(); ?>


	<!-- - 1 - Section with metrics info - -->
    <section class="insights">
        <h1 class="insights-header"><?php the_title(); ?></h1>
        <div class="insights-wrapper" style="background: url(<?php the_field('bg_img_ins'); ?>) center center no-repeat; background-size: cover;">
            <div class="insights-content">
                <h2 class="insights-title" style="color:<?php the_field('color_title_ins');?>"><?php the_field('title_ins'); ?></h2>
                <p class="insights-text" style="color:<?php the_field('color_text_ins');?>"><?php the_field('text_ins'); ?></p>
                <?php if( get_field('link_url_ins') ): ?>
                	<a href="<?php the_field('link_url_ins'); ?>" class="insights-more" style="color:<?php the_field('color_link_url_ins');?>">Learn more</a>
                <?php endif; ?>
            </div>
            <ul class="insights-menu">
                <li><a href="<?php the_field('articles_ins'); ?>">Articles</a></li>
                <li><a href="<?php the_field('case_studies_ins'); ?>">Case Studies</a></li>
                <li><a href="<?php the_field('videos_ins'); ?>">Videos</a></li>
            </ul>
        </div>
    </section>


	 <!-- - 2 - Section with partners info - -->
    <section class="insights__main content">
		
	    <?php while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->

            <?php the_content(); ?> <!-- Page Content -->            

	    <?php endwhile; //resetting the page loop
	    wp_reset_query(); //resetting the page query
	    ?>

		<ul class="insights__main-clients">
			<?php

			if( have_rows('block_list_ins') ):

			    while ( have_rows('block_list_ins') ) : the_row(); ?>

		            <li class="insights__main-item">
		                <div class="insights__main-item__box">
		                    <h4 class="insights__main-item__title"><?php the_sub_field('title_ins'); ?></h4>
		                    <a href="<?php the_sub_field('link_url_ins'); ?>" class="insights__main-item__link">Read more</a>
		                </div>
		                <div class="insights__main-item__img insights-img2" style="background: #ffffff url(<?php the_sub_field('img_ins'); ?>) center center no-repeat; background-size: cover;"></div>
		            </li>
				
			<?php   endwhile;

			endif; 

			?>
		</ul>

	</section>

	<!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>



<?php get_footer(); ?>