<?php
/*
WP SyntaxHighlighter Widget
by Redcocker
Last modified: 2011/12/14
License: GPL v2
http://www.near-mint.com/blog/
*/

class WPSyntaxHighlighterWidget extends WP_Widget {

	public $wp_syntaxhighlighter_widget = 0;

	function WPSyntaxHighlighterWidget() {
		$widget_ops = array('classname' => 'WPSyntaxHighlighterWidget', 'description' => __("WP SyntaxHighlighter Widget shows highlighted code.", "wp_sh"));
		parent::WP_Widget(false, $name = __("WP SyntaxHighlighter Widget", "wp_sh"), $widget_ops);	
	}

	function widget($args, $instance) {
		global $wp_sh_setting_opt;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$sourcecode = $instance['sourcecode'];
		$sourcecode = wp_sh_replace_marker(wp_sh_escape_code($sourcecode));
		// Apply shortcode parser
		if ($wp_sh_setting_opt['wiget_shorcode'] == 1) {
			$sourcecode = wp_sh_do_shortcode(wp_sh_add_extra_bracket($sourcecode));
		}
		?>
			<?php
				echo $before_widget;

				if ($title) {
					echo $before_title . $title . $after_title;
				}

				if ($sourcecode != "") {
					echo $sourcecode;
				}

				echo $after_widget;
			?>
			<?php wp_sh_load_scripts_by_shortcut(); ?>
		<?php
	}

	function update($new_instance, $old_instance) {
		$new_instance['sourcecode'] = wp_sh_escape_code($new_instance['sourcecode']);
		return $new_instance;
	}

	function form($instance) {
		global $wp_sh_plugin_url, $wp_sh_setting_opt;
		$title = esc_attr($instance['title']);
		$sourcecode = wp_sh_escape_code($instance['sourcecode']);
		if ($wp_sh_setting_opt['wiget_tag'] == "shortcode") {
			$tag = "shorcode";
		} else {
			$tag = "pre";
		}
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:"); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			<?php
			echo "<p>";
			if ($wp_sh_setting_opt['lib_version'] == '3.0') {
				$languages = get_option('wp_sh_language3');
			} elseif ($wp_sh_setting_opt['lib_version'] == '2.1') {
				$languages = get_option('wp_sh_language2');
			}
			$gutter = $wp_sh_setting_opt['gutter'];
			if (is_array($languages)) {
				asort($languages);
				foreach ($languages as $key => $val) {
					if ($val[1] == 'true' || $val[1] =='added') {
						echo "<a href=\"javascript:void(0);\" onclick=\"surroundHTML('".$key."','".$this->get_field_id('sourcecode')."','".$gutter."','".$wp_sh_setting_opt['first_line']."','".$tag."','0');\">".$val[0]."</a> | ";
					}
				}
			}
			echo "</p>";
			?>
			<textarea rows="16" cols="20" class="widefat" id="<?php echo $this->get_field_id('sourcecode'); ?>" name="<?php echo $this->get_field_name('sourcecode'); ?>"><?php echo $sourcecode; ?></textarea>
			<input type="hidden" name="widget-width" class="widget-width" value="400" />
			<input type="hidden" name="widget-height" class="widget-height" value="700" />
		<?php 
	}

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("WPSyntaxHighlighterWidget");'));

?>