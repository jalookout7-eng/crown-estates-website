<?php
/**
 * Crowns Estates Theme Functions
 */

// Theme setup
function ce_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    register_nav_menus([
        'primary' => __('Primary Menu', 'crowns-estates'),
        'footer'  => __('Footer Menu', 'crowns-estates'),
    ]);
}
add_action('after_setup_theme', 'ce_theme_setup');

// Widget areas
function ce_widgets_init() {
    register_sidebar([
        'name'          => __('Blog Sidebar', 'crowns-estates'),
        'id'            => 'blog-sidebar',
        'before_widget' => '<div class="ce-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="ce-widget__title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'ce_widgets_init');

// Includes
require get_template_directory() . '/inc/enqueue.php';
// Uncomment as files are created:
require get_template_directory() . '/inc/cpt-property.php';
require get_template_directory() . '/inc/cpt-testimonial.php';
require get_template_directory() . '/inc/taxonomy-city.php';
require get_template_directory() . '/inc/acf-fields-property.php';
require get_template_directory() . '/inc/acf-fields-testimonial.php';
require get_template_directory() . '/inc/acf-options.php';
require get_template_directory() . '/inc/currency-helpers.php';
require get_template_directory() . '/inc/enquiry-handler.php';
require get_template_directory() . '/inc/schema-markup.php';
// require get_template_directory() . '/inc/admin-dashboard.php';
require get_template_directory() . '/inc/ga4-tracking.php';
