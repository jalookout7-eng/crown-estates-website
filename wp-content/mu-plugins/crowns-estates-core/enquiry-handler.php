<?php
// wp-content/mu-plugins/crowns-estates-core/enquiry-handler.php
defined('ABSPATH') || exit;

/**
 * REST POST /wp-json/ce/v1/enquiry
 * Handles Register Interest and Contact form submissions.
 */
add_action('rest_api_init', function (): void {
    register_rest_route('ce/v1', '/enquiry', [
        'methods'             => 'POST',
        'callback'            => 'ce_handle_enquiry',
        'permission_callback' => '__return_true',
    ]);
});

function ce_handle_enquiry(WP_REST_Request $request): WP_REST_Response {
    // Nonce verification
    $nonce = $request->get_param('nonce');
    if (!wp_verify_nonce($nonce, 'ce_enquiry_nonce')) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid request.'], 403);
    }

    $name        = sanitize_text_field($request->get_param('name') ?? '');
    $email       = sanitize_email($request->get_param('email') ?? '');
    $phone       = sanitize_text_field($request->get_param('phone') ?? '');
    $message     = sanitize_textarea_field($request->get_param('message') ?? '');
    $property_id = (int) $request->get_param('property_id');
    $source      = sanitize_text_field($request->get_param('source') ?? 'register_interest');
    $consent     = (bool) $request->get_param('gdpr_consent');

    // Validation
    if (empty($name)) {
        return new WP_REST_Response(['success' => false, 'error' => 'Name is required.'], 400);
    }
    if (!is_email($email)) {
        return new WP_REST_Response(['success' => false, 'error' => 'A valid email address is required.'], 400);
    }
    if (!$consent) {
        return new WP_REST_Response(['success' => false, 'error' => 'GDPR consent is required.'], 400);
    }
    $allowed_sources = ['register_interest', 'contact_form'];
    if (!in_array($source, $allowed_sources, true)) {
        $source = 'register_interest';
    }

    // Store
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ce_enquiries',
        [
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'message'      => $message,
            'property_id'  => $property_id ?: null,
            'source'       => $source,
            'gdpr_consent' => 1,
            'ip_address'   => ce_get_client_ip(),
            'status'       => 'new',
            'created_at'   => current_time('mysql', true),
        ],
        ['%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s']
    );

    // Emails
    $property_name = $property_id ? get_the_title($property_id) : '';
    ce_send_auto_responder($name, $email, $property_name);
    ce_send_admin_notification($name, $email, $phone, $message, $source, $property_name);

    return new WP_REST_Response(['success' => true], 200);
}

/**
 * REST POST /wp-json/ce/v1/brochure-gate
 * Gated brochure email capture — validates, stores lead, emails signed download URL.
 */
add_action('rest_api_init', function (): void {
    register_rest_route('ce/v1', '/brochure-gate', [
        'methods'             => 'POST',
        'callback'            => 'ce_handle_brochure_gate',
        'permission_callback' => '__return_true',
    ]);
});

function ce_handle_brochure_gate(WP_REST_Request $request): WP_REST_Response {
    $nonce = $request->get_param('nonce');
    if (!wp_verify_nonce($nonce, 'ce_enquiry_nonce')) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid request.'], 403);
    }

    $name        = sanitize_text_field($request->get_param('name') ?? '');
    $email       = sanitize_email($request->get_param('email') ?? '');
    $property_id = (int) $request->get_param('property_id');
    $consent     = (bool) $request->get_param('gdpr_consent');

    if (empty($name) || !is_email($email) || !$property_id || !$consent) {
        return new WP_REST_Response(['success' => false, 'error' => 'All fields are required.'], 400);
    }

    // Verify brochure exists for this property
    $brochure = get_field('ce_brochure_pdf', $property_id);
    if (empty($brochure)) {
        return new WP_REST_Response(['success' => false, 'error' => 'No brochure available for this property.'], 404);
    }

    // Store lead
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ce_enquiries',
        [
            'name'         => $name,
            'email'        => $email,
            'property_id'  => $property_id,
            'source'       => 'brochure_download',
            'gdpr_consent' => 1,
            'ip_address'   => ce_get_client_ip(),
            'status'       => 'new',
            'created_at'   => current_time('mysql', true),
        ],
        ['%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s']
    );

    // Generate signed 24-hour download URL
    $token   = wp_generate_password(32, false);
    $expires = time() + DAY_IN_SECONDS;
    set_transient('ce_brochure_token_' . $token, ['property_id' => $property_id, 'expires' => $expires], DAY_IN_SECONDS);

    $download_url = add_query_arg([
        'ce_brochure' => $property_id,
        'token'       => $token,
    ], home_url('/'));

    $property_name = get_the_title($property_id);
    ce_send_brochure_delivery($name, $email, $property_name, $download_url);

    return new WP_REST_Response(['success' => true], 200);
}

/**
 * Serve the signed brochure file on template_redirect.
 */
add_action('template_redirect', function (): void {
    if (!isset($_GET['ce_brochure'], $_GET['token'])) {
        return;
    }
    $property_id = (int) $_GET['ce_brochure'];
    $token       = sanitize_text_field($_GET['token']);
    $data        = get_transient('ce_brochure_token_' . $token);

    if (!$data || (int) $data['property_id'] !== $property_id || time() > $data['expires']) {
        wp_die('This brochure link has expired or is invalid.', 'Link Expired', ['response' => 403]);
    }

    $brochure = get_field('ce_brochure_pdf', $property_id);
    $file_url = is_array($brochure) ? $brochure['url'] : $brochure;
    if (empty($file_url)) {
        wp_die('Brochure not found.', 'Not Found', ['response' => 404]);
    }

    wp_redirect($file_url);
    exit;
});

/**
 * Helper: get client IP address.
 */
function ce_get_client_ip(): string {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = sanitize_text_field(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '';
}
