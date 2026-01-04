<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('is_plugin_active_for_network')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

function yoper_core_is_network_active() {
    return is_multisite() && is_plugin_active_for_network(YOPER_CORE_BASENAME);
}

function yoper_core_get_option($key, $default = '', $network = false) {
    if ($network && is_multisite()) {
        $value = get_site_option($key, $default);
    } else {
        $value = get_option($key, $default);
    }

    return $value;
}

function yoper_core_update_option($key, $value, $network = false) {
    if ($network && is_multisite()) {
        return update_site_option($key, $value);
    }

    return update_option($key, $value);
}
