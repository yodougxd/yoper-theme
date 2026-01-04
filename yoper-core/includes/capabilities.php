<?php
if (!defined('ABSPATH')) {
    exit;
}

// Returns the list of custom capabilities used by Yoper Core.
function yoper_core_get_custom_capabilities() {
    return array(
        'yoper_manage_products',
        'yoper_do_stock_count',
        'yoper_view_purchase_lists',
        'yoper_manage_purchase_lists',
        'yoper_add_price_entries',
        'yoper_view_price_reports',
        // yoper_item post type caps
        'edit_yoper_item',
        'read_yoper_item',
        'delete_yoper_item',
        'edit_yoper_items',
        'edit_others_yoper_items',
        'publish_yoper_items',
        'read_private_yoper_items',
        'delete_yoper_items',
        'delete_private_yoper_items',
        'delete_published_yoper_items',
        'delete_others_yoper_items',
        'edit_private_yoper_items',
        'edit_published_yoper_items',
    );
}

// Adds capabilities to a role helper.
function yoper_core_grant_caps_to_role($role, $caps) {
    if (!$role) {
        return;
    }

    foreach ($caps as $cap) {
        $role->add_cap($cap);
    }
}

// Creates/updates roles with needed capabilities.
function yoper_core_setup_caps() {
    $custom_caps = yoper_core_get_custom_capabilities();

    $admin = get_role('administrator');
    yoper_core_grant_caps_to_role($admin, $custom_caps);

    $employee_caps = array(
        'yoper_do_stock_count',
        'yoper_view_purchase_lists',
        'yoper_add_price_entries',
    );

    $employee = get_role('yoper_employee');
    if (!$employee) {
        $employee = add_role('yoper_employee', __('Funcionário', 'yoper-core'), array());
    }

    yoper_core_grant_caps_to_role($employee, $employee_caps);
}
