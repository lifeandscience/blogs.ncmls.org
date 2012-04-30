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
						delete_option( 'wpt_curl_error' );
					} else if ( $connection->http_code == 0 ) {
						$error_information = __("WP to Twitter was unable to make a connection to Twitter. Verify with your host that your server has cURL support and <code>curl_exec()</code> commands are enabled.",'wp-to-twitter'); 
						update_option( 'wpt_curl_error',$error_information);
					} else {
						$error_information = array("http_code"=>$connection->http_code,"status"=>$connection->http_header['status']);
						$error_code = __("Twitter response: http_code $error_information[http_code] - $error_information[status]",'wp-to-twitter');
						update_option( 'wpt_curl_error',$error_code );
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
			if ( $message == 'failed' && ( time() < strtotime( $connection->http_header['date'] )-300 || time() > strtotime( $connection->http_header['date'] )+300 ) ) {
				$message = 'nosync';
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
echo '<div class="postbox">';
echo '<div class="handlediv" title="Click to toggle"><br/></div>';
$server_time = date( DATE_COOKIE );

$response = wp_remote_get( "https://api.twitter.com/1/");
if ( is_wp_error( $response ) ) {
	$date = __('There was an error querying Twitter\'s servers.','wp-to-twitter');
} else {
	$date = date( DATE_COOKIE, strtotime($response['headers']['date']) );
}

	if ( !wtt_oauth_test() ) {
		print('	
			<h3>'.__('Connect to Twitter','wp-to-twitter').'</h3>
			<div class="inside">
			<p>'.__('Your server time:','wp-to-twitter').' <code>'.$server_time.'</code><br />'.__("Twitter's server time:").' <code>'.$date.'</code>.<br />'.__( 'If these times are not within 5 minutes of each other, your server will not be able to connect to Twitter.','wp-to-twitter').'</p>
			<p>'.__('The process to set up OAuth authentication for your web site is needlessly laborious. However, this is the method available. Note that you will not add your Twitter username or password to WP to Twitter; they are not used in OAuth authentication.', 'wp-to-twitter').'</p> 
			<form action="" method="post">
				<fieldset class="options">
					<h4>'.__('1. Register this site as an application on ', 'wp-to-twitter') . '<a href="http://dev.twitter.com/apps/new" target="_blank">'.__('Twitter\'s application registration page','wp-to-twitter').'</a></h4>
						<ul>
						<li>'.__('If you\'re not currently logged in, log-in with the Twitter username and password which you want associated with this site' , 'wp-to-twitter').'</li>
						<li>'.__('Your Application\'s Name will be what shows up after "via" in your twitter stream. Your application name cannot include the word "Twitter." Use the name of your web site.' , 'wp-to-twitter').'</li>
						<li>'.__('Your Application Description can be whatever you want.','wp-to-twitter').'</li>
						<li>'.__('The WebSite and Callback URL should be ' , 'wp-to-twitter').'<strong>'.  get_bloginfo( 'url' ) .'</strong></li>					
						</ul>
					<p>'.__('Agree to the Developer Rules of the Road and continue.','wp-to-twitter').'</p>
					<h4>'.__('2. Switch to "Settings" tab in Twitter apps','wp-to-twitter').'</h4>
						<ul>
						<li>'.__('Select "Read and Write" for the Application Type' , 'wp-to-twitter').'</li>
						<li>'.__('Update the application settings' , 'wp-to-twitter').'</li>
						<li>'.__('Return to Details tab and create your access token. Refresh page to view your access tokens.','wp-to-twitter').'</li>		
						</ul>					
					<p><em>'.__('Once you have registered your site as an application, you will be provided with four keys.' , 'wp-to-twitter').'</em></p>
					<h4>'.__('3. Copy and paste your consumer key and consumer secret into the fields below' , 'wp-to-twitter').'</h4>
					<div class="tokens">
					<p>
						<label for="wtt_app_consumer_key">'.__('Twitter Consumer Key', 'wp-to-twitter').'</label>
						<input type="text" size="45" name="wtt_app_consumer_key" id="wtt_app_consumer_key" value="'.esc_attr( get_option('app_consumer_key') ).'" />
					</p>
					<p>
						<label for="wtt_app_consumer_secret">'.__('Twitter Consumer Secret', 'wp-to-twitter').'</label>
						<input type="text" size="45" name="wtt_app_consumer_secret" id="wtt_app_consumer_secret" value="'.esc_attr( get_option('app_consumer_secret') ).'" />
					</p>
					</div>
					<h4>'.__('4. Copy and paste your Access Token and Access Token Secret into the fields below','wp-to-twitter').'</h4>
					<p>'.__('If the Access level reported for your Access Token is not "Read and write", you need to go back to step 2 and generate a new Access Token.','wp-to-twitter').'</p>
					<div class="tokens">
					<p>
						<label for="wtt_oauth_token">'.__('Access Token', 'wp-to-twitter').'</label>
						<input type="text" size="45" name="wtt_oauth_token" id="wtt_oauth_token" value="'.esc_attr( get_option('oauth_token') ).'" />
					</p>
					<p>
						<label for="wtt_oauth_token_secret">'.__('Access Token Secret', 'wp-to-twitter').'</label>
						<input type="text" size="45" name="wtt_oauth_token_secret" id="wtt_oauth_token_secret" value="'.esc_attr( get_option('oauth_token_secret') ).'" />
					</p>
					</div>
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
			<form action="" method="post">
				<div id="wtt_authentication_display">
				<p>'.__('<strong>Troubleshooting tip:</strong> Connected, but getting a notice that your Authentication credentials are missing or incorrect? Check whether your Access token has read and write permission. If not, you\'ll need to create a new token.','wp-to-twitter').'</p>
					<fieldset class="options">
					<ul>
						<li><strong class="auth_label">'.__('Twitter Username ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('wtt_twitter_username').'</code></li>
						<li><strong class="auth_label">'.__('Consumer Key ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('app_consumer_key').'</code></li>
						<li><strong class="auth_label">'.__('Consumer Secret ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('app_consumer_secret').'</code></li>
						<li><strong class="auth_label">'.__('Access Token ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('oauth_token').'</code></li>
						<li><strong class="auth_label">'.__('Access Token Secret ', 'wp-to-twitter').'</strong> <code class="auth_code">'.get_option('oauth_token_secret').'</code></li>
					</ul>
					</fieldset>
					<div>
					<input type="submit" name="submit" class="button-primary" value="'.__('Disconnect Your WordPress and Twitter Account', 'wp-to-twitter').'" />
					<input type="hidden" name="oauth_settings" value="wtt_twitter_disconnect" class="hidden" style="display: none;" />
					'.wp_nonce_field('wtt_twitter_disconnect', '_wpnonce', true, false).wp_referer_field(false).'
					</div>
				</div>		
			</form>
			<p>'.__('Your server time:','wp-to-twitter').' <code>'.$server_time.'</code>.<br />'.__('Twitter\'s current server time: ','wp-to-twitter').'<code>'.$date.'</code>.</p><p> '.__( 'If these times are not within 5 minutes of each other, your server could lose its connection with Twitter.','wp-to-twitter').'</p></div>');
	}
echo "</div>";
echo "</div>";
}