<?php

function scoper_add_custom_taxonomies(&$taxonomies) {
	//global $scoper;
	//$taxonomies =& $scoper->taxonomies->members;
	
	// note: use_term_roles elements are auto-created (and thus eligible for scoping activation via Roles > Realm) based on registered WP taxonomies
	$arr_use_wp_taxonomies = array();

	if ( ! $use_term_roles = get_option( 'scoper_use_term_roles' ) ) {  // TODO: why does scoper_get_option not reflect values updated via scoper_update_option in version update function earlier in same request?
		global $scoper_default_otype_options;		// TODO: is this necessary?
		
		scoper_refresh_default_otype_options();
		$use_term_roles = $scoper_default_otype_options['use_term_roles'];
	}	

	$core_taxonomies = array( 'category', 'link_category', 'nav_menu' );

	foreach( array_keys($use_term_roles) as $src_otype ) 
		if ( is_array( $use_term_roles[$src_otype] ) ) {
			foreach ( array_keys($use_term_roles[$src_otype]) as $taxonomy )
				if ( $use_term_roles[$src_otype][$taxonomy] && ! in_array( $taxonomy, $core_taxonomies ) )
					$arr_use_wp_taxonomies[$taxonomy] = true;
		}

	// Detect and support additional WP taxonomies (just require activation via Role Scoper options panel)
	if ( ! empty($arr_use_wp_taxonomies) ) {
		global $scoper, $wp_taxonomies, $wp_post_types;
		
		if ( defined( 'CUSTAX_DB_VERSION' ) ) {	// Extra support for Custom Taxonomies plugin
			global $wpdb;
			if ( ! empty($wpdb->custom_taxonomies) ) {
				$custom_taxonomies = array();
				$results = $wpdb->get_results( "SELECT * FROM $wpdb->custom_taxonomies" );  // * to support possible future columns
				foreach ( $results as $row )
					$custom_taxonomies[$row->slug] = $row;
			}
		} else
			$custom_taxonomies = array();

		foreach ( $wp_taxonomies as $taxonomy => $wp_tax ) {
			if ( in_array( $taxonomy, $core_taxonomies ) )
				continue;
			
			// taxonomy must be approved for scoping and have a Scoper-defined object type
			if ( isset($arr_use_wp_taxonomies[$taxonomy]) || strpos( $_SERVER['REQUEST_URI'], 'admin.php?page=rs-options' ) ) { // always load taxonomy ID data for Realm Options display
				$tx_otypes = (array) $wp_tax->object_type;

				foreach ( $tx_otypes as $wp_tax_object_type ) {
				
					if ( isset($wp_post_types[$wp_tax_object_type]) || isset( $scoper->data_sources->members['post']->object_types[$wp_tax_object_type] ) )
						$src_name = 'post';
					elseif ( $scoper->data_sources->is_member($wp_tax_object_type) ) 
						$src_name = $wp_tax_object_type;
					elseif ( ! $src_name = $scoper->data_sources->is_member_alias($wp_tax_object_type) )  // in case the 3rd party plugin uses a taxonomy->object_type property different from the src_name we use for RS data source definition
						continue;
						
					// create taxonomies definition if necessary (additional properties will be set later)
					$taxonomies[$taxonomy] = (object) array(
						'name' => $taxonomy,								
						'uses_standard_schema' => 1,	'autodetected_wp_taxonomy' => 1,
						'hierarchical' => $wp_tax->hierarchical,
						'object_source' => $scoper->data_sources->get( $src_name )
					);
					
					$taxonomies[$taxonomy]->requires_term = $wp_tax->hierarchical;	// default all hierarchical taxonomies to strict, non-hierarchical to non-strict

					if ( isset( $custom_taxonomies[$taxonomy] ) && ! empty( $custom_taxonomies[$taxonomy]->plural ) ) {
						$taxonomies[$taxonomy]->display_name = $custom_taxonomies[$taxonomy]->name;
						$taxonomies[$taxonomy]->display_name_plural = $custom_taxonomies[$taxonomy]->plural;
						
						// possible future extension to Custom Taxonomies plugin: ability to specify "required" property apart from hierarchical property (and enforce it in Edit Forms)
						if ( isset( $custom_taxonomies[$taxonomy]->required ) )
							$taxonomies[$taxonomy]->requires_term = $custom_taxonomies[$taxonomy]->required;
					} else {
						$taxonomies[$taxonomy]->display_name = ( ! empty( $wp_tax->singular_label ) ) ? $wp_tax->singular_label : ucwords( __( preg_replace('/[_-]/', ' ', $taxonomy) ) );
						$taxonomies[$taxonomy]->display_name_plural = ( ! empty( $wp_tax->label ) ) ? $wp_tax->label : $taxonomies[$taxonomy]->display_name;			
					}
				}	
			} // endif scoping is enabled for this taxonomy
		} // end foreach taxonomy known to WP core
	} // endif any taxonomies have scoping enabled		
}

