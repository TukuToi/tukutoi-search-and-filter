<?php
/**
 * Provide a Custom Search Inputs template
 *
 * You can copy this file as a start to your Theme folder and then call it in the TukuToi Search API Functions.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public/partials
 */
?>

<form class="example" action="" type="GET">
	<input type="text" placeholder="Search.." name="title">
	<input type="hidden" value="<?php echo $this->instance ?>" name="instance">
	<button type="submit"><i class="fa fa-search"></i></button>
</form>