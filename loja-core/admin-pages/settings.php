<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_render_settings() {
    if (!current_user_can('loja_manage_settings')) {
        wp_die(__('Sem permissão.', 'loja-core'));
    }

    ?>
    <div class="wrap loja-container">
        <h1 class="title"><?php esc_html_e('Configurações da Loja', 'loja-core'); ?></h1>
        <div class="box loja-card">
            <p><?php esc_html_e('Configurações adicionais podem ser adicionadas aqui.', 'loja-core'); ?></p>
        </div>
    </div>
    <?php
}
