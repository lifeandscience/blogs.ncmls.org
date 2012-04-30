<?php
function the_ad_header_nav(){
?>			<div class="clearfix">
				<ul class="text">
					<li><a href="<?php bloginfo('siteurl'); ?>/categories">Categories</a></li>
					<li><a href="<?php bloginfo('siteurl'); ?>/archives">Archives</a></li>
					<li><a href="<?php bloginfo('siteurl'); ?>/bloggers">Dummies</a></li>
					<li><a href="<?php bloginfo('siteurl'); ?>/rss">Subscribe</a></li>
					<li><a href="http://www.ncmls.org/blogs">Other Blogs</a></li>
					<li><a href="http://lifeandscience.org/visit">Visit the Museum</a></li>
				</ul>
			</div>
			<div id="piclist" class="clearfix">
				<div class="dummies">
					<ul class="pics">
						<li class="cat-item author-sam">
							<a title="View all posts authored by Sierra Sam" href="<?php bloginfo('siteurl'); ?>/author/sierrasam/">Sierra Sam</a>
						</li>
						<li class="cat-item author-hybrid">
							<a title="View all posts authored by Hybrid III" href="<?php bloginfo('siteurl'); ?>/author/hybridthree/">Hybrid III</a>
						</li>
						<li class="cat-item author-noodle">
							<a title="View all posts authored by Noodle J. Noodle" href="<?php bloginfo('siteurl'); ?>/author/noodlejnoodle/">Noodle J. Noodle</a>
						</li>
						<li class="cat-item author-carl">
							<a title="View all posts authored by Crash Test Carl" href="<?php bloginfo('siteurl'); ?>/author/crashtestcarl/">Crash Test Carl</a>
						</li>
					</ul>
					<span class="title">dummies</span>
				</div>
				<div class="categories">
					<ul class="pics">
<?php wp_list_categories('child_of=5367&hide_empty=0&depth=1&orderby=order&title_li='); ?>
					</ul>
					<span class="title">categories</span>
				</div>
				<div class="popularity">
					<ul class="pics">
						<li class="cat-item most-popular">
							<a title="View the most popular posts" href="<?php bloginfo('siteurl'); ?>/most-popular/">Most Popular</a>
						</li>
						<li class="cat-item newest">
							<a title="View the newest posts" href="<?php bloginfo('siteurl'); ?>">Newest</a>
						</li>
						<li class="cat-item most-comments">
							<a title="View the most commented-on posts" href="<?php bloginfo('siteurl'); ?>/most-comments/">Most Commented-on</a>
						</li>
						<li class="cat-item rss-feed">
							<a title="View the RSS feed" href="<?php bloginfo('siteurl'); ?>/rss/">RSS</a>
						</li>
					</ul>
					<span class="title">popularity</span>
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

function ncmls_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>">
		<?php if($comment->user_id){ ?>
			<span class="keeper-title-outer"><span class="keeper-title">Blogger Comment</span> :</span>
		<?php } ?>
		<?php if ($comment->comment_approved == '0') : ?>
			<em><?php _e('Your comment is awaiting moderation.') ?></em>
			<br />
		<?php endif; ?>
		<?php comment_text() ?>
		<div class="comment-author vcard"><?php printf(__('<span class="says">Posted by</span> <cite class="fn">%s</cite>'), get_comment_author_link()) ?></div>
		<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%2$s on %1$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>

		<div class="reply">
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</div>
  	</div>
<?php
}