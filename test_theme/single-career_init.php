<?php get_header(); ?>


    <!-- - 1 - Section with job info - -->
    <section class="careers-manager">

        <div class="careers-manager__header">
           <!--  <ul class="careers-manager__breadcrumbs breadcrumbs">
               <?php //if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>              
           </ul> -->
            <ul class="careers-manager__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/about-us/">About Us</a></li>
                <li><a href="/about-us/careers/">Careers</a></li>
                <li><a href="/about-us/careers_all/">Careers at bugzzy Partners</a></li>
                <li><span><?php the_title(); ?></span></li>
            </ul>
        </div>


        <div class="careers-manager__job">

            <div class="careers-manager__job-cv">
                <h2 class="careers-manager__job-title">Careers</h2>
                <div class="careers-manager__job-cv-box">
                    <h3 class="careers-manager__job-cv-title">Apply for this role</h3>
                    <span class="careers-manager__job-cv-required">all the fields below are required</span>

                    <?php echo do_shortcode('[contact-form-7 id="450" title="Contact form role careers"]'); ?>
                   
                    <div id="popup_form" class="careers-manager__job-cv-done">
                      <h5 class="careers-manager__job-cv-done-title">Thank You!</h5>
                    </div>
                </div>
            </div>

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <div class="careers-manager__job-info">
                    <h2 class="careers-manager__job-title"><?php the_title(); ?></h2>

                    <div class="careers-manager__job-info-box content">

                        <div class="careers-manager__job-info-header">
                            <div><h5>Region:</h5> <span class="europe-region"><?php the_field('country'); ?></span></div>
                            <div><h5>Location:</h5> <span class="location-region"><?php the_field('location'); ?></span></div>
                        </div>

                        <?php the_content(); ?>

                    </div>
                </div>

                <?php endwhile; ?>
            <?php endif; ?>
            <!-- end of the loop -->

        </div>

    </section>



<?php get_footer(); ?>