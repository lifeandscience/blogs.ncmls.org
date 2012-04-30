=== Blogger Image Import ===
Contributors: poco
Donate link: http://notions.okuda.ca
Tags: google, blogger, image, import
Requires at least: 2.0.2
Tested up to: 3.3.1
Stable tag: trunk

A plugin that copies blogger hosted images to your local server and updates the links within the posts.

== Description ==

This plugin searches your blog posts for images that are hosted on Blogger (blogger.com or blogspot.com),
downloads them to your server, and updates your posts to refer to the downloaded versions.

== Installation ==

1. Install directly from the Wordpress dashboard

OR

1. Unzip the plugin to the `/wp-content/plugins/blogger-image-import` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Access it from the Options screen under “Blogger Image Import”.
1. Select the maximum number of images you wish to process.
1. Click on “Test Import” to do a test pass or “Start Import” to permanently apply the changes.

== Frequently Asked Questions ==

= How do I fix timeout errors? =

Check with your hosting provider about making longer timeouts for server scripts.  The plugin does its best to set the timeouts as long as possible.

= Does this plugin support multiple domains =

Yes.  The "Image Host Domains" setting takes a comma ',' separated list of domains from which to download.
This should work with linked images from outside blogger, but has not been tested.

== Changelog ==

= 2.1 =
* Fixed what I think was an error in the regex (can't explain why it was the way it was, but it works better now) Confirmed by Morgan (http://notions.okuda.ca/wordpress-plugins/blogger-image-import/#comment-201663)
* Added "jpeg" to the list of valid extensions.  Verified by Morgan (http://notions.okuda.ca/wordpress-plugins/blogger-image-import/#comment-201663)

= 2.0 =
* Importing changes from various submissions including Jondor (http://www.thedwarfers.net/otherstuff/blogger-image-import-edit.zip) and JT (http://notions.okuda.ca/wordpress-plugins/blogger-image-import/#comment-107213)
* Added support for dynamic image domains
* Fixed up to work with most recent versions of Blogger and Wordpress.
* Fixed a bug with files with spaces in their names not getting downloaded successfully (the resulting files contained the 404 error instead of the image).  Resolved by replacing spaces in the URL with %20.
* Added support for multiple image formats (png, gif, tif) though the link extension must match the <img> tag extension.
* Added support for multiple domains (blogger.com and blogspot.com by default) since Blogger has used at least two in the past.

= 1.2 =
* Added support for images with spaces in their names

= 0.2 =
* Initial version
