<?php
/**
 * The ShortCodes of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/public
 */

/**
 * Defines all ShortCodes.
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/public
 * @author     Your Name <hello@tukutoi.com>
 */
class Tkt_Search_And_Filter_Shortcodes {

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
	 * The Configuration object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $declarations    All configurations and declarations of this plugin.
	 */
	private $declarations;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 * @param      object $declarations     The Configuration object.
	 * @param      object $query            The Query object.
	 * @param      object $sanitizer        The Sanitization object.
	 * @param      object $plugin_public    The Public object of this plugin.
	 */
	public function __construct( $plugin_prefix, $version, $declarations, $query, $sanitizer, $plugin_public ) {

		$this->plugin_prefix    = $plugin_prefix;
		$this->version          = $version;
		$this->declarations     = $declarations;

		$this->sanitizer        = $sanitizer;
		$this->query            = $query;
		$this->plugin_public    = $plugin_public;

	}

	/**
	 * TukuToi `[searchtemplate]` ShortCode.
	 *
	 * Outputs the Search Form.</br>
	 * Mandatory to use when adding Search ShortCodes.
	 *
	 * Example usage:
	 * ```
	 * [searchtemplate instance="my_instance" customid="my_id" customclasses="class_one classtwo"]
	 *   // Search ShortCodes here.
	 * [/searchtemplate]
	 * ```</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $instance       The Instance used to bind this Search section to a Loop Results Section. Default: ''. Accepts: '', any valid string or number.
	 *      @type string    $customid     ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses   CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi Search and Filter Search ShortCodes, HTML.
	 * @param string $tag       The Shortcode tag. Value: 'searchtemplate'.
	 */
	public function searchtemplate( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'instance'          => 'my_instance',
				'type'              => 'reload', // ajax or reload.
				'customid'          => '',
				'customclasses'     => '',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
		}

		/**
		 * Global used to tag the current instance and map search URL parameters to search Query parameters.
		 *
		 * Set the current instance.
		 * Set the search type (ajax or reload).
		 *
		 * @since 2.0.0
		 */
		global $tkt_src_fltr;
		$this->query->set_type( $atts['type'] );
		$tkt_src_fltr['instance'] = $atts['instance'];

		$data_tkt = 'ajax' === $atts['type'] ? 'data-tkt-ajax-src-form="' . $atts['instance'] . '"' : '';
		// Build the Form Start.
		$src_form_start = '<form id="' . $atts['customid'] . '" class="' . $atts['customclasses'] . '" type="GET" ' . $data_tkt . '>';

		/**
		 * We need to run the content thru ShortCodes Processor, otherwise ShortCodes are not expanded.
		 *
		 * @todo check if we can sanitize the $content here with $content = $this->sanitizer->sanitize( 'post_kses', $content );
		 * @since 2.0.0
		 */
		$content = apply_filters( 'tkt_post_process_shortcodes', $content );
		$content = do_shortcode( $content, false );

		// Add the Instance as hidden field so it is available after Form Submit in URL param.
		$instance = '<input type="hidden" value="' . $atts['instance'] . '" name="instance">';

		// Build the Form End.
		$src_form_end = '</form>';

		// Merge the form parts.
		$out = $src_form_start . $content . $instance . $src_form_end;

