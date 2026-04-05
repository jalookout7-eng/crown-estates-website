<?php
// wp-content/mu-plugins/crowns-estates-core/crowns-estates-core.php
/**
 * Plugin Name: Crowns Estates Core
 * Description: Data layer for Crowns Estates — CPTs, taxonomy, DB table, REST API, email system.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

define('CE_CORE_DIR', plugin_dir_path(__FILE__));
define('CE_CORE_VERSION', '1.0.0');

require CE_CORE_DIR . 'cpt-property.php';
require CE_CORE_DIR . 'cpt-testimonial.php';
require CE_CORE_DIR . 'taxonomy-city.php';

if (file_exists(CE_CORE_DIR . 'db-table.php')) {
    require CE_CORE_DIR . 'db-table.php';
}
if (file_exists(CE_CORE_DIR . 'currency-helpers.php')) {
    require CE_CORE_DIR . 'currency-helpers.php';
}
if (file_exists(CE_CORE_DIR . 'enquiry-handler.php')) {
    require CE_CORE_DIR . 'enquiry-handler.php';
}
if (file_exists(CE_CORE_DIR . 'enquiry-admin.php')) {
    require CE_CORE_DIR . 'enquiry-admin.php';
}
if (file_exists(CE_CORE_DIR . 'email-handler.php')) {
    require CE_CORE_DIR . 'email-handler.php';
}
