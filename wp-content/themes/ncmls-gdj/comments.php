<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
	global $post;
?>

<!-- You can start editing here. -->
<?php if(is_single()){ ?>
<?php if ( count(get_comments("type=comment&post_id=".$post->ID)) ) : ?>
	<ol class="commentlist">
	<?php wp_list_comments('callback=ncmls_comment'); ?>
	<?php
		global $user_ID;
		if($user_ID == 1){ 
			error_log(print_r(get_comments("type=comment&order=ASC&post_id=".$post->ID), true), 3, "/tmp/blog_comment_log");
			wp_list_comments('callback=ncmls_comment_noop&type=comment');
		}
	?>
	</ol>
 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ( comments_open() ) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Comments are closed.</p>

	<?php endif; ?>
<?php endif; ?>


<?php if ( comments_open() ) : ?>

<div class="respond">

<?php /* ?><h3><?php comment_form_title( 'Leave a Reply', 'Leave a Reply to %s' ); ?></h3><?php // */ ?>

<div class="cancel-comment-reply">
	<small><?php cancel_comment_reply_link(); ?></small>
</div>

<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<p>You must be <a href="<?php echo wp_login_url( get_permalink() ); ?>">logged in</a> to post a comment.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
<p>Your email is <em>never</em> published nor shared. Required fields are marked <span class="required">*</span></p>
<?php if ( is_user_logged_in() ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></p>

<?php else : ?>

<p><label for="author">Name <?php if ($req) echo "<span class=\"required\">*</span>"; ?></label><input type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> /></p>

<p><label for="email">Email <?php if ($req) echo "<span class=\"required\">*</span>"; ?></label><input type="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> /></p>

<p><label for="url">Website</label><input type="text" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="3" /></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->

<p><textarea name="comment" id="comment" cols="58" rows="10" tabindex="4"></textarea></p>

<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>
</div>
<?php endif; // if you delete this the sky will fall on your head ?>

<?php
}else{
	$commentNumber = get_comments_number();
?>
	<div class="commentlist">
		<p class="summary">There are <a<?php print $commentNumber != 0 ? ' class="orange"':''; ?> href="<?php the_permalink(); ?>"><?php comments_number('no comments', '1 comment', '% comments'); ?></a> on this journal entry. <a href="<?php the_permalink(); ?>"><?php print $commentNumber == 0 ? 'Start' : 'Join'; ?> the conversation.</a></p>
	</div>
<?php }
