<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_yoper_save_price_entry', 'yoper_core_handle_price_entry_save');

function yoper_core_render_price_research_page() {
    if (!current_user_can('yoper_add_price_entries')) {
        wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'yoper-core'));
    }

    $message = isset($_GET['message']) ? sanitize_text_field(wp_unslash($_GET['message'])) : '';

    $products = get_posts(array(
        'post_type'      => 'yoper_product',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'   => 'yoper_product_active',
                'value' => '1',
            ),
            array(
                'key'   => '_yoper_product_active',
                'value' => '1',
            ),
        ),
    ));

    $report_entries = array();
    if (current_user_can('yoper_view_price_reports')) {
        $report_entries = get_posts(array(
            'post_type'      => 'yoper_price_entry',
            'posts_per_page' => 20,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Pesquisa de Preço', 'yoper-core'); ?></h1>

        <?php if ('saved' === $message) : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Preço registrado com sucesso.', 'yoper-core'); ?></p>
            </div>
        <?php endif; ?>

        <h2><?php esc_html_e('Registrar preço pago', 'yoper-core'); ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="yoper-panel yoper-input-wide">
            <?php wp_nonce_field('yoper_save_price_entry'); ?>
            <input type="hidden" name="action" value="yoper_save_price_entry" />
            <p>
                <label for="yoper_price_product"><strong><?php esc_html_e('Produto', 'yoper-core'); ?></strong></label><br />
                <select name="yoper_price_product" id="yoper_price_product" required>
                    <option value=""><?php esc_html_e('Selecione um produto', 'yoper-core'); ?></option>
                    <?php foreach ($products as $product) : ?>
                        <option value="<?php echo esc_attr($product->ID); ?>"><?php echo esc_html(get_the_title($product)); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="yoper_price_supplier"><strong><?php esc_html_e('Fornecedor', 'yoper-core'); ?></strong></label><br />
                <input type="text" id="yoper_price_supplier" name="yoper_price_supplier" />
            </p>

            <div class="yoper-grid">
                <p>
                    <label for="yoper_price_total"><strong><?php esc_html_e('Preço total pago', 'yoper-core'); ?></strong></label><br />
                    <input type="number" step="0.01" min="0" id="yoper_price_total" name="yoper_price_total" required />
                </p>
                <p>
                    <label for="yoper_price_quantity"><strong><?php esc_html_e('Quantidade', 'yoper-core'); ?></strong></label><br />
                    <input type="number" step="0.01" min="0.0001" id="yoper_price_quantity" name="yoper_price_quantity" required />
                </p>
                <p>
                    <label for="yoper_price_purchased_at"><strong><?php esc_html_e('Data da compra', 'yoper-core'); ?></strong></label><br />
                    <input type="date" id="yoper_price_purchased_at" name="yoper_price_purchased_at" value="<?php echo esc_attr(date('Y-m-d')); ?>" required />
                </p>
            </div>

            <p>
                <label for="yoper_price_notes"><strong><?php esc_html_e('Notas', 'yoper-core'); ?></strong></label><br />
                <textarea id="yoper_price_notes" name="yoper_price_notes" rows="3" style="width:100%;"></textarea>
            </p>

            <p class="yoper-actions">
                <button type="submit" class="button button-primary"><?php esc_html_e('Registrar preço', 'yoper-core'); ?></button>
                <span class="yoper-note"><?php esc_html_e('Campos obrigatórios: produto, preço, quantidade e data.', 'yoper-core'); ?></span>
            </p>
        </form>

        <?php if (current_user_can('yoper_view_price_reports')) : ?>
            <h2><?php esc_html_e('Relatório recente', 'yoper-core'); ?></h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Produto', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Fornecedor', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Preço total', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Qtd', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Preço/Un', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Data', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Notas', 'yoper-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($report_entries)) : ?>
                        <?php foreach ($report_entries as $entry) : ?>
                            <?php
                            $product_id    = get_post_meta($entry->ID, 'yoper_price_product_id', true);
                            $supplier      = get_post_meta($entry->ID, 'yoper_price_supplier', true);
                            $price_total   = get_post_meta($entry->ID, 'yoper_price_total', true);
                            $quantity      = get_post_meta($entry->ID, 'yoper_price_quantity', true);
                            $price_per_unit= get_post_meta($entry->ID, 'yoper_price_per_unit', true);
                            $purchased_at  = get_post_meta($entry->ID, 'yoper_price_purchased_at', true);
                            $notes         = get_post_meta($entry->ID, 'yoper_price_notes', true);
                            ?>
                            <tr>
                                <td>
                                    <?php if ($product_id) : ?>
                                        <a href="<?php echo esc_url(get_edit_post_link($product_id)); ?>"><?php echo esc_html(get_the_title($product_id)); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($supplier); ?></td>
                                <td><?php echo esc_html(number_format((float) $price_total, 2, ',', '.')); ?></td>
                                <td><?php echo esc_html($quantity); ?></td>
                                <td><?php echo esc_html(number_format((float) $price_per_unit, 4, ',', '.')); ?></td>
                                <td><?php echo esc_html($purchased_at); ?></td>
                                <td><?php echo esc_html($notes); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7"><?php esc_html_e('Nenhum registro recente.', 'yoper-core'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

function yoper_core_handle_price_entry_save() {
    if (!current_user_can('yoper_add_price_entries')) {
        wp_die(esc_html__('Você não tem permissão para registrar preços.', 'yoper-core'));
    }

    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'yoper_save_price_entry')) {
        wp_die(esc_html__('Falha de verificação de segurança.', 'yoper-core'));
    }

    $product_id    = isset($_POST['yoper_price_product']) ? absint($_POST['yoper_price_product']) : 0;
    $supplier      = isset($_POST['yoper_price_supplier']) ? sanitize_text_field(wp_unslash($_POST['yoper_price_supplier'])) : '';
    $price_total   = isset($_POST['yoper_price_total']) ? floatval(wp_unslash($_POST['yoper_price_total'])) : 0;
    $quantity      = isset($_POST['yoper_price_quantity']) ? floatval(wp_unslash($_POST['yoper_price_quantity'])) : 0;
    $purchased_at  = isset($_POST['yoper_price_purchased_at']) ? sanitize_text_field(wp_unslash($_POST['yoper_price_purchased_at'])) : '';
    $notes         = isset($_POST['yoper_price_notes']) ? sanitize_textarea_field(wp_unslash($_POST['yoper_price_notes'])) : '';

    if ($product_id <= 0 || $price_total <= 0 || $quantity <= 0) {
        wp_die(esc_html__('Dados inválidos. Preencha produto, preço e quantidade.', 'yoper-core'));
    }

    $unit = yoper_core_get_product_meta($product_id, 'unit');
    $price_per_unit = $quantity > 0 ? $price_total / $quantity : 0;

    $post_id = wp_insert_post(array(
        'post_type'   => 'yoper_price_entry',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Preço pago - %s', 'yoper-core'), get_the_title($product_id)),
        'post_author' => get_current_user_id(),
    ));

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, 'yoper_price_product_id', $product_id);
        update_post_meta($post_id, 'yoper_price_supplier', $supplier);
        update_post_meta($post_id, 'yoper_price_total', $price_total);
        update_post_meta($post_id, 'yoper_price_quantity', $quantity);
        update_post_meta($post_id, 'yoper_price_unit', $unit);
        update_post_meta($post_id, 'yoper_price_per_unit', $price_per_unit);
        update_post_meta($post_id, 'yoper_price_purchased_at', $purchased_at);
        update_post_meta($post_id, 'yoper_price_notes', $notes);
    }

    $redirect = add_query_arg(array('page' => 'yoper-price-research', 'message' => 'saved'), admin_url('admin.php'));
    wp_safe_redirect($redirect);
    exit;
}

