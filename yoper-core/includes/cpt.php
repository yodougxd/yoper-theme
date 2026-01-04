<?php
if (!defined('ABSPATH')) {
    exit;
}

function yoper_core_register_cpts() {
    $labels = array(
        'name'               => __('Items', 'yoper-core'),
        'singular_name'      => __('Item', 'yoper-core'),
        'add_new'            => __('Add New', 'yoper-core'),
        'add_new_item'       => __('Add New Item', 'yoper-core'),
        'edit_item'          => __('Edit Item', 'yoper-core'),
        'new_item'           => __('New Item', 'yoper-core'),
        'view_item'          => __('View Item', 'yoper-core'),
        'search_items'       => __('Search Items', 'yoper-core'),
        'not_found'          => __('No items found', 'yoper-core'),
        'not_found_in_trash' => __('No items found in Trash', 'yoper-core'),
        'menu_name'          => __('Items', 'yoper-core'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'items'),
        'capability_type'    => array('yoper_item', 'yoper_items'),
        'map_meta_cap'       => true,
    );

    register_post_type('yoper_item', $args);

    yoper_core_register_operation_cpts();
    yoper_core_register_product_taxonomies();
}

// Builds a capabilities map using a single manage cap and an optional separate read cap.
function yoper_core_build_caps($manage_cap, $read_cap = null) {
    $read_cap = $read_cap ? $read_cap : $manage_cap;

    return array(
        'edit_post'              => $manage_cap,
        'read_post'              => $read_cap,
        'delete_post'            => $manage_cap,
        'edit_posts'             => $manage_cap,
        'edit_others_posts'      => $manage_cap,
        'publish_posts'          => $manage_cap,
        'read_private_posts'     => $read_cap,
        'delete_posts'           => $manage_cap,
        'delete_private_posts'   => $manage_cap,
        'delete_published_posts' => $manage_cap,
        'delete_others_posts'    => $manage_cap,
        'edit_private_posts'     => $manage_cap,
        'edit_published_posts'   => $manage_cap,
    );
}

