<?php
/*
Template Name: Most Popular
*/

global $wpdb, $dontDoSidebar;
$dontDoSidebar = true;
$table_wpp = $wpdb->prefix."popularpostsdata";
$range = "post_date_gmt < '".gmdate("Y-m-d H:i:s")."'";
$nopages = "AND $wpdb->posts.post_type = 'post'";
$sortby = 'avg_views';
$fields .= ", ($table_wpp.pageviews/(IF ( DATEDIFF(CURDATE(), MIN($table_wpp.day)) > 0, DATEDIFF(CURDATE(), MIN($table_wpp.day)), 1) )) AS 'avg_views' ";
$mostpopular = $wpdb->get_results("SELECT $wpdb->posts.ID $fields FROM $wpdb->posts LEFT JOIN $table_wpp ON $wpdb->posts.ID = $table_wpp.postid WHERE post_status = 'publish' AND post_password = '' AND $range AND pageviews > 0 $nopages GROUP BY postid ORDER BY $sortby DESC LIMIT 5");
?>
<?php get_header() ?>
	<div id="content" class="clearfix" role="main">
		<h2 class="page-title">Most Popular Posts</h2>
<?php
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