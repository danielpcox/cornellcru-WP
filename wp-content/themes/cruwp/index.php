<?php get_header(); ?>
<?php $options = get_option('pb_options'); ?>

<div id="hero">
  <ul>
    <li><a href="http://twitter.com/cornellcru"><img src="<?php bloginfo('template_url'); ?>/images/carousel_1.png" /></a></li>
    <li><a href="http://google.com"><img src="<?php bloginfo('template_url'); ?>/images/carousel_2.png" /></a></li>
    <li><a href="http://yahoo.com"><img src="<?php bloginfo('template_url'); ?>/images/carousel_3.png" /></a></li>
    <li><a href="http://github.com"><img src="<?php bloginfo('template_url'); ?>/images/carousel_4.png" /></a></li>
  </ul>
</div><!--/#hero-->

<ul id="call-to-action">
  <li class="cta" id="real-life">
      <a href="<?php echo bloginfo('url') ?>/real-life/">Real Life<span class='subtitle'>our weekly large group meetings</span></a>
  </li>
  
  <li class="cta" id="jesus-and-the-gospel">
    <a href="<?php echo bloginfo('url') ?>/jesus-and-the-gospel/">Jesus &amp;<br />The Gospel</a>
  </li>

  <li class="cta" id="community-groups">
    <a href="<?php echo bloginfo('url') ?>/community-groups/">Community Groups<span class='subtitle'>our small group meetings</span></a>
  </li>
</ul><!--/#call-to-action-->

<div id="blog">
  <h1>The Cru Blog</h1>
  <?php if ($options['header_rss']) : ?>
    <a href="<?php bloginfo('rss2_url'); ?>" id="blog-subscribe" title="<?php _e('Entries RSS','cruwp'); ?>" >subscribe</a>
  <?php endif; ?>
<!-- Gonna have to figure out how to do these filters using the wordpress stuff
  <ul id="filters">
    <li><%= link_to 'By Date' %></li>
    <li><%= link_to 'By Author' %></li>
  </ul>--><!--/#filters-->

  <ul id="posts">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <li class="post">
          <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
          <ul class="meta">
            <li class="date"><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
            <?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author_posts_link(); ?></li><?php endif; ?>
            <li class="comments"><?php comments_popup_link(__('Write comment', 'cruwp'), __('1 comment', 'cruwp'), __('% comments', 'cruwp')); ?></li>
            <?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
          </ul><!--/.meta-->
       <?php the_content(__('Read more', 'cruwp')); ?>
       <?php wp_link_pages(); ?>
       <?php the_category(' . '); ?>
       <?php the_tags('<li class="post-tag">', ' . ', '</li>'); ?>
       
      </li><!--/.post-->
    <?php endwhile; ?>
  </ul><!--/#posts-->
  <?php else: ?> <!-- that is, if !have_posts() -->
      <div class="common-navi-wrapper">
        <p><?php _e("Sorry, but you are looking for something that isn't here.","cruwp"); ?></p>
      </div>
  <?php endif; ?>

</div><!--/#blog-->

<?php if (function_exists('wp_pagenavi')) { wp_pagenavi(); } else { include('navigation.php'); } ?>

<!-- We're gonna have to totally redo the pagination styling for wordpress
<ul id="pagination">
</ul>
-->


<?php //get_sidebar(); ?> <!-- TODO: style the sidebar -->
<?php get_footer(); ?>

