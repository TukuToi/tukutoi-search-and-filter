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
	
	<div class="ui-widget tkt-notice-widgets">
		<div class="ui-state-highlight ui-corner-all tkt-highlight-widget">
			<p>
				<span class="ui-icon ui-icon-info tkt-widget-icon"></span>
				This ShortCode bundles all Search Inputs. </br>
				It will produce a HTML Form with an ID and classes as you set in this GUI.</br>
				Remember that <strong>all</strong> Search Inputs <strong>must</strong> be wrapped by</br>
				this enclosing ShortCode.</br>
			</p>
		</div>
	</div>
	<div class="ui-widget tkt-notice-widgets">
		<div class="ui-state-error ui-corner-all tkt-error-widget">
			<p>
				<span class="ui-icon ui-icon-alert tkt-widget-icon"></span>
				<strong>IMPORTANT</strong></br>
				If you have more than one Search Results section on this page or post, remember to add a</br>
				<strong>Custom Instance</strong>, so you can bind this Search Form to the correct Search Results.
			</p>
		</div>
	</div>

	<?php
	$this->text_fieldset( 'customid', 'Custom ID', '', 'Custom ID to use for the Search Input' );
	$this->text_fieldset( 'customclasses', 'Custom Classes', '', 'Custom Classes to use for the Search Input' );
	$this->text_fieldset( 'instance', 'Custom Instance', '', 'Custom instance to bind Filters to specific Loops' );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>
