<?php
/*
Plugin Name: WP to Twitter
Plugin URI: http://www.joedolson.com/articles/wp-to-twitter/
Description: Updates Twitter when you create a new blog post or add to your blogroll using Cli.gs. With a Cli.gs API key, creates a clig in your Cli.gs account with the name of your post as the title.
Version: 2.2.2
Author: Joseph Dolson
Author URI: http://www.joedolson.com/
*/
/*  Copyright 2008  Joseph C Dolson  (email : wp-to-twitter@joedolson.com)

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
// include service functions
require_once( WP_PLUGIN_DIR . '/wp-to-twitter/functions.php' );
require_once( WP_PLUGIN_DIR . '/wp-to-twitter/wp-to-twitter-oauth.php' );

global $wp_version,$version,$jd_plugin_url,$jdwp_api_post_status, $x_jdwp_post_status;	
$version = "2.2.2";
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'wp-to-twitter', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );


$jdwp_api_post_status = "http://twitter.com/statuses/update.json";
$x_jd_api_post_status = get_option( 'x_jd_api_post_status' );


$jd_plugin_url = "http://www.joedolson.com/articles/wp-to-twitter/";
$jd_donate_url = "http://www.joedolson.com/donate.php";

if ( !defined( 'WP_PLUGIN_DIR' ) ) {
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
}

// Check whether a supported version is in use.
$exit_msg='WP to Twitter requires WordPress 2.7 or a more recent version. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update your WordPress version!</a>';

	if ( version_compare( $wp_version,"2.7","<" )) {
	exit ($exit_msg);
	}
// check for OAuth configuration
	if ( !wtt_oauth_test() && get_option('disable_oauth_notice') != '1' ) {
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".sprintf(__('Twitter now requires authentication by OAuth. You will need you to update your <a href="%s">settings</a> in order to continue to use WP to Twitter.', 'wp-to-twitter'), admin_url('options-general.php?page=wp-to-twitter/wp-to-twitter.php'))."</p></div>';" ) );
	}	
	
function wptotwitter_activate() {
global $version;
$prev_version = get_option( 'wp_to_twitter_version',$version );
// this is a switch to plan for future versions
	/* switch($prev_version) {
		case '':
		break;
		default:
	} */
	update_option( 'wp_to_twitter_version',$version );
}	
	
// Function checks for an alternate URL to be tweeted. Contribution by Bill Berry.	
function external_or_permalink( $post_ID ) {
       $wtb_extlink_custom_field = get_option('jd_twit_custom_url'); 
       $perma_link = get_permalink( $post_ID );
			if ( $wtb_extlink_custom_field != '' ) {
				$ex_link = get_post_meta($post_ID, $wtb_extlink_custom_field, true);
			}
       return ( $ex_link ) ? $ex_link : $perma_link;
}

function jd_doUnknownAPIPost( $twit, $authID=FALSE, $service="basic" ) {
	global $version, $jd_plugin_url, $x_jd_api_post_status;
	//check if user login details have been entered on admin page
	if ($authID == FALSE || ( get_option( 'jd_individual_twitter_users' ) != '1' ) ) {
		$thisuser = get_option( 'x-login' );
		$thispass = stripcslashes( get_option( 'x-pw' ) );
	} else {
		if ( ( get_usermeta( $authID, 'wp-to-twitter-enable-user' ) == 'true' || get_usermeta( $authID, 'wp-to-twitter-enable-user' ) == 'userTwitter' || get_usermeta( $authID, 'wp-to-twitter-enable-user' ) == 'userAtTwitter' ) && ( get_usermeta( $authID, 'wp-to-twitter-user-username' ) != "" && get_usermeta( $authID, 'wp-to-twitter-user-password' ) != "" ) ) {	
			$thisuser = get_usermeta( $authID, 'wp-to-twitter-user-username' );
			$thispass = stripcslashes( get_usermeta( $authID, 'wp-to-twitter-user-password' ) );
		} else {
			$thisuser = get_option( 'x-login' );
			$thispass = stripcslashes( get_option( 'x-pw' ) );	
		}
	}
	if ($thisuser == '' || $thispass == '' || $twit == '' ) {
	return FALSE;
	} else {
		if ( $service == "Twitter" ) {
			return false;
		} 
		$twit = urldecode( $twit );
		$body =    array( 'status'=>$twit, 'source'=>'wptotwitter' );
		$headers = array( 'Authorization' => 'Basic '.base64_encode("$thisuser:$thispass"), 
			'X-Twitter-Client'=>'WP to Twitter',
			'X-Twitter-Client-Version' => $version, 
			'X-Twitter-Client-URL' => 'http://www.joedolson.com/scripts/wp-to-twitter.xml'
		);
		$result = jd_fetch_url( $x_jd_api_post_status, 'POST', $body, $headers, 'full' );
		// errors will be handled on receipt of $result
		if ( $result['response']['code'] != 200 ) {
		return false;
		} else {
		return true;
		}
	}
}


// This function performs the API post to Twitter
function jd_doTwitterAPIPost( $twit ) {
	global $jdwp_api_post_status;
	//check if user login details have been entered on admin page
	if ( $twit == '' ) {
	return FALSE;
	} else {
		if ( wtt_oauth_test() && ( $connection = wtt_oauth_connection() ) ) {
			$connection->post(
				$jdwp_api_post_status
				, array(
					'status' => $twit
					, 'source' => 'wp-to-twitter'
				)
			);
			if ( strcmp( $connection->http_code, '200' ) == 0 ) {
					$return = true;
					$error = __("200 OK: Success!",'wp-to-twitter');
			} else if ( strcmp( $connection->http_code, '400' ) == 0 ) {
					$return = false;
					$error = __("400 Bad Request: The request was invalid. This is the status code returned during rate limiting.",'wp-to-twitter');
			} else if ( strcmp( $connection->http_code, '401' ) == 0 ) {
					$return = false;
					$error = __("401 Unauthorized: Authentication credentials were missing or incorrect.",'wp-to-twitter');
			} else if ( strcmp( $connection->http_code, '403' ) == 0 ) {
					$return = false;
					$error = __("403 Forbidden: The request is understood, but it has been refused. This code is used when requests are being denied due to update limits.",'wp-to-twitter');							} else if ( strcmp( $connection->http_code, '500' ) == 0 ) {
					$return = false;
					$error = __("500 Internal Server Error: Something is broken at Twitter.",'wp-to-twitter');				
			} else if ( strcmp( $connection->http_code, '503' ) == 0 ) {
					$return = false;
					$error = __("503 Service Unavailable: The Twitter servers are up, but overloaded with requests Please try again later.",'wp-to-twitter');				
			} else if ( strcmp( $connection->http_code, '502' ) == 0 ) {
					$return = false;
					$error = __("502 Bad Gateway: Twitter is down or being upgraded.",'wp-to-twitter');				
			} 
			update_option( 'jd_last_tweet',$twit );
			update_option( 'jd_status_message',$error );
			return $return;			
		}		
	}
}

