<?php
// wp-content/themes/crowns-estates/inc/admin-dashboard.php
defined('ABSPATH') || exit;

require get_template_directory() . '/inc/enquiry-admin-page.php';

// ─── User Role Restrictions ──────────────────────────────────────────────────

add_filter('user_has_cap', function (array $allcaps, array $caps): array {
    $user = wp_get_current_user();
    // Strip plugin/theme management from the client admin account
    // Developer accounts identified by email domain — adjust as needed
    if (in_array('administrator', $user->roles, true) && !str_ends_with($user->user_email, '@3dvisualpro.com')) {
        $restricted = ['install_plugins', 'activate_plugins', 'update_plugins', 'delete_plugins', 'edit_plugins', 'install_themes', 'edit_themes', 'update_themes', 'delete_themes', 'update_core'];
        foreach ($restricted as $cap) {
            unset($allcaps[$cap]);
        }
    }
    return $allcaps;
}, 10, 2);

// ─── Remove Default Widgets ──────────────────────────────────────────────────

add_action('wp_dashboard_setup', function (): void {
    $to_remove = ['dashboard_quick_press', 'dashboard_primary', 'dashboard_secondary', 'dashboard_site_health', 'dashboard_activity', 'dashboard_right_now', 'dashboard_recent_comments'];
    foreach ($to_remove as $widget) {
        remove_meta_box($widget, 'dashboard', 'normal');
        remove_meta_box($widget, 'dashboard', 'side');
    }
    wp_add_dashboard_widget('ce_dashboard_main', 'Crowns Estates', 'ce_render_dashboard_widget');
});

// ─── Dashboard Widget ────────────────────────────────────────────────────────

function ce_render_dashboard_widget(): void {
    global $wpdb;
    $table            = $wpdb->prefix . 'ce_enquiries';
    $total_properties = wp_count_posts('ce_property')->publish ?? 0;
    $active_listings  = (int) wp_count_posts('ce_property')->publish;
    $total_enquiries  = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $new_enquiries    = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'new'");

    // Property value (Active listings only)
    $ids = get_posts(['post_type' => 'ce_property', 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'publish']);
    $total_value = array_sum(array_map(fn($id) => (float) (get_field('ce_price_from', $id) ?: 0), $ids));

    // Enquiries by source
    $by_source = $wpdb->get_results("SELECT source, COUNT(*) as count FROM $table GROUP BY source");
    $source_map = [];
    foreach ($by_source as $row) {
        $source_map[$row->source] = (int) $row->count;
    }

    // 30-day sparkline data
    $sparkline = $wpdb->get_results(
        "SELECT DATE(created_at) as day, COUNT(*) as count
         FROM $table
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(created_at)
         ORDER BY day ASC"
    );
    $sparkline_data = array_map(fn($r) => ['day' => $r->day, 'count' => (int) $r->count], $sparkline);

    $new_badge = $new_enquiries > 0 ? "<span style='background:#fee2e2;color:#991b1b;padding:2px 7px;border-radius:3px;font-size:11px;margin-left:6px'>{$new_enquiries} new</span>" : '';
    $admin_url = admin_url('admin.php?page=ce-enquiries');
    ?>
    <div class="ce-admin-stats">
        <div class="ce-admin-stat" style="border-top-color:#C4973A">
            <div class="ce-admin-stat__value"><?php echo $total_properties; ?></div>
            <div class="ce-admin-stat__label">Total Properties</div>
        </div>
        <div class="ce-admin-stat" style="border-top-color:#22c55e">
            <div class="ce-admin-stat__value"><?php echo $active_listings; ?></div>
            <div class="ce-admin-stat__label">Active Listings</div>
        </div>
        <div class="ce-admin-stat" style="border-top-color:#3b82f6">
            <div class="ce-admin-stat__value"><?php echo $total_enquiries; ?><?php echo $new_badge; ?></div>
            <div class="ce-admin-stat__label">Total Enquiries</div>
        </div>
        <div class="ce-admin-stat" style="border-top-color:#f59e0b">
            <div class="ce-admin-stat__value">£<?php echo number_format($total_value, 0); ?></div>
            <div class="ce-admin-stat__label">Total Property Value</div>
        </div>
    </div>
    <div class="ce-admin-row2">
        <div class="ce-admin-chart-box">
            <div class="ce-admin-chart-title">Enquiries — Last 30 Days</div>
            <canvas id="ce-enquiries-sparkline" height="80"></canvas>
        </div>
        <div class="ce-admin-source-box">
            <div class="ce-admin-chart-title">By Source</div>
            <?php foreach (['register_interest' => 'Register Interest', 'contact_form' => 'Contact Form', 'brochure_download' => 'Brochure Gate'] as $key => $label): ?>
            <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px">
                <span><?php echo $label; ?></span>
                <strong><?php echo $source_map[$key] ?? 0; ?></strong>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="ce-admin-actions-box">
            <div class="ce-admin-chart-title">Quick Actions</div>
            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=ce_property')); ?>" class="ce-admin-action-link">+ Add Property</a>
            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="ce-admin-action-link">+ Add Blog Post</a>
            <a href="<?php echo esc_url($admin_url); ?>" class="ce-admin-action-link">View Enquiries <?php if ($new_enquiries): ?>(<?php echo $new_enquiries; ?> new)<?php endif; ?></a>
            <?php if (current_user_can('manage_options')): ?>
            <a href="<?php echo esc_url(rest_url('ce/v1/enquiries/export')); ?>" class="ce-admin-action-link ce-admin-action-link--gold">↓ Export CSV</a>
            <?php endif; ?>
        </div>
    </div>
    <script>
    (function(){
        var data = <?php echo wp_json_encode($sparkline_data); ?>;
        document.addEventListener('DOMContentLoaded', function(){
            if (typeof Chart === 'undefined') return;
            var labels = data.map(function(d){ return d.day; });
            var counts = data.map(function(d){ return d.count; });
            new Chart(document.getElementById('ce-enquiries-sparkline'), {
                type: 'line',
                data: { labels: labels, datasets: [{ data: counts, borderColor: '#C4973A', backgroundColor: 'rgba(196,151,58,0.1)', tension: 0.3, pointRadius: 0, fill: true }] },
                options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false, beginAtZero: true } } }
            });
        });
    })();
    </script>
    <?php
}

