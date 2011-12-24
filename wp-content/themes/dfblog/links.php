<?php
/*
Template Name: Links
*/
?>

<?php get_header(); ?>

<div id="content">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div class="post" id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>

		<div class="entry">
			<?php the_content(); ?>
		</div>
		<div class="entry">	
			<ul class="links">
<?php 
	// See http://codex.wordpress.org/Template_Tags/wp_list_bookmarks
	wp_list_bookmarks('title_li=&categorize=0');
?>
			</ul>
		</div>
		<div class="postmetadata">
			<?php df_get_postmetadata( array( "edit" ), 'span' ); ?>
		</div>

	</div>
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
