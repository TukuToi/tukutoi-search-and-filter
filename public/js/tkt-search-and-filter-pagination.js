var TKT_GLOBAL_NAMESPACE = {}; // TukuToi Global Scope
(function( $ ) {
	'use strict';

	var page, url;

	/**
	 * Pagination Query 
	 */
	TKT_GLOBAL_NAMESPACE.pagination_query = function ( instance, tkt_ajax_params, max, paged_value ) {

		$.ajax({
		    type: 'POST',
		    url: tkt_ajax_pag_params[instance].ajax_url,
		    data: {
		        action: 'tkt_ajax_pagination',
		        is_doing_ajax: tkt_ajax_pag_params[instance].is_doing_ajax,
		        nonce: tkt_ajax_pag_params[instance].nonce,
		        instance: instance,
		        atts: tkt_ajax_pag_params[instance].atts,
		        page: paged_value,
		        paged: tkt_ajax_params.query_args.pag_arg,
		        add_args: tkt_ajax_pag_params[instance].add_args,
		        max: max,
		    },
		    beforeSend: function() {
		        $('.tkt_ajax_loader').show();
		    },
		    success: function(pagination) {
		        $('.tkt_ajax_loader').hide();
		        $('#' + instance + '_pagination').html(pagination.data);
		        TKT_GLOBAL_NAMESPACE.paginate(instance, tkt_ajax_params);
		    },
		    error: function() {
		        $('.tkt_ajax_loader').hide();
		    }
		});

	}

	/**
	 * Pagination Trigger
	 */
	TKT_GLOBAL_NAMESPACE.paginate = function ( instance, tkt_ajax_params ) {
	    $( '#' + tkt_ajax_params.instance + '_pagination' + ' a').each(function(){

			$(this).on('click', function(e){
		        e.preventDefault();

			    url = $(this).attr('href'); 
			    page = url.split( '?' + tkt_ajax_params.query_args.pag_arg + '=' ); 
			    if( 'undefined' !== typeof page[1] ){
			       	page = page[1].split( '&' ); 
		        } else {
			        page = 1;
			        TKT_GLOBAL_NAMESPACE.filter_query(instance, tkt_ajax_params, page);
			    }
			    TKT_GLOBAL_NAMESPACE.filter_query(instance, tkt_ajax_params, page[0]); 
		    });

		});
	}

})( jQuery );