function jd_truncate_tweet( $sentence, $thisposttitle, $thisblogtitle, $thispostexcerpt, $thisposturl, $thispostcategory, $thisdate, $post_ID, $authID=FALSE ) {
$post_length = 140;
$sentence = trim($sentence);
$thisposttitle = trim($thisposttitle);
$thisblogtitle = trim($thisblogtitle);
$thispostexcerpt = trim($thispostexcerpt);
$thisposturl = trim($thisposturl);
$thispostcategory = trim($thispostcategory);
	$post = get_post( $post_ID );
	$post_author = $post->post_author;
$thisauthor = get_the_author_meta( 'display_name',$post_author );

if ( get_option( 'jd_individual_twitter_users' ) == 1 ) {
	if ( get_usermeta( $authID, 'wp-to-twitter-enable-user' ) == 'mainAtTwitter' ) {
	$at_append = "@" . stripcslashes(get_usermeta( $authID, 'wp-to-twitter-user-username' ));
	} else if ( get_usermeta( $authID, 'wp-to-twitter-enable-user' ) == 'mainAtTwitterPlus' ) {
	$at_append = "@" . stripcslashes(get_usermeta( $authID, 'wp-to-twitter-user-username' ) . ' @' . get_option( 'wtt_twitter_username' ));
	}
} else {
$at_append = "";
}
if ($sentence != '') {
	$sentence = $at_append . " " . $sentence;
}
	if ( get_option( 'use_tags_as_hashtags' ) == '1'  && $sentence != '' ) {
		$sentence = $sentence . " " . generate_hash_tags( $post_ID );
	}	
	
	if ( get_option( 'jd_twit_prepend' ) != "" && $sentence != '' ) {
	$sentence = get_option( 'jd_twit_prepend' ) . " " . $sentence;
	}
	if ( get_option( 'jd_twit_append' ) != "" && $sentence != '' ) {
	$sentence = $sentence . " " . get_option( 'jd_twit_append' );
	}
	if ( mb_substr( $thispostexcerpt, -1 ) == ";" || mb_substr( $thispostexcerpt, -1 ) == "," || mb_substr( $thispostexcerpt, -1 ) == ":" ) {
	$thispostexcerpt = mb_substr_replace( $thispostexcerpt,"",-1, 1 );
	}
	if ( mb_substr( $thispostexcerpt, -1 ) != "." && mb_substr( $thispostexcerpt, -1 ) != "?" && mb_substr( $thispostexcerpt, -1 ) != "!" ) {
	$thispostexcerpt = $thispostexcerpt . "...";
	}
$post_sentence = str_ireplace( "#url#", $thisposturl, $sentence );
$post_sentence = str_ireplace( '#title#', $thisposttitle, $post_sentence );
$post_sentence = str_ireplace ( '#blog#',$thisblogtitle,$post_sentence );
$post_sentence = str_ireplace ( '#post#',$thispostexcerpt,$post_sentence );
$post_sentence = str_ireplace ( '#category#',$thispostcategory,$post_sentence );
$post_sentence = str_ireplace ( '#date#', $thisdate,$post_sentence );
$post_sentence = str_ireplace ( '#author#', $thisauthor,$post_sentence );

$str_length = mb_strlen( urldecode( $post_sentence ) );
$length = get_option( 'jd_post_excerpt' );

$length_array = array();
//$order = get_option( 'jd_truncation_sort_order' );
$length_array['thispostexcerpt'] = mb_strlen($thispostexcerpt);
$length_array['thisblogtitle'] = mb_strlen($thisblogtitle);
$length_array['thisposttitle'] = mb_strlen($thisposttitle);
$length_array['thispostcategory'] = mb_strlen($thispostcategory);
$length_array['thisdate'] = mb_strlen($thisdate);
$length_array['thisauthor'] = mb_strlen($thisauthor);

if ( $str_length > $post_length ) {
	foreach($length_array AS $key=>$value) {
		if ( ( $str_length > $post_length ) && ($str_length - $value) < $post_length ) {
			$trim = $str_length - $post_length;
			$old_value = ${$key};
			$new_value = mb_substr( $old_value,0,-( $trim ) );
			$post_sentence = str_ireplace( $old_value,$new_value,$post_sentence );
			$str_length = mb_strlen( urldecode( $post_sentence ) );
		} else {
		}
	}
}
$sentence =  $post_sentence;
return $sentence;
}

