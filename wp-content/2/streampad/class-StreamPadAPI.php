<?php
/*

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

class StreamPadAPI {
	
	var $baseTable = "StreamPad_Tracks";
	
	function StreamPadAPI() { $this->__construct(); }
	
	function __construct() {}
	
	function APIresponse( $version ) {
		header('Content-type: text/plain');		
		global $wpdb;
		// go get the data
		$start = 0;
		$count = 20;
		$response = '';
		if ( isset( $_GET['count'] ) ) {  $count = $_GET['count']; } 		
		if ( isset( $_GET['start'] ) ) {  $start = $_GET['start']; } 
		
		$mp3streams = $wpdb->get_results("select id, post_id, enclosure, 
										 description, title, sourceUrl, post_date from " 
										 . $wpdb->prefix . $this->baseTable ." ORDER BY post_date DESC limit $start,$count");
		

         if ( isset( $_GET['callback'] ) ) { $response .= $_GET['callback']."("; }		
		
		$apiObj['blogname'] = $this->getBlogName();
		$apiObj['totalcount'] = $this->getTrackNumbers();
		$apiObj['tracks'] = $mp3streams;
		$apiObj['version'] = $version;
		
		
		if( function_exists('json_encode') ) {
			$response .= json_encode( $apiObj );		
		} else {
			require_once("json.php");
			$json = new Services_JSON();
			$response .= $json->encode( $apiObj );	
		}
		if ( isset( $_GET['callback'] ) ) { $response .= ")"; }		
		
		return $response;		
	}
	
	function getBlogName() {
		return get_option('blogname');
	}
	
	function getTrackNumbers() {
		global $wpdb;
		$result = $wpdb->get_var("select count('enclosure') FROM " . $wpdb->prefix . $this->baseTable);
		return $result;
	}
	
}


?>