// ─── Admin Menu + Branding ───────────────────────────────────────────────────

add_action('admin_menu', function (): void {
    remove_menu_page('edit-comments.php');
    remove_menu_page('tools.php');
    global $menu;
    foreach ($menu as $key => $item) {
        if (isset($item[2]) && $item[2] === 'edit.php') {
            $menu[$key][0] = 'Blog Posts';
        }
    }
});

add_action('admin_enqueue_scripts', function (): void {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js', [], null, true);
});

add_action('admin_head', function (): void {
    ?>
    <style>
        #adminmenuback,#adminmenuwrap{background:#1a1a1a}
        #adminmenu .wp-has-current-submenu .wp-submenu-head,#adminmenu a.wp-has-current-submenu{background:#C4973A!important}
        #wpadminbar{background:#0A0A0A}
        .ce-admin-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:0 0 16px}
        .ce-admin-stat{background:#fff;border:1px solid #e0e0e0;border-top:3px solid #ccc;border-radius:4px;padding:16px;text-align:center}
        .ce-admin-stat__value{font-size:26px;font-weight:700;color:#0A0A0A}
        .ce-admin-stat__label{font-size:12px;color:#666;margin-top:4px}
        .ce-admin-row2{display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;margin-top:4px}
        .ce-admin-chart-box,.ce-admin-source-box,.ce-admin-actions-box{background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:14px}
        .ce-admin-chart-title{font-size:11px;font-weight:700;text-transform:uppercase;color:#666;margin-bottom:10px}
        .ce-admin-action-link{display:block;padding:6px 0;color:#C4973A;font-size:13px;text-decoration:none;border-bottom:1px solid #f5f5f5}
        .ce-admin-action-link--gold{margin-top:6px;background:#C4973A;color:#fff!important;padding:6px 12px;border-radius:3px;border:none}
        @media(max-width:1200px){.ce-admin-stats{grid-template-columns:repeat(2,1fr)}.ce-admin-row2{grid-template-columns:1fr}}
    </style>
    <?php
});

add_filter('admin_footer_text', fn() => 'Crowns Estates Admin Panel &mdash; Built by 3D Visual Pro');
