<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Other Blogs
*/
?>

<?php get_header(); ?>

	<div id="content" class="archives other-blogs clearfix" role="main">
		<h2>Other Blogs:</h2>
		<ul>
			 <?php wp_list_bookmarks('category=1193&show_description=1&show_name=1&title_li=&categorize=0'); ?>
		</ul>
</div>

<?php get_footer(); ?>
