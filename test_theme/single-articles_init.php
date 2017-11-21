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
    <section class="insights-case-studies-more">

        <div class="insights-case-studies-more__header">
        	<ul class="news-more__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
   	            <li><a href="/insights/">Insights</a></li>
                <li><a href="/insights/articles/">Articles</a></li>
                <li><span><?php the_title(); ?></span></li>
    		</ul>
            <h1 class="insights-case-studies-more__title"><?php the_title(); ?></h1>
        </div>

        <div class="insights-case-studies-more__wrapper" style="background: url(<?php the_field('bg_img_study'); ?>) center center no-repeat;
    background-size: cover;">
        <?php if( get_field('file_pdf_study') ): ?>
            <a href="<?php the_field('file_pdf_study'); ?>" class="insights-case-studies-more__document" download></a>
        <?php endif; ?>
        </div>

    </section>

	<!-- - 1 - Section with metrics info - -->
   <section class="insights-case-studies-more__content content">

		<?php while ( have_posts() ) : the_post(); ?>
	        <div class="insights-case-studies-more__content-top">

	            <h1><?php the_title(); ?></h1>
	            <span class="news-more__content-item-date"><?php the_time(' jS F Y'); ?></span>

	            <?php the_content(); ?>

		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			/*if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;*/

		endwhile; // End of the loop.
		?>

                <?php
                    $flafAuthor = false;
                     if( have_rows('author_list') ):
                        while ( have_rows('author_list') ) : the_row();
                            $flafAuthor = true;
                        endwhile;
                     endif;
                ?>

                <?php if ($flafAuthor) { ?>

                    <div class="insights-case-studies-more__content-item">

                        <?php if( have_rows('author_list') ):
                            while ( have_rows('author_list') ) : the_row(); ?>

                                <div class="insights-case-studies-more__content-item__box">
                                    <h4 class="insights-case-studies-more__content-item__title"><?php the_sub_field('name_author'); ?></h4>
                                    <p class="insights-case-studies-more__content-item__text"><?php the_sub_field('profession_author'); ?></p>
                                    <a href="<?php the_sub_field('about_the_author'); ?>" class="insights-case-studies-more__content-item__link">About the Author</a>
                                </div>
                                <div class="insights-case-studies-more__content-item__img" style="background: #ffffff url(<?php the_sub_field('image_author'); ?>) center 0 no-repeat; background-size: cover;"></div>

                        <?php endwhile;

                        endif; ?>

                    </div>

                <?php } ?>

        </div>

        <div class="insights-case-studies-more__content-bot">
            <h2>Related Content</h2>

            <ul class="insights-case-studies-more__content-related">

            <?php if( have_rows('block_list_hg') ):
                while ( have_rows('block_list_hg') ) : the_row(); ?>

                    <li class="insights-case-studies-more__content-related-item">
                        <div class="insights-case-studies-more__content-related-item__box">
                            <h4 class="insights-case-studies-more__content-related-item__title"><?php the_sub_field('title_he'); ?></h4>
                            <a href="<?php the_sub_field('link_url_he'); ?>" class="insights-case-studies-more__content-related-item__link">Read more</a>
                        </div>
                        <div class="insights-case-studies-more__content-related-item__img insights-case-studies-more__item-img2" style="background: #ffffff url(<?php the_sub_field('img_he'); ?>) center center no-repeat; background-size: cover;"></div>
                    </li>

            <?php endwhile;

            endif; ?>

            </ul>

        </div>


    </section>



    <!-- - 2 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>




<?php get_footer(); ?>