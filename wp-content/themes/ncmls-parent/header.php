<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
wp_enqueue_script('navbar', get_bloginfo('template_directory').'/navbar.js', array('jquery'), '1.0');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<style type="text/css">
.clearfix:after {
	content: ".";
	display: block;
	height: 0;
	clear: both;
	visibility: hidden;
}
.clearfix {display: inline-block;}  /* for IE/Mac */
</style>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<style type="text/css" media="screen">

<?php
/*
// Checks to see whether it needs a sidebar or not
if ( empty($withcomments) && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbg-<?php bloginfo('text_direction'); ?>.jpg") repeat-y top; border: none; }
<?php } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
<?php } // */ ?>

</style>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
</head>
<body <?php body_class(); ?>>
<div id="page">
    <div id="header">
        <div id="search">
			<a class="mls" href="http://www.ncmls.org">North Carolina Museum of Life and Science</a>
			<form method="get" action="<?php bloginfo('siteurl'); ?>/">
				<label class="text" for="s">Search:</label>
				<input class="text" type="text" id="s" name="s" />
				<input type="submit" value="Search" class="submit" />
			</form>
		</div>
		<div id="branding">
			<div id="mlsLogo"><a href="<?php bloginfo('siteurl'); ?>">Museum of Life and Science Blogs</a></div>
			<div id="blog-title"><span><a href="<?php bloginfo('siteurl'); ?>" title="<?php bloginfo('title'); ?>" rel="home"><?php bloginfo('title'); ?></a></span></div>
			<h1 id="blog-description">Museum of Life and Science in Durham, NC</h1>
		</div><!--  #branding -->
		<div class="nav">
<?php do_action('header_nav'); ?>
		</div>
	</div>
<?php /* ?>
<div id="header" role="banner">
	<div id="headerimg">
		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<div class="description"><?php bloginfo('description'); ?></div>
	</div>
</div>
<hr />
<?php // */ ?>