<?php 
get_header();?>

<!-- - 1 - Section with metrics info - -->
    <section class="manag-team-more">

        <div class="manag-team-more__header">
           <!--  <ul class="manag-team-more__breadcrumbs breadcrumbs">
               <?php //if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>              
           </ul> -->
            
            <ul class="manag-team-more__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/about-us/">About Us</a></li>
                <li><a href="/about-us/managment-team/">Management Team</a></li>
                <li><span><?php the_title();?></span></li>
            </ul>
            <h1 class="manag-team-more__title">Management Team</h1>
        </div>

        <div class="manag-team-more__content content">

            <img src="<?php the_field('img_member'); ?>" alt="people" class="manag-team-more__content-img">

		    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	            <h2><?php the_title();?></h2>
	            <h3><?php the_field('profession'); ?></h3>
	     		<!-- the loop -->

	            <?php the_content();?>

		        <?php endwhile; ?>
		    <?php endif; ?>
		    <!-- end of the loop -->

    </section>
        

    <!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

<?php get_footer();?>