<?php 
/*
* Template Name: Public sector
*/
get_header(); ?>

 	<!-- - 1 - Section with metrics info - -->
    <section class="public-sector__business">
        <h1 class="public-sector__business-header"><?php the_title(); ?></h1>
        <div class="public-sector__business-wrapper" style="background-image: url(<?php the_field('bg_img_ps'); ?>);">
            <div class="public-sector__business-content">
                <h2 class="public-sector__business-title" style="color:<?php the_field('color_title_ps');?>"><?php the_field('title_ps'); ?></h2>
                <p class="public-sector__business-text" style="color:<?php the_field('color_text_ps');?>"><?php the_field('text_ps'); ?></p>
                <?php if( get_field('link_url_ps') ): ?>
                    <a href="<?php the_field('link_url_ps'); ?>" class="public-sector__business-more" style="color:<?php the_field('color_link_url_ps');?>">Learn more</a>
                <?php endif; ?>
            </div>
            <ul class="public-sector__business-menu">
                <li><a href="<?php the_field('govern_url'); ?>">Government, Healthcare & NGOs</a></li>
                <li><a href="<?php the_field('higher_education_ps'); ?>">Higher Education</a></li>
            </ul>
        </div>
    </section>

	

	<!-- - 2 - Section with partners info - -->
    <section class="public-sector__main clients-more__clients content">
		
	    <?php while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->

            <?php the_content(); ?> <!-- Page Content -->

	    <?php
	    endwhile; //resetting the page loop
	    wp_reset_query(); //resetting the page query
	    ?>

        </section>

    <section class="public-sector__main">

        <ul class="public-sector__main-clients">
			<?php

			if( have_rows('block_list_hg') ):

			    while ( have_rows('block_list_hg') ) : the_row(); ?>

                    <li class="public-sector__main-item">
                        <div class="public-sector__main-item__box">
                            <h4 class="public-sector__main-item__title"><?php the_sub_field('title_he'); ?></h4>
                            <a href="<?php the_sub_field('link_url_he'); ?>" class="public-sector__main-item__link">Read more</a>
                        </div>
                        <div class="public-sector__main-item__img public-sector-img2" style="background: #ffffff url(<?php the_sub_field('img_he'); ?>) center center no-repeat; background-size: cover;"></div>
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