<?php
	if ( isset($_POST['oauth_settings'] ) ) {
		$oauth_message = jd_update_oauth_settings();
	}
	// FUNCTION to see if checkboxes should be checked
	function jd_checkCheckbox( $theFieldname ) {
		if( get_option( $theFieldname ) == '1'){
			echo 'checked="checked"';
		}
	}
	function jd_checkSelect( $theFieldname, $theValue ) {
		if( get_option( $theFieldname ) == $theValue ){
			echo 'selected="selected"';
		}
	}
	$wp_twitter_error = FALSE;
	$wp_cligs_error = FALSE;
	$wp_bitly_error = FALSE;
	$message = "";

	// SET DEFAULT OPTIONS
	if ( get_option( 'twitterInitialised') != '1' ) {
		update_option( 'newpost-published-update', '1' );
		update_option( 'newpost-published-text', 'New post: #title# #url#' );
		
		update_option( 'oldpost-edited-update', '1' );
		update_option( 'oldpost-edited-text', 'Post Edited: #title# #url#' );

		update_option( 'jd_twit_pages','0' );
		update_option( 'newpage-published-text','New page: #title# #url#' );
		
		update_option( 'jd_twit_edited_pages','0' );
		update_option( 'oldpage-edited-text','Page edited: #title# #url#' );

		update_option( 'jd_twit_blogroll', '1');
		update_option( 'newlink-published-text', 'New link: #title# #url#' );
		
		update_option( 'limit_categories','0' );
		update_option( 'jd_twit_quickpress', '1' );
		update_option( 'jd_shortener', '1' );
		update_option( 'use_tags_as_hashtags', '0' );
		update_option('jd_max_tags',3);
		update_option('jd_max_characters',15);	
		update_option('jd_replace_character','_');
			
		update_option( 'jd_twit_remote', '0' );
		update_option( 'jd_post_excerpt', 30 );
		// Use Google Analytics with Twitter
		update_option( 'twitter-analytics-campaign', 'twitter' );
		update_option( 'use-twitter-analytics', '0' );
		update_option( 'jd_dynamic_analytics','0' );		
		update_option( 'use_dynamic_analytics','category' );			
		// Use custom external URLs to point elsewhere. 
		update_option( 'jd_twit_custom_url', 'external_link' );	
		// Error checking
		update_option( 'wp_twitter_failure','0' );
		update_option( 'wp_url_failure','0' );
		// Default publishing options.
		update_option( 'jd_tweet_default', '0' );
		// Note that default options are set.
		update_option( 'twitterInitialised', '1' );	
		//Twitter API
		update_option( 'jd_keyword_format', '1' );
	}
	if ( get_option( 'twitterInitialised') == '1' && get_option( 'jd_post_excerpt' ) == "" ) { 
		update_option( 'jd_post_excerpt', 30 );
	}