function scoper_add_custom_data_sources(&$data_sources) {
	//global $scoper;
	//$data_sources =& $scoper->data_sources->members;

	$custom_types = get_post_types( array(), 'object' );

	$core_types = array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' );
	
	foreach ( $custom_types as $otype ) {
		if ( ! in_array( $otype->name, $core_types ) ) {
			$name = $otype->name;	
			$captype = $otype->capability_type;
			
			$singular_label = ( ! empty($otype->labels->singular_name) ) ? $otype->labels->singular_name : $otype->singular_label;
			$data_sources['post']->object_types[$name] = (object) array( 'val' => $name, 'uri' => array( "wp-admin/add-{$name}.php", "wp-admin/manage-{$name}.php" ), 'display_name' => $singular_label, 'display_name_plural' => $otype->label, 'ignore_object_hierarchy' => true, 'admin_default_hide_empty' => true, 'admin_max_unroled_objects' => 100 );
			
			$data_sources['post']->reqd_caps['read'][$name] = array(
				"published" => 	array( "read" ), 	
				"private" => 	array( "read", "read_private_{$captype}s" )
			);

			$data_sources['post']->reqd_caps['edit'][$name] = array(
				"published" =>	array( "edit_others_{$captype}s", "edit_published_{$captype}s" ),
				"private" => 	array( "edit_others_{$captype}s", "edit_published_{$captype}s", "edit_private_{$captype}s" ), 
				"draft" => 		array( "edit_others_{$captype}s" ),
				"pending" => 	array( "edit_others_{$captype}s" ),
				"future" => 	array( "edit_others_{$captype}s" ),
				"trash" => 		array( "edit_others_{$captype}s" )
			);	
	
			$data_sources['post']->reqd_caps['admin'] = $data_sources['post']->reqd_caps['edit'];
		}
	}
}

function scoper_add_custom_cap_defs( &$cap_defs ) {
	if ( awp_ver( '2.9' ) ) {
		$custom_types = get_post_types( array(), 'object' );

		$core_types = array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' );
		
		foreach ( $custom_types as $otype ) {
			if ( ! in_array( $otype->name, $core_types ) ) {
				$name = $otype->name;
				$captype = $otype->capability_type;

				if ( $captype && ( 'post' != $captype ) ) {
					$cap_defs["read_private_{$captype}s"] =		(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_READ_RS, 		'owner_privilege' => true, 			'status' => 'private' );
					$cap_defs["edit_{$captype}s"] = 			(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_EDIT_RS,		'owner_privilege' => true, 			'no_custom_remove' => true );
					$cap_defs["edit_others_{$captype}s"] =  	(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_EDIT_RS, 		'attributes' => array('others'), 	'base_cap' => "edit_{$captype}s", 		'no_custom_remove' => true  );
					$cap_defs["edit_private_{$captype}s"] =  	(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_EDIT_RS,		'owner_privilege' => true, 			'status' => 'private' );
					$cap_defs["edit_published_{$captype}s"] = 	(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_EDIT_RS,		'status' => 'published' );
					$cap_defs["delete_{$captype}s"] =  			(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_DELETE_RS,	'owner_privilege' => true );
					$cap_defs["delete_others_{$captype}s"] =  	(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_DELETE_RS, 	'attributes' => array('others'),	'base_cap' => "delete_{$captype}s" );
					$cap_defs["delete_private_{$captype}s"] =  	(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_DELETE_RS,	'status' => 'private' );
					$cap_defs["delete_published_{$captype}s"] = (object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_DELETE_RS,	'status' => 'published' );
					$cap_defs["publish_{$captype}s"] = 			(object) array( 'src_name' => 'post', 'object_type' => $name, 'op_type' => OP_PUBLISH_RS );
				}
			}
		}
	}
}

