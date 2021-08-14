(function( $ ) {
	'use strict';

	var inputs = {};
	var selects = {};
	var paged_value, paged, url, ajax_url, instance, content, query_args, error, is_doing_ajax, max;


	$( document ).ready( function() {
		
		/**
	 	 * On Document ready load posts, and pagination, if set.
	 	 */
	    tkt_get_posts();
	    tkt_paginate();
	 	
	    //If list item is clicked, trigger input change and add css class
	    // $('#genre-filter li').live('click', function(){
	    //     var input = $(this).find('input');
	 
	    //             //Check if clear all was clicked
	    //     if ( $(this).attr('class') == 'clear-all' )
	    //     {
	    //         $('#genre-filter li').removeClass('selected').find('input').prop('checked',false); //Clear settings
	    //         tkt_get_posts(); //Load Posts
	    //     }
	    //     else if (input.is(':checked'))
	    //     {
	    //         input.prop('checked', false);
	    //         $(this).removeClass('selected');
	    //     } else {
	    //         input.prop('checked', true);
	    //         $(this).addClass('selected');
	    //     }
	 
	    //     input.trigger("change");
	    // });
	 
	    /**
	 	 * When changing a Select input, update results on the fly.
	 	 */
	    $('form[data-tkt-ajax-src-form] select').each(function() {
	    	$(this).on('change', function() {
	    		get_form_search_values();
		        tkt_get_posts(); //Load Posts
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
		        tkt_get_posts(); //Load Posts
		    });
	    });
	 
	 	/**
	 	 * When submitting the search button.
	 	 */
	    $('#submit-search').on('click', function(e) {

	        e.preventDefault();

	        get_form_search_values();

	        tkt_get_posts(); //Load Posts
	        
	    });

	    /**
	     * Get all search values of each input of each form.
	     */
	 	function get_form_search_values() {

		 	$('form[data-tkt-ajax-src-form]').each(function(){

			    get_search_values_by_type( $(this).data("tkt-ajax-src-form") );//my_instance

			});

		 }

	    /**
	     * Get all search values of each input by type.
	     */
	    function get_search_values_by_type( form ) {	
	        $('form[data-tkt-ajax-src-form="' + form + '"] input').each(function(){

			    inputs[ $(this).attr('data-tkt-ajax-src') ] = $(this).val();

			})
			$('form[data-tkt-ajax-src-form="' + form + '"] select').each(function(){

			    selects[ $(this).attr('data-tkt-ajax-src') ] = $(this).val();
			    
			})
	    }
	 
	    /**
	     * Pagination
	     */
	    function tkt_paginate() {
	    	$( '#' + tkt_ajax_params.instance + '_pagination' + ' a').each(function(){

				$(this).on('click', function(e){
			        e.preventDefault();

			        url = $(this).attr('href'); //Grab the URL destination as a string
			        paged = url.split( '?' + tkt_ajax_params.query_args.pag_arg + '=' ); //Split the string at the occurance of &paged=
			        if( 'undefined' !== typeof paged[1] ){
			        	paged = paged[1].split( '&' ); //Split the string at the occurance of &paged=
			        } else {
			        	paged = 1;
			        	tkt_get_posts(paged);
			        }
			        tkt_get_posts(paged[0]); //Load Posts (feed in paged value)
		    	});

			});
	    }
	 	
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
	    function tkt_get_posts( paged ) {	

	        paged_value 	= paged; //Store the paged value if it's being sent through when the function is called
	        ajax_url 		= tkt_ajax_params.ajax_url; //Get ajax url (added through wp_localize_script)
	        instance 		= tkt_ajax_params.instance;
	        content 		= tkt_ajax_params.content;
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
	            	max = results.data.max_num_pages;

	                $.ajax({
	                	type: 'POST',
	                	url: ajax_url,
	                	data: {
	                		action: 'tkt_ajax_loop',
	                		nonce: tkt_ajax_params.nonce,
	                		is_doing_ajax: is_doing_ajax,
	                		template: content,
	                		objects: results.data.ids,
	                		instance: instance,
	                		error: error,
	                	},
				        beforeSend: function () {	
				        },
				        success: function( layout ){
				        	$('.tkt_ajax_loader').hide();
				            $( '#' + instance ).html( layout.data );

				            $.ajax({
			                	type: 'POST',
			                	url: ajax_url,
			                	data: {
			                		action: 'tkt_ajax_pagination',
			                		is_doing_ajax: true,
									ajax_url: tkt_ajax_pag_params.ajax_url,
									nonce: tkt_ajax_pag_params.nonce,
									instance: tkt_ajax_pag_params.instance,
									atts: tkt_ajax_pag_params.atts,
									page: paged_value,
									paged: query_args.pag_arg,
									add_args: tkt_ajax_pag_params.add_args,
									max: max,
			                	},
						        beforeSend: function () {	
						        	$('.tkt_ajax_loader').show();
						        },
						        success: function( pagination ){
						        	$('.tkt_ajax_loader').hide();
						            $(  '#' + instance + '_pagination' ).html(pagination.data);
						            tkt_paginate();
						        },
						        error: function() {
						        	$('.tkt_ajax_loader').hide();
						        }
						    }); 
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
	    
	});

})( jQuery );
