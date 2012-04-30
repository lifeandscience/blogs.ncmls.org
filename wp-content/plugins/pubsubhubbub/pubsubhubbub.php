<?php
/*
Plugin Name: PubSubHubbub
Plugin URI: http://code.google.com/p/pubsubhubbub/
Description: A better way to tell the world when your blog is updated.
Version: 1.6
Author: Josh Fraser, Matthias Pfefferle
Author Email: josh@eventvue.com
Author URI: http://wordpress.org/extend/plugins/pubsubhubbub/
*/

include("publisher.php");
include("subscriber.php");

// the ability for other plugins to hook into the PuSH code based on a
// fix by Stephen Paul Weber (http://singpolyma.net)
function pshb_publish_to_hub($feed_urls)  {
  // remove dups (ie. they all point to feedburner)
  $feed_urls = array_unique($feed_urls);
  // get the list of hubs
  $hub_urls = pshb_get_pubsub_endpoints();
  // loop through each hub
  foreach ($hub_urls as $hub_url) {
    $p = new PshbPublisher($hub_url);
    // publish the update to each hub
    if (!$p->publish_update($feed_urls)) {
      // TODO: add better error handling here
    }
  }
}

// subscribe to a feed
// NOTE! THIS IS BETA STATE
function pshb_subscribe($url) {
  try {
    $s = new PshbSubscriber(site_url("/?pubsubhubbub=endpoint"));
    $s->find_hub($url);

    $subscriptions = get_option('pubsub_subscribe');
    $subscriptions[] = $s->get_topic_url();
    update_option('pubsub_subscribe', array_unique($subscriptions));

    if ($s->subscribe($s->get_topic_url()) !== false) {
      return true;
    }
  } catch (Exception $e) {
    return $e->getMessage();
  }

  return false;
}

// unsubscribe from a feed
// NOTE! THIS IS BETA STATE
function pshb_unsubscribe($url) {
  try {
    $s = new PshbSubscriber(site_url("/?pubsubhubbub=endpoint"));
    $s->find_hub($url);

    $to_unsubscribe = get_option('pubsub_unsubscribe');
    $to_unsubscribe[] = $s->get_topic_url();
    update_option('pubsub_unsubscribe', array_unique($to_unsubscribe));

    if ($s->unsubscribe($s->get_topic_url()) !== false) {
      return true;
    }
  } catch (Exception $e) {
    return $e->getMessage();
  }

  return false;
}

// function that is called whenever a new post is published
function pshb_publish_post($post_id) {
  // we want to notify the hub for every feed
  $feed_urls = array();
  $feed_urls[] = get_bloginfo('atom_url');
  $feed_urls[] = get_bloginfo('rss_url');
  $feed_urls[] = get_bloginfo('rdf_url');
  $feed_urls[] = get_bloginfo('rss2_url');
  // customize default feeds
  $feed_urls   = apply_filters('pshb_feed_urls', $feed_urls);

  pshb_publish_to_hub($feed_urls);

  return $post_id;
}

// function that is called whenever a new comment is published
function pshb_publish_comment($comment_id) {
  // we want to notify the hub for every feed
  $feed_urls = array();
  $feed_urls[] = get_bloginfo('comments_atom_url');
  $feed_urls[] = get_bloginfo('comments_rss2_url');
  // customize default feeds
  $feed_urls   = apply_filters('pshb_comment_feed_urls', $feed_urls);

  pshb_publish_to_hub($feed_urls);

  return $comment_id;
}

function pshb_add_atom_link_tag() {
  $hub_urls = pshb_get_pubsub_endpoints();
  foreach ($hub_urls as $hub_url) {
    echo '<link rel="hub" href="'.$hub_url.'" />';
  }
}

function pshb_add_rss_link_tag() {
  $hub_urls = pshb_get_pubsub_endpoints();
  foreach ($hub_urls as $hub_url) {
    echo '<atom:link rel="hub" href="'.$hub_url.'"/>';
  }
}

function pshb_add_rdf_ns_link() {
  echo 'xmlns:atom="http://www.w3.org/2005/Atom"';
}

