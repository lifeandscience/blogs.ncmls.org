<?php
// function to test credentials		
function wtt_oauth_test() {
	return ( wtt_oauth_credentials_to_hash() == get_option('wtt_oauth_hash') );
}	
// function to make connection		
function wtt_oauth_connection() {
$ack = get_option('app_consumer_key');
$acs = get_option('app_consumer_secret');
$ot = get_option('oauth_token');
$ots = get_option('oauth_token_secret');

if ( !empty( $ack ) && !empty( $acs ) && !empty( $ot ) && !empty( $ots ) ) {	
	require_once( WP_PLUGIN_DIR . '/wp-to-twitter/jd_twitterOAuth.php' );
	$connection = new jd_TwitterOAuth(
		get_option('app_consumer_key'), 
		get_option('app_consumer_secret'), 
		get_option('oauth_token'), 
		get_option('oauth_token_secret')
	);
	$connection->useragent = 'WP to Twitter http://www.joedolson.com/articles/wp-to-twitter';
	return $connection;
}
else {
	return false;
}
}	

// convert credentials to md5 hash
function wtt_oauth_credentials_to_hash() {
	$hash = md5(get_option('app_consumer_key').get_option('app_consumer_secret').get_option('oauth_token').get_option('oauth_token_secret'));
	return $hash;		
}
// response to settings updates
function jd_update_oauth_settings() {
switch ( $_POST['oauth_settings'] ) {
	case 'wtt_oauth_test':
			if (!wp_verify_nonce($_POST['_wpnonce'], 'wtt_oauth_test')) {
				wp_die('Oops, please try again.');
			}
			$auth_test = false;
			if ( !empty($_POST['wtt_app_consumer_key'])
				&& !empty($_POST['wtt_app_consumer_secret'])
				&& !empty($_POST['wtt_oauth_token'])
				&& !empty($_POST['wtt_oauth_token_secret'])
			) {
				update_option('app_consumer_key',trim($_POST['wtt_app_consumer_key']));
				update_option('app_consumer_secret',trim($_POST['wtt_app_consumer_secret']));
				update_option('oauth_token',trim($_POST['wtt_oauth_token']));
				update_option('oauth_token_secret',trim($_POST['wtt_oauth_token_secret']));
				
				$message = 'fail';
				
				if ($connection = wtt_oauth_connection()) {
					$data = $connection->get('account/verify_credentials');
					if ($connection->http_code == '200') {
						$decode = json_decode($data);
						update_option('wtt_twitter_username', stripslashes($decode->screen_name));
						$oauth_hash = wtt_oauth_credentials_to_hash();
						update_option('wtt_oauth_hash', $oauth_hash);
						$message = 'success';
					} else {
						$error_information = array("http_code"=>$connection->http_code,"status"=>$connection->http_header['status']);
					}
					if ( get_option('wp_debug_oauth') == '1' ) {
					echo "<pre><strong>Summary Connection Response:</strong><br />";
					print_r($error_information);
					echo "<br /><strong>Account Verification Data:</strong><br />";
					print_r($data);
					echo "<br /><strong>Full Connection Response:</strong><br />";
					print_r($connection);
					echo "</pre>";										
					}
				}
			}
			return $message;
		break;
		case 'wtt_twitter_disconnect':
			if (!wp_verify_nonce($_POST['_wpnonce'], 'wtt_twitter_disconnect')) {
				wp_die('Oops, please try again.');
			}
			
			update_option('app_consumer_key', '');
			update_option('app_consumer_secret', '');
			update_option('oauth_token', '');
			update_option('oauth_token_secret', '');
			$message = "cleared";
			return $message;
		break;
	}
}

