<?php 
/*
* Template Name: Contact us
*/
?>

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
        $email_to = "mexico@bugzzypartners.com";
    }
    if ($_POST['region'] == 'Asia, Middle East & Africa') {
        $email_to = "hongkong@bugzzypartners.com";
    } 
    //$email_to = "hello@deluxcode.com";

    $email_subject = "Contact from bugzzy's website";
 
    $region = $_POST['region']; // required
    // $location = $_POST['location']; // required
    $first_name = htmlspecialchars($_POST['first_name']); // required
    $last_name = htmlspecialchars($_POST['last_name']); // required
    $job_title = htmlspecialchars($_POST['job_title']); // required
    $organization = htmlspecialchars($_POST['organization']); // required
    $email_from = htmlspecialchars($_POST['email']); // required
    $phone = htmlspecialchars($_POST['phone']); // not required
 
    $email_message = "Form details below.\n\n";
 
     
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
 
    $email_message .= "Region: ".clean_string($region)."\n";
    // $email_message .= "Location: ".clean_string($location)."\n";
    $email_message .= "First Name: ".clean_string($first_name)."\n";
    $email_message .= "Surname: ".clean_string($last_name)."\n";
    $email_message .= "Job Title: ".clean_string($job_title)."\n";
    $email_message .= "Organization: ".clean_string($organization)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
    $email_message .= "Phone: ".clean_string($phone)."\n";
 
// create email headers
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
mail($email_to, $email_subject, $email_message, $headers); 

$thanx = "Thank you for contacting us. We will be in touch with you very soon.";
 
}

?>


