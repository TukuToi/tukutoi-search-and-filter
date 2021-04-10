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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

		  <div class="testimonial">
			<div class="testimonial-content">	
              <div class="testimonial-icon">
				<i class="fa fa-quote-left"></i>	
              </div>
              <div class="description">
                <h3><?php echo the_title();?></h3>
                <p class="starability-result" data-rating="<?php echo get_post_meta($this->post->ID, 'wpcf-rating-stars', true)?>"></p>
                <?php echo get_post_meta($this->post->ID, 'wpcf-feedback', true)?>
              </div>
            </div>
            <h3 class="title"><?php echo get_post_meta($this->post->ID, 'wpcf-name', true)?></h3>
            <span class="post"><?php echo types_render_field("website", array("output" => "normal", "separator" => ", "))?></span>
          </div>
		