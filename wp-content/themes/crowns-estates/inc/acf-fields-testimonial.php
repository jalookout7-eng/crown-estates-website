<?php
/**
 * ACF field group: Testimonial Details.
 * Location: ce_testimonial post type.
 */
function ce_register_testimonial_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_ce_testimonial',
        'title'    => 'Testimonial Details',
        'fields'   => [
            [
                'key'   => 'field_ce_client_name',
                'label' => 'Client Name',
                'name'  => 'ce_client_name',
                'type'  => 'text',
                'instructions' => 'Can be anonymised, e.g. "UK Investor"',
            ],
            [
                'key'   => 'field_ce_client_location',
                'label' => 'Location',
                'name'  => 'ce_client_location',
                'type'  => 'text',
                'instructions' => 'e.g. "London, UK"',
            ],
            [
                'key'   => 'field_ce_quote',
                'label' => 'Quote',
                'name'  => 'ce_quote',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            [
                'key'           => 'field_ce_rating',
                'label'         => 'Rating',
                'name'          => 'ce_rating',
                'type'          => 'number',
                'min'           => 1,
                'max'           => 5,
                'default_value' => 5,
            ],
            [
                'key'   => 'field_ce_google_review_link',
                'label' => 'Google Review Link',
                'name'  => 'ce_google_review_link',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_ce_testimonial_featured',
                'label' => 'Featured',
                'name'  => 'ce_testimonial_featured',
                'type'  => 'true_false',
                'ui'    => 1,
                'instructions' => 'Show on homepage.',
            ],
            [
                'key'            => 'field_ce_testimonial_date',
                'label'          => 'Review Date',
                'name'           => 'ce_testimonial_date',
                'type'           => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format'  => 'Y-m-d',
            ],
        ],
        'location' => [
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'ce_testimonial'],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
    ]);
}
add_action('acf/init', 'ce_register_testimonial_fields');
