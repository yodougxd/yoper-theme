<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_render_precos() {
    if (!current_user_can('loja_manage_price_history')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }

    $produtos = get_posts(array(
        'post_type'      => 'loja_produto',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ));

    $produto_filtro = isset($_GET['produto_id']) ? absint($_GET['produto_id']) : 0;
    $precos = get_posts(array(
        'post_type'      => 'loja_preco',
        'posts_per_page' => 20,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => $produto_filtro ? array(
            array(
                'key'   => '_loja_produto_id',
                'value' => $produto_filtro,
            ),
        ) : array(),
    ));
    ?>
    <div class="wrap loja-container">
        <h1 class="title"><?php esc_html_e('Preços', 'loja-core'); ?></h1>
        <div class="box loja-card">
            <h2 class="title is-5"><?php esc_html_e('Registrar preço', 'loja-core'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="loja-inputs">
                <?php wp_nonce_field('loja_preco', '_loja_preco_nonce'); ?>
                <input type="hidden" name="action" value="loja_salvar_preco" />
                <div class="field">
                    <label class="label"><?php esc_html_e('Produto', 'loja-core'); ?></label>
                    <div class="control">
                        <div class="select">
                            <select name="produto_id" required>
                                <option value=""><?php esc_html_e('Selecione', 'loja-core'); ?></option>
                                <?php foreach ($produtos as $p) : ?>
                                    <option value="<?php echo esc_attr($p->ID); ?>"><?php echo esc_html($p->post_title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label"><?php esc_html_e('Fornecedor', 'loja-core'); ?></label>
                    <div class="control"><input class="input" type="text" name="fornecedor" /></div>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <label class="label"><?php esc_html_e('Preço total', 'loja-core'); ?></label>
                        <input class="input" type="number" step="0.01" min="0" name="preco_total" required />
                    </div>
                    <div class="control">
                        <label class="label"><?php esc_html_e('Quantidade', 'loja-core'); ?></label>
                        <input class="input" type="number" step="0.01" min="0.0001" name="quantidade" required />
                    </div>
                    <div class="control">
                        <label class="label"><?php esc_html_e('Data', 'loja-core'); ?></label>
                        <input class="input" type="date" name="data" value="<?php echo esc_attr(date('Y-m-d')); ?>" />
                    </div>
                </div>
                <div class="field">
                    <label class="label"><?php esc_html_e('Observação', 'loja-core'); ?></label>
                    <div class="control"><textarea class="textarea" name="obs"></textarea></div>
                </div>
                <div class="loja-actions">
                    <button class="button is-primary" type="submit"><?php esc_html_e('Salvar', 'loja-core'); ?></button>
                </div>
            </form>
        </div>

        <div class="box loja-card">
            <h2 class="title is-5"><?php esc_html_e('Histórico', 'loja-core'); ?></h2>
            <form method="get" class="loja-actions">
                <input type="hidden" name="page" value="loja-precos" />
                <div class="select">
                    <select name="produto_id" onchange="this.form.submit()">
                        <option value=""><?php esc_html_e('Todos os produtos', 'loja-core'); ?></option>
                        <?php foreach ($produtos as $p) : ?>
                            <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($produto_filtro, $p->ID); ?>><?php echo esc_html($p->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <table class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Produto', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Fornecedor', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Preço/Un', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Qtd', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Data', 'loja-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($precos) : ?>
                        <?php foreach ($precos as $pr) : ?>
                            <?php
                            $pid   = get_post_meta($pr->ID, '_loja_produto_id', true);
                            $forn  = get_post_meta($pr->ID, '_loja_fornecedor', true);
                            $ppu   = get_post_meta($pr->ID, '_loja_preco_unit', true);
                            $qty   = get_post_meta($pr->ID, '_loja_quantidade', true);
                            $data  = get_post_meta($pr->ID, '_loja_data', true);
                            ?>
                            <tr>
                                <td><?php echo esc_html($pid ? get_the_title($pid) : ''); ?></td>
                                <td><?php echo esc_html($forn); ?></td>
                                <td><?php echo esc_html(number_format((float) $ppu, 4, ',', '.')); ?></td>
                                <td><?php echo esc_html($qty); ?></td>
                                <td><?php echo esc_html($data ?: get_the_date('', $pr)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5"><?php esc_html_e('Sem registros.', 'loja-core'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
