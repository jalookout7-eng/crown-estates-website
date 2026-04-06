<?php
// wp-content/themes/crowns-estates/inc/admin-styles.php
defined('ABSPATH') || exit;

add_action('admin_enqueue_scripts', function (): void {
    wp_enqueue_style(
        'ce-admin-ui',
        get_template_directory_uri() . '/css/admin.css',
        [],
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style(
        'ce-admin-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600&display=swap',
        [],
        null
    );
});

// Hide the WP logo in the admin bar, show CE wordmark instead
add_action('admin_bar_menu', function (WP_Admin_Bar $bar): void {
    $bar->remove_node('wp-logo');
}, 999);

add_action('admin_bar_menu', function (WP_Admin_Bar $bar): void {
    $bar->add_node([
        'id'    => 'ce-wordmark',
        'title' => '<span style="font-family:\'Playfair Display\',serif;font-size:14px;font-weight:600;color:#C4973A;letter-spacing:0.02em">Crowns Estates</span>',
        'href'  => admin_url(),
        'meta'  => ['class' => 'ce-wordmark-node'],
    ]);
}, 999);
