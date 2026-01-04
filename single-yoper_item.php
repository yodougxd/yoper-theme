<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-entry single-item'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <?php
                    if (taxonomy_exists('yoper_item_category')) {
                        $terms_list = get_the_term_list(get_the_ID(), 'yoper_item_category', '<span class="term-list">', ', ', '</span>');

                        if ($terms_list) {
                            echo wp_kses_post($terms_list);
                        }
                    }
                    ?>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php esc_html_e('Item não encontrado.', 'yoper-theme'); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
