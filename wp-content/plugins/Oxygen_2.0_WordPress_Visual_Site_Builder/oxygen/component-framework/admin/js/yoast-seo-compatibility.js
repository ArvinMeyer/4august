(function($){
	YoastOxygen = function() {
		if(typeof(YoastSEO) !== 'undefined') {
			YoastSEO.app.registerPlugin( 'YoastOxygen', {status: 'ready'} );
			YoastSEO.app.registerModification( 'content', this.replaceDataWithOxygenMarkup, 'YoastOxygen', 5 );
		}
	}

	/**
	 * Replaces the full content with Oxygen generated markup, as it is supposed to contain the_content too
	 *
	 * @param data The data to modify
	 */
	YoastOxygen.prototype.replaceDataWithOxygenMarkup = function(data) {
		// The full Oxygen generated markup is already enqueued
		return ysco_data.oxygen_markup;
	};

	$(document).ready(function(){
		new YoastOxygen();
	});
})(jQuery);
