<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Archives Extended GDJ
*/
?>

<?php get_header(); ?>

	<div id="content" class="archives clearfix" role="main">
		<h2>Archives by Category:</h2>
		<ul class="archives-left"><?php wp_list_categories('title_li=&child_of=12'); ?></ul>
		<ul class="archives-right"><?php wp_list_categories('title_li=&child_of=13'); ?></ul>
		<h2>Archives by Month:</h2>
<?php
$y = date('Y');
$m = 12;
$years = array($y => array());
$output = '';
while($m > date('n')){
	$years[$y][] = date('M', strtotime($m.'/1/'.$y));
	$m--;
}
query_posts("monthnum=$m&year=$y&orderby=date&nopaging=true");
$url = get_bloginfo('siteurl');
while(have_posts() && $y > 2000){
	$time = strtotime($m.'/1/'.$y);
	$years[$y][] = "<a href='".$url."/$y/$m'>".date('M', $time).'</a>';
	$output .= '<h3>'.date('F Y', $time).'</h3><ul>';
	$content[$time] = array();
	while(have_posts()){
		the_post();
		$output .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
	}
	$output .= '</ul>';
	$m--;
	if($m == 0){
		$m = 12;
		$y--;
		$years[$y] = array();
	}
	query_posts("monthnum=$m&year=$y");
}
while($m > 0){
	$years[$y][] = date('M', strtotime($m.'/1/'.$y));
	$m--;
}
foreach($years as $year => $months){
	print '<div class="year"><a href="/keepers/'.$year.'">'.$year.'</a>: '.implode(' ', $months)."</div>\n";
}
print $output;
?>
<?php /* ?>
		<h2>Archives by Month:</h2>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
<?php // */ ?>
</div>

<?php get_footer(); ?>
