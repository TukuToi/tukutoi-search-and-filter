<?php
/**
 * The Posts Query Builder
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public
 */

/**
 * The Posts Query Builder
 *
 * Defines all available arguments of the WP_Query
 * Populates those arguments according user settings
 * Passes the WP_Query results 
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_Posts_Query {

	/**
	 * The WP_Query arguments
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $args    All Possible Arguments of the WP_Query.
	 */
	private $query_args;

	/**
	 * The Post Instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $post    The WordPress Post Global.
	 */
	private $post;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {

		global $post;

		$this->query_args = array();
		$this->post = $post;

	}

	private function set_instance( $instance ){
		$this->instance = $instance;
	}

	private function set_query_args($default_query_args){
		
		$query_args = $default_query_args;

		if( isset($_GET) && is_array($_GET) && !empty($_GET) && array_key_exists('instance', $_GET) ){
			if( $this->instance == $_GET['instance'] ){
				unset($_GET['instance']);
				$query_args = array_merge ( $_GET, $default_query_args );
			}
		}

		$this->query_args = $query_args;

	}

	private function get_query_args(){

		return $this->query_args;

	}

	private function get_query_results(){
		
		$query_results = new WP_Query( $this->get_query_args() );

		$this->query_results = $query_results;

	}

	private function maybe_render_results($template, $error){

		if ( $this->query_results->have_posts() ) {

			while ( $this->query_results->have_posts() ) {

				$this->query_results->the_post();
				include( $template );

			}

		} 
		else {
			
			include( $error );
		
		}

		wp_reset_postdata();

	}

	public function render_results($default_query_args, $instance, $template, $error){
		
		$this->set_instance( $instance );

		$this->set_query_args( $default_query_args );

		$this->get_query_results();

		$this->maybe_render_results( $template, $error );

	}

	public function render_search($instance, $template){
		
		$this->set_instance( $instance );

		include( $template );

	}

}