<?php
if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
exit();
} else {
delete_option( 'newpost-published-update' );
delete_option( 'newpost-published-text' );
delete_option( 'newpost-published-showlink' );

delete_option( 'oldpost-edited-update' );
delete_option( 'oldpost-edited-text' );
delete_option( 'oldpost-edited-showlink' );

delete_option( 'jd_twit_pages' );
delete_option( 'jd_twit_edited_pages' );
delete_option( 'oldpage-edited-text' );
delete_option( 'newpage-published-text' );

delete_option( 'jd_twit_remote' );

delete_option( 'jd_post_excerpt' );

// Cligs API
delete_option( 'cligsapi' );

// Error checking
delete_option( 'jd-functions-checked' );
delete_option( 'wp_twitter_failure' );
delete_option( 'wp_cligs_failure' );
delete_option( 'wp_url_failure' );
delete_option( 'wp_bitly_failure' );

// Blogroll options
delete_option( 'jd-use-link-title' );
delete_option( 'jd-use-link-description' );
delete_option( 'newlink-published-text' );
delete_option( 'jd_twit_blogroll' );

// Default publishing options.
delete_option( 'jd_tweet_default' );
// Note that default options are set.
delete_option( 'twitterInitialised' );
delete_option( 'wp_twitter_failure' );
delete_option( 'wp_cligs_failure' );
delete_option( 'twitterlogin' );
delete_option( 'twitterpw' );
delete_option( 'twitterlogin_encrypted' );
delete_option( 'cligsapi' );
delete_option( 'jd_twit_quickpress' );
delete_option( 'jd-use-cligs' );
delete_option( 'jd-use-none' );
delete_option( 'jd-use-wp' );

// Special Options
delete_option( 'jd_twit_prepend' );
delete_option( 'jd_twit_append' );
delete_option( 'jd_twit_remote' );
delete_option( 'twitter-analytics-campaign' );
delete_option( 'use-twitter-analytics' );
delete_option( 'jd_twit_custom_url' );
delete_option( 'jd_shortener' );

delete_option( 'jd_individual_twitter_users' );
delete_option( 'use_tags_as_hashtags' );
delete_option('jd_max_tags');
delete_option('jd_max_characters');	
// Bitly Settings
delete_option( 'bitlylogin' );
delete_option( 'jd-use-bitly' );
delete_option( 'bitlyapi' );

// twitter compatible api
delete_option( 'jd_api_post_status' );
delete_option( 'jd-twitter-service-name' );
delete_option( 'jd-twitter-char-limit' );	
delete_option( 'jd_use_both_services'  );
delete_option( 'x-twitterlogin' );
delete_option( 'x-twitterpw' );
delete_option( 'x-pw' );
delete_option( 'x-login' );
delete_option( 'x_jd_api_post_status' );
delete_option('app_consumer_key');
delete_option('app_consumer_secret');
delete_option('oauth_token');
delete_option('oauth_token_secret');
		
//dymamic analytics
delete_option( 'jd_dynamic_analytics' );		
delete_option( 'use_dynamic_analytics' );
//category limits
delete_option('limit_categories' );
delete_option('tweet_categories' );
//yourls installation
delete_option( 'yourlsapi' );
delete_option( 'yourlspath' );
delete_option( 'yourlsurl' );
delete_option( 'yourlslogin' );		
delete_option( 'jd_replace_character' );
delete_option( 'jd_date_format' );
delete_option( 'jd_keyword_format' );

}
?>