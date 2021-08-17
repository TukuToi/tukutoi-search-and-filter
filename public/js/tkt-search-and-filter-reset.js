(function( $ ) {
	'use strict';

	$( document ).ready( function() {

	    /**
	     * Trigger reset with AJAX
	     */
	 	$('[type=reset]').each(function(){
	 		$(this).off().on('click', function(){ // These little off() are a lifesaver. Without it, you will sum up clicks over clicks and it never stops..
				$( this ).closest( 'form' ).each(function(){
	 				$( this ).off().on('reset', function(){
	 					if( 'undefined' !== typeof tkt_ajax_reset_object){
	 						// We just need one valid input element to trigger a change, we dont need to trigger all.
	 						var element = $(this ).find( ':input' ).not(':input[type=reset],:input[type=hidden],:input[type=submit]')[0];
		 					setTimeout(function(){ 
		 						$(element).trigger('change');
		 						return false;
		 					}, 1, element);
		 				} else{
		 					window.location = window.location.href.split("?")[0];
		 				}
		 			});
		 		});
	 		});
	 	});
		
	});

})( jQuery );
