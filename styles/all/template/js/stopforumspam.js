(function($) { // Avoid conflicts with other libraries

	'use strict';

	// Hide the report button of a post or private message once it has been reported
	phpbb.addAjaxCallback('reporttosfs', function(data) {
		if (typeof data.postid !== 'undefined') {
			$('#sfs' + data.postid).hide();
			phpbb.closeDarkenWrapper(5000);
		}
	});

})(jQuery);
