<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>
<!--	<div id="content" class="narrowcolumn" role="main">-->
	<div id="content" class="clearfix" role="main">

	<?php if (have_posts()) : ?>

<?php
			while (have_posts()):
				the_post();
				do_the_post(false);
			endwhile;
?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div>

<?php //get_sidebar(); ?>

<?php get_footer(); ?>
