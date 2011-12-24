<div class="content-header"><h2><?php _e( "Upss, not found...", "default" ); ?></h2></div>

<div class="post">
	<div class="notice">

		<div id="icon">&nbsp;</div>		

		<div id="box">

			<h3>
<?php
if ( is_page() ) {
	echo(__("The page does not exist", "default"));
} else if ( is_category() ) {
	printf(__("Sorry, but there aren't any posts in the %s category yet.", "default"), single_cat_title('',false));
} else if ( is_search() ) {
	echo(__("Sorry, but you are looking for something that isn&#8217;t here.", "default"));
} else if ( is_date() ) {
	echo(__("Sorry, but there aren't any posts with this date.", "default"));
} else if ( is_author() ) {
	$userdata = get_userdatabylogin(get_query_var('author_name'));
	printf(__("Sorry, but there aren't any posts by %s yet.", "default"), $userdata->display_name);
} else {
	echo(__('No posts found.', 'default'));
}
?>
			</h3>

			<p><?php _e("Try a different search?", "default"); ?></p>
			<?php include (TEMPLATEPATH."/searchform.php"); ?>

			<p><?php _e("Or follow one of these links", "default"); ?></p>
			<ul><?php get_archives('postbypost', 10); ?></ul>

		</div>

		<div class="clear">&nbsp;</div>

	</div>
</div>