		return $out;

	}

	/**
	 * TukuToi `[loop]` ShortCode.
	 *
	 * Outputs the Search Results and loops over each item found.</br>
	 * Mandatory to use when adding Search Results.
	 *
	 * Example usage:
	 * ```
	 * [loop instance="my_instance" customid="my_id" customclasses="class_one classtwo" type="post" error="no posts found"]
	 *   // Any TukuToi ShortCodes, or other HTML and Post Data to display for each item found.
	 * [/loop]
	 * ```</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $instance       The Instance used to bind this Loop section to a Search Form Section. Default: ''. Accepts: '', any valid string or number. Must match corresponding Search Form instance.
	 *      @type string    $type           For what type the query results are for. Default: 'post'. Accepts: valid post type, valid taxonomy type, valid user role.
	 *      @type string    $error          The no results found message: Default ''. Accepts: valid string or HTML.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi ShortCodes, ShortCodes and HTML. No TukuToi Search ShortCodes.
	 * @param string $tag       The Shortcode tag. Value: 'loop'.
	 *
	 * @todo This is messy, redo this query args.
	 */
	public function loop( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'instance'      => 'my_instance',
				'type'          => 'post',
				'error'         => 'No Results Found',
				'pag_arg'       => '',
				'posts_per_page' => '',
				'customclasses' => '',
				'container'     => '',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			if ( 'error' === $key ) {
				$atts['error'] = $this->sanitizer->sanitize( 'post_kses', $value );
			} elseif ( 'posts_per_page' === $atts['type'] ) {
				$atts['posts_per_page'] = $this->sanitizer->sanitize( 'intval', $value );
			} elseif ( 'type' === $key ) {
				$atts['type'] = $this->sanitizer->sanitize( 'text_field', $value );
				// If several types are passed to type.
				if ( strpos( $atts['type'], ',' ) !== false ) {
					$atts['type'] = explode( ',', $atts['type'] );
				}
			} else {
				$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
			}
		}

		if ( ( ! is_array( $atts['type'] )
			&& post_type_exists( $atts['type'] )
			) || is_array( $atts['type'] )
			&& (bool) array_product( array_map( 'post_type_exists', $atts['type'] ) ) === true
		) {
			// The Post Type or Post Types do exit but may not be an array if only one was passed.
			if ( ! is_array( $atts['type'] ) ) {
				$post_types = array( $atts['type'] );
			} else {
				$post_types = $atts['type'];
			}
			$default_query_args = array(
				'post_type'              => $post_types,
				'post_status'            => array( 'publish' ),
				'posts_per_page'         => $atts['posts_per_page'],
				'order'                  => 'DESC',
				'orderby'                => 'date',
				'cache_results'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);

		}

		// Get our loop.
		$this->query->set_instance( $atts['instance'] );
		if ( ! empty( $atts['pag_arg'] ) ) {
			$this->query->set_custom_pagarg( $atts['pag_arg'] );
		}
		if ( 'ajax' !== $this->query->get_type() ) {
			unset( $atts['pag_arg'] );
		}
		unset( $atts['posts_per_page'] );

		// Merge the default Query args into the User Args. Overwrite defaults with User Input.
		if ( isset( $atts['pag_arg'] ) ) {
			$default_query_args['pag_arg'] = $atts['pag_arg'];// array_merge( $default_query_args, $atts );.
		}

		$this->query->set_query_args( $default_query_args );
		$loop = $this->query->the_loop( $content, $atts['error'] );

		// If it is an AJAX search.
		if ( 'ajax' === $this->query->get_type() ) {
			/**
			 * AJAX not needed unless we are in a AJAX type of loop/search.
			 *
			 * Save the users some headaches, usually plugins just throw the scripts on all pages...
			 *
			 * Here we:
			 * 1. Enqueue TukuToi AJAX if needed.
			 * 2. Localise TukuToi AJAX object if needed.
			 *
			 * @since 2.10.0
			 */
			wp_enqueue_script( $this->plugin_prefix . 'query' );
			$this->plugin_public->maybe_localize_script(
				$this->plugin_prefix . 'query',
				'tkt_ajax_loop_object',
				$atts['instance'],
				array(
					'is_doing_ajax' => true,
					'ajax_url'      => $this->sanitizer->sanitize( 'esc_url_raw', admin_url( 'admin-ajax.php' ) ),
					'nonce'         => wp_create_nonce( 'tkt_ajax_nonce' ),
					'content'       => $this->sanitizer->sanitize( 'text_field', $content ),
					'instance'      => $atts['instance'],
					'query_args'    => $default_query_args,
					'error'         => $atts['error'],
				)
			);

		}

		/**
		 * If AJAX, we need a container to populate with a data-tkt-ajax-src-loop and ID set.
		 *
		 * This container is also set if the user populates the container without AJAX method.
		 * In that case, we do not need data-tkt-ajax-src-loop.
		 *
		 * If the user does not provide a container type, we assueme raw output and return only the loop.
		 *
		 * @since 2.19.0
		 */
		if ( ! empty( $atts['container'] ) && 'ajax' !== $this->query->get_type() ) {
			$out = '<' . $atts['container'] . ' id="' . $atts['instance'] . '" class="' . $atts['customclasses'] . '">' . $loop . '</' . $atts['container'] . '>';
		} elseif ( 'ajax' === $this->query->get_type() ) {
			$out = '<' . $atts['container'] . ' id="' . $atts['instance'] . '" data-tkt-ajax-src-loop="' . $atts['instance'] . '" class="' . $atts['customclasses'] . '"></' . $atts['container'] . '>';
		} else {
			$out = $loop;
		}

		return $out;

	}

	/**
	 * TukuToi `[textsearch]` ShortCode.
	 *
	 * Outputs the Text Search Form.</br>
	 * Can only be used inside a `[searchtemplate][/searchtemplate]` ShortCode.
	 *
	 * Example usage:
	 * `[textsearch placeholder="Search..." url_param="_s" search_by="title" customid="my_id" customclasses="class_one classtwo"]`</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $placeholder    The Search Input Placeholder. Default: 'Search...'. Accepts: valid string.
	 *      @type string    $urlparam      URL parameter to use. Default: '_s'. Accepts: valid URL search parameter.
	 *      @type string    $searchby      Query Parameter. Default: 's'. Accepts: valid WP Query Parmater.
	 *      @type string    $customid       ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses  CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi Search and Filter Search ShortCodes, HTML.
	 * @param string $tag       The Shortcode tag. Value: 'textsearch'.
	 */
	public function textsearch( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'placeholder'   => 'Search...',
				'urlparam'     => '_s',
				'searchby'     => 's',
				'customid'      => '',
				'customclasses' => '',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
		}

		/**
		 * Global used to tag the current instance and map search URL parameters to search Query parameters.
		 *
		 * Map the URL param to the actual Query Param.
		 *
		 * @since 2.0.0
		 */
		global $tkt_src_fltr;
		$tkt_src_fltr['searchby'][ $atts['urlparam'] ] = $atts['searchby'];

		// Build our Serach input.
		$search = '<label for="' . $atts['customid'] . '">' . $atts['placeholder'] . '</label>';
		$src_type = $this->query->get_type();
		$tkt_data = 'ajax' === $src_type ? 'data-tkt-ajax-src="' . $atts['searchby'] . '"' : '';
		$search = '<input type="text" id="' . $atts['customid'] . '" placeholder="' . $atts['placeholder'] . '" name="' . $atts['urlparam'] . '" ' . $tkt_data . ' class="' . $atts['customclasses'] . '">';

		// Return our Search Input. Already Sanitized.
		return $search;

	}

	/**
	 * TukuToi `[selectsearch]` ShortCode.
	 *
	 * Outputs the Select Search Form.</br>
	 * Can only be used inside a `[searchtemplate][/searchtemplate]` ShortCode.
	 *
	 * Example usage:
	 * `[selectsearch placeholder="Search..." urlparam="_s" searchby="title" type="multiples2" customid="my_id" customclasses="class_one classtwo"]`</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @since    2.29.0 Added ShortCode attributes `custom_tax` and `tax_field`
	 *
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $placeholder    The Search Input Placeholder. Default: 'Search...'. Accepts: valid string.
	 *      @type string    $urlparam       URL parameter to use. Default: '_s'. Accepts: valid URL search parameter.
	 *      @type string    $searchby       Query Parameter. Default: 's'. Accepts: valid WP Query Parmater.
	 *      @type string    $type           Type of Select. Default: 'single'. Accepts: 'single', 'multiple', 'singleS2', 'multipleS2'.
	 *      @type string    $customid       ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses  CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 *      @type string    $custom_tax     Custom Taxonomy Slug to query by. Default: ''. Accepts: valid custom taxonomy slug.
	 *      @type string    $tax_field      Custom Taxonomy field to query by. Default: 'term_id'. Accepts: valid `tax_query` `field` values.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi Search and Filter Search ShortCodes, HTML.
	 * @param string $tag       The Shortcode tag. Value: 'selectsearch'.
	 */
	public function selectsearch( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'placeholder'   => 'Search...',
				'urlparam'      => '_s',
				'searchby'      => 's',
				'type'          => 'single',
				'post_type'     => 'post',
				'customid'      => '',
				'customclasses' => '',
				'custom_tax'    => '',
				'tax_field'     => 'term_id',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			if ( 'post_type' === $key ) {
				$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
				if ( strpos( $atts['post_type'], ',' ) !== false ) {
					$atts['post_type'] = explode( ',', $atts['type'] );
				}
			} else {
				$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
			}
		}

		// The select Type - if multiple - needs a `[]` appended to name.
		$multiple_name  = 'multiple' === $atts['type'] || 'multipleS2' === $atts['type'] ? '[]' : '';
		$multiple_value = 'multiple' === $atts['type'] || 'multipleS2' === $atts['type'] ? 'multiple' : '';
		/**
		 * Global used to tag the current instance and map search URL parameters to search Query parameters.
		 *
		 * Map the URL param to the actual Query Param.
		 *
		 * For taxonomies, we must pass an array composed of taxonomy to query and the fields to query.
		 * The relation for this is (not yet) set globally in the search shortcode.
		 *
		 * @since 2.0.0
		 * @since 2.29.0 Added `tax_query`
		 */
		global $tkt_src_fltr;
		$tkt_src_fltr['searchby'][ $atts['urlparam'] ] = $atts['searchby'];
		if ( 'tax_query' === $atts['searchby'] ) {
			$tkt_src_fltr['searchby']['tax_query'][] = array(
				'url_param' => $atts['urlparam'],
				'taxonomy' => $atts['custom_tax'],
				'field' => $atts['tax_field'],
			);
		}

		/**
		 * Build a Select Input with either User, Term or Post Data.
		 *
		 * Use better_dropdown_users() for Users.
		 *
		 * @see https://docs.classicpress.net/reference/functions/wp_dropdown_users/
		 * @see {/includes/tkt-search-and-filter-fix-worcpress.php}
		 *
		 * Use better_dropdown_categories() for all Taxonomies.
		 *
		 * @see https://docs.classicpress.net/reference/functions/wp_dropdown_categories/
		 * @see {/includes/tkt-search-and-filter-fix-worcpress.php}
		 *
		 * Use get_posts for Posts (because it is faster than WP_Query for non-paginated lists).
		 *
		 * @see https://docs.classicpress.net/reference/functions/get_posts/
		 * @see example https://www.smashingmagazine.com/2016/03/advanced-wordpress-search-with-wp_query/
		 * @see performance details https://wordpress.stackexchange.com/questions/1753/when-should-you-use-wp-query-vs-query-posts-vs-get-posts
		 *
		 * @since 2.0.0
		 * @since 2.29.0 Added $value_field for tax_query
		 */
		$post_query_vars = $this->declarations->data_map( 'post_query_vars' );
		$value_field    = isset( $post_query_vars[ $atts['searchby'] ]['field'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['field'] )
						: null;
		$value_field    = 'tax_query' === $atts['searchby'] ? $atts['tax_field'] : $value_field;
		$query_type     = isset( $post_query_vars[ $atts['searchby'] ]['type'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['type'] )
						: null;
		$callback       = isset( $post_query_vars[ $atts['searchby'] ]['cback'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['cback'] )
						: null;
		$values         = isset( $post_query_vars[ $atts['searchby'] ]['vals'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['vals'] )
						: null;
		$usr_show_value = 'display_name';
		/**
		 * Filter `tkt_src_fltr_user_select_search_show` for the User Select Search "Show" Value.
		 *
		 * We do not have a GUI for this, as it is too many-versed.
		 * Instead, we offer a Filter.
		 *
		 * @since 2.26.0
		 * @since 2.29.0 Added `taxonomy` drop down case, `custom_attr` key.
		 *
		 * @param string $usr_show_value The value to show for the select dropdowns. Default: 'display_name'. Accepts: any user field, or 'display_name_with_login' to show the display name with user_login in parentheses.
		 */
		$usr_show_value = apply_filters( 'tkt_src_fltr_user_select_search_show', $usr_show_value );
		$usr_show_value = $this->sanitizer->sanitize( 'key', $usr_show_value );
		switch ( $post_query_vars[ $atts['searchby'] ]['type'] ) {
			case 'user':
				$select_form = better_dropdown_users(
					array(
						'show_option_all'   => empty( $multiple_value ) ? $atts['placeholder'] : null,
						'multi'             => $multiple_value,
						'show'              => $usr_show_value,
						'value_field'       => $value_field,
						'echo'              => false,
						'name'              => $atts['urlparam'],
						'id'                => $atts['customid'],
						'class'             => $atts['customclasses'],
						'data_attr'         => $atts['searchby'],
					)
				);
				break;
			case 'category':
			case 'post_tag':
				$select_form = better_dropdown_categories(
					array(
						'show_option_all'   => empty( $multiple_value ) ? $atts['placeholder'] : null,
						'show_count'        => true,
						'echo'              => false,
						'hierarchical'      => true,
						'value_field'       => $value_field,
						'taxonomy'          => $query_type,
						'name'              => $atts['urlparam'],
						'id'                => $atts['customid'],
						'class'             => $atts['customclasses'],
						'multi'             => $multiple_value,
						'data_attr'         => $atts['searchby'],
					)
				);
				break;
			case 'taxonomy':
				$select_form = better_dropdown_categories(
					array(
						'show_option_all'   => empty( $multiple_value ) ? $atts['placeholder'] : null,
						'show_count'        => true,
						'echo'              => false,
						'hierarchical'      => true,
						'value_field'       => $value_field,
						'taxonomy'          => $atts['custom_tax'],
						'name'              => $atts['urlparam'],
						'id'                => $atts['customid'],
						'class'             => $atts['customclasses'],
						'multi'             => $multiple_value,
						'data_attr'         => $atts['searchby'],
						'custom_attr'       => $atts['custom_tax'],
					)
				);
				break;
			default:
				/**
				* Build our select input.
				*
				* This is a complicated beast.
				* We cannot build this select with just hardcoded options, but also not by just dynamic Post Objet Options.
				* For example, you may search by a dynamically populated post_types or post_statuses list, but those options
				* exist only ONCE, not ONCE FOR EACH post. However, when we want to query say by pagename, then
				* the select should offer options of each post, as each post will be distinct.
				*
				* Wether or not that is actually wise, is another question.
				* This might be better removed in future in favour of a handpicked few options.
				* For example, it makes poor sense to create a Select with pagenames, or IDs, or any other thing
				* that is looped for each post.
				* However, right now, it is up to the user how much sillyshness he/she/it wants to apply.
				* The code is safe enough to handle it.
				*
				* The real power in these selects are Taxonomy, Author and Postmeta.
				*
				* @todo Add postmeta support.
				* @since 2.0.0
				* @since 2.29.0 added data-tkt-ajax-custom attribute.
				*/
				if ( empty( $multiple_value ) ) {
					$options = '<option value="">' . $atts['placeholder'] . '</option>';
				}
				if ( ! is_null( $value_field )
					&& (
						( ! is_array( $atts['post_type'] )
							&& post_type_exists( $atts['post_type'] )
						)
						|| is_array( $atts['post_type'] )
						&& (bool) array_product( array_map( 'post_type_exists', $atts['post_type'] ) ) === true
					)
				) {
					// The Post Type or Post Types do exist but may not be an array if only one was passed.
					if ( ! is_array( $atts['post_type'] ) ) {
						$post_type = array( $atts['post_type'] );
					}
					$posts_data = get_posts(
						array(
							'numberposts'   => -1,
							'post_type'     => $post_type,
						)
					);
					foreach ( $posts_data as $key => $post_object ) {
						$options .= '<option value="' . esc_attr( $post_object->post_status ) . '">' . esc_html( ucfirst( $post_object->post_status ) ) . '</option>';
					}
				} elseif ( ! is_null( $callback ) ) {
					$callback_options = call_user_func( $callback );
					foreach ( $callback_options as $option => $label ) {
						$options .= '<option value="' . esc_attr( $option ) . '">' . esc_html( $label ) . '</option>';
					}
				} elseif ( ! is_null( $values ) ) {
					foreach ( $values as $value => $label ) {
						$options .= '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
					}
				}
				$select_form = '<label for="' . $atts['customid'] . '">' . $atts['placeholder'] . '</label>';
				$src_type = $this->query->get_type();
				$tkt_data = 'ajax' === $src_type ? 'data-tkt-ajax-src="' . $atts['searchby'] . '"' : '';
				$tkt_custom = 'ajax' === $src_type ? 'data-tkt-ajax-custom="' . $atts['custom_tax'] . '"' : '';
				$select_form .= '<select name="' . $atts['urlparam'] . $multiple_name . '" id="' . $atts['customid'] . '"' . $multiple_value . ' ' . $tkt_data . ' ' . $tkt_custom . '>';
				$select_form .= $options;
				$select_form .= '</select>';
				break;
		}

		/**
		 * Select2 is not needed unless we are in a Select ShortCode and declared Select2 instances.
		 *
		 * Save the users some headaches, usually plugins just throw the scripts on all pages...
		 *
		 * Here we:
		 * 1. Enqueue Select2 CSS if needed.
		 * 3. Enqueue TukuToi Select2 JS if needed. Dependency: select2.
		 * 4. Localise TukuToi Select2 JS if needed.
		 *
		 * @since 2.10.0
		 */

		if ( 'multipleS2' === $atts['type'] || 'singleS2' === $atts['type'] ) {
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( $this->plugin_prefix . 'select2' );
			$this->plugin_public->maybe_localize_script(
				$this->plugin_prefix . 'select2',
				'tkt_select2',
				$atts['customid'],
				$value = array(
					'placeholder'   => $atts['placeholder'],
				)
			);
		}

		// Return Select Form.
		return $select_form;

	}

	/**
	 * TukuToi `[buttons]` ShortCode.
	 *
	 * Outputs the Buttons for Search Form.</br>
	 * Can only be used inside a `[searchtemplate][/searchtemplate]` ShortCode.</br>
	 * Can be used to produce Search input as well, apart of Submit and Reset buttons.
	 *
	 * Example usage:
	 * `[buttons label="Submit..." type="submit" customid="my_id" customclasses="class_one classtwo"]`</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $label          The Button Label. Default: 'Submit'. Accepts: valid string.
	 *      @type string    $url_param      URL parameter to use. Default: ''. Accepts: valid URL search parameter.
	 *      @type string    $value          The value to pass to the URL parameter 'url_param'. Default: ''. Accepts: valid URL search parameter.
	 *      @type string    $search_by      Query Parameter. Default: ''. Accepts: valid WP Query Parmater.
	 *      @type string    $type           Type of Button. Default: 'submit'. Accepts: 'submit', 'reset', 'button'.
	 *      @type string    $autofocus      Whether to autofocus the button. Only one item on document can be autofocused. Default: ''. Accepts: '', autofocus'.
	 *      @type string    $form           Form ID to submit. Default: ancestor Form. Accepts: valid Form ID.
	 *      @type string    $formtarget     Target of the form. Default: '_self'. Accepts: '_self', '_blank'.
	 *      @type string    $customid       ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses  CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi Search and Filter Search ShortCodes, HTML.
	 * @param string $tag       The Shortcode tag. Value: 'selectsearch'.
	 */
	public function buttons( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'label'         => 'Submit', // Some label.
				'url_param'     => '_s', // This is the 'name' in a button.
				'value'         => '', // passe a =value to the URL ?name.
				'search_by'     => '',
				'type'          => 'submit', // defaults to submit when inside a fomr. possible: submit, reset, button.
				'autofocus'     => '', // Only one element in a document can have this attribute.
				'form'          => '', // defaults to ancestor form ID.
				'formtarget'    => '', // if wewant to send to new page.
				'customid'      => '',
				'customclasses' => '',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
		}

		/**
		 * Currently only Submit and Reset Buttons are tested.
		 *
		 * @todo this needs same process as a search input.
		 * @since 2.0.0
		 */

		// Build our button. All Inputs are sanitized.
		$button = '<button';
		$button .= ! empty( $atts['autofocus'] ) ? ' autofocus="' . $atts['autofocus'] . '"' : '';
		$button .= ! empty( $atts['form'] ) ? ' form="' . $atts['form'] . '"' : '';
		$button .= ! empty( $atts['type'] ) ? ' type="' . $atts['type'] . '"' : '';
		$button .= ! empty( $atts['name'] ) ? ' name="' . $atts['name'] . '"' : '';
		$button .= ! empty( $atts['value'] ) ? ' value="' . $atts['value'] . '"' : '';
		$button .= ! empty( $atts['formtarget'] ) ? 'formtarget="' . $atts['formtarget'] . '"' : '';
		$button .= ! empty( $atts['customid'] ) ? 'id="' . $atts['customid'] . '"' : '';
		$button .= ! empty( $atts['customclasses'] ) ? 'class="' . $atts['customclasses'] . '"' : '';
		$button .= '>' . $atts['label'] . '</button>';

		if ( 'reset' === $atts['type'] ) {
			global $tkt_src_fltr;
			wp_enqueue_script( $this->plugin_prefix . 'reset' );
			if ( 'ajax' === $this->query->get_type() ) {

				$this->plugin_public->maybe_localize_script( $this->plugin_prefix . 'reset', 'tkt_ajax_reset_object', $tkt_src_fltr['instance'], array( 'is_doing_ajax' => true ) );
			}
		}
		// Return our Button, all inputs are sanitized.
		return $button;

	}

	/**
	 * TukuToi `[spinner]` ShortCode.
	 *
	 * Outputs the Spinners for Search and Paginations when using AJAX.</br>
	 *
	 * Example usage:
	 * `[spinner url="/path/to/spinner.gif" container="div" customid="my_id" customclasses="class_one classtwo" value=""]`</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $url            Url to the Spinner asset. Default: ''. Accepts: valid URL to resource/asset.
	 *      @type string    $container      HTML container to use for displaying the spinner. Default: 'span'. Accepts: valid HTML element.
	 *      @type string    $value          Value to show instead of or together with URL asset. Default: ''. Accepts: valid string.
	 *      @type string    $customid       ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses  Additional CSS Classes to use for the Search Form. Default: 'tkt_ajax_loader'. Mandatory 'tkt_ajax_loader'. Accepts: 'tkt_ajax_loader' + valid HTML CSS classes, space delimited.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. Not applicable for this ShortCode.
	 * @param string $tag       The Shortcode tag. Value: 'spinner'.
	 */
	public function spinner( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'url'           => '', // Some label.
				'container'     => 'span', // This is the 'name' in a button.
				'value'         => '', // passe a =value to the URL ?name.
				'customid'      => '',
				'customclasses' => 'tkt_ajax_loader',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			if ( 'url' === $key ) {
				$atts[ $key ] = $this->sanitizer->sanitize( 'esc_url_raw', $value );
			}
			$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
		}

		/**
		 * Currently only submit button works/
		 *
		 * @todo this needs same process as a search input, as well as reset button logic.
		 * @since 2.0.0
		 */

		$spinner = ! empty( $atts['container'] ) ? '<' . $atts['container'] . ' id="' . $atts['customid'] . '" class="tkt_ajax_loader ' . $atts['customclasses'] . '">' : '';
		$spinner .= ! empty( $atts['url'] ) ? '<img src="' . $atts['url'] . '">' : '';
		$spinner .= ! empty( $atts['value'] ) ? $atts['value'] : '';
		$spinner .= ! empty( $atts['container'] ) ? '</' . $atts['container'] . '>' : '';

		// Return our Button, all inputs are sanitized.
		return $spinner;

	}

	/**
	 * TukuToi `[pagination]` ShortCode.
	 *
	 * Outputs the pagination Buttons.</br>
	 *
	 * Example usage:
	 * `[pagination label_prev="Previous" label_next="Next" urlparam="_page" customclasses="class classone"]`</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $aria_current       The value for the aria-current attribute. Default: page. Accepts: valid string.
	 *      @type bool      $show_all           Whether to show all pages. Default: false. Accepts: boolean true|false.
	 *      @type int       $end_size           How many numbers on either the start and the end list edges. Default 1. Accepts: numeric value.
	 *      @type string    $mid_size           How many numbers to either side of the current page. Default: 2. Accepts: numeric value.
	 *      @type string    $prev_next          Whether to include the previous and next links. Default: true. Accepts: bool true|false.
	 *      @type string    $prev_text          The previous page text. Default: 'Pre'. Accepts: valid string.
	 *      @type string    $next_text          The next page text. Default: 'Next'. Accepts: valid string.
	 *      @type string    $type               Controls format of the returned value. Default: plain. Accepts: plain, list.
	 *      @type string    $add_args           Query arguments to append to the URL. Default: ''. Accepts: URL arguments formatted like so: 'url_param:value,another_param:another-value'.
	 *      @type string    $add_fragment       A string to append to each URL (link) at the end. Default: ''. Accepts: valid string or urlparam.
	 *      @type string    $before_page_number A string to appear before the page number. Default: ''. Accepts: valid string.
	 *      @type string    $after_page_number  A string to appear after the page number. Default: ''. Accepts: valid string.
	 *      @type string    $instance           The unique instance of search and loop this pagination has to control. Default: ''. Accepts: valid instance (must match  Search template and Loop instance).
	 *      @type string    $customclasses      CSS Classes to use for the pagination links. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 *      @type string    $pag_arg            The URL parameter to use for this pagination. Default: item. Accepts: valid string but NOT 'page' or 'paged'.
	 *      @type string    $container          Container Type to put the pagination into. Default: ''. Accepts: '', valid HTML Container type.
	 *      @type string    $containerclasses   CSS Classes to use for the Pagination Container. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. Not applicable for this ShortCode.
	 * @param string $tag       The Shortcode tag. Value: 'pagination'.
	 */
	public function pagination( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'aria_current'          => 'page',
				'show_all'              => false,
				'end_size'              => 1,
				'mid_size'              => 2,
				'prev_next'             => true,
				'prev_text'             => 'Pre',
				'next_text'             => 'Next',
				'type'                  => 'plain',
				'add_args'              => '',
				'add_fragment'          => '',
				'before_page_number'    => '',
				'after_page_number'     => '',
				'instance'              => '',
				'pag_arg'               => 'item',
				'container'             => '',
				'containerclasses'      => '',
				'li_classes'            => '',
				'ul_classes'            => '',
				'a_classes'             => '',
				'current_classes'       => '',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			if ( 'show_all' === $key || 'prev_next' === $key ) {
				$atts[ $key ] = $this->sanitizer->sanitize( 'boolval', $value );
			} elseif ( 'end_size' === $key || 'mid_size' === $key ) {
				$atts[ $key ] = $this->sanitizer->sanitize( 'intval', $value );
			} elseif ( 'add_args' === $key ) {
				$value = $this->sanitizer->sanitize( 'text_field', $value );
				$add_args = array();
				if ( ! empty( $value ) ) {
					// If several args are passed.
					if ( strpos( $value, ',' ) !== false ) {
						$args_pre = explode( ',', $value );
						foreach ( $args_pre as $key => $arrval ) {
							list( $k, $v ) = explode( ':', $arrval );
							$add_args[ $k ] = $v;
						}
					} else {
						list( $k, $v ) = explode( ':', $value );
						$add_args[ $k ] = $v;
					}
				}
			} else {
				$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
			}
		}

		/**
		 * Pagination Parameters.
		 *
		 * Note: pag_arg must be set both in Loop and Pagination.
		 * Note: append .$instance to $paged value, to avoid pagination parameters to break.
		 * It will then be able to use ?page.instance=#.
		 * By default, this plugin does NOT ALLOW usage of 'page', or 'paged' URL parameters.
		 *
		 * Reviewers:
		 * It makes no sense to nonce this GET request, see comment in Tkt_Search_And_Filter_Posts_Query->set_query_args();
		 *
		 * @since 2.13.0
		 * @todo check nonce.
		 */
		$paged = $atts['pag_arg'];
		$page = isset( $_GET[ $paged ] ) ? absint( $_GET[ $paged ] ) : 1;// @codingStandardsIgnoreLine
		$max = $this->query->get_query_results()->max_num_pages;

		/**
		 * Add some wrapper for pagination.
		 * This is required for AJAX pagination.
		 * Without this, we have no target to listen to.
		 *
		 * @since 2.13.0
		 */
		$pag = '<' . $atts['container'] . ' class="' . $atts['containerclasses'] . '" id="' . $atts['instance'] . '_pagination">';
		$pag .= $this->paginate_helper( $atts, $page, $paged, $max, $add_args );
		$pag .= '</' . $atts['container'] . '>';

		if ( 'ajax' === $this->query->get_type() ) {
			wp_enqueue_script( $this->plugin_prefix . 'pagination' );
			$this->plugin_public->maybe_localize_script(
				$this->plugin_prefix . 'pagination',
				'tkt_ajax_pag_params',
				$atts['instance'],
				array(
					'is_doing_ajax' => true,
					'ajax_url'      => $this->sanitizer->sanitize( 'esc_url_raw', admin_url( 'admin-ajax.php' ) ),
					'nonce'         => wp_create_nonce( 'tkt_ajax_nonce' ),
					'instance'      => $atts['instance'],
					'atts'          => $atts,
					'page'          => $page,
				)
			);
		}

		/**
		 * When there are multiple loops in a page you must reset postdata.
		 * The loop did not yet reset, as we needed its data for pagination.
		 * Thus reset now, before the next loop initiates.
		 *
		 * @todo Check if this is enough or if we need to reset in the_loop as well.
		 * @since 2.13.0
		 */
		wp_reset_postdata();

		// Return our Pagination, all inputs are sanitized.
		return $pag;

	}

	/**
	 * Paginate Links Builder for TukuToi `[pagination]` ShortCode.
	 *
	 * Builds the Pagination Links array/list/plain.</br>
	 *
	 * Internal Function to build pagination links. Not intended for external Usage.
	 *
	 * @since    2.0.0
	 * @access private
	 * @param array  $atts      The ShortCode Attributes, @see {/public/class-tkt-search-and-filter-shortcodes.php} > `pagination` ShortCode.
	 * @param int    $page      The current page, populates $pargs['current']. Default: 1. Accepts: valid integer.
	 * @param string $paged     The Pagination URL parameter, populates $pargs['format']. Default: ''. Accepts: valid URL parameter.
	 * @param string $max       The Number Posts Found, populates $parg['total']. Default: ''. Accepts: valid integer.
	 * @param string $add_args  Additional URL parameters, populates $pargs['add_args']. Default: array. Accepts: array of URL parameter => value.
	 * @param string $base      The Pagination URL Base, populates $pargs['base']. Default: ''. Accepts: valid base URL.
	 * @return mixed $pag       The Pagination Links, either in plain, list or array mode.
	 */
	private function paginate_helper( $atts, $page = 1, $paged = '', $max, $add_args = array(), $base = false ) {
		/**
		 * Build the pagination parameters
		 *
		 * Note: Do NOT pass a url_parameter related to pagination.
		 * Note: Do NOT attempt to use reserved words such as 'page' or 'paged'.
		 * Note: Technically all input is already sanitized in either the ShortCode or ajax_pagination(),
		 * however for safeyt sake, since we move around the values outside the functions here, we sanitize again.
		 *
		 * @see https://docs.classicpress.net/reference/functions/paginate_links/
		 * @since 2.19.0
		 */
		$pargs = array(
			'format'                => '?' . $this->sanitizer->sanitize( 'key', $paged ) . '=%#%',
			'total'                 => $this->sanitizer->sanitize( 'absint', $max ),
			'current'               => $this->sanitizer->sanitize( 'absint', $page ),
			'aria_current'          => $this->sanitizer->sanitize( 'key', $atts['aria_current'] ),
			'show_all'              => $this->sanitizer->sanitize( 'boolval', $atts['show_all'] ),
			'end_size'              => $this->sanitizer->sanitize( 'absint', $atts['end_size'] ),
			'mid_size'              => $this->sanitizer->sanitize( 'absint', $atts['mid_size'] ),
			'prev_next'             => $this->sanitizer->sanitize( 'boolval', $atts['prev_next'] ),
			'prev_text'             => $this->sanitizer->sanitize( 'text_field', $atts['prev_text'] ),
			'next_text'             => $this->sanitizer->sanitize( 'text_field', $atts['next_text'] ),
			'type'                  => $this->sanitizer->sanitize( 'key', $atts['type'] ),
			'add_args'              => array_map( 'sanitize_key', $add_args ),
			'add_fragment'          => $this->sanitizer->sanitize( 'key', $atts['add_fragment'] ),
			'before_page_number'    => $this->sanitizer->sanitize( 'text_field', $atts['before_page_number'] ),
			'after_page_number'     => $this->sanitizer->sanitize( 'text_field', $atts['after_page_number'] ),
			'li_classes'            => $this->sanitizer->sanitize( 'esc_attr', $atts['li_classes'] ),
			'ul_classes'            => $this->sanitizer->sanitize( 'esc_attr', $atts['ul_classes'] ),
			'a_classes'             => $this->sanitizer->sanitize( 'esc_attr', $atts['a_classes'] ),
			'current_classes'       => $this->sanitizer->sanitize( 'esc_attr', $atts['current_classes'] ),
		);
		if ( false !== $base ) {
			$pargs['base'] = $this->sanitizer->sanitize( 'esc_url_raw', $base ) . '%_%';
		}

		$pag = better_paginate_links( $pargs );

		return $pag;

	}

	/**
	 * Alias for TukuToi `[pagination]` ShortCode used when doing AJAX.
	 *
	 * @see {/public/class-tkt-search-and-filter-shortcodes.php} > `pagination` ShortCode.</br>
	 *
	 * Internal Function used as Alias to the Pagination ShortCode when doing ajax.
	 * Public because AJAX requires public callback, but shouldn't be used. Use ShortCode instead.
	 *
	 * @since    2.19.0
	 * @access public
	 * @return void.
	 */
	public function tkt_ajax_pagination() {

		if ( ! is_array( $_POST )
			|| empty( $_POST )
			|| ! isset( $_POST['is_doing_ajax'] )
			|| empty( $_POST['is_doing_ajax'] )
			|| ! isset( $_POST['action'] )
			|| empty( $_POST['action'] )
			|| ! isset( $_POST['nonce'] )
			|| empty( $_POST['nonce'] )
			|| ! isset( $_POST['instance'] )
			|| empty( $_POST['instance'] )
			|| ! isset( $_POST['atts'] )
			|| empty( $_POST['atts'] )
			|| ! is_array( $_POST['atts'] )
		) {

			wp_send_json_error( 'Insufficient or Malformed Request', 400 );

		}

		$action     = sanitize_text_field( wp_unslash( $_POST['action'] ) );
		$is_ajax    = boolval( wp_unslash( $_POST['is_doing_ajax'] ) );

		if ( 'tkt_ajax_pagination' !== $action
			|| ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'tkt_ajax_nonce' )
			|| true !== $is_ajax
		) {

			wp_send_json_error( 'Unauthorized Request', 401 );

		}

		$instance   = sanitize_text_field( wp_unslash( $_POST['instance'] ) );
		foreach ( $_POST as $post_key => $post_value ) {
			if ( 'atts' === $post_key ) {
				foreach ( $post_value as $key => $value ) {

					if ( 'show_all' === $key || 'prev_next' === $key ) {
						$atts[ $key ] = $this->sanitizer->sanitize( 'boolval', $value );
					} elseif ( 'end_size' === $key || 'mid_size' === $key ) {
						$atts[ $key ] = $this->sanitizer->sanitize( 'intval', $value );
					} elseif ( 'add_args' === $key ) {
						$value = $this->sanitizer->sanitize( 'text_field', $value );
						$add_args = array();
						if ( ! empty( $value ) ) {
							// If several args are passed.
							if ( strpos( $value, ',' ) !== false ) {
								$args_pre = explode( ',', $value );
								foreach ( $args_pre as $key => $arrval ) {
									list( $k, $v ) = explode( ':', $arrval );
									$add_args[ $k ] = $v;
								}
							} else {
								list( $k, $v ) = explode( ':', $value );
								$add_args[ $k ] = $v;
							}
						}
					} else {
						$atts[ $key ] = $this->sanitizer->sanitize( 'text_field', $value );
					}
				}
			}
		}

		$page       = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;
		$paged      = isset( $atts['pag_arg'] ) ? $atts['pag_arg'] : '';
		$max        = isset( $_POST['max'] ) ? absint( wp_unslash( $_POST['max'] ) ) : -1;
		$add_args   = isset( $atts['add_args'] ) ? $atts['add_args'] : array();
		$base       = wp_doing_ajax() ? esc_url_raw( wp_get_referer() ) : '';

		$pag        = $this->paginate_helper( $atts, $page, $paged, $max, $add_args, $base );

		if ( empty( $pag ) ) {
			$pag = '';
		}

		wp_send_json_success( $pag );

	}

}
