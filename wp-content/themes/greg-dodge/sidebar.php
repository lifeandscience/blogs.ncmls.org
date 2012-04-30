<?php thematic_abovemainasides(); ?>

	<div id="primary" class="aside main-aside">
<?php global $dontDoSidebar; if(is_single() && !is_page() && !$dontDoSidebar){ ?>
<!--		<h1>Test! <?php print $post->ID; ?></h1>-->
<?php
		$cats = get_the_category();
		if(count($cats)){
?>
		<ul class="post-categories">
<?php		foreach($cats as $cat){ ?>
			<li class="cat-item-<?php print $cat->cat_ID; ?>"><a href="<?php print get_category_link($cat); ?>"><?php print $cat->name; ?></a></li>
<?php		} ?>
		</ul>
<?php 	} ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
<?php } ?>
		<div class="portrait clearfix">
			<img src="<?php print get_stylesheet_directory_uri(); ?>/images/dodge_woods.jpg" alt="Greg Dodge's Portrait" />
		</div>
<?php if(is_single() && !is_page() && !$dontDoSidebar){ ?>
		<span class="author"><?php the_author(); ?></span>
		<span class="publish-date"><?php the_time('F j, Y'); ?></span>
<?php } ?>
<?php if (is_sidebar_active('primary-aside')) { ?>
		<ul class="xoxo">
	<?php dynamic_sidebar('primary-aside'); ?>
		</ul>
		<div class="tags"><?php the_tags(); ?></div>
<?php } ?>
	</div><!-- #primary .aside -->

<?php thematic_betweenmainasides(); ?>

<?php if(is_single() && !is_page() && !$dontDoSidebar){ ?>
	<div id="secondary" class="aside main-aside">
		<h4 class="join"><?php if($question = get_post_meta($post->ID, 'comment_question', true)){ print $question.' <br/><br/>'; } ?>Join the Conversation!</h4>
<?php thematic_comments_template(); ?>
<?php if (is_sidebar_active('secondary-aside')) { ?>
		<ul class="xoxo">
<?php dynamic_sidebar('secondary-aside') ?>
		</ul>
<?php } } ?>
	</div><!-- #secondary .aside -->
	
<?php thematic_belowmainasides(); ?>