// notifications from oauth connection		
    if ( isset($_POST['oauth_settings'] ) ) {
		if ( $oauth_message == "success" ) {
			print('
				<div id="message" class="updated fade">
					<p>'.__('WP to Twitter is now connected with Twitter.', 'wp-to-twitter').'</p>
				</div>

			');
		}
		else if ( $oauth_message == "fail" ) {
			print('
				<div id="message" class="updated fade">
					<p>'.__('OAuth Authentication Failed. Check your credentials and verify that <a href="http://www.twitter.com/">Twitter</a> is running.', 'wp-to-twitter').'</p>
				</div>

			');
		} else if ( $oauth_message == "cleared" ) {
			print('
				<div id="message" class="updated fade">
					<p>'.__('OAuth Authentication Data Cleared.', 'wp-to-twitter').'</p>
				</div>

			');		
		}
	}
	
	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'clear-error' ) {
		update_option( 'wp_twitter_failure','0' );
		update_option( 'wp_url_failure','0' );
		$message =  __("WP to Twitter Errors Cleared", 'wp-to-twitter');
	}
	
	// Error messages on status update or url shortener failures	
	if ( get_option( 'wp_twitter_failure' ) == '1' ) {
			
			if ( get_option( 'wp_twitter_failure' ) == '1' ) {
				$wp_to_twitter_failure .= "<p>" . __("Sorry! I couldn't get in touch with the Twitter servers to post your new blog post. Your tweet has been stored in a custom field attached to the post, so you can Tweet it manually if you wish! ", 'wp-to-twitter') . "</p>";
			} else if ( get_option( 'wp_twitter_failure' ) == '2') {
				$wp_to_twitter_failure .= "<p>" . __("Sorry! I couldn't get in touch with the Twitter servers to post your <strong>new link</strong>! You'll have to post it manually, I'm afraid. ", 'wp-to-twitter') . "</p>";
			}
			
		} else {
		$wp_to_twitter_failure = '';
	}

	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'advanced' ) {
		update_option( 'jd_tweet_default', $_POST['jd_tweet_default'] );
		update_option( 'jd_twit_remote',$_POST['jd_twit_remote'] );
		update_option( 'jd_twit_custom_url', $_POST['jd_twit_custom_url'] );
		update_option( 'jd_twit_quickpress', $_POST['jd_twit_quickpress'] );
		update_option( 'use_tags_as_hashtags', $_POST['use_tags_as_hashtags'] );
		update_option( 'jd_twit_prepend', $_POST['jd_twit_prepend'] );	
		update_option( 'jd_twit_append', $_POST['jd_twit_append'] );
		update_option( 'jd_post_excerpt', $_POST['jd_post_excerpt'] );	
		update_option('jd_max_tags',$_POST['jd_max_tags']);
		update_option('jd_max_characters',$_POST['jd_max_characters']);	
		update_option('jd_replace_character',$_POST['jd_replace_character']);
		update_option( 'jd_date_format',$_POST['jd_date_format'] );	
		update_option( 'jd_dynamic_analytics',$_POST['jd-dynamic-analytics'] );		
		update_option( 'use_dynamic_analytics',$_POST['use-dynamic-analytics'] );		
		update_option( 'use-twitter-analytics', $_POST['use-twitter-analytics'] );
		update_option( 'twitter-analytics-campaign', $_POST['twitter-analytics-campaign'] );
		update_option( 'jd_individual_twitter_users', $_POST['jd_individual_twitter_users'] );
		update_option( 'disable_url_failure' , $_POST['disable_url_failure'] );
		update_option( 'disable_twitter_failure' , $_POST['disable_twitter_failure'] );
		update_option( 'jd_twit_postie' , (int) $_POST['jd_twit_postie'] );
		if ( $_POST['jd_twit_postie'] == 1 ) {
			update_option( 'oldpost-edited-update','1');
		}
		update_option( 'disable_oauth_notice' , $_POST['disable_oauth_notice'] );
		update_option( 'wp_debug_oauth' , $_POST['wp_debug_oauth'] );
	
	
		$message .= __( 'WP to Twitter Advanced Options Updated' , 'wp-to-twitter');
	}
	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'options' ) {
		// UPDATE OPTIONS
		update_option( 'newpost-published-update', $_POST['newpost-published-update'] );
		update_option( 'newpost-published-text', $_POST['newpost-published-text'] );
		update_option( 'oldpost-edited-update', $_POST['oldpost-edited-update'] );
		update_option( 'oldpost-edited-text', $_POST['oldpost-edited-text'] );
		update_option( 'jd_twit_pages',$_POST['jd_twit_pages'] );
		update_option( 'jd_twit_edited_pages',$_POST['jd_twit_edited_pages'] );
		update_option( 'newpage-published-text', $_POST['newpage-published-text'] );
		update_option( 'oldpage-edited-text', $_POST['oldpage-edited-text'] );		
		update_option( 'newlink-published-text', $_POST['newlink-published-text'] );
		update_option( 'jd_twit_blogroll',$_POST['jd_twit_blogroll'] );
		update_option( 'jd_shortener', $_POST['jd_shortener'] );

		if ( get_option( 'jd_shortener' ) == 2 && ( get_option( 'bitlylogin' ) == "" || get_option( 'bitlyapi' ) == "" ) ) {
			$message .= __( 'You must add your Bit.ly login and API key in order to shorten URLs with Bit.ly.' , 'wp-to-twitter');
			$message .= "<br />";
		}
		if ( get_option( 'jd_shortener' ) == 6 && ( get_option( 'yourlslogin' ) == "" || get_option( 'yourlsapi' ) == "" || get_option( 'yourlsurl' ) == "" ) ) {
			$message .= __( 'You must add your YOURLS remote URL, login, and password in order to shorten URLs with a remote installation of YOURLS.' , 'wp-to-twitter');
			$message .= "<br />";
		}
		if ( get_option( 'jd_shortener' ) == 5 && ( get_option( 'yourlspath' ) == "" ) ) {
			$message .= __( 'You must add your YOURLS server path in order to shorten URLs with a remote installation of YOURLS.' , 'wp-to-twitter');
			$message .= "<br />";
		}		
		
		$message .= __( 'WP to Twitter Options Updated' , 'wp-to-twitter');

	}

	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'setcategories' ) {
		
		if ( is_array($_POST['categories'])) {
			$categories = $_POST['categories'];
			update_option('limit_categories','1');
			update_option('tweet_categories',$categories);
			$message = __("Category limits updated.");
		} else {
			update_option('limit_categories','0');
			update_option('tweet_categories','');
			$message = __("Category limits unset.",'wp-to-twitter');
		}
	
	}
		
	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'yourlsapi' ) {
		if ( $_POST['yourlsapi'] != '' && isset( $_POST['submit'] ) ) {
			update_option( 'yourlsapi', trim($_POST['yourlsapi']) );
			$message = __("YOURLS password updated. ", 'wp-to-twitter');
		} else if ( isset( $_POST['clear'] ) ) {
			update_option( 'yourlsapi','' );
			$message = __( "YOURLS password deleted. You will be unable to use your remote YOURLS account to create short URLS.", 'wp-to-twitter');
		} else {
			$message = __( "Failed to save your YOURLS password! ", 'wp-to-twitter' );
		}
		if ( $_POST['yourlslogin'] != '' ) {
			update_option( 'yourlslogin', trim($_POST['yourlslogin']) );
			$message .= __( "YOURLS username added. ",'wp-to-twitter' ); 
		}
		if ( $_POST['yourlsurl'] != '' ) {
			update_option( 'yourlsurl', trim($_POST['yourlsurl']) );
			$message .= __( "YOURLS API url added. ",'wp-to-twitter' ); 
		} else {
			update_option('yourlsurl','');
			$message .= __( "YOURLS API url removed. ",'wp-to-twitter' ); 			
		}
		if ( $_POST['yourlspath'] != '' ) {
			update_option( 'yourlspath', trim($_POST['yourlspath']) );	
			if ( file_exists( $_POST['yourlspath'] ) ) {
			$message .= __( "YOURLS local server path added. ",'wp-to-twitter'); 
			} else {
			$message .= __( "The path to your YOURLS installation is not correct. ",'wp-to-twitter' );
			}
		} else {
			update_option( 'yourlspath','' );
			$message .= __( "YOURLS local server path removed. ",'wp-to-twitter');
		}
		if ( $_POST['jd_keyword_format'] != '' ) {
			update_option( 'jd_keyword_format', $_POST['jd_keyword_format'] );
			$message .= __( "YOURLS will use Post ID for short URL slug.",'wp-to-twitter');
		} else {
			update_option( 'jd_keyword_format','' );
			$message .= __( "YOURLS will not use Post ID for the short URL slug.",'wp-to-twitter');
		}
	} 
	
	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'cligsapi' ) {
		if ( $_POST['cligsapi'] != '' && isset( $_POST['submit'] ) ) {
			update_option( 'cligsapi', trim($_POST['cligsapi']) );
			$message = __("Cligs API Key Updated", 'wp-to-twitter');
		} else if ( isset( $_POST['clear'] ) ) {
			update_option( 'cligsapi','' );
			$message = __("Cli.gs API Key deleted. Cli.gs created by WP to Twitter will no longer be associated with your account. ", 'wp-to-twitter');
		} else {
			$message = __("Cli.gs API Key not added - <a href='http://cli.gs/user/api/'>get one here</a>! ", 'wp-to-twitter');
		}
	} 
	if ( isset($_POST['submit-type']) && $_POST['submit-type'] == 'bitlyapi' ) {
		if ( $_POST['bitlyapi'] != '' && isset( $_POST['submit'] ) ) {
			update_option( 'bitlyapi', trim($_POST['bitlyapi']) );
			$message = __("Bit.ly API Key Updated.", 'wp-to-twitter');
		} else if ( isset( $_POST['clear'] ) ) {
			update_option( 'bitlyapi','' );
			$message = __("Bit.ly API Key deleted. You cannot use the Bit.ly API without an API key. ", 'wp-to-twitter');
		} else {
			$message = __("Bit.ly API Key not added - <a href='http://bit.ly/account/'>get one here</a>! An API key is required to use the Bit.ly URL shortening service.", 'wp-to-twitter');
		}
		if ( $_POST['bitlylogin'] != '' && isset( $_POST['submit'] ) ) {
			update_option( 'bitlylogin', trim($_POST['bitlylogin']) );
			$message .= __(" Bit.ly User Login Updated.", 'wp-to-twitter');
		} else if ( isset( $_POST['clear'] ) ) {
			update_option( 'bitlylogin','' );
			$message = __("Bit.ly User Login deleted. You cannot use the Bit.ly API without providing your username. ", 'wp-to-twitter');
		} else {
			$message = __("Bit.ly Login not added - <a href='http://bit.ly/account/'>get one here</a>! ", 'wp-to-twitter');
		}
	}
	///*
	// Check whether the server has supported for needed functions.
	if (  isset($_POST['submit-type']) && $_POST['submit-type'] == 'check-support' ) {
		$message = jd_check_functions();
	}
