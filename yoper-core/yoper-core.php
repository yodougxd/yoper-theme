<?php
/**
 * Plugin Name: Yoper Core
 * Plugin URI: https://example.com/yoper-core
 * Description: Core functionality plugin for Yoper sites with multisite awareness.
 * Version: 0.1.0
 * Author: Yoper
 * Author URI: https://example.com
 * Text Domain: yoper-core
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('YOPER_CORE_VERSION', '0.1.0');
define('YOPER_CORE_PATH', plugin_dir_path(__FILE__));
define('YOPER_CORE_URL', plugin_dir_url(__FILE__));
define('YOPER_CORE_BASENAME', plugin_basename(__FILE__));

require_once YOPER_CORE_PATH . 'includes/helpers.php';
require_once YOPER_CORE_PATH . 'includes/multisite.php';
require_once YOPER_CORE_PATH . 'includes/class-yoper-core.php';
require_once YOPER_CORE_PATH . 'includes/cpt.php';
require_once YOPER_CORE_PATH . 'includes/admin-menu.php';
require_once YOPER_CORE_PATH . 'includes/settings-business.php';
require_once YOPER_CORE_PATH . 'includes/capabilities.php';
require_once YOPER_CORE_PATH . 'includes/cap-fix.php';
require_once YOPER_CORE_PATH . 'includes/acf-json.php';

register_activation_hook(__FILE__, 'yoper_core_activate');
register_deactivation_hook(__FILE__, 'yoper_core_deactivate');

add_action('plugins_loaded', 'yoper_core_bootstrap');
add_action('init', 'yoper_core_setup_caps');

function yoper_core_bootstrap() {
    $network_mode = yoper_core_is_network_active();

    Yoper_Core::init($network_mode);
}

function yoper_core_activate($network_wide) {
    if (is_multisite() && $network_wide) {
        yoper_core_for_each_site('yoper_core_activate_site');
        return;
    }

    yoper_core_activate_site();
}

function yoper_core_activate_site($site_id = null) {
    add_option('yoper_core_version', YOPER_CORE_VERSION);
    yoper_core_register_cpts();
    yoper_core_setup_caps();
}

function yoper_core_deactivate($network_wide) {
    if (is_multisite() && $network_wide) {
        yoper_core_for_each_site('yoper_core_deactivate_site');
        return;
    }

    yoper_core_deactivate_site();
}

function yoper_core_deactivate_site($site_id = null) {
    // Add per-site cleanup logic here if needed.
}

if (is_multisite()) {
    add_action('wpmu_new_blog', 'yoper_core_on_new_blog', 10, 6);
}
