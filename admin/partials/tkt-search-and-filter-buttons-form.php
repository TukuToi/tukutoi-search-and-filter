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
	$this->text_fieldset( 'label', 'Label', 'Search', 'What Label to use on the button' );
	$this->text_fieldset( 'urlparam', 'URL Parameter', '', 'URL parameter to use for this Button, if used as a Search Input' );
	$this->text_fieldset( 'value', 'URL Parameter Value', '', 'URL parameter value to use for this Button, if used as a Search Input' );
	$this->select_fieldset( 'searchby', 'Query By', '', array( $additional_options, 'queryvars_options' ) );
	$this->select_fieldset( 'type', 'Button Type', 'submit', array( $additional_options, 'buttontype_options' ) );
	$this->checkbox_fieldset( 'autofocus', 'Autofocus', '', 'Automatically Focus the button when the page is loaded. Works only for one Button each Document', '' );
	$this->text_fieldset( 'form', 'Form ID', '', 'Form ID to submit (if used as submit button). Defaults to the Ancestor Form', '' );
	$this->text_fieldset( 'formtarget', 'Form Target', '_self', 'The targe when submitting the form with this button (Self, Blank)', '' );
	$this->text_fieldset( 'customid', 'Custom ID', '', 'Custom ID to use for the Search Input' );
	$this->text_fieldset( 'customclasses', 'Custom Classes', '', 'Custom Classes to use for the Search Input' );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>