// connect or disconnect form
function wtt_connect_oauth() {
echo '<div class="ui-sortable meta-box-sortables">';
if ( !wtt_oauth_test() ) { 
echo '<div class="postbox">';
} else { 
echo '<div class="postbox closed">';
}
echo '<div class="handlediv" title="Click to toggle"><br/></div>';

	if ( !wtt_oauth_test() ) {
		print('	
			<h3>'.__('Connect to Twitter','wp-to-twitter').'</h3>
			<div class="inside">
			<br class="clear" />			
			<p>'.__('The process to set up OAuth authentication for your web site is needlessly laborious. It is also confusing. However, this is the only method available from Twitter at this time. Note that you will not, at any time, enter you Twitter username or password into WP to Twitter; they are not used in OAuth authentication.', 'wp-to-twitter').'</p> 
			<form action="" method="post">
				<fieldset class="options">
					<h4>'.__('1. Register this site as an application on ', 'wp-to-twitter') . '<a href="http://dev.twitter.com/apps/new" target="_blank">'.__('Twitter\'s application registration page','wp-to-twitter').'</a></h4>
						<ul>
						<li>'.__('If you\'re not currently logged in, use the Twitter username and password which you want associated with this site' , 'wp-to-twitter').'</li>
						<li>'.__('Your Application\'s Name will be what shows up after "via" in your twitter stream; previously, "WP to Twitter." Your application name cannot include the word "Twitter." I suggest using the name of your web site.' , 'wp-to-twitter').'</li>
						<li>'.__('Your Application Description can be whatever you want.','wp-to-twitter').'</li>
						<li>'.__('Application Type should be set on ' , 'wp-to-twitter').'<strong>'.__('Browser' , 'wp-to-twitter').'</strong></li>
						<li>'.__('The Callback URL should be ' , 'wp-to-twitter').'<strong>'.  get_bloginfo( 'url' ) .'</strong></li>
						<li>'.__('Default Access type must be set to ' , 'wp-to-twitter').'<strong>'.__('Read &amp; Write' , 'wp-to-twitter').'</strong> '.__('(this is NOT the default)' , 'wp-to-twitter').'</li>
						</ul>
					<p><em>'.__('Once you have registered your site as an application, you will be provided with a consumer key and a consumer secret.' , 'wp-to-twitter').'</em></p>
					<h4>'.__('2. Copy and paste your consumer key and consumer secret into the fields below' , 'wp-to-twitter').'</h4>
				
					<p>
						<label for="wtt_app_consumer_key">'.__('Twitter Consumer Key', 'wp-to-twitter').'</label>
						<input type="text" size="25" name="wtt_app_consumer_key" id="wtt_app_consumer_key" value="'.esc_attr( get_option('app_consumer_key') ).'" autocomplete="off">
					</p>
					<p>
						<label for="wtt_app_consumer_secret">'.__('Twitter Consumer Secret', 'wp-to-twitter').'</label>
						<input type="text" size="25" name="wtt_app_consumer_secret" id="wtt_app_consumer_secret" value="'.esc_attr( get_option('app_consumer_secret') ).'" autocomplete="off">
					</p>
					<h4>'.__('3. Copy and paste your Access Token and Access Token Secret into the fields below','wp-to-twitter').'</h4>
					<p>'.__('On the right hand side of your new application page at Twitter, click on \'My Access Token\'.','wp-to-twitter').'</p>
					<p>
						<label for="wtt_oauth_token">'.__('Access Token', 'wp-to-twitter').'</label>
						<input type="text" size="25" name="wtt_oauth_token" id="wtt_oauth_token" value="'.esc_attr( get_option('oauth_token') ).'" autocomplete="off">
					</p>
					<p>
						<label for="wtt_oauth_token_secret">'.__('Access Token Secret', 'wp-to-twitter').'</label>
						<input type="text" size="25" name="wtt_oauth_token_secret" id="wtt_oauth_token_secret" value="'.esc_attr( get_option('oauth_token_secret') ).'" autocomplete="off">
					</p>
				</fieldset>
				<p class="submit">
					<input type="submit" name="submit" class="button-primary" value="'.__('Connect to Twitter', 'wp-to-twitter').'" />
				</p>
				<input type="hidden" name="oauth_settings" value="wtt_oauth_test" class="hidden" style="display: none;" />
				'.wp_nonce_field('wtt_oauth_test', '_wpnonce', true, false).wp_referer_field(false).'
			</form>
			</div>	
				');
	}
	else if ( wtt_oauth_test() ) {
		print('	
			<h3>'.__('Disconnect from Twitter','wp-to-twitter').'</h3>
		
			<div class="inside">
			<br class="clear" />			
			<form action="" method="post">
				<div id="wtt_authentication_display">
					<fieldset class="options">
						<p><strong class="auth_label">'.__('Twitter Username ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('wtt_twitter_username').'</code></p>
						<p><strong class="auth_label">'.__('Consumer Key ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('app_consumer_key').'</code></p>
						<p><strong class="auth_label">'.__('Consumer Secret ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('app_consumer_secret').'</code></p>
						<p><strong class="auth_label">'.__('Access Token ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('oauth_token').'</code></p>
						<p><strong class="auth_label">'.__('Access Token Secret ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('oauth_token_secret').'</code></p>
					</fieldset>
					<p class="submit">
					<input type="submit" name="submit" class="button-primary" value="'.__('Disconnect Your WordPress and Twitter Account', 'wp-to-twitter').'" />
					</p>
					<input type="hidden" name="oauth_settings" value="wtt_twitter_disconnect" class="hidden" style="display: none;" />
					'.wp_nonce_field('wtt_twitter_disconnect', '_wpnonce', true, false).wp_referer_field(false).' 
				</div>		
			</form>
			</div>
			');
	}
echo "</div>";
echo "</div>";
}
?>