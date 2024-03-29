=== Plugin Name ===
Contributors: joshfraz
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5426516
Tags: pubsubhubbub
Requires at least: 2.5
Tested up to: 2.9.1
Stable tag: /trunk/

A better way to tell the world when your blog is updated.

== Description ==

This [PubSubHubbub](http://code.google.com/p/pubsubhubbub/ "PubSubHubbub") plugin is a simple way to let people know in real-time when your blog is updated.  PubSubHubbub is quickly gaining adoption and is already being used by Google Reader, Google Alerts, FriendFeed and more. 

This plugin:
 
* Supports multi-user installations (NEW!)
* Supports multiple hubs
* Supports all of the feed formats used by WordPress, not just ATOM and RSS2
* Announces which hubs you are using by adding `<link rel="hub" ...>` declarations to your template header and ATOM feed
* Adds `<atom:link rel="hub" ...>` to your RSS feeds along with the necessary XMLNS declaration for RSS 0.92/1.0

By default this plugin will ping the following hubs:

* [Demo hub on Google App Engine](http://pubsubhubbub.appspot.com "Demo hub on Google App Engine")
* [SuperFeedr](http://superfeedr.com/hubbub "SuperFeedr")

Please contact me if you operate a hub that you would like to be included as a default option.

== Installation ==

1. Upload the `pubsubhubbub` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select custom hubs under your PubSubHubbub Settings (optional)

Note: PHP 5.0 or better is required.

== Frequently Asked Questions ==

= Where can I learn more about the PubSubHubbub (PuSH) protocol? =

You can visit [PubSubHubbb on Google Code](http://code.google.com/p/pubsubhubbub/ "PubSubHubbb on Google Code")

= Where can I learn more about the author of this plugin? =

You can learn more about Josh Fraser at [Online Aspect](http://www.onlineaspect.com "Online Aspect") or follow [@joshfraser on twitter](http://www.twitter.com/joshfraser "Josh Fraser on Twitter")

= Does this plugin work with MU? =

Multi-user support was added in version 1.3

= Does this plugin work with PHP 4.x? =

Nope.  Sorry.  For now you must have PHP 5.0 or better.

= Blog posts don't show up right away in Google Reader. Is it broken? =

Google Reader currently supports PuSH for shared items, but not general subscriptions.  Hopefully they will add that functionality soon (I hear they're working on it).  In the meantime, you can check that everything is working correctly by publishing a post and then checking the status at http://pubsubhubbub.appspot.com/topic-details?hub.url=URL-OF-YOUR-FEED

= Got another question that isn't covered here? =

Visit [my contact page](http://onlineaspect.com/contact/ "Contact Josh Fraser") to see various ways to get in touch with me.

== Screenshots ==

1. The PubSubHubbub Settings page allows you to define which hubs you want to use
