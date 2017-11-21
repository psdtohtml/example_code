<?php
/**
 * bugzzy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bugzzy
 */

if ( ! function_exists( 'bugzzy_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function bugzzy_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on bugzzy, use a find and replace
	 * to change 'bugzzy' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'bugzzy', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'menu-1' => esc_html__( 'Primary', 'bugzzy' ),
    'shared' => esc_html__( 'Shared Services', 'bugzzy' ),
    'business' => esc_html__( 'Business Transformation', 'bugzzy' ),
    'security' => esc_html__( 'Enterprise Wide Security', 'bugzzy' ),
		'footer_menu-1' => esc_html__( 'Footer1', 'bugzzy' ),
		'footer_menu-2' => esc_html__( 'Footer2', 'bugzzy' ),
		'footer_menu-3' => esc_html__( 'Footer3', 'bugzzy' ),
	) );


class Child_Wrap extends Walker_Nav_Menu
{
   function start_lvl(&$output, $depth)
   {
       $indent = str_repeat("\t", $depth);
       $output .= "\n$indent<div class=\"header-top__link-popup\"><ul class=\"sub-menu header-top__popup-nav\">\n";
   }
   function end_lvl(&$output, $depth)
   {
       $indent = str_repeat("\t", $depth);
       $output .= "$indent</ul></div><div class=\"header-top__cross\"></div>\n";
   }
}


	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'bugzzy_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
	) );
}
endif;
add_action( 'after_setup_theme', 'bugzzy_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function bugzzy_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'bugzzy_content_width', 640 );
}
add_action( 'after_setup_theme', 'bugzzy_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function bugzzy_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'bugzzy' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'bugzzy' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'bugzzy_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function bugzzy_scripts() {
	wp_enqueue_style( 'bugzzy-style', get_stylesheet_uri() );

	wp_enqueue_script( 'bugzzy-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'bugzzy-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script('ajax-mailchimp', get_template_directory_uri() . '/js/ajax-mailchimp.js', array(), '1.0', true);

    wp_localize_script( 'ajax-mailchimp', 'ajaxmailchimp', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));
}
add_action( 'wp_enqueue_scripts', 'bugzzy_scripts' );

/**
 * Implement Mailchimp class
 */
include( __DIR__ . '/inc/MailChimp.php');
use \DrewM\MailChimp\MailChimp as Mailchimp;

/**
 * Add ajax function
 */
add_action( 'wp_ajax_nopriv_ajax_mailchimp', 'my_ajax_mailchimp' );
add_action( 'wp_ajax_ajax_mailchimp', 'my_ajax_mailchimp' );

function my_ajax_mailchimp() {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $list_id = 'e7aaabc949';

    $mailchimp = new MailChimp('22ee91977e2623926c3367a9696d7c10-us11');

    $result = $mailchimp->post("lists/$list_id/members", array(
        'email_address' => $email,
        'status'        => 'subscribed',
        'merge_fields'  => array(
            'FNAME' => $name
        )
    ));

    if ($mailchimp->success()) {
        echo 'true';
    } else {
        echo 'false';
    }
    die();
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page();
	
}

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page('Social');
	
}

