<h2>
<?php
	if(is_home()) {
		_e( 'Latest Publications', 'default' );

	} else if (is_category()) {
		printf(__('Archive for the &#8216;%s&#8217; Category', 'default'), single_cat_title('', false));

	} else if(is_tag()) {
		printf(__('Posts Tagged &#8216;%s&#8217;', 'default'), single_tag_title('', false) );

	} else if(is_day()) {
		printf(_c('Archive for %s|Daily archive page', 'default'), get_the_time(get_option('date_format')));

	} else if(is_month()) {
		printf(_c('Archive for %s|Monthly archive page', 'default'), get_the_time(__('F, Y', 'default')));

	} else if(is_year()) {
		printf(_c('Archive for %s|Yearly archive page', 'default'), get_the_time(__('Y', 'default')));

	} else if(is_author()) {
		_e('Author Archive', 'default');

	}else if(is_search()) {
		_e('Search results for ','default');
		?><span class="highlight"><?php echo the_search_query(); ?></span><?php
	}
?>
</h2>
