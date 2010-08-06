<?php
/*
Template Name:Page Content Under Hero
*/
?>
<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<!-- HEROES AND CAROUSEL -->
<?php
    $custom_fields = get_post_custom();
    $hero_category = $custom_fields['hero_category'][0];
?>
<?php $only_one_hero = (count(get_bookmarks("categorize=0&title_li=&orderby=rating&category_name=".$hero_category)) == 1); ?>

<script lang=javascript>
	$(document).ready(function(){	
		$("#hero").easySlider({
      auto: <?php if ($only_one_hero) { echo "false"; } else { echo "true"; } ?>, 
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
      <?php
        if ($custom_fields['embed_hero']) { echo $custom_fields['embed_hero'][0]; } else {
      ?>
      <?php echo wp_list_bookmarks("categorize=0&title_li=&orderby=rating&category_name=".$hero_category); } ?>
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
<div id="blog">
    <div>&nbsp;</div>
      <div id="posts">
          <div class="post">
            <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
            <ul class="meta">
              <li class="date">Updated on <?php the_modified_time(__('F jS, Y', 'cruwp')) ?></li>
              <li class="author">by <?php the_author(); ?></li>
              <?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
            </ul><!--/.meta-->
            <?php the_content(); ?>
          </div><!--/#posts-->
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