function jd_shorten_link( $thispostlink, $thisposttitle, $post_ID, $testmode='false' ) {
		$cligsapi =  trim ( get_option( 'cligsapi' ) );
		$bitlyapi =  trim ( get_option( 'bitlyapi' ) );
		$bitlylogin =  trim ( get_option( 'bitlylogin' ) );
		$yourlslogin =  trim ( get_option( 'yourlslogin') );
		$yourlsapi = stripcslashes( get_option( 'yourlsapi' ) );
		if ($testmode != 'false') {
			if ( ( get_option('twitter-analytics-campaign') != '' ) && ( get_option('use-twitter-analytics') == 1 || get_option('use_dynamic_analytics') == 1 ) ) {
				if ( get_option('use_dynamic_analytics') == '1' ) {
					$campaign_type = get_option('jd_dynamic_analytics');
					if ($campaign_type == "post_category" && $testmode != 'link' ) {
						$category = get_the_category( $post_ID );
						$this_campaign = $category[0]->cat_name;
					} else if ($campaign_type == "post_ID") {
						$this_campaign = $post_ID;
					} else if ($campaign_type == "post_title" && $testmode != 'link' ) {
						$post = get_post( $post_ID );
						$this_campaign = $post->post_title; 
					} else {
						if ( $testmode != 'link' ) {
						$post = get_post( $post_ID );
						$post_author = $post->post_author;
						$this_campaign = get_the_author_meta( 'user_login',$post_author );
						} else {
							$post_author = '';
							$this_campaign = '';
						}
					}
				} else {
				$this_campaign = get_option('twitter-analytics-campaign');
				}
				$search = array(" ","&","?");
				$this_campaign = str_replace($search,'',$this_campaign);
				if ( strpos( $thispostlink,"%3F" ) === FALSE) {
				$thispostlink .= "?";
				} else {
				$thispostlink .= "&";
				}
				$thispostlink .= "utm_campaign=$this_campaign&utm_medium=twitter&utm_source=twitter";
			}
		}
		$thispostlink = urlencode(trim($thispostlink));

		// custom word setting
		$keyword_format = ( get_option( 'jd_keyword_format' ) == '1' )?$post_ID:'';
		// Generate and grab the short url
		switch ( get_option( 'jd_shortener' ) ) {
			case 0:
			case 1:
			$shrink = jd_fetch_url( "http://cli.gs/api/v1/cligs/create?t=wphttp&appid=WP-to-Twitter&url=".$thispostlink."&title=".$thisposttitle."&key=".$cligsapi );
			update_option( 'wp_cligs_error',"Cligs API result: $shrink" );					
			if ( !is_valid_url($shrink) ) { $shrink = false; }
			break;
			case 2: // updated to v3 3/31/2010
			$decoded = jd_remote_json( "http://api.bit.ly/v3/shorten?longUrl=".$thispostlink."&login=".$bitlylogin."&apiKey=".$bitlyapi."&format=json" );
				if ($decoded) {
					if ($decoded['status_code'] != 200) {
						$shrink = $decoded;
						$error = $decoded['status_txt'];
					} else {
						$shrink = $decoded['data']['url'];		
					}
				} else {
				$shrink = false;
				update_option( 'wp_bitly_error',"JSON result could not be decoded");
				}	
				if ( !is_valid_url($shrink) ) { $shrink = false; update_option( 'wp_bitly_error',$error ); }
			break;
			case 3:
			$shrink = urldecode($thispostlink);
			break;
			case 4:
			$shrink = get_bloginfo('url') . "/?p=" . $post_ID;
			break;
			case 5:
			// local YOURLS installation
			$thispostlink = urldecode($thispostlink);
			global $yourls_reserved_URL;
			define('YOURLS_INSTALLING', true); // Pretend we're installing YOURLS to bypass test for install or upgrade
			define('YOURLS_FLOOD_DELAY_SECONDS', 0); // Disable flood check
			if( file_exists( dirname( get_option( 'yourlspath' ) ).'/load-yourls.php' ) ) { // YOURLS 1.4
				global $ydb;
				require_once( dirname( get_option( 'yourlspath' ) ).'/load-yourls.php' ); 
				$yourls_result = yourls_add_new_link( $thispostlink, $keyword_format );
			} else { // YOURLS 1.3
				require_once( get_option( 'yourlspath' ) ); 
				$yourls_db = new wpdb( YOURLS_DB_USER, YOURLS_DB_PASS, YOURLS_DB_NAME, YOURLS_DB_HOST );
				$yourls_result = yourls_add_new_link( $thispostlink, $keyword_format, $yourls_db );
			}
			if ($yourls_result) {
				$shrink = $yourls_result['shorturl'];			
			} else {
				$shrink = false;
			}
			break;
			case 6:
			// remote YOURLS installation
			$api_url = sprintf( get_option('yourlsurl') . '?username=%s&password=%s&url=%s&format=json&action=shorturl&keyword=%s',
				$yourlslogin, $yourlsapi, $thispostlink, $keyword_format );
			$json = jd_remote_json( $api_url, false );			
			if ($json) {
				$shrink = $json->shorturl;
			} else {
				$shrink = false;
			}
			break;
		}
		if ($testmode == 'false') {
			if ( $shrink === FALSE || ( stristr( $shrink, "http://" ) === FALSE )) {
				update_option( 'wp_url_failure','1' );
				$shrink = $thispostlink;
			} else {
				update_option( 'wp_url_failure','0' );
			}
		}
	return $shrink;
}

function jd_expand_url( $short_url ) {
	$short_url = urlencode( $short_url );
	$decoded = jd_remote_json("http://api.longurl.org/v2/expand?format=json&url=" . $short_url );
	$url = $decoded['long-url'];
	return $url;
	//return $short_url;
}
function jd_expand_yourl( $short_url, $remote ) {
	if ( $remote == 6 ) {
		$short_url = urlencode( $short_url );
		$yourl_api = get_option( 'yourlsurl' );
		$user = get_option( 'yourlslogin' );
		$pass = stripcslashes( get_option( 'yourlsapi' ) );
		$decoded = jd_remote_json( $yourl_api . "?action=expand&shorturl=$short_url&format=json&username=$user&password=$pass" );
		$url = $decoded['longurl'];
		return $url;
	} else {
		global $yourls_reserved_URL;
		define('YOURLS_INSTALLING', true); // Pretend we're installing YOURLS to bypass test for install or upgrade
		define('YOURLS_FLOOD_DELAY_SECONDS', 0); // Disable flood check
		if ( file_exists( dirname( get_option( 'yourlspath' ) ).'/load-yourls.php' ) ) { // YOURLS 1.4
			global $ydb;
			require_once( dirname( get_option( 'yourlspath' ) ).'/load-yourls.php' ); 
			$yourls_result = yourls_api_expand( $short_url );
		} else { // YOURLS 1.3
			require_once( get_option( 'yourlspath' ) ); 
			$yourls_db = new wpdb( YOURLS_DB_USER, YOURLS_DB_PASS, YOURLS_DB_NAME, YOURLS_DB_HOST );
			$yourls_result = yourls_api_expand( $short_url );
		}	
		$url = $yourls_result['longurl'];
		return $url;
	}
}

function in_allowed_category( $array ) {
	$allowed_categories =  get_option( 'tweet_categories' );
	if ( is_array( $array ) && is_array( $allowed_categories ) ) {
	$common = @array_intersect( $array,$allowed_categories );
		if ( count( $common ) >= 1 ) {
			return true;
		} else {
			return false;
		}
	} else {
	return true;
	}
}

