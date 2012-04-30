<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Bloggers
*/
?>

<?php get_header(); ?>

	<div id="content" class="archives clearfix" role="main">
		<h2>Bloggers:</h2>
		<ul>
			 <?php wp_list_bookmarks('category=1192'); ?>
		</ul>
</div>

<?php get_footer(); ?>
