<?php
/*
Plugin Name: Blogger Image Import
Plugin URI: http://notions.okuda.ca/wordpress-plugins/blogger-image-import/
Description: A plugin that copies blogger hosted images to your local server and updates the links within the posts.
Author: Poco
Version: 1.2
Author URI: http://notions.okuda.ca
*/


// Returns an array containing the current upload directory's path and url, or an error message.
// Code copied from wp_upload_dir in wp 2.0.1
function ko_blogger_image_import_upload_dir($source_url) {
	$siteurl = get_settings('siteurl');
	$source_url = str_replace('s1600-h','s1600',$source_url);	// MODIFIED: some blogger images are actually html pages; need to get just image for import to work
	//prepend ABSPATH to $dir and $siteurl to $url if they're not already there
	$path = str_replace(ABSPATH, '', trim(get_settings('upload_path')));
	$dir = ABSPATH . $path;
	$url = trailingslashit($siteurl) . $path;

	if ( $dir == ABSPATH ) { //the option was empty
		$dir = ABSPATH . 'wp-content/uploads';
	}

	if ( defined('UPLOADS') ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit($siteurl) . UPLOADS;
	}

	// Put all blogger imported files into the blogger directory.
	$dir = $dir . "/blogger";
	$url = $url . "/blogger";

	// Use the original path to help locate the new file
	$urlchar = '[a-z0-9:?=&@/._-]+?'; 
	$matchcount = preg_match ("|".$urlchar."blogspot.com(/.+)/|i", $source_url, $matches); // MODIFIED: changed blogger.com to blogspot.com
	if ($matchcount > 0)
	{
		$dir = $dir . $matches[1];
		$url = $url . $matches[1];
	}

		
// 	if ( get_settings('uploads_use_yearmonth_folders')) {
// 		// Generate the yearly and monthly dirs
// 		$time = current_time( 'mysql' );
// 		$y = substr( $time, 0, 4 );
// 		$m = substr( $time, 5, 2 );
// 		$dir = $dir . "/$y/$m";
// 		$url = $url . "/$y/$m";
// 	}

	// Make sure we have an uploads dir
	if ( ! wp_mkdir_p( $dir ) ) {
		$message = sprintf(__('Unable to create directory %s. Is its parent directory writable by the server?'), $dir);
		return array('error' => $message);
	}

    $uploads = array('path' => $dir, 'url' => $url, 'error' => false);
	return apply_filters('upload_dir', $uploads);
}

function ko_blogger_image_import_construct_image_post($file, $thumb_file, $post)
{

	// We should make the description and/or title something consistent so that we
	// can verify if a post has already been added to the DB.
	// For now the description is the old URL.
	$descr = $file['original_url'];
	
	/////////////////////////////////////////////////////////////////////////////////////////////////
	// Most of the following code was stolen from inline-uploading.php with minimum modifications. //
	/////////////////////////////////////////////////////////////////////////////////////////////////

	if ( isset($file['error']) )
		die($file['error'] . '<br />\n');
	
	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$filename = basename($file);
	
	// Construct the attachment array
	$attachment = array(
		'post_title' => $imgtitle ? $imgtitle : $filename,
		'post_content' => $descr,
		'post_status' => 'attachment',
		'post_parent' => $post,
		'post_mime_type' => $type,
		'guid' => $url
		);
	
	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post);
	
	if ( preg_match('!^image/!', $attachment['post_mime_type']) ) {
		// Generate the attachment's postmeta.
		$imagesize = getimagesize($file);
		$imagedata['width'] = $imagesize['0'];
		$imagedata['height'] = $imagesize['1'];
		list($uwidth, $uheight) = get_udims($imagedata['width'], $imagedata['height']);
		$imagedata['hwstring_small'] = "height='$uheight' width='$uwidth'";
		$imagedata['file'] = $file;
	
		add_post_meta($id, '_wp_attachment_metadata', $imagedata);

		// The thumbnails have already been created, so we just need to hook it all up here.	
		if ( $thumb_file ) {
			$thumb = $thumb_file['file'];

			if ( @file_exists($thumb) ) {
				$newdata = $imagedata;
				$newdata['thumb'] = basename($thumb);
				update_post_meta($id, '_wp_attachment_metadata', $newdata, $imagedata);
			} else {
				$error = $thumb;
			}
		}
	} else {
		add_post_meta($id, '_wp_attachment_metadata', array());
	}
}

// Returns true on success or false if there was an error
function ko_blogger_image_import_download_file($source, $dest)
{	
	$source_url = str_replace('s1600-h','s1600',$source_url);	// MODIFIED: some blogger images are actually html pages; need to get just image for import to work
	$ch = curl_init($source);
	$fp = fopen($dest, "w");
	
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	curl_exec($ch);

	$error = true;
	if (curl_errno($ch) != 0)
	{	
		$error = false;
	}
	
	curl_close($ch);
	fclose($fp);
	
	return $error;
}

