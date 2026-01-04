<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$option_keys = array(
    'yoper_core_version',
    'yoper_core_business_name',
    'yoper_core_network_business_name',
);

if (is_multisite()) {
    $site_ids = get_sites(array('fields' => 'ids'));

    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        foreach ($option_keys as $option_key) {
            delete_option($option_key);
        }
        restore_current_blog();
    }

    delete_site_option('yoper_core_network_business_name');
} else {
    foreach ($option_keys as $option_key) {
        delete_option($option_key);
    }
}