// If you're attempting to solve the "settings page doesn't display" problem, begin your comment here. 



function jd_check_functions() {
	$message = "<div class='update'><ul>";
	// grab or set necessary variables
	$testurl =  get_bloginfo( 'url' );
	$shortener = get_option( 'jd_shortener' );
	$title = urlencode( 'Your blog home' );
	$shrink = jd_shorten_link( $testurl, $title, false, 'true' );

	$api_url = $jdwp_api_post_status;
	$yourls_URL = "";
		
	if ($shrink == FALSE) {
		if ($shortener == 1) {
			$error = htmlentities( get_option('wp_cligs_error') );
		} else if ( $shortener == 2 ) {
			$error = htmlentities( get_option('wp_bitly_error') );
		} else {
			$error = _('No error information is available for your shortener.','wp-to-twitter');
		}	
		$message .= __("<li class=\"error\"><strong>WP to Twitter was unable to contact your selected URL shortening service.</strong></li>",'wp-to-twitter');
		$message .= "<li><code>$error</code></li>";
	} else {
		$message .= __("<li><strong>WP to Twitter successfully contacted your selected URL shortening service.</strong>  The following link should point to your blog homepage:",'wp-to-twitter');
		$message .= " <a href='$shrink'>$shrink</a></li>";	
	}
		
	//check twitter credentials
	if ( wtt_oauth_test() ) {
		$rand = rand(1000000,9999999);
		$testpost = jd_doTwitterAPIPost( "This is a test of WP to Twitter. $shrink ($rand)" );
			if ($testpost) {
				$message .= __("<li><strong>WP to Twitter successfully submitted a status update to Twitter.</strong></li>",'wp-to-twitter'); 
			} else {
				$message .=	__("<li class=\"error\"><strong>WP to Twitter failed to submit an update to Twitter.</strong></li>",'wp-to-twitter'); 
				}
	} else {
		$message .= "<strong>"._e('You have not connected WordPress to Twitter.','wp-to-twitter')."</strong> ";
	}
		// If everything's OK, there's  no reason to do this again.	
		if ($testpost == FALSE && $shrink == FALSE  ) {
			$message .= __("<li class=\"error\"><strong>Your server does not appear to support the required methods for WP to Twitter to function.</strong> You can try it anyway - these tests aren't perfect.</li>", 'wp-to-twitter');
		} else { 
		}
	if ( $testpost && $shrink ) {
	$message .= __("<li><strong>Your server should run WP to Twitter successfully.</strong></li>", 'wp-to-twitter');
	}
	$message .= "</ul>
	</div>";
	return $message;
}
?>

<div class="wrap" id="wp-to-twitter">
<?php if ( $message ) { ?>
<div id="message" class="updated fade"><?php echo $message; ?></div>
<?php } ?>
<div id="dropmessage" class="updated" style="display:none;"></div>
<?php if (isset($_GET['export']) && $_GET['export'] == "settings") {
print_settings();
} ?>

<h2><?php _e("WP to Twitter Options", 'wp-to-twitter'); ?></h2>

<?php //echo get_option('jd_last_tweet'); ?>

