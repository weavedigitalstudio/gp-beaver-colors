<?php
/**
 * GP Beaver Colors - Color Grid Shortcode
 *
 * This file provides the shortcode functionality for displaying GeneratePress color palettes
 * in a visual grid format. It's particularly useful for style guides and documentation.
 *
 * The shortcode [gp_global_color_grid] creates a responsive grid that shows:
 * - Color swatches using your GeneratePress Global Colors
 * - Color names from your palette
 * - CSS variable names for developer reference
 * - Hex color codes
 *
 * @package GP_Beaver_Colors
 * @since 0.4.0
 */

// Prevent direct access to this file
if (!defined("ABSPATH")) {
    exit();
}

/**
 * Calculates whether text should be light or dark based on background color
 * Uses W3C recommendations for contrast calculations to ensure readability
 *
 * @param string $hexcolor The hex color code to analyze (with or without #)
 * @return string Returns either black (#000000) or white (#ffffff) hex code
 */
function gpbc_get_readable_text_color($hexcolor)
{
    $hex = ltrim($hexcolor, "#");
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Calculate relative luminance using W3C formula
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $luminance > 0.5 ? "#000000" : "#ffffff";
}

/**
 * Renders the color grid display
 * Creates a responsive grid showing all GeneratePress Global Colors
 *
 * @return string HTML output of the color grid
 */
function gpbc_render_color_grid()
{
    // Bail early if GeneratePress isn't active
    if (!function_exists("generate_get_option")) {
        return "<p>GeneratePress not active</p>";
    }

    // Register and enqueue styles if not already done
    if (!wp_style_is("gp-color-grid-styles")) {
        wp_register_style("gp-color-grid-styles", false, [], "0.4.0");

        // Define our grid styles
        wp_add_inline_style(
            "gp-color-grid-styles",
            '
            .gp-color-grid-alt {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
                gap: 20px;
                margin-block: 40px;
            }

            .gp-color-box {
                height: 190px;
                padding: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
            }

            .gp-color-box.has-white-bg {
                border: 1px solid #000;
            }

            .gp-color-info-alt {
                display: flex;
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }

            .gp-color-label-alt {
                font-size: 1.4em;
                font-weight: bold;
                margin: 0;
                color: inherit;
            }

            .gp-color-var-alt,
            .gp-color-hex-alt {
                font-family: ui-monospace, Menlo, Monaco, "Courier New", monospace;
                font-size: 0.9em;
                background: inherit;
            }
        '
        );
        wp_enqueue_style("gp-color-grid-styles");
    }

    // Get GeneratePress settings once and cache the result
    static $global_colors = null;
    if ($global_colors === null) {
        $gp_settings = get_option("generate_settings");
        $global_colors = isset($gp_settings["global_colors"])
            ? $gp_settings["global_colors"]
            : [];
    }

    // Start output buffering for clean return
    ob_start();

    echo '<section class="gp-style-guide-alt">';
    echo "<h2>Global Colors</h2>";
    echo '<div class="gp-color-grid-alt">';

    if (!empty($global_colors)) {
        foreach ($global_colors as $color_slug => $color_data) {
            // Skip if we're missing required color data
            if (empty($color_data["color"]) || empty($color_data["name"])) {
                continue;
            }

            // Prepare our display values
            $var_name = "--" . sanitize_title(strtolower($color_data["name"]));
            $label = esc_html($color_data["name"]);
            $hex = esc_attr($color_data["color"]);
            $text_color = gpbc_get_readable_text_color($hex);

            // Check if the color is white (case insensitive)
            $is_white = strtolower($hex) === '#ffffff';
            $white_class = $is_white ? ' has-white-bg' : '';

            // Output the color box with all its information
            printf(
                '<article class="gp-color-box%5$s" style="background-color: var(%1$s)">
                    <div class="gp-color-info-alt" style="color: %4$s">
                        <h3 class="gp-color-label-alt">%2$s</h3>
                        <code class="gp-color-var-alt">var(%1$s)</code>
                        <code class="gp-color-hex-alt">%3$s</code>
                    </div>
                </article>',
                $var_name,
                $label,
                $hex,
                $text_color,
                $white_class
            );
        }
    } else {
        echo "<p>No global colors found in GeneratePress Customizer color settings.</p>";
    }

    echo "</div>";
    echo "</section>";

    return ob_get_clean();
}

// Register our shortcode with WordPress
add_shortcode("gp_global_color_grid", "gpbc_render_color_grid");
