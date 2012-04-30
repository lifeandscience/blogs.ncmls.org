<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Categories
*/
?>

<?php get_header(); ?>

	<div id="content" class="archives clearfix" role="main">
		<h2>Archives by Subject:</h2>
		<ul>
			 <?php wp_list_categories(); ?>
		</ul>
</div>

<?php get_footer(); ?>
