<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BRGL_Divi5_Integration {
    public static function init() {
        add_action( 'divi_module_library_modules_dependency_tree', array( __CLASS__, 'register_modules' ) );
        add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array( __CLASS__, 'enqueue_visual_builder_assets' ) );
        add_action( 'wp_ajax_brgl_divi5_preview', array( __CLASS__, 'preview_ajax' ) );
    }

    public static function register_modules( $dependency_tree ) {
        if( ! self::is_enabled() || ! class_exists('ET\\Builder\\Packages\\ModuleLibrary\\ModuleRegistration') ) {
            return;
        }

        require_once __DIR__ . '/loader.php';
        brgl_divi5_register_modules( $dependency_tree );
    }

    public static function enqueue_visual_builder_assets() {
        if( ! self::is_enabled() || ! class_exists('ET\\Builder\\VisualBuilder\\Assets\\PackageBuildManager') ) {
            return;
        }

        $asset_path = dirname( __DIR__ ) . '/visual-builder/build/woocommerce-grid-list-view-divi5.js';
        if( ! file_exists( $asset_path ) ) {
            return;
        }

        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
            array(
                'name'    => 'woocommerce-grid-list-view-divi5-visual-builder',
                'version' => BeRocket_List_Grid_version . '-' . filemtime( $asset_path ),
                'script'  => array(
                    'src'                => add_query_arg(
                        array(
                            'brgl_ajax_url' => rawurlencode( admin_url( 'admin-ajax.php' ) ),
                            'brgl_action'   => 'brgl_divi5_preview',
                            'brgl_nonce'    => wp_create_nonce( 'brgl_divi5_preview' ),
                            'brgl_build'    => filemtime( $asset_path ),
                        ),
                        plugin_dir_url( BeRocket_List_Grid_file ) . 'divi5/visual-builder/build/woocommerce-grid-list-view-divi5.js'
                    ),
                    'deps'               => array( 'divi-module-library', 'divi-vendor-wp-hooks' ),
                    'enqueue_top_window' => false,
                    'enqueue_app_window' => true,
                ),
            )
        );
    }

    public static function preview_ajax() {
        if( ! self::is_enabled() ) {
            wp_send_json_error( array( 'message' => __( 'Divi 5 is not enabled.', 'BeRocket_LGV_domain' ) ), 400 );
        }

        if( ! check_ajax_referer( 'brgl_divi5_preview', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'BeRocket_LGV_domain' ) ), 403 );
        }

        if( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) && ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to preview this module.', 'BeRocket_LGV_domain' ) ), 403 );
        }

        $attrs_json = empty( $_POST['attrs'] ) ? '{}' : wp_unslash( $_POST['attrs'] );
        $attrs      = json_decode( $attrs_json, true );
        if( ! is_array( $attrs ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid module attributes.', 'BeRocket_LGV_domain' ) ), 400 );
        }

        require_once __DIR__ . '/ModuleRenderer.php';
        $renderer = new BRGL_Divi5_Module_Renderer();
        wp_send_json_success( array( 'html' => $renderer->render_module( $attrs ) ) );
    }

    private static function is_enabled() {
        return function_exists('et_builder_d5_enabled') && et_builder_d5_enabled();
    }
}
