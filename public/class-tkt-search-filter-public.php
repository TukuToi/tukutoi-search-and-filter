<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_Search_Filter_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The Human name of this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $human_plugin_name    The human plugin name.
	 */
	private $human_plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 * @param      string $human_plugin_name    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $human_plugin_name ) {

		$this->plugin_name = $plugin_name;
		$this->human_plugin_name;
		$this->version = $version;

		$this->load_dependencies();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Require the Posts Query Class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt-posts-query.php';

		/**
		 * Require the Public API Functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/functions-tkt-search-filter.php';

		/**
		 * Require the ShortCodes File
		 * 
		 * @since 1.1.1
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shortcodes-tkt-search-filter.php';

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-styles', plugin_dir_url( __FILE__ ) . 'css/tkt_search_filter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . '-scripts', plugin_dir_url( __FILE__ ) . 'js/tkt_search_filter-public.js', array( 'jquery' ), $this->version, true );

	}



}
