<?php
if(!current_user_can('manage_options')) {
 die('Access Denied');
}

if($_GET["page"]=='explanatory-dictionary/explanatory-dictionary-manage.php'){
    if($_POST["action"]=="add"){
        $explanatory_dictionary_word = htmlspecialchars(stripslashes($_POST[$explanatory_dictionary_plugin_prefix."word"]));
        $explanatory_dictionary_synonyms_and_forms = htmlspecialchars(stripslashes($_POST[$explanatory_dictionary_plugin_prefix."synonyms_and_forms"]));
        $explanatory_dictionary_explanation = htmlspecialchars(stripslashes($_POST[$explanatory_dictionary_plugin_prefix."explanation"]));

        if(add_explanatory_dictionary_word($explanatory_dictionary_word,$explanatory_dictionary_synonyms_and_forms,$explanatory_dictionary_explanation)){
            echo('<div id="message" class="updated fade"><p><strong>Added.</strong></p></div>');
        }else{
            echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
        }
    }elseif($_POST["action"]=="edit"){
        $explanatory_dictionary_word = htmlspecialchars(stripslashes($_POST[$explanatory_dictionary_plugin_prefix."word"]));
        $explanatory_dictionary_synonyms_and_forms = htmlspecialchars(stripslashes($_POST[$explanatory_dictionary_plugin_prefix."synonyms_and_forms"]));
        $explanatory_dictionary_explanation = htmlspecialchars(stripslashes($_POST[$explanatory_dictionary_plugin_prefix."explanation"]));

        $word_id = $_POST[$explanatory_dictionary_plugin_prefix."word_id"];

        if(is_numeric($word_id) && $word_id>0){
            if(edit_explanatory_dictionary_word($word_id,$explanatory_dictionary_word,$explanatory_dictionary_synonyms_and_forms,$explanatory_dictionary_explanation)){
                echo('<div id="message" class="updated fade"><p><strong>Edited.</strong></p></div>');
            }else{
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }
        }else{
            echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
        }
    }elseif($_POST["action"]=="delete"){
        $deleting_word = $_POST[$explanatory_dictionary_plugin_prefix."word_id"];

        if(is_numeric($deleting_word) && $deleting_word>0){
            delete_explanatory_dictionary_word($deleting_word);

            echo('<div id="message" class="updated fade"><p><strong>Deleted.</strong></p></div>');
        }else{
            echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
        }
    }
}

$explanatory_dictionary = get_explanatory_dictionary();

?>
<style>
  .widefat td {
  	padding: 3px 7px;
  	vertical-align: middle;
  }

  .widefat tbody th.check-column {
  	padding: 7px 0;
      vertical-align: middle;
  }
</style>

