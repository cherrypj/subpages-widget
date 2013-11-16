<?php
/**
 * Plugin Name: Sub-pages Multi-widget
 * Description: Show only the sub-pages, if the current page has sub-pages
 * Author: Michael Hessling
 * Version: 2.0
 * Author URI: http://cherrypj.com/

 * Changelog:
 ** Version 2.0:
 *** 2013  Michael Hessling  (email : mike@cherrypj.com)
 *** Converted to WP Multi-widget
 ** Version 1.1:
 *** 2007  Alper Haytabay  (email : alper@haytabay.de)
 *** http://wordpress.org/plugins/subpages-widget/
 *** Can only be used once site-wide

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

 * http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'subpages_load_widgets' );

/**
 * Register our widget.
 *
 * @since 0.1
 */
function subpages_load_widgets() {
	register_widget( 'Subpages_Widget' );
}

/**
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Subpages_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Subpages_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'subpages', 'description' => __('A widget that display child pages.', 'subpages') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'subpages-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'subpages-widget', __('Subpages Widget', 'subpages'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		global $post;
		extract( $args );

		if (is_page()) {

			/* Our variables from the widget settings. */
			$title = apply_filters('widget_title', $instance['title'] );
			$use_root = $instance['use_root'] ? 'true' : 'false';
			$only_first = $instance['only_first'] ? 'false' : 'true';
			$add_parent = $instance['add_parent'] ? 'true' : 'false';
  
			$root_post = $post;

			// find out the root page only when needed
			if ($use_root) {
				// find the root page and use it
				while ($root_post->post_parent != 0) {
					$root_post = &get_post($root_post->post_parent);
				}
			}

			// If only the first level should be used set the
			// depthStr to the correct value.
			// see http://codex.wordpress.org/Template_Tags/wp_list_pages
			$depthStr = '';
			if ($only_first && !$use_root) {
				$depthStr='&depth=1';
			}

			// the title 
			$title = $title.$root_post->post_title;

			$output = wp_list_pages('sort_column=menu_order'.$depthStr.'&title_li=&echo=0&child_of='.$root_post->ID);

			if (!empty($output)) {
				/* Before widget (defined by themes). */
				echo $before_widget;
				/* Display the widget title if one was input
				 * (before and after defined by themes). */
				if ( $title ) {
					echo $before_title . '<a href="'.get_permalink($root_post->ID).'">' . $title . '</a>' . $after_title;
				}

				echo '<ul>';
				echo $output;
				echo '</ul>';

				/* After widget (defined by themes). */
				echo $after_widget;
			}
		}
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		/* No need to strip tags for checkboxes. */
		$instance['use_root'] = $new_instance['use_root'];
		$instance['only_first'] = $new_instance['only_first'];
		$instance['add_parent'] = $new_instance['add_parent'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Parent Page Title', 'subpages'), 'use_root' => true, 'only_first' => false, 'add_parent' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Checkboxes -->
		<!--p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['use_root'], 'on' ); ?> id="<?php echo $this->get_field_id('use_root'); ?>" name="<?php echo $this->get_field_name('use_root'); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'use_root' ); ?>"><?php _e('Use root?', 'subpages'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['only_first'], 'on' ); ?> id="<?php echo $this->get_field_id('only_first'); ?>" name="<?php echo $this->get_field_name('only_first'); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'only_first' ); ?>"><?php _e('Only first level?', 'subpages'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['add_parent'], 'on' ); ?> id="<?php echo $this->get_field_id('add_parent'); ?>" name="<?php echo $this->get_field_name('add_parent'); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'add_parent' ); ?>"><?php _e('Add parent page?', 'subpages'); ?></label>
		</p -->

	<?php
	}
}

?>