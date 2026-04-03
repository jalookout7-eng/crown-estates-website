<?php
/**
 * Register ce_testimonial Custom Post Type.
 */
function ce_register_testimonial_cpt() {
    $labels = [
        'name'               => 'Testimonials',
        'singular_name'      => 'Testimonial',
        'menu_name'          => 'Testimonials',
        'add_new'            => 'Add New Testimonial',
        'add_new_item'       => 'Add New Testimonial',
        'edit_item'          => 'Edit Testimonial',
        'new_item'           => 'New Testimonial',
        'view_item'          => 'View Testimonial',
        'search_items'       => 'Search Testimonials',
        'not_found'          => 'No testimonials found',
        'not_found_in_trash' => 'No testimonials found in trash',
    ];

    $args = [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => false,
        'supports'      => ['title'],
        'menu_icon'     => 'dashicons-format-quote',
        'show_in_rest'  => true,
        'menu_position' => 6,
    ];

    register_post_type('ce_testimonial', $args);
}
add_action('init', 'ce_register_testimonial_cpt');
