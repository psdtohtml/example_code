<?php 
/*
* Template Name: Shared Services
*/
get_header(); ?>


	
	<!-- - 1 - Section with metrics info - -->

    <section class="shared-services">
        <h1 class="shared-services-header"><?php the_title(); ?></h1>
        <div class="shared-services-wrapper" style="background: url(<?php the_field('bg_img_serv_child'); ?>) center center no-repeat; background-size: cover;">
            <div class="shared-services-content">
         	 	<h2 class="shared-services-title" style="color:<?php the_field('color_title_sp');?>"><?php the_field('title_sp'); ?></h2>
                <p class="shared-services-text" style="color:<?php the_field('color_text_sp');?>"><?php the_field('text_sp'); ?></p>
          		<?php if( get_field('link_url_sp') ): ?>
            	<a href="<?php the_field('link_url_sp'); ?>" class="shared-services-learn-more" style="color:<?php the_field('color_link_sp');?>">Learn more</a>
        		<?php endif; ?>
          	</div>
        </div>
    </section>


    <!-- - 2 - Section with partners info - -->
    <section class="shared-services__main content">

	    <div class="shared-services__main-top">

	        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	       			<?php the_content(); ?>

		        <?php endwhile; ?>
	        <?php endif; ?>
	        <!-- end of the loop -->

        </div>

        <div class="shared-services__main-bot">
            <h2><?php the_field('title_serv_cgild'); ?></h2>
            <ul>
			
				<?php if( have_rows('what_we_can') ):
				    while ( have_rows('what_we_can') ) : the_row(); ?>
				    
				    	<li class="shared-services__main-finance" style="background: url(<?php the_sub_field('img_what_we_can'); ?>) center center no-repeat;">
				    		<a href="<?php the_sub_field('link_url'); ?>"><?php the_sub_field('title_what_we_can'); ?></a>
			    		</li>

				<?php endwhile;
				endif; ?>

            </ul>

        </div>

    </section>


    <?php

    $flag = false;
    while ( have_rows('testimonials_serv') ) : the_row(); 
        if (get_sub_field('text') or get_sub_field('title') or get_sub_field('author')){
            $flag = true;
        }
    endwhile;

    if ($flag) { ?>

	    <section class="shared-services__testimonials" style="background: rgba(0, 0, 0, 0.6) url(<?php the_field('bg_testimonials'); ?>) no-repeat center center; background-size: cover;">
	        <h2 class="shared-services__testimonials-title">Testimonials</h2>
	        <div class="shared-services__testimonials-slider-box">	

			    <?php if( have_rows('testimonials_serv') ):
			    	while ( have_rows('testimonials_serv') ) : the_row(); ?>

				    <div class="shared-services__testimonials-slider-item">
			            <p><?php the_sub_field('text'); ?></p>
			            <h3><?php the_sub_field('title'); ?></h3>
			            <h4><?php the_sub_field('author'); ?></h4>
			        </div>

					<?php endwhile;
				endif; ?>
		
	        </div>
	    </section>

    <?php } ?>

    <section class="shared-services__info">
        <ul class="shared-services__info-box">


			<?php if( have_rows('block_list_serv_child') ):
			    while ( have_rows('block_list_serv_child') ) : the_row(); ?>

			    <li class="shared-services__info-item">
	                <div class="shared-services__info-item-header">
	                    <h2 class="shared-services__info-item-title"><?php the_sub_field('title'); ?></h2>
	                    <a href="<?php the_sub_field('link_url'); ?>" class="shared-services__info-item-link">Read more</a>
	                </div>
	                <div class="shared-services__info-item-img shared-services__info-articles" style="background: #ffffff url(<?php the_sub_field('img'); ?>) center center no-repeat;
    background-size: cover;"></div>
	            </li>

            <?php endwhile;
			endif; ?>

        </ul>
    </section>

    <!-- - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>



<?php get_footer(); ?>