<?php
if (!defined('ABSPATH')) {
    exit;
}

// Default business settings keys.
if (!function_exists('yoper_business_settings_defaults')) {
    function yoper_business_settings_defaults() {
        return array(
            'business_name' => '',
            'slogan'        => '',
            'whatsapp'      => '',
            'phone'         => '',
            'address'       => '',
            'city'          => '',
            'hours'         => '',
            'instagram'     => '',
            'delivery'      => '',
            'site'          => '',
        );
    }
}

// Returns business settings merged with defaults.
if (!function_exists('yoper_get_business_settings')) {
    function yoper_get_business_settings() {
        $options = get_option('yoper_business_settings', array());

        if (!is_array($options)) {
            $options = array();
        }

        return wp_parse_args($options, yoper_business_settings_defaults());
    }
}

// Returns a single business setting with a fallback.
if (!function_exists('yoper_get_business_setting')) {
    function yoper_get_business_setting($key, $default = '') {
        $settings = yoper_get_business_settings();

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return $default;
    }
}

// Builds a WhatsApp wa.me link from a number and optional message.
if (!function_exists('yoper_build_whatsapp_link')) {
    function yoper_build_whatsapp_link($number, $message = '') {
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
}

// Gets the WhatsApp link using saved settings (with optional fallback number).
if (!function_exists('yoper_get_whatsapp_link')) {
    function yoper_get_whatsapp_link($message = '', $fallback_number = '') {
        $number = yoper_get_business_setting('whatsapp', $fallback_number);

        return yoper_build_whatsapp_link($number, $message);
    }
}
