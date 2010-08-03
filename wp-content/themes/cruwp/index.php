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

  <!-- HEROES AND CAROUSEL -->
  <?php
    $hero_category = "Front Page";
  ?>
  <div id="hero">
    <ul>
      <?php echo wp_list_bookmarks("categorize=0&title_li=&orderby=rating&category_name=".$hero_category); ?>
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
    <?php query_posts($query_string . '&cat=-5'); // exclude news and events category TODO : hardcoded is bad ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <li class="post">
          <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
          <ul class="meta">
            <li class="date"><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
            <!--<?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author_posts_link(); ?></li><?php endif; ?>--><!-- TODO : enable author link? -->
            <?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author(); ?></li><?php endif; ?>
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

<?php if (function_exists('wp_pagenavi')) { wp_pagenavi(); } else { include('navigation.php'); } ?>

<!-- We're gonna have to totally redo the pagination styling for wordpress
<ul id="pagination">
</ul>
-->


<?php //get_sidebar(); ?> <!-- TODO: style the sidebar -->
<?php get_footer(); ?>

