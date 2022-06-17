var TKT_GLOBAL_NAMESPACE = {};
(function( $ ) {
	'use strict';

	var inputs = {};
	var selects = {};
	var form_inputs = {};
	var paged_value, paged, ajax_url, instance, query_args, error, is_doing_ajax;

	$( document ).ready( function() {

		/**
	     * Get all search values of each input of each form.
	     */
	 	function get_form_search_values() {
	 		

		 	$('form[data-tkt-ajax-src-form]').each(function(){

		 		form_inputs[$(this).data("tkt-ajax-src-form")] = {};

			    get_search_values_by_type( $(this).data("tkt-ajax-src-form") );//my_instance
			    
			});

		}

		/**
	     * Get all search values of each input by type.
	     * 
	     * data-tkt-ajax-src-form (form) is === instance.
	     * 
	     * @since 2.29.0 Added tax_query case.
	     */
	    function get_search_values_by_type( form ) {

	    	form_inputs[form] = { 'inputs':{}, 'selects':{} };
	    	form_inputs[form]['selects']['tax_query'] = new Array();

	    	/**
	    	 * For all non-select, non-hidden inputs.
	    	 */
	        $('form[data-tkt-ajax-src-form="' + form + '"] input').not(':input[type=hidden]').each(function(){

			    form_inputs[form]['inputs'][$(this).attr('data-tkt-ajax-src')] = $(this).val();

			})
			/**
			 * For all select inputs.
			 */
			$('form[data-tkt-ajax-src-form="' + form + '"] select').each(function(){

				// If not a tax-query input.
				if( 'tax_query' !== $(this).attr('data-tkt-ajax-src') ) {
				 	form_inputs[form]['selects'][$(this).attr('data-tkt-ajax-src')] = $(this).val();
				}
				// If a tax-query input.
			    if( '' !== $(this).attr('data-tkt-ajax-custom') && $(this).attr('data-tkt-ajax-custom').length && null !== $(this).val() ) {
			    	form_inputs[form]['selects']['tax_query'].push({ 'taxonomy': $(this).attr('data-tkt-ajax-custom'), 'fields': 'term_id', 'terms': $(this).val() })
			    }
			})
	    }

		/**
	 	 * For each Loop Instance there is.
	 	 */
	 	$.each( tkt_ajax_loop_object, function( instance, tkt_ajax_params ) {

	 		/**
		     * Get the posts with AJAX.
		     * 
		     * We GET the requested results.
		     * Then we POST the template so it can be expanded and sent back with each post data from GET.
		     * Then we POST again in case of pagination, to refresh pagination section.
		     * 
		     * @since 2.19.0
		     * @param int $paged The page to get.
		     */
			TKT_GLOBAL_NAMESPACE.filter_query = function ( instance, tkt_ajax_params, paged ) {	
	    	
	    		if( 'undefined' !== typeof form_inputs[instance] ){
	    			selects = form_inputs[instance]['selects'];
	    			inputs = form_inputs[instance]['inputs'];
	    		}

		        paged_value 	= paged; //Store the paged value if it's being sent through when the function is called
		        ajax_url 		= tkt_ajax_params.ajax_url; //Get ajax url (added through wp_localize_script)
		        instance 		= tkt_ajax_params.instance;
		        query_args 		= $.extend(tkt_ajax_params.query_args, selects, inputs);
		        error 			= tkt_ajax_params.error;
		        is_doing_ajax 	= tkt_ajax_params.is_doing_ajax;


		        if( 'undefined' === typeof paged_value ){
		    		paged_value = 1;
		    	}
	    	
		        $.ajax({
		            type: 'GET',
		            url: ajax_url,
		            data: {
		            	action: 'tkt_ajax_query',
		            	nonce: tkt_ajax_params.nonce,
		            	is_doing_ajax: is_doing_ajax,
		                paged: paged_value, //If paged value is being sent through with function call, store here
		                instance: instance,
		                query_args: query_args,
		            },
		            beforeSend: function () {	
		            	$('.tkt_ajax_loader').show();
		            },
		            success: function( results ) {

		                $.ajax({
		                	type: 'POST',
		                	url: ajax_url,
		                	data: {
		                		action: 'tkt_ajax_loop',
		                		nonce: tkt_ajax_params.nonce,
		                		is_doing_ajax: is_doing_ajax,
		                		template: tkt_ajax_params.content,
		                		objects: results.data.ids,
		                		instance: tkt_ajax_params.instance,
		                		error: error,
		                	},
					        beforeSend: function () {	
					        },
					        success: function( layout ){
					        	$('.tkt_ajax_loader').hide();
					            $( '#' + instance ).html( layout.data );
					            TKT_GLOBAL_NAMESPACE.pagination_query( instance, tkt_ajax_params, results.data.max_num_pages, paged_value );
					            
					        },
					        error: function() {
					            $('.tkt_ajax_loader').hide();
					            $( '#' + instance ).html(error);
					        }
					    }); 
		            },
		            error: function() {
		            	$('.tkt_ajax_loader').hide();
		            	$( '#' + instance ).html(error);
		            }
		        });
		    }

			TKT_GLOBAL_NAMESPACE.filter_query(instance, tkt_ajax_params);
	    	TKT_GLOBAL_NAMESPACE.paginate(instance, tkt_ajax_params);
	 
		    /**
		 	 * When changing a Select input, update results on the fly.
		 	 */
		    $('form[data-tkt-ajax-src-form] select').each(function() {
		    	$(this).on('change', function() {
		    		get_form_search_values();
			        TKT_GLOBAL_NAMESPACE.filter_query(instance, tkt_ajax_params); //Load Posts
			    });
		    });
	 
		    /**
		 	 * When typing in an input, update results on the fly.
		 	 */
		    $('form[data-tkt-ajax-src-form] input').each(function() {
		    	$(this).on('keyup', function(e) {
			        if( e.keyCode == 27 ) {
			            $(this).val(''); //If 'escape' was pressed, clear value
			        }
			 		get_form_search_values();
			        TKT_GLOBAL_NAMESPACE.filter_query(instance, tkt_ajax_params); //Load Posts
			    });
		    });
	 
		 	/**
		 	 * When submitting the search button.
		 	 */
		    $('#submit-search').on('click', function(e) {

		        e.preventDefault();

		        get_form_search_values();

		        TKT_GLOBAL_NAMESPACE.filter_query(instance, tkt_ajax_params); //Load Posts
		        
		    });

	    });
	    
	});

})( jQuery );
