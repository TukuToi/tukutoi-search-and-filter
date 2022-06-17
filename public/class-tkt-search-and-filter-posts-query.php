<?php
/**
 * The Posts Query Builder
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/public
 */

/**
 * The Posts Query Builder
 *
 * Defines all available arguments of the WP_Query
 * Populates those arguments according user settings
 * Gets results from the WP_Query
 * Builds the output and loads accurate templates
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_Search_And_Filter_Posts_Query {

	/**
	 * The WP_Query arguments
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $args    All Possible Arguments of the WP_Query.
	 */
	private $query_args;

	/**
	 * The Query Results
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $query_results The Results of the WP_Query.
	 */
	private $query_results;

	/**
	 * The Search and Results Instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $instance Unique instance to bind Search and Results.
	 */
	private $instance;

	/**
	 * The Type of query
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $type The Type of query - AJAX or Reload.
	 */
	private $type;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param object $sanitizer The sanitizer object.
	 */
	public function __construct( $sanitizer ) {

		$this->query_args = array();
		$this->sanitizer = $sanitizer;
		$this->instance = '';
		$this->content = '';

	}

	/**
	 * Set The Unique Instance.
	 *
	 * @since   2.0.0
	 * @param   string $instance  The Unique instance of Search and Loop to "connect" them.
	 * @access  private
	 */
	public function set_instance( $instance ) {

		$this->instance = $instance;

	}

	/**
	 * Set the type of Query request.
	 *
	 * Only used to enqueue scripts conditionally.
	 *
	 * @param string $type ajax|''.
	 * @since 2.11.0
	 */
	public function set_type( $type ) {

		$this->type = $type;

	}

	/**
	 * Set the custom Pagination arg.
	 *
	 * @since   1.0.0
	 * @param string $pagarg The Custom pagination argument.
	 * @return  void.
	 */
	public function set_custom_pagarg( $pagarg ) {

		$this->custom_pagarg = $pagarg;

	}

	/**
	 * Get the type of Query request.
	 *
	 * Only used to enqueue scripts conditionally.
	 *
	 * @since 2.11.0
	 */
	public function get_type() {

		return $this->type;

	}

	/**
	 * Get the Query results.
	 *
	 * @since   1.0.0
	 * @return  array $this->query_results   The Query Results.
	 */
	public function get_query_results() {

		$this->query_results = new WP_Query( $this->get_query_args() );

		return $this->query_results;

	}

	/**
	 * The Query Results (non ajax)
	 *
	 * @param mixed $content The ShortCode enclosing content.
	 * @param mixed $error The ShortCode error/nothing found provided by user.
	 */
	public function the_loop( $content, $error ) {

		// Get the Query Results.
		$results = $this->get_query_results();

		/**
		 * Loop over the results and build the output.
		 *
		 * @since 2.0.0
		 */
		$out = '';
		if ( $results->have_posts() ) {
			while ( $results->have_posts() ) {
				$results->the_post();
				/**
				 * Expand ShortCodes inside a Looop.
				 *
				 * All Loops are base64 encoded so WordPress does not mess with our content.
				 * We decode this in Tkt_Shortcodes_Processor->post_process_shortcodes().
				 * Once decoded, it will resolve the inner shortcodes (used as attributes to other ShortCodes)
				 * using Tkt_Shortcodes_Processor->resolve_inner_shortcodes( $content )
				 * Then, it resolves ShortCodes inside HTML attributes (which are at this points still using {{shortcode}})
				 *
				 * Since the content returned from there still has ShortCodes inside, which are normally expandable by WordPress
				 * we pass the processed content thru do_shortcode.
				 *
				 * Only then the output of a loop is fully expanded and can be passed to the Loop shortcode to be returned.
				 *
				 * @since 2.0.0
				 */
				$processed_content = apply_filters( 'tkt_post_process_shortcodes', $content );
				$processed_content = do_shortcode( $processed_content, false );
				$out .= stripslashes_deep( $this->sanitizer->sanitize( 'post_kses', $processed_content ) );
			}
		} else {
			/**
			 * No results found.
			 *
			 * This is already sanitized.
			 *
			 * @since 2.0.0
			 */
			$out = $error;
		}

		/**
		 * Normally here we would reset post data.
		 *
		 * @todo check if this is needed, specially when no pagination is on site
		 */
		wp_reset_postdata();

		return $out;

	}

	/**
	 * Build the Loop Results in AJAX request.
	 *
	 * We use a POST request because we need to resolve the template.
	 * The Template might hold ShortCodes (nested/attributes) and even be encoded.
	 * Thus we cannot use a GET request because the Request URI might become too long very quickly.
	 *
	 * @since 2.19.0
	 */
	public function tkt_ajax_loop() {

		if ( ! is_array( $_POST )
			|| empty( $_POST )
			|| ! isset( $_POST['action'] )
			|| empty( $_POST['action'] )
			|| ! isset( $_POST['nonce'] )
			|| empty( $_POST['nonce'] )
			|| ! isset( $_POST['is_doing_ajax'] )
			|| empty( $_POST['is_doing_ajax'] )
			|| ! isset( $_POST['instance'] )
			|| empty( $_POST['instance'] )
			|| ! isset( $_POST['error'] ) // Error might be empty.
			|| ! isset( $_POST['template'] )
			|| empty( $_POST['template'] )
			|| ! isset( $_POST['objects'] ) // Objects might be empty if no results found.
		) {

			wp_send_json_error( 'Insufficient or Malformed Request', 400 );

		}

		$action     = sanitize_text_field( wp_unslash( $_POST['action'] ) );
		$is_ajax    = rest_sanitize_boolean( sanitize_text_field( wp_unslash( $_POST['is_doing_ajax'] ) ) );

		if ( 'tkt_ajax_loop' !== $action
			|| ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'tkt_ajax_nonce' )
			|| true !== $is_ajax
		) {

			wp_send_json_error( 'Unauthorized Request', 401 );

		}

		$instance   = sanitize_text_field( wp_unslash( $_POST['instance'] ) );
		$template   = wp_kses_post( wp_unslash( $_POST['template'] ) );
		$objects    = array_map( 'absint', wp_unslash( $_POST['objects'] ) );

		$out = '';
		foreach ( $objects as $key => $post_id ) {

			global $post;
			/**
			 * Yes, we shouldn't override WP Globals...
			 * Yet we have not much other choice, and we reset it a few lines after
			 *
			 * @todo check if we could do $custom_post = $post, then work with $custom_post instead.
			 */
			$post = get_post( $post_id );// @codingStandardsIgnoreLine

			setup_postdata( $post );
			$processed_content = apply_filters( 'tkt_post_process_shortcodes', $template );
			$processed_content = do_shortcode( $processed_content, false );
			$out .= stripslashes_deep( $this->sanitizer->sanitize( 'post_kses', $processed_content ) );
			wp_reset_postdata();

		}

		wp_send_json_success( $out );

	}

	/**
	 * The Query Results (ajax)
	 */
	public function tkt_ajax_query() {

		if ( ! is_array( $_GET )
			|| empty( $_GET )
			|| ! isset( $_GET['action'] )
			|| empty( $_GET['action'] )
			|| ! isset( $_GET['nonce'] )
			|| empty( $_GET['nonce'] )
			|| ! isset( $_GET['is_doing_ajax'] )
			|| empty( $_GET['is_doing_ajax'] )
			|| ! isset( $_GET['instance'] )
			|| empty( $_GET['instance'] )
			|| ! isset( $_GET['query_args'] )
			|| empty( $_GET['query_args'] )
		) {

			wp_send_json_error( 'Insufficient GET Request Data', 400 );

		}

		$action     = sanitize_text_field( wp_unslash( $_GET['action'] ) );
		$is_ajax    = rest_sanitize_boolean( sanitize_text_field( wp_unslash( $_GET['is_doing_ajax'] ) ) );

		if ( 'tkt_ajax_query' !== $action
			|| ! wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'tkt_ajax_nonce' )
			|| true !== $is_ajax
		) {

			wp_send_json_error( 'Unauthorized Request', 401 );

		}

		$instance   = sanitize_text_field( wp_unslash( $_GET['instance'] ) );

		$this->set_type( 'ajax' );
		$this->set_instance( $instance );

		if ( isset( $_GET['paged'] ) && isset( $_GET['query_args']['posts_per_page'] ) ) {
			$this->paged            = absint( wp_unslash( $_GET['paged'] ) );
			$this->posts_per_page   = absint( wp_unslash( $_GET['query_args']['posts_per_page'] ) );
		}
		foreach ( $_GET as $key => $value ) {
			if ( 'query_args' !== $key && 'instance' !== $key ) {
				unset( $_GET[ $key ] );
			}
		}
		$this->set_query_args( array() );

		$results = $this->get_query_results();

		/**
		 * It would be quicker to just send fields => ids
		 * however we need some additional data from WP Query in the AJAX object.
		 * This might be useful as well in future to load found results, etc.
		 */
		wp_send_json_success(
			array(
				'max_num_pages' => $results->max_num_pages,
				'ids'           => wp_list_pluck( $results->posts, 'ID' ),
			)
		);

	}

	/**
	 * Set the Query Args
	 *
	 * @since   1.0.0
	 * @param   array $default_query_args   Default Query args passed to the Loop Renderer.
	 * @access  public
	 */
	public function set_query_args( $default_query_args ) {

		/**
		 * Global used to tag the current instance and map search URL parameters to search Query parameters.
		 *
		 * @since 2.0.0
		 */
		global $tkt_src_fltr;
		/**
		 * Map our URL parameters to the default query args and build the final args to pass to WP Query.
		 *
		 * Reviewers:
		 * It makes no sense to check a nonce on a front end Seearch Input form.
		 * The user could copy paste an old URL with Query parameters, and if visited,
		 * that search would have an invalid nonce at this point.
		 * Also, we do not perform any CRUD action here, just a GET of data using WP Query.
		 * All possible parameters are sanitized before passing to Query.
		 * Also note that if the Query is performed with AJAX, the GET is actually nonced
		 * (because there's no URL query parameters in this case).
		 *
		 * @since 2.0.0
		 */
		$query_args = $default_query_args;
		$new_query  = array();

		if ( isset( $this->instance )
			&& isset( $_GET )// @codingStandardsIgnoreLine
			&& is_array( $_GET )// @codingStandardsIgnoreLine
			&& ! empty( $_GET )// @codingStandardsIgnoreLine
			&& array_key_exists( 'instance', $_GET )// @codingStandardsIgnoreLine
		) {

			if ( $this->instance === $_GET['instance'] ) {// @codingStandardsIgnoreLine

				unset( $_GET['instance'] );// @codingStandardsIgnoreLine

				if ( 'ajax' === $this->get_type() ) {
					// In ajax requets, the Query is inside the $_GET['query_args'].
					if ( array_key_exists( 'query_args', $_GET ) && is_array( $_GET['query_args'] ) ) {// @codingStandardsIgnoreLine

						/**
						 * Sanitize all $_GET members.
						 *
						 * @since 2.0.0
						 */
						foreach ( $_GET as $get => $query_vars ) {// @codingStandardsIgnoreLine
							foreach ( $query_vars as $query_var => $value ) {
								$query_var = $this->sanitizer->sanitize( 'text_field', $query_var );
								if ( ! is_array( $value ) ) {
									$value = $this->sanitizer->sanitize( 'text_field', $value );
								} else {
									// If an array was passed, such as key[]=value_one,valuetwo.
									foreach ( $value as $skey => $svalue ) {
										if ( ! is_array( $svalue ) ) { // term queryes have arrays here.
											$skey = $this->sanitizer->sanitize( 'text_field', $skey );
											$svalue = $this->sanitizer->sanitize( 'text_field', $svalue );
											$value[ $skey ] = $svalue;
										} else { // this is a term query.
											if ( ! empty( $svalue['terms'] ) ) {
												foreach ( $svalue as $tax_q_key => $tax_q_value ) {
													$tax_q_key = $this->sanitizer->sanitize( 'text_field', $tax_q_key );
													if ( ! is_array( $tax_q_value ) ) { // the 'terms' value itself is another array.
														$tax_q_value = $this->sanitizer->sanitize( 'text_field', $tax_q_value );
													} else {
														foreach ( $tax_q_value as $term_key => $term_value ) {
															$term_value = $this->sanitizer->sanitize( 'text_field', $term_value );
															$tax_q_value[ $term_key ] = $term_value;
														}
													}
													$svalue[ $tax_q_key ] = $tax_q_value;
												}
												$value[ $skey ] = $svalue;
											}
										}
									}
								}
								// null check.
								$value = empty( $value ) || ! isset( $value ) ? null : $value;
								// boolean check.
								$value = 'true' === $value ? true : ( 'false' === $value ? false : $value );
								// numeric check.
								$value = is_numeric( $value ) ? (int) $value : $value;

								$new_query[ $query_var ] = $value;
							}
						}
					}
				} else {
					// This is not an AJAX query.
					foreach ( $_GET as $key => $value ) {// @codingStandardsIgnoreLine
						/**
						 * Sanitize the URL GET Inputs.
						 *
						 * @since 2.0.0
						 */
						$key = $this->sanitizer->sanitize( 'text_field', $key );
						if ( ! is_array( $value ) ) {
							// If just one value was added to $key, like key=val.
							$value = $this->sanitizer->sanitize( 'text_field', $value );
						} elseif ( is_array( $value ) ) {
							// If an array was passed, such as key[]=value_one,valuetwo.
							foreach ( $value as $skey => $svalue ) {
								$skey = $this->sanitizer->sanitize( 'text_field', $skey );
								$svalue = $this->sanitizer->sanitize( 'text_field', $svalue );
								$value[ $skey ] = $svalue;
							}
						}

						/**
						 * Set the new URL Parms to query args.
						 *
						 * We have to map the URL param to the real QP Query arg.
						 *
						 * Additionally check for numbers and cast those.
						 *
						 * Only add if key (url param) exists.
						 *
						 * @since 2.0.0
						 */
						if ( ! is_null( $tkt_src_fltr )
						&& ! empty( $tkt_src_fltr )
						&& isset( $tkt_src_fltr['searchby'] )
						&& array_key_exists( $key, $tkt_src_fltr['searchby'] )
						) {
							// null check.
							$value = empty( $value ) || ! isset( $value ) ? null : $value;
							// boolean check.
							$value = 'true' === $value ? true : ( 'false' === $value ? false : $value );
							// numeric check.
							$value = is_numeric( $value ) ? (int) $value : $value;

							/**
							 * If a Custom Taxonomy Filter is added, we need to build an array.
							 * We already have the taxonomy and the fields to query, not the terms.
							 * We append those to the array fromt he GET.
							 *
							 * @since 2.29.0
							 */
							if ( array_key_exists( 'tax_query', $tkt_src_fltr['searchby'] ) ) {

								foreach ( $tkt_src_fltr['searchby']['tax_query'] as $tax_k => $tax_arr ) {

									if ( $key === $tax_arr['url_param'] ) {
										$tax_arr['terms'] = $value;
									}
									$tkt_src_fltr['searchby']['tax_query'][ $tax_k ] = $tax_arr;

								}

								$keys_t = array_keys( $tkt_src_fltr['searchby'], 'tax_query' );

								if ( in_array( $key, $keys_t ) ) {
									$value = $tkt_src_fltr['searchby']['tax_query'];
								}
							}

							$new_query[ $tkt_src_fltr['searchby'][ $key ] ] = $value;

						}
					}
				}
				// Merge URL query args into default Query Args.
				$query_args = array_merge( $default_query_args, $new_query );

				/**
				 * Remove empty Taxonomy Query Parameters.
				 * 
				 * @since 2.29.0
				 */
				if ( isset( $query_args['tax_query'] ) ) {
					foreach ( $query_args['tax_query'] as $key => $value ) {
						if ( ! array_key_exists( 'terms', $value ) ) {
							unset( $query_args['tax_query'][ $key ] );
						}
					}
				}
			}
		}

		/**
		 * We check if it is paginated.
		 *
		 * Remember that we only support location/?pagination_parameter=#
		 * We do not support location/page/# or location/# because this requires using reserved WP/CP pagination terms
		 * You can use those words but only if using as a URL parameter.
		 *
		 * In WordPress, there is a massive confusion about how pagination links and URLs work.
		 * But as far I was able to dig out, you would use /?paged=# if using ugly permalinks.
		 * This then gets rewritten to location/page/# if you use pretty permalinks with above format.
		 *
		 * Instead using ?page=# would produce location/#, but again this will produce unexpected issues.
		 * The unexpected issues are because apart of the permalink difference, WP also uses those paramaters for actual
		 * in page pagination. And it does all of that "automatically", which means it does just force the URL.
		 *
		 * For this reason, you can have all of above in ONE query on the same page, but as soon the same page features
		 * more than one query with pagination, the mess starts. This is logicaly because location/page/# or similar are
		 * rewritten by WP and affect the oen main query on a specific location, whereas an URL parameter can
		 * pass MORE THAN ONE pagination arguments
		 * I think this is pretty clear with this and justifies that due to the mess, desinformation
		 * (in docs, trac and forums), we can safely say
		 * "TukuToi Search & Filter will NEVER support any such pagination structure".
		 *
		 * @since 2.13.0
		 */
		if ( 'ajax' !== $this->get_type() ) {
			$this->paged = isset( $_GET[ $this->custom_pagarg ] ) ? absint( wp_unslash( $_GET[ $this->custom_pagarg ] ) ) : 1;// @codingStandardsIgnoreLine
		}
		if ( isset( $this->paged )
			&& ! empty( $this->paged )
		) {
			$query_args = wp_parse_args(
				array(
					'paged' => $this->paged,
				),
				$query_args
			);
		}

		/**
		 * Allow this Query to be filterd.
		 *
		 * Other plugins or users might want to alter the Query programmatically.
		 * For example, to exclude some posts in a Certain Category, you can use this filter.
		 *
		 * @since 2.20.3
		 * @param array $query_args {
		 *      The query arguments of the WP Query. Default: WP Query Args passed to the Search and Filter instance. Accepts: valid WP Query arguments.
		 * }
		 * @param string $this->instance The current Instance.
		 */
		$query_args = apply_filters( 'tkt_src_fltr_query_args', $query_args, $this->instance );
		$this->query_args = $query_args;

	}

	/**
	 * Set the custom posts per page arg.
	 *
	 * @since   1.0.0
	 * @param string $posts_per_page The Custom posts per page argument.
	 * @return  void.
	 */
	private function set_posts_per_page( $posts_per_page ) {

		$this->posts_per_page = $posts_per_page;

	}

	/**
	 * Get the Query Args
	 *
	 * @since    1.0.0
	 * @return  array $this->query_args   The merged query args.
	 */
	private function get_query_args() {

		return $this->query_args;

	}

}
