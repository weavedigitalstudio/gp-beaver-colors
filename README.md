# GP Beaver Colors

This plugin integrates GeneratePress's Global Color Palette with Beaver Builder, allowing you to maintain consistent branding across your website when WordPress Global Styles are disabled.

It automatically makes your GeneratePress theme Global Color Palette available in Beaver Builder's color picker ensuring color consistency throughout your site.

## Features

The plugin provides two main functions:

1. Color Integration: Makes your GeneratePress Global Colors automatically available in Beaver Builder's color picker by adding the correct block editor prefix '--wp--preset--color--'.

2. Color Documentation: Includes a shortcode `[gp_global_color_grid]` for use on a style guide which creates a visual display of your color palette, perfect for style guides or documentation pages.

## How It Works

The plugin works automatically once activated. You don't need to:

- Set up a separate Global Color Palette in Beaver Builder
- Make any changes to your existing GeneratePress color settings
- Configure any plugin settings

Just set your colors in GeneratePress, and they'll be available in Beaver Builder's color picker automatically.

## Using the Color Grid

To display your color palette anywhere on your site, use the shortcode:

```shortcode
[gp_global_color_grid]
```

This creates a responsive grid showing all your GeneratePress Global Colors with their names, CSS variables, and hex values.

---

## Installation from GitHub

When installing this plugin from GitHub:

1. Go to the [Releases](https://github.com/weavedigitalstudio/gp-beaver-colors/releases) page
2. Download the latest release ZIP file
3. Extract the ZIP file on your computer
4. Rename the extracted folder to remove the version number
   (e.g., from `gp-beaver-colors-0.3.0` to `gp-beaver-colors`)
5. Create a new ZIP file from the renamed folder
6. In your WordPress admin panel, go to Plugins → Add New → Upload Plugin
7. Upload your new ZIP file and activate the plugin
8. Plugin should then auto-update moving forward if there are any changes.

**Note**: The folder renaming step is necessary for WordPress to properly handle plugin updates and functionality.

## Requirements

- GeneratePress theme
- Beaver Builder plugin
- WordPress Global Styles disabled (recommended for performance)

## Removing Global Styles

For optimal performance, add this code to your theme's functions.php file or use something like Perfmatters.

```php
add_action("wp_enqueue_scripts", "remove_global_styles");
function remove_global_styles()
{
	wp_dequeue_style("global-styles");
```

For more information about removing global inline styles, see:
[https://perfmatters.io/docs/remove-global-inline-styles-wordpress/](https://perfmatters.io/docs/remove-global-inline-styles-wordpress/)

## Changelog

### 0.4.0

- Added color grid shortcode for displaying color palettes in style guides
- Updated plugin name for clarity
- Improved code organization and inline documentation
- Enhanced compatibility checks

### 0.3.0

- Minified CSS output for better performance
- Removed unnecessary comments and whitespace in generated styles
