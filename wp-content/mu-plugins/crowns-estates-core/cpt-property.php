<?php
// wp-content/mu-plugins/crowns-estates-core/cpt-property.php
defined('ABSPATH') || exit;

function ce_register_property_cpt() {
    $labels = [
        'name'               => 'Properties',
        'singular_name'      => 'Property',
        'menu_name'          => 'Properties',
        'add_new'            => 'Add New Property',
        'add_new_item'       => 'Add New Property',
        'edit_item'          => 'Edit Property',
        'new_item'           => 'New Property',
        'view_item'          => 'View Property',
        'search_items'       => 'Search Properties',
        'not_found'          => 'No properties found',
        'not_found_in_trash' => 'No properties found in trash',
    ];
    register_post_type('ce_property', [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'properties'],
        'supports'      => ['title', 'editor', 'thumbnail', 'revisions'],
        'menu_icon'     => 'dashicons-building',
        'show_in_rest'  => true,
        'menu_position' => 5,
    ]);
}
add_action('init', 'ce_register_property_cpt');
