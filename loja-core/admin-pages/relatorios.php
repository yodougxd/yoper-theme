<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_render_relatorios() {
    if (!current_user_can('loja_view_reports')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }

    $produtos = get_posts(array(
        'post_type'      => 'loja_produto',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ));

    $produto_id = isset($_GET['produto_id']) ? absint($_GET['produto_id']) : 0;
    $precos = array();
    if ($produto_id) {
        $precos = get_posts(array(
            'post_type'      => 'loja_preco',
            'posts_per_page' => 30,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_loja_produto_id',
                    'value' => $produto_id,
                ),
            ),
        ));
    }

    ?>
    <div class="wrap loja-container">
        <h1 class="title"><?php esc_html_e('Relatórios', 'loja-core'); ?></h1>
        <form method="get" class="loja-actions">
            <input type="hidden" name="page" value="loja-relatorios" />
            <div class="select">
                <select name="produto_id" onchange="this.form.submit()">
                    <option value=""><?php esc_html_e('Selecione um produto', 'loja-core'); ?></option>
                    <?php foreach ($produtos as $p) : ?>
                        <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($produto_id, $p->ID); ?>><?php echo esc_html($p->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($produto_id && $precos) : ?>
            <table class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Data', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Fornecedor', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Preço/Un', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Qtd', 'loja-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($precos as $pr) : ?>
                        <?php
                        $forn = get_post_meta($pr->ID, '_loja_fornecedor', true);
                        $ppu  = get_post_meta($pr->ID, '_loja_preco_unit', true);
                        $qty  = get_post_meta($pr->ID, '_loja_quantidade', true);
                        $data = get_post_meta($pr->ID, '_loja_data', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($data ?: get_the_date('', $pr)); ?></td>
                            <td><?php echo esc_html($forn); ?></td>
                            <td><?php echo esc_html(number_format((float) $ppu, 4, ',', '.')); ?></td>
                            <td><?php echo esc_html($qty); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php esc_html_e('Selecione um produto para ver o histórico.', 'loja-core'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}
