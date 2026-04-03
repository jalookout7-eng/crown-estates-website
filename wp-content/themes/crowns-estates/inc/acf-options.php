<?php
/**
 * ACF Options Pages: Site Settings, Exchange Rates, Calculator.
 */
function ce_register_options_pages() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug'  => 'ce-site-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-generic',
        'position'   => 30,
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Exchange Rates',
        'menu_title'  => 'Exchange Rates',
        'parent_slug' => 'ce-site-settings',
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Calculator Settings',
        'menu_title'  => 'Calculator',
        'parent_slug' => 'ce-site-settings',
    ]);
}
add_action('acf/init', 'ce_register_options_pages');

/**
 * Register ACF fields for options pages.
 */
function ce_register_options_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // Exchange Rates
    acf_add_local_field_group([
        'key'    => 'group_ce_exchange_rates',
        'title'  => 'Exchange Rates',
        'fields' => [
            [
                'key'           => 'field_ce_rate_gbp_to_sar',
                'label'         => 'GBP to SAR',
                'name'          => 'ce_rate_gbp_to_sar',
                'type'          => 'number',
                'step'          => 0.01,
                'default_value' => 4.68,
            ],
            [
                'key'           => 'field_ce_rate_usd_to_sar',
                'label'         => 'USD to SAR',
                'name'          => 'ce_rate_usd_to_sar',
                'type'          => 'number',
                'step'          => 0.01,
                'default_value' => 3.75,
            ],
            [
                'key'           => 'field_ce_rate_gbp_to_usd',
                'label'         => 'GBP to USD',
                'name'          => 'ce_rate_gbp_to_usd',
                'type'          => 'number',
                'step'          => 0.01,
                'default_value' => 1.27,
            ],
        ],
        'location' => [
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-exchange-rates'],
            ],
        ],
    ]);

    // Calculator Rates
    acf_add_local_field_group([
        'key'    => 'group_ce_calculator',
        'title'  => 'Calculator Settings',
        'fields' => [
            [
                'key'           => 'field_ce_calc_registration_fee',
                'label'         => 'Registration Fee (%)',
                'name'          => 'ce_calc_registration_fee',
                'type'          => 'number',
                'step'          => 0.1,
                'default_value' => 2.5,
            ],
            [
                'key'           => 'field_ce_calc_vat',
                'label'         => 'VAT (%)',
                'name'          => 'ce_calc_vat',
                'type'          => 'number',
                'step'          => 0.1,
                'default_value' => 5,
            ],
            [
                'key'           => 'field_ce_calc_agency_fee',
                'label'         => 'Agency Fee (%)',
                'name'          => 'ce_calc_agency_fee',
                'type'          => 'number',
                'step'          => 0.1,
                'default_value' => 2,
            ],
        ],
        'location' => [
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-calculator-settings'],
            ],
        ],
    ]);

    // General Site Settings
    acf_add_local_field_group([
        'key'    => 'group_ce_site_settings',
        'title'  => 'General Settings',
        'fields' => [
            [
                'key'   => 'field_ce_whatsapp_number',
                'label' => 'WhatsApp Number',
                'name'  => 'ce_whatsapp_number',
                'type'  => 'text',
                'instructions' => 'International format, e.g. 447000000000',
            ],
            [
                'key'           => 'field_ce_contact_email',
                'label'         => 'Contact Email',
                'name'          => 'ce_contact_email',
                'type'          => 'email',
                'default_value' => 'info@crownsestates.co.uk',
            ],
        ],
        'location' => [
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'ce-site-settings'],
            ],
        ],
    ]);
}
add_action('acf/init', 'ce_register_options_fields');
