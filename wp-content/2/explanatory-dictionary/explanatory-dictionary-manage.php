<?php
    if(!current_user_can('manage_options')) {
	    die('Access Denied');
    }

    if($_GET["page"]=='explanatory-dictionary/explanatory-dictionary-manage.php'){
        if($_POST["action"]=="add"){
            $explanatory_dictionary_word = htmlspecialchars($_POST[$explanatory_dictionary_plugin_prefix."word"]);
            $explanatory_dictionary_explanation = htmlspecialchars($_POST[$explanatory_dictionary_plugin_prefix."explanation"]);

            if(add_explanatory_dictionary_word($explanatory_dictionary_word,$explanatory_dictionary_explanation)){
                echo('<div id="message" class="updated fade"><p><strong>Added.</strong></p></div>');
            }else{
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }
        }elseif($_POST["action"]=="delete"){
            $deleting_words = $_POST["words"];

            if(empty($deleting_words)){
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }else{
                foreach($deleting_words as $deleting_word){
                    delete_explanatory_dictionary_word($deleting_word);
                }

                echo('<div id="message" class="updated fade"><p><strong>Deleted.</strong></p></div>');
            }
        }
    }

    $explanatory_dictionary = get_explanatory_dictionary();

    ?>
    <div class="wrap">
      <h2>Manage <?php echo $explanatory_dictionary_plugin_title; ?></h2>

      <form method="post">
        <table width="100%" border="0" id="add_explanatory_dictionary_table">
          <tr>
            <td width="30%" valign="middle"><strong>Word (Words Expression, Sentence)</strong></td>
            <td width="70%">
                <input name="<?php echo($explanatory_dictionary_plugin_prefix); ?>word" id="<?php echo($explanatory_dictionary_plugin_prefix); ?>word" type="text" maxlength="255" style="width:300px;" />
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
      <script type="text/javascript">
      function edit_explanation(id){
            document.getElementById("explanation_of_"+id).contentEditable = "true";

            if(!document.getElementById("save_button_"+id)){
                var explanation_save_button = document.createElement("input");
                explanation_save_button.type = "button";
                explanation_save_button.id = "save_button_"+id;
                explanation_save_button.value = "Save";
                explanation_save_button.onclick = function(){
                    save_edited_explanation(id);
                }
                document.getElementById(id).appendChild(explanation_save_button);
            }
      }

      function save_edited_explanation(id){
          var the_id = document.getElementById("id_of_"+id).value;
          var the_explanation = document.getElementById("explanation_of_"+id).innerHTML;

          the_explanation = the_explanation.replace("&nbsp;"," ");
          the_explanation = the_explanation.replace("&","");

          if(window.XMLHttpRequest){
              request = new XMLHttpRequest();
              params = "id="+the_id+"&explanation="+the_explanation;
              request.open("POST", "<?php echo($explanatory_dictionary_plugin_url); ?>/explanatory-dictionary-save-edits.php", true);
              request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              request.setRequestHeader("Content-length", params.length);
              request.setRequestHeader("Connection", "close");
              request.onreadystatechange = function saveRequest(){
                    if(document.getElementById("save_button_"+id)){
                        document.getElementById(id).removeChild(document.getElementById("save_button_"+id));
                    }

                    document.getElementById("explanation_of_"+id).contentEditable = false;

                    if(request.readyState==4){
                        if(request.status==200){
                            alert(request.responseText);

                            if(request.responseText=="There is no explanation."){
                                edit_explanation(id);
                            }
                        }else{
                            alert("Cannot save the explanation.");
                        }
                    }
              }
              request.send(params);
          }else if(window.ActiveXObject){
              request = new ActiveXObject("Microsoft.XMLHTTP");
              if(request){
                  params = "id="+the_id+"&explanation="+the_explanation;
                  request.open("POST", "<?php echo($explanatory_dictionary_plugin_url); ?>/explanatory-dictionary-save-edits.php", true);
                  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                  request.setRequestHeader("Content-length", params.length);
                  request.setRequestHeader("Connection", "close");
                  request.onreadystatechange = function saveRequest(){
                        if(document.getElementById("save_button_"+id)){
                            document.getElementById(id).removeChild(document.getElementById("save_button_"+id));
                        }

                        document.getElementById("explanation_of_"+id).contentEditable = false;

                        if(request.readyState==4){
                            if(request.status==200){
                                alert(request.responseText);
                            }else{
                                alert("Cannot save the explanation.");
                            }
                        }
                  }
                  request.send(params);
              }
          }
      }
      </script>
      <p align="right">To edit the explanation double click on it.</p>
      <form method="post">
        <table class="widefat fixed" cellspacing="0">
        	<thead>
        	<tr>
        	<th scope="col" id="cb" class="check-column" style=""><input type="checkbox" /></th>
        	<th scope="col" style="width: 30%">Word (Words Expression, Sentence)</th>
        	<th scope="col" style="width: 70%">Explanation</th>
        	</tr>
        	</thead>

        	<tfoot>
        	<tr>
        	<th scope="col" class="check-column" style=""><input type="checkbox" /></th>
        	<th scope="col" style="">Word (Words Expression, Sentence)</th>
        	<th scope="col" style="">Explanation</th>
        	</tr>
        	</tfoot>

        	<tbody>
            <?php for($i=0; $i<count($explanatory_dictionary); $i++){ ?>
        	<tr class='alternate' valign="top">
        		<th scope="row" class="check-column"><input type="checkbox" name="words[]" value="<?php echo($explanatory_dictionary[$i]['id']); ?>" /></th>
        		<td>
                    <?php echo($explanatory_dictionary[$i]['word']); ?>
        		</td>
                <td>
                    <div id="explanation_id_<?php echo($explanatory_dictionary[$i]['id']); ?>"><div id="explanation_of_explanation_id_<?php echo($explanatory_dictionary[$i]['id']); ?>" ondblclick="edit_explanation('explanation_id_<?php echo($explanatory_dictionary[$i]['id']); ?>');"><?php echo($explanatory_dictionary[$i]['explanation']); ?></div><input type="hidden" id="id_of_explanation_id_<?php echo($explanatory_dictionary[$i]['id']); ?>" value="<?php echo($explanatory_dictionary[$i]['id']); ?>" /></div>
        		</td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <p class="submit">
          <input name="delete" type="submit" value="Delete" />
          <input type="hidden" name="action" value="delete" />
        </p>
      </form>
</div>