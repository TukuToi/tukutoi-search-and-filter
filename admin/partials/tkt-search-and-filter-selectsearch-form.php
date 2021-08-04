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
	$this->text_fieldset( 'label', 'Label', 'Submit', 'What Label to show on the Button' );
    $this->checkbox_fieldset( 'autofocus', 'Autofocus', '', 'Whether the button should autofocus (only one element per document can)', '' );
	$this->text_fieldset( 'form', 'Form ID', '', 'Defaults to parent form' );
    $this->select_fieldset( 'type', 'Button Type', 'submit', 'conditional_options' );
    $this->text_fieldset( 'name', 'Name', '', 'Pass a URL parameter when pressing this button' );
    $this->text_fieldset( 'value', 'Value of Name', '', 'Pass a URL parameter value to name when pressing this button' );
     $this->select_fieldset( 'formtarget', 'Target', '_self', 'conditional_options' );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>