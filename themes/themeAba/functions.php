<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

/*
 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */

define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
require_once dirname( __FILE__ ) . '/inc/options-framework.php';

// Loads options.php from child or parent theme
$optionsfile = locate_template( 'options.php' );
load_template( $optionsfile );

/*
 * This is an example of how to add custom scripts to the options panel.
 * This one shows/hides the an option when a checkbox is clicked.
 *
 * You can delete it if you not using that option
 */
add_action( 'optionsframework_custom_scripts', 'optionsframework_custom_scripts' );

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function() {

    jQuery('#example_showhidden').click(function() {
        jQuery('#section-example_text_hidden').fadeToggle(400);
    });

    if (jQuery('#example_showhidden:checked').val() !== undefined) {
        jQuery('#section-example_text_hidden').show();
    }

});
</script>



<?php
}

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('blog', 782, 450, true); // Large Thumbnail
    add_image_size('large', 700, '', true);
    add_image_size('medium', 450, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('carThumb', 480, 286, true); // Custom Thumbnail Size call using the_post_thumbnail('car');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('html5blank', get_template_directory() . '/languages');
}

/*------------------------------------*\
	Functions
\*------------------------------------*/

// HTML5 Blank navigation
function html5blank_nav()
{
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => '',
		'container'       => 'div',
		'container_class' => 'menu-{menu slug}-container',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
		)
	);
}

function footer_nav()
{
    wp_nav_menu(
    array(
        'theme_location'  => 'footer-menu',
        'menu'            => '',
        'container'       => 'div',
        'container_class' => 'menu-{menu slug}-container',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'depth'           => 0,
        'walker'          => ''
        )
    );
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

    	wp_register_script('conditionizr', get_template_directory_uri() . '/js/lib/conditionizr-4.3.0.min.js', array(), '4.3.0'); // Conditionizr
        wp_enqueue_script('conditionizr'); // Enqueue it!

        wp_register_script('modernizr', get_template_directory_uri() . '/js/lib/modernizr-2.7.1.min.js', array(), '2.7.1'); // Modernizr
        wp_enqueue_script('modernizr'); // Enqueue it!

        wp_register_script('swiper', get_template_directory_uri() . '/js/vendor/swiper.min.js', array('jquery'), '2.7.1', true); // Modernizr
        wp_enqueue_script('swiper'); // Enqueue it!

        wp_register_script('ABAscripts', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true); // Custom scripts
        wp_enqueue_script('ABAscripts'); // Enqueue it!

        wp_register_script('ABAmenu', get_template_directory_uri() . '/js/vendor/jquery.slicknav.min.js', array('jquery'), '1.0.0', true); // Custom scripts
        wp_enqueue_script('ABAmenu'); // Enqueue it!

        wp_register_script('material', get_template_directory_uri() . '/js/vendor/material.min.js', array(), '1.1.1', true); // Modernizr
        wp_enqueue_script('material'); // Enqueue it!
    }
}

