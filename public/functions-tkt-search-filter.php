<?php
/**
 * Public Functions
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public
 */

/**
 * Provide a Method to Render a Search and pass its inputs to the relevant Loop
 *
 * @param string $instance  The unique name of this Search/Loop group.
 * @param string $template  Path to the Template where the Search HTML lives.
 */
function tkt_search_render( $instance, $template = '' ) {

	$search = new Tkt_Posts_Query();

	if ( empty( $template ) ) {
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/tkt-search-filter-form.php';
	}

	$search->render_search( $instance, $template );

}

/**
 * Provide a Method to Render a Loop and get inputs from the relevant search or Query args
 *
 * @param array  $args   Native WP Query arguments.
 * @param string $instance   The unique name of this Search/Loop group.
 * @param string $template  Path to the Template where the Loop HTML lives.
 * @param string $error  Path to the Template where the No results found HTML lives.
 */
function tkt_results_render( $args, $instance, $template = '', $error = '' ) {

	$results = new Tkt_Posts_Query();

	if ( empty( $template ) ) {
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/tkt-search-filter-post-loop.php';
	}

	if ( empty( $error ) ) {
		$error = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/tkt-search-filter-no-posts-found.php';
	}

	$results->render_results( $args, $instance, $template, $error );

}
