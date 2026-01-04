<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
// Business data with safe fallbacks.
$business_settings = function_exists('yoper_theme_get_business_settings') ? yoper_theme_get_business_settings() : array();
$business_name     = !empty($business_settings['business_name']) ? $business_settings['business_name'] : get_bloginfo('name');
$business_slogan   = isset($business_settings['slogan']) ? $business_settings['slogan'] : '';
$business_address  = isset($business_settings['address']) ? $business_settings['address'] : '';
$business_city     = isset($business_settings['city']) ? $business_settings['city'] : '';
$business_hours    = isset($business_settings['hours']) ? $business_settings['hours'] : '';
$whatsapp_link     = function_exists('yoper_theme_get_whatsapp_link') ? yoper_theme_get_whatsapp_link(__('Olá, gostaria de saber mais.', 'yoper-theme')) : '';
?>

<main id="primary" class="site-main">
    <section class="section section-hero">
        <div class="section-inner">
            <p class="eyebrow"><?php esc_html_e('Bem-vindo(a)', 'yoper-theme'); ?></p>
            <h1 class="business-name"><?php echo esc_html($business_name); ?></h1>
            <?php if (!empty($business_slogan)) : ?>
                <p class="business-slogan"><?php echo esc_html($business_slogan); ?></p>
            <?php endif; ?>
            <?php if (!empty($whatsapp_link)) : ?>
                <a class="button button-primary" href="<?php echo esc_url($whatsapp_link); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Fale no WhatsApp', 'yoper-theme'); ?>
                </a>
            <?php else : ?>
                <p class="note"><?php esc_html_e('WhatsApp ainda não configurado.', 'yoper-theme'); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section-info">
        <div class="section-inner">
            <h2><?php esc_html_e('Informações', 'yoper-theme'); ?></h2>
            <?php if ($business_address || $business_city || $business_hours) : ?>
                <ul class="business-info">
                    <?php if ($business_address || $business_city) : ?>
                        <li>
                            <strong><?php esc_html_e('Endereço', 'yoper-theme'); ?>:</strong>
                            <?php echo esc_html(trim($business_address . ' ' . $business_city)); ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($business_hours) : ?>
                        <li>
                            <strong><?php esc_html_e('Horário de funcionamento', 'yoper-theme'); ?>:</strong>
                            <?php echo nl2br(esc_html($business_hours)); ?>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php else : ?>
                <p><?php esc_html_e('Adicione endereço e horário nas Configurações do Negócio.', 'yoper-theme'); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section-promos">
        <div class="section-inner">
            <h2><?php esc_html_e('Promoções em destaque', 'yoper-theme'); ?></h2>
            <?php
            $promo_query = new WP_Query(array(
                'post_type'      => 'yoper_promo',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
            ));
            ?>
            <?php if ($promo_query->have_posts()) : ?>
                <div class="cards cards-promos">
                    <?php while ($promo_query->have_posts()) : $promo_query->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('card card-promo'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="card-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="card-excerpt"><?php the_excerpt(); ?></div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Nenhuma promoção cadastrada ainda.', 'yoper-theme'); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section-catalog">
        <div class="section-inner">
            <h2><?php esc_html_e('Catálogo', 'yoper-theme'); ?></h2>
            <?php
            $catalog_query = new WP_Query(array(
                'post_type'      => 'yoper_item',
                'posts_per_page' => 8,
                'post_status'    => 'publish',
            ));
            ?>
            <?php if ($catalog_query->have_posts()) : ?>
                <div class="cards cards-catalog">
                    <?php while ($catalog_query->have_posts()) : $catalog_query->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('card card-item'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="card-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="card-excerpt"><?php the_excerpt(); ?></div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Nenhum item no catálogo ainda.', 'yoper-theme'); ?></p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>
