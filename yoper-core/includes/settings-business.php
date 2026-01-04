<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_core_register_settings() {
    register_setting(
        'yoper_core',
        'yoper_core_business_name',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    add_settings_section(
        'yoper_core_section_business',
        __('Business Settings', 'yoper-core'),
        'yoper_core_settings_section_description',
        'yoper-core'
    );

    add_settings_field(
        'yoper_core_business_name',
        __('Business name', 'yoper-core'),
        'yoper_core_business_name_field',
        'yoper-core',
        'yoper_core_section_business'
    );

    if (is_multisite()) {
        register_setting(
            'yoper_core_network',
            'yoper_core_network_business_name',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            )
        );

        add_settings_section(
            'yoper_core_network_section',
            __('Network Defaults', 'yoper-core'),
            'yoper_core_network_section_description',
            'yoper-core-network'
        );

        add_settings_field(
            'yoper_core_network_business_name',
            __('Network business name', 'yoper-core'),
            'yoper_core_network_business_name_field',
            'yoper-core-network',
            'yoper_core_network_section'
        );
    }
}

function yoper_core_settings_section_description() {
    echo '<p>' . esc_html__('Shared business metadata available to the theme and other plugins.', 'yoper-core') . '</p>';
}

function yoper_core_business_name_field() {
    $value = yoper_core_get_option('yoper_core_business_name', '');

    echo '<input type="text" name="yoper_core_business_name" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">' . esc_html__('Used for headers, footers, and system messages.', 'yoper-core') . '</p>';
}

function yoper_core_network_section_description() {
    echo '<p>' . esc_html__('Defaults applied when the plugin is network-activated.', 'yoper-core') . '</p>';
}

function yoper_core_network_business_name_field() {
    $value = yoper_core_get_option('yoper_core_network_business_name', '', true);

    echo '<input type="text" name="yoper_core_network_business_name" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">' . esc_html__('Acts as a fallback for new sites in the network.', 'yoper-core') . '</p>';
}

function yoper_core_get_business_name($network = false) {
    $key = $network ? 'yoper_core_network_business_name' : 'yoper_core_business_name';

    return yoper_core_get_option($key, '', $network);
}
