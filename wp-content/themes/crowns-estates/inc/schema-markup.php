<?php
// wp-content/themes/crowns-estates/inc/schema-markup.php
defined('ABSPATH') || exit;

add_action('wp_head', function (): void {
    $schemas = [];

    // ── Sitewide: RealEstateAgent ────────���─────────────────────────────────
    $office_address = function_exists('get_field') ? (get_field('ce_office_address', 'option') ?: '') : '';
    $agent = [
        '@context'    => 'https://schema.org',
        '@type'       => 'RealEstateAgent',
        'name'        => 'Crowns Estates',
        'url'         => home_url('/'),
        'email'       => 'info@crownsestates.co.uk',
        'description' => 'UK-registered real estate agency specialising in Saudi Arabian property investment.',
        'areaServed'  => 'Saudi Arabia',
    ];
    if ($office_address) {
        $agent['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $office_address];
    }
    $schemas[] = $agent;

    // ── Single Property: RealEstateListing ───────────��────────────────────
    if (is_singular('ce_property')) {
        $id         = get_the_ID();
        $price      = (float) get_field('ce_price_from', $id);
        $currency   = get_field('ce_currency', $id) ?: 'SAR';
        $city_terms = get_the_terms($id, 'ce_city');
        $city       = $city_terms ? $city_terms[0]->name : 'Saudi Arabia';
        $image      = get_the_post_thumbnail_url($id, 'large') ?: '';
        $floor_size = (float) get_field('ce_size_sqm', $id);
        $bedrooms   = (int) get_field('ce_bedrooms', $id);

        $listing = [
            '@context'        => 'https://schema.org',
            '@type'           => 'RealEstateListing',
            'name'            => get_the_title($id),
            'description'     => get_field('ce_short_description', $id) ?: '',
            'url'             => get_permalink($id),
            'datePosted'      => get_the_date('c', $id),
            'contentLocation' => ['@type' => 'Place', 'name' => "{$city}, Saudi Arabia"],
        ];
        if ($image)      $listing['image']           = $image;
        if ($floor_size) $listing['floorSize']        = ['@type' => 'QuantitativeValue', 'value' => $floor_size, 'unitCode' => 'MTK'];
        if ($bedrooms)   $listing['numberOfRooms']    = $bedrooms;
        if ($price)      $listing['offers']           = ['@type' => 'Offer', 'price' => $price, 'priceCurrency' => $currency];
        $schemas[] = $listing;
    }

    // ── Pages with Testimonials: Review ─────────��─────────────────────────
    if (is_front_page() || is_page('about')) {
        $testimonials = get_posts([
            'post_type'      => 'ce_testimonial',
            'posts_per_page' => 3,
            'meta_query'     => [['key' => 'ce_featured', 'value' => '1', 'compare' => '=']],
        ]);
        foreach ($testimonials as $t) {
            $rating = (int) get_field('ce_rating', $t->ID);
            $schemas[] = [
                '@context'     => 'https://schema.org',
                '@type'        => 'Review',
                'author'       => ['@type' => 'Person', 'name' => get_field('ce_client_name', $t->ID) ?: $t->post_title],
                'reviewBody'   => get_field('ce_quote', $t->ID) ?: '',
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $rating, 'bestRating' => 5],
                'itemReviewed' => ['@type' => 'RealEstateAgent', 'name' => 'Crowns Estates'],
            ];
        }
    }

    // ── How It Works: FAQPage ───────────────────���─────────────────────────
    if (is_page('how-it-works')) {
        $page_id = get_queried_object_id();
        $faqs    = get_field('ce_faq_items', $page_id); // ACF repeater: ce_question, ce_answer
        if ($faqs) {
            $entities = array_map(fn($faq) => [
                '@type'          => 'Question',
                'name'           => $faq['ce_question'] ?? '',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['ce_answer'] ?? ''],
            ], $faqs);
            $schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities];
        }
    }

    // ── Single Blog Post: Article ────────��────────────────────────────────
    if (is_singular('post')) {
        $id    = get_the_ID();
        $thumb = get_the_post_thumbnail_url($id, 'large') ?: '';
        $article = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title($id),
            'datePublished' => get_the_date('c', $id),
            'dateModified'  => get_the_modified_date('c', $id),
            'url'           => get_permalink($id),
            'author'        => ['@type' => 'Organization', 'name' => 'Crowns Estates'],
        ];
        if ($thumb) $article['image'] = $thumb;
        $schemas[] = $article;
    }

    // Output all schemas
    foreach (array_filter($schemas) as $schema) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}, 1);
