<?php
	global $options;
	foreach ($options as $value) {
	    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }
?>
				</div><!-- end #container -->
				<div class="clear">&nbsp;</div>
			</div><!-- end #wrap -->

			<div id="bottom">
				<div id="footer"><?php df_footer($dfblog_copyright, __('Powered by ' ), __('GoTo top', 'default')); ?></div>
			</div>

		</div><!-- end #wrapper -->
	</div><!-- end #page -->
	<?php wp_footer(); ?>

</body>
</html>
