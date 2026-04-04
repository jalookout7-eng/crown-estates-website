<?php
/**
 * Custom Admin Dashboard & Branded Backend.
 * Reference: griyakita admin screenshots.
 */

// Remove default dashboard widgets
function ce_remove_dashboard_widgets() {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'ce_remove_dashboard_widgets');

// Add custom dashboard widget
function ce_add_dashboard_widgets() {
    wp_add_dashboard_widget('ce_dashboard_overview', 'Crowns Estates Overview', 'ce_dashboard_overview_render');
}
add_action('wp_dashboard_setup', 'ce_add_dashboard_widgets');

function ce_dashboard_overview_render() {
    global $wpdb;
    $total_properties = wp_count_posts('ce_property')->publish ?? 0;
    $enquiries_table = $wpdb->prefix . 'ce_enquiries';
    $total_enquiries = (int) $wpdb->get_var("SELECT COUNT(*) FROM $enquiries_table");
    $total_users = count_users()['total_users'];

    // Calculate total property value
    $property_ids = get_posts(['post_type' => 'ce_property', 'posts_per_page' => -1, 'fields' => 'ids']);
    $total_value = 0;
    foreach ($property_ids as $pid) {
        $total_value += (float) (get_field('ce_price_from', $pid) ?: 0);
    }

    echo '<div class="ce-admin-stats">';
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">%d</div><div class="ce-admin-stat__label">Total Properties</div><canvas class="ce-admin-sparkline" data-type="properties"></canvas></div>', $total_properties);
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">%d</div><div class="ce-admin-stat__label">Enquiries</div><canvas class="ce-admin-sparkline" data-type="enquiries"></canvas></div>', $total_enquiries);
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">%d</div><div class="ce-admin-stat__label">Users</div></div>', $total_users);
    printf('<div class="ce-admin-stat"><div class="ce-admin-stat__value">SAR %s</div><div class="ce-admin-stat__label">Total Property Value</div></div>', number_format($total_value, 0));
    echo '</div>';
}

// Customise admin sidebar
function ce_custom_admin_menu() {
    remove_menu_page('edit-comments.php');
    remove_menu_page('tools.php');

    global $menu;
    foreach ($menu as $key => $item) {
        if (isset($item[2]) && $item[2] === 'edit.php') {
            $menu[$key][0] = 'Blog Posts';
        }
    }
}
add_action('admin_menu', 'ce_custom_admin_menu');

// Admin branding: colour scheme
function ce_admin_styles() {
    echo '<style>
        #adminmenuback, #adminmenuwrap { background: #1a1a1a; }
        #adminmenu .wp-has-current-submenu .wp-submenu-head,
        #adminmenu a.wp-has-current-submenu { background: #C4973A !important; }
        #wpadminbar { background: #0A0A0A; }
        .ce-admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 16px 0; }
        .ce-admin-stat { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center; }
        .ce-admin-stat__value { font-size: 28px; font-weight: 700; color: #0A0A0A; }
        .ce-admin-stat__label { font-size: 13px; color: #666; margin-top: 4px; }
        .ce-admin-sparkline { width: 100%; height: 40px; margin-top: 8px; }
    </style>';
}
add_action('admin_head', 'ce_admin_styles');

// Custom admin footer
function ce_admin_footer_text() {
    return 'Crowns Estates Admin Panel &mdash; Powered by WordPress';
}
add_filter('admin_footer_text', 'ce_admin_footer_text');
