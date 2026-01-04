<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main">
<?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <p class="entry-meta">
                <?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(get_the_author()); ?>
            </p>
        </header>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </article>
    <?php if (comments_open() || get_comments_number()) {
        comments_template();
    } ?>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
