<?php
/**
 * The Declarations File of this Plugin.
 *
 * Registers an array of ShortCodes with localised labels,
 * as well maintains a list of arrays containing object properties and array members
 * which are used allover this plugin, and a list of all sanitization options, plus their callbacks.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/includes
 */

/**
 * The Declarations Class.
 *
 * This is used both in public and admin when we need an instance of all shortcodes,
 * or a centrally managed list of object properties or array members where we cannot already
 * get it from the code (such as user object, which is a entangled mess, or get_bloginfo which is a case switcher).
 *
 * @since      1.0.0
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/includes
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tkt_Search_And_Filter_Declarations {

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
	 * The ShortCodes of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $shortcodes    All ShortCode tags, methods and labels of this plugin.
	 */
	public $shortcodes;

	/**
	 * The Sanitization options and callbacks.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $sanitization_options    All Sanitization Options of this plugin and their callbacks.
	 */
	public $sanitization_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct( $plugin_prefix, $version ) {

		$this->plugin_prefix    = $plugin_prefix;
		$this->version          = $version;
		$this->shortcodes       = $this->declare_shortcodes();
		$this->sanitization_options = $this->sanitize_options();

	}

	/**
	 * Register an array of Shortcodes of this plugin
	 *
	 * Multidimensional array keyed by ShortCode tagname,
	 * each holding an array of ShortCode data:
	 * - Label
	 * - Type
	 *
	 * @since 1.0.0
	 * @return array $shortcodes The ShortCodes array.
	 */
	private function declare_shortcodes() {

		$shortcodes = array(
			'searchtemplate' => array(
				'label' => esc_html__( 'Search Form', 'tkt-search-and-filter' ),
				'type'  => 'queryable',
			),
			'loop' => array(
				'label' => esc_html__( 'Search Results', 'tkt-search-and-filter' ),
				'type'  => 'queryable',
			),
			'textsearch' => array(
				'label' => esc_html__( 'Text Search', 'tkt-search-and-filter' ),
				'type'  => 'queryable',
			),
			'selectsearch' => array(
				'label' => esc_html__( 'Select Search', 'tkt-search-and-filter' ),
				'type'  => 'queryable',
			),
			'buttons' => array(
				'label' => esc_html__( 'Buttons', 'tkt-search-and-filter' ),
				'type'  => 'queryable',
			),
		);

		return $shortcodes;

	}

	/**
	 * Register an array of object properties, array members to re-use as configurations.
	 *
	 * Adds Array Maps for:
	 * - 'site_infos':              Members and corresponding GUI labels of get_bloginfo.
	 * - 'user_data':               Keys of WP_User object property "data".
	 * - 'valid_operators':         Members represent valid math operatiors and their GUI label.
	 * - 'valid_comparison':        Members represent valid comparison operators and their GUI label.
	 * - 'valid_round_constants':   Members represent valid PHP round() directions and their GUI label.
	 * - 'shortcode_types':         Members represent valid ShortCode Types.
	 *
	 * @since 1.0.0
	 * @param string $map the data map to retrieve. Accepts: 'site_infos', 'user_data', 'valid_operators', 'valid_comparison', 'valid_round_constants', 'shortcode_types'.
	 * @return array $$map The Array Map requested.
	 */
	public function data_map( $map ) {

		$user_data = array(
			'ID',
			'user_login',
			'user_pass',
			'user_nicename',
			'user_email',
			'user_url',
			'user_registered',
			'user_activation_key',
			'user_status',
			'display_name',
		);

		$valid_comparison = array(
			'eqv'   => esc_html__( 'Equal', 'tkt-search-and-filter' ),
			'eqvt'  => esc_html__( 'Identical', 'tkt-search-and-filter' ),
			'nev'   => esc_html__( 'Not equal', 'tkt-search-and-filter' ),
			'nevt'  => esc_html__( 'Not identical', 'tkt-search-and-filter' ),
			'lt'    => esc_html__( 'Lesss than', 'tkt-search-and-filter' ),
			'gt'    => esc_html__( 'Greater than', 'tkt-search-and-filter' ),
			'gte'   => esc_html__( 'Less than or equal to', 'tkt-search-and-filter' ),
			'lte'   => esc_html__( 'Greater than or equal to', 'tkt-search-and-filter' ),
		);

		$post_query_vars = array(
			'author'            => esc_html__( 'By Author ID', 'tkt-search-and-filter' ),
			'author_name'       => esc_html__( 'By User NiceName', 'tkt-search-and-filter' ),
			'author__in'        => esc_html__( 'By Authors in these User IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'author__not_in'    => esc_html__( 'By Authors no in these User IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'cat'               => esc_html__( 'By Category ID', 'tkt-search-and-filter' ),
			'category_name'     => esc_html__( 'By Category Slug', 'tkt-search-and-filter' ),
			'category__and'     => esc_html__( 'By Categories in all these Category IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'category__in'      => esc_html__( 'By Categories in these Category IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'category__not_in'  => esc_html__( 'By Categories not in any of these Category IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'tag'               => esc_html__( 'By Tag Slug', 'tkt-search-and-filter' ),
			'tag_id'            => esc_html__( 'By Tag ID', 'tkt-search-and-filter' ),
			'tag__and'          => esc_html__( 'By Tags in all these Tag IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'tag__in'           => esc_html__( 'By Tags in these Tag IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'tag__not_in'       => esc_html__( 'By Tags not in any of these Tag IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'tag_slug__and'     => esc_html__( 'By Tags in all these Tag Slugs (Comma Delimited)', 'tkt-search-and-filter' ),
			'tag_slug__in'      => esc_html__( 'By Tags in some of these Tag Slugs (Comma Delimited)', 'tkt-search-and-filter' ),
			's'                 => esc_html__( 'By Search keyword', 'tkt-search-and-filter' ),
			'p'                 => esc_html__( 'By Post ID', 'tkt-search-and-filter' ),
			'name'              => esc_html__( 'By Post Slug', 'tkt-search-and-filter' ),
			'page_id'           => esc_html__( 'By Page ID', 'tkt-search-and-filter' ),
			'pagename'          => esc_html__( 'By Page Slug', 'tkt-search-and-filter' ),
			'post_parent'       => esc_html__( 'By Parent Page ID (If set to 0 returns all Parent Pages)', 'tkt-search-and-filter' ),
			'post_parent__in'   => esc_html__( 'By Parent Page in these IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'post_parent__not_in'   => esc_html__( 'By Parent Page not in these IDs (Comma Delimited)', 'tkt-search-and-filter' ),
			'post__in'          => esc_html__( 'By these IDs (Use "ignore_sticky_posts" to remove sticky posts)', 'tkt-search-and-filter' ),
			'post__not_in'      => esc_html__( 'Not By these IDs', 'tkt-search-and-filter' ),
			'post_name__in'     => esc_html__( 'By these Slugs', 'tkt-search-and-filter' ),
			'has_password'      => esc_html__( 'By Password set or not (true for posts with passwords ; false for posts without passwords ; null for all posts with and without passwords)', 'tkt-search-and-filter' ),
			'post_password'     => esc_html__( 'By Password', 'tkt-search-and-filter' ),
			'post_type'         => esc_html__( 'By Post Type (For this to work you must include the post types in the loop)', 'tkt-search-and-filter' ),
			'post_status'       => esc_html__( 'By Post Status', 'tkt-search-and-filter' ),
			'comment_count'     => esc_html__( 'The amount of comments your CPT has to have ( Search operator will do a ‘=’ operation', 'tkt-search-and-filter' ),
			'comment_count'     => esc_html__( 'By Comment Count', 'tkt-search-and-filter' ),
		);

		$shortcode_types = array(
			'queryable' => esc_html__( 'Search and Filters', 'tkt-search-and-filter' ),
			'internal'  => '',
		);

		$select_types = array(
			'single'        => esc_html__( 'Single Select Input', 'tkt-search-and-filter' ),
			'multiple'      => esc_html__( 'Multiple Select Input', 'tkt-search-and-filter' ),
			'single_s2'     => esc_html__( 'Single Select2 Input', 'tkt-search-and-filter' ),
			'multiple_s2'   => esc_html__( 'Multiple Select2 Input', 'tkt-search-and-filter' ),
		);

		$button_types = array(
			'submit'    => esc_html__( 'Submit Button', 'tkt-search-and-filter' ),
			'reset'     => esc_html__( 'Reset Button', 'tkt-search-and-filter' ),
			'button'    => esc_html__( 'Button (Actionless)', 'tkt-search-and-filter' ),
		);

		return $$map;
	}

	/**
	 * All Sanitization Options.
	 *
	 * @since 1.0.0
	 * @return array {
	 *      Multidimensional Array keyed by Sanitization options.
	 *
	 *      @type array $sanitization_option {
	 *          Single sanitization option array, holding label and callback of sanitization option.
	 *
	 *          @type string $label Label of Sanitization option as used in GUI.
	 *          @type string $callback The callback to the Sanitization function.
	 *      }
	 * }
	 */
	private function sanitize_options() {

		$sanitization_options = array(
			'none' => array(
				'label'     => esc_html__( 'No Sanitization', 'tkt-search-and-filter' ),
			),
			'email' => array(
				'label'     => esc_html__( 'Sanitize Email', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_email',
			),
			'file_name' => array(
				'label'     => esc_html__( 'File Name', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_file_name',
			),
			'html_class' => array(
				'label'     => esc_html__( 'HTML Class', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_html_class',
			),
			'key' => array(
				'label'     => esc_html__( 'Key', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_key',
			),
			'meta' => array(
				'label'     => esc_html__( 'Meta', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_meta',
			),
			'mime_type' => array(
				'label'     => esc_html__( 'Mime Type', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_mime_type',
			),
			'option' => array(
				'label'     => esc_html__( 'Option', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_option',
			),
			'sql_orderby' => array(
				'label'     => esc_html__( 'SQL Orderby', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_sql_orderby',
			),
			'text_field' => array(
				'label'     => esc_html__( 'Text Field', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_text_field',
			),
			'textarea_field' => array(
				'label'     => esc_html__( 'Text Area', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_textarea_field',
			),
			'title' => array(
				'label'     => esc_html__( 'Title', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_title',
			),
			'title_for_query' => array(
				'label'     => esc_html__( 'Title for Query', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_title_for_query',
			),
			'title_with_dashes' => array(
				'label'     => esc_html__( 'Title with Dashes', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_title_with_dashes',
			),
			'user' => array(
				'label'     => esc_html__( 'User', 'tkt-search-and-filter' ),
				'callback'  => 'sanitize_user',
			),
			'url_raw' => array(
				'label'     => esc_html__( 'URL Raw', 'tkt-search-and-filter' ),
				'callback'  => 'esc_url_raw',
			),
			'post_kses' => array(
				'label'     => esc_html__( 'Post KSES', 'tkt-search-and-filter' ),
				'callback'  => 'wp_filter_post_kses',
			),
			'nohtml_kses' => array(
				'label'     => esc_html__( 'NoHTML KSES', 'tkt-search-and-filter' ),
				'callback'  => 'wp_filter_nohtml_kses',
			),
			'intval' => array(
				'label'     => esc_html__( 'Integer', 'tkt-search-and-filter' ),
				'callback'  => 'intval',
			),
			'floatval' => array(
				'label'     => esc_html__( 'Float', 'tkt-search-and-filter' ),
				'callback'  => 'floatval',
			),
			'is_bool' => array(
				'label'     => esc_html__( 'Boolean', 'tkt-search-and-filter' ),
				'callback'  => 'is_bool',
			),
		);

		return $sanitization_options;

	}

	/**
	 * Provide a public facing method to add ShortCodes to the TukuToi ShortCodes library
	 *
	 * Adds ShortCodes to `tkt_scs_register_shortcode` Filter.
	 *
	 * @since 2.0.0
	 * @param array $external_shortcodes The array of shortcodes being added.
	 * @return array $$external_shortcodes The ShortCodes array.
	 */
	public function declare_shortcodes_add_filter( $external_shortcodes ) {

		$external_shortcodes = $this->declare_shortcodes();

		return $external_shortcodes;

	}

	/**
	 * Provide a public facing method to add ShortCode Types to the TukuToi ShortCodes GUI.
	 *
	 * Adds ShortCode Types to `tkt_scs_register_shortcode_type` Filter.
	 *
	 * @since 2.0.0
	 * @param array $external_shortcode_types The array of Shortcode Types being added.
	 * @return array $$external_shortcode_types The ShortCode Types array.
	 */
	public function declare_shortcodes_types_add_filter( $external_shortcode_types ) {

		$external_shortcode_types = $this->data_map( 'shortcode_types' );

		return $external_shortcode_types;

	}

}
