<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Search_And_Filter
 * @subpackage Tkt_Search_And_Filter/admin/partials
 */

?>
<?php
/**
 * We need to add some data to the existing TukuToi ShortCode GUI Selector Options.
 *
 * @since 2.0.0
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-tkt-search-and-filters-gui.php';
$additional_options = new Tkt_Search_And_Filters_Gui( new Tkt_Search_And_Filter_Declarations() );
?>
<form class="tkt-shortcode-form">
	<?php
	$this->text_fieldset( 'instance', 'Instance', '', 'The unique instance of Search and Loop this pagination has to control' );
	$this->text_fieldset( 'pag_arg', 'Pagination Argument', '', 'The URL parameter to use for the paginatio. Can not be \'page\' or \'paged\'' );
	$this->checkbox_fieldset( 'show_all', 'Show all Pages', '0', 'Whether to show all pages in the pagination or not', '' );
	$this->text_fieldset( 'end_size', 'End Size', '1', 'How many pagination items to show on start and end of the list' );
	$this->text_fieldset( 'mid_size', 'Mid Size', '2', 'How many pagination items to show on each side of the current page' );
	$this->checkbox_fieldset( 'prev_next', 'Show Pre/Next', '1', 'Whether to show the Pre and Next pagination navigation', '' );
	$this->text_fieldset( 'prev_text', 'Previous Label', 'Pre', 'The label of the Previous Page link' );
	$this->text_fieldset( 'next_text', 'Next Label', 'Next', 'The label of the Next Page link' );
	$this->select_fieldset( 'type', 'HTML Structure', '', array( $additional_options, 'pagtype_options' ) );
	$this->text_fieldset( 'before_page_number', 'Before Page Number', '', 'Text to appear before the paginaton Numbere Link Anchor' );
	$this->text_fieldset( 'after_page_number', 'After Page Number', '', 'Text to appear after the paginaton Numbere Link Anchor' );
	$this->text_fieldset( 'aria_current', 'The aria-current', 'page', 'What value to use for the aria-current of the current Page' );
	$this->text_fieldset( 'ul_classes', 'Custom \'ul\' Classes', '', 'Custom Classes to use for the \'ul\' Pagination List Element' );
	$this->text_fieldset( 'li_classes', 'Custom \'li\' Classes', '', 'Custom Classes to use for each Pagination List Item' );
	$this->text_fieldset( 'a_classes', 'Custom \'a\' Classes', '', 'Custom Classes to use for each Pagination Link (\'a href\') Item' );
	$this->text_fieldset( 'current_classes', 'aria-current span Classes', '', 'Custom Classes to use for the aria-current span Pagination List' );
	$this->text_fieldset( 'container', 'Container Type', 'div', 'Type of HTML container to wrap Pagination HTML into' );
	$this->text_fieldset( 'containerclasses', 'Container Classes', '', 'Custom CSS Classes to use for the Container' );

	$this->text_fieldset( 'add_args', 'Additional URL parameter', '', 'Additional URL parameters to append. Format \'param_one:value-one,param_two:value-two\'' );
	$this->text_fieldset( 'add_fragment', 'Trailing Fragment', '', 'You can append a URL fragment at the end of the pagination Link' );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>