function yoper_core_register_operation_cpts() {
    $base_args = array(
        'public'             => false,
        'publicly_queryable' => false,
        'exclude_from_search'=> true,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'show_in_nav_menus'  => false,
        'has_archive'        => false,
        'rewrite'            => false,
        'hierarchical'       => false,
        'supports'           => array('title'),
        'map_meta_cap'       => true,
    );

    $cpts = array(
        'yoper_product' => array(
            'labels' => array(
                'name'               => __('Produtos', 'yoper-core'),
                'singular_name'      => __('Produto', 'yoper-core'),
                'add_new'            => __('Adicionar novo', 'yoper-core'),
                'add_new_item'       => __('Adicionar novo produto', 'yoper-core'),
                'edit_item'          => __('Editar produto', 'yoper-core'),
                'new_item'           => __('Novo produto', 'yoper-core'),
                'view_item'          => __('Ver produto', 'yoper-core'),
                'search_items'       => __('Buscar produtos', 'yoper-core'),
                'not_found'          => __('Nenhum produto encontrado', 'yoper-core'),
                'not_found_in_trash' => __('Nenhum produto na lixeira', 'yoper-core'),
                'menu_name'          => __('Produtos', 'yoper-core'),
            ),
            'capabilities' => yoper_core_build_caps('yoper_manage_products'),
            'capability_type' => array('yoper_product', 'yoper_products'),
        ),
        'yoper_stock_count' => array(
            'labels' => array(
                'name'               => __('Contagens de estoque', 'yoper-core'),
                'singular_name'      => __('Contagem de estoque', 'yoper-core'),
                'add_new'            => __('Adicionar nova', 'yoper-core'),
                'add_new_item'       => __('Adicionar nova contagem', 'yoper-core'),
                'edit_item'          => __('Editar contagem', 'yoper-core'),
                'new_item'           => __('Nova contagem', 'yoper-core'),
                'view_item'          => __('Ver contagem', 'yoper-core'),
                'search_items'       => __('Buscar contagens', 'yoper-core'),
                'not_found'          => __('Nenhuma contagem encontrada', 'yoper-core'),
                'not_found_in_trash' => __('Nenhuma contagem na lixeira', 'yoper-core'),
                'menu_name'          => __('Contagens de estoque', 'yoper-core'),
            ),
            'capabilities' => yoper_core_build_caps('yoper_do_stock_count'),
            'capability_type' => array('yoper_stock_count', 'yoper_stock_counts'),
        ),
        'yoper_purchase_list' => array(
            'labels' => array(
                'name'               => __('Listas de compras', 'yoper-core'),
                'singular_name'      => __('Lista de compras', 'yoper-core'),
                'add_new'            => __('Adicionar nova', 'yoper-core'),
                'add_new_item'       => __('Adicionar nova lista', 'yoper-core'),
                'edit_item'          => __('Editar lista', 'yoper-core'),
                'new_item'           => __('Nova lista', 'yoper-core'),
                'view_item'          => __('Ver lista', 'yoper-core'),
                'search_items'       => __('Buscar listas', 'yoper-core'),
                'not_found'          => __('Nenhuma lista encontrada', 'yoper-core'),
                'not_found_in_trash' => __('Nenhuma lista na lixeira', 'yoper-core'),
                'menu_name'          => __('Listas de compras', 'yoper-core'),
            ),
            'capability_type' => array('yoper_purchase_list', 'yoper_purchase_lists'),
            'capabilities'    => array(
                'edit_post'              => 'yoper_view_purchase_lists',
                'read_post'              => 'yoper_view_purchase_lists',
                'delete_post'            => 'yoper_manage_purchase_lists',
                'edit_posts'             => 'yoper_view_purchase_lists',
                'edit_others_posts'      => 'yoper_manage_purchase_lists',
                'publish_posts'          => 'yoper_manage_purchase_lists',
                'read_private_posts'     => 'yoper_view_purchase_lists',
                'delete_posts'           => 'yoper_manage_purchase_lists',
                'delete_private_posts'   => 'yoper_manage_purchase_lists',
                'delete_published_posts' => 'yoper_manage_purchase_lists',
                'delete_others_posts'    => 'yoper_manage_purchase_lists',
                'edit_private_posts'     => 'yoper_view_purchase_lists',
                'edit_published_posts'   => 'yoper_manage_purchase_lists',
            ),
        ),
        'yoper_price_entry' => array(
            'labels' => array(
                'name'               => __('Registros de preço', 'yoper-core'),
                'singular_name'      => __('Registro de preço', 'yoper-core'),
                'add_new'            => __('Adicionar novo', 'yoper-core'),
                'add_new_item'       => __('Adicionar novo registro', 'yoper-core'),
                'edit_item'          => __('Editar registro', 'yoper-core'),
                'new_item'           => __('Novo registro', 'yoper-core'),
                'view_item'          => __('Ver registro', 'yoper-core'),
                'search_items'       => __('Buscar registros', 'yoper-core'),
                'not_found'          => __('Nenhum registro encontrado', 'yoper-core'),
                'not_found_in_trash' => __('Nenhum registro na lixeira', 'yoper-core'),
                'menu_name'          => __('Registros de preço', 'yoper-core'),
            ),
            'capabilities' => yoper_core_build_caps('yoper_add_price_entries', 'yoper_view_price_reports'),
            'capability_type' => array('yoper_price_entry', 'yoper_price_entries'),
        ),
    );

    foreach ($cpts as $post_type => $config) {
        $args = wp_parse_args($config, $base_args);
        register_post_type($post_type, $args);
    }
}

function yoper_core_register_product_taxonomies() {
    $labels = array(
        'name'              => __('Categorias de Produto', 'yoper-core'),
        'singular_name'     => __('Categoria de Produto', 'yoper-core'),
        'search_items'      => __('Buscar categorias', 'yoper-core'),
        'all_items'         => __('Todas as categorias', 'yoper-core'),
        'edit_item'         => __('Editar categoria', 'yoper-core'),
        'update_item'       => __('Atualizar categoria', 'yoper-core'),
        'add_new_item'      => __('Adicionar nova categoria', 'yoper-core'),
        'new_item_name'     => __('Nova categoria', 'yoper-core'),
        'menu_name'         => __('Categorias', 'yoper-core'),
    );

    register_taxonomy(
        'yoper_product_category',
        'yoper_product',
        array(
            'labels'            => $labels,
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => false,
            'show_admin_column' => true,
            'hierarchical'      => true,
            'show_in_quick_edit'=> false,
            'rewrite'           => false,
        )
    );
}
