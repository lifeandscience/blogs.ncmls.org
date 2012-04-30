<?php
/*

	Streampad

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

class Streampad_DB_Manage {

	var $baseTable = "StreamPad_Tracks";

	function Streampad_DB_Manage() { $this->__construct(); }
	
	function __construct() {
		/* instaniate */
	}

	function manuallyInsertTrack( $title, $enclosure ) {
         //require_once( ABSPATH . '/wp-content/plugins/streampad/class-StreamPadMP3.php' );	
         require_once( 'class-StreamPadMP3.php' );	   
         $siteurl = get_option("siteurl") . "/";

         $mp3 = new StreamPadMP3();
         $mp3->date = current_time('mysql'); 
         $mp3->enclosure = $this->combineUrl( $siteurl, $enclosure );
         $mp3->sourceUrl = $siteurl; // not associated with a post, use blog URL
         $mp3->id = 0; // make the post_id: 0
         $mp3->title = $title; 
         $mp3->description = ""; 
                  
		$mp3array = array();
		array_push($mp3array, $mp3);
		$count = count( $mp3array );
		if( $count > 0 ) { $response = $this->storeTracks( $mp3array, $count ); return $response; }  
		
		return 0;       
 
	}

	function parseContent( $id, $postDate, $postContent, $guid, &$arr ) {
		/* Smart Parse :: For php5, do nice things - for php4 -- use 'old way' */		
		switch(true)
		{
			case (version_compare("5", phpversion(), "<=")):
			//do php5 code
				$this->parsePHP5Content( $id, $postDate, $postContent, $guid, $arr );
				
			break;
			case (version_compare("5", phpversion(), ">")):
				error_log('no php 5 on this box');
				$this->parsePHP4Content( $id, $postDate, $postContent, $guid, $arr );
			break;
		}	

	}
	
	
	function parsePHP4Content( &$id, &$postDate, &$postContent, &$guid, &$arr ) {
		/* Load The extension for DOM XML */
		//http://us3.php.net/domxml		
		if (!extension_loaded('domxml')) {
		   error_log("ERROR : You don't have an XML/HTML lib that can parse content! Sorry, I cannot work");
		   return null;
		}
		
		if (!$dom = domxml_open_mem("<sp>" . $postContent . "</sp>")) {
  			error_log( "Error while parsing the document\n" );  			
			return null;
		}
		
		$root = $dom->document_element(); // specific to php4
		$nodes = $dom->create_element( $postContent ); // specific to php4
		$as = $dom->get_elements_by_tagname( 'a' ); // specific to php4
				
		require_once( 'class-StreamPadMP3.php' );
		$siteurl = get_option("siteurl") . "/";
		
		
		foreach( $as as $a ) {

			$href = $a->get_attribute('href'); // specific to php4
			$class = $a->get_attribute('class'); // specific to php4
			if (strtolower(substr($href, -4, 4)) != '.mp3' && stristr($class, 'spmp3') === FALSE ) { continue; } // looking for class name spmp3 in Init file aslo
			
          	$mp3 = new StreamPadMP3();
          	$mp3->id = $id;
          	$mp3->date = $postDate;
          	$mp3->enclosure = $this->combineUrl( $siteurl, $href );

          	$mp3->title = $a->get_content(); // specific to php4
          	$mp3->description = $postContent;
          	$mp3->sourceUrl = $guid;
          	array_push( $arr, $mp3 );			

		}
		
	}
	
	function parsePHP5Content( &$id, &$postDate, &$postContent, &$guid, &$arr ) {
		  $doc = new DOMDocument();
		  @$doc->loadHTML( $postContent );	
          $as = $doc->getElementsByTagName('a');
          
          require_once( 'class-StreamPadMP3.php' );
          $siteurl = get_option("siteurl") . "/";
          //http://www.web-max.ca/PHP/misc_24.php
          
          foreach( $as as $a ) {
          	$href = $a->getAttribute('href');
          	$class = $a->getAttribute('class');
          	
          	if (strtolower(substr($href, -4, 4)) != '.mp3' && stristr($class, 'spmp3') === FALSE ) { continue; } // looking for class name spmp3 in Init file aslo
          	
          	$mp3 = new StreamPadMP3();
          	$mp3->id = $id;
          	$mp3->date = $postDate;
          	$mp3->enclosure = $this->combineUrl( $siteurl, $href );
          	//$mp3->enclosure = $href;
          	$mp3->title = $a->nodeValue;
          	$mp3->description = $postContent;
          	$mp3->sourceUrl = $guid;
          	array_push( $arr, $mp3 );
          }		
	
	}
	
	function checkMP3Post( $post_id ) {
		global $wpdb;
		
		$mp3streams = $wpdb->get_results("select guid, id, post_date, post_content from " . 
			$wpdb->prefix .
			"posts where post_content like '%.mp3%' AND post_status = 'publish' AND ID='". $post_id ."'");		
		
		$count = count($mp3streams);
		if( $count > 0 ) {
			$mp3array = array();
			for ($i = 0; $i < $count; $i++) {
				$this->parseContent( $mp3streams[$i]->id, $mp3streams[$i]->post_date, $mp3streams[$i]->post_content, $mp3streams[$i]->guid, $mp3array );
			}
			
			$count = count( $mp3array );
			if( $count > 0 ) {
				$this->removeTracks( $post_id );
				$this->storeTracks( $mp3array, $count );
			}						
		}		
	}
	
	function scrapePostText( $post_id, $post_date, $post_content, $post_guid ) {
		// scrape a string in post object for values, used with transition_post_status
		$mp3array = array();
		error_log('scraping text');
		$this->parseContent( $post_id, $post_date, $post_content, $post_guid, $mp3array );
		$count = count( $mp3array );
		if( $count > 0 ) {
			$this->removeTracks( $post_id );
			$this->storeTracks( $mp3array, $count );
		}		
	}
	
	function removeTracks( $post_id ) {
		global $wpdb;
		$delete = "delete FROM ". $this->getTableName() ." WHERE post_id = '". $post_id ."'";
		$result = $wpdb->query( $delete );
	}
	
	function storeTracks( &$mp3arr, $count ) {
		// Store existing tracks in DB
		global $wpdb;
		$insert = "INSERT INTO " . $this->getTableName() . "(
			post_id, enclosure, post_date, sourceUrl, description, title
		) VALUES ";	
		
		$arr = array();
		for( $i=0; $i<$count; $i++ ) {
		
			$values = "('" .  $wpdb->escape( $mp3arr[$i]->id ) . "', '" . 
						$wpdb->escape( $mp3arr[$i]->enclosure ) . "', '" . $wpdb->escape( $mp3arr[$i]->date ) . "', '" .  
						$wpdb->escape( $mp3arr[$i]->sourceUrl ) . "', '" . $wpdb->escape( $mp3arr[$i]->description ) . "', '" . 
						$wpdb->escape( $mp3arr[$i]->title ) . "')";
			
			array_push( $arr, $values );
		}
		
		$insert .= join(", ", $arr );
		$results = $wpdb->query( $insert );
		
		return $results;
	}	
	
    function combineUrl($absolute, $relative) {
        $p = parse_url($relative);
        if( isset( $p["scheme"] )) { return $relative; }
        
        extract(parse_url($absolute));
        
        $path = dirname($path); 
    
        if($relative{0} == '/') {
            $cparts = array_filter(explode("/", $relative));
        }
        else {
            $aparts = array_filter(explode("/", $path));
            $rparts = array_filter(explode("/", $relative));
            $cparts = array_merge($aparts, $rparts);
            foreach($cparts as $i => $part) {
                if($part == '.') {
                    $cparts[$i] = null;
                }
                if($part == '..') {
                    $cparts[$i - 1] = null;
                    $cparts[$i] = null;
                }
            }
            $cparts = array_filter($cparts);
        }
        $path = implode("/", $cparts);
        $url = "";
        if($scheme) {
            $url = "$scheme://";
        }
        if( isset( $user )) {
            $url .= "$user";
            if($pass) {
                $url .= ":$pass";
            }
            $url .= "@";
        }
        if($host) {
            $url .= "$host/";
        }
        $url .= $path;
        return $url;
    }
	
	function dropSPTable() {
		global $wpdb;
		$wpdb->query("DROP TABLE " . $this->getTableName() );
	}
	
	function reSyncPosts() {
		// this function will delete all posts whose post_id <> 0
		global $wpdb;		
		$response = $wpdb->query("DELETE FROM " . $this->getTableName() . " WHERE post_id<>0");
		return $response;
	}
	
	function deleteByUniqueId( $unique_id ) {
		global $wpdb;
		//error_log( $unique_id );	
		$response = $wpdb->query("DELETE FROM " . $this->getTableName() . " WHERE id = '" . (int) $unique_id . "'");
		return $response;
	}
	
	function updateTrackByUniqueId( $unique_id, $title, $enclosure ) {
		global $wpdb;	
		$response = $wpdb->query("UPDATE " . $this->getTableName() . " 
					  SET enclosure='" . $enclosure . "', title='". $title ."' 
					  WHERE id = '" . (int) $unique_id . "'");
					  
		return $response;			  		
	}
	
	
	function getTableName() {
		global $wpdb;
		return $wpdb->prefix . $this->baseTable;
	}

}

?>
