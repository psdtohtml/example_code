<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bugzzy
 */

get_header(); ?>

	<!-- - 1 - Section with metrics info - -->
    <section id="post-<?php the_ID(); ?>" <?php post_class('page-box'); ?>>

    	<h1 class="page__title"><?php the_title(); ?></h1>

		<div class="page_content content">

				<?php
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', 'page' );

					// If comments are open or we have at least one comment, load up the comment template.
					/*if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;*/

				endwhile; // End of the loop.
				?>

		</div><!-- #primary -->
	</section><!-- #post-<?php the_ID(); ?> -->

<?php
//get_sidebar();
get_footer();
