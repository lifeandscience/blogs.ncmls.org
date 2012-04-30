<?php
/*
Template Name: Most Popular
*/

global $wpdb, $dontDoSidebar;
$dontDoSidebar = true;
$table_wpp = "wp_2_popularpostsdata";
$range = "post_date_gmt < '".gmdate("Y-m-d H:i:s")."'";
$nopages = "AND $wpdb->posts.post_type = 'post'";
$sortby = 'avg_views';
$fields .= ", ($table_wpp.pageviews/(IF ( DATEDIFF(CURDATE(), MIN($table_wpp.day)) > 0, DATEDIFF(CURDATE(), MIN($table_wpp.day)), 1) )) AS 'avg_views' ";

$mostpopular = $wpdb->get_results("SELECT $wpdb->posts.ID $fields FROM $wpdb->posts LEFT JOIN $table_wpp ON $wpdb->posts.ID = $table_wpp.postid WHERE post_status = 'publish' AND post_password = '' AND $range AND pageviews > 0 $nopages GROUP BY postid ORDER BY $sortby DESC LIMIT 5");

global $options;
foreach ($options as $value) {
    if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; }
    else { $$value['id'] = get_option( $value['id'] ); }
    }
?>
<?php get_header() ?>

	<div id="container">
		<div id="content">

			<?php thematic_navigation_above();?>
			
<?php get_sidebar('index-top') ?>

<?php thematic_above_indexloop() ?>
<h1 class="page-title">Most Popular Posts</h1>
<?php
foreach($mostpopular as $p){
	query_posts('p='.$p->ID);
	greg_index_loop();
}
//the_content();
?>
<?php //thematic_indexloop() ?>

<?php thematic_below_indexloop() ?>

<?php get_sidebar('index-bottom') ?>

			<?php //thematic_navigation_below();?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php thematic_sidebar() ?>
<?php get_footer() ?>