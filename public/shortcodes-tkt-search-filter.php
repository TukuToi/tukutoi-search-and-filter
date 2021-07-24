<?php
/**
 * This file registers all TukuToi Search And Filter ShortCodes.
 *
 * @since 1.1.1
 * @package Tkt_Search_Filter
 * @subpackage Tkt_Search_Filter/public
 */

/**
 * Register all ShortCodes
 *
 * @since 1.1.1
 */
add_shortcode( 'tkt_search', 'tkt_search_render_shortcode' );
add_shortcode( 'tkt_loop', 'tkt_results_render_shortcode' );

/**
 * Render Search ShortCode
 *
 * @since 1.0.1
 * @param array $attr ShortCode Attributes.
 * @return string $search The Search Template HTML.
 */
function tkt_search_render_shortcode( $attr ) {

	$atts = shortcode_atts(
		array(
			'instance' => 'unique-instance-name',
			'template' => '',
		),
		$atts,
		'tkt_search'
	);

	$search = tkt_search_render( $atts['instance'], $atts['template'] );

	return $search;

}

/**
 * Render Search Results (loop)
 *
 * @since 1.1.1
 * @param array $attr ShortCode Attributes.
 * @return string $loop The Search Results rendered with Results Template.
 */
function tkt_results_render_shortcode( $attr ) {

	$atts = shortcode_atts(
		array(
			'args'     => array(
				'post_type'              => array( 'post' ),
				'post_status'            => array( 'publish' ),
				'posts_per_page'         => -1,
				'order'                  => 'DESC',
				'orderby'                => 'date',
				'cache_results'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true,
			),
			'instance' => 'unique-instance-name',
			'template' => '',
			'error'    => 'No Results Found',
		),
		$atts,
		'tkt_loop'
	);

	$loop = tkt_results_render( $atts['args'], $atts['instance'], $atts['template'], $atts['error'] );

	return $loop;

}