function is_post_type($type){
    global $wp_query;
    if($type == get_post_type($wp_query->post->ID)) return true;
    return false;
}
// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_post_type('autos')) {
        wp_register_script('ABAgrid', get_template_directory_uri() . '/js/vendor/grid.js', array('jquery'), '1.0.0', true); // Conditional script(s)
        wp_enqueue_script('ABAgrid'); // Enqueue it!
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
    wp_register_style('aba', get_template_directory_uri() . '/style.css', array(), '1.2', 'all');
    wp_enqueue_style('aba'); // Enqueue it!

    wp_register_style('responsive', get_template_directory_uri() . '/css/responsive.css', array(), '1.0', 'all');
    wp_enqueue_style('responsive'); // Enqueue it!

    wp_register_style('animate', get_template_directory_uri() . '/css/animate.min.css', array(), '1.0', 'all');
    wp_enqueue_style('animate'); // Enqueue it!

    wp_register_style('swiper', get_template_directory_uri() . '/css/vendor/swiper.min.css', array(), '1.0', 'all');
    wp_enqueue_style('swiper'); // Enqueue it!

    wp_register_style('material', get_template_directory_uri() . '/css/vendor/material.css', array(), '3.3.1', 'all');
    wp_enqueue_style('material'); // Enqueue it!
}

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
        'footer-menu' => __('Footer Menu', 'html5blank'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'html5blank') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Top', 'html5blank'),
        'description' => __('Espacio para seleccionar idioma', 'html5blank'),
        'id' => 'widget-top',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 3
    register_sidebar(array(
        'name' => __('Widget Pre Footer', 'html5blank'),
        'description' => __('Zona antes del footer', 'html5blank'),
        'id' => 'widget-footer',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 4
    register_sidebar(array(
        'name' => __('Widget Form', 'html5blank'),
        'description' => __('Formulario de reserva', 'html5blank'),
        'id' => 'widget-form',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
     // Define Sidebar Widget Area 5
    register_sidebar(array(
        'name' => __('Widget Contact', 'html5blank'),
        'description' => __('informacion de contacto', 'html5blank'),
        'id' => 'widget-contact',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    // Define Sidebar Widget Area 6
    register_sidebar(array(
        'name' => __('Widget Servicios', 'html5blank'),
        'description' => __('servicios', 'html5blank'),
        'id' => 'widget-services',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function html5blankcomments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
		?>
	</div>

	<?php comment_text() ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('init', 'create_post_type_autos'); // Add our HTML5 Blank Custom Post Type
add_action('init', 'create_news_ABA'); // Add our HTML5 Blank Custom Post Type
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [html5_shortcode_demo] [html5_shortcode_demo_2] Here's the page title! [/html5_shortcode_demo_2] [/html5_shortcode_demo]

/*------------------------------------*\
	Custom Post Types
\*------------------------------------*/

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_autos()
{
    register_taxonomy_for_object_type('category', 'autos'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'autos');
    register_post_type('autos', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('[:es]Autos de ABA Rent a Car[:en]ABA Rent a Car vehicles[:pt]Carros AB Rent a Car[:]', 'html5blank'), // Rename these to suit
            'singular_name' => __('Auto de ABA', 'html5blank'),
            'add_new' => __('Nuevo Auto', 'html5blank'),
            'add_new_item' => __('Agregar nuevo Auto', 'html5blank'),
            'edit' => __('Editar', 'html5blank'),
            'edit_item' => __('Editar el auto', 'html5blank'),
            'new_item' => __('Nuevo auto', 'html5blank'),
            'view' => __('Ver auto', 'html5blank'),
            'view_item' => __('Ver auto ABA', 'html5blank'),
            'search_items' => __('Buscar auto', 'html5blank'),
            'not_found' => __('No se encontro el auto', 'html5blank'),
            'not_found_in_trash' => __('No se encontro el auto en la basura', 'html5blank'),
            'menu_name'          => 'Autos'
        ),
        'public' => true,
        'publicly_queryable' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true // Allows export in Tools > Export
    ));
}
/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_car_cat() {
  // Add new "Locations" taxonomy to Posts
  register_taxonomy('abacategory', 'autos', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Categoría autos', 'taxonomy general name' ),
      'singular_name' => _x( 'Categoría Auto', 'taxonomy singular name' ),
      'search_items' =>  __( 'Buscar categoría auto' ),
      'all_items' => __( 'todas las categorias de autos' ),
      'parent_item' => __( 'Categoría superior' ),
      'parent_item_colon' => __( 'Categoría superior:' ),
      'edit_item' => __( 'Editar categoría' ),
      'update_item' => __( 'Actualizar categoría' ),
      'add_new_item' => __( 'Agregar nueva categoría de autos' ),
      'new_item_name' => __( 'Nuevo nombre de categoría de autos' ),
      'menu_name' => __( 'Categoría de autos' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'flota', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/locations/"
      'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
    ),
  ));
}
add_action( 'init', 'add_custom_car_cat', 0 );

function abacategory_add_meta_fields( $taxonomy ) {
    ?>
    <div class="form-field term-group">
        <label for="days2"><?php _e( '2 días (500 km)', 'price' ); ?></label>
        <input type="text" id="days2" name="days2" />
    </div>
    <div class="form-field term-group">
        <label for="days3"><?php _e( '3 días (700 km)', 'price' ); ?></label>
        <input type="text" id="days3" name="days3" />
    </div>
    <div class="form-field term-group">
        <label for="days4"><?php _e( '4 días (800 km)', 'price' ); ?></label>
        <input type="text" id="days4" name="days4" />
    </div>
    <div class="form-field term-group">
        <label for="days5"><?php _e( '5 días (1000 km)', 'price' ); ?></label>
        <input type="text" id="days5" name="days5" />
    </div>
    <div class="form-field term-group">
        <label for="days6"><?php _e( '6 días (1200 km)', 'price' ); ?></label>
        <input type="text" id="days6" name="days6" />
    </div>
    <div class="form-field term-group">
        <label for="days7"><?php _e( '7 días (1400 km)', 'price' ); ?></label>
        <input type="text" id="days7" name="days7" />
    </div>
    <div class="form-field term-group">
        <label for="kmexc"><?php _e( 'Km Exc', 'price' ); ?></label>
        <input type="text" id="kmexc" name="kmexc" />
    </div>
    <?php
}
add_action( 'abacategory_add_form_fields', 'abacategory_add_meta_fields', 10, 2 );



function abacategory_edit_meta_fields( $term, $taxonomy ) {
    $days2 = get_term_meta( $term->term_id, 'days2', true );
    $days3 = get_term_meta( $term->term_id, 'days3', true );
    $days4 = get_term_meta( $term->term_id, 'days4', true );
    $days5 = get_term_meta( $term->term_id, 'days5', true );
    $days6 = get_term_meta( $term->term_id, 'days6', true );
    $days7 = get_term_meta( $term->term_id, 'days7', true );
    $kmexc = get_term_meta( $term->term_id, 'kmexc', true );
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="days2"><?php _e( '2 días (500 km)', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="days2" name="days2" value="<?php echo $days2; ?>" />
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="days3"><?php _e( '3 días (700 km)', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="days3" name="days3" value="<?php echo $days3; ?>" />
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="days4"><?php _e( '4 días (800 km)', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="days4" name="days4" value="<?php echo $days4; ?>" />
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="days5"><?php _e( '5 días (1000 km)', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="days5" name="days5" value="<?php echo $days5; ?>" />
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="days6"><?php _e( '6 días (1200 km)', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="days6" name="days6" value="<?php echo $days6; ?>" />
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="days7"><?php _e( '7 días (1400 km)', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="days7" name="days7" value="<?php echo $days7; ?>" />
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="kmexc"><?php _e( 'Km Exc', 'price' ); ?></label>
        </th>
        <td>
            <input type="text" id="kmexc" name="kmexc" value="<?php echo $kmexc; ?>" />
        </td>
    </tr>
    <?php
}
add_action( 'abacategory_edit_form_fields', 'abacategory_edit_meta_fields', 10, 2 );


function abacategory_save_taxonomy_meta( $term_id, $tag_id ) {
    if( isset( $_POST['days2'] ) ) {
        update_term_meta( $term_id, 'days2', esc_attr( $_POST['days2'] ) );
    }
    if( isset( $_POST['days3'] ) ) {
        update_term_meta( $term_id, 'days3', esc_attr( $_POST['days3'] ) );
    }
    if( isset( $_POST['days4'] ) ) {
        update_term_meta( $term_id, 'days4', esc_attr( $_POST['days4'] ) );
    }
    if( isset( $_POST['days5'] ) ) {
        update_term_meta( $term_id, 'days5', esc_attr( $_POST['days5'] ) );
    }
    if( isset( $_POST['days6'] ) ) {
        update_term_meta( $term_id, 'days6', esc_attr( $_POST['days6'] ) );
    }
    if( isset( $_POST['days7'] ) ) {
        update_term_meta( $term_id, 'days7', esc_attr( $_POST['days7'] ) );
    }
    if( isset( $_POST['kmexc'] ) ) {
        update_term_meta( $term_id, 'kmexc', esc_attr( $_POST['kmexc'] ) );
    }
}
add_action( 'created_abacategory', 'abacategory_save_taxonomy_meta', 10, 2 );
add_action( 'edited_abacategory', 'abacategory_save_taxonomy_meta', 10, 2 );

function create_news_ABA()
{
    register_taxonomy_for_object_type('category', 'Noticias-ABA'); // Register Taxonomies for Category
    register_post_type('news-ABA', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('[:es]Noticias[:en]News[:pt]Notícia[:]', 'html5blank'), // Rename these to suit
            'singular_name' => __('Noticias ABA', 'html5blank'),
            'add_new' => __('Agregar Noticia', 'html5blank'),
            'add_new_item' => __('Agregar una nueva Noticia', 'html5blank'),
            'edit' => __('Editar', 'html5blank'),
            'edit_item' => __('Editar Noticia', 'html5blank'),
            'new_item' => __('Nueva Noticia', 'html5blank'),
            'view' => __('Ver Noticia', 'html5blank'),
            'view_item' => __('Ver Noticia ABA', 'html5blank'),
            'search_items' => __('Buscar Noticia', 'html5blank'),
            'not_found' => __('No se encontro ninguna Noticia', 'html5blank'),
            'not_found_in_trash' => __('No se encontro ningúna Noticia en la basura', 'html5blank'),
            'parent_item_colon'  => ''
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'menu_position' => 2,
        'supports' => array(
            'title',
            'editor',
            'thumbnail'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'category'
        ) // Add Category and Post Tags support
    ));
}

/*------------------------------------*\
	ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}

//Making jQuery Google API
function modify_jquery() {
    if (!is_admin()) {
        // comment out the next two lines to load the local copy of jQuery
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', false, '1.11.0');
        wp_enqueue_script('jquery');
    }
}
add_action('init', 'modify_jquery');

function add_this_script_footer(){
        if (is_post_type('autos')) {
    
            echo "<script type='text/javascript'>
                $(function() {
                    Grid.init();
                });
            </script>";

        }
    } 
add_action('wp_footer', 'add_this_script_footer', 100);

/*function allow_button_onclick_mce($settings) {
    $settings['extended_valid_elements'] =  "a[rel|rev|charset|hreflang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick]";
    return $settings;
  }
  add_filter('textarea_before_init', 'allow_button_onclick_mce');*/

  /*add_filter( 'gform_submit_button_2', 'add_ga_onclick', 10, 2 );
function add_ga_onclick( $button, $form ) {
    $dom = new DOMDocument();
    $dom->loadHTML( $button );
    $input = $dom->getElementsByTagName( 'input' )->item(0);
    $onclick = $input->getAttribute( 'onclick' );
    $permalink = get_permalink();
    $onclick .= " return gtag_report_conversion('https://abarentacar.com.ar/#book');";
    $input->setAttribute( 'onclick', $onclick );
    return $dom->saveHtml( $input );
}*/

// filter the Gravity Forms button type
add_filter("gform_submit_button_2", "form_submit_button", 10, 2);
function form_submit_button($button, $form){
    // The following line is from the Gravity Forms documentation - it doesn't include your custom button text
    // return "<button class='button' id='gform_submit_button_{$form["id"]}'>'Submit'</button>";
    // This includes your custom button text:
    return "<button class='button' onclick='return gtag_report_conversion('https://abarentacar.com.ar/#book')' id='gform_submit_button_{$form["id"]}'>{$form['button']['text']}</button>";
}
// Oops this strips important stuff
?>