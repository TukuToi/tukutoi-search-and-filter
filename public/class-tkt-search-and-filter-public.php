<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/public
 * @author     Your Name <hello@tukutoi.com>
 */
class Tkt_Search_And_Filter_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name      The name of the plugin.
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.9.0
	 */
	public function enqueue_styles() {

		wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), '4.1.0-rc.0', 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.9.0
	 */
	public function enqueue_scripts() {

		/**
		 * Script select2 and tkt_src_fltr_select2 are only needed when loading a Select2 type of search.
		 * They are enqueued and localised in the selectsearch ShortCode on demand.
		 *
		 * Script tkt_src_fltr_query is only needed when the Search/Loop is of AJAX type.
		 * It is enqueued and localised in the loop shortcode on demand.
		 *
		 * Script tkt_src_fltr_pagination is only needed when the Search/Loop is of AJAX type and has pagination.
		 * It is enqueued and localised in the pagination shortcode on demand.
		 *
		 * Script tkt_src_fltr_reset is only needed when there is a Button of Type Reset.
		 * It is enqueued and localised in the buttons shortcode on demand.
		 *
		 * Main script $this->plugin_name is used everywhere in the front end, even if perhaps only needed when
		 * a search or reset button is preset, but the script encompasess both AJAX and Reload functionality,
		 * as well other eventual requirements that have a more global scope, thus we dont load this on demand
		 * but just always and everywhere. The script is tiny, thus this should not be an issue.
		 * If at some point it becomes and issue, we can always load it in the respective ShortCodes.
		 */
		wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), '4.1.0-rc.0', true );
		wp_register_script( $this->plugin_prefix . 'select2', plugin_dir_url( __FILE__ ) . 'js/tkt-search-and-filter-select2.js', array( 'select2' ), $this->version, true );
		wp_register_script( $this->plugin_prefix . 'query', plugin_dir_url( __FILE__ ) . 'js/tkt-search-and-filter-query-loop.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_prefix . 'pagination', plugin_dir_url( __FILE__ ) . 'js/tkt-search-and-filter-pagination.js', array( $this->plugin_prefix . 'query' ), $this->version, true );
		wp_register_script( $this->plugin_prefix . 'reset', plugin_dir_url( __FILE__ ) . 'js/tkt-search-and-filter-reset.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Maybe localise scripts on demand.
	 *
	 * @since    2.9.0
	 * @param string $tag    The Script Tag to localise.
	 * @param string $object The Object to localise.
	 * @param string $item   The Localised Array Key to add.
	 * @param array  $value  The values to add to $item.
	 */
	public function maybe_localize_script( $tag, $object, $item, $value = array() ) {

		// Setup global scripts.
		global $wp_scripts;

		// Get localised script data from global scripts.
		$data = $wp_scripts->get_data( $tag, 'data' );

		// Localise first time if not yet localised.
		if ( empty( $data ) ) {
			wp_localize_script(
				$tag,
				$object,
				array(
					$item => $value,
				)
			);
		} else {
			if ( ! is_array( $data ) ) {
				$data = json_decode( str_replace( 'var ' . $object . ' = ', '', substr( $data, 0, -1 ) ), true );
			}
			$data[ $item ] = $value;
			$wp_scripts->add_data( $tag, 'data', '' );
			wp_localize_script( $tag, $object, $data );
		}

	}

}
