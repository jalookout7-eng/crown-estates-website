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

// Enqueue assets
function ce_enqueue_assets() {
    wp_enqueue_style('ce-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap',
        [], null
    );
    wp_enqueue_style('ce-style', get_stylesheet_uri(), ['ce-google-fonts'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'ce_enqueue_assets');