// hack to add the atom definition to the RSS feed
// start capturing the feed output.  this is run at priority 9 (before output)
function pshb_start_rss_link_tag() {
  ob_start();
}

// this is run at priority 11 (after output)
// add in the xmlns atom definition link
function pshb_end_rss_link_tag() {
  $feed = ob_get_clean();
  $pattern = '/<rss version="(.+)">/i';
  $replacement = '<rss version="$1" xmlns:atom="http://www.w3.org/2005/Atom">';
  // change <rss version="X.XX"> to <rss version="X.XX" xmlns:atom="http://www.w3.org/2005/Atom">
  echo preg_replace($pattern, $replacement, $feed);
}

// add a link to our settings page in the WP menu
function pshb_add_plugin_menu() {
  add_options_page('PubSubHubbub Settings', 'PubSubHubbub', 'administrator', __FILE__, 'pshb_add_settings_page');
}

// get the endpoints from the wordpress options table
// valid parameters are "publish" or "subscribe"
function pshb_get_pubsub_endpoints() {
  $endpoints = get_option('pubsub_endpoints');
  $hub_urls = explode("\n",$endpoints);

  // if no values have been set, revert to the defaults (pubsubhubbub on app engine & superfeedr)
  if (!$endpoints) {
    $hub_urls[] = "http://pubsubhubbub.appspot.com";
    $hub_urls[] = "http://superfeedr.com/hubbub";
  }

  // clean out any blank values
  foreach ($hub_urls as $key => $value) {
    if (is_null($value) || $value=="") {
      unset($hub_urls[$key]);
    } else {
      $hub_urls[$key] = trim($hub_urls[$key]);
    }
  }

  return $hub_urls;
}

// write the content for our settings page that allows you to define your endpoints
function pshb_add_settings_page() { ?>
  <div class="wrap">
  <h2>Define custom hubs</h2>

  <form method="post" action="options.php">
  <?php //wp_nonce_field('update-options'); ?>
  <!-- starting -->
  <?php settings_fields('my_settings_group'); ?>
  <?php do_settings_sections('my_settings_section'); ?>
  <!-- ending -->

  <?php
  // load the existing pubsub endpoint list from the wordpress options table
  $pubsub_endpoints = trim(implode("\n",pshb_get_pubsub_endpoints()),"\n");
  ?>

  <table class="form-table">

  <tr valign="top">
  <th scope="row">Hubs (one per line)</th>
  <td><textarea name="pubsub_endpoints" style='width:600px;height:100px'><?php echo $pubsub_endpoints; ?></textarea></td>
  </tr>

  </table>

  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="page_options" value="pubsub_endpoints" />

  <p class="submit">
  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>

  </form>

  <br /><br />
  <div style='background-color:#FFFEEB;border:1px solid #CCCCCC;padding:12px'>
    <strong>Thanks for using PubSubHubbub!</strong><br />
    Visit these links to learn more about PubSubHubbub and the author of this plugin:<br />
    <ul>
      <li><a href='http://www.onlineaspect.com'>Subscribe to Online Aspect</a></li>
      <li><a href='http://www.twitter.com/joshfraser'>Follow Josh Fraser on twitter</a></li>
      <li><a href='http://code.google.com/p/pubsubhubbub/'>Learn more about the PubSubHubbub protocol</a></li>
    </ul>
  </div>
</div>

<?php }

// add a settings link next to deactive / edit
function pshb_add_settings_link( $links, $file ) {
  if( $file == 'pubsubhubbub/pubsubhubbub.php' && function_exists( "admin_url" ) ) {
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=pubsubhubbub/pubsubhubbub' ) . '">' . __('Settings') . '</a>';
    array_unshift( $links, $settings_link ); // before other links
  }
  return $links;
}

// adds some query vars
function pshb_query_var($vars) {
  $vars[] = 'hub_mode';
  $vars[] = 'hub_challenge';
  $vars[] = 'hub_topic';
  $vars[] = 'hub_url';
  $vars[] = 'pubsubhubbub';
  return $vars;
}

