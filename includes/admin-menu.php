<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_register_admin_menu() {
    $capability = 'manage_options';
    $menu_slug  = 'yoper-business-settings';

    add_menu_page(
        __('Yoper', 'yoper-theme'),
        __('Yoper', 'yoper-theme'),
        $capability,
        $menu_slug,
        'yoper_render_business_settings_page',
        'dashicons-store',
        58
    );

    add_submenu_page(
        $menu_slug,
        __('Config. do Negócio', 'yoper-theme'),
        __('Config. do Negócio', 'yoper-theme'),
        $capability,
        $menu_slug,
        'yoper_render_business_settings_page'
    );
}
add_action('admin_menu', 'yoper_register_admin_menu');
