<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main">
    <header class="page-header">
        <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
        <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="archive-list archive-items">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('archive-card'); ?>>
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                    <a class="read-more" href="<?php the_permalink(); ?>"><?php esc_html_e('Ver item', 'yoper-theme'); ?></a>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => __('Anterior', 'yoper-theme'),
            'next_text' => __('Próximo', 'yoper-theme'),
        )); ?>
    <?php else : ?>
        <p><?php esc_html_e('Nenhum item encontrado.', 'yoper-theme'); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
