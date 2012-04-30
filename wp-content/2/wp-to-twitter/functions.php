<?php 
// This file contains secondary functions supporting WP to Twitter
// These functions don't perform any WP to Twitter actions, but are sometimes called for when 
// support for primary functions is lacking.

if ( version_compare( $wp_version,"2.9.3",">" )) {
if (!class_exists('WP_Http')) {
	require_once( ABSPATH.WPINC.'/class-http.php' );
	}
}
	
function jd_remote_json( $url, $array=true ) {
	$input = jd_fetch_url( $url );
	$obj = json_decode($input, $array );
	return $obj;
	// TODO: some error handling ?
}			

function is_valid_url( $url ) {
    if (is_string($url)) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);	
	} else {
	return false;
	}
}
// Fetch a remote page. Input url, return content
function jd_fetch_url( $url, $method='GET', $body='', $headers='', $return='body' ) {
	$request = new WP_Http;
	$result = $request->request( $url , array( 'method'=>$method, 'body'=>$body, 'headers'=>$headers, 'user-agent'=>'WP to Twitter http://www.joedolson.com/articles/wp-to-twitter/' ) );
	// Success?
	if ( !is_wp_error($result) && isset($result['body']) ) {
		if ( $result['code'] != 200 ) {
			if ($return == 'body') {
			return $result['body'];
			} else {
			return $result;
			}
		} else {
			return $result['code'];
		}
	// Failure (server problem...)
	} else {
		return false;
	}
}

if ( !class_exists('SERVICES_JSON') ) {
	if ( version_compare( $wp_version,"2.9","<" )) {
	require_once( WP_PLUGIN_DIR.'/wp-to-twitter/json.class.php' );
	} else {
	require_once( ABSPATH.WPINC.'/class-json.php' );
	}
}
if (!function_exists('json_encode')) {
	function json_encode($data) {
		$json = new Services_JSON();
		return( $json->encode($data) );
	}
}
if (!function_exists('json_decode')) {
	function json_decode($data) {
		$json = new Services_JSON( SERVICES_JSON_LOOSE_TYPE );
		return( $json->decode($data) );
	}
}
if (!function_exists('mb_strlen')) {
	function mb_strlen($data) {
		return strlen($data);
	}
}

if (!function_exists('mb_substr')) {
	function mb_substr($data,$start,$length = null, $encoding = null) {
		return substr($data,$start,$length);
	}
}

// str_ireplace substitution for PHP4
if ( !function_exists( 'str_ireplace' ) ) {
	function str_ireplace( $needle, $str, $haystack ) {
		$needle = preg_quote( $needle, '/' );
		return preg_replace( "/$needle/i", $str, $haystack );
	}
}
// str_split substitution for PHP4
if( !function_exists( 'str_split' ) ) {
    function str_split( $string,$string_length=1 ) {
        if( strlen( $string )>$string_length || !$string_length ) {
            do {
                $c = strlen($string);
                $parts[] = substr($string,0,$string_length);
                $string = substr($string,$string_length);
            } while($string !== false);
        } else {
            $parts = array($string);
        }
        return $parts;
    }
}
// mb_substr_replace substition for PHP4
if ( !function_exists( 'mb_substr_replace' ) ) {
    function mb_substr_replace( $string, $replacement, $start, $length = null, $encoding = null ) {
        if ( extension_loaded( 'mbstring' ) === true ) {
            $string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);   
            if ( $start < 0 ) {
                $start = max(0, $string_length + $start);
            } else if ( $start > $string_length ) {
                $start = $string_length;
            }
            if ( $length < 0 ) {
                $length = max( 0, $string_length - $start + $length );
            } else if ( ( is_null( $length ) === true ) || ( $length > $string_length ) ) {
                $length = $string_length;
            }
            if ( ( $start + $length ) > $string_length) {
                $length = $string_length - $start;
            }
            if ( is_null( $encoding ) === true) {
                return mb_substr( $string, 0, $start ) . $replacement . mb_substr( $string, $start + $length, $string_length - $start - $length );
            }
		return mb_substr( $string, 0, $start, $encoding ) . $replacement . mb_substr( $string, $start + $length, $string_length - $start - $length, $encoding );
        }
	return ( is_null( $length ) === true ) ? substr_replace( $string, $replacement, $start ) : substr_replace( $string, $replacement, $start, $length );
    }
}

