<?php
/*
Plugin Name: Explanatory Dictionary
Plugin URI: http://rubensargsyan.com/wordpress-plugin-explanatory-dictionary/
Description: This plugin is used when there are words, words expressions or sentences to be explained via tooltips in the posts or pages of your wordpress blog. <a href="admin.php?page=explanatory-dictionary.php">Settings</a>
Version: 3.0.1
Author: Ruben Sargsyan
Author URI: http://rubensargsyan.com/
*/

/*  Copyright 2011 Ruben Sargsyan (email: info@rubensargsyan.com)

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
    $explanatory_dictionary_version = "3.0.1";

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
            "synonyms_and_forms TEXT NOT NULL,".
			"explanation TEXT NOT NULL,".
			"PRIMARY KEY (id)) $charset_collate;";

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($create_explanatory_dictionary_table);
    }

    if(get_option("explanatory_dictionary_version")===false){
        set_default_explanatory_dictionary_settings();
        add_option("explanatory_dictionary_version",$explanatory_dictionary_version);
    }elseif(get_option("explanatory_dictionary_version")<$explanatory_dictionary_version){
        if(get_option("explanatory_dictionary_version")<"3.0"){
            $create_explanatory_dictionary_not_exists_fields = "ALTER TABLE $explanatory_dictionary_table_name ADD synonyms_and_forms TEXT NOT NULL AFTER word";

            $wpdb->query($create_explanatory_dictionary_not_exists_fields);
        }else{
            delete_option($explanatory_dictionary_plugin_prefix."settings");
            set_default_explanatory_dictionary_settings();
        }

        update_option("explanatory_dictionary_version",$explanatory_dictionary_version);
    }
}

function explanatory_dictionary_menu(){
    if(function_exists('add_menu_page')){
		add_menu_page('Manage', 'Dictionary', 'manage_options', 'explanatory-dictionary/explanatory-dictionary-manage.php');
	}
	if(function_exists('add_submenu_page')){
	    add_submenu_page('explanatory-dictionary/explanatory-dictionary-manage.php', 'Manage', 'Manage',  'manage_options', 'explanatory-dictionary/explanatory-dictionary-manage.php');
		add_submenu_page('explanatory-dictionary/explanatory-dictionary-manage.php', 'Options', 'Options', 'manage_options', 'explanatory-dictionary/explanatory-dictionary-options.php');
		add_submenu_page('explanatory-dictionary/explanatory-dictionary-manage.php', 'Donate', 'Donate',  'manage_options', 'explanatory-dictionary/explanatory-dictionary-donate.php');
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

function add_explanatory_dictionary_word($word,$synonyms_and_forms,$explanation){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    $word = trim(mysql_real_escape_string($word));
    $explanation = trim(mysql_real_escape_string($explanation));

    if($word=="" || $explanation==""){
        return false;
    }

    $synonyms_and_forms_array = explode(",",$synonyms_and_forms);
    $synonyms_and_forms_filtered_array = array();

    foreach($synonyms_and_forms_array as $synonym_or_form){
        $synonym_or_form = trim($synonym_or_form);

        if($synonym_or_form!=""){
            $synonyms_and_forms_filtered_array[] = $synonym_or_form;
        }
    }

    $explanatory_dictionary = get_explanatory_dictionary();

    foreach($explanatory_dictionary as $existing_word){
        $existing_word_synonyms_and_forms = explode(",",$existing_word["synonyms_and_forms"]);

        if($existing_word["word"]==$word || in_array($word,$synonyms_and_forms_filtered_array) || in_array($existing_word["word"],$synonyms_and_forms_filtered_array) || in_array($word,$existing_word_synonyms_and_forms) || array_intersect($synonyms_and_forms_filtered_array,$existing_word_synonyms_and_forms)){
            return false;
        }
    }

    $synonyms_and_forms = mysql_real_escape_string(implode(",",array_unique($synonyms_and_forms_filtered_array)));

    $add = "INSERT INTO ".$explanatory_dictionary_table_name." (word, synonyms_and_forms, explanation) VALUES ('".$word."','".$synonyms_and_forms."','".$explanation."')";

    $wpdb->query($add);

    return true;
}

function edit_explanatory_dictionary_word($word_id,$word,$synonyms_and_forms,$explanation){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    if(!is_numeric($word_id) || $word_id<1){
        return false;
    }

    $word = trim(mysql_real_escape_string($word));
    $explanation = trim(mysql_real_escape_string($explanation));

    if($word=="" || $explanation==""){
        return false;
    }

    $synonyms_and_forms_array = explode(",",$synonyms_and_forms);
    $synonyms_and_forms_filtered_array = array();

    foreach($synonyms_and_forms_array as $synonym_or_form){
        $synonym_or_form = trim($synonym_or_form);

        if($synonym_or_form!=""){
            $synonyms_and_forms_filtered_array[] = $synonym_or_form;
        }
    }

    $explanatory_dictionary = get_explanatory_dictionary();

    foreach($explanatory_dictionary as $existing_word){
        if($existing_word["id"]!=$word_id){
            $existing_word_synonyms_and_forms = explode(",",$existing_word["synonyms_and_forms"]);

            if($existing_word["word"]==$word || in_array($word,$synonyms_and_forms_filtered_array) || in_array($existing_word["word"],$synonyms_and_forms_filtered_array) || in_array($word,$existing_word_synonyms_and_forms) || array_intersect($synonyms_and_forms_filtered_array,$existing_word_synonyms_and_forms)){
                return false;
            }
        }
    }

    $synonyms_and_forms = mysql_real_escape_string(implode(",",array_unique($synonyms_and_forms_filtered_array)));

    $edit = "UPDATE ".$explanatory_dictionary_table_name." SET word='".$word."', synonyms_and_forms='".$synonyms_and_forms."', explanation='".$explanation."' WHERE id='".$word_id."';";

    $wpdb->query($edit);

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
            $explanatory_dictionary[$i]["synonyms_and_forms"] = stripslashes($result->synonyms_and_forms);
			$explanatory_dictionary[$i]["explanation"] = stripslashes($result->explanation);

            $i++;
		}
	}

    return $explanatory_dictionary;
}

function get_explanatory_dictionary_word($id){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    $get = "SELECT * FROM ".$explanatory_dictionary_table_name." WHERE id='".$id."';";
    $word = $wpdb->get_row($get);

    return $word;
}

function explanatory_dictionary_headers(){
  global $explanatory_dictionary_plugin_url, $explanatory_dictionary_plugin_prefix;
  ?>
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

function add_explanatory_dictionary_words($content){
  global $explanatory_dictionary_plugin_prefix;

  $explanatory_dictionary = get_explanatory_dictionary();

  if(empty($explanatory_dictionary)){
    return $content;
  }

  $explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");

  if(explode(",",$explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."exclude"])!="-1"){
    $exclude = explode(",",$explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."exclude"]);
    if(array_search(get_the_ID(),$exclude)!==false){
      return $content;
    }
  }

  usort($explanatory_dictionary, 'sort_explanatory_dictionary_by_words_count');

  $limit = intval($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."limit"]);

  if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."unicode"]=="yes"){
    foreach($explanatory_dictionary as $explanatory_dictionary_word){
        $replacing_characters = array('\\','#','/','$','!','@','^','%','(',')');
        $replacement_caracters = array('\\\\','\#','\/','\$','\!','\@','\^','\%','\(','\)');
        $explanatory_dictionary_word['word'] = str_replace($replacing_characters,$replacement_caracters,$explanatory_dictionary_word['word']);

        $content = preg_replace('/'.$explanatory_dictionary_word['word'].'(?![^<|\[]*[>|\]]|(\[no explanation\])*(\[\/no explanation\]))/ui', '<span class="domtooltips">$0<span class="domtooltips_tooltip" style="display: none">'.htmlspecialchars_decode($explanatory_dictionary_word['explanation']).'</span></span>', $content, $limit, $count);

        if($limit==-1 || ($limit-$count)>0){
            $remained_limit = $limit-$count;
            if($remained_limit<-1){
                $remained_limit = -1;
            }

            $synonyms_and_forms = explode(",",$explanatory_dictionary_word['synonyms_and_forms']);
            if(!empty($synonyms_and_forms)){
                foreach($synonyms_and_forms as $synonym_or_form){
                    $synonym_or_form = str_replace($replacing_characters,$replacement_caracters,$synonym_or_form);

                    $content = preg_replace('/'.$synonym_or_form.'(?![^<|\[]*[>|\]]|(\[no explanation\])*(\[\/no explanation\]))/ui', '<span class="domtooltips">$0<span class="domtooltips_tooltip" style="display: none">'.htmlspecialchars_decode($explanatory_dictionary_word['explanation']).'</span></span>', $content, $remained_limit, $count);

                    if($remained_limit!=-1){
                        $remained_limit -= $count;
                        if($remained_limit<=0){
                            break;
                        }
                    }
                }
            }
        }
    }
  }else{
    foreach($explanatory_dictionary as $explanatory_dictionary_word){
        $replacing_characters = array('\\','#','/','$','!','@','^','%','(',')');
        $replacement_caracters = array('\\\\','\#','\/','\$','\!','\@','\^','\%','\(','\)');
        $explanatory_dictionary_word['word'] = str_replace($replacing_characters,$replacement_caracters,$explanatory_dictionary_word['word']);

        $content = preg_replace('/\b'.$explanatory_dictionary_word['word'].'\b(?![^<|\[]*[>|\]]|(\[no explanation\])*(\[\/no explanation\]))/i', '<span class="domtooltips">$0<span class="domtooltips_tooltip" style="display: none">'.htmlspecialchars_decode($explanatory_dictionary_word['explanation']).'</span></span>', $content, $limit, $count);

        if($limit==-1 || ($limit-$count)>0){
            $remained_limit = $limit-$count;
            if($remained_limit<-1){
                $remained_limit = -1;
            }

            if($explanatory_dictionary_word['synonyms_and_forms']!=""){
                $synonyms_and_forms = explode(",",$explanatory_dictionary_word['synonyms_and_forms']);
                if(!empty($synonyms_and_forms)){
                    foreach($synonyms_and_forms as $synonym_or_form){
                        $synonym_or_form = str_replace($replacing_characters,$replacement_caracters,$synonym_or_form);

                        $content = preg_replace('/\b'.$synonym_or_form.'\b(?![^<|\[]*[>|\]]|(\[no explanation\])*(\[\/no explanation\]))/i', '<span class="domtooltips">$0<span class="domtooltips_tooltip" style="display: none">'.htmlspecialchars_decode($explanatory_dictionary_word['explanation']).'</span></span>', $content, $remained_limit, $count);

                        if($remained_limit!=-1){
                            $remained_limit -= $count;
                            if($remained_limit<=0){
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
  }

  $no_explanation_tags = array("[no explanation]","[/no explanation]");

  $content = str_replace($no_explanation_tags,"",$content);

  return $content;
}

function add_explanatory_dictionary($content){
  global $explanatory_dictionary_plugin_prefix;

  $explanatory_dictionary_settings = get_option($explanatory_dictionary_plugin_prefix."settings");
  $explanatory_dictionary_selected_letter = "";

  $explanatory_dictionary_content = '<div class="explanatory_dictionary">';

  if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."alphabet"]!=""){
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
        }

        if($explanatory_dictionary_selected_letter==$explanatory_dictionary_alphabet_letter){
            $explanatory_dictionary_alphabet .= '<a href="'.add_query_arg("explanatory_dictionary_alphabet_letter", $explanatory_dictionary_alphabet_letter).'" class="explanatory_dictionary_selected_letter">'.$explanatory_dictionary_alphabet_letter.'</a>';
        }else{
            $explanatory_dictionary_alphabet .= '<a href="'.add_query_arg("explanatory_dictionary_alphabet_letter", $explanatory_dictionary_alphabet_letter).'">'.$explanatory_dictionary_alphabet_letter.'</a>';
        }

      }

      $explanatory_dictionary_alphabet .= '</div>';

      $explanatory_dictionary_content .= $explanatory_dictionary_alphabet;
  }

  $explanatory_dictionary = get_explanatory_dictionary($explanatory_dictionary_selected_letter);

  for($i=0; $i<count($explanatory_dictionary); $i++){
    if($i==0){
        $explanatory_dictionary_content .= '<div class="explanatory_dictionary_first">';
    }elseif($i==(count($explanatory_dictionary)-1)){
        $explanatory_dictionary_content .= '<div class="explanatory_dictionary_last">';
    }else{
        $explanatory_dictionary_content .= '<div>';
    }

    $explanatory_dictionary_content .= '<span class="explanatory_dictionary_word">'.$explanatory_dictionary[$i]['word'].'</span> - <span class="explanatory_dictionary_explanation">'.htmlspecialchars_decode($explanatory_dictionary[$i]['explanation']).'</span></div>';
  }

  $explanatory_dictionary_content .= '</div>';

  $content = str_replace("[explanatory dictionary]",$explanatory_dictionary_content,$content);

  return $content;
}

wp_enqueue_script('jquery');
wp_enqueue_script('explanatory_dictionary_scripts', $explanatory_dictionary_plugin_url.'javascript/scripts.js');

add_filter('the_excerpt', 'add_explanatory_dictionary_words');
add_filter('the_content', 'add_explanatory_dictionary_words');
add_filter('the_content', 'add_explanatory_dictionary');

add_action('plugins_loaded','explanatory_dictionary_load');
add_action('admin_menu', 'explanatory_dictionary_menu');
add_action('wp_head', 'explanatory_dictionary_headers');
?>