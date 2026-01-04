<?php
if (!defined('ABSPATH')) {
    exit;
}

$business_settings = function_exists('yoper_theme_get_business_settings') ? yoper_theme_get_business_settings() : array();
$business_name     = !empty($business_settings['business_name']) ? $business_settings['business_name'] : get_bloginfo('name');
$instagram_link    = isset($business_settings['instagram']) ? $business_settings['instagram'] : '';
$delivery_link     = isset($business_settings['delivery']) ? $business_settings['delivery'] : '';
?>
<footer class="site-footer">
    <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($business_name); ?></p>
    <?php if ($instagram_link || $delivery_link) : ?>
        <div class="footer-links">
            <?php if ($instagram_link) : ?>
                <a href="<?php echo esc_url($instagram_link); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
            <?php endif; ?>
            <?php if ($delivery_link) : ?>
                <a href="<?php echo esc_url($delivery_link); ?>" target="_blank" rel="noopener noreferrer">Delivery</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