function jd_post_info( $post_ID ) {
	$get_post_info = get_post( $post_ID );
	$values = array();
	// get post author
	$values['authId'] = $get_post_info->post_author;
		$postdate = $get_post_info->post_date;
		$dateformat = (get_option('jd_date_format')=='')?get_option('date_format'):get_option('jd_date_format');
		$thisdate = mysql2date( $dateformat,$postdate );
	$values['postDate'] = $thisdate;
	// get first category
		$category = null;
		$categories = get_the_category( $post_ID );
		if ( $categories > 0 ) {
			$category = $categories[0]->cat_name;
		}		
		foreach ($categories AS $cat) {
			$category_ids[] = $cat->term_id;
		}
	$values['categoryIds'] = $category_ids;
	$values['category'] = $category;
		$excerpt_length = get_option( 'jd_post_excerpt' );
	$values['postExcerpt'] = ( trim( $get_post_info->post_excerpt ) == "" )?@mb_substr( strip_tags($get_post_info->post_content), 0, $excerpt_length ):@mb_substr( strip_tags($get_post_info->post_excerpt), 0, $excerpt_length );
	$thisposttitle =  stripcslashes( strip_tags( $get_post_info->post_title ) );
		if ($thisposttitle == "") {
			$thisposttitle =  stripcslashes( strip_tags( $_POST['title'] ) );
		}
	$values['postTitle'] = $thisposttitle;
	$values['postLink'] = external_or_permalink( $post_ID );
	$values['blogTitle'] = get_bloginfo( 'name' );
	$values['shortUrl'] = get_post_meta( $post_ID, '_wp_jd_clig', TRUE );
	$values['postStatus'] = $get_post_info->post_status;
	return $values;
}


function jd_get_post_meta( $post_ID, $value, $boolean ) {
	$return = get_post_meta( $post_ID, "_$value", TRUE );
	if (!$return) {
		$return = get_post_meta( $post_ID, $value, TRUE );
	}
	return $return;
}

function jd_twit( $post_ID ) {	
	$jd_tweet_this = get_post_meta( $post_ID, '_jd_tweet_this', TRUE);
	if ( $jd_tweet_this != "no" ) {
		$jd_post_info = jd_post_info( $post_ID );
	    $sentence = '';
		$customTweet = stripcslashes( trim( $_POST['_jd_twitter'] ) );
		if ( ( $jd_post_info['postStatus'] == 'publish' || $_POST['publish'] == 'Publish') && ($_POST['prev_status'] == 'draft' || $_POST['original_post_status'] == 'draft' || $_POST['prev_status'] == 'pending' || $_POST['original_post_status'] =='pending' || $_POST['original_post_status'] == 'auto-draft' ) ) {
		// publish new post
				if ( get_option( 'newpost-published-update' ) == '1' ) {
					$nptext = stripcslashes( get_option( 'newpost-published-text' ) );	
					$newpost = true;
				}
		} else if ( (( $_POST['originalaction'] == "editpost" ) && ( ( $_POST['prev_status'] == 'publish' ) || ($_POST['original_post_status'] == 'publish') ) ) && $jd_post_info['postStatus'] == 'publish') {
				// if this is an old post and editing updates are enabled
				if ( get_option( 'oldpost-edited-update') == '1' ) {
				    $nptext = stripcslashes( get_option( 'oldpost-edited-text' ) );
					$oldpost = true;
				}			
		}
		if ($newpost || $oldpost) {
			$sentence = ( $customTweet != "" ) ? $customTweet : $nptext;
			if ($jd_post_info['shortUrl'] != '') {
				$shrink = $jd_post_info['shortUrl'];
			} else {
				$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
				store_url( $post_ID, $shrink );
			}		
			$sentence = custom_shortcodes( $sentence, $post_ID );					
			$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );		
		}
			
		if ( $sentence != '' ) {
			if ( get_option('limit_categories') == '0' || in_allowed_category( $jd_post_info['categoryIds'] ) ) {
				$sendToTwitter = ( get_option( 'x_jd_api_post_status' ) == '' )?jd_doTwitterAPIPost( $sentence ):jd_doUnknownAPIPost( $sentence, $jd_post_info['authId']  );
				if ( $sendToTwitter == false ) {
					update_post_meta( $post_ID,'_jd_wp_twitter',urldecode( $sentence ) );
					update_option( 'wp_twitter_failure','1' );
				}
			}
		}
	}
	return $post_ID;
}

function jd_twit_page( $post_ID ) {	
	$jd_tweet_this = get_post_meta( $post_ID, '_jd_tweet_this', TRUE);
	if ( $jd_tweet_this != "no" ) {
		$jd_post_info = jd_post_info( $post_ID );
	    $sentence = '';
		$customTweet = stripcslashes( trim( $_POST['_jd_twitter'] ) );
		if (( $jd_post_info['postStatus'] == 'publish' || $_POST['publish'] == 'Publish') && ($_POST['prev_status'] == 'draft' || $_POST['original_post_status'] == 'draft' || $_POST['original_post_status'] == 'auto-draft' ) ) {
				$newpost = true; // publish new post
				if ( get_option( 'jd_twit_pages' ) == '1' ) {
				    $nptext = stripcslashes( get_option( 'newpage-published-text' ) );
				}
		} else if ( (( $_POST['originalaction'] == "editpost" ) && ( ( $_POST['prev_status'] == 'publish' ) || ($_POST['original_post_status'] == 'publish') ) ) && $jd_post_info['postStatus'] == 'publish') {
				$oldpost = true; // if this is an old page and editing updates are enabled
			if ( get_option( 'jd_twit_edited_pages' ) == '1' ) {
				    $nptext = stripcslashes( get_option( 'oldpage-published-text' ) );
			}
		}
		if ($newpost || $oldpost) {
			$sentence = ( $customTweet != "" ) ? $customTweet : $nptext;
			if ($jd_post_info['shortUrl'] != '') {
				$shrink = $jd_post_info['shortUrl'];
			} else {
				$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
				store_url( $post_ID, $shrink );
			}		
			$sentence = custom_shortcodes( $sentence, $post_ID );					
			$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );		
		}			
		if ( $sentence != '' ) {
			if ( get_option('limit_categories') == '0' || in_allowed_category( $jd_post_info['categoryIds'] ) ) {
				$sendToTwitter = ( get_option( 'x_jd_api_post_status' ) == '' )?jd_doTwitterAPIPost( $sentence ):jd_doUnknownAPIPost( $sentence, $jd_post_info['authId']  );
				if ( $sendToTwitter == false ) {
					update_post_meta( $post_ID,'_jd_wp_twitter',urldecode( $sentence ) );
					update_option( 'wp_twitter_failure','1' );
				}
			}
		}
	}
	return $post_ID;
} // page tweet end

