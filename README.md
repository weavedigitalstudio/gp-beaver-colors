# GeneratePress Global Color Palette for Beaver Builder
This WordPress plugin seamlessly integrates GeneratePress's Global Color Palette with Beaver Builder, allowing you to maintain consistent branding across your website. 

It automatically allows Beaver Builder's color picker to see your GeneratePress theme Global Color Palette, streamlining your design workflow and ensuring color consistency throughout your site.

This is needed is you do not use the the WP Global Inline block styles.

If you have removed the Global Inline styles from WordPress as you are not using a FSE (or a Block theme) you can use this plugin which adds the correct css variable prefix to the Generate Press theme Global Colours in order for them to be picked up and to work with Beave Builder.

You do not need to set a Global Colour Palette in Beaver Builder also.
It adds the correct block editor prefix '--wp--preset--color--' to the GeneratePress Global Colors so they are usable in Beaver Builder.

We remove the Global Inline styles for performance reason as we do not currently use them.
https://perfmatters.io/docs/remove-global-inline-styles-wordpress/
