<?php
/**
 * Plugin Name: Loja Core
 * Description: Sistema interno de loja (backoffice) com produtos, contagens e preços.
 * Version: 0.1.0
 * Author: Loja
 * Text Domain: loja-core
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LOJA_CORE_VERSION', '0.1.0');
define('LOJA_CORE_PATH', plugin_dir_path(__FILE__));
define('LOJA_CORE_URL', plugin_dir_url(__FILE__));
define('LOJA_CORE_BASENAME', plugin_basename(__FILE__));

require_once LOJA_CORE_PATH . 'includes/roles.php';
require_once LOJA_CORE_PATH . 'includes/admin-menu.php';
require_once LOJA_CORE_PATH . 'includes/cpt-produtos.php';
require_once LOJA_CORE_PATH . 'includes/cpt-contagens.php';
require_once LOJA_CORE_PATH . 'includes/prices.php';
require_once LOJA_CORE_PATH . 'includes/purchases.php';

register_activation_hook(__FILE__, 'loja_core_activate');

function loja_core_activate() {
    loja_core_setup_roles();
    loja_core_register_cpts();
    flush_rewrite_rules();
}

add_action('init', 'loja_core_register_cpts');
add_action('admin_menu', 'loja_core_register_menu');
add_action('admin_enqueue_scripts', 'loja_core_admin_assets');