// Calculate the destination name and download the file there.
// Similar to wp_handle_upload but from a URL instead of a temporary file
/* array */ function ko_blogger_image_import_handle_download($source_url, $dest_name, $applychanges = true, $force_unique = false)
{
	$source_url = str_replace('s1600-h','s1600',$source_url);	// MODIFIED: some blogger images are actually html pages; need to get just image for import to work
	$file = array('name' => $dest_name);
	$ext = 'jpg';
	$type = 'image/jpeg';
	
	////////////////////////////////////////////////////////////
	// Code stolen and hacked up from wp_handle_upload v2.0.1 //
	// What is left has been changed as little as possible    //
	// to allow for easy integration of future versions       //
	////////////////////////////////////////////////////////////

	// The default error handler.
	if (! function_exists('wp_handle_upload_error') ) {
		function wp_handle_upload_error(&$file, $message) {
			return array('error'=>$message);
		}
	}

	$upload_error_handler = 'wp_handle_upload_error';

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = ko_blogger_image_import_upload_dir($source_url) ) && false === $uploads['error'] ) )
		return $upload_error_handler($file, $uploads['error']);

	// Don't make the files unique unless we are asked to
	if ($force_unique)
	{
		// Increment the file number until we have a unique file to save in $dir.
		if ( isset($unique_filename_callback) && function_exists($unique_filename_callback) ) {
			$filename = $unique_filename_callback($uploads['path'], $file['name']);
		} else {
			$number = '';
			$filename = str_replace('#', '_', $file['name']);
			$filename = str_replace(array('\\', "'"), '', $filename);
			if ( empty($ext) )
				$ext = '';
			else
				$ext = ".$ext";
			while ( file_exists($uploads['path'] . "/$filename") ) {
				if ( '' == "$number$ext" )
					$filename = $filename . ++$number . $ext;
				else
					$filename = str_replace("$number$ext", ++$number . $ext, $filename);
			}
		}
	}
	else
	{
		$filename = $file['name'];
	}

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";

	// Compute the URL
	$url = $uploads['url'] . "/" . rawurlencode("$filename");

	// Don't copy the file if it already exists.
	if (file_exists($new_file))
	{
		echo "WARNING - ".$new_file." already exists on the server, it will not be copied.<br />\n";	
	}
	else
	{
		if ($applychanges)
		{
			//if ( false === @ move_uploaded_file($file['tmp_name'], $new_file) )
			if ( false === ko_blogger_image_import_download_file($source_url, $new_file))
				die(printf(__('The downloaded file could not be put in %s.'), $new_file));
	
			// Set correct file permissions
			$stat = stat(dirname($new_file));
			$perms = $stat['mode'] & 0000666;
			@ chmod($new_file, $perms);
	
			echo "Old File = " . $source_url . "<br />\n";
			echo "New File = " . $new_file   . "<br />\n";
			echo "New URL  = " . $url        . "<br />\n";
		}
		else
		{
			echo "Old File = " . $source_url . "<br />\n";
			echo "New File = " . $new_file   . "<br />\n";
			echo "New URL  = " . $url        . "<br />\n";
		}
	}
	
	return array('file' => $new_file, 'url' => $url, 'type' => $type, 'original_url' => $source_url);
}

