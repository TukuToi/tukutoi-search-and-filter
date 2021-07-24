<?php
/**
 * Provide a default template for the Loop ites
 *
 * Copy this file to the theme and call it within TukuToi Search & Filter Method to design the results loops
 *
 * Use $this->post instead of $post global.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_search_filter
 * @subpackage Tkt_search_filter/public/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="loop-item-wrapper">
	<div class="loop-item-heading">
		<h3><?php echo esc_html( the_title() ); ?></h3>
	</div>
	<div class="loop-item-content">	
		<?php echo wp_kses( get_post_field( 'post_content', $this->post->ID ), 'post' ); ?>
		<?php echo esc_html( get_post_meta( 'maybe-custom-field', $this->post->ID, true ) ); ?>
	</div>
</div>
		