// parses the request
function pshb_parse_request() {
  global $wp_query, $wp;
  $query_vars = $wp->query_vars;

  // handle (un)subscribe requests
  if (array_key_exists('hub_mode', $query_vars)
      && in_array($query_vars['hub_mode'], array("subscribe", "unsubscribe"))
      && isset($query_vars['hub_challenge'])) {
    $list = get_option('pubsub_'.$query_vars['hub_mode']);
    if (is_array($list) && in_array($query_vars['hub_topic'], $list)) {
      // remove urls from option lists when unsubscribing
      if ($query_vars['hub_mode'] == "unsubscribe") {
        pshb_remove_from_option($query_vars['hub_topic'], "unsubscribe");
        pshb_remove_from_option($query_vars['hub_topic'], "subscribe");
      }
      echo $query_vars['hub_challenge'];
      exit;
    }
  // handle pushes
  } elseif (array_key_exists('pubsubhubbub', $query_vars)
            && $query_vars['pubsubhubbub'] == "endpoint"
            && $request_body = @file_get_contents('php://input')) {
    do_action('pshb_push', $request_body);
    exit;
  }
}

// remove something from the option list
function pshb_remove_from_option($url, $option) {
  if (!in_array($option, array("subscribe", "unsubscribe"))) {
    return false;
  }

  $list = get_option('pubsub_'.$option);
  $key = array_search($url, $list);
  unset($list[$key]);
  update_option('pubsub_'.$option, $list);
}

// adds link headers as defined in the curren v0.4 draft
// https://github.com/pubsubhubbub/PubSubHubbub/issues/2
function pshb_template_redirect() {
  if ((is_comment_feed() && !is_singular())
      || (is_feed() && !is_comment_feed() && !is_archive())) {
    $hub_urls = pshb_get_pubsub_endpoints();
    foreach ($hub_urls as $hub_url) {
      header('Link: <'.$hub_url.'>; rel=hub');
    }
    header('Link: <'.( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'>; rel=self');
  }
}

// attach the handler that gets called every time you publish a post
add_action('publish_post', 'pshb_publish_post');
// attach the handler that gets called every time you get a comment
add_action('comment_post', 'pshb_publish_comment');
// add the link to our settings page in the WP menu structure
add_action('admin_menu', 'pshb_add_plugin_menu');

// keep WPMU happy
add_action('admin_init', 'pshb_register_my_settings');
function pshb_register_my_settings() {
  register_setting('my_settings_group','pubsub_endpoints');
}

// add the link tag that points to the hub in the header of our template...

// to our atom feed
add_action('atom_head', 'pshb_add_atom_link_tag');
add_action('comments_atom_head', 'pshb_add_atom_link_tag');
// to our RSS 0.92 feed (requires a bit of a hack to include the ATOM namespace definition)
add_action('do_feed_rss', 'pshb_start_rss_link_tag', 9); // run before output
add_action('do_feed_rss', 'pshb_end_rss_link_tag', 11); // run after output
add_action('rss_head', 'pshb_add_rss_link_tag');
// to our RDF / RSS 1 feed
add_action('rdf_ns', 'pshb_add_rdf_ns_link');
add_action('rdf_header', 'pshb_add_rss_link_tag');
// to our RSS 2 feed
add_action('rss2_head', 'pshb_add_rss_link_tag');
add_action('commentsrss2_head', 'pshb_add_rss_link_tag');
// to our main HTML header -- not sure if we want to include this long-term or not.
add_action('wp_head', 'pshb_add_atom_link_tag');
add_action('template_redirect', 'pshb_template_redirect');

add_filter('plugin_action_links', 'pshb_add_settings_link', 10, 2);
add_filter('query_vars', 'pshb_query_var');
add_action('parse_request', 'pshb_parse_request');

/**
 * beeing backwards compatible
 * @deprecated
 */
function publish_to_hub($deprecated = null, $feed_urls)  {
  pshb_publish_to_hub($feed_urls);
}