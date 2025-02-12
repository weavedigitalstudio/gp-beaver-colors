<?php
/**
 * Plugin Name:       GP Beaver Colors
 * Plugin URI:        https://github.com/weavedigitalstudio/gp-beaver-colors
 * Description:       Integrates GeneratePress Global Colors with Beaver Builder's color picker for consistent branding.
 * Version:           0.4.1
 * Primary Branch:    main
 * GitHub Plugin URI: weavedigitalstudio/gp-beaver-colors
 * Author:           Weave Digital Studio
 * Author URI:        https://weave.co.nz
 * License:           GPL-2.0+
 */

// Prevent direct access to this file
if (!defined("ABSPATH")) {
	exit();
}

/**
 * Handles plugin activation requirements
 * Makes sure GeneratePress and Beaver Builder are active before allowing activation
 */
function gpbc_activate_plugin()
{
	$theme = wp_get_theme();
	$is_generatepress =
		"GeneratePress" === $theme->get("Name") ||
		"generatepress" === $theme->get_template();

	if (!$is_generatepress || !class_exists("FLBuilder")) {
		deactivate_plugins(plugin_basename(__FILE__));
		$missing = !$is_generatepress ? "GeneratePress theme" : "Beaver Builder";
		wp_die(
			"This plugin requires both GeneratePress theme and Beaver Builder to be installed and active. Missing: {$missing}",
			"Plugin Activation Error",
			["back_link" => true]
		);
	}
}
register_activation_hook(__FILE__, "gpbc_activate_plugin");

/**
 * Includes the color grid shortcode functionality
 * We keep this separate as it's an additional feature
 */
require_once plugin_dir_path(__FILE__) . "includes/color-grid.php";

/**
 * Generates CSS for global color variables
 * This makes GeneratePress colors available to Beaver Builder
 */
function gpbc_generate_global_colors_css($global_colors)
{
	if (empty($global_colors)) {
		return "";
	}

	$css = ":root{";
	foreach ($global_colors as $data) {
		if (!isset($data["slug"]) || !isset($data["color"])) {
			continue;
		}
		$css .= sprintf(
			"--wp--preset--color--%s:%s;",
			esc_attr($data["slug"]),
			esc_attr($data["color"])
		);
	}
	return $css . "}";
}

/**
 * Enqueues the color variables as inline CSS
 * This adds them to the GeneratePress stylesheet for efficiency
 */
function gpbc_enqueue_inline_styles()
{
	if (!function_exists("generate_get_global_colors")) {
		return;
	}

	$global_colors = generate_get_global_colors();
	$css = gpbc_generate_global_colors_css($global_colors);

	if (!empty($css) && wp_style_is("generate-style", "enqueued")) {
		wp_add_inline_style("generate-style", $css);
	}
}
add_action("wp_enqueue_scripts", "gpbc_enqueue_inline_styles", 20);

/**
 * Enqueues the color picker enhancement script
 * Makes the colors available in Beaver Builder's color picker
 */
function gpbc_enqueue_admin_scripts()
{
	if (!function_exists("generate_get_global_colors")) {
		return;
	}

	$global_colors = generate_get_global_colors();

	// Only enqueue if we have colors to work with
	if (!empty($global_colors)) {
		wp_enqueue_script(
			"gpbc-color-picker",
			plugin_dir_url(__FILE__) . "js/color-picker.js",
			["wp-color-picker"],
			"0.4.1",
			true
		);

		// Convert colors array to simple palette array
		$palette = array_map(function ($color) {
			return isset($color["color"]) ? $color["color"] : "";
		}, $global_colors);

		wp_localize_script("gpbc-color-picker", "generatePressPalette", $palette);
	}
}
add_action("admin_enqueue_scripts", "gpbc_enqueue_admin_scripts");
