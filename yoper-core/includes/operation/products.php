<?php
if (!defined('ABSPATH')) {
    exit;
}

// Helpers to read/write product meta with fallback to legacy keys.
function yoper_core_get_product_meta($post_id, $key, $default = '') {
    $new_key = 'yoper_product_' . $key;
    $old_key = '_yoper_product_' . $key;

    $value = get_post_meta($post_id, $new_key, true);
    if ('' === $value) {
        $value = get_post_meta($post_id, $old_key, true);
    }

    return '' === $value ? $default : $value;
}

function yoper_core_update_product_meta($post_id, $key, $value) {
    $new_key = 'yoper_product_' . $key;
    $old_key = '_yoper_product_' . $key;

    update_post_meta($post_id, $new_key, $value);

    if (metadata_exists('post', $post_id, $old_key)) {
        delete_post_meta($post_id, $old_key);
    }
}

add_action('add_meta_boxes', 'yoper_core_register_product_metaboxes');
function yoper_core_register_product_metaboxes() {
    add_meta_box(
        'yoper_product_details',
        __('Detalhes do produto', 'yoper-core'),
        'yoper_core_render_product_metabox',
        'yoper_product',
        'normal',
        'high'
    );
}

function yoper_core_render_product_metabox($post) {
    if (!current_user_can('yoper_manage_products')) {
        wp_die(esc_html__('Você não tem permissão para editar este produto.', 'yoper-core'));
    }

    wp_nonce_field('yoper_product_meta', 'yoper_product_meta_nonce');

    $sku              = yoper_core_get_product_meta($post->ID, 'sku');
    $unit             = yoper_core_get_product_meta($post->ID, 'unit');
    $category_text    = yoper_core_get_product_meta($post->ID, 'category_text');
    $stock_min        = yoper_core_get_product_meta($post->ID, 'stock_min');
    $stock_current    = yoper_core_get_product_meta($post->ID, 'stock_current');
    $cost_reference   = yoper_core_get_product_meta($post->ID, 'cost_reference');
    $is_active        = yoper_core_get_product_meta($post->ID, 'active');
    $unit_options     = array('un', 'kg', 'g', 'l', 'ml', 'cx');

    ?>
    <div class="yoper-product-fields">
        <p>
            <label for="yoper_product_sku"><strong><?php esc_html_e('SKU (opcional)', 'yoper-core'); ?></strong></label><br />
            <input type="text" id="yoper_product_sku" name="yoper_product_sku" class="regular-text" value="<?php echo esc_attr($sku); ?>" />
        </p>

        <p>
            <label for="yoper_product_unit"><strong><?php esc_html_e('Unidade', 'yoper-core'); ?></strong></label><br />
            <select id="yoper_product_unit" name="yoper_product_unit">
                <option value=""><?php esc_html_e('Selecione...', 'yoper-core'); ?></option>
                <?php foreach ($unit_options as $option) : ?>
                    <option value="<?php echo esc_attr($option); ?>" <?php selected($unit, $option); ?>><?php echo esc_html($option); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="yoper_product_category_text"><strong><?php esc_html_e('Categoria (texto)', 'yoper-core'); ?></strong></label><br />
            <input type="text" id="yoper_product_category_text" name="yoper_product_category_text" class="regular-text" value="<?php echo esc_attr($category_text); ?>" />
            <span class="description"><?php esc_html_e('Use categorias rápidas; taxonomia pode ser usada opcionalmente.', 'yoper-core'); ?></span>
        </p>

        <p>
            <label for="yoper_product_stock_min"><strong><?php esc_html_e('Estoque mínimo', 'yoper-core'); ?></strong></label><br />
            <input type="number" step="0.01" min="0" id="yoper_product_stock_min" name="yoper_product_stock_min" value="<?php echo esc_attr($stock_min); ?>" />
        </p>

        <p>
            <label for="yoper_product_stock_current"><strong><?php esc_html_e('Estoque atual', 'yoper-core'); ?></strong></label><br />
            <input type="number" step="0.01" min="0" id="yoper_product_stock_current" name="yoper_product_stock_current" value="<?php echo esc_attr($stock_current); ?>" />
        </p>

        <p>
            <label for="yoper_product_cost_reference"><strong><?php esc_html_e('Custo de referência (opcional)', 'yoper-core'); ?></strong></label><br />
            <input type="number" step="0.01" min="0" id="yoper_product_cost_reference" name="yoper_product_cost_reference" value="<?php echo esc_attr($cost_reference); ?>" />
        </p>

        <p>
            <label for="yoper_product_active">
                <input type="checkbox" id="yoper_product_active" name="yoper_product_active" value="1" <?php checked($is_active, '1'); ?> />
                <?php esc_html_e('Produto ativo', 'yoper-core'); ?>
            </label>
        </p>
    </div>
    <?php
}

