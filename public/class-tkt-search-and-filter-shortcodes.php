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
	 */
	public function __construct( $plugin_prefix, $version, $declarations ) {

		$this->plugin_prefix    = $plugin_prefix;
		$this->version          = $version;
		$this->declarations     = $declarations;

		$this->sanitizer        = new Tkt_Search_And_Filter_Sanitizer( $this->plugin_prefix, $this->version, $this->declarations );
		$this->query            = new Tkt_Search_And_Filter_Posts_Query( $this->sanitizer );

	}

	/**
	 * TukuToi `[searchtemplate]` ShortCode.
	 *
	 * Outputs the Search Form.</br>
	 * Mandatory to use when adding Search ShortCodes.
	 * Group: `internal`.
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
		 *
		 * @since 2.0.0
		 */
		global $tkt_src_fltr;
		$tkt_src_fltr['instance'] = $atts['instance'];

		// Build the Form Start.
		$src_form_start = '<form id="' . $atts['customid'] . '" class="' . $atts['customclasses'] . '" type="GET">';

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
	 * Group: `internal`.
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
	 *      @type string    $customid       ID to use for the Search Form. Default: ''. Accepts: '', valid HTML ID.
	 *      @type string    $customclasses  CSS Classes to use for the Search Form. Default: ''. Accepts: '', valid HTML CSS classes, space delimited.
	 *      @type string    $type           For what type the query results are for. Default: 'post'. Accepts: valid post type, valid taxonomy type, valid user role.
	 *      @type string    $error          The no results found message: Default ''. Accepts: valid string or HTML.
	 * }
	 * @param mixed  $content   ShortCode enclosed content. TukuToi ShortCodes, ShortCodes and HTML. No TukuToi Search ShortCodes.
	 * @param string $tag       The Shortcode tag. Value: 'loop'.
	 */
	public function loop( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'instance'      => 'my_instance',
				'customid'      => '',
				'customclasses' => '',
				'type'          => 'post',
				'error'         => 'No Results Found',
			),
			$atts,
			$tag
		);

		// Sanitize the User input atts.
		foreach ( $atts as $key => $value ) {
			if ( 'error' === $key ) {
				$atts['error'] = $this->sanitizer->sanitize( 'post_kses', $value );
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
				'posts_per_page'         => -1,
				'order'                  => 'DESC',
				'orderby'                => 'date',
				'cache_results'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true,
			);

		}

		// Merge the default Query args into the User Args. Overwrite defaults with User Input.
		$query_args = array_merge( $default_query_args, $atts );

		/**
		 * Get all results of the Query.
		 *
		 * @todo This currently only works with Post Query, make it work with User and Term query.
		 *
		 * @since 2.0.0
		 */
		$results = $this->query->render_results( $query_args, $atts['instance'] );

		/**
		 * Loop over the results and build the output.
		 *
		 * @since 2.0.0
		 */
		$out = '';
		if ( $results->have_posts() ) {
			while ( $results->have_posts() ) {
				$results->the_post();
				/**
				 * We need to run the content thru ShortCodes Processor, otherwise ShortCodes are not expanded.
				 *
				 * @todo check if we can sanitize the $content here with $content = $this->sanitizer->sanitize( 'post_kses', $content );
				 * @since 2.0.0
				 */
				$processed_content = apply_filters( 'tkt_scs_pre_process_shortcodes', $content );
				$processed_content = do_shortcode( $content, false );
				$out .= $this->sanitizer->sanitize( 'post_kses', $processed_content );
			}
			wp_reset_postdata();
		} else {
			/**
			 * No results found.
			 *
			 * This is already sanitized.
			 *
			 * @since 2.0.0
			 */
			$out = $atts['error'];
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
	 *      @type string    $url_param      URL parameter to use. Default: '_s'. Accepts: valid URL search parameter.
	 *      @type string    $search_by      Query Parameter. Default: 's'. Accepts: valid WP Query Parmater.
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
				'url_param'     => '_s',
				'search_by'     => 's',
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
		$tkt_src_fltr['search_by'][ $atts['url_param'] ] = $atts['search_by'];

		// Build our Serach input.
		$search = '<label for="' . $atts['customid'] . '">' . $atts['placeholder'] . '</label>';
		$search = '<input type="text" id="' . $atts['customid'] . '" placeholder="' . $atts['placeholder'] . '" name="' . $atts['url_param'] . '">';

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
	 * `[selectsearch placeholder="Search..." url_param="_s" search_by="title" type="s2multi" customid="my_id" customclasses="class_one classtwo"]`</br>
	 * For possible attributes see the Parameters > $atts section below or use the TukuToi ShortCodes GUI.
	 *
	 * @since    2.0.0
	 * @param array  $atts {
	 *      The ShortCode Attributes.
	 *
	 *      @type string    $placeholder    The Search Input Placeholder. Default: 'Search...'. Accepts: valid string.
	 *      @type string    $url_param      URL parameter to use. Default: '_s'. Accepts: valid URL search parameter.
	 *      @type string    $search_by      Query Parameter. Default: 's'. Accepts: valid WP Query Parmater.
	 *      @type string    $type           Type of Select. Default: 'simple'. Accepts: 'simple', 'multi', 's2simple', 's2multi'.
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
				'url_param'     => '_s',
				'search_by'     => 's',
				'type'          => 'simple', // Simple, MultiSelect, and/or S2.
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
		$tkt_src_fltr['search_by'][ $atts['url_param'] ] = $atts['search_by'];

		/**
		 * This currently is hardcoded. Provide a dynamic method to populate these.
		 *
		 * @see example https://www.smashingmagazine.com/2016/03/advanced-wordpress-search-with-wp_query/
		 * @since 2.0.0
		 */
		$options_arr = array(
			'1'     => 'hello-world',
			'340'   => 'fsddas',
		);

		/**
		 * Build our select input.
		 *
		 * @todo Currently this only supports a simple Select. Support multiple and as well S2.
		 * @since 2.0.0
		 */
		$options = '<option value="">' . $atts['placeholder'] . '</option>';
		foreach ( $options_arr as $value => $label ) {
			$options .= '<option value="' . $value . '">' . $label . '</option>';
		}
		$search = '<label for="' . $atts['customid'] . '">' . $atts['placeholder'] . '</label>';
		$search .= '<select name="' . $atts['url_param'] . '" id="' . $atts['customid'] . '">';
		$search .= $options;
		$search .= '</select>';

		/**
		 * Return the search.
		 * Currently all sanitized but when we will have dynamic options need to revisit sanitization.
		 *
		 * @todo check sanitization once dynamic options are added.
		 */
		return $search;

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
		$button .= '>' . $atts['label'] . '</button>';

		// Return our Button, all inputs are sanitized.
		return $button;

	}

}
