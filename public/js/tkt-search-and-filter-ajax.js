(function( $ ) {
	'use strict';

	var inputs = {};
	var selects = {};

	$( document ).ready( function() {
		//Load posts on document ready
	    tkt_get_posts();
	 	
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
	 
	    //If input is changed, load posts
	    // $('#genre-filter input').live('change', function(){
	    //     tkt_get_posts(); //Load Posts
	    // });
	 
	    //Fire ajax request when typing in search
	    // $('#genre-search input.text-search').live('keyup', function(e){
	    //     if( e.keyCode == 27 )
	    //     {
	    //         $(this).val(''); //If 'escape' was pressed, clear value
	    //     }
	 
	    //     tkt_get_posts(); //Load Posts
	    // });
	 
	 	/**
	 	 * When submitting the search
	 	 */
	    $('#submit-search').live('click', function(e){

	        e.preventDefault();

	        get_form_search_values();

	        tkt_get_posts(); //Load Posts
	        
	    });
	    /**
	     * Get all search values of each input of each form.
	     */
	 	function get_form_search_values(){

		 	$('form[data-tkt-ajax-src-form]').each(function(){

			    get_search_values_by_type( $(this).data("tkt-ajax-src-form") );//my_instance

			});

		 }
	    /**
	     * Get all search values of each input by type.
	     */
	    function get_search_values_by_type( form )
	    {	
	        $('form[data-tkt-ajax-src-form="' + form + '"] input').each(function(){

			    inputs[ $(this).attr('data-tkt-ajax-src') ] = $(this).val();

			})
			$('form[data-tkt-ajax-src-form="' + form + '"] select').each(function(){

			    selects[ $(this).attr('data-tkt-ajax-src') ] = $(this).val();
			    
			})
	    }
	 
	    /**
	     * Pagination
	     * 
	     * @todo Not done.
	     */
	    $('.genre-filter-navigation a').live('click', function(e){
	        e.preventDefault();
	 		
	        var url = $(this).attr('href'); //Grab the URL destination as a string
	        var paged = url.split('&paged='); //Split the string at the occurance of &paged=
	 
	        tkt_get_posts(paged[1]); //Load Posts (feed in paged value)
	    });
	 
	    /**
	     * Get the posts with AJAX.
	     */
	    function tkt_get_posts(paged)
	    {
	        var paged_value = paged; //Store the paged value if it's being sent through when the function is called
	        var ajax_url = tkt_ajax_params.ajax_url; //Get ajax url (added through wp_localize_script)
	        var instance = tkt_ajax_params.instance
	        var content = tkt_ajax_params.content
	        var query_args = $.extend(tkt_ajax_params.query_args, selects, inputs)
	        var error = tkt_ajax_params.error
	        var is_doing_ajax = tkt_ajax_params.is_doing_ajax

	        $.ajax({
	            type: 'GET',
	            url: ajax_url,
	            data: {
	            	action: 'the_ajax_loop',
	            	is_doing_ajax: is_doing_ajax,
	                paged: paged_value, //If paged value is being sent through with function call, store here
	                content: content,
	                instance: instance,
	                query_args: query_args,
	            },
	            beforeSend: function ()
	            {	
	                //You could show a loader here
	            },
	            success: function(data)
	            {
	                //Hide loader here
	                $( '#' + instance ).html( JSON.parse(data) );
	            },
	            error: function()
	            {
					//If an ajax error has occured, do something here...
	                $( '#' + instance ).html(error);
	            }
	        });
	    }

	});

})( jQuery );
