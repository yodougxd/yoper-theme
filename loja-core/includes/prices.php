<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_register_precos() {
    $args = array(
        'labels' => array(
            'name'          => __('Histórico de Preço', 'loja-core'),
            'singular_name' => __('Preço', 'loja-core'),
        ),
        'public'             => false,
        'show_ui'            => false,
        'show_in_menu'       => false,
        'supports'           => array('title'),
        'capability_type'    => array('loja_preco', 'loja_precos'),
        'map_meta_cap'       => true,
    );
    register_post_type('loja_preco', $args);
}

add_action('admin_post_loja_salvar_preco', 'loja_core_salvar_preco');

function loja_core_salvar_preco() {
    if (!current_user_can('loja_manage_price_history')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }
    if (!isset($_POST['_loja_preco_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_loja_preco_nonce'])), 'loja_preco')) {
        wp_die(__('Falha de segurança.', 'loja-core'));
    }

    $produto_id   = absint($_POST['produto_id'] ?? 0);
    $fornecedor   = sanitize_text_field(wp_unslash($_POST['fornecedor'] ?? ''));
    $preco_total  = floatval($_POST['preco_total'] ?? 0);
    $quantidade   = floatval($_POST['quantidade'] ?? 0);
    $data         = sanitize_text_field(wp_unslash($_POST['data'] ?? ''));
    $obs          = sanitize_textarea_field(wp_unslash($_POST['obs'] ?? ''));

    if ($produto_id <= 0 || $preco_total <= 0 || $quantidade <= 0) {
        wp_die(__('Dados inválidos.', 'loja-core'));
    }

    $unit = get_post_meta($produto_id, '_loja_unidade', true);
    $ppu  = $quantidade > 0 ? $preco_total / $quantidade : 0;

    $post_id = wp_insert_post(array(
        'post_type'   => 'loja_preco',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Preço - %s', 'loja-core'), get_the_title($produto_id)),
        'post_author' => get_current_user_id(),
    ));

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_loja_produto_id', $produto_id);
        update_post_meta($post_id, '_loja_fornecedor', $fornecedor);
        update_post_meta($post_id, '_loja_preco_total', $preco_total);
        update_post_meta($post_id, '_loja_quantidade', $quantidade);
        update_post_meta($post_id, '_loja_unidade', $unit);
        update_post_meta($post_id, '_loja_preco_unit', $ppu);
        update_post_meta($post_id, '_loja_data', $data);
        update_post_meta($post_id, '_loja_obs', $obs);
    }

    wp_safe_redirect(admin_url('admin.php?page=loja-precos&message=saved'));
    exit;
}
