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

if (!class_exists('Streampad_DB_Init')) {
	class Streampad_DB_Init {
		var $version = "1.0";
		var $baseTable = "StreamPad_Tracks";
		var $dbManage;
		
		function Streampad_DB_Init(){$this->__construct();}
		/* init obj */
		function __construct() {}
		
		function init() {
			global $wpdb;
			// check on db existence

			if($wpdb->get_var("show tables like '".$this->getTableName() ."'") == $this->getTableName() ) { return null; }
			
			$this->createDB();
			$this->combData();
			$this->spOptions();		
		}
		

		function createDB() {
		
		     $sql = "CREATE TABLE " . $this->getTableName() . " (
			  id bigint(20) NOT NULL AUTO_INCREMENT,
			  post_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  post_id bigint(20) NOT NULL,
			  enclosure VARCHAR(255) NOT NULL,
			  sourceUrl VARCHAR(255) NOT NULL,
			  description longtext NOT NULL,
			  title VARCHAR(255) NOT NULL,
			  UNIQUE KEY id (id),
			  INDEX (enclosure)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		
		function getTableName() {
			global $wpdb;
			return $wpdb->prefix . $this->baseTable;
		}
	
		function combData() {
			// get all the posts out with .mp3 in the body OR spmp3, store the URL, date and post id
			// query the db
			global $wpdb;
			
			require_once( "class-Streampad_DB_Manage.php" );
			$this->dbManage = new Streampad_DB_Manage();			
			
			$mp3streams = $wpdb->get_results("select guid, id, post_date, post_content from " . 
				$wpdb->prefix .
				"posts where (post_content like '%.mp3%' OR post_content like '%spmp3%')  AND post_status = 'publish' ");
				
			$mp3array = array();
			$count = count($mp3streams);				
			for ($i = 0; $i < $count; $i++) {
				$this->dbManage->parseContent( $mp3streams[$i]->id, $mp3streams[$i]->post_date, $mp3streams[$i]->post_content, $mp3streams[$i]->guid, $mp3array );
			}
			
			$count = count( $mp3array );
			if( $count > 0 ) {
				$this->dbManage->storeTracks( $mp3array, $count );
			}
			
			// parse the dom
		}
		
		function spOptions() {
			add_option("streampad_db_version", $this->version);
		}
	
	}
}



?>