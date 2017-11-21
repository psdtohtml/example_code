<?php 
/*
* Template Name: Higher Education Videos
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

    <div id="popup1" class="overlay hidden">
      <div class="popup">
        <a class="close" href="javascript:void(0);">&times;</a>
        <div class="content mailchimp-response"><p style="color:red;">Please fill in the blank fields!</p></div>
      </div>
    </div>



  <!-- - 1 - Section with metrics info - -->

    <section class="insights-videos">
        <div class="insights-videos__header">
            <ul class="insights-videos__breadcrumbs breadcrumbs">
                <?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
            </ul>
            <h1 class="insights-videos__title"><?php the_title(); ?></h1>
        </div>
    </section>

  
    <!-- - 2 - Section with partners info - -->
    <section class="insights-videos__main">
        <div class="insights-videos__main-header">
            <form class="insights-videos__main-form mailchimp-form" action="<?php the_permalink(); ?>" method="POST" enctype="multipart/form-data">
                <h4 class="insights-videos__main-form-title">Keep up to date</h4>
                <div class="insights-videos__main-form-group">
                    <label class="insights-videos__main-form-label">
                        <input type="text" name="your-name" id="form-name" class="insights-videos__main-form-input"
                               placeholder="Name" required>
                    </label>
                    <label class="insights-videos__main-form-label">
                        <input type="email" name="your-email" id="form-email" class="insights-videos__main-form-input"
                               placeholder="Email" required>
                    </label>
                </div>
                <input type="submit" value="Subscribe" class="insights-videos__main-form-submit mailchimp_send">
                <div class="insights-articles__main-form-done">
                    <h5 class="insights-articles__main-form-done-title">Thank You!</h5>
                </div>
                <?php if($thanx) { ?>
                  <span style="color:rgba(12, 148, 12, 0.86); font-weight: 700;margin-top: 20px;"><?php echo $thanx; ?></span>
                  <style>.insights-articles__main-form {height: auto;}</style>
                <?php } ?>
            </form>
            <ul>
                <!-- <li class="insights-videos__main-articles"><a href="#">Articles</a></li>
                <li class="insights-videos__main-case-studies"><a href="#">Case Studies</a></li>
                <li class="insights-videos__main-videos active"><a href="#">Videos</a></li>
                 -->
                <li class="insights-videos__main-articles" style="background: url(<?php the_field('articles_bg_img'); ?>) center center no-repeat; background-size: cover;"><a href="<?php the_field('link_to_page_art'); ?>">Articles</a></li>
                <li class="insights-videos__main-case-studies" style="background: url(<?php the_field('case_studi_bg'); ?>) center center no-repeat; background-size: cover;"><a href="<?php the_field('link_to_page_cs'); ?>">Case Studies</a></li>
                <li class="insights-videos__main-videos active" style="background: url(<?php the_field('videos_art_bg'); ?>) center center no-repeat; background-size: cover;"><a href="<?php the_field('link_to_page_video'); ?>">Videos</a></li>
            </ul>
        </div>

        <?php //query_posts('posts_per_page=2&paged='. get_query_var('paged')); ?>

    <?php if( have_posts() ): ?>

          <?php while( have_posts() ): the_post(); ?>

        <div class="insights-videos__main-box">
            <div class="insights-videos__main-box-one">
                <span class="insights-videos__main-box-date"><?php the_field('title_video'); ?></span>
                <!-- <iframe width="100%" height="748" src="" frameborder="0" allowfullscreen></iframe> -->
                <?php the_field('large_video'); ?>
            </div>
            <ul class="insights-videos__main-box-all">

        <?php if( have_rows('list_video') ):
            while ( have_rows('list_video') ) : the_row(); ?>

          <li>
            <span class="insights-videos__main-box-date2"><?php the_sub_field('title'); ?></span>
            <!-- <iframe width="100%" height="340" src="" frameborder="0" allowfullscreen></iframe> -->
            <?php the_sub_field('video_item'); ?>
          </li>
            
        <?php endwhile;
        endif; ?>

            </ul>
        </div>

      <?php endwhile; ?>



        <div class="insights-videos__main-pagination">
          <?php if (function_exists(pagination)) {
              pagination($custom_query->max_num_pages,"",$paged);
            } ?>
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

         <?php wp_reset_postdata(); ?>

        <?php else:  ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>

    </section>

  <!-- - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>


<?php get_footer(); ?>