function ko_blogger_image_import_process($maximports, $applychanges = true)
{
	global $wpdb, $tableposts;
	
	$now = gmdate("Y-m-d H:i:s",time());
	$words = $wpdb->get_results("SELECT ID, post_content FROM $tableposts WHERE post_status = 'publish' AND post_date < '$now'");
	$count = 0;
	$postcount = 0;

	echo "Importing a maximum of ".$maximports." images<br /><br />\n";
	
	//$word = $words[0];
	foreach ($words as $word)
	{
		$post = $word->post_content;

		// This is the guts of the regex.
		// This is what should match only .jpg files hosted on blogspot.com.
		$urlchar = '[^\s]+?'; 
		$innerT = $urlchar."blogspot.com".$urlchar."\.jpg"; // MODIFIED: changed blogger.com to blogspot.com
		
		$matchcount = preg_match_all ("/<a[^>]+href\=([\"'`])(".$innerT.")\\1(?:(?<!a)>)*<img[^>]*src\=([\"'`])(".$innerT.")\\3[^>]*>/i", $post, $matches, PREG_SET_ORDER);
		
		if ($matchcount > 0)
		{
			$post_content = $word->post_content;
			$ID = $word->ID;
			$found_match = false;

			echo "**** POST ".$ID." ****<br />\n";
			//$matches = array($matchcount);	
			//print_r($matches);
			foreach ($matches as $match)
			{
				
				$source_url = $match[2];
				$thumb_url = $match[4];

				// We will ignore the thumbnail filename given on the blogger server and create the name
				// ourselves using code from wp_create_thumbnail...
				// If no filters change the filename, we'll do a default transformation.
				//if ( basename($source_url) == $thumb = apply_filters('thumbnail_filename', basename($source_url)) )
				//$thumb = preg_replace('!(\.[^.]+)?$!', __('.thumbnail').'$1', basename($source_url), 1);
				$thumb = rawurldecode(basename($thumb_url));

				$image_file = ko_blogger_image_import_handle_download($source_url, rawurldecode(basename($source_url)), $applychanges);
				$thumb_file = ko_blogger_image_import_handle_download($thumb_url, $thumb, $applychanges);
				
				// If don't make a post it should all still work.
				// The only advantage that I can see is these will show up in the post editor
				// but I don't really need them there.
				//if ($applychanges)
				//{
				//	ko_blogger_image_import_construct_image_post($image_file, $thumb_file, $word->ID);
				//}

				// Replace the old URLs with the new URLs.
				$post_content = str_replace($source_url, $image_file["url"], $post_content);
				$post_content = str_replace($thumb_url, $thumb_file["url"], $post_content);

				$count += 1;
				$found_match = true;

				// Check if the maximum number of imports has been exceeded and break out.
				if ($maximports <= $count)
				{
					// We have imported enough for now
					break;
				}
			}

			if ($applychanges && $found_match)
			{
				//echo "<xmp>".$post_content."</xmp><br />\n";
				echo "Applying Changes to Database...<br />\n";
				$whole_post['ID'] = $ID;
				$whole_post['post_content'] = $wpdb->escape($post_content);
				wp_update_post($whole_post);
			}
		}
		
		// Now try a simpler match that should produce the same number of matches to confirm that we didn't miss anything.
		$simplermatchcount = preg_match_all ("|src\=([\"'`])(".$innerT.")\\1|i", $post, $simplermatches, PREG_SET_ORDER);
		if ($matchcount != $simplermatchcount)
		{
			echo "<br /><u>WARNING - There may be a problem with the matches.<br />\n";
			echo "We have found some blogger hosted jpg files without a well formed A tag.<br />\n";
			echo "There were only ".$matchcount." matches but we found ".$simplermatchcount." jpg img tags.<br />\n";
			echo "This may be nothing but you should check this post</u><br />\n";

			// Show the link to the questionable post
			$badlink = get_permalink($word->ID);
			echo "<a href='" . $badlink . "'>" . $badlink . "</a><br />\n";

			if ($simplermatchcount > 0)
			{
				foreach ($simplermatches as $match)
				{
					echo $match[2] . "<br />\n";
				}
				echo "<br />\n";
			}
		}

		$postcount += 1;
		
		// Check if the maximum number of imports has been exceeded and break out.
		if ($maximports <= $count)
		{
			// We have imported enough for now
			break;
		}

	}

	echo "Posts Processed : ".$postcount."<br />\n";
	echo "Images Imported : ".$count." (+ thumbnails)<br />\n";
}


function ko_blogger_import_page()
{
	if (isset($_POST['maximports']))
	{
		$max_imports = $_POST['maximports'];
	}
	else
	{
		$max_imports = 10;
	}

	if (isset($_POST['start']))
	{
		?><div class="updated"><p><strong><?php
			$applychanges = true;
			ko_blogger_image_import_process($max_imports, $applychanges);
			_e('Success!',  'Localization name')
		?></strong></p></div><?php
	}
	else if (isset($_POST['test']))
	{
		?><div class="updated"><p><strong><?php
			$applychanges = false;
			ko_blogger_image_import_process($max_imports, $applychanges);
			_e('Success!',  'Localization name')
		?></strong></p></div><?php
	}

	?><div class=wrap>
		<form method="post">
			<h2>Blogger Image Import</h2>
			<p>
				This plugin will go through each post looking for images hosted in blogspot.com.  Those images will be downloaded to the server and the links will be updated appropriately.<br />
				<br />
				Press the Start button below to start the process.<br />
				<br />
				Press the Test button below to test the process without applying any changes.  Note - this may create the upload folder where the files would have been copied, but it will not copy any files.<br />
			</p>
			Max Imports <input type="text" name="maximports" value="<?php echo $max_imports ?>" >
			<div class="submit">
				<input type="submit" name="start" value="<?php
				_e('Start Import', 'Localization name')?> »" />
				<input type="submit" name="test" value="<?php
				_e('Test Import', 'Localization name')?> »" />
			</div>
		</form>
	</div><?php
}

function ko_add_blogger_import_page()
{
	if (function_exists('add_options_page'))
	{
		add_options_page('Blogger Image Import', 'Blogger Image Import', 10, basename(__FILE__), 'ko_blogger_import_page');
	}
}

add_action('admin_menu', 'ko_add_blogger_import_page');

?>
