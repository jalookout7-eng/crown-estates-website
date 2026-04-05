<?php
// wp-content/themes/crowns-estates/inc/login-page.php
defined('ABSPATH') || exit;

// Enqueue custom login CSS
add_action('login_enqueue_scripts', function (): void {
    wp_enqueue_style(
        'ce-login',
        get_template_directory_uri() . '/css/login.css',
        [],
        '1.0.0'
    );
    // Google Fonts
    wp_enqueue_style(
        'ce-login-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Playfair+Display:wght@600&display=swap',
        [],
        null
    );
});

// Replace WordPress logo with Crowns Estates wordmark
add_filter('login_headertext', fn() => 'Crowns Estates');

// Point logo link to the homepage instead of wordpress.org
add_filter('login_headerurl', fn() => home_url('/'));