// Add Tweets on links in Blogroll
function jd_twit_link( $link_ID )  {
global $version;
	$thislinkprivate = $_POST['link_visible'];
	if ($thislinkprivate != 'N') {
		$thislinkname = stripcslashes( $_POST['link_name'] );
		$thispostlink =  $_POST['link_url'] ;
		$thislinkdescription =  stripcslashes( $_POST['link_description'] );
		$sentence = stripcslashes( get_option( 'newlink-published-text' ) );
		if ( get_option( 'jd-use-link-description' ) == '1' || get_option ( 'jd-use-link-title' ) == '1' ) {
			if ( get_option( 'jd-use-link-description' ) == '1' && get_option ( 'jd-use-link-title' ) == '0' ) {
			$sentence = $sentence . ' ' . $thislinkdescription;
			} else if ( get_option( 'jd-use-link-description' ) == '0' && get_option ( 'jd-use-link-title' ) == '1' ) {
			$sentence = $sentence . ' ' . $thislinkname;
			}
		} else {
			$sentence = str_ireplace("#title#",$thislinkname,$sentence);
			$sentence = str_ireplace("#description#",$thislinkdescription,$sentence);		 
		}
		if (mb_strlen( $sentence ) > 120) {
			$sentence = mb_substr($sentence,0,116) . '...';
		}
		$shrink = jd_shorten_link( $thispostlink, $thislinkname, $link_ID, 'link' );
				if ( stripos($sentence,"#url#") === FALSE ) {
				$sentence = $sentence . " " . $shrink;
				} else {
				$sentence = str_ireplace("#url#",$shrink,$sentence);
				}						
			if ( $sentence != '' ) {
				$sendToTwitter = ( get_option( 'x_jd_api_post_status' ) == '' )?jd_doTwitterAPIPost( $sentence ):jd_doUnknownAPIPost( $sentence, $jd_post_info['authId']  );	
			if ( $sendToTwitter == false ) {
				update_option('wp_twitter_failure','2');
				}
			}
		return $link_ID;
	} else {
	return '';
	}
}

// HANDLES SCHEDULED POSTS
function jd_twit_future( $post_ID ) {
    $post_ID = $post_ID->ID;
	if ( $jd_tweet_this != "no" ) {	
		$jd_post_info = jd_post_info( $post_ID );
		$sentence = '';
		$customTweet = get_post_meta( $post_ID, '_jd_twitter', TRUE ); 
		$sentence = stripcslashes(get_option( 'newpost-published-text' ));
		$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
			// Stores the post's short URL in a custom field for later use as needed.
			store_url($post_ID, $shrink);
				if ( $customTweet != "" ) {
				$sentence = $customTweet;
				}  
			$sentence = custom_shortcodes( $sentence, $post_ID );			
			$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );		
		
			if ( $sentence != '' ) {
				if ( get_option('limit_categories') == '0' || in_allowed_category( $jd_post_info['categoryIds'] ) ) {
					$sendToTwitter = ( get_option( 'x_jd_api_post_status' ) == '' )?jd_doTwitterAPIPost( $sentence ):jd_doUnknownAPIPost( $sentence, $jd_post_info['authId']  );	
					if ( $sendToTwitter == false ) {
						add_post_meta( $post_ID,'_jd_wp_twitter',urldecode($sentence) );
						update_option( 'wp_twitter_failure','1' );
					}
				}
			}			
		return $post_ID;
	}	
} // END jd_twit_future

// Tweet from QuickPress (no custom fields, so can't control whether to tweet.)
function jd_twit_quickpress( $post_ID ) {
    $post_ID = $post_ID->ID;
	$jd_post_info = jd_post_info ( $post_ID );
	$sentence = '';
	$sentence = stripcslashes(get_option( 'newpost-published-text' ));
	$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
		// Stores the posts CLIG in a custom field for later use as needed.
		store_url($post_ID, $shrink);			
		$sentence = custom_shortcodes( $sentence, $post_ID );		
		$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );		
		if ( $sentence != '' ) {
			if ( get_option('limit_categories') == '0' || in_allowed_category( $jd_post_info['categoryIds'] ) ) {
					$sendToTwitter = ( get_option( 'x_jd_api_post_status' ) == '' )?jd_doTwitterAPIPost( $sentence ):jd_doUnknownAPIPost( $sentence, $jd_post_info['authId']  );	
				if ($sendToTwitter == false ) {
					add_post_meta( $post_ID,'_jd_wp_twitter',urldecode($sentence) );
					update_option( 'wp_twitter_failure','1' );
				}
			}
		}
		return $post_ID;	
} // END jd_twit_quickpress

// HANDLES xmlrpc POSTS
function jd_twit_xmlrpc( $post_ID ) {
	$get_post_info = get_post( $post_ID );
	$post_status = $get_post_info->post_status;	
	if ( get_option('oldpost-edited-update') != 1 && get_post_meta ( $post_ID, '_wp_jd_clig', TRUE ) != '' ) {
		return;
	} else {	
	if ( get_option('jd_tweet_default') != '1' && get_option('jd_twit_remote') == '1' ) {
		$jd_post_info = jd_post_info( $post_ID );
		$sentence = '';
		$sentence = stripcslashes(get_option( 'newpost-published-text' ));
			$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
			// Stores the posts CLIG in a custom field for later use as needed.
			store_url($post_ID, $shrink);				
			// Check the length of the tweet and truncate parts as necessary.
			$sentence = custom_shortcodes( $sentence, $post_ID );			
			$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );		
			
		if ( $sentence != '' ) {	
			if ( get_option('limit_categories') == '0' || in_allowed_category( $jd_post_info['categoryIds'] ) ) {
				$sendToTwitter = ( get_option( 'x_jd_api_post_status' ) == '' )?jd_doTwitterAPIPost( $sentence ):jd_doUnknownAPIPost( $sentence, $jd_post_info['authId']  );	
				if ($sendToTwitter == false ) {
					add_post_meta( $post_ID,'_jd_wp_twitter',urldecode($sentence));
					update_option('wp_twitter_failure','1');
				}
			}				
		}
	}
	return $post_ID;
	}
} // END jd_twit_xmlrpc


add_action('admin_menu','jd_add_twitter_outer_box');

