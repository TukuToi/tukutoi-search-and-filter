<?php
/**
 * This file contains Functions of ClassicPress and WordPress
 * which are plain and simple obnoxious, abonimal, horrend and
 * should be immediately fixed in core.
 *
 * Instead of re-nventing the wheel we should be able to use this CMS.
 * However in some cases it is truly implemented in hoorific ways.
 * This file attempts to fix this.
 * The function names are precisely the same as in Core, just that `better_`
 * has been prefixed.
 * All changes made to the functions are documented on top of each function.
 *
 * @todo This should really be ported all to CP. Forget WP, they dont care anyway.
 *
 * @since 2.0.0
 * @package Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/includes
 */

/**
 * ### Start Comment to document Fix ###
 * This function is not offerng a way to alter the `value` of the User Dropdown options.
 * The fix adds the option to alter the value by any data from the _user data object_ which should be used to start with,
 * but WP Somehow chose to do things awkwardly when it comes to users
 *
 * Also this fix includes a better output build. The Horrific syntax used in core for this is ununderstandable to me.
 * It includes as well a new attribute to actually escape the output echoed. I mean, wtf.
 *
 * Needless to say that also now it is WPCS compliant, which it was NOT at time of editing.
 * If code comments would support emojy, I would :puke: at this point.
 *
 * And for those who say this is useless or so:
 * 1. wp_dropdown_categories( string|array $args = '' ) does HAVE the value_field attribute which exactly what is needed.
 * 2. It IS useful because these dropdowns are not only used for silly thigns.
 * 3. Current core code was/is messy, unsafe and incomplete, as usual when it comes to USERS in WP.
 *
 * Additionally this thing does not even support multiple selects, for whatever reasons.
 * Yes, the HTML has a filter applied, but it is not the goal of filters to FIX things, it is the goal of filters
 * to ALTER EXISTING content. Thus, we added a option for multi, too, which was there already but literally did NOTHING.
 * I mean NOTHING. AT. ALL.
 * ### End Comment to document Fix ###
 *
 * Create dropdown HTML content of users.
 *
 * The content can either be displayed, which it is by default or retrieved by
 * setting the 'echo' argument. The 'include' and 'exclude' arguments do not
 * need to be used; all users will be displayed in that case. Only one can be
 * used, either 'include' or 'exclude', but not both.
 *
 * The available arguments are as follows:
 *
 * @since WP-2.3.0
 * @since WP-4.5.0 Added the 'display_name_with_login' value for 'show'.
 * @since WP-4.7.0 Added the `$role`, `$role__in`, and `$role__not_in` parameters.
 * @since TUKUTOI Added the `$value_field` parameter.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a drop-down of users.
 *     See WP_User_Query::prepare_query() for additional available arguments.
 *
 *     @type string       $show_option_all         Text to show as the drop-down default (all).
 *                                                 Default empty.
 *     @type string       $show_option_none        Text to show as the drop-down default when no
 *                                                 users were found. Default empty.
 *     @type int|string   $option_none_value       Value to use for $show_option_non when no users
 *                                                 were found. Default -1.
 *     @type string       $hide_if_only_one_author Whether to skip generating the drop-down
 *                                                 if only one user was found. Default empty.
 *     @type string       $orderby                 Field to order found users by. Accepts user fields.
 *                                                 Default 'display_name'.
 *     @type string       $order                   Whether to order users in ascending or descending
 *                                                 order. Accepts 'ASC' (ascending) or 'DESC' (descending).
 *                                                 Default 'ASC'.
 *     @type array|string $include                 Array or comma-separated list of user IDs to include.
 *                                                 Default empty.
 *     @type array|string $exclude                 Array or comma-separated list of user IDs to exclude.
 *                                                 Default empty.
 *     @type bool|int     $multi                   Whether to skip the ID attribute on the 'select' element.
 *                                                 Accepts 1|true or 0|false. Default 0|false.
 *     @type string       $show                    User data to display. If the selected item is empty
 *                                                 then the 'user_login' will be displayed in parentheses.
 *                                                 Accepts any user field, or 'display_name_with_login' to show
 *                                                 the display name with user_login in parentheses.
 *                                                 Default 'display_name'.
 *     @type string       $value_field             User `data` object values to use as selection option **value**.
 *                                                 Accepts any User `data` object property.
 *                                                 Default to user ID.
 *     @type int|bool     $echo                    Whether to echo or return the drop-down. Accepts 1|true (echo)
 *                                                 or 0|false (return). Default 1|true.
 *     @type int|bool     $allowed_html            Array of allowed HTML to pass to wp_kses if echo.
 *     @type int          $selected                Which user ID should be selected. Default 0. If $value is passed,
 *                                                 $selected should pass a valid value to match $value.
 *     @type bool         $include_selected        Whether to always include the selected user ID in the drop-
 *                                                 down. Default false.
 *     @type string       $name                    Name attribute of select element. Default 'user'.
 *     @type string       $id                      ID attribute of the select element. Default is the value of $name.
 *     @type string       $class                   Class attribute of the select element. Default empty.
 *     @type int          $blog_id                 ID of blog (Multisite only). Default is ID of the current blog.
 *     @type string       $who                     Which type of users to query. Accepts only an empty string or
 *                                                 'authors'. Default empty.
 *     @type string|array $role                    An array or a comma-separated list of role names that users must
 *                                                 match to be included in results. Note that this is an inclusive
 *                                                 list: users must match *each* role. Default empty.
 *     @type array        $role__in                An array of role names. Matched users must have at least one of
 *                                                 these roles. Default empty array.
 *     @type array        $role__not_in            An array of role names to exclude. Users matching one or more of
 *                                                 these roles will not be included in results. Default empty array.
 * }
 * @return string String of HTML content.
 */