function yoper_core_render_price_reports_page() {
    if (!current_user_can('yoper_view_price_reports')) {
        wp_die(esc_html__('Você não tem permissão para acessar relatórios.', 'yoper-core'));
    }

    $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;

    $products = get_posts(array(
        'post_type'      => 'yoper_product',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));

    if (!$product_id && !empty($products)) {
        $product_id = $products[0]->ID;
    }

    $entries = array();
    if ($product_id) {
        $entries_query = new WP_Query(array(
            'post_type'      => 'yoper_price_entry',
            'posts_per_page' => 100,
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
            'meta_key'       => 'yoper_price_purchased_at',
            'meta_query'     => array(
                array(
                    'key'   => 'yoper_price_product_id',
                    'value' => $product_id,
                ),
            ),
        ));
        $entries = $entries_query->posts;
        wp_reset_postdata();
    }

    $recent_entries = array_slice($entries, 0, 10);

    $stats = yoper_core_price_stats($entries);

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Relatórios de Preço', 'yoper-core'); ?></h1>

        <form method="get" action="">
            <input type="hidden" name="page" value="yoper-price-reports" />
            <label for="yoper_report_product"><?php esc_html_e('Produto', 'yoper-core'); ?></label>
            <select name="product_id" id="yoper_report_product" onchange="this.form.submit()">
                <?php foreach ($products as $product) : ?>
                    <option value="<?php echo esc_attr($product->ID); ?>" <?php selected($product_id, $product->ID); ?>>
                        <?php echo esc_html(get_the_title($product)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($product_id && !empty($entries)) : ?>
            <h2><?php esc_html_e('Resumo', 'yoper-core'); ?></h2>
            <p>
                <?php
                $last_price = isset($stats['last_price']) ? $stats['last_price'] : 0;
                $avg30      = isset($stats['avg_30']) ? $stats['avg_30'] : 0;
                $avg60      = isset($stats['avg_60']) ? $stats['avg_60'] : 0;
                $avg90      = isset($stats['avg_90']) ? $stats['avg_90'] : 0;
                $indicator  = yoper_core_price_indicator($last_price, $avg30);
                ?>
                <strong><?php esc_html_e('Último preço pago:', 'yoper-core'); ?></strong>
                <?php echo esc_html(number_format((float) $last_price, 4, ',', '.')); ?><br />
                <strong><?php esc_html_e('Média 30 dias:', 'yoper-core'); ?></strong>
                <?php echo esc_html(number_format((float) $avg30, 4, ',', '.')); ?><br />
                <strong><?php esc_html_e('Média 60 dias:', 'yoper-core'); ?></strong>
                <?php echo esc_html(number_format((float) $avg60, 4, ',', '.')); ?><br />
                <strong><?php esc_html_e('Média 90 dias:', 'yoper-core'); ?></strong>
                <?php echo esc_html(number_format((float) $avg90, 4, ',', '.')); ?><br />
                <strong><?php esc_html_e('Indicador:', 'yoper-core'); ?></strong>
                <?php echo esc_html($indicator); ?><br />
                <?php if (isset($stats['min_price'])) : ?>
                    <strong><?php esc_html_e('Menor preço (90d):', 'yoper-core'); ?></strong>
                    <?php
                    echo esc_html(number_format((float) $stats['min_price'], 4, ',', '.'));
                    if (!empty($stats['min_supplier'])) {
                        echo ' (' . esc_html($stats['min_supplier']) . ')';
                    }
                    ?>
                <?php endif; ?>
            </p>

            <h2><?php esc_html_e('Últimos registros', 'yoper-core'); ?></h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Data', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Fornecedor', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Preço/Un', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Preço total', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Qtd', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Notas', 'yoper-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_entries as $entry) : ?>
                        <?php
                        $supplier     = get_post_meta($entry->ID, 'yoper_price_supplier', true);
                        $price_total  = get_post_meta($entry->ID, 'yoper_price_total', true);
                        $quantity     = get_post_meta($entry->ID, 'yoper_price_quantity', true);
                        $price_unit   = get_post_meta($entry->ID, 'yoper_price_per_unit', true);
                        $purchased_at = get_post_meta($entry->ID, 'yoper_price_purchased_at', true);
                        $notes        = get_post_meta($entry->ID, 'yoper_price_notes', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($purchased_at ?: get_the_date('', $entry)); ?></td>
                            <td><?php echo esc_html($supplier); ?></td>
                            <td><?php echo esc_html(number_format((float) $price_unit, 4, ',', '.')); ?></td>
                            <td><?php echo esc_html(number_format((float) $price_total, 2, ',', '.')); ?></td>
                            <td><?php echo esc_html($quantity); ?></td>
                            <td><?php echo esc_html($notes); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php esc_html_e('Nenhum registro encontrado para este produto.', 'yoper-core'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function yoper_core_price_stats($entries) {
    $now         = current_time('timestamp');
    $last_price  = null;
    $sum30 = 0;
    $sum60 = 0;
    $sum90 = 0;
    $c30  = 0;
    $c60  = 0;
    $c90  = 0;
    $min_price    = null;
    $min_supplier = '';

    foreach ($entries as $idx => $entry) {
        $price_unit  = (float) get_post_meta($entry->ID, 'yoper_price_per_unit', true);
        $purchased_at = get_post_meta($entry->ID, 'yoper_price_purchased_at', true);
        $entry_ts     = $purchased_at ? strtotime($purchased_at) : get_post_timestamp($entry);

        if (0 === $idx) {
            $last_price = $price_unit;
        }

        $diff_days = ($now - $entry_ts) / DAY_IN_SECONDS;

        if ($diff_days <= 30) {
            $sum30 += $price_unit;
            $c30++;
        }
        if ($diff_days <= 60) {
            $sum60 += $price_unit;
            $c60++;
        }
        if ($diff_days <= 90) {
            $sum90 += $price_unit;
            $c90++;
            if (null === $min_price || $price_unit < $min_price) {
                $min_price = $price_unit;
                $min_supplier = get_post_meta($entry->ID, 'yoper_price_supplier', true);
            }
        }
    }

    return array(
        'last_price'  => $last_price,
        'avg_30'      => $c30 ? $sum30 / $c30 : 0,
        'avg_60'      => $c60 ? $sum60 / $c60 : 0,
        'avg_90'      => $c90 ? $sum90 / $c90 : 0,
        'min_price'   => $min_price,
        'min_supplier'=> $min_supplier,
    );
}

function yoper_core_price_indicator($last_price, $avg_price) {
    if (!$avg_price) {
        return __('Sem base de comparação', 'yoper-core');
    }

    $tolerance = 0.05 * $avg_price;

    if ($last_price > $avg_price + $tolerance) {
        return __('Acima da média', 'yoper-core');
    }

    if ($last_price < $avg_price - $tolerance) {
        return __('Abaixo da média', 'yoper-core');
    }

    return __('Na média', 'yoper-core');
}
