<?php
/**
 * Enquiry handler: DB table creation, REST endpoint for form submissions,
 * auto-responder email, and gated brochure download.
 */

/**
 * Create custom database table on theme activation.
 * Uses after_switch_theme (not register_activation_hook — that's plugins only).
 * Also checks on init for existing installs via a version option.
 */
function ce_create_enquiries_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_enquiries';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        property_interest VARCHAR(255),
        message TEXT,
        gdpr_consent TINYINT(1) NOT NULL DEFAULT 0,
        source VARCHAR(100) DEFAULT 'website',
        source_url VARCHAR(500),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('ce_enquiries_db_version', '1.0');
}
add_action('after_switch_theme', 'ce_create_enquiries_table');

add_action('init', function () {
    if (get_option('ce_enquiries_db_version') !== '1.0') {
        ce_create_enquiries_table();
    }
});

/**
 * REST endpoint: POST /wp-json/ce/v1/enquiry
 * Handles Register Interest form, Contact form, and Brochure Gate submissions.
 */
function ce_register_enquiry_endpoint() {
    register_rest_route('ce/v1', '/enquiry', [
        'methods'             => 'POST',
        'callback'            => 'ce_handle_enquiry',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'ce_register_enquiry_endpoint');

function ce_handle_enquiry(\WP_REST_Request $request): \WP_REST_Response {
    $name    = sanitize_text_field($request->get_param('name') ?? '');
    $email   = sanitize_email($request->get_param('email') ?? '');
    $phone   = sanitize_text_field($request->get_param('phone') ?? '');
    $property = sanitize_text_field($request->get_param('property_interest') ?? '');
    $message = sanitize_textarea_field($request->get_param('message') ?? '');
    $consent = (bool) $request->get_param('gdpr_consent');
    $source  = sanitize_text_field($request->get_param('source') ?? 'website');
    $source_url = esc_url_raw($request->get_param('source_url') ?? '');
    $brochure_url = esc_url_raw($request->get_param('brochure_url') ?? '');

    // Validation
    if (empty($name)) {
        return new \WP_REST_Response(['success' => false, 'error' => 'Name is required.'], 400);
    }
    if (!is_email($email)) {
        return new \WP_REST_Response(['success' => false, 'error' => 'Valid email is required.'], 400);
    }
    if (!$consent) {
        return new \WP_REST_Response(['success' => false, 'error' => 'GDPR consent is required.'], 400);
    }

    // Store in DB
    global $wpdb;
    $table = $wpdb->prefix . 'ce_enquiries';
    $wpdb->insert($table, [
        'name'              => $name,
        'email'             => $email,
        'phone'             => $phone,
        'property_interest' => $property,
        'message'           => $message,
        'gdpr_consent'      => 1,
        'source'            => $source,
        'source_url'        => $source_url,
    ]);

    // Send admin notification
    $admin_email = function_exists('get_field') ? get_field('ce_contact_email', 'option') : '';
    $admin_email = $admin_email ?: 'info@crownsestates.co.uk';

    $admin_subject = 'New Enquiry — Crowns Estates';
    $admin_body = "New enquiry received:\n\n";
    $admin_body .= "Name: {$name}\n";
    $admin_body .= "Email: {$email}\n";
    $admin_body .= "Phone: {$phone}\n";
    $admin_body .= "Property: {$property}\n";
    $admin_body .= "Source: {$source}\n";
    $admin_body .= "Message:\n{$message}\n";
    wp_mail($admin_email, $admin_subject, $admin_body);

    // Auto-responder to enquirer
    $responder_subject = 'Thank you for your enquiry — Crowns Estates';
    $responder_body = "Dear {$name},\n\n";
    $responder_body .= "Thank you for your interest in investing with Crowns Estates. Our team will be in touch within 24 hours.\n\n";

    // If brochure gate, include download link
    if (!empty($brochure_url)) {
        $responder_body .= "Your brochure is ready to download:\n{$brochure_url}\n\n";
    }

    $responder_body .= "In the meantime, explore our latest opportunities at https://www.crownsestates.co.uk/projects\n\n";
    $responder_body .= "Best regards,\nThe Crowns Estates Team";
    wp_mail($email, $responder_subject, $responder_body);

    return new \WP_REST_Response(['success' => true, 'message' => 'Thank you! We\'ll be in touch within 24 hours.'], 200);
}

/**
 * Admin page: View Enquiries.
 * Registered as a submenu under Site Settings.
 */
function ce_register_enquiries_admin_page() {
    add_submenu_page(
        'ce-site-settings',
        'Enquiries',
        'Enquiries',
        'manage_options',
        'ce-enquiries',
        'ce_render_enquiries_page'
    );
}
add_action('admin_menu', 'ce_register_enquiries_admin_page');

function ce_render_enquiries_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_enquiries';
    $enquiries = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");

    echo '<div class="wrap">';
    echo '<h1>Enquiries</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Property</th><th>Source</th><th>Date</th></tr></thead>';
    echo '<tbody>';
    if ($enquiries) {
        foreach ($enquiries as $e) {
            printf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                esc_html($e->name),
                esc_html($e->email),
                esc_html($e->phone),
                esc_html($e->property_interest),
                esc_html($e->source),
                esc_html($e->created_at)
            );
        }
    } else {
        echo '<tr><td colspan="6">No enquiries yet.</td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}
