<?php
/**
 * Plugin Name:       GeneratePress Global Colour Palette for Beaver Builder
 * Plugin URI:        https://github.com/weavedigitalstudio/GeneratePress-BB-Color-Palettes 
 * Description:       A custom plugin to add Beaver Builder color picker compatibility for the GeneratePress Global Color Palette.
 * Version:           0.2.9
 * Primary Branch:    main
 * GitHub Plugin URI: weavedigitalstudio/GeneratePress-BB-Color-Palettes
 * Author:            Weave Digital Studio
 * Author URI:        https://weave.co.nz
 * License:           GPL-2.0+
 */
 
namespace GP\BB_Color_Palette;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin activation checks and setup.
 * 
 * @return void
 */
function activate_plugin(): void {
    // Check for GeneratePress theme
    if ( ! is_generatepress_active() ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 
            'This plugin requires GeneratePress theme to be installed and active. Please install and activate GeneratePress first.',
            'Plugin Activation Error',
            array( 'back_link' => true )
        );
    }

    // Check for Beaver Builder
    if ( ! class_exists( 'FLBuilder' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 
            'This plugin requires Beaver Builder to be installed and active. Please install and activate Beaver Builder first.',
            'Plugin Activation Error',
            array( 'back_link' => true )
        );
    }
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate_plugin' );

/**
 * Checks if the active theme is GeneratePress or its child theme.
 *
 * @return bool True if GeneratePress or its child theme is active, false otherwise.
 */
function is_generatepress_active(): bool {
    $theme = wp_get_theme();
    return ( 'GeneratePress' === $theme->get( 'Name' ) || 'generatepress' === $theme->get_template() );
}

/**
 * Check if all required plugins and theme are active.
 * 
 * @return bool True if all dependencies are met, false otherwise.
 */
function check_dependencies(): bool {
    if ( ! is_generatepress_active() || ! class_exists( 'FLBuilder' ) || ! function_exists( 'generate_get_global_colors' ) ) {
        return false;
    }
    return true;
}

/**
 * Generates custom CSS for global color variables compatible with Beaver Builder.
 *
 * @param array $global_colors Array of global color data (slug and color values).
 * @return string Generated CSS for the global colors.
 */
function generate_global_colors_css( array $global_colors ): string {
    if ( empty( $global_colors ) ) {
        return '';
    }

    $css = "/* Beaver Builder Color Compatibility with GeneratePress */\n:root {\n";
    foreach ( $global_colors as $data ) {
        if ( ! isset( $data['slug'] ) || ! isset( $data['color'] ) ) {
            continue;
        }
        $css .= sprintf( "--wp--preset--color--%s: %s;\n", esc_attr( $data['slug'] ), esc_attr( $data['color'] ) );
    }
    $css .= '}';

    return $css;
}

/**
 * Enqueues custom inline styles for Beaver Builder compatibility.
 */
function enqueue_inline_styles(): void {
    if ( ! check_dependencies() ) {
        return;
    }

    $global_colors = generate_get_global_colors();
    $css = generate_global_colors_css( $global_colors );

    if ( ! empty( $css ) && wp_style_is( 'generate-style', 'enqueued' ) ) {
        wp_add_inline_style( 'generate-style', $css );
    }
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_inline_styles', 20 );

/**
 * Enqueues scripts and localizes the GeneratePress global palette for JavaScript.
 */
function enqueue_scripts(): void {
    if ( ! check_dependencies() ) {
        return;
    }

    $global_colors = generate_get_global_colors();

    // Map colors to a simple palette array for JavaScript
    $palette = array_map(
        function( $color ) {
            return isset( $color['color'] ) ? $color['color'] : '';
        },
        $global_colors
    );

    // Only enqueue if we have colors to work with
    if ( ! empty( $palette ) ) {
        wp_enqueue_script(
            'generatepress-color-picker',
            plugin_dir_url( __FILE__ ) . 'js/gp-color-picker.js',
            [ 'wp-color-picker' ],
            '0.2.9', // Use plugin version for cache busting
            true
        );

        wp_localize_script( 'generatepress-color-picker', 'generatePressPalette', $palette );
    }
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts' );

/**
 * Add admin notice if dependencies are not met.
 */
function admin_notices(): void {
    if ( ! is_generatepress_active() ) {
        echo '<div class="notice notice-error"><p>GeneratePress theme is required for the BB Color Palette plugin to work.</p></div>';
    }
    if ( ! class_exists( 'FLBuilder' ) ) {
        echo '<div class="notice notice-error"><p>Beaver Builder plugin is required for the BB Color Palette plugin to work.</p></div>';
    }
}
add_action( 'admin_notices', __NAMESPACE__ . '\\admin_notices' );