<?php  
$wp_to_twitter_directory = get_bloginfo( 'wpurl' ) . '/' . PLUGINDIR . '/' . dirname( plugin_basename(__FILE__) );
?>
<div class="resources">
<img src="<?php echo $wp_to_twitter_directory; ?>/wp-to-twitter-logo.png" alt="WP to Twitter" />
<p>
<a href="http://www.joedolson.com/articles/wp-to-twitter/support/"><?php _e("Get Support",'wp-to-twitter'); ?></a> &middot; 
<a href="?page=wp-to-twitter/wp-to-twitter.php&amp;export=settings"><?php _e("Export Settings",'wp-to-twitter'); ?></a> &middot; 
<a href="http://www.joedolson.com/donate.php"><?php _e("Make a Donation",'wp-to-twitter'); ?></a>
</p>
<div>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<div>
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="8490399" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="Donate" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</div>
</form>
</div>

</div>

<p><?php _e("Shortcodes available in post update templates:", 'wp-to-twitter'); ?></p>
<ul>
<li><?php _e("<code>#title#</code>: the title of your blog post", 'wp-to-twitter'); ?></li>
<li><?php _e("<code>#blog#</code>: the title of your blog", 'wp-to-twitter'); ?></li>
<li><?php _e("<code>#post#</code>: a short excerpt of the post content", 'wp-to-twitter'); ?></li>
<li><?php _e("<code>#category#</code>: the first selected category for the post", 'wp-to-twitter'); ?></li>
<li><?php _e("<code>#date#</code>: the post date", 'wp-to-twitter'); ?></li>
<li><?php _e("<code>#url#</code>: the post URL", 'wp-to-twitter'); ?></li>
<li><?php _e("<code>#author#</code>: the post author",'wp-to-twitter'); ?></li>
</ul>
<p><?php _e("You can also create custom shortcodes to access WordPress custom fields. Use doubled square brackets surrounding the name of your custom field to add the value of that custom field to your status update. Example: <code>[[custom_field]]</code></p>", 'wp-to-twitter'); ?>
		
<?php if ( get_option( 'wp_twitter_failure' ) == '1' || get_option( 'wp_url_failure' ) == '1' ) { ?>
		<div class="error">
		<?php if ( get_option( 'wp_twitter_failure' ) == '1' ) {
		_e("<p>One or more of your last posts has failed to send it's status update to Twitter. Your Tweet has been saved in your post custom fields, and you can re-Tweet it at your leisure.</p>", 'wp-to-twitter');
		echo "<p><strong>".get_option( 'jd_status_message' )."</strong></p>";
		}
		if ( get_option( 'wp_url_failure' ) == '1' ) {
		_e("<p>The query to the URL shortener API failed, and your URL was not shrunk. The full post URL was attached to your Tweet. Check with your URL shortening provider to see if there are any known issues. [<a href=\"http://blog.cli.gs\">Cli.gs Blog</a>] [<a href=\"http://blog.bit.ly\">Bit.ly Blog</a>]</p>", 'wp-to-twitter');
		}
		echo $wp_to_twitter_failure;
		?>
		</div>
	<form method="post" action="">
	<div><input type="hidden" name="submit-type" value="clear-error" /></div>
	<p><input type="submit" name="submit" value="<?php _e("Clear 'WP to Twitter' Error Messages", 'wp-to-twitter'); ?>" class="button-primary" /></p>
	</form>
<?php
}
?>		
<div class="jd-settings" id="poststuff">

<?php wtt_connect_oauth(); ?>

