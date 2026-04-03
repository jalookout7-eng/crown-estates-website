<?php
/**
 * Multi-currency helper functions.
 *
 * IMPORTANT: Server-side rendering ALWAYS outputs the listing's native currency.
 * Currency conversion is handled entirely client-side by currency-toggle.js.
 * This ensures compatibility with WP Super Cache (no cookies read server-side).
 */

/**
 * Get exchange rates from ACF options.
 */
function ce_get_exchange_rates(): array {
    return [
        'GBP_SAR' => (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_sar', 'option') : 0) ?: 4.68,
        'USD_SAR'  => (float) (function_exists('get_field') ? get_field('ce_rate_usd_to_sar', 'option') : 0) ?: 3.75,
        'GBP_USD'  => (float) (function_exists('get_field') ? get_field('ce_rate_gbp_to_usd', 'option') : 0) ?: 1.27,
    ];
}

/**
 * Format a price with currency symbol.
 */
function ce_format_price(float $amount, string $currency): string {
    $symbols = ['GBP' => '£', 'SAR' => 'SAR ', 'USD' => '$'];
    $symbol = $symbols[$currency] ?? $currency . ' ';
    return $symbol . number_format($amount, 0);
}

/**
 * Display a property price in its NATIVE currency.
 * Outputs data attributes for JS client-side currency conversion.
 */
function ce_display_price(int $post_id = 0): string {
    $post_id = $post_id ?: get_the_ID();
    $price = (float) get_field('ce_price_from', $post_id);
    $native = get_field('ce_currency', $post_id) ?: 'SAR';

    if (!$price) {
        return '<span class="ce-price" data-price="0" data-currency="">Price on request</span>';
    }

    $formatted = ce_format_price($price, $native);
    return sprintf(
        '<span class="ce-price" data-price="%s" data-currency="%s">%s</span>',
        esc_attr($price),
        esc_attr($native),
        esc_html($formatted)
    );
}

/**
 * REST endpoint for currency rates.
 * JS currency-toggle.js fetches these and does all conversion client-side.
 */
function ce_rest_exchange_rates() {
    register_rest_route('ce/v1', '/rates', [
        'methods'             => 'GET',
        'callback'            => function () {
            return ce_get_exchange_rates();
        },
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_rest_exchange_rates');
