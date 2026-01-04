<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_yoper_save_stock_count', 'yoper_core_handle_stock_count_save');

function yoper_core_get_active_products_for_count($search = '', $category = '') {
    $meta_query = array(
        'relation' => 'AND',
        array(
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
    );

    $tax_query = array();
    if (!empty($category)) {
        $tax_query[] = array(
            'taxonomy' => 'yoper_product_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($category),
        );
    }

    $args = array(
        'post_type'      => 'yoper_product',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'meta_query'     => $meta_query,
        'tax_query'      => $tax_query,
        'orderby'        => 'title',
        'order'          => 'ASC',
        's'              => $search ? sanitize_text_field($search) : '',
        'no_found_rows'            => true,
        'update_post_meta_cache'   => true,
        'update_post_term_cache'   => false,
    );

    // Also search by SKU if provided (alongside title search).
    if ($search) {
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     => 'yoper_product_sku',
                'value'   => sanitize_text_field($search),
                'compare' => 'LIKE',
            ),
            array(
                'key'     => '_yoper_product_sku',
                'value'   => sanitize_text_field($search),
                'compare' => 'LIKE',
            ),
        );
    }

    $query = new WP_Query($args);
    $posts = $query->posts;
    wp_reset_postdata();

    return $posts;
}

function yoper_core_render_stock_count_page() {
    if (!current_user_can('yoper_do_stock_count')) {
        wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'yoper-core'));
    }

    $search   = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $category = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : '';
    $products = yoper_core_get_active_products_for_count($search, $category);

    $message = isset($_GET['message']) ? sanitize_text_field(wp_unslash($_GET['message'])) : '';
    $saved_post_id = isset($_GET['count_id']) ? absint($_GET['count_id']) : 0;

    $summary = array();
    if ($message === 'saved' && $saved_post_id) {
        $summary = get_post_meta($saved_post_id, '_yoper_stock_count_summary', true);
        if (!is_array($summary)) {
            $summary = array();
        }
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Fechamento (Contagem de Estoque)', 'yoper-core'); ?></h1>

        <?php if ($message === 'saved') : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Contagem salva com sucesso.', 'yoper-core'); ?></p>
                <?php if (!empty($summary)) : ?>
                    <p>
                        <?php
                        printf(
                            esc_html__('Itens contados: %1$d | Abaixo do mínimo: %2$d', 'yoper-core'),
                            isset($summary['total_items']) ? (int) $summary['total_items'] : 0,
                            isset($summary['below_min']) ? (int) $summary['below_min'] : 0
                        );
                        ?>
                    </p>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('yoper_generate_purchase_list'); ?>
                        <input type="hidden" name="action" value="yoper_generate_purchase_list" />
                        <input type="hidden" name="count_id" value="<?php echo esc_attr($saved_post_id); ?>" />
                        <button class="button button-primary" type="submit">
                            <?php esc_html_e('Gerar Lista de Compras', 'yoper-core'); ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="get" action="">
            <input type="hidden" name="page" value="yoper-stock-count" />
            <div class="tablenav top yoper-sticky-actions">
                <div class="alignleft actions">
                    <label class="screen-reader-text" for="search-products"><?php esc_html_e('Buscar produtos', 'yoper-core'); ?></label>
                    <input type="search" id="search-products" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Buscar por nome ou SKU', 'yoper-core'); ?>" />

                    <?php
                    wp_dropdown_categories(array(
                        'show_option_all' => __('Todas as categorias', 'yoper-core'),
                        'taxonomy'        => 'yoper_product_category',
                        'name'            => 'category',
                        'orderby'         => 'name',
                        'selected'        => $category,
                        'hierarchical'    => true,
                        'hide_empty'      => false,
                    ));
                    ?>
                    <button class="button"><?php esc_html_e('Filtrar', 'yoper-core'); ?></button>
                </div>
            </div>
        </form>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('yoper_stock_count', 'yoper_stock_count_nonce'); ?>
            <input type="hidden" name="action" value="yoper_save_stock_count" />

            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Produto', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('SKU', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Un.', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Mínimo', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Estoque atual', 'yoper-core'); ?></th>
                        <th><?php esc_html_e('Contado agora', 'yoper-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)) : ?>
                        <?php foreach ($products as $product) : ?>
                            <?php
                            $sku           = yoper_core_get_product_meta($product->ID, 'sku');
                            $unit          = yoper_core_get_product_meta($product->ID, 'unit');
                            $stock_min     = yoper_core_get_product_meta($product->ID, 'stock_min');
                            $stock_current = yoper_core_get_product_meta($product->ID, 'stock_current');
                            $row_low       = (is_numeric($stock_min) && is_numeric($stock_current) && (float) $stock_current <= (float) $stock_min);
                            ?>
                            <tr class="<?php echo $row_low ? 'yoper-row-low' : ''; ?>">
                                <td><?php echo esc_html(get_the_title($product)); ?></td>
                                <td><?php echo esc_html($sku); ?></td>
                                <td><?php echo esc_html($unit); ?></td>
                                <td><?php echo esc_html($stock_min); ?></td>
                                <td>
                                    <?php echo esc_html($stock_current); ?>
                                    <?php if ($row_low) : ?>
                                        <span class="yoper-stock-low"><?php esc_html_e('BAIXO', 'yoper-core'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="count[<?php echo esc_attr($product->ID); ?>]" value="" class="widefat" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6"><?php esc_html_e('Nenhum produto ativo encontrado.', 'yoper-core'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <p class="yoper-sticky-actions">
                <button type="submit" class="button button-primary"><?php esc_html_e('Salvar contagem', 'yoper-core'); ?></button>
            </p>
        </form>
    </div>
    <?php
}

function yoper_core_handle_stock_count_save() {
    if (!current_user_can('yoper_do_stock_count')) {
        wp_die(esc_html__('Você não tem permissão para salvar contagens.', 'yoper-core'));
    }

    if (!isset($_POST['yoper_stock_count_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yoper_stock_count_nonce'])), 'yoper_stock_count')) {
        wp_die(esc_html__('Falha de verificação de segurança.', 'yoper-core'));
    }

    $counts = isset($_POST['count']) && is_array($_POST['count']) ? $_POST['count'] : array();

    $items       = array();
    $below_min   = 0;
    $total_items = 0;

    foreach ($counts as $product_id => $qty) {
        $product_id = absint($product_id);
        $qty_val    = floatval(wp_unslash($qty));

        if ($product_id <= 0) {
            continue;
        }

        $stock_prev = yoper_core_get_product_meta($product_id, 'stock_current');
        $stock_min  = yoper_core_get_product_meta($product_id, 'stock_min');

        $items[] = array(
            'product_id'   => $product_id,
            'qty_counted'  => $qty_val,
            'stock_before' => $stock_prev,
            'stock_min'    => $stock_min,
        );

        $total_items++;

        $stock_min_val = is_numeric($stock_min) ? (float) $stock_min : 0;
        if ($stock_min_val > 0 && $qty_val <= $stock_min_val) {
            $below_min++;
        }
    }

    $now      = current_time('mysql');
    $user_id  = get_current_user_id();
    $post_id  = wp_insert_post(array(
        'post_type'   => 'yoper_stock_count',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Contagem em %s', 'yoper-core'), $now),
        'post_author' => $user_id,
    ));

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_yoper_stock_count_items', wp_json_encode($items));
        update_post_meta($post_id, '_yoper_stock_count_user', $user_id);
        update_post_meta($post_id, '_yoper_stock_count_time', $now);
        update_post_meta($post_id, '_yoper_stock_count_summary', array(
            'total_items' => $total_items,
            'below_min'   => $below_min,
        ));
    }

    $redirect = add_query_arg(
        array(
            'page'     => 'yoper-stock-count',
            'message'  => 'saved',
            'count_id' => $post_id,
        ),
        admin_url('admin.php')
    );

    wp_safe_redirect($redirect);
    exit;
}
