<?php
// wp-content/mu-plugins/crowns-estates-core/taxonomy-city.php
defined('ABSPATH') || exit;

function ce_register_city_taxonomy() {
    $labels = [
        'name'          => 'Cities',
        'singular_name' => 'City',
        'search_items'  => 'Search Cities',
        'all_items'     => 'All Cities',
        'edit_item'     => 'Edit City',
        'add_new_item'  => 'Add New City',
        'menu_name'     => 'Cities',
    ];
    register_taxonomy('ce_city', ['ce_property', 'ce_testimonial'], [
        'labels'       => $labels,
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => ['slug' => 'city'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'ce_register_city_taxonomy');
