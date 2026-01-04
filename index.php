<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main">
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
            </header>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
    <?php the_posts_navigation(); ?>
<?php else : ?>
    <section class="no-results not-found">
        <h2><?php esc_html_e('Nothing found', 'yoper-theme'); ?></h2>
        <p><?php esc_html_e('Content will appear here soon.', 'yoper-theme'); ?></p>
    </section>
<?php endif; ?>
</main>
<?php get_footer(); ?>
