<?php
if (!defined('ABSPATH')) {
    exit;
}

function loja_core_caps() {
    return array(
        'loja_view_dashboard',
        'loja_manage_products',
        'loja_manage_stock_counts',
        'loja_manage_purchases',
        'loja_manage_price_history',
        'loja_view_reports',
        'loja_manage_settings',
    );
}

function loja_core_setup_roles() {
    $caps = loja_core_caps();

    $admin = get_role('administrator');
    if ($admin) {
        foreach ($caps as $cap) {
            $admin->add_cap($cap);
        }
    }

    $employee_caps = array(
        'loja_view_dashboard',
        'loja_manage_products',
        'loja_manage_stock_counts',
        'loja_manage_purchases',
        'loja_manage_price_history',
        'loja_view_reports',
    );

    $employee = get_role('loja_employee');
    if (!$employee) {
        $employee = add_role('loja_employee', __('Funcionário', 'loja-core'), array());
    }

    foreach ($employee_caps as $cap) {
        $employee->add_cap($cap);
    }
}

add_action('init', 'loja_core_setup_roles');
