<?php
function the_ad_header_nav(){
?>			<div class="clearfix">
				<ul class="text">
					<li><a href="<?php bloginfo('siteurl'); ?>/archives">Archives</a></li>
					<li><a href="<?php bloginfo('siteurl'); ?>/rss">Subscribe</a></li>
					<li><a href="http://www.ncmls.org/blogs">Other Blogs</a></li>
					<li><a href="http://lifeandscience.org/visit">Visit the Museum</a></li>
				</ul>
			</div>
			<div id="piclist" class="clearfix">
				<div class="topic">
					<ul class="pics">
<?php wp_list_categories('child_of=12&hide_empty=0&depth=1&orderby=order&title_li='); ?>
					</ul>
					<span class="title">topic</span>
				</div>
				<div class="season">
					<ul class="pics">
<?php wp_list_categories('child_of=13&hide_empty=0&depth=1&orderby=order&title_li='); ?>
					</ul>
					<span class="title">season</span>
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


function do_the_post($showNav = true){
	if(is_single()){
		// This is a straight copy of do_the_post from ncmls-parent
		global $withcomments;
		$withcomments = 1;
?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<div class="meta">	
<?php
						$cats = get_the_category();
						if(count($cats)){
?>
					<ul class="post-categories">
<?php						foreach($cats as $cat){ ?>
						<li class="cat-item-<?php print $cat->cat_ID; ?>">
							<a class="icon" title="<?php print $cat->name; ?>" href="<?php print get_category_link($cat); ?>"><?php print $cat->name; ?></a>
							<a class="name" title="<?php print $cat->name; ?>" href="<?php print get_category_link($cat); ?>"><?php print strlen($cat->name)>6?substr($cat->name, 0, 6).'&hellip;':$cat->name; ?></a>
						</li>
<?php						} ?>
					</ul>
<?php 					} ?>
					<?php the_author_image(); ?>
					<span class="author">by <a href="#"><?php the_author_posts_link(); ?></a>, <?php $auth = get_the_author_meta('user_login'); print get_author_title($auth); ?></span>
<?php
						$author = get_the_author_meta('description');
						$author = explode("\n", $author);
						if(count($author)){
?>
					<div class="author-blurb"><?php print $author[0]; ?></div>
					<div class="author-meet"><?php print $author[2]; ?></div>
<?php 					} ?>
					<div class="tags"><?php the_tags('Tags: ', ', '); ?></div>
				</div>
				<div class="content">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a><?php edit_post_link('Edit', ' | '); ?></h2>
					<span class="date"><?php the_time('F jS, Y') ?></span>
					<div class="entry">
						<?php the_content('Read the rest of this entry &raquo;'); ?>
					</div>
<?php					if($showNav){ ?>
					<div id="nav-below">
						<?php previous_post_link( '<div class="nav-previous">Previous<br/>%link</div>', '%title' ); ?>
						<?php next_post_link( '<div class="nav-next">Next<br/>%link</div>', '%title' ); ?>
					</div><!-- #nav-below -->
<?php					} ?>
				</div>
				<div class="comment">
					<h3>Join the conversation:</h3>
					<?php comments_template(); ?>
				</div>
<div class="recent">
	<div class="left">
		<h4>You Might Also Like These Posts</h4>
<?php
		$cats = get_the_category();
		$c = array();
		foreach($cats as $cat){
			$c[] = $cat->term_id;
		}
		global $post;
		$p = $post;
		$newQuery = new WP_Query(array('category__in' => $c, 'posts_per_page' => 5));
		if($newQuery->have_posts()){
?>
		<ul>
<?php 		while($newQuery->have_posts()){ $newQuery->the_post(); ?>
			<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
<?php 		} ?>
		</ul>
<?php
		}
		$post = $p;
		//wp_reset_query();
?>
	</div>
	<div class="right">
		<h4>Most Popular Posts With These Tags</h4>
		<?php $tags = array(); foreach(get_the_tags() as $tag){ $tags[] = $tag->term_taxonomy_id; } get_mostpopular('stats_comments=0&limit=5&tags_to_include='.implode(',', $tags)); ?>
	</div>
	<div class="call-to-action">
		<p>If you have an account on any of the Museum's blogs, you can <a href="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>">sign in with the same login</a> to contribute to the discussion.</p>
		<p>If you don't have an account, <a href="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>?action=register">signing up</a> is free and easy.</p>
	</div>
</div>
			</div>
<?php
	}else{
		global $withcomments;
		$withcomments = 1;
?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<div class="meta">	
<?php
						$cats = get_the_category();
						if(count($cats)){
?>
					<ul class="post-categories">
<?php						foreach($cats as $cat){ ?>
						<li class="cat-item-<?php print $cat->cat_ID; ?>">
							<a class="icon" title="<?php print $cat->name; ?>" href="<?php print get_category_link($cat); ?>"><?php print $cat->name; ?></a>
							<a class="name" title="<?php print $cat->name; ?>" href="<?php print get_category_link($cat); ?>"><?php print strlen($cat->name)>6?substr($cat->name, 0, 6).'&hellip;':$cat->name; ?></a>
						</li>
<?php						} ?>
					</ul>
<?php 					} ?>
				</div>
				<div class="content">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a><?php edit_post_link('Edit', ' | '); ?><span class="author"> by <a href="#"><?php the_author_posts_link(); ?></a>, <?php $auth = get_the_author_meta('user_login'); print get_author_title($auth); ?></span></h2>
					<span class="date"><?php the_time('F jS, Y') ?></span>
					<div class="entry">
						<?php //the_content('Read the rest of this entry &raquo;'); ?>
<?php
					global $post;
					if(strpos($post->post_content, '<!--more-->') !== false){
						// Has Read More
						the_content('Read the rest of this entry &raquo;');
					}else{
						$excerpt = get_the_excerpt();
//						if(strpos($excerpt, '[...]') !== false){
//							$excerpt = substr($excerpt, 0, -5); 
//						}
?>
						<p><?php print $excerpt; ?> <a href="<?php the_permalink(); ?>">Read the rest of this entry &raquo;</a></p>
<?php				} ?>
					</div>
<?php					if($showNav){ ?>
					<div id="nav-below">
						<?php previous_post_link( '<div class="nav-previous">Previous<br/>%link</div>', '%title' ); ?>
						<?php next_post_link( '<div class="nav-next">Next<br/>%link</div>', '%title' ); ?>
					</div><!-- #nav-below -->
<?php					} ?>
				</div>
				<div class="comment">
					<?php comments_template(); ?>
				</div>
			</div>
<?php
	}
}
//*/