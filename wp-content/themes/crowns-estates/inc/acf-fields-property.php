<?php
/**
 * ACF field group: Property Details.
 * Location: ce_property post type.
 */
function ce_register_property_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_ce_property',
        'title'    => 'Property Details',
        'fields'   => [
            [
                'key'   => 'field_ce_developer',
                'label' => 'Developer',
                'name'  => 'ce_developer',
                'type'  => 'text',
                'instructions' => 'e.g. ROSHN, Dar Global',
            ],
            [
                'key'     => 'field_ce_developer_badge',
                'label'   => 'Developer Badge',
                'name'    => 'ce_developer_badge',
                'type'    => 'select',
                'choices' => [
                    'none'            => 'None',
                    'verified'        => 'Verified Developer',
                    'track_record'    => 'Track Record',
                    'premium_partner' => 'Premium Partner',
                ],
                'default_value' => 'none',
            ],
            [
                'key'   => 'field_ce_price_from',
                'label' => 'Price From',
                'name'  => 'ce_price_from',
                'type'  => 'number',
            ],
            [
                'key'     => 'field_ce_currency',
                'label'   => 'Currency',
                'name'    => 'ce_currency',
                'type'    => 'select',
                'choices' => [
                    'SAR' => 'SAR',
                    'GBP' => 'GBP',
                    'USD' => 'USD',
                ],
                'default_value' => 'SAR',
            ],
            [
                'key'     => 'field_ce_property_type',
                'label'   => 'Property Type',
                'name'    => 'ce_property_type',
                'type'    => 'select',
                'choices' => [
                    'apartment'  => 'Apartment',
                    'villa'      => 'Villa',
                    'commercial' => 'Commercial',
                ],
            ],
            [
                'key'     => 'field_ce_status',
                'label'   => 'Status',
                'name'    => 'ce_status',
                'type'    => 'select',
                'choices' => [
                    'off-plan'          => 'Off-Plan',
                    'under-construction' => 'Under Construction',
                    'ready'             => 'Ready',
                ],
            ],
            [
                'key'          => 'field_ce_completion_date',
                'label'        => 'Completion Date',
                'name'         => 'ce_completion_date',
                'type'         => 'date_picker',
                'display_format' => 'F Y',
                'return_format'  => 'Y-m-d',
            ],
            [
                'key'   => 'field_ce_bedrooms',
                'label' => 'Bedrooms',
                'name'  => 'ce_bedrooms',
                'type'  => 'text',
                'instructions' => 'e.g. "2-3" or "4"',
            ],
            [
                'key'   => 'field_ce_size_sqm',
                'label' => 'Size (Sq.M.)',
                'name'  => 'ce_size_sqm',
                'type'  => 'number',
            ],
            [
                'key'   => 'field_ce_is_freehold',
                'label' => 'Is Freehold',
                'name'  => 'ce_is_freehold',
                'type'  => 'true_false',
                'ui'    => 1,
            ],
            [
                'key'   => 'field_ce_short_description',
                'label' => 'Short Description',
                'name'  => 'ce_short_description',
                'type'  => 'textarea',
                'rows'  => 3,
                'instructions' => 'Used on listing cards.',
            ],
            [
                'key'   => 'field_ce_full_description',
                'label' => 'Full Description',
                'name'  => 'ce_full_description',
                'type'  => 'wysiwyg',
                'instructions' => 'Used on single property page.',
            ],
            [
                'key'   => 'field_ce_gallery',
                'label' => 'Gallery',
                'name'  => 'ce_gallery',
                'type'  => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'   => 'field_ce_brochure_pdf',
                'label' => 'Brochure PDF',
                'name'  => 'ce_brochure_pdf',
                'type'  => 'file',
                'return_format' => 'url',
                'mime_types'    => 'pdf',
            ],
            [
                'key'   => 'field_ce_brochure_gated',
                'label' => 'Gate Brochure Download',
                'name'  => 'ce_brochure_gated',
                'type'  => 'true_false',
                'ui'    => 1,
                'instructions' => 'If enabled, users must enter their email to download the brochure.',
            ],
            [
                'key'   => 'field_ce_featured',
                'label' => 'Featured',
                'name'  => 'ce_featured',
                'type'  => 'true_false',
                'ui'    => 1,
                'instructions' => 'Show on homepage.',
            ],
            [
                'key'   => 'field_ce_map_embed',
                'label' => 'Map Embed URL',
                'name'  => 'ce_map_embed',
                'type'  => 'url',
            ],
        ],
        'location' => [
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'ce_property'],
            ],
        ],
        'position' => 'normal',
        'style'    => 'default',
    ]);
}
add_action('acf/init', 'ce_register_property_fields');
