<?php 
/*
* Template Name: Careers All
*/
get_header(); ?>

	<!-- - 1 - Section with job info - -->

    <section class="careers-all">

        <div class="careers-all__header">
            <ul class="careers-all__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/about-us/">About Us</a></li>
                <li><a href="/about-us/careers/">Careers</a></li>
                <li><span>Careers at bugzzy Partners</span></li>
            </ul>
            <h1 class="careers-all__title"><?php the_title(); ?></h1>
        </div>

        <div class="careers-all__job" style="background: url(<?php the_field('bg_img_care'); ?>) center center no-repeat;background-size: cover;">
            <div class="careers-all__job-content">
                <h2 class="careers-all__job-title"><?php the_field('title_care'); ?></h2>
                <p class="careers-all__job-text"><?php the_field('text_care'); ?> <a class="careers-all__job-mail"
                           href="mailto:<?php the_field('email_care'); ?>"><?php the_field('email_care'); ?></a>
                </p>
                <p class="careers-all__job-text"><?php the_field('text_2_care'); ?></p>
            </div>
        </div>

    </section>


    <!-- - 2 - Section with opportunities info - -->
    <section class="careers-all__opportunities">
        <h2 class="careers-manager__job-title">OPPORTUNITIES</h2>
        <div class="careers-all__opportunities-box">

            <div class="careers-all__opportunities-filter">
                <h3 class="careers-all__opportunities-filter-title">Filter by Region</h3>
                <!--<form action="" class="careers-all__opportunities-filter-form" enctype="multipart/form-data">-->
                    <!--<label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">All Regions</label>
                    <label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">Europe</label>
                    <label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">Africa</label>
                    <label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">Australia</label>
                    <label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">Asia</label>
                    <label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">South America</label>
                    <label class="careers-all__opportunities-filter-checkbox"><input type="checkbox">North America</label>-->	
					
					<?php
					/* global $wpdb;
					$field_name = "country";
					$sql = $wpdb->prepare( "select post_id from " . $wpdb->prefix . "postmeta where meta_key = %s limit 0,1 ", $field_name);
					$post = $wpdb->get_results( $sql );

					$field = get_field_object( $field_name, $post[0]->post_id );

					if( $field )
					{
						//echo '<select name="' . $field['key'] . '">';
							foreach( $field['choices'] as $k => $v )
							{
								echo  '<label class="careers-all__opportunities-filter-checkbox">';
								echo '<input type="checkbox" value="' . $k . '" rel="' . $k . '">' . $v . '';
								echo '</label>';
							}
						//echo '</select>';
					} */
					?>
				
                <!--</form>-->
			<?php echo do_shortcode('[ULWPQSF id=1643]'); ?>
            </div>

            <div class="careers-all__opportunities-region">
                <div class="careers-all__opportunities-region-header">
                    <h3 class="careers-all__opportunities-region-title">Position</h3>
                    <h3 class="careers-all__opportunities-region-title">Region</h3>
                </div>

                <ul class="careers-all__opportunities-region-box">


					<?php
			        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 0;
			        $custom_args = array(
			            'post_type' =>  array('career_init'),
			            'posts_per_page' => 6,
			            'paged' => $paged,
			            'post_status'=>'publish',
			            // 'order_by' => 'menu_order',
			            'order' => 'DESC'
			        );
			        $custom_query = new WP_Query( $custom_args ); ?>

			        <?php if ( $custom_query->have_posts() ) : ?>

			            <!-- the loop -->
			            <?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>

							<li class="<?php
					
							foreach( $field['choices'] as $k => $v )
							{
								
								echo $k;
								echo ' ';
								
							}

					?>">
		                        <div>
		                            <h4><?php the_title(); ?></h4>

								<span class="europe-region"><?php the_field('country'); ?></span>

		                            <a href="<?php the_permalink(); ?>">Continue Reading</a>
		                        </div>
		                        <?php 
	                                // $content = the_content();
	                                // echo mb_strimwidth($content, 0, 400, '...');
	                                the_excerpt();
	                            ?>
		                    </li>

		            	<?php endwhile; ?>
		                    
                </ul>
            </div>
        </div>

        <div class="careers-all__opportunities-pagination">
            <?php
              if (function_exists(pagination)) {
                pagination($custom_query->max_num_pages,"",$paged);
              }
            ?>
        </div>

        <?php wp_reset_postdata(); ?>

        <?php else:  ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>


       <!--  <div class="careers-all__opportunities-pagination">
           <nav class="pagination">
               <a href="#" class="prev page-numbers">&larr;</a>
       
               | <a href="#" class="page-numbers">1</a> |
               <a href="#" class="page-numbers current">2</a> |
               <a href="#" class="page-numbers">3</a> |
               <span class="page-skip">...</span>
               | <a href="#" class="page-numbers">20</a> |
       
               <a href="#" class="next page-numbers">&rarr;</a>
           </nav>
       </div>
        -->
    </section>

    <!-- - 3 - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>


<?php get_footer(); ?>