<div class="ui-sortable meta-box-sortables">
<?php if ( isset( $_POST['submit-type']) && $_POST['submit-type'] == 'options' ) { ?>
<div class="postbox">
<?php } else { ?>
<div class="postbox closed">
<?php } ?>
	<div class="handlediv" title="Click to toggle"><br/></div>
	<h3><?php _e('Basic Settings','wp-to-twitter'); ?></h3>
	<div class="inside">
		<br class="clear" />
	<form method="post" action="">
	<div>
		<fieldset>
			<legend><?php _e("Tweet Templates", 'wp-to-twitter'); ?></legend>
			<p>
				<input type="checkbox" name="newpost-published-update" id="newpost-published-update" value="1" <?php jd_checkCheckbox('newpost-published-update')?> />
				<label for="newpost-published-update"><strong><?php _e("Update when a post is published", 'wp-to-twitter'); ?></strong></label> <label for="newpost-published-text"><br /><?php _e("Text for new post updates:", 'wp-to-twitter'); ?></label> <input type="text" name="newpost-published-text" id="newpost-published-text" size="60" maxlength="120" value="<?php echo( attribute_escape( stripslashes( get_option( 'newpost-published-text' ) ) ) ); ?>" />
			</p>
		
			<p>
			<?php if ( get_option( 'jd_twit_postie' ) != 1 ) { ?>
				<input type="checkbox" name="oldpost-edited-update" id="oldpost-edited-update" value="1" <?php jd_checkCheckbox('oldpost-edited-update')?> />
				<label for="oldpost-edited-update"><strong><?php _e("Update when a post is edited", 'wp-to-twitter'); ?></strong></label><br /><label for="oldpost-edited-text"><?php _e("Text for editing updates:", 'wp-to-twitter'); ?></label> <input type="text" name="oldpost-edited-text" id="oldpost-edited-text" size="60" maxlength="120" value="<?php echo( attribute_escape( stripslashes( get_option('oldpost-edited-text' ) ) ) ); ?>" />		
			<?php } else { ?>
				<input type="checkbox" name="oldpost-edited-update" id="oldpost-edited-update" value="1" disabled="disabled" checked="checked" />
				<label for="oldpost-edited-update"><strong><?php _e("Update when a post is edited", 'wp-to-twitter'); ?></strong></label><br /><label for="oldpost-edited-text"><?php _e("Text for editing updates:", 'wp-to-twitter'); ?></label> <input type="text" name="oldpost-edited-text" id="oldpost-edited-text" size="60" maxlength="120" value="<?php echo( attribute_escape( stripslashes( get_option('oldpost-edited-text' ) ) ) ); ?>" readonly="readonly" />					
				<br /><small><?php _e('You can not disable updates on edits when using Postie or similar plugins.'); ?></small>
			<?php } ?>			</p>	
			<p>
				<input type="checkbox" name="jd_twit_pages" id="jd_twit_pages" value="1" <?php jd_checkCheckbox('jd_twit_pages')?> />
				<label for="jd_twit_pages"><strong><?php _e("Update Twitter when new Wordpress Pages are published", 'wp-to-twitter'); ?></strong></label><br /><label for="newpage-published-text"><?php _e("Text for new page updates:", 'wp-to-twitter'); ?></label> <input type="text" name="newpage-published-text" id="newpage-published-text" size="60" maxlength="120" value="<?php echo( attribute_escape( stripslashes( get_option('newpage-published-text' ) ) ) ); ?>" />	
			</p>
			<p>
				<input type="checkbox" name="jd_twit_edited_pages" id="jd_twit_edited_pages" value="1" <?php jd_checkCheckbox('jd_twit_edited_pages')?> />
				<label for="jd_twit_edited_pages"><strong><?php _e("Update Twitter when WordPress Pages are edited", 'wp-to-twitter'); ?></strong></label><br /><label for="oldpage-edited-text"><?php _e("Text for page edit updates:", 'wp-to-twitter'); ?></label> <input type="text" name="oldpage-edited-text" id="oldpage-edited-text" size="60" maxlength="120" value="<?php echo( attribute_escape( stripslashes( get_option('oldpage-edited-text' ) ) ) ); ?>" />	
			</p>
			<p>
				<input type="checkbox" name="jd_twit_blogroll" id="jd_twit_blogroll" value="1" <?php jd_checkCheckbox('jd_twit_blogroll')?> />
				<label for="jd_twit_blogroll"><strong><?php _e("Update Twitter when you post a Blogroll link", 'wp-to-twitter'); ?></strong></label><br />				
				<label for="newlink-published-text"><?php _e("Text for new link updates:", 'wp-to-twitter'); ?></label> <input type="text" name="newlink-published-text" id="newlink-published-text" size="60" maxlength="120" value="<?php echo ( attribute_escape( stripslashes( get_option( 'newlink-published-text' ) ) ) ); ?>" /><br /><small><?php _e('Available shortcodes: <code>#url#</code>, <code>#title#</code>, and <code>#description#</code>.','wp-to-twitter'); ?></small>
			</p>
			</fieldset>
			<fieldset>	
			<legend><?php _e("Choose your short URL service (account settings below)",'wp-to-twitter' ); ?></legend>
			<p>
			<select name="jd_shortener" id="jd_shortener">
				<option value="1" <?php jd_checkSelect('jd_shortener','1'); ?>><?php _e("Use Cli.gs for my URL shortener.", 'wp-to-twitter'); ?></option> 
				<option value="2" <?php jd_checkSelect('jd_shortener','2'); ?>><?php _e("Use Bit.ly for my URL shortener.", 'wp-to-twitter'); ?></option>
				<option value="5" <?php jd_checkSelect('jd_shortener','5'); ?>><?php _e("YOURLS (installed on this server)", 'wp-to-twitter'); ?></option>
				<option value="6" <?php jd_checkSelect('jd_shortener','6'); ?>><?php _e("YOURLS (installed on a remote server)", 'wp-to-twitter'); ?></option>		
				<option value="4" <?php jd_checkSelect('jd_shortener','4'); ?>><?php _e("Use WordPress as a URL shortener.", 'wp-to-twitter'); ?></option> 
				<option value="3" <?php jd_checkSelect('jd_shortener','3'); ?>><?php _e("Don't shorten URLs.", 'wp-to-twitter'); ?></option>
			</select><br />		
			<small><?php _e("Using WordPress as a URL shortener will send URLs to Twitter in the default URL format for WordPress: <code>http://domain.com/wpdir/?p=123</code>. Google Analytics is not available when using WordPress shortened URLs.", 'wp-to-twitter'); ?></small>
			</p>
			</fieldset>
				<div>
		<input type="hidden" name="submit-type" value="options" />
		</div>
	<input type="submit" name="submit" value="<?php _e("Save WP->Twitter Options", 'wp-to-twitter'); ?>" class="button-primary" />	
	</div>
	</form>
</div>
</div>
</div>
<div class="ui-sortable meta-box-sortables">
<?php if ( ( isset( $_POST['submit-type'] ) && ($_POST['submit-type']!='setcategories' && $_POST['submit-type']!='advanced' && $_POST['submit-type']!='options' && $_POST['submit-type']!="check-support" ) ) ) { ?>
<div class="postbox">
<?php } else { ?>
<div class="postbox closed">
<?php } ?>				<div class="handlediv" title="Click to toggle"><br/></div>
				<h3><?php _e('<abbr title="Uniform Resource Locator">URL</abbr> Shortener Account Settings','wp-to-twitter'); ?></h3>

				<div class="inside">
		<div class="panel">
<h4 class="cligs"><span><?php _e("Your Cli.gs account details", 'wp-to-twitter'); ?></span></h4>

	<form method="post" action="">
	<div>
		<p>
		<label for="cligsapi"><?php _e("Your Cli.gs <abbr title='application programming interface'>API</abbr> Key:", 'wp-to-twitter'); ?></label>
		<input type="text" name="cligsapi" id="cligsapi" size="40" value="<?php echo ( attribute_escape( get_option( 'cligsapi' ) ) ) ?>" />
		</p>
		<div>
		<input type="hidden" name="submit-type" value="cligsapi" />
		</div>
		<p><input type="submit" name="submit" value="Save Cli.gs API Key" class="button-primary" /> <input type="submit" name="clear" value="Clear Cli.gs API Key" />&raquo; <small><?php _e("Don't have a Cli.gs account or Cligs API key? <a href='http://cli.gs/user/api/'>Get one free here</a>!<br />You'll need an API key in order to associate the Cligs you create with your Cligs account.", 'wp-to-twitter'); ?></small></p>
	</div>
	</form>
	</div>
	<div class="panel">
	
