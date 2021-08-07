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
		$this->instance = '';

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
	 * The Query Results (non ajax)
	 *
	 * @param mixed $content The ShortCode enclosing content.
	 * @param mixed $error The ShortCode error/nothing found provided by user.
	 */
	public function the_loop( $content, $error ) {

		// Get the Query Results.
		$results = $this->results( $this->get_query_args() );

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
				 * We need to run the content thru ShortCodes Processor, otherwise ShortCodes are not expanded.
				 *
				 * @todo check if we can sanitize the $content here with $content = $this->sanitizer->sanitize( 'post_kses', $content );
				 * @since 2.0.0
				 */
				$processed_content = apply_filters( 'tkt_scs_pre_process_shortcodes', $content );
				$processed_content = do_shortcode( $content, false );
				$out .= $this->sanitizer->sanitize( 'post_kses', $processed_content );
			}
			wp_reset_postdata();
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

		return $out;

	}

	/**
	 * The Query Results (ajax)
	 */
	public function the_ajax_loop() {

		if ( ! is_array( $_GET )
			|| ! isset( $_GET['action'] )
			|| ! isset( $_GET['is_doing_ajax'] )
			|| ! isset( $_GET['content'] )
			|| ! isset( $_GET['instance'] )
			|| ! isset( $_GET['query_args'] )
		) {

			echo json_encode( 'Request is malformed' );

			die();

		}

		$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
		$is_ajax = sanitize_text_field( wp_unslash( $_GET['is_doing_ajax'] ) );
		$content = wp_kses_post( wp_unslash( $_GET['content'] ) );
		$instance = sanitize_text_field( wp_unslash( $_GET['instance'] ) );

		if ( 'the_ajax_loop' !== $action
			|| ! isset( $is_ajax )
		) {

			echo json_encode( 'Request is malformed' );

			die();

		}

		$this->set_type( 'ajax' );
		$this->set_instance( $instance );
		foreach ( $_GET as $key => $value ) {

			if ( 'query_args' !== $key && 'instance' !== $key ) {
				unset( $_GET[ $key ] );
			}
		}

		$this->set_query_args( array() );

		$results = $this->results( $this->get_query_args() );

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
				 * We need to run the content thru ShortCodes Processor, otherwise ShortCodes are not expanded.
				 *
				 * @todo check if we can sanitize the $content here with $content = $this->sanitizer->sanitize( 'post_kses', $content );
				 * @since 2.0.0
				 */
				$processed_content = apply_filters( 'tkt_scs_pre_process_shortcodes', $content );
				$processed_content = do_shortcode( $content, false );
				$out .= $this->sanitizer->sanitize( 'post_kses', $processed_content );
			}
			wp_reset_postdata();
		} else {
			/**
			 * No results found.
			 *
			 * This is already sanitized.
			 *
			 * @since 2.0.0
			 */
			$out = 'no results';
		}

		echo json_encode( $out );

		die();

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
		 * @since 2.0.0\
		 */
		$query_args = $default_query_args;
		$new_query  = array();

		if ( isset( $this->instance )
			&& isset( $_GET )
			&& is_array( $_GET )
			&& ! empty( $_GET )
			&& array_key_exists( 'instance', $_GET )
		) {

			if ( $this->instance === $_GET['instance'] ) {

				unset( $_GET['instance'] );

				if ( 'ajax' === $this->get_type() ) {

					// In ajax requets, the Query is inside the $_GET['query_args'].
					if ( array_key_exists( 'query_args', $_GET ) && is_array( $_GET['query_args'] ) ) {

						/**
						 * Sanitize all $_GET members.
						 *
						 * @since 2.0.0
						 */
						foreach ( $_GET as $get => $query_vars ) {
							foreach ( $query_vars as $query_var => $value ) {
								$query_var = $this->sanitizer->sanitize( 'text_field', $query_var );
								if ( ! is_array( $value ) ) {
									$value = $this->sanitizer->sanitize( 'text_field', $value );
								} else {
									// If an array was passed, such as key[]=value_one,valuetwo.
									foreach ( $value as $skey => $svalue ) {
										$skey = $this->sanitizer->sanitize( 'text_field', $skey );
										$svalue = $this->sanitizer->sanitize( 'text_field', $svalue );
										$value[ $skey ] = $svalue;
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
					foreach ( $_GET as $key => $value ) {

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
							$new_query[ $tkt_src_fltr['searchby'][ $key ] ] = $value;

						}
					}
				}

				// Merge URL query args into default Query Args.
				$query_args = array_merge( $default_query_args, $new_query );
			}
		}

		// Setup Instance WP Query args.
		$this->query_args = $query_args;

	}

	/**
	 * Bootstrap the Loop
	 *
	 * @since   1.0.0
	 * @param   array $query_args        Default Query args passed to the Loop Renderer.
	 * @access  private
	 */
	private function results( $query_args ) {

		return $this->get_query_results();

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

	/**
	 * Get the Query results.
	 *
	 * @since   1.0.0
	 * @return  array $this->query_results   The Query Results.
	 */
	private function get_query_results() {

		$this->query_results = new WP_Query( $this->get_query_args() );

		return $this->query_results;

	}

}
