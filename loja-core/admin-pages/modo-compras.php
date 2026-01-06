<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_render_modo_compras() {
    if (!current_user_can('loja_manage_purchases')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }

    $contagens = get_posts(array(
        'post_type'      => 'loja_contagem',
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    ?>
    <div class="wrap loja-container">
        <h1 class="title"><?php esc_html_e('Modo Compras', 'loja-core'); ?></h1>
        <p class="subtitle"><?php esc_html_e('Selecione itens para comprar e registre preços pagos.', 'loja-core'); ?></p>

        <div class="box loja-card">
            <h2 class="title is-5"><?php esc_html_e('Sessão de compras rápida', 'loja-core'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('loja_compra', '_loja_compra_nonce'); ?>
                <input type="hidden" name="action" value="loja_salvar_compra" />
                <table class="table is-fullwidth is-striped loja-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Produto', 'loja-core'); ?></th>
                            <th><?php esc_html_e('Qtd', 'loja-core'); ?></th>
                            <th><?php esc_html_e('Preço pago', 'loja-core'); ?></th>
                            <th><?php esc_html_e('Obs', 'loja-core'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                wp_dropdown_pages(array(
                                    'post_type'        => 'loja_produto',
                                    'name'             => 'compra[0][produto_id]',
                                    'show_option_none' => __('Selecione um produto', 'loja-core'),
                                    'option_none_value'=> '',
                                    'echo'             => 1,
                                ));
                                ?>
                            </td>
                            <td><input class="input" type="number" step="0.01" name="compra[0][qty]" /></td>
                            <td><input class="input" type="number" step="0.01" name="compra[0][preco]" /></td>
                            <td><input class="input" type="text" name="compra[0][obs]" /></td>
                        </tr>
                    </tbody>
                </table>
                <div class="loja-actions">
                    <button type="submit" class="button is-primary"><?php esc_html_e('Salvar compras', 'loja-core'); ?></button>
                </div>
            </form>
        </div>

        <div class="box loja-card">
            <h2 class="title is-5"><?php esc_html_e('Contagens recentes para gerar lista', 'loja-core'); ?></h2>
            <table class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Data', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Autor', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Ação', 'loja-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($contagens) : ?>
                        <?php foreach ($contagens as $c) : ?>
                            <tr>
                                <td><?php echo esc_html(get_the_date('', $c)); ?></td>
                                <td><?php echo esc_html(get_the_author_meta('display_name', $c->post_author)); ?></td>
                                <td><a class="button is-small" href="<?php echo esc_url(get_edit_post_link($c->ID)); ?>"><?php esc_html_e('Ver contagem', 'loja-core'); ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3"><?php esc_html_e('Nenhuma contagem.', 'loja-core'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