<h4 class="bitly"><span><?php _e("Your Bit.ly account details", 'wp-to-twitter'); ?></span></h4>
	<form method="post" action="">
	
	<div>
		<p>
		<label for="bitlylogin"><?php _e("Your Bit.ly username:", 'wp-to-twitter'); ?></label>
		<input type="text" name="bitlylogin" id="bitlylogin" value="<?php echo ( attribute_escape( get_option( 'bitlylogin' ) ) ) ?>" />
		</p>	
		<p>
		<label for="bitlyapi"><?php _e("Your Bit.ly <abbr title='application programming interface'>API</abbr> Key:", 'wp-to-twitter'); ?></label>
		<input type="text" name="bitlyapi" id="bitlyapi" size="40" value="<?php echo ( attribute_escape( get_option( 'bitlyapi' ) ) ) ?>" />
		</p>

		<div>
		<input type="hidden" name="submit-type" value="bitlyapi" />
		</div>
		<p><input type="submit" name="submit" value="<?php _e('Save Bit.ly API Key','wp-to-twitter'); ?>" class="button-primary" /> <input type="submit" name="clear" value="<?php _e('Clear Bit.ly API Key','wp-to-twitter'); ?>" /><br /><small><?php _e("A Bit.ly API key and username is required to shorten URLs via the Bit.ly API and WP to Twitter.", 'wp-to-twitter' ); ?></small></p>
	</div>
	</form>	
</div>
<div class="panel">
<h4 class="yourls"><span><?php _e("Your YOURLS account details", 'wp-to-twitter'); ?></span></h4>
	<form method="post" action="">
	<div>
		<p>
		<label for="yourlspath"><?php _e('Path to the YOURLS config file (Local installations)','wp-to-twitter'); ?></label> <input type="text" id="yourlspath" name="yourlspath" size="60" value="<?php echo ( attribute_escape( get_option( 'yourlspath' ) ) ); ?>"/>
		<small><?php _e('Example:','wp-to-twitter'); ?> <code>/home/username/www/www/yourls/includes/config.php</code></small>
		</p>				
		<p>
		<label for="yourlsurl"><?php _e('URI to the YOURLS API (Remote installations)','wp-to-twitter'); ?></label> <input type="text" id="yourlsurl" name="yourlsurl" size="60" value="<?php echo ( attribute_escape( get_option( 'yourlsurl' ) ) ); ?>"/>
		<small><?php _e('Example:','wp-to-twitter'); ?> <code>http://domain.com/yourls-api.php</code></small>
		</p>
		<p>
		<label for="yourlslogin"><?php _e("Your YOURLS username:", 'wp-to-twitter'); ?></label>
		<input type="text" name="yourlslogin" id="yourlslogin" value="<?php echo ( attribute_escape( get_option( 'yourlslogin' ) ) ) ?>" />
		</p>	
		<p>
		<label for="yourlsapi"><?php _e("Your YOURLS password:", 'wp-to-twitter'); ?> <?php if ( get_option( 'yourlsapi' ) != '') { _e("<em>Saved</em>",'wp-to-twitter'); } ?></label>
		<input type="text" name="yourlsapi" id="yourlsapi" size="40" value="" />
		</p>
		<p>
		<input type="checkbox" name="jd_keyword_format" id="jd_keyword_format" value="1" <?php jd_checkCheckbox( 'jd_keyword_format' ); ?> /> 		<label for="jd_keyword_format"><?php _e("Use Post ID for YOURLS url slug.",'wp-to-twitter'); ?></label>
		</p>
		<div>
		<input type="hidden" name="submit-type" value="yourlsapi" />
		</div>
		<p><input type="submit" name="submit" value="<?php _e('Save YOURLS Account Info','wp-to-twitter'); ?>" class="button-primary" /> <input type="submit" name="clear" value="<?php _e('Clear YOURLS password','wp-to-twitter'); ?>" /><br /><small><?php _e("A YOURLS password and username is required to shorten URLs via the remote YOURLS API and WP to Twitter.", 'wp-to-twitter' ); ?></small></p>
	</div>
	</form>		
	</div>
	
