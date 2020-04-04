(function($) { // Avoid conflicts with other libraries
	'use strict';
	phpbb.addAjaxCallback('build_adminsmods', function(res) {
		if (typeof res.success === 'undefined' || !res.success) {
			return;
		}

		$('#sfs_notice').remove();
		phpbb.alert(res.MESSAGE_TITLE, res.MESSAGE_TEXT);		
	});
})(jQuery);