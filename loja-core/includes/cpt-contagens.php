<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_register_contagens() {
    $args = array(
        'labels' => array(
            'name'          => __('Contagens', 'loja-core'),
            'singular_name' => __('Contagem', 'loja-core'),
        ),
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'supports'           => array('title'),
        'capability_type'    => array('loja_contagem', 'loja_contagens'),
        'map_meta_cap'       => true,
    );
    register_post_type('loja_contagem', $args);
}

add_action('admin_post_loja_salvar_contagem', 'loja_core_salvar_contagem');

function loja_core_salvar_contagem() {
    if (!current_user_can('loja_manage_stock_counts')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }
    if (!isset($_POST['_loja_contagem_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_loja_contagem_nonce'])), 'loja_contagem')) {
        wp_die(__('Falha de segurança.', 'loja-core'));
    }
    $itens = isset($_POST['itens']) && is_array($_POST['itens']) ? $_POST['itens'] : array();
    $items_clean = array();
    foreach ($itens as $produto_id => $qtd) {
        $pid = absint($produto_id);
        if ($pid <= 0) {
            continue;
        }
        $qty = floatval(wp_unslash($qtd));
        $min = floatval(get_post_meta($pid, '_loja_estoque_min', true));
        $items_clean[] = array(
            'produto_id'   => $pid,
            'qtd_atual'    => $qty,
            'estoque_min'  => $min,
            'sugestao'     => max($min - $qty, 0),
        );
    }

    $now    = current_time('mysql');
    $post_id = wp_insert_post(array(
        'post_type'   => 'loja_contagem',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Contagem %s', 'loja-core'), $now),
        'post_author' => get_current_user_id(),
    ));
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_loja_itens', wp_json_encode($items_clean));
        update_post_meta($post_id, '_loja_status', 'enviado');
    }

    wp_safe_redirect(admin_url('admin.php?page=loja-contagens&message=saved'));
    exit;
}
