<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/includes/helpers.php';
require_once get_template_directory() . '/includes/settings-business.php';
require_once get_template_directory() . '/includes/admin-menu.php';

function yoper_theme_setup() {
    load_theme_textdomain('yoper-theme', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    register_nav_menus(array(
        'principal' => __('Menu Principal', 'yoper-theme'),
    ));
}
add_action('after_setup_theme', 'yoper_theme_setup');

function yoper_theme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'yoper-theme'),
        'id'            => 'sidebar-1',
        'description'   => __('Default sidebar', 'yoper-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'yoper_theme_widgets_init');

function yoper_theme_assets() {
    $theme   = wp_get_theme('yoper-theme');
    $version = $theme->get('Version') ?: null;

    wp_enqueue_style('yoper-theme-style', get_stylesheet_uri(), array(), $version);

    $main_css = get_template_directory() . '/assets/css/main.css';
    if (file_exists($main_css)) {
        wp_enqueue_style(
            'yoper-theme-main',
            get_template_directory_uri() . '/assets/css/main.css',
            array('yoper-theme-style'),
            filemtime($main_css)
        );
    }

    // Inline CSS variables for theming (defaults, can be driven by settings later).
    $inline_vars = ':root{--yoper-primary:#2563eb;--yoper-bg:#f8fafc;--yoper-surface:#ffffff;--yoper-text:#0f172a;--yoper-muted:#475569;--yoper-border:#e2e8f0;}';
    wp_add_inline_style('yoper-theme-main', $inline_vars);

    $main_js = get_template_directory() . '/assets/js/main.js';
    if (file_exists($main_js)) {
        wp_enqueue_script(
            'yoper-theme-main',
            get_template_directory_uri() . '/assets/js/main.js',
            array('jquery'),
            filemtime($main_js),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'yoper_theme_assets');

function yoper_theme_body_classes($classes) {
    if (is_multisite()) {
        $classes[] = 'is-multisite';
    }

    return $classes;
}
add_filter('body_class', 'yoper_theme_body_classes');

// Safely returns business settings from the plugin, falling back to defaults.
function yoper_theme_get_business_settings() {
    $settings = array();

    if (function_exists('yoper_get_business_settings')) {
        $settings = yoper_get_business_settings();
    }

    if (!is_array($settings)) {
        $settings = array();
    }

    if (function_exists('yoper_business_settings_defaults')) {
        $settings = wp_parse_args($settings, yoper_business_settings_defaults());
    }

    return $settings;
}

// Helper to fetch a single business setting with a safe fallback.
function yoper_theme_get_business_setting($key, $default = '') {
    $settings = yoper_theme_get_business_settings();

    if (array_key_exists($key, $settings)) {
        return $settings[$key];
    }

    return $default;
}

// Helper for building a WhatsApp link from saved settings.
function yoper_theme_get_whatsapp_link($message = '', $fallback_number = '') {
    $number = yoper_theme_get_business_setting('whatsapp', $fallback_number);

    if (function_exists('yoper_build_whatsapp_link')) {
        return yoper_build_whatsapp_link($number, $message);
    }

    $digits = preg_replace('/\D+/', '', (string) $number);

    if ('' === $digits) {
        return '';
    }

    $url = 'https://wa.me/' . $digits;

    if ('' !== $message) {
        $url .= '?text=' . rawurlencode($message);
    }

    return $url;
}