function print_settings() {
global $version;

if ( get_option ( 'twitterpw' ) != '' ) {
	$twitterpw = "Saved.";
} else {
	$twitterpw = "Blank.";
}
if ( get_option ( 'yourlsapi' ) != '' ) {
	$yourlsapi = "Saved.";
} else {
	$yourlsapi = "Blank.";
}
if ( get_option ( 'bitlyapi' ) != '' ) {
	$bitlyapi = "Saved.";
} else {
	$bitlyapi = "Blank.";
}
$options = array( 
	'newpost-published-update'=>get_option( 'newpost-published-update' ),
	'newpost-published-text'=>get_option( 'newpost-published-text' ),

	'oldpost-edited-update'=>get_option( 'oldpost-edited-update' ),
	'oldpost-edited-text'=>get_option( 'oldpost-edited-text' ),

	'jd_twit_pages'=>get_option( 'jd_twit_pages' ),
	'jd_twit_edited_pages'=>get_option( 'jd_twit_edited_pages' ),
	
	'oldpage-edited-text'=>get_option( 'oldpage-edited-text' ),
	'newpage-published-text'=>get_option( 'newpage-published-text' ),
	// Blogroll options
	'jd_twit_blogroll'=>get_option( 'jd_twit_blogroll' ),
	'newlink-published-text'=>get_option( 'newlink-published-text' ),

	// account settings
	'twitterlogin'=>get_option( 'twitterlogin' ),
	'twitterpw'=>$twitterpw,
	'cligsapi'=>get_option( 'cligsapi' ),
	'bitlylogin'=>get_option( 'bitlylogin' ),
	'bitlyapi'=>$bitlyapi,	
	'jd_api_post_status'=>get_option( 'jd_api_post_status' ),
	//yourls installation
	'yourlsapi'=>$yourlsapi,
	'yourlspath'=>get_option( 'yourlspath' ),
	'yourlsurl' =>get_option( 'yourlsurl' ),
	'yourlslogin'=>get_option( 'yourlslogin' ),
	'jd_keyword_format'=>get_option( 'jd_keyword_format' ),
	
	//twitter api
	'jd-twitter-service-name'=>get_option( 'jd-twitter-service-name' ),
	'jd-twitter-char-limit'=>get_option( 'jd-twitter-char-limit' ),	
	'jd_use_both_services'=>get_option( 'jd_use_both_services'  ),
	'x-twitterlogin'=>get_option( 'x-twitterlogin' ),
	'x-twitterpw'=>get_option( 'x-twitterpw' ),
	
	//advanced settings
	'use_tags_as_hashtags'=>get_option( 'use_tags_as_hashtags' ),
	'jd_replace_character'=>get_option( 'jd_replace_character' ),
	'jd_max_tags'=>get_option('jd_max_tags'),
	'jd_max_characters'=>get_option('jd_max_characters'),	
	'jd_post_excerpt'=>get_option( 'jd_post_excerpt' ),
	'jd_date_format'=>get_option( 'jd_date_format' ),
	'jd_twit_prepend'=>get_option( 'jd_twit_prepend' ),
	'jd_twit_append'=>get_option( 'jd_twit_append' ),
	'jd_twit_custom_url'=>get_option( 'jd_twit_custom_url' ),
	'jd_tweet_default'=>get_option( 'jd_tweet_default' ),
	'jd_twit_remote'=>get_option( 'jd_twit_remote' ),
	'jd_twit_quickpress'=>get_option( 'jd_twit_quickpress' ),

	'twitter-analytics-campaign'=>get_option( 'twitter-analytics-campaign' ),
	'use-twitter-analytics'=>get_option( 'use-twitter-analytics' ),
	'jd_dynamic_analytics'=>get_option( 'jd_dynamic_analytics' ),		
	'use_dynamic_analytics'=>get_option( 'use_dynamic_analytics' ),
	'jd_individual_twitter_users'=>get_option( 'jd_individual_twitter_users' ),
	'jd_shortener'=>get_option( 'jd_shortener' ),
	
	// Error checking
	'wp_twitter_failure'=>get_option( 'wp_twitter_failure' ),
	'wp_url_failure' =>get_option( 'wp_url_failure' ),
	'twitterInitialised'=>get_option( 'twitterInitialised' ),

	//category limits
	'limit_categories'=>get_option('limit_categories' ),
	'tweet_categories'=>get_option('tweet_categories' ),
	'disable_url_failure'=>get_option('disable_url_failure' ),
	'disable_twitter_failure'=>get_option('disable_twitter_failure' ),
	'wp_bitly_error'=>get_option( 'wp_bitly_error' ),
	'wp_cligs_error'=>get_option( 'wp_cligs_error' )
);

echo "<div class=\"settings\">";
echo "<strong>Raw Settings Output: Version $version</strong>";
echo "<ol>";
foreach ($options as $key=>$value) {
	echo "<li><code>$key</code>:$value</li>";
}

echo "</ol>";
echo "<p>";
_e( "[<a href='options-general.php?page=wp-to-twitter/wp-to-twitter.php'>Hide</a>] If you're experiencing trouble, please copy these settings into any request for support.",'wp-to-twitter');
echo "</p></div>";
}
?>