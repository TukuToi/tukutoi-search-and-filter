<?php
/**
 * This file includes the ShortCodes GUI interfaces.
 *
 * @since 1.4.0
 * @package Tkt_Search_And_Filters/admin
 */

/**
 * The class to generate a ShortCode GUI.
 *
 * Defines all type of Input fields necessary, also
 * creates specific methods to populate eventual options
 * and returns a fully usable GUI (jQuery dialog) for each ShortCode.
 *
 * @todo Move all these procedural silly single methods to a more abstract method!
 * The almost to all the same thing, unless one or two. Thus use arguments, not new methods.
 *
 * @since      1.4.0
 * @package    Tkt_Search_And_Filters
 * @subpackage Tkt_Search_And_Filters/admin
 * @author     Your Name <hello@tukutoi.com>
 */
class Tkt_Search_And_Filters_Gui {

	/**
	 * The Configuration object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $declarations    All configurations and declarations of this plugin.
	 */
	private $declarations;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since   1.0.0
	 * @param   array $declarations    The Configuration object.
	 */
	public function __construct( $declarations ) {

		$this->declarations = $declarations;

	}

	/**
	 * Create a Select Field set for the ShortCodes Forms SiteInfo Display Options.
	 *
	 * @since 1.4.0
	 */
	public function alltypes_options() {

		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$editable_roles = array_reverse( get_editable_roles() );

		/**
		 * Somehow WP neglects its users.
		 * Pun intended, but it is just true. Both in real and programming.
		 */
		foreach ( $editable_roles as $role => $details ) {

			$user_roles[] = (object) array(
				'name'      => $role,
				'labels'    => (object) array( 'menu_name' => translate_user_role( $details['name'] ) ),
			);

		}

		$alltypes = array_merge( $taxonomies, $post_types, $user_roles );

		foreach ( $alltypes as $alltype => $object ) {
			$label = $object->labels->menu_name;
			$name  = $object->name;
			$selected = 'post' === $alltype ? 'selected' : '';
			printf( '<option value="%s" ' . esc_attr( $selected ) . '>%s</option>', esc_attr( $name ), esc_html( $label ) );
		}

		add_filter(
			'tkt_scs_shortcodes_fieldset_explanation',
			function( $explanation ) {
				$explanation = __( 'Show Search Results for this (Content or User) Type', 'tkt-search-and-filter' );
				return $explanation;
			}
		);

	}

	/**
	 * Create a Select Field set for the ShortCodes Forms Post Query Vars Options.
	 *
	 * @since 1.4.0
	 */
	public function queryvars_options() {

		$query_vars = $this->declarations->data_map( 'post_query_vars' );

		foreach ( $query_vars as $query_var => $array ) {

			$selected = 's' === $query_var ? 'selected' : '';
			printf( '<option value="%s" ' . esc_attr( $selected ) . '>%s</option>', esc_attr( $query_var ), esc_html( $array['label'] ) );
		}

		add_filter(
			'tkt_scs_shortcodes_fieldset_explanation',
			function( $explanation ) {
				$explanation = __( 'Search by this variable', 'tkt-search-and-filter' );
				return $explanation;
			}
		);

	}

	/**
	 * Create a Select Field set for the ShortCodes Forms Select Type Display Options.
	 *
	 * @since 1.4.0
	 */
	public function selecttype_options() {

		$select_types = $this->declarations->data_map( 'select_types' );

		foreach ( $select_types as $select_type => $label ) {

			$selected = 's' === $select_type ? 'selected' : '';
			printf( '<option value="%s" ' . esc_attr( $selected ) . '>%s</option>', esc_attr( $select_type ), esc_html( $label ) );
		}

		add_filter(
			'tkt_scs_shortcodes_fieldset_explanation',
			function( $explanation ) {
				$explanation = __( 'The Type of Select Input to use', 'tkt-search-and-filter' );
				return $explanation;
			}
		);

	}

	/**
	 * Create a Select Field set for the ShortCodes Forms Select Type Display Options.
	 *
	 * @since 1.4.0
	 */
	public function buttontype_options() {

		$button_types = $this->declarations->data_map( 'button_types' );

		foreach ( $button_types as $button_type => $label ) {

			$selected = 's' === $button_type ? 'selected' : '';
			printf( '<option value="%s" ' . esc_attr( $selected ) . '>%s</option>', esc_attr( $button_type ), esc_html( $label ) );
		}

		add_filter(
			'tkt_scs_shortcodes_fieldset_explanation',
			function( $explanation ) {
				$explanation = __( 'The Type of Button to use', 'tkt-search-and-filter' );
				return $explanation;
			}
		);

	}

	/**
	 * Create a Select Field set for the ShortCodes Forms Select Type Display Options.
	 *
	 * @since 1.4.0
	 */
	public function pagtype_options() {

		$pagination_types = array(
			'plain' => 'Plain',
			'list' => 'List',
		);

		foreach ( $pagination_types as $pagination_type => $label ) {

			$selected = 'plain' === $pagination_type ? 'selected' : '';
			printf( '<option value="%s" ' . esc_attr( $selected ) . '>%s</option>', esc_attr( $pagination_type ), esc_html( $label ) );
		}

		add_filter(
			'tkt_scs_shortcodes_fieldset_explanation',
			function( $explanation ) {
				$explanation = __( 'The Type of Pagination to output (Plain or List)', 'tkt-search-and-filter' );
				return $explanation;
			}
		);

	}

	/**
	 * Create a Select Field set for the ShortCodes Forms Select Type Display Options.
	 *
	 * @since 1.4.0
	 */
	public function filtertype_options() {

		$filtertype_options = array(
			'reload'    => 'Full Page Reload',
			'ajax'      => 'AJAX refresh',
		);

		foreach ( $filtertype_options as $filtertype_option => $label ) {

			$selected = 'reload' === $filtertype_option ? 'selected' : '';
			printf( '<option value="%s" ' . esc_attr( $selected ) . '>%s</option>', esc_attr( $filtertype_option ), esc_html( $label ) );
		}

		add_filter(
			'tkt_scs_shortcodes_fieldset_explanation',
			function( $explanation ) {
				$explanation = __( 'The Type of Search and Filter (AJAX or Full Page Reload)', 'tkt-search-and-filter' );
				return $explanation;
			}
		);

	}

}
