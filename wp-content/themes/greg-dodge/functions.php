<?php

function add_search(){
?>
<div id="search">
	<a class="mls" href="http://www.ncmls.org">North Carolina Museum of Life and Science</a>
	<form method="get" action="/greg-dodge/">
		<label class="text" for="s">Search:</label>
		<input class="text" type="text" id="s" name="s" />
<!--		<input class="radio" type="radio" id="blog" name="typeOfSearch" />
		<label class="radio" for="blog">Blog</label>
		<input class="radio" type="radio" id="site" name="typeOfSearch" />
		<label class="radio" for="site">Site</label>-->
		<input type="submit" value="Search" class="submit" />
	</form>
</div>
<?php
}
add_action('thematic_header', 'add_search', 0);
function adjust_branding(){
?>
<div id="mlsLogo"><a href="<?php bloginfo('siteurl'); ?>">Museum of Life and Science Blogs</a></div>
<?php
}
add_action('thematic_header', 'adjust_branding', 2);
function add_clearfix_style($content){
	$content .= <<<EOF
<style type="text/css">

  .clearfix:after {
    content: ".";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
    }

.clearfix {display: inline-block;}  /* for IE/Mac */

</style><!-- main stylesheet ends, CC with new stylesheet below... -->

<!--[if IE]>
<style type="text/css">
  .clearfix {
    zoom: 1;     /* triggers hasLayout */
    display: block;     /* resets display for IE/Win */
    }  /* Only IE can see inside the conditional comment
    and read this CSS rule. Don't ever use a normal HTML
    comment inside the CC or it will close prematurely. */
</style>
<![endif]-->
EOF;
	return $content;
}
add_filter('thematic_create_stylesheet', 'add_clearfix_style');
function add_navigation(){
?>
<div class="nav">
	<div class="clearfix">
		<ul class="text">
			<li><a href="/greg-dodge/archives">Archives</a></li>
			<li><a href="/greg-dodge/rss">Subscribe</a></li>
			<li><a href="http://lifeandscience.org">Visit the Museum</a></li>
		</ul>
	</div>
	<div id="piclist" class="clearfix">
		<div class="topic">
			<ul class="pics">
<?php wp_list_categories('child_of=12&hide_empty=0&title_li='); ?>
			</ul>
			<span class="title">Topic</span>
		</div>
		<div class="season">
			<ul class="pics">
<?php wp_list_categories('child_of=13&hide_empty=0&title_li='); ?>
			</ul>
			<span class="title">Season</span>
		</div>
		<div class="popularity">
			<ul class="pics">
				<li class="cat-item cat-item-thumbs-up"><a href="/greg-dodge/most-popular" title="View most popular posts">Most popular posts</a></li>
				<li class="cat-item cat-item-new"><a href="/greg-dodge/" title="View most recent posts">Most recent posts</a></li>
				<li class="cat-item cat-item-speech-bubble"><a href="/greg-dodge/most-comments" title="View posts with the most comments">Most Comments</a></li>
				<li class="cat-item cat-item-waves"><a href="/greg-dodge/rss" title="Subscribe to the RSS Feed">RSS</a></li>
			</ul>
			<span class="title">Popularity</span>
		</div>
	</div>
</div>
<?php
}
add_action('thematic_header', 'add_navigation');
/*
// Remove default Thematic actions
function remove_thematic_actions() {
	remove_action('thematic_header', 'thematic_access', 9);
}
// Information in Post Header
function greg_postheader($content) {
	return '';
} // end greg_postheader
add_filter('thematic_postheader','greg_postheader');
add_filter('thematic_postfooter','greg_postheader');
//*/
// Remove default Thematic actions
function remove_thematic_actions() {
    remove_action('thematic_singlepost','thematic_single_post');
	remove_action('thematic_header', 'thematic_access', 9);
	remove_action('thematic_indexloop', 'thematic_index_loop');
	remove_action('thematic_archiveloop', 'thematic_archive_loop');
	remove_action('thematic_categoryloop', 'thematic_category_loop');
//	remove_action('thematic_navigation_below', 'thematic_nav_below', 2);
}
add_action('init','remove_thematic_actions');

