(function( $ ) {
	'use strict';

	$( document ).ready( function() {
		$.each( tkt_select2, function( instance, config ) {
			$( '#' + instance ).select2({
				placeholder: config.placeholder
			});
		});
	});

})( jQuery );
