<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_loja_salvar_compra', 'loja_core_salvar_compra');

function loja_core_salvar_compra() {
    if (!current_user_can('loja_manage_purchases')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }
    if (!isset($_POST['_loja_compra_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_loja_compra_nonce'])), 'loja_compra')) {
        wp_die(__('Falha de segurança.', 'loja-core'));
    }

    $itens = isset($_POST['compra']) && is_array($_POST['compra']) ? $_POST['compra'] : array();
    $items_clean = array();
    foreach ($itens as $produto_id => $info) {
        $pid = absint($produto_id);
        if ($pid <= 0) {
            continue;
        }
        $price = floatval($info['preco'] ?? 0);
        $qty   = floatval($info['qty'] ?? 0);
        $obs   = sanitize_text_field($info['obs'] ?? '');
        $items_clean[] = array(
            'produto_id' => $pid,
            'preco'      => $price,
            'qty'        => $qty,
            'obs'        => $obs,
        );
    }

    $now = current_time('mysql');
    $post_id = wp_insert_post(array(
        'post_type'   => 'loja_compra',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Sessão de compras %s', 'loja-core'), $now),
        'post_author' => get_current_user_id(),
    ));

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_loja_compra_itens', wp_json_encode($items_clean));
        update_post_meta($post_id, '_loja_compra_data', $now);
    }

    wp_safe_redirect(admin_url('admin.php?page=loja-modo-compras&message=saved'));
    exit;
}
