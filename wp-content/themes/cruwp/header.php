<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php wp_title(''); if (function_exists('is_tag') and is_tag()) { ?><?php } if (is_archive()) { ?><?php } elseif (is_search()) { ?><?php echo $s; } if ( !(is_404()) and (is_search()) or (is_single()) or (is_page()) or (function_exists('is_tag') and is_tag()) or (is_archive()) ) { ?><?php _e(' | '); ?><?php } ?><?php bloginfo('name'); ?></title>
<meta name="description" content="<?php if (is_home()): echo bloginfo('description'); else: echo the_title(); endif; ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" /> 
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/comment-style.css" type="text/css" media="screen" />
<?php if (strtoupper(get_locale()) == 'JA') : ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/japanese.css" type="text/css" media="screen" />
<?php elseif (strtoupper(get_locale()) == 'HE_IL' || strtoupper(get_locale()) == 'FA_IR') : ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/rtl.css" type="text/css" media="screen" />
<?php endif; ?>

<!--[if lt IE 7]>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/iepngfix.js"></script>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie6.css" type="text/css" media="screen" />
<![endif]--> 

<?php $options = get_option('pb_options'); if($options['image_style']) { ?>
<style type="text/css">
.post img, .post a img { border:1px solid #222; padding:5px; margin:0;  background:#555; }
.post a:hover img { border:1px solid #849ca0; background:#59847d; }
.post img.wp-smiley { border:0px; padding:0px; margin:0px; background:none; }
</style>
<?php }; ?>

<?php wp_enqueue_script( 'jquery' ); ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?> 
<?php wp_head(); ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/scroll.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jscript.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/comment.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/easySlider1.7.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/application.js"></script>
<!-- Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17839609-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
    <div id="header">
      <a id="logo" href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a><!--/#logo-->
        <?php if ($options['header_search']) : ?>
         <?php if ($options['use_google_search']) : ?>
         <form action="http://www.google.com/cse" method="get" id="search">
          <input type="text" value="<?php _e('Google Search','cruwp'); ?>" name="q" id="search-box" />
         </form>
         <?php else: ?>
          <form method="get" id="search" action="<?php bloginfo('home'); ?>/">
           <input type="text" value="<?php _e('Search','cruwp'); ?>" name="s" id="search-box" />
          </form>
         <?php endif; ?>
        <?php endif; ?>
    </div><!--/#header-->

    <ul id="navigation">
      <li class="<?php if (is_tree('front-page')) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>/"><?php _e('HOME','cruwp'); ?></a></li>
      <li class="<?php if (is_tree('im-new-here')) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo bloginfo('url'); ?>/im-new-here/">I'm New Here</a></li>
      <li class="<?php if (is_tree('news-and-events')) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo bloginfo('url'); ?>/news-and-events/">News and Events</a></li>
      <li class="<?php if ((!is_paged() && is_home()) || (is_paged())) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo bloginfo('url');?>/blog">Blog</a></li>
      <li class="<?php if (is_tree('about-us')) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo bloginfo('url'); ?>/about-us/">About Us</a></li>
      <!--<?php wp_list_pages('sort_column=menu_order&depth=0&title_li=&include=' . $options['exclude_pages']); ?>--><!-- using exclude pages as if they were actually INCLUDE pages -->
    </ul><!--/#navigation-->
