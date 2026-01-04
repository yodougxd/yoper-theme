<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_yoper_generate_purchase_list', 'yoper_core_generate_purchase_list_from_count');
add_action('add_meta_boxes', 'yoper_core_register_purchase_list_metaboxes');
add_action('save_post_yoper_purchase_list', 'yoper_core_save_purchase_list_meta');
add_action('admin_post_yoper_update_purchase_list_mode', 'yoper_core_update_purchase_list_mode');
add_action('admin_post_yoper_save_purchase_progress', 'yoper_core_save_purchase_progress');

function yoper_core_generate_purchase_list_from_count() {
    if (!current_user_can('yoper_do_stock_count')) {
        wp_die(esc_html__('Você não tem permissão para gerar listas de compras.', 'yoper-core'));
    }

    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'yoper_generate_purchase_list')) {
        wp_die(esc_html__('Falha de verificação de segurança.', 'yoper-core'));
    }

    $count_id = isset($_GET['count_id']) ? absint($_GET['count_id']) : 0;
    if ($count_id <= 0) {
        wp_die(esc_html__('Contagem inválida.', 'yoper-core'));
    }

    $items_json = get_post_meta($count_id, '_yoper_stock_count_items', true);
    $items_data = $items_json ? json_decode($items_json, true) : array();

    if (!is_array($items_data)) {
        $items_data = array();
    }

    $purchase_items = array();

    foreach ($items_data as $entry) {
        $product_id   = isset($entry['product_id']) ? absint($entry['product_id']) : 0;
        $qty_counted  = isset($entry['qty_counted']) ? floatval($entry['qty_counted']) : 0;
        $stock_min    = isset($entry['stock_min']) ? floatval($entry['stock_min']) : 0;

        if ($product_id <= 0) {
            continue;
        }

        $unit = yoper_core_get_product_meta($product_id, 'unit');

        $needed = max($stock_min - $qty_counted, 0);

        if ($needed > 0) {
            $purchase_items[] = array(
                'product_id' => $product_id,
                'qty_needed' => $needed,
                'unit'       => $unit,
            );
        }
    }

    $now     = current_time('mysql');
    $user_id = get_current_user_id();

    $post_id = wp_insert_post(array(
        'post_type'   => 'yoper_purchase_list',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Lista de compras em %s', 'yoper-core'), $now),
        'post_author' => $user_id,
    ));

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, 'yoper_purchase_list_source_stock_count_id', $count_id);
        update_post_meta($post_id, 'yoper_purchase_list_created_by', $user_id);
        update_post_meta($post_id, 'yoper_purchase_list_mode', 'draft');
        update_post_meta($post_id, 'yoper_purchase_list_items', wp_json_encode($purchase_items));
        update_post_meta($post_id, 'yoper_purchase_list_notes', '');
    }

    $redirect = admin_url('post.php?post=' . $post_id . '&action=edit');
    wp_safe_redirect($redirect);
    exit;
}

function yoper_core_update_purchase_list_mode() {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'yoper_purchase_list_mode')) {
        wp_die(esc_html__('Falha de verificação de segurança.', 'yoper-core'));
    }

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $mode    = isset($_POST['mode']) ? sanitize_text_field(wp_unslash($_POST['mode'])) : 'draft';

    if ($post_id <= 0) {
        wp_die(esc_html__('Lista inválida.', 'yoper-core'));
    }

    if (!current_user_can('yoper_manage_purchase_lists', $post_id)) {
        wp_die(esc_html__('Você não tem permissão para alterar o status.', 'yoper-core'));
    }

    $allowed_modes = array('draft', 'shopping', 'done');
    if (!in_array($mode, $allowed_modes, true)) {
        $mode = 'draft';
    }

    update_post_meta($post_id, 'yoper_purchase_list_mode', $mode);

    if ('done' === $mode) {
        update_post_meta($post_id, 'yoper_purchase_list_completed_by', get_current_user_id());
        update_post_meta($post_id, 'yoper_purchase_list_completed_at', current_time('mysql'));
    }

    $redirect = admin_url('post.php?post=' . $post_id . '&action=edit');
    wp_safe_redirect($redirect);
    exit;
}

