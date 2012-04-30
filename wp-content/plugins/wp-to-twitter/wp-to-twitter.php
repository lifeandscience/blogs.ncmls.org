<?php
/*
Plugin Name: WP to Twitter
Plugin URI: http://www.joedolson.com/articles/wp-to-twitter/
Description: Posts a Twitter status update when you update your WordPress blog or post to your blogroll, using your chosen URL shortening service. Rich in features for customizing and promoting your Tweets.
Version: 2.3.14
Author: Joseph Dolson
Author URI: http://www.joedolson.com/
*/
/*  Copyright 2008-2012  Joseph C Dolson  (email : wp-to-twitter@joedolson.com)

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

if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
	$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
} else {
	$wp_content_url = get_option( 'siteurl' );
}
$wp_content_url .= '/wp-content';
$wp_content_dir = ABSPATH . 'wp-content';

if ( defined('WP_CONTENT_URL') ) {
	$wp_content_url = constant('WP_CONTENT_URL');
}
if ( defined('WP_CONTENT_DIR') ) {
	$wp_content_dir = constant('WP_CONTENT_DIR');
}

$wp_plugin_url = $wp_content_url . '/plugins';
$wp_plugin_dir = $wp_content_dir . '/plugins';
$wpmu_plugin_url = $wp_content_url . '/mu-plugins';
$wpmu_plugin_dir = $wp_content_dir . '/mu-plugins';

if ( version_compare( phpversion(), '5.0', '<' ) || !function_exists( 'curl_init' ) ) {
	$warning = __('WP to Twitter requires PHP version 5 or above with cURL support. Please upgrade PHP or install cURL to run WP to Twitter.','wp-to-twitter' );
	add_action('admin_notices', create_function( '', "echo \"<div class='error'><p>$warning</p></div>\";" ) );
	
} else {
	require_once( $wp_plugin_dir . '/wp-to-twitter/wp-to-twitter-oauth.php' );
}

// include service functions
require_once( $wp_plugin_dir . '/wp-to-twitter/functions.php' );

global $wp_version,$wpt_version,$jd_plugin_url,$jdwp_api_post_status;
$wpt_version = "2.3.14";
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'wp-to-twitter', false, dirname( plugin_basename( __FILE__ ) ) );

$jdwp_api_post_status = "https://api.twitter.com/1/statuses/update.json";

$jd_plugin_url = "http://www.joedolson.com/articles/wp-to-twitter/";
$jd_donate_url = "http://www.joedolson.com/donate.php";

function wpt_marginal_function() {
global $wp_version;
$exit_msg=__('WP to Twitter requires WordPress 2.9.2 or a more recent version, but some features will not work below 3.0.6. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update WordPress to continue using WP to Twitter with all features!</a>','wp-to-twitter');
	if ( version_compare( $wp_version,"3.0.6","<" ) ) {
		if ( is_admin() ) {
			echo "<div class='error'><p>".($exit_msg)."</p></div>";
		}
	}
}
 // check for OAuth configuration
if ( !function_exists('wtt_oauth_test') ) {
	$oauth = false;
} else {
	$oauth = wtt_oauth_test();
}
 
if ( !$oauth && get_option('disable_oauth_notice') != '1' ) {
	$message = sprintf(__("Twitter requires authentication by OAuth. You will need to <a href='%s'>update your settings</a> to complete installation of WP to Twitter.", 'wp-to-twitter'), admin_url('options-general.php?page=wp-to-twitter/wp-to-twitter.php'));
	add_action('admin_notices', create_function( "", "if ( ! current_user_can( 'manage_options' ) ) { return; } else { 
	echo \"<div class='error'><p>$message</p></div>\";}" ) );
}

function wpt_check_version() {
	global $wpt_version;
	$prev_version = get_option( 'wp_to_twitter_version' );
	if ( version_compare( $prev_version,$version,"<" ) ) {
		wptotwitter_activate();
	}
}

function wptotwitter_activate() {
global $wpt_version;
$prev_version = get_option( 'wp_to_twitter_version' );
// this is a switch to plan for future versions
$upgrade = version_compare( $prev_version,"2.2.9","<" );
	if ($upgrade) {
			delete_option( 'x-twitterlogin' );
			delete_option( 'twitterlogin' );
			delete_option( 'twitterpw' );
			delete_option( 'jd-use-link-title' );
			delete_option( 'jd-use-link-description' );
			delete_option( 'jd_use_both_services' );
			delete_option( 'jd-twitter-service-name' );
			delete_option( 'jd_api_post_status' );
			delete_option( 'jd-twitter-char-limit' );
			delete_option( 'x-twitterpw' );	
			delete_option( 'x_jd_api_post_status' );
			delete_option( 'cligsapi' );
			delete_option( 'cligslogin' );
			delete_option( 'wp_cligs_error' );
	}
$upgrade = version_compare( $prev_version, "2.3.1","<" );
	if ($upgrade) {
		$array = 
			array(
				'post'=> array(
						'post-published-update'=>get_option('newpost-published-update'),
						'post-published-text'=>get_option('newpost-published-text'),
						'post-edited-update'=>get_option('oldpost-edited-update'),
						'post-edited-text'=>get_option('oldpost-edited-text')
					),
				'page'=> array(
						'post-published-update'=>get_option('jd_twit_pages'),
						'post-published-text'=>get_option('newpage-published-text'),
						'post-edited-update'=>get_option('jd_twit_edited_pages'),
						'post-edited-text'=>get_option('oldpage-edited-text')				
					)
			);
		add_option( 'wpt_post_types', $array );
		add_option( 'comment-published-update', 0 );
		add_option( 'comment-published-text', 'New comment on #title# #url#' );
		delete_option('newpost-published-update');
		delete_option('newpost-published-text');
		delete_option('oldpost-edited-update');
		delete_option('oldpost-edited-text');
		delete_option('newpage-published-text');
		delete_option('oldpage-edited-text');
		delete_option( 'newpost-published-showlink' );
		delete_option( 'oldpost-edited-showlink' );
		delete_option( 'jd_twit_pages' );
		delete_option( 'jd_twit_edited_pages' );		
		delete_option( 'jd_twit_postie' );
	}
$upgrade = version_compare( $prev_version, "2.3.3","<" );
	if ( $upgrade ) {
		delete_option( 'jd_twit_quickpress' );
	}
$upgrade = version_compare( $prev_version, "2.3.4","<" );
	if ( $upgrade ) {
		add_option( 'wpt_inline_edits', '0' );
	}
	update_option( 'wp_to_twitter_version',$wpt_version );
}	
	
// Function checks for an alternate URL to be tweeted. Contribution by Bill Berry.	
function external_or_permalink( $post_ID ) {
       $wtb_extlink_custom_field = get_option('jd_twit_custom_url'); 
       $permalink = get_permalink( $post_ID );
			if ( $wtb_extlink_custom_field != '' ) {
				$ex_link = get_post_meta($post_ID, $wtb_extlink_custom_field, true);
			}
       return ( $ex_link ) ? $ex_link : $permalink;
}

// This function performs the API post to Twitter
function jd_doTwitterAPIPost( $twit ) {
	// prevent duplicate tweets
	$check = get_option('jd_last_tweet');
	if ( $check == $twit ) {
		return true;
	} else {
		global $jdwp_api_post_status;
		if ( $twit == '' ) {
		return FALSE;
		} else {
			if ( wtt_oauth_test() && ( $connection = wtt_oauth_connection() ) ) {
				$connection->post( $jdwp_api_post_status, array( 'status' => $twit, 'source' => 'wp-to-twitter'	) );
				$http_code = ($connection)?$connection->http_code:'failed';
				/*
				echo "<pre>";
				print_r($connection);
				echo "</pre>";
				*/
				switch ($http_code) {
					case '200':
						$return = true;
						$error = __("200 OK: Success!",'wp-to-twitter');
						break;
					case '400':
						$return = false;
						$error = __("400 Bad Request: The request was invalid. This is the status code returned during rate limiting.",'wp-to-twitter');
						break;
					case '401':
						$return = false;
						$error = __("401 Unauthorized: Authentication credentials were missing or incorrect.",'wp-to-twitter');
						break;
					case '403':
						$return = false;
						$error = __("403 Forbidden: The request is understood, but it has been refused. This code is used when requests are understood, but are denied by Twitter. Reasons can include: Too many tweets created in a short time or the same tweet text was submitted twice in a row, among others. This is not an error by WP to Twitter.",'wp-to-twitter');
						break;
					case '500':
						$return = false;
						$error = __("500 Internal Server Error: Something is broken at Twitter.",'wp-to-twitter');
						break;
					case '503':
						$return = false;
						$error = __("503 Service Unavailable: The Twitter servers are up, but overloaded with requests - Please try again later.",'wp-to-twitter');
						break;
					case '502':
						$return = false;
						$error = __("502 Bad Gateway: Twitter is down or being upgraded.",'wp-to-twitter');
						break;
					default:
						$return = false;
						$error = __("<strong>Code $http_code</strong>: Twitter did not return a recognized response code.",'wp-to-twitter');
						break;
				}
				update_option( 'jd_last_tweet',$twit );
				update_option( 'jd_status_message',$error );
				return $return;			
			}	
		}
	}
}

