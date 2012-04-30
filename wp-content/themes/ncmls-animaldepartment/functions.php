<?php
function the_ad_header_nav(){
?>			<div class="clearfix">
				<ul class="text">
<?php /* ?>					<li><a href="<?php bloginfo('siteurl'); ?>/categories">Categories</a></li><?php // */ ?>
					<li><a href="<?php bloginfo('siteurl'); ?>/archives">Archives</a></li>
					<li><a href="<?php bloginfo('siteurl'); ?>/bloggers">Bloggers</a></li>
					<li><a href="<?php bloginfo('siteurl'); ?>/rss">Subscribe</a></li>
					<li><a href="http://www.ncmls.org/blogs">Other Blogs</a></li>
					<li><a href="http://lifeandscience.org/visit">Visit the Museum</a></li>
				</ul>
			</div>
			<div id="piclist" class="clearfix">
				<div class="exhibits">
					<ul class="pics">
<?php wp_list_categories('child_of=1197&hide_empty=0&depth=1&orderby=order&title_li='); ?>
					</ul>
					<span class="title">exhibits</span>
				</div>
				<div class="topics">
					<ul class="pics">
<?php wp_list_categories('child_of=1198&hide_empty=0&depth=1&orderby=order&title_li='); ?>
					</ul>
					<span class="title">animal department</span>
				</div>
<?php /* ?>
				<div class="browse-by">
					<ul class="pics">
						<li class="cat-item cat-item-videos"><a href="<?php bloginfo('siteurl'); ?>/category/videos" title="View posts with videos">Videos</a></li>
						<li class="cat-item cat-item-most-popular"><a href="<?php bloginfo('siteurl'); ?>/most-popular" title="View most popular posts">Most popular posts</a></li>
						<li class="cat-item cat-item-most-comments"><a href="<?php bloginfo('siteurl'); ?>/most-comments" title="View posts with the most comments">Most Comments</a></li>
						<li class="cat-item cat-item-rss"><a href="<?php bloginfo('siteurl'); ?>/rss" title="Subscribe to the RSS Feed">RSS</a></li>
					</ul>
					<span class="title">Browse-by</span>
				</div>
<?php // */ ?>
			</div>
<?php
}
add_action('header_nav', 'the_ad_header_nav');

function do_init(){
	remove_action('header_nav', 'the_header_nav');
}
add_action('init', 'do_init');