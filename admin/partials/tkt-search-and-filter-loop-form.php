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
 $additional_options = new Tkt_Search_And_Filters_Gui( '$plugin_prefix', '$version', '$shortcode', '$declarations' );
?>
<form class="tkt-shortcode-form">
	
	<div class="ui-widget tkt-notice-widgets">
		<div class="ui-state-highlight ui-corner-all tkt-highlight-widget">
			<p>
				<span class="ui-icon ui-icon-info tkt-widget-icon"></span>
				This ShortCode bundles all Search Results. </br>
				It will process whatever you add within its enclosing ShortCode tags for <em>each</em> found result.</br>
				Remember that <strong>all</strong> Search Results <strong>must</strong> be wrapped by</br>
				this enclosing ShortCode.</br>
			</p>
		</div>
	</div>
	<div class="ui-widget tkt-notice-widgets">
		<div class="ui-state-error ui-corner-all tkt-error-widget">
			<p>
				<span class="ui-icon ui-icon-alert tkt-widget-icon"></span>
				<strong>IMPORTANT</strong></br>
				If you have more than one Search Results section on this page or post, or more than one Search Form, remember to add a</br>
				<strong>Custom Instance</strong>, so you can bind this Search Result to the correct Search Form.
			</p>
		</div>
	</div>
	<?php
	$this->text_fieldset( 'instance', 'Custom Instance', '', 'Custom instance to bind Results to specific Search Forms' );
	$this->select_fieldset( 'type', 'Query Type', 'post', array( $additional_options, 'alltypes_options' ) );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>
