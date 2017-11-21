<?php 
/*
* Template Name: Higher Education Articles
*/
?>

<?php
if(isset($_POST['your-email'])) {
 
    // EDIT THE 2 LINES BELOW AS REQUIRED
    $email_to = "hello@deluxcode.com";
    $email_subject = "The Subscription form from bugzzy's website";
     
    $your_name = htmlspecialchars($_POST['your-name']); // required
    $email_from = htmlspecialchars($_POST['your-email']); // required
 
    $email_message = "Subscriber.\n\n";
 
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
 
    $email_message .= "Name: ".clean_string($your_name)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
 
// create email headers
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
mail($email_to, $email_subject, $email_message, $headers); 

$thanx = "Thank you. Your contact form sent.";

}

?>

<?php get_header(); ?>


  <!-- - 1 - Section with metrics info - -->

    <section class="insights-articles">
        <div class="insights-articles__header">
            <ul class="insights-articles__breadcrumbs breadcrumbs">
                <?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
            </ul>
            <h1 class="insights-articles__title"><?php the_title(); ?></h1>
        </div>
    </section>


    <!-- - 2 - Section with partners info - -->
    <section class="insights-articles__main">


        <div class="insights-articles__main-header">
            <form class="insights-articles__main-form mailchimp-form" action="<?php the_permalink(); ?>" method="POST" enctype="multipart/form-data">
                <h4 class="insights-articles__main-form-title">Keep up to date</h4>
                <div class="insights-articles__main-form-group">
                    <label class="insights-articles__main-form-label">
                        <input type="text" name="your-name" id="form-name" class="insights-articles__main-form-input"
                               placeholder="Name" required>
                    </label>
                    <label class="insights-articles__main-form-label">
                        <input type="email" name="your-email" id="form-email" class="insights-articles__main-form-input"
                               placeholder="Email" required>
                    </label>
                </div>
                <input type="submit" value="Subscribe" class="insights-articles__main-form-submit">
                <div class="insights-articles__main-form-done">
                    <h5 class="insights-articles__main-form-done-title">Thank You!</h5>
                </div>
                <?php if($thanx) { ?>
                  <span style="color:rgba(12, 148, 12, 0.86); font-weight: 700;margin-top: 20px;"><?php echo $thanx; ?></span>
                  <style>.insights-articles__main-form {height: auto;}</style>
                <?php } ?>
            </form>
            <ul>
                <li class="insights-articles__main-articles active" style="background: url(<?php the_field('articles_bg_img'); ?>) center center no-repeat; background-size: cover;"><a href="<?php the_field('link_to_page_art'); ?>">Articles</a></li>
                <li class="insights-articles__main-case-studies" style="background: url(<?php the_field('case_studi_bg'); ?>) center center no-repeat; background-size: cover;"><a href="<?php the_field('link_to_page_cs'); ?>">Case Studies</a></li>
                <li class="insights-articles__main-videos" style="background: url(<?php the_field('videos_art_bg'); ?>) center center no-repeat; background-size: cover;"><a href="<?php the_field('link_to_page_video'); ?>">Videos</a></li>
            </ul>
        </div>


        <div class="insights-articles__main-content">

        <?php query_posts('post_type=articles_init&articlecat=higher-education-articles&post_status=publish&posts_per_page=6&paged='. get_query_var('paged')); ?>

    <?php if( have_posts() ): ?>

          <?php while( have_posts() ): the_post(); ?>

            <div class="insights-articles__main-item">
                <span class="insights-articles__main-item-date"><?php the_time(' jS F Y'); ?></span>
                <div class="insights-articles__main-item-content">
                    <div class="insights-articles__main-item-img-box">
                      <?php the_post_thumbnail( '', array(
                          'class' => "insights-articles__main-item-img",
                          'alt' => trim(strip_tags( $wp_postmeta->_wp_attachment_image_alt )),) 
                          ); ?>
                        <!-- <img src="img/insights_articles1.jpg" class="insights-articles__main-item-img" alt="news1"> -->
                    </div>
                    <div class="insights-articles__main-item-text-box">
                        <div class="insights-articles__main-item-text-header">
                            <h2 class="insights-articles__main-item-text-title"><?php the_title(); ?></h2>
                            <a href="<?php the_permalink(); ?>" class="insights-articles__main-item-text-button">Continue
                                Reading</a>
                        </div>
                        <?php the_excerpt(); ?>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
            
        </div>


        <div class="insights-articles__main-pagination">
          <?php if (function_exists(pagination)) {
              pagination($custom_query->max_num_pages,"",$paged);
          } ?>
        </div>

        <?php wp_reset_postdata(); ?>

        <?php else:  ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>
           <!--  <nav class="pagination">
               <a href="#" class="prev page-numbers">&larr;</a>
           
               | <a href="#" class="page-numbers">1</a> |
               <a href="#" class="page-numbers current">2</a> |
               <a href="#" class="page-numbers">3</a> |
               <span class="page-skip">...</span>
               | <a href="#" class="page-numbers">20</a> |
           
               <a href="#" class="next page-numbers">&rarr;</a>
           </nav> -->
        </div>

    </section>


<?php get_footer(); ?>