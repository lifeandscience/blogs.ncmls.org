<?php
header("content-type: text/css");
$path  = '';

if(!defined('WP_LOAD_PATH')){
	$root = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/';

	if(file_exists($root.'wp-load.php')){
        define('WP_LOAD_PATH',$root);
	}else{
        if(file_exists($path.'wp-load.php')){
            define('WP_LOAD_PATH',$path);
        }else{
            exit("Cannot find wp-load.php");
        }
	}
}

require_once(WP_LOAD_PATH.'wp-load.php');

global $wpdb;

$explanatory_dictionary_plugin_prefix = "explanatory_dictionary_";
$explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");
?>
.domtooltips_tooltip{
  width: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."width"]); ?>;
  text-align: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."text_align"]); ?>;
  font-size: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."font_size"]); ?>;
  color: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."color"]); ?>;
  font-style: normal;
  font-weight: normal;
  text-decoration: none;
  text-transform: none;
  letter-spacing: normal;
  line-height: normal;
  border: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."border_size"]); ?> solid <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."border_color"]); ?>;
  background: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."background"]); ?>;
  padding: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."padding"]); ?>;
  -moz-border-radius: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."border_radius"]); ?>; 
}

.domtooltips{
  color: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_color"]); ?>;
  font-style: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_font_style"]); ?>;
  font-weight: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_font_weight"]); ?>;
  text-decoration: <?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_text_decoration"]); ?>;
  cursor: help;
}