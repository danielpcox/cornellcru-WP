<?php get_header(); ?>

<div id="content">

<?php if (have_posts()) : ?>

	<div class="content-header"><?php include('includes/content-header.inc.php'); ?></div>

<?php while (have_posts()) : the_post(); ?>

	<div class="post" id="post-<?php the_ID(); ?>">
		<h2>
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'default'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="postmetadata">
			<?php df_get_postmetadata( array( "date", "author", "comment" ), 'span' ); ?>
		</div>

		<div class="entry">
			<?php the_content(); ?>
		</div>

		<div class="postmetadata">
			<?php df_get_postmetadata( array( "category", "tag", "edit" ), 'span' ); ?>
		</div>

	</div><!-- end of post -->

<?php endwhile; ?>

<?php df_pagenavigator('<div id="pagenavigator">', '</div>'); ?>

<?php else : /* NO posts */

	if ( '' != get_404_template() )
		include( get_404_template() );
	else
		echo( "<h3><?php _e( 'Upss, not found...', 'default' ); ?></h3>" );

endif; ?>

</div><!-- end #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
