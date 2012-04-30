<?php
/*
Template Name: Most Comments
*/

global $wpdb, $dontDoSidebar;
$dontDoSidebar = true;
$table_wpp = $wpdb->prefix."popularpostsdata";
$range = "post_date_gmt < '".gmdate("Y-m-d H:i:s")."'";
$nopages = "AND $wpdb->posts.post_type = 'post'";
$sortby = 'comment_count';
$fields .= '';

$mostpopular = $wpdb->get_results("SELECT $wpdb->posts.ID $fields FROM $wpdb->posts LEFT JOIN $table_wpp ON $wpdb->posts.ID = $table_wpp.postid WHERE post_status = 'publish' AND post_password = '' AND $range AND pageviews > 0 $nopages GROUP BY postid ORDER BY $sortby DESC LIMIT 5");
?>
<?php get_header() ?>
	<div id="content" class="clearfix" role="main">
		<h2 class="page-title">Most Commented-on Posts</h2>
<?php
echo "<!--"."SELECT $wpdb->posts.ID $fields FROM $wpdb->posts LEFT JOIN $table_wpp ON $wpdb->posts.ID = $table_wpp.postid WHERE post_status = 'publish' AND post_password = '' AND $range AND pageviews > 0 $nopages GROUP BY postid ORDER BY $sortby DESC LIMIT 5"."-->";

if(count($mostpopular)){
foreach($mostpopular as $p){
	query_posts('p='.$p->ID);
	if(have_posts()){
		while(have_posts()){
			the_post();
			do_the_post(false);
		}
	}
}
}
?>
		</div><!-- #content -->
<?php get_footer() ?>