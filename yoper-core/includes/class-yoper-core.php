<?php
if (!defined('ABSPATH')) {
    exit;
}

class Yoper_Core {
    private static $instance = null;
    private $network_mode = false;

    public static function init($network_mode = false) {
        if (null === self::$instance) {
            self::$instance = new self($network_mode);
        }

        return self::$instance;
    }

    private function __construct($network_mode = false) {
        $this->network_mode = (bool) $network_mode;

        $this->load_operation_modules();

        add_action('init', array($this, 'load_textdomain'));
        add_action('init', 'yoper_core_register_cpts');
        add_action('admin_menu', 'yoper_core_register_admin_menu');
        add_action('network_admin_menu', 'yoper_core_register_network_menu');
        add_action('admin_init', 'yoper_core_register_settings');
    }

    public function load_textdomain() {
        load_plugin_textdomain('yoper-core', false, dirname(YOPER_CORE_BASENAME) . '/languages');
    }

    public function is_network_mode() {
        return $this->network_mode;
    }

    private function load_operation_modules() {
        require_once YOPER_CORE_PATH . 'includes/operation/products.php';
        require_once YOPER_CORE_PATH . 'includes/operation/stock-count.php';
        require_once YOPER_CORE_PATH . 'includes/operation/purchase-list.php';
        require_once YOPER_CORE_PATH . 'includes/operation/price-entry.php';
    }
}
