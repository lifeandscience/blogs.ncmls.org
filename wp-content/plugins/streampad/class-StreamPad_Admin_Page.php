<?php

/*

	Streampad
	References : http://amiworks.co.in/talk/simplified-ajax-for-wordpress-plugin-developers-using-jquery/

Copyright 2009 AOL, LLC  (email : gregory.tomlinson@corp.aol.com | http://gregorytomlinson.com/encoded/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


class StreamPad_Admin_Page {
	var $hiddenField = "sync_mp3_track_list";
	var $baseTable = "StreamPad_Tracks";

	function StreamPad_Admin_Page() { $this->__construct(); }	
	
	function __construct() {}

	function init() {

		$this->renderPage();
	}
	
	function renderPage() {
		
		$data = "";
		
		$data .= "<div><h2>Sync MP3 list</h2>";
		if( isset($_POST[ $this->hiddenField ] ) && $_POST[ $this->hiddenField ] == 'sync' ) {
		
			require_once('class-Streampad_DB_Manage.php');
			$manage = new Streampad_DB_Manage();
			$reult = $manage->reSyncPosts();
			
			require_once("class-Streampad_DB_Init.php");
			$create = new Streampad_DB_Init();
			$create->combData();
			
			$data .= '<div class="updated"><p><strong> Sync Complete </strong></p></div>';		
		}

		$data .= "<p> If you are having trouble with tracks not in sync or producing the wrong URL in the player, try re-syncing the Streampad MP3 List.</p>";
		$data .= '<p><form name="form1" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
		$data .= '<input type="hidden" value="sync" name="'. $this->hiddenField  .'"/>';
		$data .= '<input class="button-primary" type="submit" value="Sync MP3s" name="Sync"/>';
		$data .= "</form></p>";
		$data .= "<p><i>Note: This will only affect mp3s linked from posts or pages. Manually managed mp3s will not be affected</i></p></div>";		
		
		$data .= "<div><h2>Manually Managed MP3s</h2>";
		$data .= '<p>Manually managing mp3s allows you to: </p>';
		$data .= '<ul>';
		$data .= '<li>1. Add mp3s without including them in a post.</li>';		
		$data .= '<li>2. Change the url of any mp3 file.</li>';				
		$data .= '</ul>';
		$data .= '<p>Please be aware that if you re-publish a post, any changes you made manually to an mp3 will be over-written. <br />In addition, if you \'Sync MP3s\' above, any manual changes you made below will also be over-written.</p>';		
		$data .= '<div class="sp_ajaxControlBttns"><a id="insertTracks" href="'. get_option('siteurl') .'/wp-admin/admin-ajax.php">Insert Track</a> | <a id="showTracks" href="'. get_option('siteurl') .'/wp-admin/admin-ajax.php">Show Tracks</a></div>';		
		$data .= '<div id="trackList"></div>';			
		$data .= '<div id="exisitingTrackList"></div>';
		$data .= "</div>";
				
		echo $data;
	}
	
	function wp_admin_scripts() {
		echo '<link rel="stylesheet" type="text/css" href="'. get_option('siteurl') .'/wp-content/plugins/streampad/css/sp_options.css" />';
		echo '<script type="text/javascript" src="'. get_option('siteurl') .'/wp-content/plugins/streampad/js/manage-db-tracks.js"></script>';
	}
	
}




?>