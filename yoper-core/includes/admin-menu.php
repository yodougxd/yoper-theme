<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_core_register_admin_menu() {
    add_menu_page(
        __('Yoper Core', 'yoper-core'),
        __('Yoper Core', 'yoper-core'),
        'manage_options',
        'yoper-core',
        'yoper_core_render_dashboard',
        'dashicons-admin-generic',
        3
    );

    add_submenu_page(
        'yoper-core',
        __('Produtos', 'yoper-core'),
        __('Produtos', 'yoper-core'),
        'yoper_manage_products',
        'edit.php?post_type=yoper_product'
    );

    add_submenu_page(
        'yoper-core',
        __('Fechamento (Contagem)', 'yoper-core'),
        __('Fechamento (Contagem)', 'yoper-core'),
        'yoper_do_stock_count',
        'yoper-stock-count',
        'yoper_core_render_stock_count_page'
    );

    add_submenu_page(
        'yoper-core',
        __('Compras', 'yoper-core'),
        __('Compras', 'yoper-core'),
        'yoper_view_purchase_lists',
        'yoper-purchase-lists',
        'yoper_core_render_purchase_lists_page'
    );

    add_submenu_page(
        'yoper-core',
        __('Pesquisa de Preço', 'yoper-core'),
        __('Pesquisa de Preço', 'yoper-core'),
        'yoper_add_price_entries',
        'yoper-price-research',
        'yoper_core_render_price_research_page'
    );

    add_submenu_page(
        'yoper-core',
        __('Relatórios de Preço', 'yoper-core'),
        __('Relatórios de Preço', 'yoper-core'),
        'yoper_view_price_reports',
        'yoper-price-reports',
        'yoper_core_render_price_reports_page'
    );
}

add_action('admin_enqueue_scripts', 'yoper_core_admin_assets');
function yoper_core_admin_assets($hook) {
    // Load styles only for Yoper screens.
    if (strpos($hook, 'yoper-') === false && strpos($hook, 'yoper_') === false) {
        return;
    }

    $css = YOPER_CORE_PATH . 'assets/admin.css';
    if (file_exists($css)) {
        wp_enqueue_style('yoper-core-admin', YOPER_CORE_URL . 'assets/admin.css', array(), filemtime($css));
    }

    $js = YOPER_CORE_PATH . 'assets/admin.js';
    if (file_exists($js)) {
        wp_enqueue_script('yoper-core-admin', YOPER_CORE_URL . 'assets/admin.js', array(), filemtime($js), true);
    }
}

function yoper_core_register_network_menu() {
    add_menu_page(
        __('Yoper Core Network', 'yoper-core'),
        __('Yoper Core', 'yoper-core'),
        'manage_network_options',
        'yoper-core-network',
        'yoper_core_render_network_dashboard',
        'dashicons-admin-multisite',
        3
    );
}

function yoper_core_render_dashboard() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'yoper-core'));
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Yoper Core', 'yoper-core') . '</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('yoper_core');
    do_settings_sections('yoper-core');
    submit_button(__('Save changes', 'yoper-core'));
    echo '</form>';
    echo '</div>';
}

function yoper_core_render_network_dashboard() {
    if (!current_user_can('manage_network_options')) {
        wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'yoper-core'));
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Yoper Core (Network)', 'yoper-core') . '</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('yoper_core_network');
    do_settings_sections('yoper-core-network');
    submit_button(__('Save network changes', 'yoper-core'));
    echo '</form>';
    echo '</div>';
}