function better_dropdown_users( $args = '' ) {

	$defaults = array(
		'show_option_all'         => '',
		'show_option_none'        => '',
		'hide_if_only_one_author' => '',
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => '',
		'exclude'                 => '',
		'multi'                   => '',
		'show'                    => 'display_name',
		'echo'                    => 1,
		'allowed_html'            => array(),
		'value_field'             => 'ID',
		'selected'                => 0,
		'name'                    => 'user',
		'class'                   => '',
		'id'                      => '',
		'blog_id'                 => get_current_blog_id(),
		'who'                     => '',
		'include_selected'        => false,
		'option_none_value'       => -1,
		'role'                    => '',
		'role__in'                => array(),
		'role__not_in'            => array(),
		'data_attr'				  => ''
	);

	$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

	$r = wp_parse_args( $args, $defaults );

	$data_attr = $r['data_attr'];

	$query_args = wp_array_slice_assoc( $r, array( 'blog_id', 'include', 'exclude', 'orderby', 'order', 'who', 'role', 'role__in', 'role__not_in' ) );

	$fields = array( 'ID', 'user_login' );

	$show = ! empty( $r['show'] ) ? $r['show'] : 'display_name';
	if ( 'display_name_with_login' === $show ) {
		$fields[] = 'display_name';
	} else {
		$fields[] = $show;
	}

	$value_field = ! empty( $r['value_field'] ) ? $r['value_field'] : 'ID';

	$query_args['fields'] = $fields;

	$show_option_all = $r['show_option_all'];
	$show_option_none = $r['show_option_none'];
	$option_none_value = $r['option_none_value'];

	/**
	 * Filters the query arguments for the list of users in the dropdown.
	 *
	 * @since WP-4.4.0
	 *
	 * @param array $query_args The query arguments for get_users().
	 * @param array $r          The arguments passed to wp_dropdown_users() combined with the defaults.
	 */
	$query_args = apply_filters( 'wp_dropdown_users_args', $query_args, $r );

	$users = get_users( $query_args );

	$output = '';
	if ( ! empty( $users ) && ( empty( $r['hide_if_only_one_author'] ) || count( $users ) > 1 ) ) {
		$name = esc_attr( $r['name'] );
		$multiple = $r['multi'];
		$id = $r['id'] ? " id='" . esc_attr( $r['id'] ) . "'" : " id='$name'";
		if ( ! empty( $r['multi'] ) ) {
			$name = $name . '[]';
		}
		$output = "<select data-tkt-ajax-src='{$data_attr}' name='{$name}' {$id} class='" . $r['class'] . "' $multiple>\n";

		if ( $show_option_all ) {
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$_selected = selected( $option_none_value, $r['selected'], false );
			$output .= "\t<option value='" . esc_attr( $option_none_value ) . "'$_selected>$show_option_none</option>\n";
		}

		if ( $r['include_selected'] && ( $r['selected'] > 0 ) ) {
			$found_selected = false;
			$r['selected'] = (int) $r['selected'];
			foreach ( (array) $users as $user ) {
				$user->ID = (int) $user->ID;

				if ( $user->ID === $r['selected'] ) {
					$found_selected = true;
				}
			}

			if ( ! $found_selected ) {
				$selected_user = get_userdata( $r['selected'] );
				if ( $selected_user ) {
					$users[] = $selected_user;
				} else {
					// The selected user ID was not found as a valid user.
					$users[] = (object) array(
						'_invalid' => true,
						'ID'       => $r['selected'],
					);
				}
			}
		}

		foreach ( (array) $users as $user ) {
			if ( ! empty( $user->_invalid ) ) {
				/* translators: user ID */
				$display = sprintf( __( '(Invalid user: ID=%d)' ), $user->ID );
			} elseif ( 'display_name_with_login' === $show ) {
				/* translators: 1: display name, 2: user_login */
				$display = sprintf( _x( '%1$s (%2$s)', 'user dropdown' ), $user->display_name, $user->user_login );
			} elseif ( ! empty( $user->$show ) ) {
				$display = $user->$show;
			} else {
				$display = '(' . $user->user_login . ')';
			}
			if ( 'ID' === $r['value_field'] ) {
				$_selected = selected( $user->ID, $r['selected'], false );
				$output .= '<option value="' . esc_attr( $user->ID ) . '" ' . $_selected . '>' . esc_html( $display ) . '</option>"';
			} else {
				$user_object = get_userdata( $user->ID );
				$_selected = selected( $user_object->$value_field, $r['selected'], false );
				$output .= '<option value="' . esc_attr( $user_object->$value_field ) . '" ' . $_selected . '>' . esc_html( $display ) . '</option>"';
			}
		}

		$output .= '</select>';
	}

	/**
	 * Filters the wp_dropdown_users() HTML output.
	 *
	 * @since WP-2.3.0
	 *
	 * @param string $output HTML output generated by wp_dropdown_users().
	 */
	$html = apply_filters( 'wp_dropdown_users', $output );

	if ( $r['echo'] ) {
		echo wp_kses( $html, $r['allowed_html'] );
	}
	return $html;
}

