<?php
/*
Template Name:One Recent Post, Page with Hero
*/
?>
<?php get_header(); ?>

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


  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <!-- HEROES AND CAROUSEL -->
  <?php
    $custom_fields = get_post_custom();
    $hero_category = $custom_fields['hero_category'][0];
  ?>
  <div id="hero">
    <ul>
      <?php echo wp_list_bookmarks("categorize=0&title_li=&orderby=rating&category_name=".$hero_category); ?>
    </ul>
  </div><!--/#hero-->



  <?php
    if($post->post_parent) {
    $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
    $titlenamer = get_the_title($post->post_parent);
    }

    else {
    $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
    $titlenamer = get_the_title($post->ID);
    }
    if ($children) { ?>
      <div id="sidebar">
        <ul class="nav">
          <?php echo $children; ?>
        </ul><!--/.nav-->
      </div><!--/#sidebar-->

  <?php } ?>
<div id="main-cont">
      <h1><?php the_title(); ?></h1>

      <div id="posts">
          <?php
            $my_query = new WP_Query('posts_per_page=1&category_name='.$hero_category);
            while ($my_query->have_posts()) : $my_query->the_post();
          ?>

          <div class="post">
            <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
            <ul class="meta">
              <li class="date"><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
              <!--<?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author_posts_link(); ?></li><?php endif; ?>--><!-- TODO : enable author link? -->
              <?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author(); ?></li><?php endif; ?>
              <!--<li class="comments"><?php comments_popup_link(__('Write comment', 'cruwp'), __('1 comment', 'cruwp'), __('% comments', 'cruwp')); ?></li>--><!-- TODO : enable comments -->
              <?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
            </ul><!--/.meta-->
            <?php the_content(); ?>
          </div><!--/#posts-->
          <?php endwhile; ?>
      </div>

  <?php endwhile; else: ?>
  <div id="main-cont">
  <h1>Page Not Found</h1>
      <div id="posts">
        <div class="post">
          <p><?php _e("Sorry, but you are looking for something that isn't here.","cruwp"); ?></p>
        </div>
      </div><!--/.post-->
  <?php endif; ?>

  </div> <!-- #main-cont -->


<?php get_footer(); ?>