function dimox_breadcrumbs() {

  /* === ОПЦИИ === */
  $text['home'] = 'Home'; // текст ссылки "Главная"
  $text['category'] = '%s'; // текст для страницы рубрики
  $text['search'] = 'Search results for "%s"'; // текст для страницы с результатами поиска
  $text['tag'] = 'Tagged with "%s"'; // текст для страницы тега
  $text['author'] = 'Author\'s articles %s'; // текст для страницы автора
  $text['404'] = 'Error 404'; // текст для страницы 404
  $text['page'] = 'Page %s'; // текст 'Страница N'
  $text['cpage'] = 'Comment page %s'; // текст 'Страница комментариев N'

  //$wrap_before = '<li>'; // открывающий тег обертки
  //$wrap_after = '</li><!-- .breadcrumbs -->'; // закрывающий тег обертки
  $sep = '›'; // разделитель между "крошками"
  $sep_before = '<span class="sep">'; // тег перед разделителем
  $sep_after = '</span>'; // тег после разделителя
  $show_home_link = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
  $show_on_home = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
  $show_current = 1; // 1 - показывать название текущей страницы, 0 - не показывать
  $before = '<li><span class="current">'; // тег перед текущей "крошкой"
  $after = '</span></li>'; // тег после текущей "крошки"
  /* === КОНЕЦ ОПЦИЙ === */

  global $post;
  $home_url = home_url('/');
 /* $link_before = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
  $link_after = '</span>';
  $link_in_before = '<span itemprop="name">';
  $link_in_after = '</span>';*/
  $link_attr = ' itemprop="item"';
  $link = '<li><a href="%1$s"' . $link_attr . '> %2$s </a></li>';
  $frontpage_id = get_option('page_on_front');
  $parent_id = ($post) ? $post->post_parent : '';
  $sep = ' ' . $sep_before . $sep . $sep_after . ' ';
  $home_link = '<li><a href="' . $home_url . '"class="home">'. $text['home'] . '</a></li>';

  if (is_home() || is_front_page()) {

    if ($show_on_home) echo $wrap_before . $home_link . $wrap_after;

  } else {

    //echo $wrap_before;
    if ($show_home_link) echo $home_link;

    if ( is_category() ) {
      $cat = get_category(get_query_var('cat'), false);
      if ($cat->parent != 0) {
        $cats = get_category_parents($cat->parent, TRUE, $sep);
        $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', '<li><a$1> $2 </a></li>', $cats);
        if ($show_home_link) echo $sep;
        echo $cats;
      }
      if ( get_query_var('paged') ) {
        $cat = $cat->cat_ID;
        echo $sep . sprintf($link, get_category_link($cat), get_cat_name($cat)) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . $before . sprintf($text['category'], single_cat_title('', false)) . $after;
      }

    } elseif ( is_search() ) {
      if (have_posts()) {
        if ($show_home_link && $show_current) echo $sep;
        if ($show_current) echo $before . sprintf($text['search'], get_search_query()) . $after;
      } else {
        if ($show_home_link) echo $sep;
        echo $before . sprintf($text['search'], get_search_query()) . $after;
      }

    } elseif ( is_day() ) {
      if ($show_home_link) echo $sep;
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $sep;
      echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F'));
      if ($show_current) echo $sep . $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      if ($show_home_link) echo $sep;
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'));
      if ($show_current) echo $sep . $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      if ($show_home_link && $show_current) echo $sep;
      if ($show_current) echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ($show_home_link) echo $sep;
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        printf($link, $home_url . $slug['slug'] . '/', $post_type->labels->singular_name);
        if ($show_current) echo $sep . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, $sep);
        if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#','<li><a$1>$2</a></li>', $cats);
        echo $cats;
        if ( get_query_var('cpage') ) {
          echo $sep . sprintf($link, get_permalink(), get_the_title()) . $sep . $before . sprintf($text['cpage'], get_query_var('cpage')) . $after;
        } else {
          if ($show_current) echo $before . get_the_title() . $after;
        }
      }

    // custom post type
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      if ( get_query_var('paged') ) {
        echo $sep . sprintf($link, get_post_type_archive_link($post_type->name), $post_type->label) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . $before . $post_type->label . $after;
      }

    } elseif ( is_attachment() ) {
      if ($show_home_link) echo $sep;
      $parent = get_post($parent_id);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      if ($cat) {
        $cats = get_category_parents($cat, TRUE, $sep);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', '<li><a$1>$2</a></li>', $cats);
        echo $cats;
      }
      printf($link, get_permalink($parent), $parent->post_title);
      if ($show_current) echo $sep . $before . get_the_title() . $after;

    } elseif ( is_page() && !$parent_id ) {
      if ($show_current) echo $sep . $before . get_the_title() . $after;

    } elseif ( is_page() && $parent_id ) {
      if ($show_home_link) echo $sep;
      if ($parent_id != $frontpage_id) {
        $breadcrumbs = array();
        while ($parent_id) {
          $page = get_page($parent_id);
          if ($parent_id != $frontpage_id) {
            $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
          }
          $parent_id = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        for ($i = 0; $i < count($breadcrumbs); $i++) {
          echo $breadcrumbs[$i];
          if ($i != count($breadcrumbs)-1) echo $sep;
        }
      }
      if ($show_current) echo $sep . $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      if ( get_query_var('paged') ) {
        $tag_id = get_queried_object_id();
        $tag = get_tag($tag_id);
        echo $sep . sprintf($link, get_tag_link($tag_id), $tag->name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
      }

    } elseif ( is_author() ) {
      global $author;
      $author = get_userdata($author);
      if ( get_query_var('paged') ) {
        if ($show_home_link) echo $sep;
        echo sprintf($link, get_author_posts_url($author->ID), $author->display_name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_home_link && $show_current) echo $sep;
        if ($show_current) echo $before . sprintf($text['author'], $author->display_name) . $after;
      }

    } elseif ( is_404() ) {
      if ($show_home_link && $show_current) echo $sep;
      if ($show_current) echo $before . $text['404'] . $after;

    } elseif ( has_post_format() && !is_singular() ) {
      if ($show_home_link) echo $sep;
      echo get_post_format_string( get_post_format() );
    }

    //echo $wrap_after;

  }
} // end of dimox_breadcrumbs()


