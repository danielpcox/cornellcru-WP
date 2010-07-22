<?php get_header(); ?>

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
      <div id="sidebar">
        <ul class="nav">
          <?php echo $children; ?>
        </ul><!--/.nav-->
      </div><!--/#sidebar-->

  <?php } ?>
<div id="main-cont">
      <h1><?php the_title(); ?></h1>
      <ul class="meta">
        <li class="date"><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
        <li class="author"><?php _e('by ','cruwp'); ?><?php the_author(); ?></li>
        <?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
      </ul><!--/.meta-->

      <div id="posts">
            <div class="post">
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
