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
	$this->text_fieldset( 'placeholder', 'Placeholder', 'Search', 'What placeholder to show in the Search Input' );
	$this->text_fieldset( 'urlparam', 'URL Paramter', '', 'URL paramter to use for this Search Input' );
	?>
	<div class="ui-widget tkt-notice-widgets">
		<div class="ui-state-error ui-corner-all tkt-highlight-widget">
			<p>
				<span class="ui-icon ui-icon-alert tkt-widget-icon"></span>
				Do not use any WordPress reserved words for URL parameters</br>
				Classic examples are <code>author</code>, <code>page</code></br>
				WordPress tries to rewrite those terms to an archive or pagination permalink
			</p>
		</div>
	</div>
	<?php
	$this->select_fieldset( 'searchby', 'Search Argument', '', array( $additional_options, 'queryvars_options' ) );
	?>
	<div class="ui-widget tkt-notice-widgets">
		<div class="ui-state-highlight ui-corner-all tkt-highlight-widget">
			<p>
				<span class="ui-icon ui-icon-info tkt-widget-icon"></span>
				Remember this is a Text Search Input. It might not make sense to query it by multiple Category IDs, for example ;)
			</p>
		</div>
	</div>
	<?php
	$this->text_fieldset( 'customid', 'Custom ID', '', 'Custom ID to use for the Search Input' );
	$this->text_fieldset( 'customclasses', 'Custom Classes', '', 'Custom Classes to use for the Search Input' );
	$this->checkbox_fieldset( 'quotes', 'Quotes', '"', 'What Quotes to use in ShortCodes (Useful when using ShortCodes in other ShortCodes attributes, or in HTML attributes', '' );
	?>
</form>
