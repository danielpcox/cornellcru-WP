<?php get_header(); ?>

<div id="content" class="narrowcolumn">

<!-- This sets the $curauth variable -->

    <?php
    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
    ?>

 <!---   <h2>About: <?php echo $curauth->name; ?></h2>
    <dl>
        <dt>Website</dt>
        <dd><a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></dd>
        <dt>Profile</dt>
        <dd><?php echo $curauth->user_description; ?></dd>
    </dl> -->

<div id="blog">
    <h1>Posts by <?php echo $curauth->display_name; ?>:</h1>

    <ul id="posts"> <!-- The Loop -->
    	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <li class="post">
        	<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
            <!--<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
            <?php the_title(); ?></a>, 
            <?php the_time('d M Y'); ?> in <?php the_category('&');?>-->
            
            <ul class="meta">
            	<li class="date"><?php the_time(__('F jS, Y', 'cruwp')) ?></li>
            	<?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author_posts_link(); ?></li><?php endif; ?><!-- TODO : enable author link? -->
            	<!--<?php if ($options['author']) : ?><li class="author"><?php _e('by ','cruwp'); ?><?php the_author(); ?></li><?php endif; ?>-->
            	<!--<li class="comments"><?php comments_popup_link(__('Write comment', 'cruwp'), __('1 comment', 'cruwp'), __('% comments', 'cruwp')); ?></li>--><!-- TODO : enable comments -->
            	<?php edit_post_link(__('[ EDIT ]', 'cruwp'), '<li class="post-edit">', '</li>' ); ?>
          	</ul><!--/.meta-->
          	
			<?php the_content(__('[...more]', 'cruwp')); ?>
       		<?php wp_link_pages(); ?>
       		<!--<?php the_category(' . '); ?>--><!-- TODO : enable categories? -->
       		<!--<?php the_tags('<li class="post-tag">', ' . ', '</li>'); ?>--> <!-- TODO : enable tags? --> 
        </li>
    	<?php endwhile; else: ?> <!-- that is, if !have_posts() -->
      <div class="common-navi-wrapper">
        <p><?php _e("Sorry, but you are looking for something that isn't here.","cruwp"); ?></p>
      </div>
  <?php endif; ?>

<!-- End Loop -->
    </ul>
</div> 
</div>
<!-- <?php get_sidebar(); ?> -->
<?php get_footer(); ?>