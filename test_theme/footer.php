<!-- - Footer Begin - -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-top__logo">
                <a href="<?php echo home_url(); ?>" class="header-logo">
                    <img src="<?php the_field('logo_3', 'option') ?>" alt="Logo" class="header-logo__img">
                </a>
                <span class="footer-top__logo-text">Delivering The Promise</span>
            </div>
            <nav class="footer-top__nav">
                
                <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer_menu-1',
                        'menu_id'        => 'footer1-menu',
                        'menu_class'     => 'footer-top__nav-box'
                    ) );
                ?>
                
                <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer_menu-2',
                        'menu_id'        => 'footer2-menu',
                        'menu_class'     => 'footer-top__nav-box'
                    ) );
                ?>
                <div class="footer-top__social-box">
                    
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer_menu-3',
                        'menu_id'        => 'footer3-menu',
                        'menu_class'     => 'footer-top__nav-box'
                    ) );
                ?>
                    <ul class="header-top__social-box">
                        <li class="header-top__social-item"><a href="<?php the_field('facebook_url', 'option') ?>" target="_blank"
                                                               class="header-top__social-link facebook"><i
                                class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li class="header-top__social-item"><a href="<?php the_field('twitter_url', 'option') ?>" target="_blank"
                                                               class="header-top__social-link twitter"><i
                                class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li class="header-top__social-item"><a href="<?php the_field('linkedin_url', 'option') ?>" target="_blank"
                                                               class="header-top__social-link linkedin"><i
                                class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="footer-underline">
            <p class="footer-underline__copy">bugzzy Partners 2017 &copy;</p>

            <!-- Design & Development by Deluxcode -->
            <!-- <p class="footer-underline__dev">Design & Development <a href="http://deluxcode.com/" target="_blank" class="footer-underline__dev-link"><img
                    src="<?php //echo get_template_directory_uri(); ?>/img/deluxcode_logo.png" alt="Deluxcode Logo" class="footer-underline__dev-img"></a></p> -->
        </div>

    </footer>
    <!-- - Footer End - -->

</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/slick.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/slider.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/main.js"></script>

<?php if(is_search()) { ?>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/search_page.js"></script>
<?php } ?>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/newScript.js"></script>

<?php wp_footer(); ?>

</body>
</html>
