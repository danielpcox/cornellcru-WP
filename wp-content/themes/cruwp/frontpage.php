<?php /* Template Name: Front Page*/ ?>
<?php get_header(); ?>
<?php $options = get_option('pb_options'); ?>

<!-- HEROES AND CAROUSEL -->
<?php
    $hero_category = "Front Page";
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


<!-- We're gonna have to totally redo the pagination styling for wordpress
<ul id="pagination">
</ul>
-->


<?php //get_sidebar(); ?> <!-- TODO: style the sidebar -->
<?php get_footer(); ?>