add_action('save_post_yoper_product', 'yoper_core_save_product_meta');
function yoper_core_save_product_meta($post_id) {
    if (!isset($_POST['yoper_product_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yoper_product_meta_nonce'])), 'yoper_product_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('yoper_manage_products', $post_id)) {
        wp_die(esc_html__('Você não tem permissão para salvar este produto.', 'yoper-core'));
    }

    $allowed_units = array('un', 'kg', 'g', 'l', 'ml', 'cx');

    $sku            = isset($_POST['yoper_product_sku']) ? sanitize_text_field(wp_unslash($_POST['yoper_product_sku'])) : '';
    $unit           = isset($_POST['yoper_product_unit']) ? sanitize_text_field(wp_unslash($_POST['yoper_product_unit'])) : '';
    $category_text  = isset($_POST['yoper_product_category_text']) ? sanitize_text_field(wp_unslash($_POST['yoper_product_category_text'])) : '';
    $stock_min      = isset($_POST['yoper_product_stock_min']) ? floatval(wp_unslash($_POST['yoper_product_stock_min'])) : 0;
    $stock_current  = isset($_POST['yoper_product_stock_current']) ? floatval(wp_unslash($_POST['yoper_product_stock_current'])) : 0;
    $cost_reference = isset($_POST['yoper_product_cost_reference']) ? floatval(wp_unslash($_POST['yoper_product_cost_reference'])) : '';
    $active         = isset($_POST['yoper_product_active']) ? '1' : '0';

    if (!in_array($unit, $allowed_units, true)) {
        $unit = '';
    }

    yoper_core_update_product_meta($post_id, 'sku', $sku);
    yoper_core_update_product_meta($post_id, 'unit', $unit);
    yoper_core_update_product_meta($post_id, 'category_text', $category_text);
    yoper_core_update_product_meta($post_id, 'stock_min', $stock_min);
    yoper_core_update_product_meta($post_id, 'stock_current', $stock_current);
    yoper_core_update_product_meta($post_id, 'cost_reference', $cost_reference);
    yoper_core_update_product_meta($post_id, 'active', $active);
}

add_filter('manage_yoper_product_posts_columns', 'yoper_core_product_columns');
function yoper_core_product_columns($columns) {
    $new = array();
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ('title' === $key) {
            $new['yoper_stock_current'] = __('Estoque atual', 'yoper-core');
            $new['yoper_stock_min']     = __('Mínimo', 'yoper-core');
            $new['yoper_unit']          = __('Unidade', 'yoper-core');
        }
    }
    return $new;
}

add_action('manage_yoper_product_posts_custom_column', 'yoper_core_product_column_content', 10, 2);
function yoper_core_product_column_content($column, $post_id) {
    if ('yoper_stock_current' === $column) {
        $current = yoper_core_get_product_meta($post_id, 'stock_current');
        $min     = yoper_core_get_product_meta($post_id, 'stock_min');

        $current_val = is_numeric($current) ? (float) $current : 0;
        $min_val     = is_numeric($min) ? (float) $min : 0;

        $is_low = $min_val > 0 && $current_val <= $min_val;
        $label  = $is_low ? __('BAIXO', 'yoper-core') : '';

        echo esc_html($current_val);
        if ($label) {
            echo ' <span class="yoper-stock-low">' . esc_html($label) . '</span>';
        }
    }

    if ('yoper_stock_min' === $column) {
        $min = yoper_core_get_product_meta($post_id, 'stock_min');
        echo esc_html(is_numeric($min) ? (float) $min : '');
    }

    if ('yoper_unit' === $column) {
        $unit = yoper_core_get_product_meta($post_id, 'unit');
        echo esc_html($unit);
    }
}

add_action('admin_head-edit.php', 'yoper_core_product_list_styles');
function yoper_core_product_list_styles() {
    $screen = get_current_screen();
    if (!isset($screen->post_type) || 'yoper_product' !== $screen->post_type) {
        return;
    }
    ?>
    <style>
        .yoper-stock-low {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 600;
            font-size: 11px;
        }
    </style>
    <?php
}
