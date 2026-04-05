<?php
// wp-content/themes/crowns-estates/inc/enquiry-admin-page.php
defined('ABSPATH') || exit;

add_action('admin_menu', function (): void {
    add_menu_page(
        'Enquiries',
        'Enquiries',
        'edit_posts',
        'ce-enquiries',
        'ce_render_enquiries_admin_page',
        'dashicons-email-alt',
        25
    );
});

function ce_render_enquiries_admin_page(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'ce_enquiries';
    $status  = sanitize_text_field($_GET['status'] ?? '');
    $search  = sanitize_text_field($_GET['search'] ?? '');
    $paged   = max(1, (int) ($_GET['paged'] ?? 1));
    $per     = 20;
    $offset  = ($paged - 1) * $per;

    $where  = 'WHERE 1=1';
    $params = [];
    if ($status && in_array($status, ['new', 'read', 'replied', 'archived'], true)) {
        $where .= ' AND status = %s'; $params[] = $status;
    }
    if ($search) {
        $where .= ' AND (name LIKE %s OR email LIKE %s)';
        $like = '%' . $wpdb->esc_like($search) . '%'; $params[] = $like; $params[] = $like;
    }

    $total_query  = $params ? $wpdb->prepare("SELECT COUNT(*) FROM $table $where", ...$params) : $wpdb->prepare("SELECT COUNT(*) FROM $table $where");
    $total        = (int) $wpdb->get_var($total_query);
    $data_query   = $params
        ? $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", ...[...$params, $per, $offset])
        : $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", $per, $offset);
    $enquiries    = $wpdb->get_results($data_query);

    $counts = $wpdb->get_results("SELECT status, COUNT(*) as n FROM $table GROUP BY status");
    $count_map = ['new' => 0, 'read' => 0, 'replied' => 0, 'archived' => 0];
    foreach ($counts as $c) $count_map[$c->status] = (int) $c->n;
    $total_all = array_sum($count_map);

    $base_url = admin_url('admin.php?page=ce-enquiries');
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Enquiries</h1>
        <?php if (current_user_can('manage_options')): ?>
        <a href="<?php echo esc_url(add_query_arg('_wpnonce', wp_create_nonce('wp_rest'), rest_url('ce/v1/enquiries/export'))); ?>" class="page-title-action" style="background:#C4973A;color:#fff;border-color:#C4973A">↓ Export CSV</a>
        <?php endif; ?>

        <ul class="subsubsub" style="margin:12px 0">
            <?php
            $tabs = ['All' => $total_all, 'New' => $count_map['new'], 'Read' => $count_map['read'], 'Replied' => $count_map['replied'], 'Archived' => $count_map['archived']];
            $tab_keys = ['All' => '', 'New' => 'new', 'Read' => 'read', 'Replied' => 'replied', 'Archived' => 'archived'];
            $last = array_key_last($tabs);
            foreach ($tabs as $label => $count):
                $active = ($tab_keys[$label] === $status || ($label === 'All' && $status === ''));
                $url    = $tab_keys[$label] ? add_query_arg('status', $tab_keys[$label], $base_url) : $base_url;
            ?>
            <li><a href="<?php echo esc_url($url); ?>"<?php echo $active ? ' class="current"' : ''; ?>><?php echo $label; ?> <span class="count">(<?php echo $count; ?>)</span></a><?php echo $label !== $last ? ' | ' : ''; ?></li>
            <?php endforeach; ?>
        </ul>

        <form method="get" style="margin-bottom:16px">
            <input type="hidden" name="page" value="ce-enquiries">
            <?php if ($status): ?><input type="hidden" name="status" value="<?php echo esc_attr($status); ?>"><?php endif; ?>
            <input type="search" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Search by name or email" style="padding:4px 8px;width:280px">
            <button type="submit" class="button">Search</button>
            <?php if ($search): ?><a href="<?php echo esc_url($base_url . ($status ? '&status=' . $status : '')); ?>" class="button">Clear</a><?php endif; ?>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Source</th><th>Property</th><th>Status</th><th>Date</th>
                </tr>
            </thead>
            <tbody id="ce-enquiries-tbody">
            <?php if ($enquiries): foreach ($enquiries as $e):
                $prop_name = $e->property_id ? get_the_title((int) $e->property_id) : '—';
                $status_colours = ['new' => '#fee2e2;color:#991b1b', 'read' => '#f5f5f5;color:#444', 'replied' => '#dcfce7;color:#166534', 'archived' => '#e5e7eb;color:#666'];
                $badge_style = $status_colours[$e->status] ?? '#f5f5f5;color:#444';
            ?>
            <tr data-id="<?php echo esc_attr($e->id); ?>" style="cursor:pointer" onclick="ceToggleDetail(this)">
                <td><strong><?php echo esc_html($e->name); ?></strong></td>
                <td><?php echo esc_html($e->email); ?></td>
                <td><?php echo esc_html($e->phone ?: '—'); ?></td>
                <td><?php echo esc_html($e->source); ?></td>
                <td><?php echo esc_html($prop_name); ?></td>
                <td>
                    <span class="ce-status-badge" style="background:<?php echo $badge_style; ?>;padding:2px 8px;border-radius:3px;font-size:11px">
                        <?php echo esc_html(ucfirst($e->status)); ?>
                    </span>
                    <?php if (current_user_can('edit_posts')): ?>
                    <select class="ce-status-select" data-id="<?php echo esc_attr($e->id); ?>" style="font-size:11px;margin-left:6px" onclick="event.stopPropagation()">
                        <?php foreach (['new', 'read', 'replied', 'archived'] as $s): ?>
                        <option value="<?php echo $s; ?>"<?php selected($e->status, $s); ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html(date_i18n('d M Y', strtotime($e->created_at))); ?></td>
            </tr>
            <tr class="ce-detail-row" id="ce-detail-<?php echo esc_attr($e->id); ?>" style="display:none;background:#fffbf0">
                <td colspan="7" style="padding:16px 20px">
                    <strong>Message:</strong> <?php echo nl2br(esc_html($e->message ?: '—')); ?><br>
                    <small style="color:#666;margin-top:6px;display:block">GDPR: <?php echo $e->gdpr_consent ? '✓ Consented' : '✗ No consent'; ?> · IP: <?php echo esc_html($e->ip_address ?: '—'); ?> · Submitted: <?php echo esc_html($e->created_at); ?></small>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="7">No enquiries found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total > $per): ?>
        <div class="tablenav bottom" style="margin-top:12px">
            <?php
            $total_pages = ceil($total / $per);
            for ($p = 1; $p <= $total_pages; $p++):
                $page_url = add_query_arg('paged', $p, $base_url . ($status ? '&status=' . $status : '') . ($search ? '&search=' . urlencode($search) : ''));
            ?>
            <a href="<?php echo esc_url($page_url); ?>" class="button<?php echo $p === $paged ? ' button-primary' : ''; ?>" style="margin-right:2px"><?php echo $p; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function ceToggleDetail(row) {
        var id = row.getAttribute('data-id');
        var detail = document.getElementById('ce-detail-' + id);
        if (detail) detail.style.display = detail.style.display === 'none' ? 'table-row' : 'none';
    }
    document.querySelectorAll('.ce-status-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var id     = this.getAttribute('data-id');
            var status = this.value;
            var nonce  = '<?php echo wp_create_nonce('wp_rest'); ?>';
            fetch('<?php echo esc_url(rest_url('ce/v1/enquiries/')); ?>' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ status: status })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (!data.success) alert('Update failed.');
            });
        });
    });
    </script>
    <?php
}
