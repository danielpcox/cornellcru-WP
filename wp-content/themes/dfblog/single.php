<?php get_header(); ?>

<div id="content">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div class="post" id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>

		<div class="postmetadata">
			<?php df_get_postmetadata( array( "date", "author", "comment" ), 'span' ); ?>
		</div>

			<div class="entry">
				<?php the_content(); ?>
			</div>

		<div class="postmetadata">
			<?php df_get_postmetadata( array( "category", "tag", "edit" ), 'span' ); ?>
		</div>
			
		<p class="small">
		<?php printf(__("You can follow any responses to this entry through the <a href='%s'>RSS 2.0</a> feed.", "default"), get_post_comments_feed_link()); ?> 
		<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
			// Both Comments and Pings are open ?>
			<?php printf(__('You can <a href="#respond">leave a response</a>, or <a href="%s" rel="trackback">trackback</a> from your own site.', 'default'), trackback_url(false)); ?>
		<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
			// Only Pings are Open ?>
			<?php printf(__('Responses are currently closed, but you can <a href="%s" rel="trackback">trackback</a> from your own site.', 'default'), trackback_url(false)); ?>
		<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
			// Comments are open, Pings are not ?>
			<?php _e('You can skip to the end and leave a response. Pinging is currently not allowed.', 'default'); ?>
		<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
			// Neither Comments, no Pings are open ?>
			<?php _e('Both comments and pings are currently closed.', 'default'); ?>
		<?php } ?>
		</p>

	</div><!-- end of post -->

	<?php comments_template(); ?>

<?php endwhile; ?>

<?php else : /* NO posts */

	if ( '' != get_404_template() )
		include( get_404_template() );
	else
		echo( "<h3><?php _e( 'Upss, not found...', 'default' ); ?></h3>" );

endif; ?>

</div>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>