</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">
<?php if ( isset( $_POST['submit-type']) && $_POST['submit-type']=='advanced') { ?>
<div class="postbox">
<?php } else { ?>
<div class="postbox closed">
<?php } ?>

	<div class="handlediv" title="Click to toggle"><br/></div>
	<h3><?php _e('Advanced Settings','wp-to-twitter'); ?></h3>
	<div class="inside">
		<br class="clear" />
	<form method="post" action="">
	<div>		

			<fieldset>
				<legend><?php _e("Advanced Tweet settings","wp-to-twitter"); ?></legend>
			<p>
				<input type="checkbox" name="use_tags_as_hashtags" id="use_tags_as_hashtags" value="1" <?php jd_checkCheckbox('use_tags_as_hashtags')?> />
				<label for="use_tags_as_hashtags"><?php _e("Add tags as hashtags on Tweets", 'wp-to-twitter'); ?></label>
				<br /><label for="jd_replace_character"><?php _e("Spaces replaced with:",'wp-to-twitter'); ?></label> <input type="text" name="jd_replace_character" id="jd_replace_character" value="<?php echo attribute_escape( get_option('jd_replace_character') ); ?>" size="3" /><br />
				<small><?php _e("Default replacement is an underscore (<code>_</code>). Use <code>[ ]</code> to remove spaces entirely.",'wp-to-twitter'); ?></small>					
			</p>
			<p>
			<label for="jd_max_tags"><?php _e("Maximum number of tags to include:",'wp-to-twitter'); ?></label> <input type="text" name="jd_max_tags" id="jd_max_tags" value="<?php echo attribute_escape( get_option('jd_max_tags') ); ?>" size="3" />
			<label for="jd_max_characters"><?php _e("Maximum length in characters for included tags:",'wp-to-twitter'); ?></label> <input type="text" name="jd_max_characters" id="jd_max_characters" value="<?php echo attribute_escape( get_option('jd_max_characters') ); ?>" size="3" /><br />
			<small><?php _e("These options allow you to restrict the length and number of WordPress tags sent to Twitter as hashtags. Set to <code>0</code> or leave blank to allow any and all tags.",'wp-to-twitter'); ?></small>			
			</p>			
			<p>
				<label for="jd_post_excerpt"><?php _e("Length of post excerpt (in characters):", 'wp-to-twitter'); ?></label> <input type="text" name="jd_post_excerpt" id="jd_post_excerpt" size="3" maxlength="3" value="<?php echo ( attribute_escape( get_option( 'jd_post_excerpt' ) ) ) ?>" /><br /><small><?php _e("By default, extracted from the post itself. If you use the 'Excerpt' field, that will be used instead.", 'wp-to-twitter'); ?></small>
			</p>				
			<p>
				<label for="jd_date_format"><?php _e("WP to Twitter Date Formatting:", 'wp-to-twitter'); ?></label> <input type="text" name="jd_date_format" id="jd_date_format" size="12" maxlength="12" value="<?php if (get_option('jd_date_format')=='') { echo ( attribute_escape( get_option('date_format') ) ); } else { echo ( attribute_escape( get_option( 'jd_date_format' ) ) ); }?>" /> (<?php if ( get_option( 'jd_date_format' ) != '' ) { echo date_i18n( get_option( 'jd_date_format' ) ); } else { echo date_i18n( get_option( 'date_format' ) ); } ?>)<br />
				<small><?php _e("Default is from your general settings. <a href='http://codex.wordpress.org/Formatting_Date_and_Time'>Date Formatting Documentation</a>.", 'wp-to-twitter'); ?></small>
			</p>
			
			<p>
				<label for="jd_twit_prepend"><?php _e("Custom text before all Tweets:", 'wp-to-twitter'); ?></label> <input type="text" name="jd_twit_prepend" id="jd_twit_prepend" size="20" maxlength="20" value="<?php echo ( attribute_escape( get_option( 'jd_twit_prepend' ) ) ) ?>" />
				<label for="jd_twit_append"><?php _e("Custom text after all Tweets:", 'wp-to-twitter'); ?></label> <input type="text" name="jd_twit_append" id="jd_twit_append" size="20" maxlength="20" value="<?php echo ( attribute_escape( get_option( 'jd_twit_append' ) ) ) ?>" />
			</p>
			<p>
				<label for="jd_twit_custom_url"><?php _e("Custom field for an alternate URL to be shortened and Tweeted:", 'wp-to-twitter'); ?></label> <input type="text" name="jd_twit_custom_url" id="jd_twit_custom_url" size="40" maxlength="120" value="<?php echo ( attribute_escape( get_option( 'jd_twit_custom_url' ) ) ) ?>" /><br />
				<small><?php _e("You can use a custom field to send an alternate URL for your post. The value is the name of a custom field containing your external URL.", 'wp-to-twitter'); ?></small>
			</p>	
		</fieldset>	
		<fieldset>
		<legend><?php _e( "Special Cases when WordPress should send a Tweet",'wp-to-twitter' ); ?></legend>
			<p>
				<input type="checkbox" name="jd_tweet_default" id="jd_tweet_default" value="1" <?php jd_checkCheckbox('jd_tweet_default')?> />
				<label for="jd_tweet_default"><?php _e("Do not post status updates by default", 'wp-to-twitter'); ?></label><br />
				<small><?php _e("By default, all posts meeting other requirements will be posted to Twitter. Check this to change your setting.", 'wp-to-twitter'); ?></small>
			</p>
			<p>
				<input type="checkbox" name="jd_twit_remote" id="jd_twit_remote" value="1" <?php jd_checkCheckbox('jd_twit_remote')?> />
				<label for="jd_twit_remote"><?php _e("Send Twitter Updates on remote publication (Post by Email or XMLRPC Client)", 'wp-to-twitter'); ?></label><br />
				<input type="checkbox" name="jd_twit_postie" id="jd_twit_postie" value="1" <?php jd_checkCheckbox('jd_twit_postie')?> />
				<label for="jd_twit_postie"><?php _e("I'm using a plugin to post by email, such as Postie. Only check this if your updates do not work.", 'wp-to-twitter'); ?></label>			
			</p>
			<p>
				<input type="checkbox" name="jd_twit_quickpress" id="jd_twit_quickpress" value="1" <?php jd_checkCheckbox('jd_twit_quickpress')?> />
				<label for="jd_twit_quickpress"><?php _e("Update Twitter when a post is published using QuickPress", 'wp-to-twitter'); ?></label>
			</p>
		</fieldset>
		<fieldset>
		<legend><?php _e( "Google Analytics Settings",'wp-to-twitter' ); ?></legend>
				<p><?php _e("You can track the response from Twitter using Google Analytics by defining a campaign identifier here. You can either define a static identifier or a dynamic identifier. Static identifiers don't change from post to post; dynamic identifiers are derived from information relevant to the specific post. Dynamic identifiers will allow you to break down your statistics by an additional variable.","wp-to-twitter"); ?></p>
				
			<p>
				<input type="checkbox" name="use-twitter-analytics" id="use-twitter-analytics" value="1" <?php jd_checkCheckbox('use-twitter-analytics')?> />
				<label for="use-twitter-analytics"><?php _e("Use a Static Identifier with WP-to-Twitter", 'wp-to-twitter'); ?></label><br />
				<label for="twitter-analytics-campaign"><?php _e("Static Campaign identifier for Google Analytics:", 'wp-to-twitter'); ?></label> <input type="text" name="twitter-analytics-campaign" id="twitter-analytics-campaign" size="40" maxlength="120" value="<?php echo ( attribute_escape( get_option( 'twitter-analytics-campaign' ) ) ) ?>" /><br />
			</p>
			<p>
				<input type="checkbox" name="use-dynamic-analytics" id="use-dynamic-analytics" value="1" <?php jd_checkCheckbox('use_dynamic_analytics')?> />
				<label for="use-dynamic-analytics"><?php _e("Use a dynamic identifier with Google Analytics and WP-to-Twitter", 'wp-to-twitter'); ?></label><br />
			<label for="jd-dynamic-analytics"><?php _e("What dynamic identifier would you like to use?","wp-to-twitter"); ?></label><br />
				<select name="jd-dynamic-analytics" id="jd-dynamic-analytics">
					<option value="post_category"<?php jd_checkSelect( 'jd_dynamic_analytics','post_category'); ?>><?php _e("Category","wp-to-twitter"); ?></option>
					<option value="post_ID"<?php jd_checkSelect( 'jd_dynamic_analytics','post_ID'); ?>><?php _e("Post ID","wp-to-twitter"); ?></option>
					<option value="post_title"<?php jd_checkSelect( 'jd_dynamic_analytics','post_title'); ?>><?php _e("Post Title","wp-to-twitter"); ?></option>
					<option value="post_author"<?php jd_checkSelect( 'jd_dynamic_analytics','post_author'); ?>><?php _e("Author","wp-to-twitter"); ?></option>
				</select><br />
			</p>
		</fieldset>
		<fieldset>
		<legend><?php _e('Individual Authors','wp-to-twitter'); ?></legend>
			<p>
				<input type="checkbox" name="jd_individual_twitter_users" id="jd_individual_twitter_users" value="1" <?php jd_checkCheckbox('jd_individual_twitter_users')?> />
				<label for="jd_individual_twitter_users"><?php _e("Authors have individual Twitter accounts", 'wp-to-twitter'); ?></label><br /><small><?php _e('Authors can set their username in their user profile. As of version 2.2.0, this feature no longer allows authors to post to their own Twitter accounts. It can only add an @reference to the author.', 'wp-to-twitter'); ?></small>
			</p>			
		</fieldset>
		<fieldset>
		<legend><?php _e('Disable Error Messages','wp-to-twitter'); ?></legend>
			<p>
				<input type="checkbox" name="disable_url_failure" id="disable_url_failure" value="1" <?php jd_checkCheckbox('disable_url_failure')?> />
				<label for="disable_url_failure"><?php _e("Disable global URL shortener error messages.", 'wp-to-twitter'); ?></label>
			</p>
			<p>
				<input type="checkbox" name="disable_twitter_failure" id="disable_twitter_failure" value="1" <?php jd_checkCheckbox('disable_twitter_failure')?> />
				<label for="disable_twitter_failure"><?php _e("Disable global Twitter API error messages.", 'wp-to-twitter'); ?></label>
			</p>
			<p>
				<input type="checkbox" name="disable_oauth_notice" id="disable_oauth_notice" value="1" <?php jd_checkCheckbox('disable_oauth_notice')?> />
				<label for="disable_oauth_notice"><?php _e("Disable notification to implement OAuth", 'wp-to-twitter'); ?></label>
			</p>
			<p>
				<input type="checkbox" name="wp_debug_oauth" id="wp_debug_oauth" value="1" <?php jd_checkCheckbox('wp_debug_oauth')?> />
				<label for="wp_debug_oauth"><?php _e("Get Debugging Data for OAuth Connection", 'wp-to-twitter'); ?></label>
			</p>			
		</fieldset>
		<div>
		<input type="hidden" name="submit-type" value="advanced" />
		</div>
	<input type="submit" name="submit" value="<?php _e("Save Advanced WP->Twitter Options", 'wp-to-twitter'); ?>" class="button-primary" />	
	</div>
	</form>
