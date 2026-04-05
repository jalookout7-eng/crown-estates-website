<?php
// wp-content/mu-plugins/crowns-estates-core/enquiry-admin.php
defined('ABSPATH') || exit;

add_action('rest_api_init', function (): void {

    // GET /ce/v1/enquiries — paginated list (admin only)
    register_rest_route('ce/v1', '/enquiries', [
        'methods'             => 'GET',
        'callback'            => 'ce_rest_get_enquiries',
        'permission_callback' => function () {
            return current_user_can('manage_options') || current_user_can('edit_posts');
        },
    ]);

    // GET /ce/v1/enquiries/export — CSV download (admin only)
    register_rest_route('ce/v1', '/enquiries/export', [
        'methods'             => 'GET',
        'callback'            => 'ce_rest_export_enquiries',
        'permission_callback' => fn() => current_user_can('manage_options'),
    ]);

    // PATCH /ce/v1/enquiries/{id} — update status (admin + editor)
    register_rest_route('ce/v1', '/enquiries/(?P<id>\d+)', [
        'methods'             => 'POST', // PATCH not always supported; use POST with _method override
        'callback'            => 'ce_rest_update_enquiry',
        'permission_callback' => function () {
            return current_user_can('manage_options') || current_user_can('edit_posts');
        },
        'args' => [
            'id' => ['validate_callback' => fn($v) => is_numeric($v)],
        ],
    ]);
});

function ce_rest_get_enquiries(WP_REST_Request $request): WP_REST_Response {
    global $wpdb;
    $table    = $wpdb->prefix . 'ce_enquiries';
    $page     = max(1, (int) $request->get_param('page'));
    $per_page = min(100, max(1, (int) ($request->get_param('per_page') ?: 20)));
    $offset   = ($page - 1) * $per_page;
    $status   = sanitize_text_field($request->get_param('status') ?? '');
    $search   = sanitize_text_field($request->get_param('search') ?? '');

    $where  = 'WHERE 1=1';
    $params = [];

    if ($status && in_array($status, ['new', 'read', 'replied', 'archived'], true)) {
        $where   .= ' AND status = %s';
        $params[] = $status;
    }
    if ($search) {
        $where   .= ' AND (name LIKE %s OR email LIKE %s)';
        $like     = '%' . $wpdb->esc_like($search) . '%';
        $params[] = $like;
        $params[] = $like;
    }

    $total_query = $params
        ? $wpdb->prepare("SELECT COUNT(*) FROM $table $where", ...$params)
        : "SELECT COUNT(*) FROM $table $where";
    $total = (int) $wpdb->get_var($total_query);

    $data_query = $params
        ? $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", ...[...$params, $per_page, $offset])
        : $wpdb->prepare("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset);
    $rows = $wpdb->get_results($data_query);

    $response = new WP_REST_Response($rows, 200);
    $response->header('X-WP-Total', $total);
    $response->header('X-WP-TotalPages', (int) ceil($total / $per_page));
    return $response;
}

function ce_rest_export_enquiries(): void {
    global $wpdb;
    $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ce_enquiries ORDER BY created_at DESC");

    $filename = 'enquiries-' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Message', 'Source', 'Property ID', 'Status', 'GDPR', 'IP', 'Date']);
    foreach ($rows as $row) {
        fputcsv($output, [
            $row->id,
            $row->name,
            $row->email,
            $row->phone,
            $row->message,
            $row->source,
            $row->property_id,
            $row->status,
            $row->gdpr_consent ? 'Yes' : 'No',
            $row->ip_address,
            $row->created_at,
        ]);
    }
    fclose($output);
    exit;
}

function ce_rest_update_enquiry(WP_REST_Request $request): WP_REST_Response {
    $id     = (int) $request->get_param('id');
    $status = sanitize_text_field($request->get_param('status') ?? '');
    $allowed = ['new', 'read', 'replied', 'archived'];

    if (!in_array($status, $allowed, true)) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid status.'], 400);
    }

    global $wpdb;
    $updated = $wpdb->update(
        $wpdb->prefix . 'ce_enquiries',
        ['status' => $status],
        ['id' => $id],
        ['%s'],
        ['%d']
    );

    if ($updated === false) {
        return new WP_REST_Response(['success' => false, 'error' => 'Update failed.'], 500);
    }

    return new WP_REST_Response(['success' => true, 'status' => $status], 200);
}
