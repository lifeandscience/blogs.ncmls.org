<?php
/*
Plugin Name: Explanatory Dictionary
Plugin URI: http://rubensargsyan.com/wordpress-plugin-explanatory-dictionary/
Description: This plugin is used when there are words, words expressions or sentences to be explained in the posts or pages of your wordpress blog. <a href="admin.php?page=explanatory-dictionary.php">Settings</a>
Version: 2.0
Author: Ruben Sargsyan
Author URI: http://rubensargsyan.com/
*/

/*  Copyright 2009 Ruben Sargsyan (email: info@rubensargsyan.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

$explanatory_dictionary_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
$explanatory_dictionary_plugin_title = "Explanatory Dictionary";
$explanatory_dictionary_plugin_prefix = "explanatory_dictionary_";
$explanatory_dictionary_table_name = $wpdb->prefix."explanatory_dictionary";

function explanatory_dictionary_load(){
	global $wpdb;
    $explanatory_dictionary_table_name = $wpdb->prefix."explanatory_dictionary";
    $explanatory_dictionary_plugin_prefix = "explanatory_dictionary_";
    $explanatory_dictionary_version = "2.0";

	$charset_collate = '';
	if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}

    if($wpdb->get_var("SHOW TABLES LIKE '$explanatory_dictionary_table_name'")!=$explanatory_dictionary_table_name){
	    $create_explanatory_dictionary_table = "CREATE TABLE $explanatory_dictionary_table_name(".
			"id INT(11) NOT NULL auto_increment,".
			"word VARCHAR(255) NOT NULL,".
			"explanation TEXT NOT NULL,".
			"PRIMARY KEY (id)) $charset_collate;";

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($create_explanatory_dictionary_table);
    }

    if(get_option("explanatory_dictionary_version")===false){
        set_default_explanatory_dictionary_settings();
        add_option("explanatory_dictionary_version",$explanatory_dictionary_version);
    }elseif(get_option("explanatory_dictionary_version")<$explanatory_dictionary_version){
        delete_option($explanatory_dictionary_plugin_prefix."settings");
        set_default_explanatory_dictionary_settings();
        update_option("explanatory_dictionary_version",$explanatory_dictionary_version);
    }
}

function explanatory_dictionary_menu(){
    if(function_exists('add_menu_page')){
		add_menu_page(__('Options', 'explanatory-dictionary'), __('Dictionary', 'explanatory-dictionary'), 'manage_options', basename(__FILE__), 'explanatory_dictionary_options');
	}
	if(function_exists('add_submenu_page')){
		add_submenu_page(basename(__FILE__), __('Options', 'explanatory-dictionary'), __('Options', 'explanatory-dictionary'), 'manage_options', basename(__FILE__), 'explanatory_dictionary_options');
		add_submenu_page(basename(__FILE__), __('Manage', 'explanatory-dictionary'), __('Manage', 'explanatory-dictionary'),  'manage_options', 'explanatory-dictionary/explanatory-dictionary-manage.php');
		add_submenu_page(basename(__FILE__), __('Donate', 'explanatory-dictionary'), __('Donate', 'explanatory-dictionary'),  'manage_options', 'explanatory-dictionary/explanatory-dictionary-donate.php');
	}
}

function set_explanatory_dictionary_settings($explanatory_dictionary_settings){
    global $explanatory_dictionary_plugin_prefix;

    add_option($explanatory_dictionary_plugin_prefix."settings",$explanatory_dictionary_settings);
}

function set_default_explanatory_dictionary_settings(){
    global $explanatory_dictionary_plugin_prefix;

    $explanatory_dictionary_external_css_file = "none";
    $explanatory_dictionary_width = "200px";
    $explanatory_dictionary_text_align = "justify";
    $explanatory_dictionary_font_size = "12px";
    $explanatory_dictionary_color = "#000000";
    $explanatory_dictionary_border_size = "1px";
    $explanatory_dictionary_border_color = "#214579";
    $explanatory_dictionary_background = "#FFFDF7";
    $explanatory_dictionary_padding = "5px 10px";
    $explanatory_dictionary_border_radius = "5px";
    $explanatory_dictionary_word_color = "#750909";
    $explanatory_dictionary_word_font_style = "normal";
    $explanatory_dictionary_word_font_weight = "normal";
    $explanatory_dictionary_word_text_decoration = "none";
    $explanatory_dictionary_unicode = "no";
    $explanatory_dictionary_exclude = "-1";
    $explanatory_dictionary_limit = "-1";
    $explanatory_dictionary_alphabet = "A B C D E F G H I J K L M N O P Q R S T U V W X Y Z";

    $explanatory_dictionary_settings = array($explanatory_dictionary_plugin_prefix."external_css_file"=>$explanatory_dictionary_external_css_file,$explanatory_dictionary_plugin_prefix."width"=>$explanatory_dictionary_width,$explanatory_dictionary_plugin_prefix."text_align"=>$explanatory_dictionary_text_align,$explanatory_dictionary_plugin_prefix."font_size"=>$explanatory_dictionary_font_size,$explanatory_dictionary_plugin_prefix."color"=>$explanatory_dictionary_color,$explanatory_dictionary_plugin_prefix."border_size"=>$explanatory_dictionary_border_size,$explanatory_dictionary_plugin_prefix."border_color"=>$explanatory_dictionary_border_color,$explanatory_dictionary_plugin_prefix."background"=>$explanatory_dictionary_background,$explanatory_dictionary_plugin_prefix."padding"=>$explanatory_dictionary_padding,$explanatory_dictionary_plugin_prefix."border_radius"=>$explanatory_dictionary_border_radius,$explanatory_dictionary_plugin_prefix."word_color"=>$explanatory_dictionary_word_color,$explanatory_dictionary_plugin_prefix."word_font_style"=>$explanatory_dictionary_word_font_style,$explanatory_dictionary_plugin_prefix."word_font_weight"=>$explanatory_dictionary_word_font_weight,$explanatory_dictionary_plugin_prefix."word_text_decoration"=>$explanatory_dictionary_word_text_decoration,$explanatory_dictionary_plugin_prefix."unicode"=>$explanatory_dictionary_unicode,$explanatory_dictionary_plugin_prefix."exclude"=>$explanatory_dictionary_exclude,$explanatory_dictionary_plugin_prefix."limit"=>$explanatory_dictionary_limit,$explanatory_dictionary_plugin_prefix."alphabet"=>$explanatory_dictionary_alphabet);

    set_explanatory_dictionary_settings($explanatory_dictionary_settings);
}

function update_explanatory_dictionary_settings($explanatory_dictionary_settings){
    global $explanatory_dictionary_plugin_prefix;

    $current_explanatory_dictionary_settings = get_explanatory_dictionary_settings();

    $explanatory_dictionary_settings = array_merge($current_explanatory_dictionary_settings,$explanatory_dictionary_settings);

    update_option($explanatory_dictionary_plugin_prefix."settings",$explanatory_dictionary_settings);
}

function get_explanatory_dictionary_settings(){
    global $explanatory_dictionary_plugin_prefix;

    $explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");

    return $explanatory_dictionary_settings;
}

function explanatory_dictionary_options(){
    global $explanatory_dictionary_plugin_url, $explanatory_dictionary_plugin_title, $explanatory_dictionary_plugin_prefix;
    ?>
    <script src="<?php echo($explanatory_dictionary_plugin_url.'javascript/jscolor.js'); ?>" type="text/javascript"></script>
    <?php

    if($_GET["page"]==basename(__FILE__)){
        if($_POST["action"]=="save"){
            if(trim($_POST[$explanatory_dictionary_plugin_prefix."external_css_file"])==""){
                $explanatory_dictionary_external_css_file = "none";
            }else{
                $explanatory_dictionary_external_css_file = trim($_POST[$explanatory_dictionary_plugin_prefix."external_css_file"]);
            }
            $explanatory_dictionary_width = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."width"]));
            if($_POST[$explanatory_dictionary_plugin_prefix."text_align"]=="left"){
                $explanatory_dictionary_text_align = "left";
            }elseif($_POST[$explanatory_dictionary_plugin_prefix."text_align"]=="right"){
                $explanatory_dictionary_text_align = "right";
            }else{
                $explanatory_dictionary_text_align = "justify";
            }
            $explanatory_dictionary_font_size = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."font_size"]));
            $explanatory_dictionary_color = "#".trim(strip_tags(substr($_POST[$explanatory_dictionary_plugin_prefix."color"],0,6)));
            $explanatory_dictionary_border_size = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."border_size"]));
            $explanatory_dictionary_border_color = "#".trim(strip_tags(substr($_POST[$explanatory_dictionary_plugin_prefix."border_color"],0,6)));
            $explanatory_dictionary_background = "#".trim(strip_tags(substr($_POST[$explanatory_dictionary_plugin_prefix."background"],0,6)));
            $explanatory_dictionary_padding = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."padding"]));
             $explanatory_dictionary_border_radius = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."border_radius"]));
            $explanatory_dictionary_word_color = "#".trim(strip_tags(substr($_POST[$explanatory_dictionary_plugin_prefix."word_color"],0,6)));
            if($_POST[$explanatory_dictionary_plugin_prefix."word_italic"]){
                $explanatory_dictionary_word_font_style = "italic";
            }else{
                $explanatory_dictionary_word_font_style = "normal";
            }
            if($_POST[$explanatory_dictionary_plugin_prefix."word_bold"]){
                $explanatory_dictionary_word_font_weight = "bold";
            }else{
                $explanatory_dictionary_word_font_weight = "normal";
            }
            if($_POST[$explanatory_dictionary_plugin_prefix."word_underline"]){
                $explanatory_dictionary_word_text_decoration = "underline";
            }else{
                $explanatory_dictionary_word_text_decoration = "none";
            }
            if($_POST[$explanatory_dictionary_plugin_prefix."unicode"]){
                $explanatory_dictionary_unicode = "yes";
            }else{
                $explanatory_dictionary_unicode = "no";
            }
            if(trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."exclude"]))==""){
                $explanatory_dictionary_exclude = "-1";
            }else{
                $explanatory_dictionary_exclude = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."exclude"]));
            }
            if(intval($_POST[$explanatory_dictionary_plugin_prefix."limit"])>0){
                $explanatory_dictionary_limit = trim(strip_tags($_POST[$explanatory_dictionary_plugin_prefix."limit"]));
            }else{
                $explanatory_dictionary_limit = "-1";
            }
            if(trim($_POST[$explanatory_dictionary_plugin_prefix."alphabet"])==""){
                $explanatory_dictionary_alphabet = "A B C D E F G H I J K L M N O P Q R S T U V W X Y Z";
            }else{
                $explanatory_dictionary_alphabet = trim($_POST[$explanatory_dictionary_plugin_prefix."alphabet"]);
            }

            $explanatory_dictionary_settings = array($explanatory_dictionary_plugin_prefix."external_css_file"=>$explanatory_dictionary_external_css_file,$explanatory_dictionary_plugin_prefix."width"=>$explanatory_dictionary_width,$explanatory_dictionary_plugin_prefix."text_align"=>$explanatory_dictionary_text_align,$explanatory_dictionary_plugin_prefix."font_size"=>$explanatory_dictionary_font_size,$explanatory_dictionary_plugin_prefix."color"=>$explanatory_dictionary_color,$explanatory_dictionary_plugin_prefix."border_size"=>$explanatory_dictionary_border_size,$explanatory_dictionary_plugin_prefix."border_color"=>$explanatory_dictionary_border_color,$explanatory_dictionary_plugin_prefix."background"=>$explanatory_dictionary_background,$explanatory_dictionary_plugin_prefix."padding"=>$explanatory_dictionary_padding,$explanatory_dictionary_plugin_prefix."border_radius"=>$explanatory_dictionary_border_radius,$explanatory_dictionary_plugin_prefix."word_color"=>$explanatory_dictionary_word_color,$explanatory_dictionary_plugin_prefix."word_font_style"=>$explanatory_dictionary_word_font_style,$explanatory_dictionary_plugin_prefix."word_font_weight"=>$explanatory_dictionary_word_font_weight,$explanatory_dictionary_plugin_prefix."word_text_decoration"=>$explanatory_dictionary_word_text_decoration,$explanatory_dictionary_plugin_prefix."unicode"=>$explanatory_dictionary_unicode,$explanatory_dictionary_plugin_prefix."exclude"=>$explanatory_dictionary_exclude,$explanatory_dictionary_plugin_prefix."limit"=>$explanatory_dictionary_limit,$explanatory_dictionary_plugin_prefix."alphabet"=>$explanatory_dictionary_alphabet);

            foreach($explanatory_dictionary_settings as $explanatory_dictionary_option => $explanatory_dictionary_option_value){
                if(empty($explanatory_dictionary_option_value)){
                    unset($explanatory_dictionary_settings[$explanatory_dictionary_option]);
                }
            }

            update_explanatory_dictionary_settings($explanatory_dictionary_settings);

            echo('<div id="message" class="updated fade"><p><strong>Options Saved.</strong></p></div>');
        }elseif($_POST["action"]=="reset"){
            delete_option($explanatory_dictionary_plugin_prefix."settings");

            echo('<div id="message" class="updated fade"><p><strong>Options Reset.</strong></p></div>');
        }
    }

    if(get_explanatory_dictionary_settings()===false){
        set_default_explanatory_dictionary_settings();
    }

    $explanatory_dictionary_settings = get_explanatory_dictionary_settings();

    ?>
    <div class="wrap">
      <h2><?php echo $explanatory_dictionary_plugin_title; ?> Options</h2>

      <form method="post">
        <table width="100%" border="0" id="explanatory_dictionary_settings_table">
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>External CSS file</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>external_css_file" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>external_css_file" type="text" style="width:500px;" value="<?php if(trim($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."external_css_file"])=="none"){ echo(""); }else{ echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."external_css_file"]); } ?>" />
            </td>
          </tr>
          <tr>
            <td><small>Set external CSS file URL. If an external CSS file is set, the style set in the "Explanatory Dictionary Options" will be ignored. To use the style set in "Explanatory Dictionary Options", leave this field empty.</small></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Tooltip Width</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>width" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>width" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."width"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>This option sets the width (Example: 200px, 100em ...) of the explanation tooltip.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Select The Text Align</strong></td>
            <td width="70%">
                <label for="<?php echo($explanatory_dictionary_plugin_prefix); ?>left" style="cursor: pointer">Left</label> <input type="radio" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>text_align" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>left"<?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."text_align"]=="left"){ echo(' checked="checked"'); } ?> value="left" /> <label for="<?php echo($explanatory_dictionary_plugin_prefix); ?>right" style="cursor: pointer">Right</label> <input type="radio" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>text_align" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>right"<?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."text_align"]=="right"){ echo(' checked="checked"'); } ?> value="right" /> <label for="<?php echo($explanatory_dictionary_plugin_prefix); ?>justify" style="cursor: pointer">Justify</label> <input type="radio" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>text_align" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>justify"<?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."text_align"]=="justify"){ echo(' checked="checked"'); } ?> value="justify" />
            </td>
          </tr>
          <tr>
            <td><small>Select the align of explanation tooltip text.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Font Size</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>font_size" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>font_size" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."font_size"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>This option sets the font size (Example: 15px, 10pt, 5em, 10% ...) of the explanation tooltip text.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Text Color</strong></td>
            <td width="70%">
                <input autocomplete="off" class="color" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>color" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>color" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."color"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>Click on the text field to set another color.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Border Size</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>border_size" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>border_size" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."border_size"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>This option sets the border size (Example: 1px, 0.8pt, 0.2em ...) of the explanation tooltip.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Border Color</strong></td>
            <td width="70%">
                <input autocomplete="off" class="color" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>border_color" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>border_color" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."border_color"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>Click on the text field to set another color.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Background Color</strong></td>
            <td width="70%">
                <input autocomplete="off" class="color" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>background" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>background" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."background"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>Click on the text field to set another color.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Padding</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>padding" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>padding" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."padding"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>This option sets the explanation tooltip text padding (Example: 5px 10px, 2pt 5pt 3pt 6pt, 0.5em ...) from borders.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explanatory Dictionary Border Radius</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>border_radius" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>border_radius" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."border_radius"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>This option sets the explanation tooltip border radius (Example: 5px, 2pt, 0.5em ...). This option is only for showing firefox browsers.</small></td>
          </tr>
          <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Set Explaining Word (Words Expression, Sentence) Color</strong></td>
            <td width="70%">
                <input autocomplete="off" class="color" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_color" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_color" type="text" style="width:100px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_color"]); ?>" />
            </td>
          </tr>
          <tr>
            <td><small>Click on the text field to set another color.</small></td>
          </tr>
          <tr>
            <td width="30%" valign="middle"><strong>Set Explaining Word (Words Expression, Sentence) Style</strong></td>
            <td width="70%">
                <label for="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_italic">Italic:</label> <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_italic" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_italic" type="checkbox" <?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_font_style"]=="italic"){ echo('checked="checked"'); } ?> />&nbsp;&nbsp;<label for="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_bold">Bold:</label> <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_bold" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_bold" type="checkbox" <?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_font_weight"]=="bold"){ echo('checked="checked"'); } ?> />&nbsp;&nbsp;<label for="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_underline">Underline:</label> <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_underline" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_underline" type="checkbox" <?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."word_text_decoration"]=="underline"){ echo('checked="checked"'); } ?> />
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="30%" valign="middle"><strong>Unicode</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>unicode" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>unicode" type="checkbox" <?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."unicode"]=="yes"){ echo('checked="checked"'); } ?> /> <small>Check if there is an unicode word in the explanatory dictionary.</small>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="30%" valign="middle"><strong>Exclude</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>exclude" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>exclude" type="text" style="width:100px;" value="<?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."exclude"]!="-1"){ echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."exclude"]); } ?>" /> <small>Write here (separate by commas) the pages or posts to exclude (Example: 1,10,25,77 ...).</small>
            </td>
          </tr>
          <tr>
            <td width="30%" valign="middle"><strong>Limit</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>limit" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>limit" type="text" style="width:100px;" value="<?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."limit"]>0){ echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."limit"]); } ?>" /> <small>Write here the number of words (words expressions, sentences) for showing the tooltips per page or post or leave empty for all the words (words expressions, sentences).</small>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="30%" valign="middle"><strong>Explanatory Dictionary Alphabet</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>alphabet" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>alphabet" type="text" style="width:500px;" value="<?php echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."alphabet"]); ?>" /> <small>Write here (separate by spaces) the alphabet of your explanatory dictionary (Example: A B C D E F G ...).</small>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
        <p class="submit">
          <input name="save" type="submit" value="Save" />
          <input type="hidden" name="action" value="save" />
        </p>
      </form>
      <form method="post">
        <p class="submit">
          <input name="reset" type="submit" value="Reset" />
          <input type="hidden" name="action" value="reset" />
        </p>
      </form>
    </div>
    <?php
}

function add_explanatory_dictionary_word($word,$explanation){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    $word = trim(mysql_real_escape_string($word));
    $explanation = trim(mysql_real_escape_string($explanation));

    if($word=="" || $explanation==""){
        return false;
    }

    $explanatory_dictionary = get_explanatory_dictionary();

    foreach($explanatory_dictionary as $existing_word){
        if($existing_word["word"]==$word){
            return false;
        }
    }

    $add = "INSERT INTO ".$explanatory_dictionary_table_name." (word, explanation) VALUES ('".$word."','".$explanation."')";

    $wpdb->query($add);

    return true;
}

function delete_explanatory_dictionary_word($deleting_word){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    $deleting_word = intval($deleting_word);

    if(empty($deleting_word)){
        return false;
    }

    $delete = "DELETE FROM ".$explanatory_dictionary_table_name." WHERE id='$deleting_word'";

    $wpdb->query($delete);

    return true;
}

function get_explanatory_dictionary($letter=""){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    $get = "SELECT * FROM ".$explanatory_dictionary_table_name." WHERE word LIKE '".$letter."%' ORDER BY word ASC;";

    $results = $wpdb->get_results($get);

    $explanatory_dictionary = array();
    $i = 0;

    if($results){
		foreach($results as $result) {
		    $explanatory_dictionary[$i]["id"] = intval($result->id);
			$explanatory_dictionary[$i]["word"] = stripslashes($result->word);
			$explanatory_dictionary[$i]["explanation"] = stripslashes($result->explanation);

            $i++;
		}
	}

    return $explanatory_dictionary;
}

function explanatory_dictionary_headers(){
  global $explanatory_dictionary_plugin_url, $explanatory_dictionary_plugin_prefix;
  ?>
  <script src="<?php echo($explanatory_dictionary_plugin_url.'javascript/domtooltips.js'); ?>" type="text/javascript"></script>
  <link rel="stylesheet" href="<?php echo($explanatory_dictionary_plugin_url); ?>css/explanatory-dictionary-style.css" type="text/css" />
  <?php
  $explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");
  if(trim($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."external_css_file"])=="none"){
  ?>
    <link rel="stylesheet" href="<?php echo($explanatory_dictionary_plugin_url); ?>css/explanatory-dictionary-style.php" type="text/css" />
  <?php
  }else{
  ?>
    <link rel="stylesheet" href="<?php echo(trim($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."external_css_file"])); ?>" type="text/css" />
  <?php
  }
  ?>
  <?php
}

function load_explanatory_dictionary(){
  ?>
  <script type="text/javascript"> load_domtooltips(); </script>
  <?php
}

function sort_explanatory_dictionary_by_words_count($text1,$text2){
  $text1_words_count =  explode(" ",trim(preg_replace("/\s+/"," ",$text1["word"])));
  $text2_words_count = explode(" ",trim(preg_replace("/\s+/"," ",$text2["word"])));

  if(count($text1_words_count)<count($text2_words_count)){
    return 1;
  }elseif(count($text1_words_count)==count($text2_words_count)){
    return 0;
  }else{
    return -1;
  }
}

add_filter('the_excerpt', 'add_explanatory_dictionary_words');
add_filter('the_content', 'add_explanatory_dictionary_words');

function add_explanatory_dictionary_words($content){
  global $explanatory_dictionary_plugin_prefix;

  $explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");

  if(explode(",",$explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."exclude"])!="-1"){
    $exclude = explode(",",$explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."exclude"]);
    if(array_search(get_the_ID(),$exclude)!==false){
      return $content;
    }
  }

  $explanatory_dictionary = get_explanatory_dictionary();

  usort($explanatory_dictionary, 'sort_explanatory_dictionary_by_words_count');

  $limit = intval($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."limit"]);

  if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."unicode"]=="yes"){
    for($i=0; $i<count($explanatory_dictionary); $i++){
        $replacing_characters = array('\\','#','/','$','!','@','^','%','(',')');
        $replacement_caracters = array('\\\\','\#','\/','\$','\!','\@','\^','\%','\(','\)');
        $explanatory_dictionary[$i]['word'] = str_replace($replacing_characters,$replacement_caracters,$explanatory_dictionary[$i]['word']);

        $content = preg_replace('/'.$explanatory_dictionary[$i]['word'].'(?![^<|\[]*[>|\]]|(\[no explanation\])*(\[\/no explanation\]))/ui', '<span class="domtooltips" title="'.$explanatory_dictionary[$i]['explanation'].'">$0</span>', $content, $limit);
    }
  }else{
    for($i=0; $i<count($explanatory_dictionary); $i++){
        $replacing_characters = array('\\','#','/','$','!','@','^','%','(',')');
        $replacement_caracters = array('\\\\','\#','\/','\$','\!','\@','\^','\%','\(','\)');
        $explanatory_dictionary[$i]['word'] = str_replace($replacing_characters,$replacement_caracters,$explanatory_dictionary[$i]['word']);

        $content = preg_replace('/\b'.$explanatory_dictionary[$i]['word'].'\b(?![^<|\[]*[>|\]]|(\[no explanation\])*(\[\/no explanation\]))/i', '<span class="domtooltips" title="'.$explanatory_dictionary[$i]['explanation'].'">$0</span>', $content, $limit);
    }
  }

  $no_explanation_tags = array("[no explanation]","[/no explanation]");

  $content = str_replace($no_explanation_tags,"",$content);

  return $content;
}

add_filter('the_content', 'add_explanatory_dictionary');

function add_explanatory_dictionary($content){
  global $explanatory_dictionary_plugin_prefix;

  $explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");

  $explanatory_dictionary_content = '<div class="explanatory_dictionary">';

  $explanatory_dictionary_alphabet_letters = $explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."alphabet"];
  $explanatory_dictionary_alphabet_letters = explode(" ",$explanatory_dictionary_alphabet_letters);
  $explanatory_dictionary_alphabet_first_letter = true;

  $explanatory_dictionary_alphabet = '<div class="explanatory_dictionary_alphabet">';

  foreach($explanatory_dictionary_alphabet_letters as $explanatory_dictionary_alphabet_letter){
    if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."unicode"]=="yes"){
        $explanatory_dictionary_alphabet_letter = mb_substr($explanatory_dictionary_alphabet_letter,0,1);
    }else{
        $explanatory_dictionary_alphabet_letter = substr($explanatory_dictionary_alphabet_letter,0,1);
    }

    if(!$explanatory_dictionary_alphabet_first_letter){
        $explanatory_dictionary_alphabet .= ' | ';
    }else{
        $explanatory_dictionary_alphabet_first_letter = false;
    }

    if(isset($_GET["explanatory_dictionary_alphabet_letter"]) && trim($_GET["explanatory_dictionary_alphabet_letter"])!=""){
        if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."unicode"]=="yes"){
            $explanatory_dictionary_selected_letter = mb_substr($_GET["explanatory_dictionary_alphabet_letter"],0,1);
        }else{
            $explanatory_dictionary_selected_letter = substr($_GET["explanatory_dictionary_alphabet_letter"],0,1);
        }
    }else{
        if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."unicode"]=="yes"){
            $explanatory_dictionary_selected_letter = mb_substr($explanatory_dictionary_alphabet_letters[0],0,1);
        }else{
            $explanatory_dictionary_selected_letter = substr($explanatory_dictionary_alphabet_letters[0],0,1);
        }
    }

    if($explanatory_dictionary_selected_letter==$explanatory_dictionary_alphabet_letter){
        $explanatory_dictionary_alphabet .= '<a href="'.add_query_arg("explanatory_dictionary_alphabet_letter", $explanatory_dictionary_alphabet_letter).'" class="explanatory_dictionary_selected_letter">'.$explanatory_dictionary_alphabet_letter.'</a>';
    }else{
        $explanatory_dictionary_alphabet .= '<a href="'.add_query_arg("explanatory_dictionary_alphabet_letter", $explanatory_dictionary_alphabet_letter).'">'.$explanatory_dictionary_alphabet_letter.'</a>';
    }

  }

  $explanatory_dictionary_alphabet .= '</div>';

  $explanatory_dictionary_content .= $explanatory_dictionary_alphabet;

  $explanatory_dictionary = get_explanatory_dictionary($explanatory_dictionary_selected_letter);

  for($i=0; $i<count($explanatory_dictionary); $i++){
    if($i==0){
        $explanatory_dictionary_content .= '<div class="explanatory_dictionary_first">';
    }elseif($i==(count($explanatory_dictionary)-1)){
        $explanatory_dictionary_content .= '<div class="explanatory_dictionary_last">';
    }else{
        $explanatory_dictionary_content .= '<div>';
    }

    $explanatory_dictionary_content .= '<span class="explanatory_dictionary_word">'.$explanatory_dictionary[$i]['word'].'</span> - <span class="explanatory_dictionary_explanation">'.$explanatory_dictionary[$i]['explanation'].'</span></div>';
  }

  $explanatory_dictionary_content .= '</div>';

  $content = str_replace("[explanatory dictionary]",$explanatory_dictionary_content,$content);

  return $content;
}

add_action('plugins_loaded','explanatory_dictionary_load');
add_action('admin_menu', 'explanatory_dictionary_menu');
add_action('wp_head', 'explanatory_dictionary_headers');
add_action('wp_footer', 'load_explanatory_dictionary');
?>