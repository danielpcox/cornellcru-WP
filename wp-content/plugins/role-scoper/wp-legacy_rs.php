<?php
// include this (from WP 3.0 code) so hardway terms/pages filters can use it on older WP
if ( ! function_exists( 'wp_parse_id_list' ) ) :
function wp_parse_id_list( $list ) {
	if ( !is_array($list) )
		$list = preg_split('/[\s,]+/', $list);

	return array_unique(array_map('absint', $list));
}
endif;


if ( ! function_exists( 'taxonomy_exists' ) ) :
function taxonomy_exists( $taxonomy ) {
	return is_taxonomy( $taxonomy );
}
endif;


// back compat for older MU versions
if ( IS_MU_RS && ! function_exists( 'is_super_admin' ) ) :
function is_super_admin() {
	return is_site_admin();
}
endif;

if ( ! function_exists('esc_attr') ) :
function esc_attr( $text ) {
	$safe_text = wp_check_invalid_utf8( $text );
	$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
	return apply_filters( 'attribute_escape', $safe_text, $text );
}
endif;

?>