function store_url($post_ID, $url) {
	if ( get_option( 'jd_shortener' ) == 0 || get_option( 'jd_shortener' ) == 1) {
			if ( get_post_meta ( $post_ID, '_wp_jd_clig', TRUE ) != $url ) {
			update_post_meta ( $post_ID, '_wp_jd_clig', $url );
			}	
	} elseif ( get_option( 'jd_shortener' ) == 2 ) {
			if ( get_post_meta ( $post_ID, '_wp_jd_bitly', TRUE ) != $url ) {
			update_post_meta ( $post_ID, '_wp_jd_bitly', $url );
			}	
	} elseif (  get_option( 'jd_shortener' ) == 3 ) {
			if ( get_post_meta ( $post_ID, '_wp_jd_url', TRUE ) != $url ) {
			update_post_meta ( $post_ID, '_wp_jd_url', $url );
			}
	} elseif (  get_option( 'jd_shortener' ) == 4 ) {
			if ( get_post_meta ( $post_ID, '_wp_jd_wp', TRUE ) != $url ) {
			update_post_meta ( $post_ID, '_wp_jd_wp', $url );
			}
	} elseif (  get_option( 'jd_shortener' ) == 5 || get_option( 'jd_shortener' ) == 6 ) {
			if ( get_post_meta ( $post_ID, '_wp_jd_yourls', TRUE ) != $url ) {
			update_post_meta ( $post_ID, '_wp_jd_yourls', $url );
			}
	}
	if ( get_option( 'jd_shortener' ) == '0' || get_option( 'jd_shortener' ) == '1' || get_option( 'jd_shortener' ) == '2' ) {
	$target = jd_expand_url( $url );
	} else if ( get_option( 'jd_shortener' ) == '5' || get_option( 'jd_shortener' ) == '6' ) {
	$target = jd_expand_yourl( $url, get_option( 'jd_shortener' ) );
	} else {
	$target = $url;
	}
	update_post_meta( $post_ID, '_wp_jd_target', $target );
}

function generate_hash_tags( $post_ID ) {

$max_tags = get_option( 'jd_max_tags' );
$max_characters = get_option( 'jd_max_characters' );

	if ($max_characters == 0 || $max_characters == "") {
		$max_characters = 100;
	} else {
		$max_characters = $max_characters + 1;
	}
	if ($max_tags == 0 || $max_tags == "") {
		$max_tags = 100;
	}

		$tags = get_the_tags( $post_ID );
		if ( $tags > 0 ) {
		$i = 1;
			foreach ( $tags as $value ) {
			$tag = $value->name;
			$replace = get_option( 'jd_replace_character' );
			if ($replace == "" || !$replace) { $replace = "_"; } 
			if ($replace == "[ ]") { $replace = ""; }
			$value = str_ireplace( " ",$replace,trim( $tag ) );
				$newtag = "#$value";
					if ( mb_strlen( $newtag ) > 2 && (mb_strlen( $newtag ) <= $max_characters) && ($i <= $max_tags) ) {
					$hashtags .= "$newtag ";
					$i++;
					}
			}
		}
	$hashtags = trim( $hashtags );
	if ( mb_strlen( $hashtags ) <= 1 ) {
	$hashtags = "";
	}		
	return $hashtags;	
}

function jd_add_twitter_old_box() {
?>

<div class="dbx-b-ox-wrapper">
<fieldset id="twitdiv" class="dbx-box">
<div class="dbx-h-andle-wrapper">
<h3 class="dbx-handle"><?php _e('WP to Twitter', 'wp-to-twitter', 'wp-to-twitter') ?></h3>
</div>
<div class="dbx-c-ontent-wrapper">
<div class="dbx-content">
<?php
jd_add_twitter_inner_box();
?>
</div>
</fieldset>
</div>
<?php
}

function jd_add_twitter_inner_box() {
	$twitter = "Twitter";
	$post_length = 140;

global $post, $jd_plugin_url;
	$post_id = $post;
	if (is_object($post_id)) {
		$post_id = $post_id->ID;
	}
	if ( get_post_meta ( $post_id, "_jd_post_meta_fixed", true ) != 'true' ) {
		jd_fix_post_meta( $post_id );
	}
	
	$jd_twitter = htmlspecialchars( stripcslashes( get_post_meta($post_id, '_jd_twitter', true ) ) );
	$jd_tweet_this = get_post_meta( $post_id, '_jd_tweet_this', true );
		if ( ( get_option( 'jd_tweet_default' ) == '1' && $jd_tweet_this != 'yes' ) || $jd_tweet_this == 'no') {
		$jd_selected = ' checked="checked"';
		} else {
		$jd_selected = '';		
		}
	$jd_short = get_post_meta( $post_id, '_wp_jd_clig', true );
	$shortener = "Cli.gs";
	if ( $jd_short == "" ) {
		$jd_short = get_post_meta( $post_id, '_wp_jd_bitly', true );
		$shortener = "Bit.ly";
	}
	if ( $jd_short == "" ) {
		$jd_short = get_post_meta( $post_id, '_wp_jd_wp', true );
		$shortener = "WordPress";
	}	
	if ( $jd_short == "" ) {
		$jd_short = get_post_meta( $post_id, '_wp_jd_yourls', true );
		$shortener = "YOURLS";
	}
	if ( $jd_short == "" ) {
		$jd_direct = get_post_meta( $post_id, '_wp_jd_url', true );
	}		
	$jd_expansion = get_post_meta( $post_id, '_wp_jd_target', true );
	$previous_tweet = get_post_meta ( $post_id, '_jd_wp_twitter', true );
	?>
<script type="text/javascript">
<!-- Begin
function countChars(field,cntfield) {
cntfield.value = field.value.length;
}
//  End -->
</script>
<?php if ( $previous_tweet != '' ) {
echo "<p class='error'><strong>Previous Tweet:</strong> $previous_tweet</p>";
} ?>
<p>
<label for="jd_twitter"><?php _e("Custom $twitter Post", 'wp-to-twitter', 'wp-to-twitter') ?></label><br /><textarea style="width:95%;" name="_jd_twitter" id="jd_twitter" rows="2" cols="60"
	onKeyDown="countChars(document.post.jd_twitter,document.post.twitlength)"
	onKeyUp="countChars(document.post.jd_twitter,document.post.twitlength)"><?php echo attribute_escape( $jd_twitter ); ?></textarea>
</p>
<p><input readonly type="text" name="twitlength" size="3" maxlength="3" value="<?php echo attribute_escape( mb_strlen( $description) ); ?>" />
<?php $minus_length = $post_length - 21; ?>
<?php _e(" characters.<br />$twitter posts are a maximum of $post_length characters; if your short URL is appended to the end of your document, you have about $minus_length characters available. You can use <code>#url#</code>, <code>#title#</code>, <code>#post#</code>, <code>#category#</code>, <code>#date#</code>, <code>#author#</code>, or <code>#blog#</code> to insert the shortened URL, post title, the first category selected, the post date, the post author, or a post excerpt or blog name into the Tweet.", 'wp-to-twitter', 'wp-to-twitter') ?> 
</p>
<p>
<a target="__blank" href="<?php echo $jd_donate_url; ?>"><?php _e('Make a Donation', 'wp-to-twitter', 'wp-to-twitter') ?></a> &bull; <a target="__blank" href="<?php echo $jd_plugin_url; ?>"><?php _e('Get Support', 'wp-to-twitter', 'wp-to-twitter') ?></a> &raquo;
</p>
<p>
<input type="checkbox" name="_jd_tweet_this" value="no"<?php echo attribute_escape( $jd_selected ); ?> id="jd_tweet_this" /> <label for="jd_tweet_this"><?php _e("Don't Tweet this post.", 'wp-to-twitter'); ?></label>
</p>
<p>
<?php
$this_post = get_post($post_id);
$post_status = $this_post->post_status;
if ($post_status == 'publish') {
	if ( $jd_short != "" ) {
		_e("The previously-posted $shortener URL for this post is <code>$jd_short</code>, which points to <code>$jd_expansion</code>.", 'wp-to-twitter');
	} else {
		_e("This URL is direct and has not been shortened: ","wp-to-twitter"); echo "<code>$jd_direct</code>";
	}
}
?>
</p>
<?php } 
function jd_add_twitter_outer_box() {
	if ( function_exists( 'add_meta_box' )) {
    add_meta_box( 'wptotwitter_div','WP to Twitter', 'jd_add_twitter_inner_box', 'post', 'advanced' );
		if (  get_option( 'jd_twit_pages') == 1 ) {
			add_meta_box( 'wptotwitter_div','WP to Twitter', 'jd_add_twitter_inner_box', 'page', 'advanced' );
		}
   } else {
    add_action('dbx_post_advanced', 'jd_add_twitter_old_box' );
		if ( get_option( 'jd_twit_pages') == 1 ) {
			add_action('dbx_page_advanced', 'jd_add_twitter_old_box' );
		}
  }
}

