<?php
// wp-content/mu-plugins/crowns-estates-core/cpt-testimonial.php
defined('ABSPATH') || exit;

function ce_register_testimonial_cpt() {
    $labels = [
        'name'               => 'Testimonials',
        'singular_name'      => 'Testimonial',
        'menu_name'          => 'Testimonials',
        'add_new_item'       => 'Add New Testimonial',
        'edit_item'          => 'Edit Testimonial',
        'not_found'          => 'No testimonials found',
    ];
    register_post_type('ce_testimonial', [
        'labels'        => $labels,
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'supports'      => ['title'],
        'menu_icon'     => 'dashicons-format-quote',
        'show_in_rest'  => false,
        'menu_position' => 6,
    ]);
}
add_action('init', 'ce_register_testimonial_cpt');
