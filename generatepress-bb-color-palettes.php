<?php
/**
 * Plugin Name:       GenPress global colour palette for Beaver Builder colour picker
 * Plugin URI:        https://github.com/weavedigitalstudio/GeneratePress-BB-Color-Palettes 
 * Description:       A custom plugin to add Beaver Builder color picker compatibility for the GeneratePress Global Color Palette.
 * Version:           0.1.1
 * Primary Branch:    main
 * GitHub Plugin URI: weavedigitalstudio/GeneratePress-BB-Color-Palettes
 * Author:            Weave Digital Studio
 * License:           MIT
 */

// Prevent direct access to the plugin file.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Check if the active theme is GeneratePress or a child theme of GeneratePress.
 *
 * @return bool True if GeneratePress or a child theme of GeneratePress is active, false otherwise.
 */
function generatepress_is_active() {
    $theme = wp_get_theme();
    return ( 'GeneratePress' === $theme->get( 'Name' ) || 'generatepress' === $theme->get_template() );
}

/**
 * Generates custom CSS for global color variables compatible with Beaver Builder.
 *
 * @param array $global_colors Array of global color data (slug and color values).
 * @return string Generated CSS for the global colors.
 */
function generate_custom_global_colors_css( $global_colors ) {
    $custom_css = '/* Beaver Builder Color Compatibility with GeneratePress */' . "\n";
    $custom_css .= ':root {';

    if ( ! empty( $global_colors ) ) {
        $css_variables = array();
        foreach ( (array) $global_colors as $data ) {
            $css_variables[] = '--wp--preset--color--' . $data['slug'] . ':' . $data['color'] . ';';
        }
        $custom_css .= implode( "\n", $css_variables );
    }

    $custom_css .= '}';

    return $custom_css;
}

/**
 * Enqueue custom inline styles for Beaver Builder compatibility.
 */
function generate_enqueue_custom_inline_styles() {
    if ( ! generatepress_is_active() ) {
        return; // Exit early if GeneratePress is not the active theme
    }

    if ( ! function_exists( 'generate_get_global_colors' ) ) {
        return; // Handle the error gracefully
    }

    $global_colors = generate_get_global_colors();
    $custom_global_colors_css = generate_custom_global_colors_css( $global_colors );

    if ( wp_style_is( 'generate-style', 'enqueued' ) ) {
        wp_add_inline_style( 'generate-style', $custom_global_colors_css );
    }
}

add_action( 'wp_enqueue_scripts', 'generate_enqueue_custom_inline_styles', 20 );
