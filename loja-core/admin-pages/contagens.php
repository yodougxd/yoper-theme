<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_render_contagens() {
    if (!current_user_can('loja_manage_stock_counts')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }

    $produtos = get_posts(array(
        'post_type'      => 'loja_produto',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'   => '_loja_ativo',
                'value' => '1',
            ),
        ),
        'no_found_rows' => true,
    ));

    $contagens = get_posts(array(
        'post_type'      => 'loja_contagem',
        'posts_per_page' => 10,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    ?>
    <div class="wrap loja-container">
        <h1 class="title"><?php esc_html_e('Contagem de Estoque', 'loja-core'); ?></h1>

        <div class="box loja-card">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('loja_contagem', '_loja_contagem_nonce'); ?>
                <input type="hidden" name="action" value="loja_salvar_contagem" />
                <table class="table is-fullwidth is-striped loja-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Produto', 'loja-core'); ?></th>
                            <th><?php esc_html_e('Estoque mínimo', 'loja-core'); ?></th>
                            <th><?php esc_html_e('Qtd atual', 'loja-core'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($produtos) : ?>
                            <?php foreach ($produtos as $produto) : ?>
                                <?php $min = get_post_meta($produto->ID, '_loja_estoque_min', true); ?>
                                <tr>
                                    <td><?php echo esc_html($produto->post_title); ?></td>
                                    <td><?php echo esc_html($min); ?></td>
                                    <td><input class="input" type="number" step="0.01" name="itens[<?php echo esc_attr($produto->ID); ?>]" /></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="3"><?php esc_html_e('Nenhum produto ativo.', 'loja-core'); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="loja-actions">
                    <button type="submit" class="button is-primary"><?php esc_html_e('Salvar contagem', 'loja-core'); ?></button>
                    <span class="has-text-grey"><?php esc_html_e('Campos vazios serão ignorados.', 'loja-core'); ?></span>
                </div>
            </form>
        </div>

        <h2 class="title is-4"><?php esc_html_e('Contagens recentes', 'loja-core'); ?></h2>
        <div class="box loja-card">
            <table class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Data', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Autor', 'loja-core'); ?></th>
                        <th><?php esc_html_e('Status', 'loja-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($contagens) : ?>
                        <?php foreach ($contagens as $c) : ?>
                            <tr>
                                <td><?php echo esc_html(get_the_date('', $c)); ?></td>
                                <td><?php echo esc_html(get_the_author_meta('display_name', $c->post_author)); ?></td>
                                <td><?php echo esc_html(get_post_meta($c->ID, '_loja_status', true) ?: 'enviado'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3"><?php esc_html_e('Nenhuma contagem encontrada.', 'loja-core'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