function fake_normalize( $string ) {
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) && function_exists('normalizer_normalize') ) {
		return normalizer_normalize( $string );
	} else {
		return preg_replace( '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
	}
}


function jd_truncate_tweet( $sentence, $thisposttitle, $thisblogtitle, $thispostexcerpt, $thisposturl, $thispostcategory, $thisdate, $post_ID, $authID=FALSE ) {
	$sentence = trim($sentence);
	// generate all template variable values
	$thisposttitle = trim($thisposttitle);
	$thisblogtitle = trim($thisblogtitle);
	$thispostexcerpt = trim($thispostexcerpt);
	$thisposturl = trim($thisposturl);
	$thispostcategory = trim($thispostcategory);
		$post = get_post( $post_ID );
		$post_author = $post->post_author;
	$thisauthor = get_the_author_meta( 'display_name',$post_author );

	if ( get_option( 'jd_individual_twitter_users' ) == 1 ) {
		if ( get_user_meta( $authID, 'wp-to-twitter-enable-user',true ) == 'mainAtTwitter' ) {
			$thisaccount = "@" . stripcslashes(get_user_meta( $authID, 'wp-to-twitter-user-username',true ));
		} else if ( get_user_meta( $authID, 'wp-to-twitter-enable-user',true ) == 'mainAtTwitterPlus' ) {
			$thisaccount = "@" . stripcslashes(get_user_meta( $authID, 'wp-to-twitter-user-username',true ) . ' @' . get_option( 'wtt_twitter_username' ));
		}
	} else {
		$thisaccount = "@".get_option('wtt_twitter_username');
	}
	if ( get_option( 'use_tags_as_hashtags' ) == '1' && $sentence != '' ) {
		$sentence = $sentence . " " . generate_hash_tags( $post_ID );
	}	
	if ( get_option( 'jd_twit_prepend' ) != "" && $sentence != '' ) {
		$sentence = get_option( 'jd_twit_prepend' ) . " " . $sentence;
	}
	if ( get_option( 'jd_twit_append' ) != "" && $sentence != '' ) {
		$sentence = $sentence . " " . get_option( 'jd_twit_append' );
	}
	// create full unconditional post sentence - prior to truncation
	$post_sentence = str_ireplace( '#account#', $thisaccount, $sentence );
	$post_sentence = str_ireplace( '#url#', $thisposturl, $post_sentence );
	$post_sentence = str_ireplace( '#title#', $thisposttitle, $post_sentence );
	$post_sentence = str_ireplace( '#blog#',$thisblogtitle, $post_sentence );
	$post_sentence = str_ireplace( '#post#',$thispostexcerpt, $post_sentence );
	$post_sentence = str_ireplace( '#category#',$thispostcategory, $post_sentence );
	$post_sentence = str_ireplace( '#date#', $thisdate, $post_sentence );
	$post_sentence = str_ireplace( '#author#', $thisauthor, $post_sentence );

	// check total length 
	$str_length = mb_strlen( urldecode( fake_normalize( $post_sentence ) ) );
	// what is the excerpt supposed to be?
	$length = get_option( 'jd_post_excerpt' );

	// build an array of variable names and the number of characters in that variable.
	$length_array = array();
	$length_array['thispostexcerpt'] = mb_strlen(fake_normalize($thispostexcerpt));
	$length_array['thisblogtitle'] = mb_strlen(fake_normalize($thisblogtitle));
	$length_array['thisposttitle'] = mb_strlen(fake_normalize($thisposttitle));
	$length_array['thispostcategory'] = mb_strlen(fake_normalize($thispostcategory));
	$length_array['thisdate'] = mb_strlen(fake_normalize($thisdate));
	$length_array['thisauthor'] = mb_strlen(fake_normalize($thisauthor));
	$length_array['thisaccount'] = mb_strlen(fake_normalize($thisaccount));

	// if the total length is too long, truncate items until the length is appropriate. 
	// truncation is in order of items which can most afford to be truncated. URL is never truncated.
	if ( $str_length > 140 ) {
		foreach($length_array AS $key=>$value) {
			$str_length = mb_strlen( urldecode( fake_normalize( trim( $post_sentence ) ) ) );
			if ( $str_length > 140 ) {
				$trim = $str_length - 140;
				$old_value = ${$key};
				$new_value = mb_substr( $old_value,0,-( $trim ) );
				$post_sentence = str_ireplace( $old_value,$new_value,$post_sentence );
			} 
		}
	}
	if ( mb_strlen( fake_normalize ( $post_sentence ) ) > 140 ) { $post_sentence = substr( $post_sentence,0,139 ); }
	$sentence = $post_sentence;
	return $sentence;
}

