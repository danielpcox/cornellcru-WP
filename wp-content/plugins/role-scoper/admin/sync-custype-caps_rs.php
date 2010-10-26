<?php

// If custom post types are defined, add their corresponding capabilities to default WP roles
function scoper_sync_wp_custype_caps( $requested_blog_id = '' ) {
	global $wp_roles;
	
	$custom_types = array_diff( get_post_types(), array( 'post', 'page', 'attachment', 'revision' ) );
	
	update_option( 'scoper_logged_custom_types', $custom_types );
	
	$add_caps = array_fill_keys( array( 'administrator', 'editor', 'revisor', 'author', 'contributor' ), array() );

	foreach ( $custom_types as $name ) {
		$add_caps['administrator'] = array_merge( $add_caps['administrator'],	array( "read_private_{$name}s", "edit_{$name}s", "edit_others_{$name}s", "edit_private_{$name}s", "edit_published_{$name}s", "delete_{$name}s", "delete_others_{$name}s", "delete_private_{$name}s", "delete_published_{$name}s", "publish_{$name}s" ) );
		$add_caps['editor'] = array_merge( $add_caps['editor'], 				array( "read_private_{$name}s", "edit_{$name}s", "edit_others_{$name}s", "edit_private_{$name}s", "edit_published_{$name}s", "delete_{$name}s", "delete_others_{$name}s", "delete_private_{$name}s", "delete_published_{$name}s", "publish_{$name}s" ) );
		$add_caps['revisor'] = array_merge( $add_caps['revisor'], 				array( "edit_{$name}s", "edit_others_{$name}s", "delete_{$name}s", "delete_others_{$name}s" ) );
		$add_caps['author'] = array_merge( $add_caps['author'], 				array( "edit_{$name}s", "edit_published_{$name}s", "delete_{$name}s", "delete_published_{$name}s", "publish_{$name}s" ) );
		$add_caps['contributor'] = array_merge( $add_caps['contributor'], 		array( "edit_{$name}s", "delete_{$name}s" ) );
	}

	if ( ! $add_caps )
		return;
	
	// modify roles for all MU blogs
	if ( IS_MU_RS ) {
		global $wpdb, $blog_id;

		if ( $requested_blog_id )
			$blog_ids = (array) $requested_blog_id;
		else
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		
		$orig_blog_id = $blog_id;	
	} else
		$blog_ids = array( '' );

	foreach ( $blog_ids as $id ) {
		if ( $id )
			switch_to_blog( $id );
	
		foreach ( array_keys($add_caps) as $role_name ) {
			if ( $role = $wp_roles->get_role( $role_name ) ) {
				foreach ( $add_caps[$role_name] as $cap_name ) {
					if ( empty( $role->capabilities[$cap_name] ) )
						$role->add_cap( $cap_name );
				}
			}
		}
	}
	
	if ( count($blog_ids) > 1 )
		switch_to_blog( $orig_blog_id );	
}
?>