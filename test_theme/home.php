<?php 
/*
* Template Name: Home page
*/
get_header(); ?>

    <!-- - 1 - Section with metrics info - -->
    <section class="homepage__metric" style="background-image: url(<?php the_field('bg_img_s1'); ?>); background-size: cover;">
        <div class="homepage__metric-content">
            <h1 class="homepage__metric-title" style="color:<?php the_field('color_title_s1');?>"><?php the_field('title_s1'); ?></h1>
            <p class="homepage__metric-text" style="color:<?php the_field('color_text_s1');?>"><?php the_field('text_s1'); ?></p>
            <?php if( get_field('link_url_s1') ): ?>
                <a href="<?php the_field('link_url_s1'); ?>" class="homepage__metric-more" style="color:<?php the_field('color_link_url_s1');?>">Learn more</a>
            <?php endif; ?>
        </div>

        <ul class="homepage__metric-menu">
            <li><a href="/shared-services/">Shared Services</a>
                <div class="header-top__link-popup">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'shared'
                        ) );
                    ?>
                </div>
            </li>
            <li><a href="/business-transformation/">Business Transformation</a>
                <div class="header-top__link-popup">
                    <?php
                    	wp_nav_menu( array(
                            'theme_location' => 'business'
                        ) );
                    ?>
                </div>
            </li>            
            <li><a href="/enterprise-wide-security/">Enterprise Wide Security</a>
                <div class="header-top__link-popup">
                    <?php
	                	wp_nav_menu( array(
	                        'theme_location' => 'security'
	                    ) );
	                ?>
                </div>
            </li>
        </ul>

    </section>

    <!-- - 2 - Section with partners info - -->
    <section class="homepage__partners">
        <div class="homepage__partners-bugzzy" style="background-color: <?php the_field('bg_color_lb'); ?>;">
            <div class="homepage__partners-title-box">
                <h2 class="homepage__partners-title"><?php the_field('title_lb'); ?></h2>
                <p class="homepage__partners-text"><?php the_field('text_lb'); ?></p>
            </div>
            <a href="<?php the_field('link_url_lb'); ?>" class="homepage__partners-link hovicon effect-5 sub-b">about us</a>
        </div>
        <div class="homepage__partners-stories" style="background-image: url(<?php the_field('bg_img_rb'); ?>);     background-size: cover;">
            <div class="homepage__partners-title-box2">
                <h2 class="homepage__partners-title2"><?php the_field('title_rb'); ?></h2>
            </div>
            <a href="<?php the_field('link_url_rb'); ?>" class="homepage__partners-link hovicon effect-5 sub-b">Read more</a>
        </div>
    </section>

    <!-- - 3 - Section with core services - -->
    <section class="homepage__services">
        <div class="homepage__services-top">
            <h2 class="homepage__services-title"><?php the_field('title_block_3'); ?></h2>
            <ul class="homepage__services-box">

                <?php

                if( have_rows('box_list') ):
                    while ( have_rows('box_list') ) : the_row();
                ?>

                    <li class="homepage__services-item homepage__services-img1" style="background-image: url(<?php the_sub_field('background_image'); ?>);">
                        <div class="homepage__services-item__box">
                            <a href="<?php the_sub_field('link_url_box'); ?>" class="homepage__services-item__link">Learn more</a>
                            <h3 class="homepage__services-item__title"><?php the_sub_field('title_box'); ?></h3>
                            <p class="homepage__services-item__text"><?php the_sub_field('text_box'); ?></p>
                        </div>
                    </li>

                <?php                       
                    endwhile;
                endif;

                ?>
            </ul>
        </div>

        <div class="homepage__services-bot">
            <ul class="homepage__services-box">

                <?php

                if( have_rows('box_list_2') ):
                    while ( have_rows('box_list_2') ) : the_row(); ?>

                <li class="homepage__services-item2">
                    <div class="homepage__services-item__box2">
                        <h3 class="homepage__services-item__title2"><?php the_sub_field('title_box'); ?></h3>
                        <p class="homepage__services-item__text2"><?php the_sub_field('text_box'); ?></p>
                        <a href="<?php the_sub_field('link_url_box'); ?>" class="homepage__services-item__link2">Learn more</a>
                    </div>
                    <div class="homepage__services-item__slider">

                    <?php if( have_rows('image_slider') ):
                            while ( have_rows('image_slider') ) : the_row();
                    ?>
                        <div class="homepage__services-item__img homepage__slider-img1" style="background-image: url(<?php the_sub_field('img_slide'); ?>);"></div>
                    <?php               

                        endwhile;
                    endif;
                    ?>

                    </div>
                </li>

                <?php
                    endwhile;
                endif;

                ?>
            </ul>
        </div>
    </section>

    <!-- - 4 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

  

</div>


<?php get_footer(); ?>