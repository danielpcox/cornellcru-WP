<?php
/*
Template Name:No sidebar, No comment
*/
?>
<?php get_header(); ?>

<div id="main-cont">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
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
      <h2> <? echo $titlenamer ?> </h2>

      <div id="sidebar">
        <ul class="nav">
          <?php echo $children; ?>
        </ul><!--/.nav-->
      </div><!--/#sidebar-->


      <h1>I Am New Here</h1>

      <div id="posts">
            <div class="post">
              <?php the_content(); ?>
            </div><!--/#posts-->
      </div>
  <?php } ?>
  <?php endwhile; else: ?>
      <h1>Page Not Found</h1>
      <div id="posts">
        <div class="post">
          <p><?php _e("Sorry, but you are looking for something that isn't here.","cruwp"); ?></p>
        </div>
      </div><!--/.post-->
  <?php endif; ?>

</div>


<?php get_footer(); ?>
