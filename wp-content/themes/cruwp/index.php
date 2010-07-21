<?php get_header(); ?>
<?php $options = get_option('pb_options'); ?>
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