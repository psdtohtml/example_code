<?php 
/*
* Template Name: Careers
*/
get_header(); ?>

	<!-- - 1 - Section with metrics info - -->

    <section class="careers">

        <div class="careers__header">
        	<ul class="careers__breadcrumbs breadcrumbs">
            	<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>              
            </ul>
            <h1 class="careers__title"><?php the_title(); ?></h1>
        </div>


        <div class="careers__job" style="background: url(<?php the_field('bg_img_care'); ?>) center center no-repeat; background-size: cover;">

            <div class="careers__job-content">
                <h2 class="careers__job-title" style="color:<?php the_field('color_title_care');?>"><?php the_field('title_care'); ?></h2>
                <p class="careers__job-text" style="color:<?php the_field('color_text_care');?>"><?php the_field('text_care'); ?> <a class="careers__job-mail" href="mailto:<?php the_field('email_care'); ?>"><?php the_field('email_care'); ?></a>
                </p>
                <p class="careers__job-text"><?php the_field('text_2_care'); ?></p>

                <p class="careers__job-text2"><?php the_field('text_3_care'); ?></p>
                <?php if( get_field('link_url_care') ): ?>                       
                    <a href="<?php the_field('link_url_care'); ?>" class="careers__job-more" style="color:<?php the_field('color_link_url_care');?>">Learn more</a>
                <?php endif; ?>
            </div>


            <ul class="careers__job-menu">
                <li><a href="<?php the_field('view_all_care'); ?>">View all positions</a></li>
            </ul>

        </div>


    </section>


    <!-- - 2 - Section with partners info - -->
    <section class="careers__content content">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

       			<?php the_content(); ?>

	        <?php endwhile; ?>
        <?php endif; ?>
        <!-- end of the loop -->

    </section>


    <!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>


<?php get_footer(); ?>