</div>
</div>
</div>
<div class="ui-sortable meta-box-sortables">
<?php if ( isset( $_POST['submit-type']) && $_POST['submit-type']=='setcategories') { ?>
<div class="postbox">
<?php } else { ?>
<div class="postbox closed">
<?php } ?>

	<div class="handlediv" title="Click to toggle"><br/></div>
	<h3><?php _e('Limit Updating Categories','wp-to-twitter'); ?></h3>
	<div class="inside">
		<br class="clear" />
		<p>
		<?php _e('Select which blog categories will be Tweeted. ','wp-to-twitter'); ?>
<?php
if ( get_option('limit_categories') == '0' ) {
	_e('<em>Category limits are disabled.</em>','wp-to-twitter');
} 
?>
		</p>
<?php jd_list_categories(); ?>

	</div>
	</div>
	</div>

	<form method="post" action="">
	<fieldset>
	<input type="hidden" name="submit-type" value="check-support" />
		<p>
		<input type="submit" name="submit" value="<?php _e('Check Support','wp-to-twitter'); ?>" class="button-primary" /> <small><?php _e('Check whether your server supports <a href="http://www.joedolson.com/articles/wp-to-twitter/">WP to Twitter\'s</a> queries to the Twitter and URL shortening APIs. This test will send a status update to Twitter and shorten a URL using your selected methods.','wp-to-twitter'); ?></small>
		</p>
	</fieldset>
	</form>	
</div>
</div>
<?php global $wp_version; ?>
<script type="text/javascript">
//<![CDATA[
jQuery('.postbox h3').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); });
jQuery('.postbox .handlediv').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); });
jQuery('.postbox.close-me').each(function() { jQuery(this).addClass("closed"); });
//]]>
</script>