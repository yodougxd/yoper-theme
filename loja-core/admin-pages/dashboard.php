<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_render_dashboard() {
    if (!current_user_can('loja_view_dashboard')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }

    $ultima_contagem = get_posts(array(
        'post_type'      => 'loja_contagem',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    $ultima = '';
    if ($ultima_contagem) {
        $ultima = sprintf(
            __('Última contagem: %s por %s', 'loja-core'),
            get_the_date('', $ultima_contagem[0]),
            get_the_author_meta('display_name', $ultima_contagem[0]->post_author)
        );
    }
    ?>
    <div class="wrap loja-container">
        <h1 class="title"><?php esc_html_e('Dashboard da Loja', 'loja-core'); ?></h1>
        <?php if ($ultima) : ?>
            <p class="subtitle"><?php echo esc_html($ultima); ?></p>
        <?php endif; ?>

        <div class="columns">
            <div class="column">
                <div class="box">
                    <p class="heading"><?php esc_html_e('Ações rápidas', 'loja-core'); ?></p>
                    <div class="buttons">
                        <a class="button is-primary" href="<?php echo esc_url(admin_url('admin.php?page=loja-contagens')); ?>"><?php esc_html_e('Nova Contagem de Estoque', 'loja-core'); ?></a>
                        <a class="button is-link" href="<?php echo esc_url(admin_url('admin.php?page=loja-modo-compras')); ?>"><?php esc_html_e('Modo Compras', 'loja-core'); ?></a>
                        <a class="button is-info" href="<?php echo esc_url(admin_url('admin.php?page=loja-precos')); ?>"><?php esc_html_e('Registrar Preço', 'loja-core'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
