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
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 * @param      string $declarations    The Configuration object.
	 * @param      string $query    The Query object.
	 * @param      string $sanitizer    The Sanitization object.
	 */
	public function __construct( $plugin_prefix, $version, $declarations, $query, $sanitizer ) {

		$this->plugin_prefix    = $plugin_prefix;
		$this->version          = $version;
		$this->declarations     = $declarations;

		$this->sanitizer        = $sanitizer;
		$this->query            = $query;

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
	 *      @type string    $type           The Type of search (AJAX or full page reload). Default: reload. Accepts: '', 'ajax'.
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

		// Build the Form Start.
		$src_form_start = '<form id="' . $atts['customid'] . '" class="' . $atts['customclasses'] . '" type="GET" data-tkt-ajax-src-form="' . $atts['instance'] . '">';

		/**
		 * We need to run the content thru ShortCodes Processor, otherwise ShortCodes are not expanded.
		 *
		 * @todo check if we can sanitize the $content here with $content = $this->sanitizer->sanitize( 'post_kses', $content );
		 * @since 2.0.0
		 */
		$content = apply_filters( 'tkt_scs_pre_process_shortcodes', $content );
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
	 *      @type string    $error          The no results found message. Default: ''. Accepts: valid string or HTML.
	 *      @type string    $pag_arg        The pagination URL parameter. Default: ''. Accepts: valid URL parameter (must match pagination)
	 *      @type string    $posts_per_page Amount of posts per page if paginated. Default: -1 (all). Accepts: valid integer.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi ShortCodes, ShortCodes and HTML. No TukuToi Search ShortCodes.
	 * @param string $tag       The Shortcode tag. Value: 'loop'.
	 */
	public function loop( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'instance'      => 'my_instance',
				'type'          => 'post',
				'error'         => 'No Results Found',
				'pag_arg'       => '',
				'posts_per_page' => '',
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
			} elseif ( 'type' === $atts['type'] ) {
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
				$post_type = array( $atts['type'] );
			}
			$default_query_args = array(
				'post_type'              => $post_type,
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
		$query_args = array_merge( $default_query_args, $atts );
		$this->query->set_query_args( $query_args );
		$out = $this->query->the_loop( $content, $atts['error'] );
		// If it is an AJAX search.
		if ( 'ajax' === $this->query->get_type() ) {
			/**
			 * AJAX not needed unless we are in a AJAX Search.
			 *
			 * Save the users some headaches, usually plugins just throw the scripts on all pages...
			 *
			 * Here we:
			 * 1. Enqueue TukuToi AJAX if needed.
			 * 2. Localise TukuToi AJAX object if needed.
			 *
			 * @since 2.10.0
			 */
			wp_enqueue_script( 'tkt-ajax-js' );
			wp_localize_script(
				'tkt-ajax-js',
				'tkt_ajax_params',
				array(
					'is_doing_ajax' => true,
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
					'content'   => $content,
					'instance'  => $atts['instance'],
					'query_args' => $query_args,
					'error'     => $atts['error'],
				)
			);
			// If it is an AJAX search we need some container to push data results to.
			$out = '<div id="' . $atts['instance'] . '" data-tkt-ajax-src-loop="' . $atts['instance'] . '"></div>';
		}

		// Return our output. Already sanitized.
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
		$search = '<input type="text" id="' . $atts['customid'] . '" placeholder="' . $atts['placeholder'] . '" name="' . $atts['urlparam'] . '" data-tkt-ajax-src="' . $atts['searchby'] . '">';

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
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $placeholder    The Search Input Placeholder. Default: 'Search...'. Accepts: valid string.
	 *      @type string    $urlparam       URL parameter to use. Default: '_s'. Accepts: valid URL search parameter.
	 *      @type string    $searchby       Query Parameter. Default: 's'. Accepts: valid WP Query Parmater.
	 *      @type string    $type           Type of Select. Default: 'single'. Accepts: 'single', 'multiple', 'singleS2', 'multipleS2'.
	 *      @type string    $customid       ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses  CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
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
		 * @since 2.0.0
		 */
		global $tkt_src_fltr;
		$tkt_src_fltr['searchby'][ $atts['urlparam'] ] = $atts['searchby'];

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
		 * @since 2.0.0
		 */
		$post_query_vars = $this->declarations->data_map( 'post_query_vars' );
		$value_field    = isset( $post_query_vars[ $atts['searchby'] ]['field'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['field'] )
						: null;
		$query_type     = isset( $post_query_vars[ $atts['searchby'] ]['type'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['type'] )
						: null;
		$callback       = isset( $post_query_vars[ $atts['searchby'] ]['cback'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['cback'] )
						: null;
		$values         = isset( $post_query_vars[ $atts['searchby'] ]['vals'] )
						? $this->sanitizer->sanitize( 'text_field', $post_query_vars[ $atts['searchby'] ]['vals'] )
						: null;
		switch ( $post_query_vars[ $atts['searchby'] ]['type'] ) {
			case 'user':
				$select_form = better_dropdown_users(
					array(
						'show_option_all'   => empty( $multiple_value ) ? $atts['placeholder'] : null,
						'multi'             => $multiple_value,
						'show'              => 'display_name_with_login',
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
			case 'tag':
			case 'taxonomy':
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
				* @todo Currently this only supports a simple Select. Support multiple and as well S2.
				* @todo Add postmeta support.
				* @since 2.0.0
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
					// The Post Type or Post Types do exit but may not be an array if only one was passed.
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
				$select_form .= '<select name="' . $atts['urlparam'] . $multiple_name . '" id="' . $atts['customid'] . '"' . $multiple_value . ' data-tkt-ajax-src="' . $atts['searchby'] . '">';
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
		 * 2. Enqueue Select2 JS if needed.
		 * 3. Enqueue TukuToi JS if needed.
		 * 4. Localise TukuToi JS if needed.
		 *
		 * @since 2.10.0
		 */
		if ( 'multipleS2' === $atts['type'] || 'singleS2' === $atts['type'] ) {
			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
			wp_localize_script(
				'tkt-script',
				'tkt_select2',
				array(
					'placeholder'   => $atts['placeholder'],
					'instance'      => $atts['customid'],
				)
			);
			wp_enqueue_script( 'tkt-script' );
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
		 * Currently only submit button works/
		 *
		 * @todo this needs same process as a search input, as well as reset button logic.
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

		// Return our Button, all inputs are sanitized.
		return $button;

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
	 *      @type string    $customclasses      CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 *      @type string    $pag_arg            The URL parameter to use for this pagination. Default: item. Accepts: valid string but NOT 'page' or 'paged'.
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
				'instance'              => 'my_instance',
				'customclasses'         => '',
				'pag_arg'               => 'item',
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
		 * By what parameter we paginate.
		 *
		 * Note: this must be set both in Loop and pagination.
		 * Note: append .$instance to $paged value, to avoid pagination parameters to break.
		 * It will then be able to use ?page.instance=#.
		 * By default, this plugin does NOT ALLOW usage of 'page', or 'paged' URL parameters.
		 *
		 * @since 2.13.0
		 */
		$paged = $atts['pag_arg'];
		$page = isset( $_GET[ $paged ] ) ? absint( $_GET[ $paged ] ) : 1;

		// Get the query results this pagination should paginate.
		$query_results = $this->query->get_query_results();

		/**
		 * Build the pagination parameters
		 *
		 * Note: Do NOT use the 'base' argument.
		 * Note: Do NOT pass a url_parameter related to pagination.
		 * Note: Do NOT attempt to use reserved words such as 'page' or 'paged'.
		 */
		$pargs = array(
			'format'                => '?' . $paged . '=%#%',
			'total'                 => $query_results->max_num_pages,
			'current'               => $page,
			'aria_current'          => $atts['aria_current'],
			'show_all'              => $atts['show_all'],
			'end_size'              => $atts['end_size'],
			'mid_size'              => $atts['mid_size'],
			'prev_next'             => $atts['prev_next'],
			'prev_text'             => $atts['prev_text'],
			'next_text'             => $atts['next_text'],
			'type'                  => $atts['type'],
			'add_args'              => $add_args, // $atts['add_args'],
			'add_fragment'          => $atts['add_fragment'],
			'before_page_number'    => $atts['before_page_number'],
			'after_page_number'     => $atts['after_page_number'],
		);

		/**
		 * Add some wrapper for pagination.
		 * This is required for AJAX pagination.
		 * Without this, we have no target to listen to.
		 *
		 * @todo Let the user customize this class and perhaps even add an ID instead.
		 * @since 2.13.0
		 */
		$pag = '<div class="genre-filter-navigation">';
		$pag .= paginate_links( $pargs );
		$pag .= '</div>';

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

}