/* Post type  Team
-----------------------------------------------------------*/
add_action( 'init', 'team_init' );
function team_init() {
  // Раздел вопроса - articlecat
  register_taxonomy('membercat', array('member'), array(
    'label'                 => 'Category', // определяется параметром $labels->name
    'labels'                => array(
      'name'              => 'Categories',
      'singular_name'     => 'Category',
      'search_items'      => 'Search Category',
      'all_items'         => 'All Categories',
      'parent_item'       => 'Parent Category',
      'parent_item_colon' => 'Parent Category:',
      'edit_item'         => 'Edit Category',
      'update_item'       => 'Update Category',
      'add_new_item'      => 'Add Category',
      'new_item_name'     => 'New Category',
      'menu_name'         => 'Categories',
    ),
    'description'           => 'Categories for Members', // описание таксономии
    'public'                => true,
    'show_in_nav_menus'     => false, // равен аргументу public
    'show_ui'               => true, // равен аргументу public
    'show_tagcloud'         => false, // равен аргументу show_ui
    'hierarchical'          => true,
    'rewrite'               => array('slug'=>'managment-team', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
    'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
  ) );

  // тип записи - article - article
  register_post_type('team_init', array(
    'label'               => 'Team',
    'labels'              => array(
      'name'          => 'Member',
      'singular_name' => 'Management Team',
      'menu_name'     => 'Team',
      'all_items'     => 'All Members',
      'add_new'       => 'Add Member',
      'add_new_item'  => 'Add New Member',
      'edit'          => 'Edit',
      'edit_item'     => 'Edit Member',
      'new_item'      => 'New Member',
    ),
    'description'         => '',
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_rest'        => false,
    'rest_base'           => '',
    'show_in_menu'        => true,
    'exclude_from_search' => false,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array( 'slug'=>'team_post', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
    'has_archive'         => 'team',
    'query_var'           => true,    
    'supports'              => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives'),
    'taxonomies'          => array( 'membercat', 'member', 'post_tag' ),
  ) );

}

/* Post type  Clients
-----------------------------------------------------------*/
add_action( 'init', 'clients_init' );
function clients_init() {
  // Раздел вопроса - articlecat
  register_taxonomy('clientcat', array('clients'), array(
    'label'                 => 'Category', // определяется параметром $labels->name
    'labels'                => array(
      'name'              => 'Categories',
      'singular_name'     => 'Category',
      'search_items'      => 'Search Category',
      'all_items'         => 'All Categories',
      'parent_item'       => 'Parent Category',
      'parent_item_colon' => 'Parent Category:',
      'edit_item'         => 'Edit Category',
      'update_item'       => 'Update Category',
      'add_new_item'      => 'Add Category',
      'new_item_name'     => 'New Category',
      'menu_name'         => 'Categories',
    ),
    'description'           => 'Categories for Clients', // описание таксономии
    'public'                => true,
    'show_in_nav_menus'     => false, // равен аргументу public
    'show_ui'               => true, // равен аргументу public
    'show_tagcloud'         => false, // равен аргументу show_ui
    'hierarchical'          => true,
    'rewrite'               => array('slug'=>'clients_post', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
    'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
  ) );

  // тип записи - article - article
  register_post_type('clients_init', array(
    'label'               => 'Clients',
    'labels'              => array(
      'name'          => 'Client',
      'singular_name' => 'Clients',
      'menu_name'     => 'Clients',
      'all_items'     => 'All Clients',
      'add_new'       => 'Add Client',
      'add_new_item'  => 'Add New Client',
      'edit'          => 'Edit',
      'edit_item'     => 'Edit Client',
      'new_item'      => 'New Client',
    ),
    'description'         => '',
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_rest'        => false,
    'rest_base'           => '',
    'show_in_menu'        => true,
    'exclude_from_search' => false,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array( 'slug'=>'clients', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
    'has_archive'         => 'clients',
    'query_var'           => true,    
    'supports'              => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives'),
    'taxonomies'          => array( 'clientcat', 'clients', 'post_tag' ),
  ) );

}

/* Post type  Case Studies
-----------------------------------------------------------*/
add_action( 'init', 'studies_init' );
function studies_init() {
  // Раздел вопроса - articlecat
  register_taxonomy('studycat', array('study'), array(
    'label'                 => 'Category', // определяется параметром $labels->name
    'labels'                => array(
      'name'              => 'Categories',
      'singular_name'     => 'Category',
      'search_items'      => 'Search Category',
      'all_items'         => 'All Categories',
      'parent_item'       => 'Parent Category',
      'parent_item_colon' => 'Parent Category:',
      'edit_item'         => 'Edit Category',
      'update_item'       => 'Update Category',
      'add_new_item'      => 'Add Category',
      'new_item_name'     => 'New Category',
      'menu_name'         => 'Categories',
    ),
    'description'           => 'Categories for Studies', // описание таксономии
    'public'                => true,
    'show_in_nav_menus'     => false, // равен аргументу public
    'show_ui'               => true, // равен аргументу public
    'show_tagcloud'         => false, // равен аргументу show_ui
    'hierarchical'          => true,
    'rewrite'               => array('slug'=>'case_studies', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
    'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
  ) );

  // тип записи - article - article
  register_post_type('studies_init', array(
    'label'               => 'Case Studies',
    'labels'              => array(
      'name'          => 'Study',
      'singular_name' => 'Studies',
      'menu_name'     => 'Case Studies',
      'all_items'     => 'All Studies',
      'add_new'       => 'Add Study',
      'add_new_item'  => 'Add New Study',
      'edit'          => 'Edit',
      'edit_item'     => 'Edit Study',
      'new_item'      => 'New Study',
    ),
    'description'         => '',
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_rest'        => false,
    'rest_base'           => '',
    'show_in_menu'        => true,
    'exclude_from_search' => false,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array( 'slug'=>'studies', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
    'has_archive'         => 'studies',
    'query_var'           => true,    
    'supports'              => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives'),
    'taxonomies'          => array( 'studycat', 'study', 'post_tag' ),
  ) );

}


/* Post type  Events
-----------------------------------------------------------*/
add_action( 'init', 'events_init' );
function events_init() {
	$labels = array(
		'name'               => _x( 'Events', 'team', 'bugzzy' ),
		'singular_name'      => _x( 'Events', 'team', 'bugzzy' ),
		'menu_name'          => _x( 'Events', 'admin menu', 'bugzzy' ),
		'name_admin_bar'     => _x( 'Events', 'add new on admin bar', 'bugzzy' ),
		'add_new'            => _x( 'Add New', 'Event', 'bugzzy' ),
		'add_new_item'       => __( 'Add New Event', 'bugzzy' ),
		'new_item'           => __( 'New Event', 'bugzzy' ),
		'edit_item'          => __( 'Edit Event', 'bugzzy' ),
		'view_item'          => __( 'View Event', 'bugzzy' ),
		'all_items'          => __( 'All Events', 'bugzzy' ),
		'search_items'       => __( 'Search Events', 'bugzzy' ),
		'parent_item_colon'  => __( 'Parent Events:', 'bugzzy' ),
		'not_found'          => __( 'No teams found.', 'bugzzy' ),
		'not_found_in_trash' => __( 'No teams found in Trash.', 'bugzzy' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'bugzzy' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'event_post' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports' => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives',),
    'taxonomies' => array('category', 'post_tag')
	);

	register_post_type( 'events_init', $args );
}


/* Post type  Webinars
-----------------------------------------------------------*/
add_action( 'init', 'webinars_init' );
function webinars_init() {
	$labels = array(
		'name'               => _x( 'Webinars', 'team', 'bugzzy' ),
		'singular_name'      => _x( 'Webinars', 'team', 'bugzzy' ),
		'menu_name'          => _x( 'Webinars', 'admin menu', 'bugzzy' ),
		'name_admin_bar'     => _x( 'Webinars', 'add new on admin bar', 'bugzzy' ),
		'add_new'            => _x( 'Add New', 'Webinar', 'bugzzy' ),
		'add_new_item'       => __( 'Add New Webinar', 'bugzzy' ),
		'new_item'           => __( 'New Webinar', 'bugzzy' ),
		'edit_item'          => __( 'Edit Webinar', 'bugzzy' ),
		'view_item'          => __( 'View Webinar', 'bugzzy' ),
		'all_items'          => __( 'All Webinars', 'bugzzy' ),
		'search_items'       => __( 'Search Webinars', 'bugzzy' ),
		'parent_item_colon'  => __( 'Parent Webinars:', 'bugzzy' ),
		'not_found'          => __( 'No teams found.', 'bugzzy' ),
		'not_found_in_trash' => __( 'No teams found in Trash.', 'bugzzy' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'bugzzy' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'webinar_post' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports' => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives',),
    'taxonomies' => array('category', 'post_tag')
	);

	register_post_type( 'webinars_init', $args );
}


/* Post type  Careers
-----------------------------------------------------------*/
add_action( 'init', 'career_init' );
function career_init() {
  // Раздел вопроса - articlecat
  register_taxonomy('careerscat', array('career'), array(
    'label'                 => 'Category', // определяется параметром $labels->name
    'labels'                => array(
      'name'              => 'Categories',
      'singular_name'     => 'Category',
      'search_items'      => 'Search Category',
      'all_items'         => 'All Categories',
      'parent_item'       => 'Parent Category',
      'parent_item_colon' => 'Parent Category:',
      'edit_item'         => 'Edit Category',
      'update_item'       => 'Update Category',
      'add_new_item'      => 'Add Category',
      'new_item_name'     => 'New Category',
      'menu_name'         => 'Categories',
    ),
    'description'           => 'Categories for Careers', // описание таксономии
    'public'                => true,
    'show_in_nav_menus'     => false, // равен аргументу public
    'show_ui'               => true, // равен аргументу public
    'show_tagcloud'         => false, // равен аргументу show_ui
    'hierarchical'          => true,
    'rewrite'               => array('slug'=>'career_post', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
    'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
  ) );

  // тип записи - article - article
  register_post_type('career_init', array(
    'label'               => 'Careers',
    'labels'              => array(
      'name'          => 'Career',
      'singular_name' => 'Careers',
      'menu_name'     => 'Careers',
      'all_items'     => 'All Careers',
      'add_new'       => 'Add Career',
      'add_new_item'  => 'Add New Career',
      'edit'          => 'Edit',
      'edit_item'     => 'Edit Career',
      'new_item'      => 'New Career',
    ),
    'description'         => '',
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_rest'        => false,
    'rest_base'           => '',
    'show_in_menu'        => true,
    'exclude_from_search' => false,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array( 'slug'=>'careers', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
    'has_archive'         => 'careers',
    'query_var'           => true,    
    'supports'              => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives'),
    'taxonomies'          => array( 'careerscat', 'career', 'post_tag' ),
  ) );

}



/* Post type  Articles
-----------------------------------------------------------*/
add_action( 'init', 'articles_init' );
function articles_init() {
  // Раздел вопроса - articlecat
  register_taxonomy('articlecat', array('article'), array(
    'label'                 => 'Category', // определяется параметром $labels->name
    'labels'                => array(
      'name'              => 'Categories',
      'singular_name'     => 'Category',
      'search_items'      => 'Search Category',
      'all_items'         => 'All Categories',
      'parent_item'       => 'Parent Category',
      'parent_item_colon' => 'Parent Category:',
      'edit_item'         => 'Edit Category',
      'update_item'       => 'Update Category',
      'add_new_item'      => 'Add Category',
      'new_item_name'     => 'New Category',
      'menu_name'         => 'Categories',
    ),
    'description'           => 'Categories for Articles', // описание таксономии
    'public'                => true,
    'show_in_nav_menus'     => false, // равен аргументу public
    'show_ui'               => true, // равен аргументу public
    'show_tagcloud'         => false, // равен аргументу show_ui
    'hierarchical'          => true,
    'rewrite'               => array('slug'=>'article_post', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
    'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
  ) );

  // тип записи - article - article
  register_post_type('articles_init', array(
    'label'               => 'Articles',
    'labels'              => array(
      'name'          => 'Articles',
      'singular_name' => 'Articles',
      'menu_name'     => 'Articles',
      'all_items'     => 'All Articles',
      'add_new'       => 'Add Article',
      'add_new_item'  => 'Add New Article',
      'edit'          => 'Edit',
      'edit_item'     => 'Edit Article',
      'new_item'      => 'New Article',
    ),
    'description'         => '',
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_rest'        => false,
    'rest_base'           => '',
    'show_in_menu'        => true,
    'exclude_from_search' => false,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array( 'slug'=>'articles', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
    'has_archive'         => 'articles',
    'query_var'           => true,    
    'supports'              => array('title','editor','thumbnail', 'excerpt', 'custom-fields', 'comments','revisions', 'archives'),
    'taxonomies'          => array( 'articlecat', 'article', 'post_tag' ),
  ) );

}



function pagination($numpages = '', $pagerange = '', $paged='') {

  if (empty($pagerange)) {
    $pagerange = 2;
  }

  global $paged;
  if (empty($paged)) {
    $paged = 1;
  }
  if ($numpages == '') {
    global $wp_query;
    $numpages = $wp_query->max_num_pages;
    if(!$numpages) {
        $numpages = 1;
    }
  }

  /** 
   * We construct the pagination arguments to enter into our paginate_links
   * function. 
   */
  $pagination_args = array(
    'base'            => get_pagenum_link(1) . '%_%',
    'format'          => 'page/%#%',
    'total'           => $numpages,
    'current'         => $paged,
    'show_all'        => False,
    'end_size'        => 1,
    'mid_size'        => $pagerange,
    'prev_next'       => True,
    'prev_class'       => "nprev page-numbers",
    'next_class'       => "next page-numbers",
    'prev_text'       => '&larr;',
    'next_text'       => '&rarr;',
    'type'            => 'plain',
    'add_args'        => false,
    'add_fragment'    => ''
  );

  $paginate_links = paginate_links($pagination_args);

  if ($paginate_links) {
   /* echo "<div class='list-navigation'><div><nav class='custom-pagination'>";
    echo "<ul><li>Page " . $paged . " of ". $numpages . "&#8195;";
      echo $paginate_links;
      echo "</li></ul></nav></div></div>";*/

  	echo '<nav class="pagination">' . $paginate_links .
            /*<a href="#" class="prev page-numbers">&larr;</a>

            | <a href="#" class="page-numbers">1</a> |
            <a href="#" class="page-numbers current">2</a> |
            <a href="#" class="page-numbers">3</a> |
            <span class="page-skip">...</span>
            | <a href="#" class="page-numbers">20</a> |

            <a href="#" class="next page-numbers">&rarr;</a>*/
        '</nav>';


  }

}
