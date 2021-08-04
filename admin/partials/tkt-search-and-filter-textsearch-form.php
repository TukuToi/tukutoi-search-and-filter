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
<form class="tkt-shortcode-form">
	<?php
	$this->text_fieldset( 'placeholder', 'Placeholder', 'Search', 'What placeholder to show in the Search Input' );
	$this->text_fieldset( 'customid', 'Custom ID', '', 'Custom ID to use for the Search Input' );
	$this->text_fieldset( 'customclasses', 'Custom Classes', '', 'Custom Classes to use for the Search Input' );
    $this->text_fieldset( 'instance', 'Custom Instance', '', 'Custom instance to bind Filters to specific Loops' );
    $this->text_fieldset( 'template', 'Custom Template', '', 'Custom template used to build the search input.' );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>
