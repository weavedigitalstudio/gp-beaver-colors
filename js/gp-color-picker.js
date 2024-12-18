(function($) {
	// Ensure WordPress Iris exists
	if (!$.a8c || !$.a8c.iris) {
		return;
	}

	// Extend Iris to set the GeneratePress palette globally
	var originalCreate = $.a8c.iris.prototype._create;
	$.a8c.iris.prototype._create = function() {
		// Set the custom palette globally
		if (typeof generatePressPalette !== 'undefined' && Array.isArray(generatePressPalette)) {
			this.options.palettes = generatePressPalette;
		}

		// Call the original Iris create function
		originalCreate.call(this);
	};

	generatePressPalette);
})(jQuery);
