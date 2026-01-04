<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_core_get_sites($args = array()) {
    if (!is_multisite()) {
        return array();
    }

    $defaults = array(
        'fields' => 'ids',
    );

    return get_sites(wp_parse_args($args, $defaults));
}

function yoper_core_with_site($site_id, callable $callback) {
    $site_id = (int) $site_id;

    if (!is_multisite()) {
        return $callback(get_current_blog_id());
    }

    if ($site_id === get_current_blog_id()) {
        return $callback($site_id);
    }

    switch_to_blog($site_id);

    try {
        return $callback($site_id);
    } finally {
        restore_current_blog();
    }
}

function yoper_core_for_each_site(callable $callback) {
    if (!is_multisite()) {
        return $callback(get_current_blog_id());
    }

    $site_ids = yoper_core_get_sites();

    foreach ($site_ids as $site_id) {
        yoper_core_with_site($site_id, $callback);
    }
}

function yoper_core_get_current_site_info() {
    $blog_id = get_current_blog_id();

    return array(
        'blog_id' => $blog_id,
        'url'     => get_site_url($blog_id, '/'),
    );
}

function yoper_core_on_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    yoper_core_with_site($blog_id, function ($site_id) {
        yoper_core_activate_site($site_id);

        $network_name = yoper_core_get_option('yoper_core_network_business_name', '', true);
        if (!empty($network_name)) {
            update_option('yoper_core_business_name', $network_name);
        }
    });
}
