<?php
// wp-content/themes/crowns-estates/inc/acf-options.php
defined('ABSPATH') || exit;

add_action('acf/init', function (): void {
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

    acf_add_options_sub_page(['page_title' => 'Exchange Rates',  'menu_title' => 'Exchange Rates',  'menu_slug' => 'acf-options-exchange-rates',   'parent_slug' => 'ce-site-settings']);
    acf_add_options_sub_page(['page_title' => 'Calculator',      'menu_title' => 'Calculator',      'menu_slug' => 'acf-options-calculator',        'parent_slug' => 'ce-site-settings']);
    acf_add_options_sub_page(['page_title' => 'Content & Legal', 'menu_title' => 'Content & Legal', 'menu_slug' => 'acf-options-content-legal',     'parent_slug' => 'ce-site-settings']);
});

add_action('acf/init', function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // General Settings
    acf_add_local_field_group([
        'key'    => 'group_ce_general',
        'title'  => 'General Settings',
        'fields' => [
            ['key' => 'field_ce_whatsapp_number',           'label' => 'WhatsApp Number',           'name' => 'ce_whatsapp_number',           'type' => 'text', 'instructions' => 'International format e.g. +447700900000'],
            ['key' => 'field_ce_admin_notification_email',  'label' => 'Admin Notification Email',  'name' => 'ce_admin_notification_email',  'type' => 'email'],
            ['key' => 'field_ce_digest_recipient_email',    'label' => 'Digest Recipient Email',    'name' => 'ce_digest_recipient_email',    'type' => 'email'],
            ['key' => 'field_ce_digest_enabled',            'label' => 'Digest Enabled',            'name' => 'ce_digest_enabled',            'type' => 'true_false', 'default_value' => 1],
            ['key' => 'field_ce_digest_time',              'label' => 'Digest Time (UTC)',          'name' => 'ce_digest_time',               'type' => 'text', 'default_value' => '08:00', 'instructions' => 'Time to send daily digest e.g. 08:00'],
            ['key' => 'field_ce_office_address',            'label' => 'Office Address',            'name' => 'ce_office_address',            'type' => 'textarea'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'ce-site-settings']]],
    ]);

    // Exchange Rates
    acf_add_local_field_group([
        'key'    => 'group_ce_exchange_rates',
        'title'  => 'Exchange Rates',
        'fields' => [
            ['key' => 'field_ce_rate_gbp_to_sar',    'label' => 'GBP to SAR', 'name' => 'ce_rate_gbp_to_sar',    'type' => 'number', 'step' => 0.01, 'default_value' => 4.68],
            ['key' => 'field_ce_rate_gbp_to_usd',    'label' => 'GBP to USD', 'name' => 'ce_rate_gbp_to_usd',    'type' => 'number', 'step' => 0.01, 'default_value' => 1.27],
            ['key' => 'field_ce_rates_last_updated', 'label' => 'Rates Last Updated', 'name' => 'ce_rates_last_updated', 'type' => 'date_picker'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-exchange-rates']]],
    ]);

    // Calculator
    acf_add_local_field_group([
        'key'    => 'group_ce_calculator',
        'title'  => 'Calculator Settings',
        'fields' => [
            ['key' => 'field_ce_calc_registration_fee', 'label' => 'Registration Fee (%)', 'name' => 'ce_calc_registration_fee', 'type' => 'number', 'step' => 0.1, 'default_value' => 2.5],
            ['key' => 'field_ce_calc_vat',              'label' => 'VAT (%)',               'name' => 'ce_calc_vat',              'type' => 'number', 'step' => 0.1, 'default_value' => 5],
            ['key' => 'field_ce_calc_agency_fee',       'label' => 'Agency Fee (%)',        'name' => 'ce_calc_agency_fee',       'type' => 'number', 'step' => 0.1, 'default_value' => 2],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-calculator']]],
    ]);

    // Content & Legal
    acf_add_local_field_group([
        'key'    => 'group_ce_content_legal',
        'title'  => 'Content & Legal',
        'fields' => [
            ['key' => 'field_ce_email_from_name',       'label' => 'Email From Name',       'name' => 'ce_email_from_name',       'type' => 'text',     'default_value' => 'Crowns Estates'],
            ['key' => 'field_ce_email_from_address',    'label' => 'Email From Address',    'name' => 'ce_email_from_address',    'type' => 'email',    'default_value' => 'info@crownsestates.co.uk'],
            ['key' => 'field_ce_email_reply_to',        'label' => 'Email Reply-To',        'name' => 'ce_email_reply_to',        'type' => 'email'],
            ['key' => 'field_ce_gtm_container_id',      'label' => 'GTM Container ID',      'name' => 'ce_gtm_container_id',      'type' => 'text',     'instructions' => 'e.g. GTM-XXXXXXX'],
            ['key' => 'field_ce_ga4_measurement_id',    'label' => 'GA4 Measurement ID',    'name' => 'ce_ga4_measurement_id',    'type' => 'text',     'instructions' => 'e.g. G-XXXXXXXXXX'],
            ['key' => 'field_ce_trust_bar_1',           'label' => 'Trust Bar Text 1',      'name' => 'ce_trust_bar_1',           'type' => 'text',     'default_value' => '20 Years in Saudi Arabia'],
            ['key' => 'field_ce_trust_bar_2',           'label' => 'Trust Bar Text 2',      'name' => 'ce_trust_bar_2',           'type' => 'text',     'default_value' => 'British Expat Expertise'],
            ['key' => 'field_ce_trust_bar_3',           'label' => 'Trust Bar Text 3',      'name' => 'ce_trust_bar_3',           'type' => 'text',     'default_value' => 'End-to-End Investor Support'],
            ['key' => 'field_ce_footer_disclaimer',     'label' => 'Footer Disclaimer',     'name' => 'ce_footer_disclaimer',     'type' => 'textarea', 'default_value' => 'Crowns Estates is not regulated by the FCA. Information on this website does not constitute financial advice. Please seek independent advice before making investment decisions.'],
            ['key' => 'field_ce_property_disclaimer',   'label' => 'Property Disclaimer',   'name' => 'ce_property_disclaimer',   'type' => 'textarea', 'default_value' => 'Prices, specifications, and completion dates are indicative and subject to change.'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-content-legal']]],
    ]);
});
