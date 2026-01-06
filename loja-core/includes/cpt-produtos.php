<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_register_cpts() {
    loja_core_register_produtos();
    loja_core_register_contagens();
    loja_core_register_precos();
}

function loja_core_register_produtos() {
    register_taxonomy('loja_categoria_produto', 'loja_produto', array(
        'label'        => __('Categorias', 'loja-core'),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => false,
        'hierarchical' => true,
    ));

    $args = array(
        'labels' => array(
            'name'          => __('Produtos', 'loja-core'),
            'singular_name' => __('Produto', 'loja-core'),
        ),
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'supports'           => array('title'),
        'capability_type'    => array('loja_produto', 'loja_produtos'),
        'map_meta_cap'       => true,
        'exclude_from_search'=> true,
    );
    register_post_type('loja_produto', $args);
}

add_action('add_meta_boxes', function () {
    add_meta_box('loja_produto_dados', __('Dados do Produto', 'loja-core'), 'loja_core_produto_metabox', 'loja_produto', 'normal', 'high');
});

function loja_core_produto_metabox($post) {
    if (!current_user_can('loja_manage_products')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }
    wp_nonce_field('loja_produto_meta', 'loja_produto_nonce');

    $sku        = get_post_meta($post->ID, '_loja_sku', true);
    $unidade    = get_post_meta($post->ID, '_loja_unidade', true);
    $fornecedor = get_post_meta($post->ID, '_loja_fornecedor', true);
    $estoque_min= get_post_meta($post->ID, '_loja_estoque_min', true);
    $ativo      = get_post_meta($post->ID, '_loja_ativo', true);
    ?>
    <div class="field">
        <label class="label"><?php esc_html_e('SKU', 'loja-core'); ?></label>
        <div class="control"><input class="input" type="text" name="loja_sku" value="<?php echo esc_attr($sku); ?>" /></div>
    </div>
    <div class="field">
        <label class="label"><?php esc_html_e('Unidade', 'loja-core'); ?></label>
        <div class="control">
            <div class="select">
                <select name="loja_unidade">
                    <?php foreach (array('un', 'kg', 'cx') as $u) : ?>
                        <option value="<?php echo esc_attr($u); ?>" <?php selected($unidade, $u); ?>><?php echo esc_html($u); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="field">
        <label class="label"><?php esc_html_e('Fornecedor padrão', 'loja-core'); ?></label>
        <div class="control"><input class="input" type="text" name="loja_fornecedor" value="<?php echo esc_attr($fornecedor); ?>" /></div>
    </div>
    <div class="field">
        <label class="label"><?php esc_html_e('Estoque mínimo', 'loja-core'); ?></label>
        <div class="control"><input class="input" type="number" step="0.01" min="0" name="loja_estoque_min" value="<?php echo esc_attr($estoque_min); ?>" /></div>
    </div>
    <div class="field">
        <label class="checkbox">
            <input type="checkbox" name="loja_ativo" value="1" <?php checked($ativo, '1'); ?> />
            <?php esc_html_e('Ativo', 'loja-core'); ?>
        </label>
    </div>
    <?php
}

add_action('save_post_loja_produto', function ($post_id) {
    if (!isset($_POST['loja_produto_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['loja_produto_nonce'])), 'loja_produto_meta')) {
        return;
    }
    if (!current_user_can('loja_manage_products', $post_id)) {
        return;
    }
    update_post_meta($post_id, '_loja_sku', sanitize_text_field(wp_unslash($_POST['loja_sku'] ?? '')));
    update_post_meta($post_id, '_loja_unidade', sanitize_text_field(wp_unslash($_POST['loja_unidade'] ?? '')));
    update_post_meta($post_id, '_loja_fornecedor', sanitize_text_field(wp_unslash($_POST['loja_fornecedor'] ?? '')));
    update_post_meta($post_id, '_loja_estoque_min', floatval($_POST['loja_estoque_min'] ?? 0));
    update_post_meta($post_id, '_loja_ativo', isset($_POST['loja_ativo']) ? '1' : '0');
});
