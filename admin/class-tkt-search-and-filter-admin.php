<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/admin
 * @author     Your Name <hello@tukutoi.com>
 */
class Tkt_Search_And_Filter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The declarations object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $declarations    The declarations object with all shortcodes and data maps.
	 */
	private $declarations;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 * @param      object $declarations    The declarations object.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version, $declarations ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;
		$this->declarations = $declarations;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tkt-search-and-filter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Add ShortCodes to the GUI.
	 *
	 * This happens only if TukuToi ShortCodes is active.
	 *
	 * @since    1.0.0
	 * @param string $file The filepath to the ShortCode GUI Form.
	 * @param string $shortcode The ShortCode tag for which we add the GUI Form.
	 */
	public function add_shortcodes_to_gui( $file, $shortcode ) {

		if ( array_key_exists( $shortcode, $this->declarations->shortcodes ) ) {
			$file = plugin_dir_path( __FILE__ ) . 'partials/tkt-search-and-filter-' . $shortcode . '-form.php';
		}

		return $file;

	}

}
