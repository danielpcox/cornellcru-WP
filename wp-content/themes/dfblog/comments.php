<?php


// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die( _e('Please do not load this page directly. Thanks!') );

	if ( post_password_required() ) { ?>
		<p class="resalted"><?php _e('This post is password protected. Enter the password to view comments.'); ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->
<div id="comments">

<?php if ( have_comments() ) : ?>

	<h3><?php comments_number( __('No Comments &#187;'), __('1 Comment &#187;'), __('% Comments &#187;') );?></h3>

	<div class="clear">&nbsp;</div>

		<ul class="commentlist">
		<?php
			$defaults = array('walker' => null, 'max_depth' => '', 'style' => 'ul', 'callback' => null, 'end-callback' => null, 'type' => 'all', 'page' => '', 'per_page' => '', 'avatar_size' => 32, 'reverse_top_level' => null, 'reverse_children' => '');
			wp_list_comments($defaults);
		?>
		</ul>

	<div class="browse">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>

	<div class="clear">&nbsp;</div>

<?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
	<!-- If comments are open, but there are no comments. -->
	
	<?php else : // comments are closed ?>
	<!-- If comments are closed. -->
	<p class="resalted"><?php _e('Comments are closed.', 'default'); ?></p>
	<?php endif; ?>

<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

	<div id="respond">

	<h3><?php comment_form_title( __('Leave a Reply', 'default'), __('Leave a Reply to %s', 'default') ); ?></h3>
	<div class="cancel-comment-reply">
		<small><?php cancel_comment_reply_link(); ?></small>
	</div>

	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>

	<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'default'), get_option('siteurl').'/wp-login.php?redirect_to='.urlencode(get_permalink())); ?></p>

	<?php else : ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

		<?php if ( $user_ID ) : ?>
			<p class="small"><?php printf(__('Logged in as <a href="%1$s">%2$s</a>.', 'default'), get_option('siteurl') . '/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'default'); ?>"><?php _e('Log out &raquo;', 'default'); ?></a></p>
		<?php else : ?>
			<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
			<label for="author"><small><?php _e('Name', 'default'); ?> <?php if ($req) _e("(required)", "default"); ?></small></label></p>
			<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
			<label for="email"><small><?php _e('Mail (will not be published)', 'default'); ?> <?php if ($req) _e("(required)", "default"); ?></small></label></p>
			<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
			<label for="url"><small><?php _e('Website', 'default'); ?></small></label></p>
		<?php endif; ?>
		<p class="small"><?php printf(__('<strong>XHTML:</strong> You can use these tags: <code>%s</code>', 'default'), allowed_tags()); ?></p>
		<p><textarea name="comment" id="comment" rows="10" tabindex="4"></textarea></p>
		<p><input class="button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment', 'default'); ?>" /><?php comment_id_fields(); ?></p>
		<?php do_action('comment_form', $post->ID); ?>
		</form>

	<?php endif; // If registration required and not logged in ?>
	</div>

<?php endif; // if you delete this the sky will fall on your head ?>
</div>