<div class="wrap">
    <div style="margin: 20px 0; text-align: center; display: inline-block"><div style="float: left"><a href="http://blorner.com?utm_source=explanatory-dictionary&utm_medium=banner&utm_campaign=admin" target="_blank"><img src="http://banners.blorner.com/blorner.com-468x60.jpg" alt="Blorner" style="border: none" /></a></div><div style="float: right; margin-left: 50px; text-align: justify; width: 400px; border: 1px solid #DFDFDF; padding: 10px;"><div style="float: left; margin-right: 10px;"><a href="http://rubensargsyan.com/wordpress-plugin-ubm-premium/" target="_blank"><img src="http://rubensargsyan.com/images/ubm-premium.png" alt="UBM Premium" style="border: none" /></a></div><div style="font-size: 11px">UBM Premium is the ultimate banner manager WordPress plugin for the serious bloggers. Rotate banners based on performance, track outgoing clicks, control nofollow/dofollow and much more. The perfect solution for all affiliate marketers and webmasters.</div></div></div>

    <?php
    if(isset($_GET[$explanatory_dictionary_plugin_prefix."word_id"]) && is_numeric($_GET[$explanatory_dictionary_plugin_prefix."word_id"]) && $_GET[$explanatory_dictionary_plugin_prefix."word_id"]>0 && $word=get_explanatory_dictionary_word($_GET[$explanatory_dictionary_plugin_prefix."word_id"])){
    ?>
        <h2>Edit</h2>

        <form method="post">
          <table width="100%" border="0" id="add_explanatory_dictionary_table">
            <tr>
              <td width="30%" valign="middle"><strong>Word (words expression, sentence)</strong></td>
              <td width="70%">
                  <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word" type="text" maxlength="255" style="width:300px;" value="<?php echo($word->word); ?>" />
              </td>
            </tr>
            <tr>
              <td width="30%" valign="top"><strong>Synonyms and forms</strong> (optional)<br /><small>Separate by commas the words (words expressions, sentences)<br />which has the same explanation.</small></td>
              <td width="70%">
                  <textarea name="<?php echo($explanatory_dictionary_plugin_prefix); ?>synonyms_and_forms" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>synonyms_and_forms" style="width:300px; height:100px"><?php echo($word->synonyms_and_forms); ?></textarea>
              </td>
            </tr>
            <tr>
              <td width="30%" valign="top"><strong>Explanation</strong></td>
              <td width="70%">
                  <textarea name="<?php echo($explanatory_dictionary_plugin_prefix); ?>explanation" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>explanation" style="width:300px; height:100px"><?php echo($word->explanation); ?></textarea>
              </td>
            </tr>
          </table>
          <p class="submit">
            <input name="save" type="submit" value="Save" />
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_id" value="<?php echo($word->id); ?>" />
            <a href="admin.php?page=explanatory-dictionary/explanatory-dictionary-manage.php">Cancel</a>
          </p>
        </form>
    <?php
    }else{
    ?>
        <h2>Add</h2>

        <form method="post">
          <table width="100%" border="0" id="add_explanatory_dictionary_table">
            <tr>
              <td width="30%" valign="middle"><strong>Word (words expression, sentence)</strong></td>
              <td width="70%">
                  <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word" type="text" maxlength="255" style="width:300px;" />
              </td>
            </tr>
            <tr>
              <td width="30%" valign="top"><strong>Synonyms and forms</strong> (optional)<br /><small>Separate by commas the words (words expressions, sentences)<br />which has the same explanation.</small></td>
              <td width="70%">
                  <textarea name="<?php echo($explanatory_dictionary_plugin_prefix); ?>synonyms_and_forms" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>synonyms_and_forms" style="width:300px; height:100px"></textarea>
              </td>
            </tr>
            <tr>
              <td width="30%" valign="top"><strong>Explanation</strong></td>
              <td width="70%">
                  <textarea name="<?php echo($explanatory_dictionary_plugin_prefix); ?>explanation" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>explanation" style="width:300px; height:100px"></textarea>
              </td>
            </tr>
          </table>
          <p class="submit">
            <input name="add" type="submit" value="Add" />
            <input type="hidden" name="action" value="add" />
          </p>
        </form>
    <?php
    }
    ?>

    <br /><br />
    <h2>Manage</h2>

    <table class="widefat fixed" cellspacing="0">
    	<thead>
    	<tr>
    	    <th scope="col" id="cb" class="check-column" style=""><input type="checkbox" /></th>
    	    <th scope="col" style="width: 25%">Word (words expression, sentence)</th>
          <th scope="col" style="width: 25%">Synonyms and forms</th>
          <th scope="col" style="width: 37%">Explanation</th>
          <th scope="col" width="5%"></th>
          <th scope="col" width="8%"></th>
    	</tr>
    	</thead>

    	<tfoot>
    	<tr>
        	<th scope="col" class="check-column"><input type="checkbox" /></th>
        	<th scope="col">Word (words expression, sentence)</th>
          <th scope="col">Synonyms and forms</th>
        	<th scope="col">Explanation</th>
          <th scope="col"></th>
          <th scope="col"></th>
    	</tr>
    	</tfoot>

    	<tbody>
        <?php foreach($explanatory_dictionary as $word){ ?>
    	<tr class='alternate' valign="top">
    		<th scope="row" class="check-column"><input type="checkbox" name="words[]" value="<?php echo($word['id']); ?>" /></th>
    		<td>
                <?php echo($word['word']); ?>
    		</td>
            <td>
                <?php echo($word['synonyms_and_forms']); ?>
    		</td>
          <td>
                <?php echo($word['explanation']); ?>
    		</td>
          <td>
            <form method="get">
                <p class="submit">
                  <input type="submit" value="Edit" />
                  <input type="hidden" name="page" value="explanatory-dictionary/explanatory-dictionary-manage.php" />
                  <input type="hidden" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_id" value="<?php echo($word["id"]); ?>" />
                </p>
            </form>
          </td>
          <td>
            <form method="post">
                <p class="submit">
                  <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>delete" type="submit" value="Delete" onclick="javascript:if(!confirm('Are you sure you want to delete it from the dictionary?')){ return false; }" />
                  <input type="hidden" name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word_id" value="<?php echo($word["id"]); ?>" />
                  <input type="hidden" name="action" value="delete" />
                </p>
            </form>
          </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>