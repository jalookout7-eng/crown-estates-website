<?php
/**
 * Register ce_city taxonomy.
 * Attached to both ce_property and ce_testimonial.
 */
function ce_register_city_taxonomy() {
    $labels = [
        'name'              => 'Cities',
        'singular_name'     => 'City',
        'search_items'      => 'Search Cities',
        'all_items'         => 'All Cities',
        'edit_item'         => 'Edit City',
        'update_item'       => 'Update City',
        'add_new_item'      => 'Add New City',
        'new_item_name'     => 'New City Name',
        'menu_name'         => 'Cities',
    ];

    $args = [
        'labels'            => $labels,
        'hierarchical'      => true,
        'rewrite'           => ['slug' => 'city'],
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ];

    register_taxonomy('ce_city', ['ce_property', 'ce_testimonial'], $args);
}
add_action('init', 'ce_register_city_taxonomy');
