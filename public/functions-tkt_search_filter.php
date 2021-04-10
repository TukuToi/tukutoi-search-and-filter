<?php

function tkt_search_render($instance, $template = ''){	

	$search = new Tkt_Posts_Query();

	if( empty($template) )
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/tkt_search_filter-form.php';

	$search->render_search($instance, $template);

}

function tkt_results_render($args, $instance, $template = '', $error = ''){

	$results = new Tkt_Posts_Query();
	
	if( empty($template) )
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/tkt_search_filter-post-loop.php';

	if( empty($error) )
		$error = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/tkt_search_filter-no-posts-found.php';

	$results->render_results($args, $instance, $template, $error);

}