function scoper_add_custom_role_caps( &$role_caps ) {
	$custom_types = get_post_types( array(), 'object' );

	$core_types = array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' );
	
	foreach ( $custom_types as $otype ) {
		if ( ! in_array( $otype->name, $core_types ) ) {
			$captype = $otype->capability_type;

			$role_caps["rs_{$captype}_reader"] = array(
				"read" => true
			);
			
			$role_caps['dasf'] = array( 'hi' );
			
			$role_caps["rs_private_{$captype}_reader"] = array(
				"read_private_{$captype}s" => true,
				"read" => true
			);
			$role_caps["rs_{$captype}_contributor"] = array(
				"edit_{$captype}s" => true,
				"delete_{$captype}s" => true,
				"read" => true
			);
			$role_caps["rs_{$captype}_revisor"] = array(
				"edit_{$captype}s" => true,
				"delete_{$captype}s" => true,
				"read" => true,
				"read_private_{$captype}s" => true,
				"edit_others_{$captype}s" => true
			);
			$role_caps["rs_{$captype}_author"] = array(
				"upload_files" => true,
				"publish_{$captype}s" => true,
				"edit_published_{$captype}s" => true,
				"delete_published_{$captype}s" => true,
				"edit_{$captype}s" => true,
				"delete_{$captype}s" => true,
				"read" => true
			);
			$role_caps["rs_{$captype}_editor"] = array(
				"moderate_comments" => true,
				"delete_others_{$captype}s" => true,
				"edit_others_{$captype}s" => true,
				"upload_files" => true,
				"publish_{$captype}s" => true,
				"delete_private_{$captype}s" => true,
				"edit_private_{$captype}s" => true,
				"delete_published_{$captype}s" => true,
				"edit_published_{$captype}s" => true,
				"delete_{$captype}s" => true,
				"edit_{$captype}s" => true,
				"read_private_{$captype}s" => true,
				"read" => true
			);
		}
	}
}

function scoper_add_custom_role_defs( &$role_defs ) {
	$custom_types = get_post_types( array(), 'object' );
	
	$core_types = array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' );
	
	foreach ( $custom_types as $otype ) {
		if ( ! in_array( $otype->name, $core_types ) ) {
			$name = $otype->name;
			
			$role_defs["rs_{$name}_reader"] = 			(object) array( 'valid_scopes' => array( 'blog' => true, 'term' => true ),  'object_type' => $name, 'anon_user_blogrole' => true );
			$role_defs["rs_private_{$name}_reader"] =	(object) array( 'objscope_equivalents' => array("rs_{$name}_reader") );
		
			$role_defs["rs_{$name}_contributor"] =		(object) array( 'objscope_equivalents' => array("rs_{$name}_revisor") );
			$role_defs["rs_{$name}_author"] =			(object) array( 'valid_scopes' => array( 'blog' => true, 'term' => true ) );
			$role_defs["rs_{$name}_revisor"] = 			(object) array( 'valid_scopes' => array( 'blog' => true, 'term' => true ) );
			$role_defs["rs_{$name}_editor"] = 			(object) array( 'objscope_equivalents' => array("rs_{$name}_author") );
			
			$role_defs["rs_private_{$name}_reader"]->other_scopes_check_role = array( 'private' => "rs_private_{$name}_reader", '' => "rs_{$name}_reader" );
		}
	}
}
?>