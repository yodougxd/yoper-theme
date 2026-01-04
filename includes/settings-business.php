<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_register_business_settings() {
    register_setting(
        'yoper_business_settings_group',
        'yoper_business_settings',
        'yoper_sanitize_business_settings'
    );

    add_settings_section(
        'yoper_business_info_section',
        __('Informações do Negócio', 'yoper-theme'),
        'yoper_business_settings_section_intro',
        'yoper-business-settings'
    );

    add_settings_field(
        'yoper_business_name',
        __('Nome do negócio', 'yoper-theme'),
        'yoper_business_render_text_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_name',
            'option_key' => 'business_name',
            'required'   => true,
        )
    );

    add_settings_field(
        'yoper_business_slogan',
        __('Slogan', 'yoper-theme'),
        'yoper_business_render_text_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_slogan',
            'option_key' => 'slogan',
        )
    );

    add_settings_field(
        'yoper_business_whatsapp',
        __('WhatsApp', 'yoper-theme'),
        'yoper_business_render_text_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_whatsapp',
            'option_key' => 'whatsapp',
            'description'=> __('Inclua DDD. Ex.: (11) 99999-9999', 'yoper-theme'),
        )
    );

    add_settings_field(
        'yoper_business_phone',
        __('Telefone', 'yoper-theme'),
        'yoper_business_render_text_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_phone',
            'option_key' => 'phone',
        )
    );

    add_settings_field(
        'yoper_business_address',
        __('Endereço', 'yoper-theme'),
        'yoper_business_render_text_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_address',
            'option_key' => 'address',
            'class'      => 'regular-text code',
        )
    );

    add_settings_field(
        'yoper_business_city',
        __('Cidade', 'yoper-theme'),
        'yoper_business_render_text_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_city',
            'option_key' => 'city',
        )
    );

    add_settings_field(
        'yoper_business_hours',
        __('Horário de funcionamento', 'yoper-theme'),
        'yoper_business_render_textarea_field',
        'yoper-business-settings',
        'yoper_business_info_section',
        array(
            'label_for'  => 'yoper_business_hours',
            'option_key' => 'hours',
            'description'=> __('Texto livre, por exemplo: Seg a Sex: 9h às 18h; Sáb: 9h às 14h', 'yoper-theme'),
        )
    );

    add_settings_section(
        'yoper_business_links_section',
        __('Links', 'yoper-theme'),
        '__return_false',
        'yoper-business-settings'
    );

    add_settings_field(
        'yoper_business_instagram',
        __('Instagram', 'yoper-theme'),
        'yoper_business_render_url_field',
        'yoper-business-settings',
        'yoper_business_links_section',
        array(
            'label_for'  => 'yoper_business_instagram',
            'option_key' => 'instagram',
            'placeholder'=> 'https://instagram.com/seu-negocio',
        )
    );

    add_settings_field(
        'yoper_business_delivery',
        __('iFood/Delivery', 'yoper-theme'),
        'yoper_business_render_url_field',
        'yoper-business-settings',
        'yoper_business_links_section',
        array(
            'label_for'  => 'yoper_business_delivery',
            'option_key' => 'delivery',
            'placeholder'=> 'https://www.ifood.com.br/',
        )
    );

    add_settings_field(
        'yoper_business_site',
        __('Site', 'yoper-theme'),
        'yoper_business_render_url_field',
        'yoper-business-settings',
        'yoper_business_links_section',
        array(
            'label_for'  => 'yoper_business_site',
            'option_key' => 'site',
            'placeholder'=> 'https://www.seusite.com',
        )
    );
}
add_action('admin_init', 'yoper_register_business_settings');

function yoper_business_settings_section_intro() {
    echo '<p>' . esc_html__('Defina as informações gerais exibidas no site.', 'yoper-theme') . '</p>';
}

function yoper_sanitize_business_settings($input) {
    $sanitized = array();

    $sanitized['business_name'] = isset($input['business_name']) ? sanitize_text_field(wp_unslash($input['business_name'])) : '';
    $sanitized['slogan']        = isset($input['slogan']) ? sanitize_text_field(wp_unslash($input['slogan'])) : '';
    $sanitized['whatsapp']      = isset($input['whatsapp']) ? sanitize_text_field(wp_unslash($input['whatsapp'])) : '';
    $sanitized['phone']         = isset($input['phone']) ? sanitize_text_field(wp_unslash($input['phone'])) : '';
    $sanitized['address']       = isset($input['address']) ? sanitize_text_field(wp_unslash($input['address'])) : '';
    $sanitized['city']          = isset($input['city']) ? sanitize_text_field(wp_unslash($input['city'])) : '';
    $sanitized['hours']         = isset($input['hours']) ? sanitize_textarea_field(wp_unslash($input['hours'])) : '';
    $sanitized['instagram']     = isset($input['instagram']) ? esc_url_raw(wp_unslash($input['instagram'])) : '';
    $sanitized['delivery']      = isset($input['delivery']) ? esc_url_raw(wp_unslash($input['delivery'])) : '';
    $sanitized['site']          = isset($input['site']) ? esc_url_raw(wp_unslash($input['site'])) : '';

    return $sanitized;
}

function yoper_render_business_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated'])) {
        add_settings_error(
            'yoper_business_settings',
            'yoper_business_settings_updated',
            __('Configurações salvas com sucesso.', 'yoper-theme'),
            'updated'
        );
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Configurações do Negócio', 'yoper-theme'); ?></h1>
        <?php settings_errors('yoper_business_settings'); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('yoper_business_settings_group');
            do_settings_sections('yoper-business-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function yoper_business_render_text_field($args) {
    $options = yoper_get_business_settings();
    $key     = $args['option_key'];
    $value   = isset($options[$key]) ? $options[$key] : '';
    $class   = isset($args['class']) ? $args['class'] : 'regular-text';
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
    $required    = !empty($args['required']);
    $attributes  = '';

    if ($placeholder) {
        $attributes .= ' placeholder="' . esc_attr($placeholder) . '"';
    }

    if ($required) {
        $attributes .= ' required aria-required="true"';
    }

    printf(
        '<input type="text" id="%1$s" name="yoper_business_settings[%2$s]" class="%3$s" value="%4$s"%5$s />',
        esc_attr($args['label_for']),
        esc_attr($key),
        esc_attr($class),
        esc_attr($value),
        $attributes
    );

    if (!empty($args['description'])) {
        printf('<p class="description">%s</p>', esc_html($args['description']));
    }
}

function yoper_business_render_url_field($args) {
    $options     = yoper_get_business_settings();
    $key         = $args['option_key'];
    $value       = isset($options[$key]) ? $options[$key] : '';
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';

    printf(
        '<input type="url" id="%1$s" name="yoper_business_settings[%2$s]" class="regular-text code" value="%3$s" placeholder="%4$s" />',
        esc_attr($args['label_for']),
        esc_attr($key),
        esc_attr($value),
        esc_attr($placeholder)
    );

    if (!empty($args['description'])) {
        printf('<p class="description">%s</p>', esc_html($args['description']));
    }
}

function yoper_business_render_textarea_field($args) {
    $options = yoper_get_business_settings();
    $key     = $args['option_key'];
    $value   = isset($options[$key]) ? $options[$key] : '';

    printf(
        '<textarea id="%1$s" name="yoper_business_settings[%2$s]" class="large-text" rows="4">%3$s</textarea>',
        esc_attr($args['label_for']),
        esc_attr($key),
        esc_textarea($value)
    );

    if (!empty($args['description'])) {
        printf('<p class="description">%s</p>', esc_html($args['description']));
    }
}
