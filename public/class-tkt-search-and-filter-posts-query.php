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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param object $sanitizer The sanitizer object.
	 */
	public function __construct( $sanitizer ) {

		$this->query_args = array();
		$this->sanitizer = $sanitizer;

	}

	/**
	 * Bootstrap the Loop
	 *
	 * @since   1.0.0
	 * @param   array  $default_query_args        Default Query args passed to the Loop Renderer.
	 * @param   string $instance                 The Unique instance of Search and Loop to "connect" them.
	 * @access  public
	 */
	public function results( $default_query_args, $instance ) {

		$this->set_instance( $instance );

		$this->set_query_args( $default_query_args );

		return $this->get_query_results();

	}

	/**
	 * Set The Unique Instance.
	 *
	 * @since   2.0.0
	 * @param   string $instance  The Unique instance of Search and Loop to "connect" them.
	 * @access  private
	 */
	private function set_instance( $instance ) {

		$this->instance = $instance;

	}

	/**
	 * Set the Query Args
	 *
	 * @since   1.0.0
	 * @param   array $default_query_args   Default Query args passed to the Loop Renderer.
	 * @access  private
	 */
	private function set_query_args( $default_query_args ) {

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
						$value = array_map( array( $this->sanitizer, 'sanitize' ), array( 'text_field' ), $value );
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
					if ( array_key_exists( $key, $tkt_src_fltr['searchby'] ) ) {
						// null check.
						$value = empty( $value ) || ! isset( $value ) ? null : $value;
						// boolean check.
						$value = 'true' === $value ? true : ( 'false' === $value ? false : $value );
						// numeric check.
						$value = is_numeric( $value ) ? (int) $value : $value;
						$new_query[ $tkt_src_fltr['searchby'][ $key ] ] = $value;
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