function yoper_core_save_purchase_progress() {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'yoper_purchase_progress')) {
        wp_die(esc_html__('Falha de verificação de segurança.', 'yoper-core'));
    }

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

    if ($post_id <= 0) {
        wp_die(esc_html__('Lista inválida.', 'yoper-core'));
    }

    if (!current_user_can('yoper_manage_purchase_lists', $post_id)) {
        wp_die(esc_html__('Você não tem permissão para salvar esta lista.', 'yoper-core'));
    }

    $items_json = get_post_meta($post_id, 'yoper_purchase_list_items', true);
    $items      = $items_json ? json_decode($items_json, true) : array();

    if (!is_array($items)) {
        $items = array();
    }

    $quantities = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : array();

    foreach ($items as &$item) {
        $product_id = isset($item['product_id']) ? absint($item['product_id']) : 0;
        if ($product_id && isset($quantities[$product_id]['bought'])) {
            $item['qty_bought'] = floatval(wp_unslash($quantities[$product_id]['bought']));
            $item['done']       = !empty($quantities[$product_id]['done']) ? 1 : 0;
        }
    }
    unset($item);

    update_post_meta($post_id, 'yoper_purchase_list_items', wp_json_encode($items));

    $redirect = admin_url('post.php?post=' . $post_id . '&action=edit&message=progress_saved');
    wp_safe_redirect($redirect);
    exit;
}

function yoper_core_register_purchase_list_metaboxes() {
    add_meta_box(
        'yoper_purchase_list_details',
        __('Detalhes da lista', 'yoper-core'),
        'yoper_core_render_purchase_list_details_metabox',
        'yoper_purchase_list',
        'side'
    );

    add_meta_box(
        'yoper_purchase_list_items',
        __('Itens necessários', 'yoper-core'),
        'yoper_core_render_purchase_list_items_metabox',
        'yoper_purchase_list',
        'normal',
        'high'
    );
}

