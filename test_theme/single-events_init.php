<?php 
get_header();?>


<!-- - 1 - Section with metrics info - -->

    <section class="events-more">

        <div class="events-more__header">
            <ul class="events-more__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/events/">Events</a></li>
                <li><a href="/events/events-items/">Events Items</a></li>
                <li><span><?php the_title();?></span></li>
            </ul>
        </div>

        <div class="events-more__wrapper" style="background: url(<?php echo get_the_post_thumbnail_url(); ?>) center center no-repeat;     background-color: rgba(0, 0, 0, 0.6); background-size: cover;">
            <div class="events-more__text-box">
                <h1 class="events-more__title" style="color:<?php the_field('color_title_ew');?>"><?php the_title();?></h1>
                <h2 class="events-more__title-info" style="color:<?php the_field('color_authors_ew');?>"><?php the_field('author_ew'); ?></h2>
            </div>
            <div class="events-more__date-box">
                <span class="events-more__date" style="color:<?php the_field('color_date_ew');?>"><?php the_field('date'); ?></span>
                <span class="events-more__time" style="color:<?php the_field('color_time_ew');?>"><?php the_field('time'); ?></span>
            </div>
        </div>

    </section>


    <section class="events-more__content content">
            
	    <!-- the loop -->
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	            <?php the_content();?>

	        <?php endwhile; ?>
	    <?php endif; ?>
	    <!-- end of the loop -->
    
		<ul class="events-more__content-related">

		<?php

			if( have_rows('block_list_hg') ):

			    while ( have_rows('block_list_hg') ) : the_row(); ?>

					<li class="events-more__content-related-item">
		                <div class="events-more__content-related-item__box">
		                    <h4 class="events-more__content-related-item__title"><?php the_sub_field('title_he'); ?></h4>
		                    <a href="<?php the_sub_field('link_url_he'); ?>" class="events-more__content-related-item__link">Read more</a>
		                </div>
		                <div class="events-more__content-related-item__img events-more__item-img2" style="background: #ffffff url(<?php the_sub_field('img_he'); ?>) center center no-repeat; background-size: cover;"></div>
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

<?php get_footer();?>