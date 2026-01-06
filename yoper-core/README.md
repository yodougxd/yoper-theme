# Yoper Core — mapa rápido

Base plugin para funções comuns do projeto, preparado para multisite e network-activation.

## Bootstrap
- `yoper-core.php`: bloqueia acesso direto, define constantes (`YOPER_CORE_VERSION`, `YOPER_CORE_PATH`, `YOPER_CORE_URL`, `YOPER_CORE_BASENAME`), carrega includes, inicializa `Yoper_Core::init()` no `plugins_loaded`, trata ativação/desativação (por site ou rede via `yoper_core_for_each_site`), hook para `wpmu_new_blog`.
- `includes/class-yoper-core.php`: singleton `Yoper_Core`, guarda flag `is_network_mode()`, carrega módulos de operação (`includes/operation/*.php`) e registra hooks (`init` para CPTs e tradução, menus/admin settings).

## Utilidades e compatibilidade
- `includes/helpers.php`: `yoper_core_is_network_active()`, helpers de opção `yoper_core_get_option()`/`yoper_core_update_option()` com suporte a site/rede.
- `includes/multisite.php`: `yoper_core_get_sites()`, `yoper_core_with_site()`, `yoper_core_for_each_site()`, `yoper_core_get_current_site_info()`, handler `yoper_core_on_new_blog()` que reaplica ativação e defaults de negócio.
- `includes/capabilities.php`: lista de caps customizadas (`yoper_core_get_custom_capabilities()`), grant helper `yoper_core_grant_caps_to_role()`, provisionamento `yoper_core_setup_caps()` (admin + role `yoper_employee`).

## Conteúdo e config
- `includes/cpt.php`: registra `yoper_item` e CPTs internos (produtos, contagens de estoque, listas de compra), taxonomias de produto, helper `yoper_core_build_caps()` para mapear capabilities.
- `includes/admin-menu.php`: menus de admin e network, carregamento de assets (`yoper_core_admin_assets()`), telas `yoper_core_render_dashboard()` e `yoper_core_render_network_dashboard()`.
- `includes/settings-business.php`: registra opções de negócio (site/rede), campos de formulário e helper `yoper_core_get_business_name()`.
- `includes/acf-json.php`: configura paths de save/load de JSON do ACF e sincroniza campos de negócio com `yoper_business_settings`.

## Operações
- `includes/operation/products.php`: meta helpers (`yoper_core_get_product_meta()`/`update_*`), metaboxes, colunas de listagem e estilos da tela de produtos.
- `includes/operation/stock-count.php`: seleção de produtos ativos, render de tela de contagem (`yoper_core_render_stock_count_page()`), salvamento de contagem.
- `includes/operation/purchase-list.php`: geração de listas a partir de contagens, metaboxes de detalhes/itens, salvamento e modos de progresso.
- `includes/operation/price-entry.php`: tela de pesquisa de preços (`yoper_core_render_price_research_page()`), salvamento de entradas, relatórios (`yoper_core_render_price_reports_page()`), stats helpers (`yoper_core_price_stats()`, `yoper_core_price_indicator()`).
