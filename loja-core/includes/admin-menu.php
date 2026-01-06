<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once LOJA_CORE_PATH . 'admin-pages/dashboard.php';
require_once LOJA_CORE_PATH . 'admin-pages/contagens.php';
require_once LOJA_CORE_PATH . 'admin-pages/modo-compras.php';
require_once LOJA_CORE_PATH . 'admin-pages/precos.php';
require_once LOJA_CORE_PATH . 'admin-pages/relatorios.php';
require_once LOJA_CORE_PATH . 'admin-pages/settings.php';

function loja_core_register_menu() {
    add_menu_page(
        __('Loja (Sistema)', 'loja-core'),
        __('Loja', 'loja-core'),
        'loja_view_dashboard',
        'loja-core',
        'loja_core_render_dashboard',
        'dashicons-store',
        3
    );

    add_submenu_page('loja-core', __('Dashboard', 'loja-core'), __('Dashboard', 'loja-core'), 'loja_view_dashboard', 'loja-core', 'loja_core_render_dashboard');
    add_submenu_page('loja-core', __('Produtos', 'loja-core'), __('Produtos', 'loja-core'), 'loja_manage_products', 'edit.php?post_type=loja_produto');
    add_submenu_page('loja-core', __('Contagens', 'loja-core'), __('Contagens', 'loja-core'), 'loja_manage_stock_counts', 'loja-contagens', 'loja_core_render_contagens');
    add_submenu_page('loja-core', __('Modo Compras', 'loja-core'), __('Modo Compras', 'loja-core'), 'loja_manage_purchases', 'loja-modo-compras', 'loja_core_render_modo_compras');
    add_submenu_page('loja-core', __('Preços', 'loja-core'), __('Preços', 'loja-core'), 'loja_manage_price_history', 'loja-precos', 'loja_core_render_precos');
    add_submenu_page('loja-core', __('Relatórios', 'loja-core'), __('Relatórios', 'loja-core'), 'loja_view_reports', 'loja-relatorios', 'loja_core_render_relatorios');
    add_submenu_page('loja-core', __('Configurações', 'loja-core'), __('Configurações', 'loja-core'), 'loja_manage_settings', 'loja-settings', 'loja_core_render_settings');
}

function loja_core_admin_assets($hook) {
    if (strpos($hook, 'loja') === false) {
        return;
    }

    $bulma = LOJA_CORE_URL . 'assets/bulma.min.css';
    wp_enqueue_style('loja-core-bulma', $bulma, array(), LOJA_CORE_VERSION);

    $css = LOJA_CORE_PATH . 'assets/admin.css';
    if (file_exists($css)) {
        wp_enqueue_style('loja-core-admin', LOJA_CORE_URL . 'assets/admin.css', array('loja-core-bulma'), filemtime($css));
    }

    $js = LOJA_CORE_PATH . 'assets/admin.js';
    if (file_exists($js)) {
        wp_enqueue_script('loja-core-admin', LOJA_CORE_URL . 'assets/admin.js', array('jquery'), filemtime($js), true);
    }
}
