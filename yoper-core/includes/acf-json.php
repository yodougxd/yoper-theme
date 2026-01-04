<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_core_acf_json_path() {
    return trailingslashit(YOPER_CORE_PATH . 'acf-json');
}

add_filter('acf/settings/save_json', 'yoper_core_acf_save_json_path');
function yoper_core_acf_save_json_path($path) {
    $custom_path = yoper_core_acf_json_path();

    if (!file_exists($custom_path)) {
        wp_mkdir_p($custom_path);
    }

    return $custom_path;
}

add_filter('acf/settings/load_json', 'yoper_core_acf_load_json_paths');
function yoper_core_acf_load_json_paths($paths) {
    $paths[] = yoper_core_acf_json_path();

    return $paths;
}

function yoper_core_acf_business_fields() {
    return array(
        'business_name',
        'slogan',
        'whatsapp',
        'phone',
        'address',
        'city',
        'hours',
        'instagram',
        'delivery',
        'site',
    );
}

function yoper_core_acf_should_handle_business_field($post_id, $field_name) {
    if ('options' !== $post_id) {
        return false;
    }

    return in_array($field_name, yoper_core_acf_business_fields(), true);
}

add_filter('acf/load_value', 'yoper_core_acf_load_business_field_value', 10, 3);
function yoper_core_acf_load_business_field_value($value, $post_id, $field) {
    $field_name = isset($field['name']) ? $field['name'] : '';

    if (!yoper_core_acf_should_handle_business_field($post_id, $field_name)) {
        return $value;
    }

    $options = get_option('yoper_business_settings', array());

    if (!is_array($options)) {
        $options = array();
    }

    if (isset($options[$field_name])) {
        return $options[$field_name];
    }

    return $value;
}

add_filter('acf/update_value', 'yoper_core_acf_update_business_field_value', 10, 3);
function yoper_core_acf_update_business_field_value($value, $post_id, $field) {
    $field_name = isset($field['name']) ? $field['name'] : '';

    if (!yoper_core_acf_should_handle_business_field($post_id, $field_name)) {
        return $value;
    }

    $options = get_option('yoper_business_settings', array());

    if (!is_array($options)) {
        $options = array();
    }

    $options[$field_name] = yoper_core_acf_sanitize_business_field_value($field_name, $value);

    update_option('yoper_business_settings', $options);

    return $options[$field_name];
}

function yoper_core_acf_sanitize_business_field_value($field_name, $value) {
    $value = is_string($value) ? wp_unslash($value) : $value;

    switch ($field_name) {
        case 'hours':
            return sanitize_textarea_field($value);
        case 'instagram':
        case 'delivery':
        case 'site':
            return esc_url_raw($value);
        default:
            return sanitize_text_field($value);
    }
}
