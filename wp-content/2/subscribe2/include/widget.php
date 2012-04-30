<?php
class S2_Form_widget extends WP_Widget {
	/**
	Declares the Subscribe2 widget class.
	*/
	function S2_Form_widget() {
		$widget_ops = array('classname' => 's2_form_widget', 'description' => __('Sidebar Widget for Subscribe2', 'subscribe2') );
		$control_ops = array('width' => 250, 'height' => 300);
		$this->WP_Widget('s2_form_widget', __('Subscribe2 Widget'), $widget_ops, $control_ops);
	}
	
	/**
	Displays the Widget
	*/
	function widget($args, $instance) {
		extract($args);
		$title = empty($instance['title']) ? __('Subscribe2', 'subscribe2') : $instance['title'];
		$div = empty($instance['div']) ? 'search' : $instance['div'];
		$widgetprecontent = empty($instance['widgetprecontent']) ? '' : $instance['widgetprecontent'];
		$widgetpostcontent = empty($instance['widgetpostcontent']) ? '' : $instance['widgetpostcontent'];
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<div class=\"" . $div . "\">";
		global $mysubscribe2;
		$content = $mysubscribe2->filter('<!--subscribe2-->');
		if ( !empty($widgetprecontent) ) {
			echo $widgetprecontent;
		}
		echo $content;
		if ( !empty($widgetpostcontent) ) {
			echo $widgetpostcontent;
		}
		echo "</div>";
		echo $after_widget;
	}
	
	/**
	Saves the widgets settings.
	*/
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['div'] = strip_tags(stripslashes($new_instance['div']));
		$instance['widgetprecontent'] = strip_tags(stripslashes($new_instance['widgetprecontent']));
		$instance['widgetpostcontent'] = strip_tags(stripslashes($new_instance['widgetpostcontent']));
		
		return $instance;
	}
	
	/**
	Creates the edit form for the widget.
	*/
	function form($instance) {
		// set some defaults, getting any old options first
		$options = get_option('widget_subscribe2widget');
		if ( $options === false ) {
			$defaults = array('title' => 'Subscribe2', 'div' => 'search', 'widgetprecontent' => '', 'widgetpostcontent' => '');
		} else {
			$defaults = array('title' => $options['title'], 'div' => $options['div'], 'widgetprecontent' => $options['widgetprecontent'], 'widgetpostcontent' => $options['widgetpostcontent']);
			delete_option('widget_subscribe2widget');
		}
		// code to obtain old settings too
		$instance = wp_parse_args( (array) $instance, $defaults);
		
		$title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$div= htmlspecialchars($instance['div'], ENT_QUOTES);
		$widgetprecontent = htmlspecialchars($instance['widgetprecontent'], ENT_QUOTES);
		$widgetpostcontent = htmlspecialchars($instance['widgetpostcontent'], ENT_QUOTES);

		echo "<div\r\n";
		echo "<p><label for=\"" . $this->get_field_name('title') . "\">" . __('Title', 'subscribe2') . ":";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id('title') . "\" name=\"" . $this->get_field_name('title') . "\" type=\"text\" value=\"" . $title . "\" /></label></p>";
		echo "<p><label for=\"" . $this->get_field_name('div') . "\">" . __('Div class name', 'subscribe2') . ":";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id('div') . "\" name=\"" . $this->get_field_name('div') . "\" type=\"text\" value=\"" . $div . "\" /></label></p>";
		echo "<p><label for=\"" . $this->get_field_name('widgetprecontent') . "\">" . __('Pre-Content', 'subscribe2') . ":";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_name('widgetprecontent') . "\" name=\"" . $this->get_field_name('widgetprecontent') . "\" type=\"text\" value=\"" . $widgetprecontent . "\" /></label></p>";
		echo "<p><label for=\"" . $this->get_field_name('widgetpostcontent') . "\">" . __('Post-Content', 'subscribe2') . ":";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id('widgetpostcontent') . "\" name=\"" . $this->get_field_name('widgetpostcontent') . "\" type=\"text\" value=\"" . $widgetpostcontent . "\" /></label></p>";
		echo "</div\r\n";
	}
} // End S2_Form_widget class
?>