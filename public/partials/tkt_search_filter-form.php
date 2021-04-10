<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public/partials
 */
?>

<form class="example" action="" type="GET">'
<input type="text" placeholder="Search.." name="title">'
<input type="hidden" value="<?php echo $this->instance ?>" name="instance">'

<button type="submit"><i class="fa fa-search"></i></button>'
</form>