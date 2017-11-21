<?php 

	if(isset($_POST['email'])) {
 
    // EDIT THE 2 LINES BELOW AS REQUIRED

    if ($_POST['region'] == 'Europe') {
        $email_to = "uk@bugzzypartners.com";
    } 
    if ($_POST['region'] == 'North America') {
        $email_to = "us@bugzzypartners.com";
    }
    if ($_POST['region'] == 'Latin America') {
        // $email_to = "mexico@bugzzypartners.com";
        $email_to = "hello@deluxcode.com";
    }
    if ($_POST['region'] == 'Asia, Middle East & Africa') {
        $email_to = "hongkong@bugzzypartners.com";
    } 
    // $email_to = "hello@deluxcode.com";
    $email_subject = "Contact from bugzzy's website";
 
    $region = $_POST['region']; // required
    $first_name = htmlspecialchars($_POST['first_name']); // required
    $last_name = htmlspecialchars($_POST['last_name']); // required
    $job_title = htmlspecialchars($_POST['job_title']); // required
    $organization = htmlspecialchars($_POST['organization']); // required
    $email_from = htmlspecialchars($_POST['email']); // required
    $phone = htmlspecialchars($_POST['phone']); // not required
 
    $email_message = "Registration on Webinar.\n\n";
 
     
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
 
    $email_message .= "Region: ".clean_string($region)."\n";
    $email_message .= "First Name: ".clean_string($first_name)."\n";
    $email_message .= "Last Name: ".clean_string($last_name)."\n";
    $email_message .= "Job Title: ".clean_string($job_title)."\n";
    $email_message .= "Organization: ".clean_string($organization)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
    $email_message .= "Phone: ".clean_string($phone)."\n";
 
// create email headers
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
mail($email_to, $email_subject, $email_message, $headers); 

// $thanx = "Thank you for contacting us. We will be in touch with you very soon.";
$thanx = "display: flex;";

}

?>


<?php get_header(); ?>



<!-- - 1 - Section with metrics info - -->

    <section class="webinars-more">

        <div class="webinars-more__header">
            <ul class="webinars-more__breadcrumbs breadcrumbs">
                <li><a href="/">Home</a></li>
                <li><a href="/events/">Events</a></li>
                <li><a href="/events/webinars-items/">Webinars</a></li>
                <li><span><?php the_title();?></span></li>
            </ul>
        </div>

        <div class="webinars-more__wrapper" style="background: url(<?php echo get_the_post_thumbnail_url(); ?>) center center no-repeat;     background-color: rgba(0, 0, 0, 0.6); background-size: cover;">
            <div class="webinars-more__text-box">
                <h1 class="webinars-more__title" style="color:<?php the_field('color_title_ew');?>"><?php the_title();?></h1>
                <h2 class="webinars-more__title-info" style="color:<?php the_field('color_authors_ew');?>"><?php the_field('author_ew'); ?></h2>
            </div>
            <div class="webinars-more__date-box">
                <span class="webinars-more__date" style="color:<?php the_field('color_date_ew');?>"><?php the_field('date'); ?></span>
                <span class="webinars-more__time" style="color:<?php the_field('color_time_ew');?>"><?php the_field('time'); ?></span>
            </div>
        </div>

    </section>

    
	<section class="webinars-more__content content">
            
	    <!-- the loop -->
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	            <?php the_content();?>

	        <?php endwhile; ?>
	    <?php endif; ?>
	    <!-- end of the loop -->


	    <div class="webinars-more__content-form-wrapper">

	    <form class="webinars-more__content-form" action="<?php the_permalink(); ?>" method="POST" enctype="multipart/form-data">
                    <h4 class="webinars-more__content-form-title">Register</h4>
                    <div class="webinars-more__content-form-group">
                        <label class="webinars-more__content-form-label">
                            <input type="text" name="first_name" id="form-name" class="webinars-more__content-form-input"
                               placeholder="First Name" required>
                        </label>
                        <select name="" class="webinars-more__content-form-region" title="Select region">
                            <option class="" disabled selected hidden>Region</option>
	                        <option class="" value="Europe" id="europe">Europe</option>
	                        <option class="" value="North America" id="n_america">North America</option>
	                        <option class="" value="Latin America" id="l_america">Latin America</option>
	                        <option class="" value="Asia, Middle East & Africa" id="asia_africa">Asia, Middle East & Africa</option>
                        </select>
                        <label class="webinars-more__content-form-label">
                            <input type="text" name="last_name" id="form-surname" class="webinars-more__content-form-input"
                               placeholder="Last Name" required>
                        </label>
                        <label class="webinars-more__content-form-label">
                            <input type="email" name="email" id="form-email" class="webinars-more__content-form-input"
                               placeholder="E-mail" required>
                        </label>
                        <label class="webinars-more__content-form-label">
                            <input type="text" name="job_title" id="form-job" class="webinars-more__content-form-input"
                               placeholder="Job Title" required>
                        </label>
                        <label class="webinars-more__content-form-label">
                            <input type="tel" name="tel" id="form-tel" class="webinars-more__content-form-input"
                                   placeholder="Telephone">
                        </label>
                        <label class="webinars-more__content-form-label">
                            <input type="text" name="organization" id="form-organization" class="contacts__main-form-input"
                               placeholder="Organization" required>
                        </label>

                    </div>
                    <input type="submit" value="Register" class="webinars-more__content-form-submit">
                </form>

                <div class="webinars-more__content-form-done" style="<?php echo $thanx; ?>">
                    <h5 class="webinars-more__content-form-done-title">Thank You</h5>
                    <h6 class="webinars-more__content-form-done-info">for Registering</h6>
                    <span class="webinars-more__content-form-done-text">Click the icon below to DOWNLOAD a calendar invite</span>
                    <?php if( get_field('file_pdf_calendar') ): ?>
                        <a href="<?php the_field('file_pdf_calendar'); ?>" class="webinars-more__content-form-done-link" download>
                            <img src="<?php echo get_template_directory_uri();?>/img/calendar.png" alt="Calendar">
                        </a>
                    <?php endif; ?>
                </div>

            </div>
    
    </section> <!-- //webinar -->


    

    <!-- - Section with help info - -->
    <section class="section__help" style="background-image: url(<?php the_field('bg_img_contact', 'option'); ?>);">
        <h2 class="section__help-title"><?php the_field('text_contacts', 'option'); ?></h2>
        <a href="<?php the_field('link_url_contacts', 'option'); ?>" class="section__help-link">Contact Us</a>
    </section>

<?php get_footer();?>