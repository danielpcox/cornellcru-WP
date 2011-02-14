<?php get_header(); ?>
<?php $options = get_option('pb_options'); ?>

<div id="blog">
  <h1>The Cru Blog</h1>

  <!-- original "subscribe" link
  <?php if ($options['header_rss']) : ?>
    <a href="<?php bloginfo('rss2_url'); ?>" id="blog-subscribe" >subscribe</a>
  <?php endif; ?>
-->

  <ul id="filters">
      <?php if ($options['header_rss']) : ?>
        <li><a href="<?php bloginfo('rss2_url'); ?>" >feed</a></li>
      <?php endif; ?>
      <?php if ($options['header_twitter']) : ?>
          <li><a href="<?php echo $options['twitter_url']; ?>" id="twitter" >twitter</a></li>
      <?php endif; ?>
  </ul><!--/#filters-->

  <ul id="posts">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <li class="post">
          <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
          <ul class="meta">
            <li class="date"><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
            <?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author_posts_link(); ?></li><?php endif; ?>
            <!--<li class="comments"><?php comments_popup_link(__('Write comment', 'cruwp'), __('1 comment', 'cruwp'), __('% comments', 'cruwp')); ?></li>--><!-- TODO : enable comments -->
            <?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
          </ul><!--/.meta-->
       <?php the_content(__('[...more]', 'cruwp')); ?>
       <?php wp_link_pages(); ?>
       <!--<?php the_category(' . '); ?>--><!-- TODO : enable categories? -->
       <!--<?php the_tags('<li class="post-tag">', ' . ', '</li>'); ?>--> <!-- TODO : enable tags? -->
       
      </li><!--/.post-->
    <?php endwhile; ?>
  </ul><!--/#posts-->
  <?php else: ?> <!-- that is, if !have_posts() -->
      <div class="common-navi-wrapper">
        <p><?php _e("Sorry, but you are looking for something that isn't here.","cruwp"); ?></p>
      </div>
  <?php endif; ?>

</div><!--/#blog-->

<?php  if (function_exists('wp_pagenavi')) { wp_pagenavi(); } else { include('navigation.php'); } ?>

<!-- We're gonna have to totally redo the pagination styling for wordpress
<ul id="pagination">
</ul>
-->


<?php //get_sidebar(); ?> <!-- TODO: style the sidebar -->
<?php get_footer(); ?>

