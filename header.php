<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$business_settings = function_exists('yoper_theme_get_business_settings') ? yoper_theme_get_business_settings() : array();
$business_name     = !empty($business_settings['business_name']) ? $business_settings['business_name'] : get_bloginfo('name');
$business_slogan   = !empty($business_settings['slogan']) ? $business_settings['slogan'] : get_bloginfo('description');
?>
<header class="site-header">
    <div class="site-branding">
        <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($business_name); ?></a>
        <?php if (!empty($business_slogan)) : ?>
            <p class="site-description"><?php echo esc_html($business_slogan); ?></p>
        <?php endif; ?>
    </div>
    <?php if (has_nav_menu('principal')) : ?>
        <nav class="primary-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'principal',
                'container'      => false,
                'menu_class'     => 'menu menu-principal',
            ));
            ?>
        </nav>
    <?php endif; ?>
</header>
