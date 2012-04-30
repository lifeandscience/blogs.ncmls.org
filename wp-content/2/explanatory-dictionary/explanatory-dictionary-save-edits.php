<?php
$path  = '';

if(!defined('WP_LOAD_PATH')){
	$root = dirname(dirname(dirname(dirname(__FILE__)))).'/';

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
$explanatory_dictionary_table_name = $wpdb->prefix."explanatory_dictionary";

$id = intval($_POST["id"]);
$explanation = trim(mysql_real_escape_string($_POST["explanation"]));

function save_edits($id,$explanation){
    global $wpdb;
    global $explanatory_dictionary_table_name;

    if(trim(strip_tags($explanation)==="")){
        return "empty";
    }

    $edit = "UPDATE ".$explanatory_dictionary_table_name." SET explanation='".$explanation."' WHERE id='".$id."';";

    $wpdb->query($edit);

    return true;
}
    $edit_status = save_edits($id,$explanation);

    if($edit_status===true){
        echo("Explanation is saved successfully.");
    }elseif($edit_status=="empty"){
        echo("There is no explanation.");
    }else{
        echo("Cannot edit the explanation.\n".mysql_error());
    }
?>