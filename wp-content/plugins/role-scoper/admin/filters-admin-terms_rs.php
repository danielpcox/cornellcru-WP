<?php

add_action( 'admin_head', 'scoper_admin_terms_js' );

// hide the "Add Term" UI if the logged user doesn't have manage_terms cap site-wide
function scoper_admin_terms_js() {
	if ( ! empty( $_REQUEST['taxonomy'] ) ) {  // using this with edit-link-categories
		if ( $tx_obj = get_taxonomy( $_REQUEST['taxonomy'] ) )
			$cap_name = $tx_obj->cap->manage_terms;
	}

	if ( empty($cap_name) )
		$cap_name = 'manage_categories';

	// Concern here is for addition of top level terms.  Subcat addition attempts will already be filtered by has_cap filter.
	if ( cr_user_can( $cap_name, BLOG_SCOPE_RS ) )
		return;
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready( function($) {
	$('#col-left').hide();
});
/* ]]> */
</script>
<?php
}

?>