function yoper_core_render_purchase_list_details_metabox($post) {
    if (!current_user_can('yoper_view_purchase_lists', $post->ID)) {
        wp_die(esc_html__('Você não tem permissão para visualizar esta lista.', 'yoper-core'));
    }

    wp_nonce_field('yoper_purchase_list_meta', 'yoper_purchase_list_meta_nonce');

    $mode_options = array(
        'draft'    => __('Rascunho', 'yoper-core'),
        'shopping' => __('Compras', 'yoper-core'),
        'done'     => __('Finalizada', 'yoper-core'),
    );

    $mode         = get_post_meta($post->ID, 'yoper_purchase_list_mode', true);
    $source_id    = get_post_meta($post->ID, 'yoper_purchase_list_source_stock_count_id', true);
    $notes        = get_post_meta($post->ID, 'yoper_purchase_list_notes', true);
    $can_manage   = current_user_can('yoper_manage_purchase_lists', $post->ID);

    ?>
    <p>
        <strong><?php esc_html_e('Modo', 'yoper-core'); ?></strong><br />
        <?php if ($can_manage) : ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('yoper_purchase_list_mode'); ?>
                <input type="hidden" name="action" value="yoper_update_purchase_list_mode" />
                <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>" />
                <select name="mode">
                    <?php foreach ($mode_options as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($mode, $value); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button"><?php esc_html_e('Atualizar modo', 'yoper-core'); ?></button>
            </form>
        <?php else : ?>
            <span class="description"><?php echo esc_html($mode_options[$mode] ?? $mode_options['draft']); ?></span>
        <?php endif; ?>
    </p>
    <p>
        <strong><?php esc_html_e('Observações', 'yoper-core'); ?></strong><br />
        <textarea name="yoper_purchase_list_notes" rows="4" style="width:100%;"><?php echo esc_textarea($notes); ?></textarea>
    </p>
    <p>
        <strong><?php esc_html_e('Contagem de origem', 'yoper-core'); ?>:</strong>
        <?php if ($source_id) : ?>
            <a href="<?php echo esc_url(admin_url('post.php?post=' . absint($source_id) . '&action=edit')); ?>">
                <?php printf(esc_html__('Contagem #%d', 'yoper-core'), absint($source_id)); ?>
            </a>
        <?php else : ?>
            <?php esc_html_e('Não informado', 'yoper-core'); ?>
        <?php endif; ?>
    </p>
    <?php
}

function yoper_core_render_purchase_list_items_metabox($post) {
    if (!current_user_can('yoper_view_purchase_lists', $post->ID)) {
        wp_die(esc_html__('Você não tem permissão para visualizar esta lista.', 'yoper-core'));
    }

    $items_json = get_post_meta($post->ID, 'yoper_purchase_list_items', true);
    $items      = $items_json ? json_decode($items_json, true) : array();

    if (!is_array($items)) {
        $items = array();
    }
    ?>
    <div class="yoper-actions" style="margin-bottom:12px;">
        <?php if (current_user_can('yoper_manage_purchase_lists', $post->ID)) : ?>
            <button type="button" class="button" onclick="window.print();"><?php esc_html_e('Modo impressão', 'yoper-core'); ?></button>
        <?php endif; ?>
    </div>

    <table class="widefat fixed striped yoper-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Produto', 'yoper-core'); ?></th>
                <th><?php esc_html_e('Qtd necessária', 'yoper-core'); ?></th>
                <th><?php esc_html_e('Qtd comprada', 'yoper-core'); ?></th>
                <th><?php esc_html_e('Status', 'yoper-core'); ?></th>
                <th><?php esc_html_e('Unidade', 'yoper-core'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)) : ?>
                <?php if (current_user_can('yoper_manage_purchase_lists', $post->ID)) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('yoper_purchase_progress'); ?>
                    <input type="hidden" name="action" value="yoper_save_purchase_progress" />
                    <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>" />
                <?php endif; ?>
                <?php foreach ($items as $item) : ?>
                    <?php
                    $product_id = isset($item['product_id']) ? absint($item['product_id']) : 0;
                    $qty_needed = isset($item['qty_needed']) ? $item['qty_needed'] : '';
                    $qty_bought = isset($item['qty_bought']) ? $item['qty_bought'] : '';
                    $done       = !empty($item['done']);
                    $unit       = isset($item['unit']) ? $item['unit'] : '';
                    ?>
                    <tr>
                        <td>
                            <?php if ($product_id) : ?>
                                <a href="<?php echo esc_url(get_edit_post_link($product_id)); ?>"><?php echo esc_html(get_the_title($product_id)); ?></a>
                            <?php else : ?>
                                <?php esc_html_e('N/A', 'yoper-core'); ?>
                            <?php endif; ?>
                        </td>
                    <td><?php echo esc_html($qty_needed); ?></td>
                    <td>
                        <?php if (current_user_can('yoper_manage_purchase_lists', $post->ID)) : ?>
                            <input type="number" step="0.01" min="0" name="items[<?php echo esc_attr($product_id); ?>][bought]" value="<?php echo esc_attr($qty_bought); ?>" />
                        <?php else : ?>
                            <?php echo esc_html($qty_bought); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (current_user_can('yoper_manage_purchase_lists', $post->ID)) : ?>
                            <label>
                                <input type="checkbox" name="items[<?php echo esc_attr($product_id); ?>][done]" value="1" <?php checked($done); ?> />
                                <?php esc_html_e('Comprado', 'yoper-core'); ?>
                            </label>
                        <?php else : ?>
                            <?php echo $done ? esc_html__('Comprado', 'yoper-core') : esc_html__('Pendente', 'yoper-core'); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($unit); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (current_user_can('yoper_manage_purchase_lists', $post->ID)) : ?>
                    <tr>
                        <td colspan="5">
                            <button class="button button-primary" type="submit"><?php esc_html_e('Salvar progresso', 'yoper-core'); ?></button>
                        </td>
                    </tr>
                </form>
                <?php endif; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php esc_html_e('Nenhum item necessário.', 'yoper-core'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    if (!empty($items)) {
        $lines = array();
        foreach ($items as $item) {
            $product_id = isset($item['product_id']) ? absint($item['product_id']) : 0;
            $title = $product_id ? get_the_title($product_id) : '';
            $qty = isset($item['qty_needed']) ? $item['qty_needed'] : '';
            $unit = isset($item['unit']) ? $item['unit'] : '';
            $lines[] = trim($title . ' - ' . $qty . ' ' . $unit);
        }
        $text = implode("\n", $lines);
        ?>
        <div class="yoper-actions" style="margin-top:12px;">
            <button class="button yoper-copy-btn" data-yoper-copy-list="<?php echo esc_attr($text); ?>"><?php esc_html_e('Copiar para WhatsApp', 'yoper-core'); ?></button>
        </div>
        <div class="yoper-print" aria-hidden="true">
            <pre><?php echo esc_html($text); ?></pre>
        </div>
    <?php } ?>
    <?php
}

function yoper_core_save_purchase_list_meta($post_id) {
    if (!isset($_POST['yoper_purchase_list_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yoper_purchase_list_meta_nonce'])), 'yoper_purchase_list_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('yoper_view_purchase_lists', $post_id)) {
        wp_die(esc_html__('Você não tem permissão para salvar esta lista.', 'yoper-core'));
    }

    $can_manage = current_user_can('yoper_manage_purchase_lists', $post_id);

    $mode  = isset($_POST['yoper_purchase_list_mode']) ? sanitize_text_field(wp_unslash($_POST['yoper_purchase_list_mode'])) : 'draft';
    $notes = isset($_POST['yoper_purchase_list_notes']) ? sanitize_textarea_field(wp_unslash($_POST['yoper_purchase_list_notes'])) : '';

    $allowed_modes = array('draft', 'shopping', 'done');
    if (!in_array($mode, $allowed_modes, true)) {
        $mode = 'draft';
    }

    if (!$can_manage && 'draft' !== $mode) {
        $mode = get_post_meta($post_id, 'yoper_purchase_list_mode', true);
        if (!in_array($mode, $allowed_modes, true)) {
            $mode = 'draft';
        }
    }

    update_post_meta($post_id, 'yoper_purchase_list_mode', $mode);
    update_post_meta($post_id, 'yoper_purchase_list_notes', $notes);
}
