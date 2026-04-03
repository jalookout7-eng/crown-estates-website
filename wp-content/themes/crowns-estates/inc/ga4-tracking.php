<?php
/**
 * Google Tag Manager integration.
 * GTM container ID is hardcoded — replace with actual ID when GA4 property is set up.
 */
define('CE_GTM_ID', 'GTM-XXXXXXX'); // Replace with actual GTM container ID

function ce_gtm_head() {
    if (CE_GTM_ID === 'GTM-XXXXXXX') return; // Skip if not configured
    ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo esc_js(CE_GTM_ID); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php
}
add_action('wp_head', 'ce_gtm_head', 1);

function ce_gtm_body() {
    if (CE_GTM_ID === 'GTM-XXXXXXX') return;
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr(CE_GTM_ID); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('wp_body_open', 'ce_gtm_body');
