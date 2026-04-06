<?php
// wp-content/themes/crowns-estates/inc/ga4-tracking.php
defined('ABSPATH') || exit;

/**
 * Get GTM container ID from ACF options (falls back to empty — suppresses snippet).
 */
function ce_get_gtm_id(): string {
    if (!function_exists('get_field')) return '';
    return sanitize_text_field(get_field('ce_gtm_container_id', 'option') ?: '');
}

/**
 * Output GTM <head> snippet + server-side dataLayer push.
 */
add_action('wp_head', function (): void {
    $gtm_id = ce_get_gtm_id();
    if (!$gtm_id) return;

    // Build server-side dataLayer context
    $dl = ['page_type' => ce_get_page_type()];
    if (is_singular('ce_property')) {
        $id = get_the_ID();
        $city_terms = get_the_terms($id, 'ce_city');
        $dl['property_id']     = $id;
        $dl['property_name']   = get_the_title($id);
        $dl['property_city']   = $city_terms ? $city_terms[0]->name : '';
        $dl['property_status'] = get_field('ce_status', $id) ?: '';
    }
    ?>
    <script>window.dataLayer=window.dataLayer||[];dataLayer.push(<?php echo wp_json_encode($dl, JSON_UNESCAPED_SLASHES); ?>);</script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php
}, 1);

/**
 * Output GTM noscript body snippet.
 */
add_action('wp_body_open', function (): void {
    $gtm_id = ce_get_gtm_id();
    if (!$gtm_id) return;
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
});

/**
 * Map current WP template to a page_type string for dataLayer.
 */
function ce_get_page_type(): string {
    if (is_front_page())            return 'home';
    if (is_singular('ce_property')) return 'property';
    if (is_page('projects'))        return 'projects';
    if (is_page('how-it-works'))    return 'how-it-works';
    if (is_page('about'))           return 'about';
    if (is_page('contact'))         return 'contact';
    if (is_singular('post'))        return 'blog-post';
    if (is_home() || is_archive())  return 'blog';
    return 'page';
}
