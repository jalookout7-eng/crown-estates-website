<?php
/**
 * Schema.org JSON-LD structured data.
 */
function ce_output_schema_markup() {
    // Sitewide: RealEstateAgent
    $org = [
        '@context' => 'https://schema.org',
        '@type'    => 'RealEstateAgent',
        'name'     => 'Crowns Estates',
        'url'      => home_url('/'),
        'email'    => 'info@crownsestates.co.uk',
        'description' => 'UK-registered real estate agency specialising in Saudi Arabian property investment.',
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    // Single property: RealEstateListing
    if (is_singular('ce_property')) {
        $post_id = get_the_ID();
        $price   = (float) get_field('ce_price_from', $post_id);
        $currency = get_field('ce_currency', $post_id) ?: 'SAR';
        $city_terms = get_the_terms($post_id, 'ce_city');
        $city = $city_terms ? $city_terms[0]->name : 'Saudi Arabia';
        $image = get_the_post_thumbnail_url($post_id, 'large') ?: '';

        $listing = [
            '@context'    => 'https://schema.org',
            '@type'       => 'RealEstateListing',
            'name'        => get_the_title($post_id),
            'description' => get_field('ce_short_description', $post_id) ?: '',
            'url'         => get_permalink($post_id),
            'datePosted'  => get_the_date('c', $post_id),
        ];
        if ($image) $listing['image'] = $image;
        if ($price) {
            $listing['offers'] = [
                '@type'         => 'Offer',
                'price'         => $price,
                'priceCurrency' => $currency,
            ];
        }
        $listing['contentLocation'] = [
            '@type' => 'Place',
            'name'  => $city . ', Saudi Arabia',
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($listing, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'ce_output_schema_markup', 1);
