<?php
/**
 * Plugin Name:       GeneratePress Beaver Builder Color Palette Compatibility
 * Plugin URI:        https://github.com/weavedigitalstudio/GeneratePress-BB-Color-Palettes 
 * Description:       A custom plugin to add Beaver Builder color compatibility for the GeneratePress Global Color Palette.
 * Version:           0.0.4
 * Primary Branch:    main
 * GitHub Plugin URI: https://github.com/weavedigitalstudio/GeneratePress-BB-Color-Palettes
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
function is_generatepress_active() {
    $theme = wp_get_theme();

    // Check if the current theme is GeneratePress or if it is a child theme of GeneratePress.
    if ( 'GeneratePress' === $theme->get( 'Name' ) || 'GeneratePress' === $theme->get( 'Template' ) ) {
        return true;
    }

    return false;
}

/**
 * Generates custom CSS for global color variables compatible with Beaver Builder.
 *
 * This function generates CSS variables for the GeneratePress global colors with the prefix
 * `--wp--preset--color--` and returns the CSS as a string, allowing Beaver Builder
 * to recognize and utilise GeneratePress global colors.
 *
 * @param array $global_colors Array of global color data (slug and color values).
 * @return string Generated CSS for the global colors.
 */
function generate_custom_global_colors_css( $global_colors ) {
    // Start the custom CSS with a comment and root selector
    $custom_css = '/* Beaver Builder Color Compatibility with GeneratePress */' . "\n";
    $custom_css .= ':root {';

    // Check if there are any global colors to process
    if ( ! empty( $global_colors ) ) {
        foreach ( (array) $global_colors as $key => $data ) {
            // Add each color as a CSS variable with the custom prefix for Beaver Builder
            $custom_css .= '--wp--preset--color--' . $data['slug'] . ':' . $data['color'] . ';';
        }
    }

    // Close the root selector
    $custom_css .= '}';

    // Return the generated CSS string
    return $custom_css;
}

/**
 * Enqueue custom inline styles for Beaver Builder compatibility.
 *
 * This function retrieves the global colors from GeneratePress,
 * generates custom CSS using the generate_custom_global_colors_css function,
 * and enqueues the CSS as inline styles, making the colors available in Beaver Builder.
 */
function generate_enqueue_custom_inline_styles() {
    // Check if GeneratePress is active
    if ( ! is_generatepress_active() ) {
        return; // Exit early if GeneratePress is not the active theme
    }

    // Get the global colors defined by GeneratePress
    $global_colors = generate_get_global_colors();

    // Generate the custom global colors CSS for Beaver Builder
    $custom_global_colors_css = generate_custom_global_colors_css( $global_colors );

    // Add the custom CSS as inline styles, attached to the 'generate-style' handle
    wp_add_inline_style( 'generate-style', $custom_global_colors_css );
}

// Hook the function to enqueue custom styles after the theme styles are loaded
add_action( 'wp_enqueue_scripts', 'generate_enqueue_custom_inline_styles', 20 );
