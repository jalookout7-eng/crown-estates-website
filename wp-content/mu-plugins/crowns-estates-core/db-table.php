<?php
// wp-content/mu-plugins/crowns-estates-core/db-table.php
defined('ABSPATH') || exit;

define('CE_ENQUIRIES_DB_VERSION', '1.1');

function ce_create_enquiries_table(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'ce_enquiries';
    $charset = $wpdb->get_charset_collate();

    // dbDelta adds missing columns — never drops existing ones
    $sql = "CREATE TABLE $table (
        id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name         VARCHAR(255) NOT NULL,
        email        VARCHAR(255) NOT NULL,
        phone        VARCHAR(50) DEFAULT NULL,
        message      TEXT DEFAULT NULL,
        property_id  BIGINT UNSIGNED DEFAULT NULL,
        source       VARCHAR(50) NOT NULL DEFAULT 'register_interest',
        gdpr_consent TINYINT(1) NOT NULL DEFAULT 0,
        ip_address   VARCHAR(45) DEFAULT NULL,
        status       VARCHAR(20) NOT NULL DEFAULT 'new',
        created_at   DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY email (email),
        KEY status (status),
        KEY created_at (created_at)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('ce_enquiries_db_version', CE_ENQUIRIES_DB_VERSION);
}

// Run on every load if version is outdated
add_action('init', function (): void {
    if (get_option('ce_enquiries_db_version') !== CE_ENQUIRIES_DB_VERSION) {
        ce_create_enquiries_table();
    }
}, 1);