function jd_shorten_link( $thispostlink, $thisposttitle, $post_ID, $testmode='false' ) {
		$suprapi =  trim ( get_option( 'suprapi' ) );
		$suprlogin = trim ( get_option( 'suprlogin' ) );
		$bitlyapi =  trim ( get_option( 'bitlyapi' ) );
		$bitlylogin =  trim ( strtolower( get_option( 'bitlylogin' ) ) );
		$yourlslogin =  trim ( get_option( 'yourlslogin') );
		$yourlsapi = stripcslashes( get_option( 'yourlsapi' ) );
		if ($testmode == 'false') {
			if ( get_option('use-twitter-analytics') == 1 || get_option('use_dynamic_analytics') == 1 ) {
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
				$this_campaign = urlencode($this_campaign);
				if ( strpos( $thispostlink,"%3F" ) === FALSE || strpos( $thispostlink,"?" ) === FALSE ) {
				$thispostlink .= "?";
				} else {
				$thispostlink .= "&";
				}
				$thispostlink .= "utm_campaign=$this_campaign&utm_medium=twitter&utm_source=twitter";
			}
		}
		$thispostlink = urldecode(trim($thispostlink));
		$thispostlink = urlencode($thispostlink);

		// custom word setting
		$keyword_format = ( get_option( 'jd_keyword_format' ) == '1' )?$post_ID:'';
		$keyword_format = ( get_option( 'jd_keyword_format' ) == '2' )?get_post_meta( $post_ID,'_yourls_keyword',true ):$keyword_format;
		// Generate and grab the short url
		switch ( get_option( 'jd_shortener' ) ) {
			case 0:
			case 1:
			$shrink = urldecode($thispostlink);
			break;
			case 2: // updated to v3 3/31/2010
			$decoded = jd_remote_json( "http://api.bit.ly/v3/shorten?longUrl=".$thispostlink."&login=".$bitlylogin."&apiKey=".$bitlyapi."&format=json" );
			$error = '';
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
				if ( $testmode == 'link' ) {
					$shrink = urldecode($thispostlink);
				} else {
					if ( function_exists( 'wp_get_shortlink' ) ) {
						$shrink = wp_get_shortlink();
						if ( !$shrink ) { $shrink = home_url( '?p=' . $post_ID ); }
					} else {
						$shrink = home_url( '?p=' . $post_ID );
					}
				}
			break;
			case 5:
			// local YOURLS installation
			$thispostlink = urldecode($thispostlink);
			global $yourls_reserved_URL;
			define('YOURLS_INSTALLING', true); // Pretend we're installing YOURLS to bypass test for install or upgrade
			define('YOURLS_FLOOD_DELAY_SECONDS', 0); // Disable flood check
			$opath = get_option( 'yourlspath' );
			$ypath = str_replace( 'user','includes', $opath );
			if ( file_exists( dirname( $ypath ).'/load-yourls.php' ) ) { // YOURLS 1.4+
				global $ydb;
				require_once( dirname( $ypath ).'/load-yourls.php' );
				if ( function_exists( 'yourls_add_new_link' ) ) {
					$yourls_result = yourls_add_new_link( $thispostlink, $keyword_format );
				} else {
					$yourls_result = $thispostlink;
				}
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
			case 7:
				if ( $suprapi != '') {
					$decoded = jd_remote_json( "http://su.pr/api/shorten?longUrl=".$thispostlink."&login=".$suprlogin."&apiKey=".$suprapi );
				} else {
					$decoded = jd_remote_json( "http://su.pr/api/shorten?longUrl=".$thispostlink );
				}
				update_option( 'wp_supr_error',"Su.pr API result: $decoded" );
				if ($decoded['statusCode'] == 'OK') {
					$page = urldecode($thispostlink);
					$shrink = $decoded['results'][$page]['shortUrl'];
					$error = $decode['errorMessage'];
				} else {
					$shrink = false;
					$error = $decode['errorMessage'];
					update_option( 'wp_supr_error',"JSON result could not be decoded");
				}	
				if ( !is_valid_url($shrink) ) { $shrink = false; update_option( 'wp_supr_error',$error ); }
			break;
			case 8:
			// Goo.gl
				$url = "https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyBSnqQOg3vX1gwR7y2l-40yEG9SZiaYPUQ";
				$link = urldecode($thispostlink);
				$body = "{'longUrl':'$link'}";
				//$body = json_encode($data);
				$json = jd_fetch_url( $url, 'POST', $body, 'Content-Type: application/json' );
				$decoded = json_decode($json);
				//$url = $decoded['id'];
				$shrink = $decoded->id;
				if ( !is_valid_url($shrink) ) { $shrink = false; }
			break;
		}
		if ($testmode != 'true') {
			if ( $shrink === false || ( stristr( $shrink, "http://" ) === FALSE )) {
				update_option( 'wp_url_failure','1' );
				$shrink = urldecode( $thispostlink );
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
		if ( is_array( $categories ) ) {
			if ( count($categories) > 0 ) {
				$category = $categories[0]->cat_name;
			}		
			foreach ($categories AS $cat) {
				$category_ids[] = $cat->term_id;
			}
		} else {
			$category = '';
			$category_ids = array();
		}
	$values['categoryIds'] = $category_ids;
	$values['category'] = $category;
		$excerpt_length = get_option( 'jd_post_excerpt' );
	$values['postExcerpt'] = ( trim( $get_post_info->post_excerpt ) == "" )?@mb_substr( strip_shortcodes( strip_tags($get_post_info->post_content) ), 0, $excerpt_length ):@mb_substr( strip_shortcodes( strip_tags($get_post_info->post_excerpt) ), 0, $excerpt_length );
	$thisposttitle =  stripcslashes( strip_tags( $get_post_info->post_title ) );
		if ($thisposttitle == "") {
			$thisposttitle =  stripcslashes( strip_tags( $_POST['title'] ) );
		}
	$values['postTitle'] = $thisposttitle;
	$values['postLink'] = external_or_permalink( $post_ID );
	$values['blogTitle'] = get_bloginfo( 'name' );
	$values['shortUrl'] = get_post_meta( $post_ID, '_wp_jd_clig', TRUE );
	$values['postStatus'] = $get_post_info->post_status;
	$values['postType'] = $get_post_info->post_type;
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
	wpt_check_version();
	$jd_tweet_this = get_post_meta( $post_ID, '_jd_tweet_this', true );
	$newpost = false;
	$oldpost = false;
	$is_inline_edit = false;
	if ( get_option('wpt_inline_edits') != 1 ) {
		if ( isset($_POST['_inline_edit']) ) { return; }
	} else {
		if ( isset($_POST['_inline_edit']) ) { $is_inline_edit = true; }
	}
	if ( $jd_tweet_this != "no" ) {
		$jd_post_info = jd_post_info( $post_ID );
		$post_type = $jd_post_info['postType'];
		$post_type_settings = get_option('wpt_post_types');
		$post_types = array_keys($post_type_settings);
		if ( in_array( $post_type, $post_types ) ) {
			$sentence = '';
			$cT = get_post_meta( $post_ID, '_jd_twitter', true );
			$customTweet = ( $cT != '' )?stripcslashes( trim( $cT ) ):'';
			// excluded post statuses that should never be tweeted
			if ( $jd_post_info['postStatus'] != 'draft' && $jd_post_info['postStatus'] != 'auto-draft' && $jd_post_info['postStatus'] != 'private' && $jd_post_info['postStatus'] != 'inherit' && $jd_post_info['postStatus'] != 'trash' ) {
				// && $jd_post_info['postStatus'] != 'pending'
				// if ops is set and equals 'publish', this is being edited. Otherwise, it's a new post.
				if ( ( isset($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' ) || $is_inline_edit == true ) {
					// if this is an old post and editing updates are enabled
					if ( $post_type_settings[$post_type]['post-edited-update'] == '1' ) {
						$nptext = stripcslashes( $post_type_settings[$post_type]['post-edited-text'] );
						$oldpost = true;
					}
				} else {
					if ( $post_type_settings[$post_type]['post-published-update'] == '1' ) {
						$nptext = stripcslashes( $post_type_settings[$post_type]['post-published-text'] );	
						$newpost = true;
					}
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
					$sendToTwitter = jd_doTwitterAPIPost( $sentence );
					$jwt = get_post_meta( $post_ID, '_jd_wp_twitter', true );
					if ( !is_array( $jwt ) ){ $jwt=array(); }
					$jwt[] = urldecode( $sentence );
					$_POST['_jd_wp_twitter'] = $jwt;
					update_post_meta( $post_ID,'_jd_wp_twitter', $jwt );
					if ( $sendToTwitter == false ) {
						update_option( 'wp_twitter_failure','1' );
					}
				}
			}
		} else {
			return $post_ID;
		}
	}
	return $post_ID;
}

// Add Tweets on links in Blogroll
function jd_twit_link( $link_ID )  {
	wpt_check_version();
	global $wpt_version;
	$thislinkprivate = $_POST['link_visible'];
	if ($thislinkprivate != 'N') {
		$thislinkname = stripcslashes( $_POST['link_name'] );
		$thispostlink =  $_POST['link_url'] ;
		$thislinkdescription =  stripcslashes( $_POST['link_description'] );
		$sentence = stripcslashes( get_option( 'newlink-published-text' ) );
		$sentence = str_ireplace("#title#",$thislinkname,$sentence);
		$sentence = str_ireplace("#description#",$thislinkdescription,$sentence);		 

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
				$sendToTwitter = jd_doTwitterAPIPost( $sentence );
			if ( $sendToTwitter == false ) {
				update_option('wp_twitter_failure','2');
				}
			}
		return $link_ID;
	} else {
	return '';
	}
}
// HANDLES xmlrpc POSTS
function jd_twit_xmlrpc( $post_ID ) {
	wpt_check_version();
	$jd_post_info = jd_post_info( $post_ID );	
	$post_type = $jd_post_info['postType'];
	$post_type_settings = get_option('wpt_post_types');
	$post_types = array_keys($post_type_settings);

	if ( in_array( $post_type, $post_types ) ) {		
		$sentence = '';	
		if ( get_option('jd_tweet_default') != '1' && get_option('jd_twit_remote') == '1' ) {
			$poststatus = $jd_post_info['postStatus'];
			if ( $poststatus == 'publish' ) {
				$sentence = stripcslashes( $post_type_settings[$post_type]['post-published-text'] );
			} else {
				$sentence = stripcslashes( $post_type_settings[$post_type]['post-edited-text'] );			
			}
			$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
			// Stores the posts CLIG in a custom field for later use as needed.
			store_url($post_ID, $shrink);				
			// Check the length of the tweet and truncate parts as necessary.
			$sentence = custom_shortcodes( $sentence, $post_ID );			
			$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );	
			if ( $sentence != '' ) {	
				if ( get_option('limit_categories') == '0' || in_allowed_category( $jd_post_info['categoryIds'] ) ) {
					$sendToTwitter = jd_doTwitterAPIPost( $sentence );
					$jwt = get_post_meta( $post_ID, '_jd_wp_twitter', true );
					if ( !is_array( $jwt ) ){ $jwt=array(); }					
					$jwt[] = urldecode( $sentence );
					update_post_meta( $post_ID,'_jd_wp_twitter', $jwt );					
					if ($sendToTwitter == false ) {
						update_option('wp_twitter_failure','1');
					}
				}				
			}
		}
	return $post_ID;
	}
} // END jd_twit_xmlrpc

// Add comment tweet function from Luis Nobrega
function jd_twit_comment( $comment_id, $approved ) {	
	$_t = get_comment( $comment_id );
	$post_ID = $_t->comment_post_ID;
	$commenter = $_t->comment_author;
	$jd_tweet_this = get_post_meta( $post_ID, '_jd_tweet_this', TRUE);
	if ( $jd_tweet_this != 'no' && $_t->comment_approved == 1 ) { // comments only tweeted on posts which are tweeted
		$jd_post_info = jd_post_info( $post_ID );
		$sentence = '';
		$sentence = stripcslashes( get_option( 'comment-published-text' ) );
		if ( $jd_post_info['shortUrl'] != '' ) {
			$shrink = $jd_post_info['shortUrl'];
		} else {
			$shrink = jd_shorten_link( $jd_post_info['postLink'], $jd_post_info['postTitle'], $post_ID );
			store_url( $post_ID, $shrink );
		}		
		$sentence = jd_truncate_tweet( $sentence, $jd_post_info['postTitle'], $jd_post_info['blogTitle'], $jd_post_info['postExcerpt'], $shrink, $jd_post_info['category'], $jd_post_info['postDate'], $post_ID, $jd_post_info['authId'] );		
		$sentence = str_replace("#commenter#",$commenter,$sentence);
		if ( $sentence != '' ) {
			$sendToTwitter = jd_doTwitterAPIPost( $sentence );
		}
	}
	return $post_ID;
}

add_action('admin_menu','jd_add_twitter_outer_box');

function store_url($post_ID, $url) {
	$shortener = get_option( 'jd_shortener' );
	switch ($shortener) {
		case 0:	case 1:	$ext = '_wp';break;
		case 2:	$ext = '_bitly';break;
		case 3:	$ext = '_url';break;
		case 4:	$ext = '_wp';break;
		case 5:	case 6:	$ext = '_yourls';break;
		case 7:	$ext = '_supr';	break;
		case 8:	$ext = '_goo';	break;
		default:$ext = '_ind';
	}
	if ( get_post_meta ( $post_ID, "_wp_jd$ext", TRUE ) != $url ) {
		update_post_meta ( $post_ID, "_wp_jd$ext", $url );
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
	$hashtags = '';
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
			$strip = get_option( 'jd_strip_nonan' );
			$search = "/[^a-zA-Z0-9]/";
			if ($replace == "[ ]") { $replace = ""; }
			$tag = str_ireplace( " ",$replace,trim( $tag ) );
			if ($strip == '1') { $tag = preg_replace( $search, $replace, $tag ); }
			if ($replace == "" || !$replace) { $replace = "_"; }
				$newtag = "#$tag";
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
	$post_length = 140;

global $post, $jd_plugin_url, $jd_donate_url;
	$post_id = $post;
	if (is_object($post_id)) {
		$post_id = $post_id->ID;
	}
	if ( get_post_meta ( $post_id, "_jd_post_meta_fixed", true ) != 'true' ) {
		jd_fix_post_meta( $post_id );
	}
	$jd_twitter = esc_attr( stripcslashes( get_post_meta($post_id, '_jd_twitter', true ) ) );
	$jd_tweet_this = get_post_meta( $post_id, '_jd_tweet_this', true );
	if ( $jd_tweet_this == '' ) { $jd_tweet_this = ( get_option( 'jd_tweet_default' ) == '1' )?'no':'yes'; }
	$jd_short = get_post_meta( $post_id, '_wp_jd_clig', true );
	$shortener = "Cli.gs";
	if ( $jd_short == "" ) {
		$jd_short = get_post_meta( $post_id, '_wp_jd_supr', true );
		$shortener = "Su.pr";
	}
	if ( $jd_short == "" ) {
		$jd_short = get_post_meta( $post_id, '_wp_jd_ind', true );
		$shortener = "other";
	}		
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
	$previous_tweets = get_post_meta ( $post_id, '_jd_wp_twitter', true );
	?>
<script type="text/javascript">
<!-- Begin
function countChars(field,cntfield) {
cntfield.value = field.value.length;
}
//  End -->
</script>
<?php if ( !is_array( $previous_tweets ) && $previous_tweets != '' ) { $previous_tweets = array( 0=>$previous_tweets ); } ?>
<?php if ( ! empty( $previous_tweets ) ) { ?>

<p class='error'><strong><?php _e('Previous Tweets','wp-to-twitter'); ?>:</strong></p>
<ul>
<?php
	foreach ( $previous_tweets as $previous_tweet ) {
		if ( $previous_tweet != '' ) {
			$hidden_fields .= "<input type='hidden' name='_jd_wp_twitter[]' value='".esc_attr($previous_tweet)."' />";
			echo "<li>$previous_tweet <a href='http://twitter.com/?status=$previous_tweet'>Retweet this</a></li>";
		}
	}
?>
</ul>
<?php echo "<div>".$hidden_fields."</div>"; } ?>
<p>
<label for="jtw"><?php _e("Custom Twitter Post", 'wp-to-twitter', 'wp-to-twitter') ?></label><br /><textarea class="attachmentlinks" name="_jd_twitter" id="jtw" rows="2" cols="60" onKeyDown="countChars(document.post.jtw,document.post.twitlength)" onKeyUp="countChars(document.post.jtw,document.post.twitlength)"><?php echo esc_attr( $jd_twitter ); ?></textarea>
</p>
<p><input readonly type="text" name="twitlength" size="3" maxlength="3" value="<?php echo esc_attr( mb_strlen( $description) ); ?>" />
<?php $minus_length = $post_length - 21; ?>
<?php _e(" characters.<br />Twitter posts are a maximum of $post_length characters; if your short URL is included in your update, you have about $minus_length characters available. You can use <code>#url#</code>, <code>#title#</code>, <code>#post#</code>, <code>#category#</code>, <code>#date#</code>, <code>#author#</code>, <code>#account#</code> or <code>#blog#</code> to insert the shortened URL, post title, the first category selected, the post date, the post author, the twitter @reference, or a post excerpt or blog name into the Tweet.", 'wp-to-twitter', 'wp-to-twitter') ?> 
</p>
<?php 
	if ( get_option('jd_keyword_format') == 2 ) {
		$custom_keyword = get_post_meta( $post_id, '_yourls_keyword', true );
		echo "<label for='yourls_keyword'>".__('YOURLS Custom Keyword','wp-to-twitter')."</label> <input type='text' name='_yourls_keyword' id='yourls_keyword' value='$custom_keyword' />";
	}
?>
<p>
<a target="__blank" href="<?php echo admin_url('options-general.php?page=wp-to-twitter/wp-to-twitter.php'); ?>#get-support"><?php _e('Get Support', 'wp-to-twitter', 'wp-to-twitter') ?></a> &bull; <a target="__blank" href="<?php echo $jd_donate_url; ?>"><?php _e('Make a Donation', 'wp-to-twitter', 'wp-to-twitter') ?></a> &raquo;
</p>
<?php
	// "no" means 'Don't Tweet' (is checked)
	$nochecked = ( $jd_tweet_this == 'no' )?' checked="checked"':'';
	$yeschecked = ( $jd_tweet_this == 'yes' )?' checked="checked"':'';
	
?>
<p>
<input type="radio" name="_jd_tweet_this" value="no" id="jtn"<?php echo $nochecked; ?> /> <label for="jtn"><?php _e("Don't Tweet this post.", 'wp-to-twitter'); ?></label> <input type="radio" name="_jd_tweet_this" value="yes" id="jty"<?php echo $yeschecked; ?> /> <label for="jty"><?php _e("Tweet this post.", 'wp-to-twitter'); ?></label>
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
	wpt_check_version();
	$wpt_post_types = get_option('wpt_post_types');
	if ( function_exists( 'add_meta_box' )) {
		if ( is_array( $wpt_post_types ) ) {
			foreach ($wpt_post_types as $key=>$value) {
				add_meta_box( 'wptotwitter_div','WP to Twitter', 'jd_add_twitter_inner_box', $key, 'advanced' );
			}
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
	if ( $post_id != 0 ) {
	add_post_meta( $post_id, "_jd_post_meta_fixed",'true' );
	}
}

// Post the Custom Tweet into the post meta table
function post_jd_twitter( $id ) {
	if ( isset($_POST['_inline_edit']) ) { return; }

	// update meta data to new format
	if ( get_post_meta ( $id, "_jd_post_meta_fixed", true ) != 'true' ) {
		jd_fix_post_meta( $id );
	}
		if ( isset( $_POST['_yourls_keyword'] ) ) {
			$yourls = $_POST[ '_yourls_keyword' ];
			update_post_meta( $id, '_yourls_keyword', $yourls );			
		}
		if ( isset( $_POST[ '_jd_twitter' ] ) ) {
			$jd_twitter = $_POST[ '_jd_twitter' ];
			update_post_meta( $id, '_jd_twitter', $jd_twitter );
		}
		if ( isset( $_POST[ '_jd_wp_twitter' ] ) ) {
			$jd_wp_twitter = $_POST[ '_jd_wp_twitter' ];
			update_post_meta( $id, '_jd_wp_twitter', $jd_wp_twitter );
		}
		if ( isset($_POST[ '_jd_tweet_this' ]) ) {
			$jd_tweet_this = ( $_POST[ '_jd_tweet_this' ] == 'no')?'no':'yes';
			update_post_meta( $id, '_jd_tweet_this', $jd_tweet_this );
		} else {
			if ( isset($_POST['_wpnonce'] ) ) {
				$jd_tweet_default = ( get_option( 'jd_tweet_default' ) == 1 )?'no':'yes';
				update_post_meta( $id, '_jd_tweet_this', $jd_tweet_default );
			}
		}
}


function jd_twitter_profile() {
	global $user_ID;
	get_currentuserinfo();
	if ( current_user_can( get_option('wtt_user_permissions') ) ) {
		if ( isset($_GET['user_id']) ) { 
			$user_edit = (int) $_GET['user_id']; 
		} else {
			$user_edit = $user_ID;
		}
			$is_enabled = get_user_meta( $user_edit, 'wp-to-twitter-enable-user',true );
			$twitter_username = get_user_meta( $user_edit, 'wp-to-twitter-user-username',true );
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
			<th scope="row"><label for="wp-to-twitter-user-username"><?php _e("Your Twitter Username", 'wp-to-twitter'); ?></label></th>
			<td><input type="text" name="wp-to-twitter-user-username" id="wp-to-twitter-user-username" value="<?php echo esc_attr( $twitter_username ); ?>" /> <?php _e('Enter your own Twitter username.', 'wp-to-twitter'); ?></td>
		</tr>
		</table>
<?php
	}
}

function custom_shortcodes( $sentence, $post_ID ) {
	$pattern = '/([([\[\]?)([A-Za-z0-9-_])*(\]\]]?)+/';
	$params = array(0=>"[[",1=>"]]");
	preg_match_all($pattern,$sentence, $matches);
	if ($matches && is_array($matches[0])) {
		foreach ($matches[0] as $value) {
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
		$edit_id = (int) $_POST['user_id']; 
	} else {
		$edit_id = $user_ID;
	}
	update_user_meta($edit_id ,'wp-to-twitter-enable-user' , $_POST['wp-to-twitter-enable-user'] );
	update_user_meta($edit_id ,'wp-to-twitter-user-username' , $_POST['wp-to-twitter-user-username'] );
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
		 $plugin_page = add_options_page( 'WP to Twitter', 'WP to Twitter', 'manage_options', __FILE__, 'jd_wp_Twitter_manage_page' );
		 add_action( 'admin_head-'. $plugin_page, 'jd_addTwitterAdminStyles' );
    }
 }
function jd_addTwitterAdminStyles() {
global $wp_plugin_url, $wp_plugin_dir;
	if ( $_GET['page'] == "wp-to-twitter/wp-to-twitter.php" ) {
		echo '<link type="text/css" rel="stylesheet" href="'.$wp_plugin_url.'/wp-to-twitter/styles.css" />';
	}
 }
// Include the Manager page
function jd_wp_Twitter_manage_page() {
	if ( file_exists ( dirname(__FILE__).'/wp-to-twitter-manager.php' )) {
    include( dirname(__FILE__).'/wp-to-twitter-manager.php' );
	} else {
	_e( '<p>Couldn\'t locate the settings page.</p>', 'wp-to-twitter' );
	}
}
function jd_plugin_action($links, $file) {
	if ($file == plugin_basename(dirname(__FILE__).'/wp-to-twitter.php'))
		$links[] = "<a href='options-general.php?page=wp-to-twitter/wp-to-twitter.php'>" . __('Settings', 'wp-to-twitter', 'wp-to-twitter') . "</a>";
	return $links;
}
//Add Plugin Actions to WordPress

add_filter('plugin_action_links', 'jd_plugin_action', -10, 2);

if ( get_option( 'jd_individual_twitter_users')=='1') {
	add_action( 'show_user_profile', 'jd_twitter_profile' );
	add_action( 'edit_user_profile', 'jd_twitter_profile' );
	add_action( 'profile_update', 'jd_twitter_save_profile');
}

if ( get_option( 'disable_url_failure' ) != '1' ) {
	if ( get_option( 'wp_url_failure' ) == '1' ) {
		add_action('admin_notices', create_function( '', "if ( ! current_user_can( 'manage_options' ) ) { return; } echo '<div class=\"error\"><p>';_e('There\'s been an error shortening your URL! <a href=\"".get_bloginfo('wpurl')."/wp-admin/options-general.php?page=wp-to-twitter/wp-to-twitter.php\">Visit your WP to Twitter settings page</a> to get more information and to clear this error message.','wp-to-twitter'); echo '</p></div>';" ) );
	}
}
if ( get_option( 'disable_twitter_failure' ) != '1' ) {
	if ( get_option( 'wp_twitter_failure' ) == '1' ) {
		add_action('admin_notices', create_function( '', "if ( ! current_user_can( 'manage_options' ) ) { return; } echo '<div class=\"error\"><p>';_e('There\'s been an error posting your Twitter status! <a href=\"".get_bloginfo('wpurl')."/wp-admin/options-general.php?page=wp-to-twitter/wp-to-twitter.php\">Visit your WP to Twitter settings page</a> to get more information and to clear this error message.','wp-to-twitter'); echo '</p></div>';" ) );
	}
}

add_action( 'in_plugin_update_message-wp-to-twitter/wp-to-twitter.php', 'wpt_plugin_update_message' );
function wpt_plugin_update_message() {
	global $wpt_version;
	define('PLUGIN_README_URL',  'http://svn.wp-plugins.org/wp-to-twitter/trunk/readme.txt');
	$response = wp_remote_get( PLUGIN_README_URL, array ('user-agent' => 'WordPress/WP to Twitter' . $wpt_version . '; ' . get_bloginfo( 'url' ) ) );
	if ( ! is_wp_error( $response ) || is_array( $response ) ) {
		$data = $response['body'];
		$bits=explode('== Upgrade Notice ==',$data);
		echo '<div id="mc-upgrade"><p><strong style="color:#c22;">Upgrade Notes:</strong> '.nl2br(trim($bits[1])).'</p></div>';
	} else {
		printf(__('<br /><strong>Note:</strong> Please review the <a class="thickbox" href="%1$s">changelog</a> before upgrading.','wp-to-twitter'),'plugin-install.php?tab=plugin-information&amp;plugin=wp-to-twitter&amp;TB_iframe=true&amp;width=640&amp;height=594');
	}
}

if ( get_option( 'jd_twit_blogroll' ) == '1' ) {
	add_action( 'add_link', 'jd_twit_link' );
}
	$post_type_settings = get_option('wpt_post_types');
	if ( is_array( $post_type_settings ) ) {
		$post_types = array_keys($post_type_settings);
		foreach ($post_types as $value ) {
			add_action( 'publish_'.$value, 'jd_twit', 16 );		
		}
	}

if ( get_option( 'jd_twit_remote' ) == '1' ) {
	add_action( 'xmlrpc_publish_post', 'jd_twit_xmlrpc' ); 
	add_action( 'publish_phone', 'jd_twit_xmlrpc' ); // to add later
}
if ( get_option('comment-published-update') == 1 ) {
	add_action( 'comment_post', 'jd_twit_comment', 10, 2 );
}
add_action( 'save_post','post_jd_twitter', 10 );
add_action( 'admin_menu', 'jd_addTwitterAdminPages' );