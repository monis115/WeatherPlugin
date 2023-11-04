<?php
namespace ExtendBuilder;

use ColibriWP\PageBuilder\ThemeHooks;

add_filter( 'colibri_page_builder/customizer/preview_data/currentPageType', function ( $value ) {
    if(colibri_woocommerce_is_active()) {
        $is_product = \is_product();
        array_set_value($value, 'isWooArchive', colibri_is_woocomerce_archive_page() || is_woocommerce_page());
        array_set_value($value, 'isWooProduct', $is_product);
    }
    return $value;
});

add_filter('colibri_page_builder/customizer/preview_data/currentPageId', function ($value) {
    if ( colibri_is_woocomerce_archive_page() ) {
        return -1;
    }
    return $value;
});


add_action( 'plugins_loaded', function () {
    add_filter( 'colibri_page_builder/customizer/preview_data', function ( $value ) {
        array_set_value($value, 'activePlugins.woocommerce', woocommerce_is_enabled());
        return $value;
    } );
} );

ThemeHooks::prefixed_add_filter( "colibri_sidebar_enabled", function ( $value, $sidebar_id ) {
    
     if (woocommerce_is_enabled() && (is_woocommerce_page() || \is_product())) {
        return false;
    }
    if ( is_customize_preview() ) {
        return true;
    }
    $side = str_replace( 'ecommerce-', '', $sidebar_id );
    return $value && get_templates_prop( 'woocommerce', "section.sidebars.{$side}", true );
}, 1000, 2);



// inheritance
add_filter(prefix("default_partial"), function ($value, $type) {
    // archive default//
    if ($type == "sidebar" && $value == -1 && woocommerce_is_enabled() && woocommerce_is_shop_archive_page()) {
        $default = get_default_partial_id($type, "product");
        return $default;
    }
    return $value;
}, 11, 2);

add_filter(prefix("default_partial"), function ($value, $type) {
    if ($type == "sidebar" && woocommerce_is_enabled() && \is_cart() ) {
        return -1;
    }
    return $value;
}, 10, 2);
add_filter('colibri_show_page_content', function ($value) {
    if (woocommerce_is_enabled() && is_woocommerce_page()) {
        return false;
    }
    return $value;
});
add_filter('colibri_page_builder/post_supports_partial', function($value, $post_id, $type) {
    if ($type == "main" && woocommerce_is_enabled() && (is_woocommerce_page() || woocommerce_is_shop_archive_page() || \is_product()) ) {
        return false;
    }
    return $value;
}, 10, 3);
