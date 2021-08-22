<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/includes
 * @author     Your Name <hello@tukutoi.com>
 */
class Tkt_Search_And_Filter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tkt_Search_And_Filter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	protected $plugin_prefix;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'TKT_SEARCH_AND_FILTER_VERSION' ) ) {

			$this->version = TKT_SEARCH_AND_FILTER_VERSION;

		} else {

			$this->version = '1.0.0';

		}

		$this->plugin_name = 'tkt-search-and-filter';
		$this->plugin_prefix = 'tkt_src_fltr_';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tkt_Search_And_Filter_Loader. Orchestrates the hooks of the plugin.
	 * - Tkt_Search_And_Filter_i18n. Defines internationalization functionality.
	 * - Tkt_Search_And_Filter_Declarations. Declares all ShortCode and Data names => labels.
	 * - Tkt_Search_And_Filter_Sanitizer. Maintains all Sanitization, Validation and Error handling.
	 * - Tkt_Search_And_Filter_Admin. Defines all hooks for the admin area.
	 * - Tkt_Search_And_Filter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * WordPress / ClassicPress are great toolset but something is wrong about them. This file provides
		 * fixes for those wrongdoings.
		 * `worcpress` stands for `WORd- and ClassicPRESSS`
		 *
		 * @todo move this to actual core.
		 * @since 2.0.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tkt-search-and-filter-fix-worcpress.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tkt-search-and-filter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tkt-search-and-filter-i18n.php';

		/**
		 * The class responsible for declaring all ShortCodes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tkt-search-and-filter-declarations.php';

		/**
		 * The class responsible for Sanitizing and Validating inputs.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tkt-search-and-filter-sanitizer.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tkt-search-and-filter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt-search-and-filter-public.php';

		/**
		 * The class responsible to load all common code
		 *
		 * NOTE: Loaded only once.
		 */
		if ( ! defined( 'TKT_COMMON_LOADED' ) ) {
			require_once( plugin_dir_path( dirname( __FILE__ ) ) . '/common/class-tkt-common.php' );
		}
		$this->common = Tkt_Common::get_instance();

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		$this->loader = new Tkt_Search_And_Filter_Loader();

		/**
		 * The class responsible for maintaining a list of Declarations of this plugin.
		 *
		 * @see {/includes/class-tkt-search-and-filter-declarations.php}.
		 */
		$this->declarations = new Tkt_Search_And_Filter_Declarations( $this->plugin_prefix, $this->version );

		/**
		 * Register ShortCodes with the TukuToi ShortCodes Plugin API.
		 *
		 * NOTE: ShortCodes are effectively ADDED only in the frontend, but we need their declaration in the backend as well.
		 *
		 * @since 2.0.0
		 */
		$this->loader->add_filter( 'tkt_scs_register_shortcode', $this->declarations, 'declare_shortcodes_add_filter' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tkt_Search_And_Filter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tkt_Search_And_Filter_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}



	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		if ( is_admin()
			&& ( current_user_can( 'manage_options' )
				|| current_user_can( 'manage_network_options' )
			)
			&& ! is_customize_preview()
		) {

			/**
			 * The class responsible for orchestrating admin facing stuff.
			 *
			 * @since 1.0.0
			 */
			$plugin_admin = new Tkt_Search_And_Filter_Admin( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version(), $this->declarations );

			/**
			 * Enqueue Scripts and Styles.
			 *
			 * @since 1.0.0
			 */
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

			/**
			 * Register ShortCode Types with the TukuToi ShortCodes Plugin API.
			 *
			 * @since 1.0.0
			 */
			$this->loader->add_filter( 'tkt_scs_register_shortcode_type', $this->declarations, 'declare_shortcodes_types_add_filter' );

			/**
			 * Add ShortCodes to the TukuToi ShortCodes Plugin GUI.
			 *
			 * @since 1.0.0
			 */
			foreach ( $this->declarations->shortcodes as $shortcode => $array ) {

				$this->loader->add_filter( "tkt_scs_{$shortcode}_shortcode_form_gui", $plugin_admin, 'add_shortcodes_to_gui', 10, 2 );

			}
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		if ( ! is_admin()
			&& ! is_customize_preview()
			|| ( is_admin()
				&& wp_doing_ajax()
				&& ! is_customize_preview()
			)
		) {

			/**
			 * The class responsible for orchestrating public facing stuff.
			 *
			 * @since 1.0.0
			 */
			$plugin_public = new Tkt_Search_And_Filter_Public( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version() );

			/**
			 * The class responsible for Sanitization.
			 *
			 * @since 1.0.0
			 */
			$sanitizer = new Tkt_Search_And_Filter_Sanitizer( $this->plugin_prefix, $this->version, $this->declarations );

			/**
			 * The class responsible for processing the queries.
			 *
			 * @since 2.0.0
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt-search-and-filter-posts-query.php';
			$query = new Tkt_Search_And_Filter_Posts_Query( $sanitizer );

			/**
			 * The class responsible for processing ShortCodes in ShortCodes or attributes.
			 *
			 * @since 2.0.0
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tkt-search-and-filter-shortcodes.php';
			$shortcodes = new Tkt_Search_And_Filter_Shortcodes( $this->plugin_prefix, $this->version, $this->declarations, $query, $sanitizer, $plugin_public );

			/**
			 * The ShortCode Processor making nested and attribute ShortCodes work.
			 *
			 * Also removes WP Autop from content and excerpts.
			 */
			$processor = new Tkt_Shortcodes_Processor( $this->plugin_prefix, $this->version, $this->declarations );
			$this->loader->add_filter( 'the_content', $processor, 'pre_process_shortcodes', 5 );
			$this->loader->add_filter( 'tkt_post_process_shortcodes', $processor, 'post_process_shortcodes' );

			/**
			 * Enqueue Scripts and Styles.
			 */
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			/**
			 * Register AJAX Callbacks.
			 */
			$this->loader->add_action( 'wp_ajax_tkt_ajax_loop', $query, 'tkt_ajax_loop' );
			$this->loader->add_action( 'wp_ajax_nopriv_tkt_ajax_loop', $query, 'tkt_ajax_loop' );
			$this->loader->add_action( 'wp_ajax_tkt_ajax_query', $query, 'tkt_ajax_query' );
			$this->loader->add_action( 'wp_ajax_nopriv_tkt_ajax_query', $query, 'tkt_ajax_query' );
			$this->loader->add_action( 'wp_ajax_tkt_ajax_pagination', $shortcodes, 'tkt_ajax_pagination' );
			$this->loader->add_action( 'wp_ajax_nopriv_tkt_ajax_pagination', $shortcodes, 'tkt_ajax_pagination' );

			/**
			 * Register AJAX Callbacks.
			 *
			 * NOTE: They will be registered with tkt_scs_ prefixed handle (prefix of TukuToi ShortCodes Plugin).
			 */
			foreach ( $this->declarations->shortcodes as $shortcode => $array ) {

				$callback = $shortcode;
				if ( method_exists( $shortcodes, $callback ) ) {

					$this->loader->add_shortcode( 'tkt_scs_' . $shortcode, $shortcodes, $callback );

				}
			}
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The unique prefix of the plugin used to uniquely prefix technical functions.
	 *
	 * @since     1.0.0
	 * @return    string    The prefix of the plugin.
	 */
	public function get_plugin_prefix() {
		return $this->plugin_prefix;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tkt_Search_And_Filter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
