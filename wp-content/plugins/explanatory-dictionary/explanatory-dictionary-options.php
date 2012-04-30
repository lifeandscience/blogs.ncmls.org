<?php
if(!current_user_can('manage_options')) {
    die('Access Denied');
}
?>
<script src="<?php echo($explanatory_dictionary_plugin_url.'javascript/jscolor.js'); ?>" type="text/javascript"></script>
<?php

if($_GET["page"]=='explanatory-dictionary/explanatory-dictionary-options.php'){
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
        if(is_numeric($_POST[$explanatory_dictionary_plugin_prefix."limit"]) && $_POST[$explanatory_dictionary_plugin_prefix."limit"]>=0){
            $explanatory_dictionary_limit = $_POST[$explanatory_dictionary_plugin_prefix."limit"];
        }else{
            $explanatory_dictionary_limit = "-1";
        }

        $explanatory_dictionary_alphabet = trim($_POST[$explanatory_dictionary_plugin_prefix."alphabet"]);

        $explanatory_dictionary_settings = array($explanatory_dictionary_plugin_prefix."external_css_file"=>$explanatory_dictionary_external_css_file,$explanatory_dictionary_plugin_prefix."width"=>$explanatory_dictionary_width,$explanatory_dictionary_plugin_prefix."text_align"=>$explanatory_dictionary_text_align,$explanatory_dictionary_plugin_prefix."font_size"=>$explanatory_dictionary_font_size,$explanatory_dictionary_plugin_prefix."color"=>$explanatory_dictionary_color,$explanatory_dictionary_plugin_prefix."border_size"=>$explanatory_dictionary_border_size,$explanatory_dictionary_plugin_prefix."border_color"=>$explanatory_dictionary_border_color,$explanatory_dictionary_plugin_prefix."background"=>$explanatory_dictionary_background,$explanatory_dictionary_plugin_prefix."padding"=>$explanatory_dictionary_padding,$explanatory_dictionary_plugin_prefix."border_radius"=>$explanatory_dictionary_border_radius,$explanatory_dictionary_plugin_prefix."word_color"=>$explanatory_dictionary_word_color,$explanatory_dictionary_plugin_prefix."word_font_style"=>$explanatory_dictionary_word_font_style,$explanatory_dictionary_plugin_prefix."word_font_weight"=>$explanatory_dictionary_word_font_weight,$explanatory_dictionary_plugin_prefix."word_text_decoration"=>$explanatory_dictionary_word_text_decoration,$explanatory_dictionary_plugin_prefix."unicode"=>$explanatory_dictionary_unicode,$explanatory_dictionary_plugin_prefix."exclude"=>$explanatory_dictionary_exclude,$explanatory_dictionary_plugin_prefix."limit"=>$explanatory_dictionary_limit,$explanatory_dictionary_plugin_prefix."alphabet"=>$explanatory_dictionary_alphabet);

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
    <div style="margin: 20px 0; text-align: center; display: inline-block"><div style="float: left"><a href="http://blorner.com?utm_source=explanatory-dictionary&utm_medium=banner&utm_campaign=admin" target="_blank"><img src="http://banners.blorner.com/blorner.com-468x60.jpg" alt="Blorner" style="border: none" /></a></div><div style="float: right; margin-left: 50px; text-align: justify; width: 400px; border: 1px solid #DFDFDF; padding: 10px;"><div style="float: left; margin-right: 10px;"><a href="http://rubensargsyan.com/wordpress-plugin-ubm-premium/" target="_blank"><img src="http://rubensargsyan.com/images/ubm-premium.png" alt="UBM Premium" style="border: none" /></a></div><div style="font-size: 11px">UBM Premium is the ultimate banner manager WordPress plugin for the serious bloggers. Rotate banners based on performance, track outgoing clicks, control nofollow/dofollow and much more. The perfect solution for all affiliate marketers and webmasters.</div></div></div>
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
              <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>limit" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>limit" type="text" style="width:100px;" value="<?php if($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."limit"]>=0){ echo($explanatory_dictionary_settings[$explanatory_dictionary_plugin_prefix."limit"]); } ?>" /> <small>Write here the number of words (words expressions, sentences) for showing the tooltips per page or post or leave empty for all the words (words expressions, sentences).</small>
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