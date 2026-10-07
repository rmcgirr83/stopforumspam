(function($) { // Avoid conflicts with other libraries

	'use strict';

	// Hide the "clear reports" row once the reports have been cleared
	phpbb.addAjaxCallback('sfsclrreports', function(data) {
		if (data.success !== false) {
			$('#sfsreportcount').hide();
			phpbb.closeDarkenWrapper(5000);
		}
	});

})(jQuery);