/**
 * ### Start Comment to document Fix ###
 * Taxonomy Dropdown is a little less messy than user Dropdown but misses the multiselect.
 * The suggested solution wp_terms_checklist is a huge garbage, almost worse than user Dropdown.
 * Long enough I have battled the messy crap it generates and yu need to subclass its walker to even alter
 * just one tiny aspect of its HTML.
 * This is just an example of what shit programming is. Not saying I can do better, but obviously this is not
 * the standards I would expect from WP or CP.
 * ### End Comment to document Fix ###
 * Display or retrieve the HTML dropdown list of categories.
 *
 * The 'hierarchical' argument, which is disabled by default, will override the
 * depth argument, unless it is true. When the argument is false, it will
 * display all of the categories. When it is enabled it will use the value in
 * the 'depth' argument.
 *
 * @since WP-2.1.0
 * @since WP-4.2.0 Introduced the `value_field` argument.
 * @since WP-4.6.0 Introduced the `required` argument.
 *
 * @param string|array $args {
 *     Optional. Array or string of arguments to generate a categories drop-down element. See WP_Term_Query::__construct()
 *     for information on additional accepted arguments.
 *
 *     @type string       $show_option_all   Text to display for showing all categories. Default empty.
 *     @type string       $show_option_none  Text to display for showing no categories. Default empty.
 *     @type string       $option_none_value Value to use when no category is selected. Default empty.
 *     @type string       $orderby           Which column to use for ordering categories. See get_terms() for a list
 *                                           of accepted values. Default 'id' (term_id).
 *     @type bool         $pad_counts        See get_terms() for an argument description. Default false.
 *     @type bool|int     $show_count        Whether to include post counts. Accepts 0, 1, or their bool equivalents.
 *                                           Default 0.
 *     @type bool|int     $echo              Whether to echo or return the generated markup. Accepts 0, 1, or their
 *                                           bool equivalents. Default 1.
 *     @type bool|int     $hierarchical      Whether to traverse the taxonomy hierarchy. Accepts 0, 1, or their bool
 *                                           equivalents. Default 0.
 *     @type int          $depth             Maximum depth. Default 0.
 *     @type int          $tab_index         Tab index for the select element. Default 0 (no tabindex).
 *     @type string       $name              Value for the 'name' attribute of the select element. Default 'cat'.
 *     @type string       $id                Value for the 'id' attribute of the select element. Defaults to the value
 *                                           of `$name`.
 *     @type string       $class             Value for the 'class' attribute of the select element. Default 'postform'.
 *     @type int|string   $selected          Value of the option that should be selected. Default 0.
 *     @type string       $value_field       Term field that should be used to populate the 'value' attribute
 *                                           of the option elements. Accepts any valid term field: 'term_id', 'name',
 *                                           'slug', 'term_group', 'term_taxonomy_id', 'taxonomy', 'description',
 *                                           'parent', 'count'. Default 'term_id'.
 *     @type string|array $taxonomy          Name of the category or categories to retrieve. Default 'category'.
 *     @type bool         $hide_if_empty     True to skip generating markup if no categories are found.
 *                                           Default false (create select element even if no categories are found).
 *     @type bool         $required          Whether the `<select>` element should have the HTML5 'required' attribute.
 *                                           Default false.
 *     @type string       $multi             Whether the `<select>` element should have the HTML5 'multiple' attribute
 *                                           and an `[]` in the name.
 *                                           Accepts '' or 'multiple'.
 *                                           Default ''.
 * }
 * @return string HTML content only if 'echo' argument is 0.
 */
