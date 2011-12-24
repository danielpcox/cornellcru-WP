<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml'); ?>>

<?php
	global $options;
	foreach ($options as $value) {
	    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }
?>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php if (is_home()) {
		echo bloginfo('name');
	} elseif (is_category()) {
		echo __('Category &raquo; ', 'blank'); wp_title('&laquo; @ ', TRUE, 'right');
		echo bloginfo('name');
	} elseif (is_tag()) {
		echo __('Tag &raquo; ', 'blank'); wp_title('&laquo; @ ', TRUE, 'right');
		echo bloginfo('name');
	} elseif (is_search()) {
		echo __('Search results &raquo;', 'blank');
		echo the_search_query();
		echo '&laquo; @ ';
		echo bloginfo('name');
	} elseif (is_404()) {
		echo '404 '; wp_title(' @ ', TRUE, 'right');
		echo bloginfo('name');
	} else {
		echo wp_title(' @ ', TRUE, 'right');
		echo bloginfo('name');
	} ?></title>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/custom.css" type="text/css" media="screen" />
<style type="text/css">
	body    { background: url(<?php echo get_template_directory_uri(); ?>/images/styles/<?php echo $dfblog_bg_style; ?>/bg-page.png) 50% repeat; }
	#page   { background: url(<?php echo get_template_directory_uri(); ?>/images/styles/<?php echo $dfblog_hd_style; ?>/bg-head.png) 50% 0 repeat-x; }
	#head   { background: url(<?php echo get_template_directory_uri(); ?>/images/styles/Default/head-bg.png) 50% 0 no-repeat; }
	#wrap   { background: url(<?php echo get_template_directory_uri(); ?>/images/styles/Default/wrap-bg.png) 50% 0 repeat-y; }
	#bottom { background: url(<?php echo get_template_directory_uri(); ?>/images/styles/Default/footer-bg.png) 50% 0 no-repeat;	}
</style>
<!--[if lte IE 7]>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie.css" type="text/css" media="screen" />
<![endif]-->
<?php
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
	wp_head();
?>
<script type="text/javascript" charset="utf-8">var $template_directori_uri = '<?php echo get_template_directory_uri(); ?>';</script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/lib/include.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/mad.jquery.js" charset="utf-8"></script>
</head>

<body>
	<div id="page">
		<div id="wrapper">
			<div id="header">

				<div id="head">
					<div id="usermenu">
						<div class="caption">
							<ul>
								<?php if( is_user_logged_in() ) {
										global $current_user;
										get_currentuserinfo();
										echo '<li>'.sprintf(__('Howdy, <a href="%1$s" title="Edit your profile">%2$s</a>', 'default'), get_template_directory_uri()."/../../../wp-admin/profile.php", $current_user->user_login ).'</li>';
									}
								?>
								<?php wp_register(); ?>
								<li class="last"><?php wp_loginout(); ?></li>
							</ul>
						</div>
						<div class="clear tab">
							<a href="#" title="<?php _e('Users', 'default'); ?>">
								<img src="<?php echo get_template_directory_uri(); ?>/images/icons/arrow-dn.png" alt="btn" />
								<img src="<?php echo get_template_directory_uri(); ?>/images/icons/arrow-up.png" alt="btn" style="display: none" />
							</a>
						</div>
					</div>
					<!-- Text version -->
					<?php if ($dfblog_logo_visibility == "off") { ?>
					<div class="logotext"><a href="<?php echo get_option('home'); ?>/" title="<?php _e('A link to home page', 'default'); ?>"><?php bloginfo('name'); ?></a></div>
					<div class="slogan"><?php bloginfo('description'); ?></div>
					<?php } else { ?>
					<!-- Image version -->
					<div class="logoimg"><a href="<?php echo get_option('home'); ?>/" title="<?php _e('A link to home page', 'default'); ?>" alt="logo">
						<img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" />
					</a></div>
					<?php } ?>
				</div>

				<div id="menubar">
					<div id="mainmenu">
					<?php
						$indexpage_class = "page_item";
						if ( is_home() ) { $indexpage_class = " current_page_item";	}
					?>
						<ul class="sf-menu">

							<li class="<?php echo $indexpage_class; ?>"><a href="<?php echo get_settings('home'); ?>"><?php echo $dfblog_home_label ?></a></li>

							<?php wp_list_pages('sort_column=menu_order&title_li='); /* old -> wp_list_pages('title_li=&depth=1'); */ ?>

							<?php if ($dfblog_feed_visibility == "on") { ?>
							<li class="page_item"><a href="<?php bloginfo('rss2_url'); ?>"  title="<?php bloginfo('name'); ?> RSS Feed"><?php _e('Subscribe to Feed'); ?></a></li>
							<?php } ?>

							<li class="page_last">&nbsp;</li>

						</ul>
					</div>

				</div>

				<div id="searchform">
					<?php include (TEMPLATEPATH . '/searchform.php'); ?>
				</div>

				<?php if(function_exists('bcn_display') && !is_home()) { /* Generate the Breadcrumbs NavXT if is installed */ ?>
				<div id="breadcrumb"><?php bcn_display(); ?></div>
				<?php } ?>

			</div><!-- End header --> 

			<div id="wrap">
				<div id="container">