function jd_fix_post_meta( $post_id ) {
	$oldmeta = array('jd_tweet_this','jd_twitter','wp_jd_clig','wp_jd_bitly','wp_jd_wp','wp_jd_yourls','wp_jd_url','wp_jd_target','jd_wp_twitter');
	foreach ($oldmeta as $value) {
		$old_value = get_post_meta($post_id,$value,true);
		update_post_meta( $post_id, "_$value", $old_value );
		delete_post_meta( $post_id, $value );
	}
	add_post_meta( $post_id, "_jd_post_meta_fixed",'true' );
}

// Post the Custom Tweet into the post meta table
function post_jd_twitter( $id ) {
	// update meta data to new format
	if ( get_post_meta ( $post_id, "_jd_post_meta_fixed", true ) != 'true' ) {
		jd_fix_post_meta( $post_id );
	}
	$jd_twitter = $_POST[ '_jd_twitter' ];
		if (isset($jd_twitter) && !empty($jd_twitter)) {
			update_post_meta( $id, '_jd_twitter', $jd_twitter );
		}
	$jd_tweet_this = esc_attr($_POST[ '_jd_tweet_this' ]);
	update_post_meta( $id, '_jd_tweet_this', $jd_tweet_this );
}


function jd_twitter_profile() {
		global $user_ID;
		get_currentuserinfo();
		if ( isset($_GET['user_id']) ) { 
			$user_ID = (int) $_GET['user_id']; 
		} 
			$is_enabled = get_usermeta( $user_ID, 'wp-to-twitter-enable-user' );
			$twitter_username = get_usermeta( $user_ID, 'wp-to-twitter-user-username' );
		?>
		<h3><?php _e('WP to Twitter User Settings', 'wp-to-twitter'); ?></h3>
		
		<table class="form-table">
		<tr>
			<th scope="row"><?php _e("Use My Twitter Username", 'wp-to-twitter'); ?></th>
			<td><input type="radio" name="wp-to-twitter-enable-user" id="wp-to-twitter-enable-user-3" value="mainAtTwitter"<?php if ($is_enabled == "mainAtTwitter") { echo " checked='checked'"; } ?> /> <label for="wp-to-twitter-enable-user-3"><?php _e("Tweet my posts with an @ reference to my username.", 'wp-to-twitter'); ?></label><br />
<input type="radio" name="wp-to-twitter-enable-user" id="wp-to-twitter-enable-user-4" value="mainAtTwitterPlus"<?php if ($is_enabled == "mainAtTwitterPlus") { echo " checked='checked'"; } ?> /> <label for="wp-to-twitter-enable-user-3"><?php _e("Tweet my posts with an @ reference to both my username and to the main site username.", 'wp-to-twitter'); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="wp-to-twitter-user-username"><?php _e("Your $twitter Username", 'wp-to-twitter'); ?></label></th>
			<td><input type="text" name="wp-to-twitter-user-username" id="wp-to-twitter-user-username" value="<?php echo attribute_escape( $twitter_username ); ?>" /> <?php _e('Enter your own Twitter username.', 'wp-to-twitter'); ?></td>
		</tr>
		</table>
<?php
}

function custom_shortcodes( $sentence, $post_ID ) {
	$pattern = '/\[\[.*\]\]/';
	$params = array(0=>"[[",1=>"]]");
	preg_match($pattern,$sentence, $matches);
	if ($matches) {
		foreach ($matches as $value) {
			$shortcode = "$value";
			$field = str_replace($params, "", $shortcode);
			$custom = get_post_meta( $post_ID, $field, TRUE );
			$sentence = str_replace( $shortcode, $custom, $sentence );
		}
	return $sentence;
	} else {
	return $sentence;
	}
}
	