<?php get_header(); ?>


	<!-- - 1 - Section with pic - -->
    <section class="contacts">
        <h1 class="contacts-header"><?php the_title(); ?></h1>
        <div class="contacts-wrapper" style="background: url(<?php the_field('image_cont1'); ?>) center center no-repeat; background-size: cover;">
            <!--  <div class="contacts-content">
                  <h2 class="contacts-title"></h2>
                  <p class="contacts-text"></p>
                  <a href="#" class="contacts-more">Learn more</a>
              </div>
              <ul class="contacts-menu">
                  <li><a href="#"></a></li>
              </ul>  -->
        </div>
    </section>


	<!-- - 2 - Section with our offices  - -->
    <section class="contacts__main">

        <div class="contacts__main-form-wrapper">
            <form class="contacts__main-form" action="<?php the_permalink(); ?>" method="POST" enctype="multipart/form-data">
                <h4 class="contacts__main-form-title">Let us contact you</h4>
                <div class="contacts__main-form-group">

                    <select name="region" class="contacts__main-form-region" title="Select region">
                        <option class="" disabled selected hidden>Region</option>
                        <option class="" value="Europe" id="europe">Europe</option>
                        <option class="" value="North America" id="n_america">North America</option>
                        <option class="" value="Latin America" id="l_america">Latin America</option>
                        <option class="" value="Asia, Middle East & Africa" id="asia_africa">Asia, Middle East & Africa</option>
                    </select>

                    <label class="contacts__main-form-label">
                        <input type="text" name="first_name" id="form-name" class="contacts__main-form-input"
                               placeholder="First Name" required>
                    </label>

                    <label class="contacts__main-form-label">
                        <input type="text" name="last_name" id="form-surname" class="contacts__main-form-input"
                               placeholder="Surname" required>
                    </label>

                    <label class="contacts__main-form-label">
                        <input type="text" name="job_title" id="form-job" class="contacts__main-form-input"
                               placeholder="Job Title" required>
                    </label>

                    <label class="contacts__main-form-label">
                        <input type="text" name="organization" id="form-organization" class="contacts__main-form-input"
                               placeholder="Organization" required>
                    </label>

                    <label class="contacts__main-form-label">
                        <input type="email" name="email" id="form-email" class="contacts__main-form-input"
                               placeholder="E-mail" required>
                    </label>

                    <label class="contacts__main-form-label">
                        <input type="tel" name="phone" id="form-tel" class="contacts__main-form-input"
                               placeholder="Telephone" required>
                    </label>

                </div>
                    <input type="submit" value="Submit" class="contacts__main-form-submit">
            </form>

        </div>

        <div class="contacts__main-offices">
            <h2 class="contacts__main-offices-title">Our Offices</h2>

            <div class="contacts__main-offices-box">
                <h3 class="contacts__main-offices-box-title">Europe</h3>
                <ul class="contacts__main-offices-box-wrapper">


					<?php if( have_rows('eurepe') ):

					    while ( have_rows('eurepe') ) : the_row(); ?>

		                    <li class="contacts__main-offices-box-item cyprus-flag" style="background: url(<?php the_sub_field('logo_country'); ?>) center 0 no-repeat;">
		                        <h5 class="contacts__main-offices-box-item-title"><?php the_sub_field('country'); ?></h5>
		                        <address class="contacts__main-offices-box-item-addr">
		                        	<?php the_sub_field('address_country'); ?>
		                        </address>
		                        <a href="tel:<?php the_sub_field('phone_office'); ?>" class="contacts__main-offices-box-item-tel"><?php the_sub_field('phone_office'); ?></a>
		                        <a href="mailto:<?php the_sub_field('email_office'); ?>" class="contacts__main-offices-box-item-mail"><?php the_sub_field('email_office'); ?></a>
		                    </li>

					<?php   endwhile;

					endif;  ?>

                </ul>
            </div>

            <div class="contacts__main-offices-box">
                <h3 class="contacts__main-offices-box-title">North America</h3>
                <ul class="contacts__main-offices-box-wrapper">
					
					<?php if( have_rows('noth_america') ):

					    while ( have_rows('noth_america') ) : the_row(); ?>

		                    <li class="contacts__main-offices-box-item canada-flag" style="background: url(<?php the_sub_field('logo_country'); ?>) center 0 no-repeat;">
		                        <h5 class="contacts__main-offices-box-item-title"><?php the_sub_field('country'); ?></h5>
		                        <address class="contacts__main-offices-box-item-addr">
		                        	<?php the_sub_field('address_country'); ?>
		                        </address>
		                        <a href="tel:<?php the_sub_field('phone_office'); ?>" class="contacts__main-offices-box-item-tel"><?php the_sub_field('phone_office'); ?></a>
		                        <a href="mailto:<?php the_sub_field('email_office'); ?>" class="contacts__main-offices-box-item-mail"><?php the_sub_field('email_office'); ?></a>
		                    </li>

                    <?php endwhile;

					endif; ?>

                </ul>
            </div>

            <div class="contacts__main-offices-box">
                <h3 class="contacts__main-offices-box-title">Latin America</h3>
                <ul class="contacts__main-offices-box-wrapper">


					<?php if( have_rows('latin_america') ):

					    while ( have_rows('latin_america') ) : the_row(); ?>

		                    <li class="contacts__main-offices-box-item argentina-flag" style="background: url(<?php the_sub_field('logo_country'); ?>) center 0 no-repeat;">
		                        <h5 class="contacts__main-offices-box-item-title"><?php the_sub_field('country'); ?></h5>
		                        <address class="contacts__main-offices-box-item-addr">
		                        	<?php the_sub_field('address_country'); ?>		                        	
		                        </address>
		                        <a href="tel:<?php the_sub_field('phone_office'); ?>" class="contacts__main-offices-box-item-tel"><?php the_sub_field('phone_office'); ?></a>
		                        <a href="mailto:<?php the_sub_field('email_office'); ?>" class="contacts__main-offices-box-item-mail"><?php the_sub_field('email_office'); ?></a>
		                    </li>

					<?php endwhile;

					endif; ?>

                </ul>
                <ul class="contacts__main-offices-box-wrapper2">



					<?php if( have_rows('latin_america_2') ):

					    while ( have_rows('latin_america_2') ) : the_row(); ?>

		                    <li class="contacts__main-offices-box-item argentina-flag" style="background: url(<?php the_sub_field('logo_country'); ?>) center 0 no-repeat;">
		                        <h5 class="contacts__main-offices-box-item-title"><?php the_sub_field('country'); ?></h5>
		                        <address class="contacts__main-offices-box-item-addr">
		                        	<?php the_sub_field('address_country'); ?>		                        	
		                        </address>
		                        <a href="tel:<?php the_sub_field('phone_office'); ?>" class="contacts__main-offices-box-item-tel"><?php the_sub_field('phone_office'); ?></a>
		                        <a href="mailto:<?php the_sub_field('email_office'); ?>" class="contacts__main-offices-box-item-mail"><?php the_sub_field('email_office'); ?></a>
		                    </li>

					<?php endwhile;

					endif; ?>

                </ul>
            </div>

            <div class="contacts__main-offices-box">
                <h3 class="contacts__main-offices-box-title">Asia, Middle East & Africa</h3>
                <ul class="contacts__main-offices-box-wrapper" style="background: url(<?php the_sub_field('logo_country'); ?>) center 0 no-repeat;">

	                <?php if( have_rows('asia_africa') ):

					    while ( have_rows('asia_africa') ) : the_row(); ?>

		                    <li class="contacts__main-offices-box-item hong_kong-flag" style="background: url(<?php the_sub_field('logo_country'); ?>) center 0 no-repeat;">
		                        <h5 class="contacts__main-offices-box-item-title"><?php the_sub_field('country'); ?></h5>
		                        <address class="contacts__main-offices-box-item-addr">
		                        	<?php the_sub_field('address_country'); ?>
		                        </address>
		                        <a href="tel:<?php the_sub_field('phone_office'); ?>" class="contacts__main-offices-box-item-tel"><?php the_sub_field('phone_office'); ?></a>
		                        <a href="mailto:<?php the_sub_field('email_office'); ?>" class="contacts__main-offices-box-item-mail"><?php the_sub_field('email_office'); ?></a>
		                    </li>


	               	<?php endwhile;

					endif; ?>

                </ul>
            </div>
        </div>

    </section>




<?php get_footer(); ?>