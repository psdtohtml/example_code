<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="icon" href="<?php echo get_template_directory_uri();?>/favicon.ico" type="image/x-icon" />
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
	<?php if(is_search()) { ?>
    	<title><?php bloginfo( 'name' ); echo ' - Search';?></title>
	<?php } ?>
	<title><?php bloginfo( 'name' ); echo ' - ';  the_title(''); ?></title>
    <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/font-awesome.min.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/slick.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/slick-theme.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/main.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/newStyle.css"/>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <div class="main-wrapper">
        <!-- - Header begin - -->
        <header class="header">

            <div class="header-top__fixed">
                <div class="header-top__box-wrapper">
                    <div class="header-top__box">
                        <ul class="header-top__info">
                            <li><a href="/shared-services/">Shared Services</a>
                                <div class="header-top__link-popup">
                                    <img src="<?php the_field('img_box_1', 'option');?>" alt="services" class="header-top__popup-img">
                                    <?php
                                        wp_nav_menu( array(
                                            'theme_location' => 'shared'
                                        ) );
                                    ?>
                                </div>
                            </li>
                            <li><a href="/business-transformation/">Business Transformation</a>
                                <div class="header-top__link-popup">
                                    <img src="<?php the_field('img_box_2', 'option');?>" alt="services" class="header-top__popup-img">
                                    <?php
                                        wp_nav_menu( array(
                                            'theme_location' => 'business'
                                        ) );
                                    ?>
                                </div>
                            </li>
                            <li><a href="/enterprise-wide-security/">Enterprise Wide Security</a>
                                <div class="header-top__link-popup">
                                    <img src="<?php the_field('img_box_3', 'option');?>" alt="services" class="header-top__popup-img">
                                    <?php
                                        wp_nav_menu( array(
                                            'theme_location' => 'security'
                                        ) );
                                    ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="header-mid__box-wrapper">
                    <div class="header-mid__box">
                        <a href="<?php echo home_url(); ?>" class="header-logo">
                            <img src="<?php the_field('logo_1', 'option') ?>" alt="Logo" class="header-logo__img">
                            <img src="<?php the_field('logo_2', 'option') ?>" alt="Logo" class="header-logo2__img">
                        </a>
                        <nav class="header-nav">
                            <div class="header-nav__box-wrapper">
                                <form class="header-top__form-mob" method="get" id="searchform-mob" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <select name="language_box-mob" class="header-top__language-box-mob"
                                            title="Select language">
                                        <option class="lang-en-mob" value="English" id="en_language-mob">Eng</option>
                                        <option class="lang-fr-mob" value="French" id="fr_language-mob">Fr</option>
                                    </select>
                                    <div class="header-top__search-box-mob">
                                        <input id="s-mob" class="header-top__search-mob" type="text" name="s"
                                           placeholder="Search here" value="<?php echo get_search_query(); ?>" required>
                                        <button id="searchsubmit-mob" class="header-top__submit-mob submit-icon-mob" type="submit"
                                                title="Search"></button>
                                    </div>
                                </form>
                                <?php
                                    wp_nav_menu( array(
                                        'theme_location' => 'menu-1',
                                        'menu_id'        => 'primary-menu',
                                        'menu_class'     => 'header-nav__box',
                                        'walker' => new Child_Wrap()
                                    ) );
                                ?>
                            </div>
                            <div id="nav_toggle_wrapper" class="nav_toggle_wrapper">
                                <a id="nav_toggle" class="nav_toggle" href="#"><span></span></a>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <!-- - Header end - -->