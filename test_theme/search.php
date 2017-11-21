<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package bugzzy
 */

get_header(); ?>


	<?php


   /* $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    $q1 = array(
        'post_type' => array('post', 'careers_init'),
        'posts_per_page' => 1,
        'paged' => $paged,
        'orderby' => 'DESC',
    );
    $query = new WP_Query($q1);*/









	if ( have_posts() ) : ?>

		<!-- - 1 - Section with metrics info - -->
	    <section class="search__metric-wrapper">
	        <h1 class="search__metric-header">
				<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search Results for: %s', 'bugzzy' ), '<span>' . get_search_query() . '</span>' );
				?>
			</h1>
		</section>


		<section class="filter_search">

			<div class="search__main">
				<aside class="sidebar">
					<?php echo do_shortcode('[ULWPQSF id=1410]');?>
				</aside>
			</div>


			<div class="search__main2">


				<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post(); ?>

					<!-- - Section with partners info - -->
					<div class="search__main_resaults">
						<?php get_template_part( 'template-parts/content', 'search' ); ?>
					</div>


				<?php endwhile; ?>

					<?php the_posts_navigation(); ?>



				<?php else : ?>

					<?php get_template_part( 'template-parts/content', 'none' ); ?>



			</div> <!-- .search__main -->


		<?php endif; ?>

	</section><!-- .filter_search -->


	<!-- - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

<?php
//get_sidebar();
get_footer();
