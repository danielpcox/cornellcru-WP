<?php get_header(); ?>
<?php $options = get_option('pb_options'); ?>

<script lang=javascript>
	$(document).ready(function(){	
		$("#hero").easySlider({
			auto: true, 
			continuous: true,
			controlsShow: true,
            numeric: true,
			speed: 700,
			pause: 5000
		});
        $("#hero").css({ width: "880px",height: "350px" });
	});	
</script>

<div id="hero">
  <ul>
    <li><a href="http://twitter.com/cornellcru"><img src="<?php bloginfo('template_url'); ?>/images/carousel_1.png" /></a></li>
    <li><a href="http://google.com"><img src="<?php bloginfo('template_url'); ?>/images/carousel_2.png" /></a></li>
    <li><a href="http://yahoo.com"><img src="<?php bloginfo('template_url'); ?>/images/carousel_3.png" /></a></li>
    <li><a href="http://github.com"><img src="<?php bloginfo('template_url'); ?>/images/carousel_4.png" /></a></li>
  </ul>
</div><!--/#hero-->
















  <div id="middle-contents" class="clearfix">

   <div id="left-col">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post">
     <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
     <ul class="post-info">
      <li><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
      <?php if ($options['author']) : ?><li><?php _e('By ','cruwp'); ?><?php the_author_posts_link(); ?></li><?php endif; ?>
      <li class="write-comment"><a href="<?php the_permalink() ?>#comments"><?php _e('Write comment','cruwp'); ?></a></li>
      <?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
     </ul>
     <div class="post-content">
       <?php the_content(__('Read more', 'cruwp')); ?>
       <?php wp_link_pages(); ?>
     </div>
    </div>
    <div class="post-meta">
     <ul class="clearfix">
      <?php if($options['post_meta_type'] == 'category') { ?>
      <li class="post-category"><?php the_category(' . '); ?></li>
      <?php } else { ?>
      <?php the_tags('<li class="post-tag">', ' . ', '</li>'); ?>
      <?php } ?>
      <li class="post-comment"><?php comments_popup_link(__('Write comment', 'cruwp'), __('1 comment', 'cruwp'), __('% comments', 'cruwp')); ?></li>
     </ul>
    </div>

<?php endwhile; ?>

<?php if (function_exists('wp_pagenavi')) { wp_pagenavi(); } else { include('navigation.php'); } ?>

<a href="#wrapper" id="back-top" class="clear"><?php _e('Return top','cruwp'); ?></a>

<?php else: ?>
    <div class="common-navi-wrapper">
      <p><?php _e("Sorry, but you are looking for something that isn't here.","cruwp"); ?></p>
    </div>
<?php endif; ?>

   </div><!-- #left-col end -->

   <?php get_sidebar(); ?>

  </div><!-- #middle-contents end -->

<?php get_footer(); ?>