// The Single Post
function greg_single_post() { ?>
			<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class(); ?>">
    			<?php //thematic_postheader(); ?>
				<div class="entry-content">
<?php thematic_content(); ?>

					<?php wp_link_pages('before=<div class="page-link">' .__('Pages:', 'thematic') . '&after=</div>') ?>
				</div>
				<?php //thematic_postfooter(); ?>
			</div><!-- .post -->
<?php
}
add_action('thematic_singlepost', 'greg_single_post');


// The Index Loop
function greg_index_loop() {
		add_filter('thematic_postheader', 'greg_make_h2');
		/* Count the number of posts so we can insert a widgetized area */ $count = 1;
		while ( have_posts() ) : the_post() ?>
			<div class="post-meta">
<!-- HERE OOGA -->
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
			</div>
			<div id="post-<?php the_ID() ?>" class="<?php thematic_post_class() ?>">
    			<?php thematic_postheader(); ?>
				<div class="entry-content">
<?php
//					thematic_content();
					global $post;
					if(strpos($post->post_content, '<!--more-->') !== false){
						// Has Read More
						the_content('Read more...');
					}else{
						$excerpt = get_the_excerpt();
//						if(strpos($excerpt, '[...]') !== false){
//							$excerpt = substr($excerpt, 0, -5); 
//						}
?>
						<p><?php print $excerpt; ?> <a href="<?php the_permalink(); ?>">Read more...</a></p>
<?php				} ?>

				<?php wp_link_pages('before=<div class="page-link">' .__('Pages:', 'thematic') . '&after=</div>') ?>
				</div>
				<?php thematic_postfooter(); ?>
			</div><!-- .post -->

				<?php

				if ($count==$thm_insert_position) {
						get_sidebar('index-insert');
				}
				$count = $count + 1;
		endwhile;
}
add_action('thematic_indexloop', 'greg_index_loop');
add_action('thematic_archiveloop', 'greg_index_loop');
add_action('thematic_categoryloop', 'greg_index_loop');
/*
// Action to create the below navigation
function greg_nav_below() {
		if (is_single()) { ?>

			<div id="nav-below" class="navigation">
				<div class="nav-previous"><span class="title">Previous</span><?php thematic_previous_post_link() ?></div>
				<div class="nav-next"><span class="title">Next</span><?php thematic_next_post_link() ?></div>
			</div>

<?php
		} else { ?>

			<div id="nav-below" class="navigation">
                <?php if(function_exists('wp_pagenavi')) { ?>
                <?php wp_pagenavi(); ?>
                <?php } else { ?>  
				<div class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&laquo;</span> Older posts', 'thematic')) ?></div>
				<div class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&raquo;</span>', 'thematic')) ?></div>
				<?php } ?>
			</div>	
	
<?php
		}
}
add_action('thematic_navigation_below', 'greg_nav_below', 2);
//*/
function greg_make_h2($content){
	global $post;
	return str_replace(array('<h1 class="entry-title">', '</h1>'), array('<h2 class="entry-title"><a href="'.get_permalink().'">', '</a></h2>'), $content);
}
function greg_previous_post_link_args($args){
	$args['format'] = '<span class="title">Previous</span>%link';
	$args['link'] = '%title';
	return $args;
}
add_filter('thematic_previous_post_link_args', 'greg_previous_post_link_args');
function greg_next_post_link_args($args){
	$args['format'] = '<span class="title">Next</span>%link';
	$args['link'] = '%title';
	return $args;
}
add_filter('thematic_next_post_link_args', 'greg_next_post_link_args');
function the_bottom_links(){
?><div class="recent">
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
<?php while($newQuery->have_posts()){ $newQuery->the_post(); ?>
			<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
<?php } ?>
		</ul>
<?php
}
$post = $p;
wp_reset_query();
?>
	</div>
	<div class="right">
		<h4>Most Popular Posts With These Tags</h4>
		<?php $tags = array(); foreach(get_the_tags() as $tag){ $tags[] = $tag->term_taxonomy_id; } get_mostpopular('stats_comments=0&tags_to_include='.implode(',', $tags)); ?>
	</div>
</div><?php
}
function gdj_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
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
function gdj_head(){
	echo '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';
}
add_action('wp_head', 'gdj_head');
