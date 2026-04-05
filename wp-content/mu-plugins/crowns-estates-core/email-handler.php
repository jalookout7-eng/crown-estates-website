<?php
// wp-content/mu-plugins/crowns-estates-core/email-handler.php
defined('ABSPATH') || exit;

/**
 * Send HTML email using a PHP template file.
 *
 * @param string $to       Recipient email.
 * @param string $subject  Email subject.
 * @param string $template Filename in email-templates/ (e.g. 'auto-responder.php').
 * @param array  $vars     Variables to extract into template scope.
 */
function ce_send_email(string $to, string $subject, string $template, array $vars = []): bool {
    $template_path = CE_CORE_DIR . 'email-templates/' . $template;
    if (!file_exists($template_path)) {
        return false;
    }

    extract($vars, EXTR_SKIP);
    ob_start();
    include $template_path;
    $body = ob_get_clean();

    $from_name    = function_exists('get_field') ? (get_field('ce_email_from_name', 'option') ?: 'Crowns Estates') : 'Crowns Estates';
    $from_address = function_exists('get_field') ? (get_field('ce_email_from_address', 'option') ?: 'info@crownsestates.co.uk') : 'info@crownsestates.co.uk';
    $reply_to     = function_exists('get_field') ? (get_field('ce_email_reply_to', 'option') ?: $from_address) : $from_address;

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        "From: {$from_name} <{$from_address}>",
        "Reply-To: {$reply_to}",
    ];

    return wp_mail($to, $subject, $body, $headers);
}

/**
 * Send auto-responder to the person who submitted an enquiry.
 */
function ce_send_auto_responder(string $name, string $email, string $property_name = ''): bool {
    return ce_send_email(
        $to       : $email,
        $subject  : 'Thank you for your enquiry — Crowns Estates',
        $template : 'auto-responder.php',
        $vars     : compact('name', 'property_name')
    );
}

/**
 * Send notification email to the admin inbox.
 */
function ce_send_admin_notification(string $name, string $email, string $phone, string $message, string $source, string $property_name = ''): bool {
    $admin_email = function_exists('get_field') ? (get_field('ce_admin_notification_email', 'option') ?: get_option('admin_email')) : get_option('admin_email');
    return ce_send_email(
        $to       : $admin_email,
        $subject  : "New enquiry: {$name} — {$source}",
        $template : 'admin-notification.php',
        $vars     : compact('name', 'email', 'phone', 'message', 'source', 'property_name')
    );
}

/**
 * Send brochure delivery email with signed download URL.
 */
function ce_send_brochure_delivery(string $name, string $email, string $property_name, string $download_url): bool {
    return ce_send_email(
        $to       : $email,
        $subject  : "Your Crowns Estates brochure — {$property_name}",
        $template : 'brochure-delivery.php',
        $vars     : compact('name', 'property_name', 'download_url')
    );
}

// ─── Daily Digest Cron ───────────────────────────────────────────────────────

add_action('init', 'ce_schedule_digest_cron');

function ce_schedule_digest_cron(): void {
    $enabled = function_exists('get_field') && get_field('ce_digest_enabled', 'option');
    if ($enabled && !wp_next_scheduled('ce_daily_digest_event')) {
        wp_schedule_event(strtotime('today 08:00 UTC'), 'daily', 'ce_daily_digest_event');
    }
    if (!$enabled) {
        wp_clear_scheduled_hook('ce_daily_digest_event');
    }
}

add_action('ce_daily_digest_event', 'ce_send_daily_digest');

function ce_send_daily_digest(): void {
    global $wpdb;
    $since = gmdate('Y-m-d H:i:s', strtotime('-24 hours'));
    $enquiries = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ce_enquiries WHERE status = 'new' AND created_at >= %s ORDER BY created_at DESC",
            $since
        )
    );

    if (empty($enquiries)) {
        return;
    }

    $recipient = function_exists('get_field') ? (get_field('ce_digest_recipient_email', 'option') ?: get_option('admin_email')) : get_option('admin_email');
    $count     = count($enquiries);
    ce_send_email(
        $to       : $recipient,
        $subject  : "Crowns Estates — {$count} new " . ($count === 1 ? 'enquiry' : 'enquiries') . ' today',
        $template : 'daily-digest.php',
        $vars     : ['enquiries' => $enquiries, 'count' => $count]
    );
}