function jd_twitter_save_profile(){
	global $user_ID;
	get_currentuserinfo();
	if ( isset($_POST['user_id']) ) { 
		$user_ID = (int) $_POST['user_id']; 
	} 
	update_usermeta($user_ID ,'wp-to-twitter-enable-user' , $_POST['wp-to-twitter-enable-user'] );
	update_usermeta($user_ID ,'wp-to-twitter-user-username' , $_POST['wp-to-twitter-user-username'] );
}
function jd_list_categories() {
	$selected = "";
	$categories = get_categories('hide_empty=0');
	$input = "<form action=\"\" method=\"post\">
	<fieldset><legend>".__('Check the categories you want to tweet:','wp-to-twitter')."</legend>
	<ul>\n";
	$tweet_categories =  get_option( 'tweet_categories' );
		foreach ($categories AS $cat) {
			if (is_array($tweet_categories)) {
				if (in_array($cat->term_id,$tweet_categories)) {
					$selected = " checked=\"checked\"";
				} else {
					$selected = "";
				}
			}
			$input .= '		<li><input'.$selected.' type="checkbox" name="categories[]" value="'.$cat->term_id.'" id="'.$cat->category_nicename.'" /> <label for="'.$cat->category_nicename.'">'.$cat->name."</label></li>\n";
		}
	$input .= "	</ul>
	</fieldset>
	<div>
	<input type=\"hidden\" name=\"submit-type\" value=\"setcategories\" />
	<input type=\"submit\" name=\"submit\" class=\"button-primary\" value=\"".__('Set Categories','wp-to-twitter')."\" />
	</div>
	</form>";
	echo $input;
}

// Add the administrative settings to the "Settings" menu.
function jd_addTwitterAdminPages() {
    if ( function_exists( 'add_submenu_page' ) ) {
		 $plugin_page = add_options_page( 'WP -> Twitter', 'WP -> Twitter', 'manage_options', __FILE__, 'jd_wp_Twitter_manage_page' );
		 add_action( 'admin_head-'. $plugin_page, 'jd_addTwitterAdminStyles' );
    }
 }
function jd_addTwitterAdminStyles() {
 $wp_to_twitter_directory = get_bloginfo( 'wpurl' ) . '/' . PLUGINDIR . '/' . dirname( plugin_basename(__FILE__) );
	echo "
<style type=\"text/css\">
<!--
#wp-to-twitter #message {
margin: 10px 0;
padding: 5px;
}
#wp-to-twitter .jd-settings {
clear: both;
}
#wp-to-twitter form .error p {
background: none;
border: none;
}
legend {
font-weight: 700;
font-size: 1.2em;
padding: 6px 0;
}
.resources {
float: right;
border: 1px solid #aaa;
padding: 10px 10px 0;
margin-left: 10px;
margin-bottom: 10px;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
border-radius: 5px;
background: #fff;
text-align: center;
}
#wp-to-twitter .resources form {
margin: 0;
}
.settings {
margin: 25px 0;
background: #fff;
padding: 10px;
border: 1px solid #000;
}
#wp-to-twitter .panel {
border: 1px solid #ddd;
background: #f6f6f6;
padding: 5px;
margin: 5px;
}
#wp-to-twitter ul {
list-style-type: disc;
margin-left: 3em;
font-size: 1em;
}
-->
</style>";
 }
// Include the Manager page
function jd_wp_Twitter_manage_page() {
	if ( file_exists ( dirname(__FILE__).'/wp-to-twitter-manager.php' )) {
    include( dirname(__FILE__).'/wp-to-twitter-manager.php' );
	} else {
	_e( '<p>Couldn\'t locate the settings page.</p>', 'wp-to-twitter' );
	}
}
function plugin_action($links, $file) {
	if ($file == plugin_basename(dirname(__FILE__).'/wp-to-twitter.php'))
		$links[] = "<a href='options-general.php?page=wp-to-twitter/wp-to-twitter.php'>" . __('Settings', 'wp-to-twitter', 'wp-to-twitter') . "</a>";
	return $links;
}
//Add Plugin Actions to WordPress

add_filter('plugin_action_links', 'plugin_action', -10, 2);

if ( get_option( 'jd_individual_twitter_users')=='1') {
	add_action( 'show_user_profile', 'jd_twitter_profile' );
	add_action( 'edit_user_profile', 'jd_twitter_profile' );
	add_action( 'profile_update', 'jd_twitter_save_profile');
}
if ( get_option( 'disable_url_failure' ) != '1' ) {
	if ( get_option( 'wp_url_failure' ) == '1' ) {
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>';_e('There\'s been an error shortening your URL! <a href=\"".get_bloginfo('wpurl')."/wp-admin/options-general.php?page=wp-to-twitter/wp-to-twitter.php\">Visit your WP to Twitter settings page</a> to get more information and to clear this error message.','wp-to-twitter'); echo '</p></div>';" ) );
	}
}
if ( get_option( 'disable_twitter_failure' ) != '1' ) {
	if ( get_option( 'wp_twitter_failure' ) == '1' ) {
		add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>';_e('There\'s been an error posting your Twitter status! <a href=\"".get_bloginfo('wpurl')."/wp-admin/options-general.php?page=wp-to-twitter/wp-to-twitter.php\">Visit your WP to Twitter settings page</a> to get more information and to clear this error message.','wp-to-twitter'); echo '</p></div>';" ) );
	}
}
if ( get_option( 'jd_twit_pages' )=='1' ) {
	add_action( 'publish_page', 'jd_twit_page' );
}
if ( get_option( 'jd_twit_edited_pages' )=='1' ) {
	add_action( 'publish_page', 'jd_twit_page' );
}
if ( get_option( 'jd_twit_blogroll' ) == '1' ) {
	add_action( 'add_link', 'jd_twit_link' );
}
add_action( 'future_to_publish', 'jd_twit_future' );
add_action( 'publish_post', 'jd_twit', 12 );

if ( get_option( 'jd_twit_quickpress' ) == '1' ) {
	add_action( 'new_to_publish', 'jd_twit_quickpress', 12 ); 
}

if ( get_option( 'jd_twit_remote' ) == '1' ) {
	add_action( 'xmlrpc_publish_post', 'jd_twit_xmlrpc' ); 
	add_action( 'publish_phone', 'jd_twit_xmlrpc' ); // to add later
}
add_action( 'save_post','post_jd_twitter' );
add_action( 'admin_menu', 'jd_addTwitterAdminPages' );

register_activation_hook( __FILE__, 'wptotwitter_activate' );
?>