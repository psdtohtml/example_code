<?php get_header(); ?>

<div class="main-wrapper">

	<div class="page-404" style="background: rgba(0, 0, 0, 0.6) url(<?php the_field('bg_img_404', 'option'); ?>) no-repeat center center; background-size: cover;">

	    <div class="page-404__content">
	        <h1>Error 404 <br/> not found</h1>
	        <?php the_field('text_404', 'option'); ?>
	        <div class="page-404__content-buttons">
	            <a href="<?php echo home_url();?>">Home</a>
	            <a href="/contact-us">Contact us</a>
	        </div>
	    </div>

	</div>
</div>
<?php get_footer(); ?>

