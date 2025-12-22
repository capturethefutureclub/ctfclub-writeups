<?php
/**
 * My Theme functions and definitions
 *
 * @package My_Theme
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
function my_theme_setup() {
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-theme'),
    ));
}
add_action('after_setup_theme', 'my_theme_setup');

/**
 * Enqueue scripts and styles
 */
function my_theme_scripts() {
    wp_enqueue_style('my-theme-style', get_stylesheet_uri(), array(), '1.0.0');
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');

/**
 * Register widget areas
 */
function my_theme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'my-theme'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here.', 'my-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'my_theme_widgets_init');

/**
 * Custom comment callback
 */
function my_theme_comment($comment, $args, $depth) {
    ?>
    <div id="comment-<?php comment_ID(); ?>" <?php comment_class('mb-6 group'); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="bg-gray-50 rounded-2xl p-6 border border-gray-100 flex gap-4 transition-all duration-300 hover:shadow-md hover:border-modern-crimson/20">
            <div class="flex-shrink-0">
                <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'rounded-full border-2 border-white shadow-sm w-12 h-12')); ?>
            </div>
            
            <div class="flex-grow">
                <header class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="font-bold text-modern-dark text-lg font-serif">
                            <?php echo get_comment_author_link(); ?>
                        </h3>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider mt-1">
                            <?php
                                printf(
                                    /* translators: 1: date, 2: time */
                                    esc_html__('%1$s at %2$s', 'my-theme'),
                                    get_comment_date(),
                                    get_comment_time()
                                );
                            ?>
                        </div>
                    </div>
                    
                    <?php comment_reply_link(array_merge($args, array(
                        'add_below' => 'div-comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '',
                        'after'     => '',
                        'class'     => 'text-xs font-bold text-modern-crimson hover:text-modern-teal uppercase tracking-widest transition-colors opacity-0 group-hover:opacity-100'
                    ))); ?>
                </header>

                <div class="comment-content text-gray-600 leading-relaxed text-sm">
                    <?php if ('0' == $comment->comment_approved) : ?>
                        <p class="text-modern-gold italic mb-2 text-xs font-bold"><?php esc_html_e('Your comment is awaiting moderation.', 'my-theme'); ?></p>
                    <?php endif; ?>
                    <?php comment_text(); ?>
                </div>
            </div>
        </article>
    <?php
}

/**
 * Get post image URL based on title or author
 */
function my_theme_get_post_image_url() {
    $title = get_the_title();
    $author = get_the_author();
    $image_name = '';

    if (strpos($title, 'Unraveling') !== false) {
        $image_name = 'Unraveling the Festive Threads.png';
    } elseif (strpos($title, 'Cocoa Supplies') !== false) {
        $image_name = 'Where Did All the Cocoa Supplies Go.png';
    } elseif (strpos($title, 'Festival Lights') !== false) {
        $image_name = 'The Festival Lights.png';
    } elseif (strpos($author, 'Lottie') !== false || strpos($title, 'Sweet Riddle') !== false) {
        $image_name = 'Lottie Thimblewhisk.png';
    }

    if ($image_name) {
        return get_template_directory_uri() . '/images/' . $image_name;
    }
    
    return '';
}
