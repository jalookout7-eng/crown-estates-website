<?php
// wp-content/mu-plugins/crowns-estates-core/currency-helpers.php
defined('ABSPATH') || exit;

/**
 * Returns exchange rates from ACF options.
 * Keys: GBP, SAR, USD (all relative to GBP as base = 1).
 */
function ce_get_exchange_rates(): array {
    $sar = (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_sar', 'option') : 0) ?: 4.68;
    $usd = (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_usd', 'option') : 0) ?: 1.27;
    $updated = function_exists('get_field') ? (get_field('ce_rates_last_updated', 'option') ?: '') : '';
    return [
        'GBP'     => 1,
        'SAR'     => $sar,
        'USD'     => $usd,
        'updated' => $updated,
    ];
}

/**
 * Format a price with currency symbol.
 */
function ce_format_price(float $amount, string $currency): string {
    $symbols = ['GBP' => '£', 'SAR' => 'SAR ', 'USD' => '$'];
    $symbol  = $symbols[$currency] ?? $currency . ' ';
    return $symbol . number_format($amount, 0);
}

/**
 * Render a property price span with data attributes for JS currency conversion.
 * Server-side always outputs native currency — no cookie reads.
 */
function ce_display_price(int $post_id = 0): string {
    $post_id = $post_id ?: get_the_ID();
    $price   = (float) get_field('ce_price_from', $post_id);
    $native  = get_field('ce_currency', $post_id) ?: 'SAR';

    if (!$price) {
        return '<span class="ce-price" data-price="0" data-currency="">Price on request</span>';
    }

    return sprintf(
        '<span class="ce-price" data-price="%s" data-currency="%s">%s</span>',
        esc_attr($price),
        esc_attr($native),
        esc_html(ce_format_price($price, $native))
    );
}

/**
 * REST GET /wp-json/ce/v1/rates
 * Cached via transient for 1 hour. Cache-Control header set.
 */
function ce_register_rates_endpoint(): void {
    register_rest_route('ce/v1', '/rates', [
        'methods'             => 'GET',
        'callback'            => 'ce_rest_get_rates',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_register_rates_endpoint');

function ce_rest_get_rates(): WP_REST_Response {
    $cached = get_transient('ce_exchange_rates');
    if ($cached !== false) {
        $response = new WP_REST_Response($cached, 200);
        $response->header('Cache-Control', 'max-age=3600');
        return $response;
    }
    $rates = ce_get_exchange_rates();
    set_transient('ce_exchange_rates', $rates, HOUR_IN_SECONDS);
    $response = new WP_REST_Response($rates, 200);
    $response->header('Cache-Control', 'max-age=3600');
    return $response;
}

// Bust transient when exchange rates are saved in ACF options
add_action('acf/save_post', function ($post_id): void {
    if ($post_id === 'options') {
        delete_transient('ce_exchange_rates');
    }
});