function better_dropdown_categories( $args = '' ) {
	$defaults = array(
		'show_option_all'   => '',
		'show_option_none'  => '',
		'orderby'           => 'id',
		'order'             => 'ASC',
		'show_count'        => 0,
		'hide_empty'        => 1,
		'child_of'          => 0,
		'exclude'           => '',
		'echo'              => 1,
		'selected'          => 0,
		'hierarchical'      => 0,
		'name'              => 'cat',
		'id'                => '',
		'class'             => 'postform',
		'depth'             => 0,
		'tab_index'         => 0,
		'taxonomy'          => 'category',
		'hide_if_empty'     => false,
		'option_none_value' => -1,
		'value_field'       => 'term_id',
		'required'          => false,
		'multiple'          => '',
		'allowed_html'      => array(),
		'data_attr'			=> '',
	);

	$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;

	// Back compat.
	if ( isset( $args['type'] ) && 'link' == $args['type'] ) {
		_deprecated_argument(
			__FUNCTION__,
			'WP-3.0.0',
			/* translators: 1: "type => link", 2: "taxonomy => link_category" */
			sprintf( wp_kses_post( __( '%1$s is deprecated. Use %2$s instead.' ) ), '<code>type => link</code>', '<code>taxonomy => link_category</code>' )
		);
		$args['taxonomy'] = 'link_category';
	}

	$r = wp_parse_args( $args, $defaults );
	$option_none_value = $r['option_none_value'];

	$data_attr = $r['data_attr'];

	if ( ! isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
		$r['pad_counts'] = true;
	}

	$tab_index = $r['tab_index'];

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 ) {
		$tab_index_attribute = " tabindex=\"$tab_index\"";
	}

	// Avoid clashes with the 'name' param of get_terms().
	$get_terms_args = $r;
	unset( $get_terms_args['name'] );
	$categories = get_terms( $r['taxonomy'], $get_terms_args );

	$name = esc_attr( $r['name'] );
	$class = esc_attr( $r['class'] );
	$id = $r['id'] ? esc_attr( $r['id'] ) : $name;
	$required = $r['required'] ? 'required' : '';
	$multiple = esc_attr( $r['multi'] );
	if ( 'multiple' === $multiple ) {
		$name = $name . '[]';
	}
	if ( ! $r['hide_if_empty'] || ! empty( $categories ) ) {
		$output = "<select data-tkt-ajax-src='$data_attr' $required name='$name' id='$id' class='$class' $tab_index_attribute $multiple>\n";
	} else {
		$output = '';
	}
	if ( empty( $categories ) && ! $r['hide_if_empty'] && ! empty( $r['show_option_none'] ) ) {

		/**
		 * Filters a taxonomy drop-down display element.
		 *
		 * A variety of taxonomy drop-down display elements can be modified
		 * just prior to display via this filter. Filterable arguments include
		 * 'show_option_none', 'show_option_all', and various forms of the
		 * term name.
		 *
		 * @since WP-1.2.0
		 *
		 * @see wp_dropdown_categories()
		 *
		 * @param string       $element  Category name.
		 * @param WP_Term|null $category The category object, or null if there's no corresponding category.
		 */
		$show_option_none = apply_filters( 'list_cats', $r['show_option_none'], null );
		$output .= "\t<option value='" . esc_attr( $option_none_value ) . "' selected='selected'>$show_option_none</option>\n";
	}

	if ( ! empty( $categories ) ) {

		if ( $r['show_option_all'] ) {

			/** This filter is documented in wp-includes/category-template.php */
			$show_option_all = apply_filters( 'list_cats', $r['show_option_all'], null );
			$selected = ( '0' === strval( $r['selected'] ) ) ? " selected='selected'" : '';
			$output .= "\t<option value='0'$selected>$show_option_all</option>\n";
		}

		if ( $r['show_option_none'] ) {

			/** This filter is documented in wp-includes/category-template.php */
			$show_option_none = apply_filters( 'list_cats', $r['show_option_none'], null );
			$selected = selected( $option_none_value, $r['selected'], false );
			$output .= "\t<option value='" . esc_attr( $option_none_value ) . "'$selected>$show_option_none</option>\n";
		}

		if ( $r['hierarchical'] ) {
			$depth = $r['depth'];  // Walk the full depth.
		} else {
			$depth = -1; // Flat.
		}
		$output .= walk_category_dropdown_tree( $categories, $depth, $r );
	}

	if ( ! $r['hide_if_empty'] || ! empty( $categories ) ) {
		$output .= "</select>\n";
	}
	/**
	 * Filters the taxonomy drop-down output.
	 *
	 * @since WP-2.1.0
	 *
	 * @param string $output HTML output.
	 * @param array  $r      Arguments used to build the drop-down.
	 */
	$output = apply_filters( 'wp_dropdown_cats', $output, $r );

	if ( $r['echo'] ) {
		echo wp_kses( $output, $r['allowed_html'] );
	}
	return $output;
}
