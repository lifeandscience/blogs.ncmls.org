<?php
/*
Plugin Name: Streampad Audio Player
Plugin URI: http://www.streampad.com/
Description: Streampad gives you everything you need to play back the music you post. Any '.mp3' file you link to or any link with class 'spmp3', will automatically show up in the Streampad player at the bottom of your page.
Author: AOL Music
Author URI: http://www.streampad.com/
Version: 1.0

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

class StreamPad {
		
	var $version = "1.0";	// update header too AND class-Streampad_DB_Init!
	var $state = true;
	var $optionsPageName = "streampad-options-page";
	function StreamPad() { $this->__construct(); }
	
	function __construct() {
		if ( isset( $_GET['streampadapi'] ) && $_GET['streampadapi'] == 'true' ) {
			require_once('class-StreamPadAPI.php');
			$streamPadApi = new StreamPadAPI();
			echo $streamPadApi->APIresponse( $this->version );
			exit;
			
		} else {

			add_action("transition_post_status", array( &$this, "attach_transition" ), 12, 3);						
			add_action("delete_post", array( &$this, "attach_delete" ));
			add_action("publish_post", array( &$this, "attach_publish" ));
			add_action("publish_page", array( &$this, "attach_publish" ));			
			add_action("wp_head", array(&$this,"serveHeader"));	
			add_action('admin_menu', array(&$this, "attach_options_page"));
			add_action('wp_ajax_save_single_track', array(&$this, "adminAjaxResponse"));
			add_action('wp_ajax_update_single_track', array(&$this, "adminAjaxUpdate"));
			add_action('wp_ajax_delete_single_track', array(&$this, "adminAjaxDelete"));
			add_filter("plugin_action_links", array(&$this, "add_config_link"), 10, 2);
			
			//error_log( phpversion() );			
		}
	}
	
	function serveHeader() {
	  $async = <<<EOT
	  <script type="text/javascript">
	  //<![CDATA[
	  (function() {
	    var d = document, s = d.createElement('script');
	    s.type = 'text/javascript';
	    s.src = 'http://o.aolcdn.com/art/merge/?f=/_media/sp/sp-player.js&f=/_media/sp/sp-player-wordpressorg.js&expsec=86400&ver=2';
	    d.getElementsByTagName('head')[0].appendChild(s);
	  })();
	  //]]>
	  </script>
EOT;
		echo $async;
      	
	}
		
	function attach_publish( $post_id ) {		
		if( $this->state ) {
			// despite the name, the function will only trigger when a post is 'updated'. It's an odd fall thru
			//error_log("publishing post id " . $post_id);		
			require_once( "class-Streampad_DB_Manage.php" );
			$dbManage = new Streampad_DB_Manage();
			$dbManage->checkMP3Post( $post_id );		
			//error_log('attach to publish');
		}

	}
	
	function attach_transition( $new_status, $old_status, $post ) {
		
		$pub_var = 'publish';
		$draft_var = 'draft';
		$pend_var = 'pending';
		$private_var = 'private';
		$inherit_var = 'inherit';
		
		if( ( $new_status == $draft_var || $new_status == $pend_var || $new_status == $private_var ) && $old_status == $pub_var ) {
				// hook into this, use case of post going from live to draft, take info from database
				//error_log("POST no longer public, remove it");
				$dbManage = new Streampad_DB_Manage();
				$dbManage->removeTracks( $post->ID );	
		} else if( $new_status == $pub_var ) {
				require_once( "class-Streampad_DB_Manage.php" );
				$dbManage = new Streampad_DB_Manage();
				$dbManage->scrapePostText( $post->ID, $post->post_date, $post->post_content, $post->guid );							
				$this->state = false; // this stops fall thru of duplicate item re-saving when post goes from draft to published	
		} 
	}
	
	function attach_delete( $post_id ) {
		$dbManage = new Streampad_DB_Manage();
		$dbManage->removeTracks( $post_id );	
	}
	
	function add_config_link($links, $file) {
		static $this_plugin;
		if (!$this_plugin) {
			$this_plugin = plugin_basename(__FILE__);
		}
		if ($file == $this_plugin) {
			$settings_link = '<a href="options-general.php?page=' . $this->optionsPageName . '">' . __('Settings') . '</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}	
	
	function attach_options_page() {
		require_once('class-StreamPad_Admin_Page.php');
		$sp = new StreamPad_Admin_Page();
		$page = add_options_page("Streampad Sync Blog MP3s", "Streampad", 8, $this->optionsPageName, array( &$sp, "init" ));
		add_action("admin_head-$page", array(&$sp, "wp_admin_scripts"));
	}
	
	///////////////////////////////////////////////////////////////
	// ADMIN AJAX
	///////////////////////////////////////////////////////////////	
	function adminAjaxResponse() {
		if( ! isset( $_POST['title'] ) || ! isset( $_POST['enclosure'] ) ) { echo "error"; exit; }

		require_once( "class-Streampad_DB_Manage.php" );
		$dbManage = new Streampad_DB_Manage();
		$response = $dbManage->manuallyInsertTrack( urldecode( $_POST['title'] ), urldecode( $_POST['enclosure']) );
		
		echo $response;
		exit;	
	}
	
	function adminAjaxUpdate() {
		if( ! isset( $_POST['unique_id'] ) || ! isset( $_POST['title'] ) || ! isset( $_POST['enclosure'] ) ) { echo "error"; exit; }	
	
		require_once( "class-Streampad_DB_Manage.php" );
		$dbManage = new Streampad_DB_Manage();
		//error_log( urldecode($_POST['title']) );
		$response = $dbManage->updateTrackByUniqueId( $_POST['unique_id'],  urldecode( $_POST['title'] ), urldecode( $_POST['enclosure'])  );			
		
		echo $response;
		exit;
	}
	
	function adminAjaxDelete() {
		if( ! isset( $_POST['unique_id'] ) ) { echo "-1"; exit; }	
	
		require_once( "class-Streampad_DB_Manage.php" );
		$dbManage = new Streampad_DB_Manage();
		
		$response = $dbManage->deleteByUniqueId( $_POST['unique_id'] );	
		echo $response;				
		exit;
	}	


}

if (class_exists('StreamPad')) {
	require_once( "class-Streampad_DB_Manage.php" );
     $Streampad = new StreamPad();
}

/* When Plugin First Launches, add to DB  */
if( function_exists("register_activation_hook") ) {
	//error_log("register activation hook");
	register_activation_hook(__FILE__, 'streampad_database_activation' );
} else {
	error_log('not able to find register activation hook');
	// not sure what to do here. maybe no solution? This is the scenario where register_activation_hook doesn't fire!
}

function streampad_database_activation() {
	require_once( 'class-Streampad_DB_Init.php');
	$db_init = new Streampad_DB_Init();
	$db_init->init();
}





?>