<?php

class ScoperHardwayParent {

	function flt_dropdown_pages($orig_options_html) {
		global $scoper, $post_ID, $post;
			
		//log_mem_usage_rs( 'start flt_dropdown_pages()' );
		if ( empty($post_ID) )
			$object_id = $scoper->data_sources->detect('id', 'post', 0, 'post');
		else
			$object_id = $post_ID;
		
		if ( $object_id )
			$stored_parent_id = $scoper->data_sources->detect('parent', 'post', $object_id);
		else
			$stored_parent_id = 0;
			
		//if ( is_content_administrator_rs() )	// WP 2.7 excludes private pages from Administrator's parent dropdown
		//	return $orig_options_html;
		
		if ( is_content_administrator_rs() ) {
			$can_associate_main = true;
			
		} elseif ( ! scoper_get_option( 'lock_top_pages' ) ) {
			global $current_user;
			$reqd_caps = array('edit_others_pages');
			$roles = $scoper->role_defs->qualify_roles($reqd_caps, '');
			
			$can_associate_main = array_intersect_key( $roles, $current_user->blog_roles[ANY_CONTENT_DATE_RS] );
		} else
			$can_associate_main = false;
		
		if ( awp_ver( '3.0' ) )
			$is_new = ( $post->post_status == 'auto-draft' );
		else
			$is_new = ( $object_id < 1 );

		// Generate the filtered page parent options, but only if user can de-associate with main page, or if parent is already non-Main
		if ( $can_associate_main || $is_new || $stored_parent_id ) {
			if ( $is_new ) $object_id = '0';
			$options_html = ScoperHardwayParent::dropdown_pages($object_id, $stored_parent_id );
		} else {
			$options_html = '';
		}
		
		$object_type = awp_post_type_from_uri();
		
		// User can't associate or de-associate a page with Main page unless they have edit_pages blog-wide.
		// Prepend the Main Page option if appropriate (or, to avoid submission errors, if we generated no other options)
		if ( ( strpos( $orig_options_html, __('Main Page (no parent)') ) || ( 'page' == $object_type ) ) 
		&& ( $can_associate_main || ( ! $is_new && ! $stored_parent_id ) || empty($options_html) ) ) {
			$current = ( $stored_parent_id ) ? '' : ' selected="selected"';
			$option_main = "\t" . '<option value=""' . $current . '> ' . __('Main Page (no parent)') . "</option>";
		} else
			$option_main = '';
		
		//return "<select name='parent_id' id='parent_id'>\n" . $option_main . $options_html . '</select>';
		
		//log_mem_usage_rs( 'end flt_dropdown_pages()' );
		
		
		// can't assume name/id for this dropdown (Quick Edit uses "post_parent")
		$mat = array();
		preg_match("/<select([^>]*)>/", $orig_options_html, $mat);
		
		// If the select tag was not passed in, don't pass it out
		if ( ! empty($mat[1]) )
			return "<select{$mat[1]}>\n" . $option_main . $options_html . '</select>';
			
		// (but if core dropdown_pages passes in a nullstring, we need to insert the missing select tag).  TODO: core patch to handle this more cleanly
		elseif ( ! $orig_options_html )
			return "<select name=\"page_id\" id=\"page_id\">\n" . $option_main . $options_html . '</select>';
		
		else
			return $option_main . $options_html;
	}
	
	function dropdown_pages($object_id = '', $stored_parent_id = '') {
		global $scoper, $wpdb;
		$defaults = array( 'depth' => 0 );
		$args = array_merge( $defaults, (array) $args );
		extract($args);

		// buffer titles in case they are filtered on get_pages hook
		$titles = ScoperHardwayParent::get_page_titles();
		
		if ( ! is_numeric($object_id) ) {
			global $post_ID;
			
			if ( empty($post_ID) )
				$object_id = $scoper->data_sources->detect('id', 'post', 0, 'post');
			else
				$object_id = $post_ID;
		}
		
		if ( $object_id && ! is_numeric($stored_parent_id) )
			$stored_parent_id = $scoper->data_sources->detect('parent', 'post', $object_id);
		
		// make sure the currently stored parent page remains in dropdown regardless of current user roles
		if ( $stored_parent_id ) {
			$preserve_or_clause = " $wpdb->posts.ID = '$stored_parent_id' ";
			$args['preserve_or_clause'] = array();
			foreach (array_keys( $scoper->data_sources->member_property('post', 'statuses') ) as $status_name )
				$args['preserve_or_clause'][$status_name] = $preserve_or_clause;
		}
		
		// alternate_caps is a 2D array because objects_request / objects_where filter supports multiple alternate sets of qualifying caps
		$args['force_reqd_caps']['page'] = array();
		foreach (array_keys( $scoper->data_sources->member_property('post', 'statuses') ) as $status_name )
			$args['force_reqd_caps']['page'][$status_name] = array('edit_others_pages');
			
		$args['alternate_reqd_caps'][0] = array('create_child_pages');
		
		$all_pages_by_id = array();
		if ( $results = scoper_get_results( "SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_type = 'page'" ) )
			foreach ( $results as $row )
				$all_pages_by_id[$row->ID] = $row;

		$object_type = awp_post_type_from_uri();
				
		// Editable / associable draft and pending pages will be included in Page Parent dropdown in Edit Forms, but not elsewhere
		if ( is_admin() && ( 'page' != $object_type ) )
			$status_clause = "AND $wpdb->posts.post_status IN ('publish', 'private')";
		else
			$status_clause = "AND $wpdb->posts.post_status IN ('publish', 'private', 'pending', 'draft')";

		$qry_parents = "SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_type = 'page' $status_clause ORDER BY menu_order";
		
		$qry_parents = apply_filters('objects_request_rs', $qry_parents, 'post', 'page', $args);

		$filtered_pages_by_id = array();
		if ( $results = scoper_get_results($qry_parents) )
			foreach ( $results as $row )
				$filtered_pages_by_id [$row->ID] = $row;
			
		$hidden_pages_by_id = array_diff_key( $all_pages_by_id, $filtered_pages_by_id );

		// temporarily add in the hidden parents so we can order the visible pages by hierarchy
		$pages = ScoperHardwayParent::add_missing_parents($filtered_pages_by_id, $hidden_pages_by_id, 'post_parent');

		// convert keys from post ID to title+ID so we can alpha sort them
		$args['pages'] = array();
		foreach ( array_keys($pages) as $id )
			$args['pages'][ $pages[$id]->post_title . chr(11) . $id ] = $pages[$id];

		// natural case alpha sort
		uksort($args['pages'], "strnatcasecmp");
	
		$args['pages'] = ScoperHardwayParent::order_by_hierarchy($args['pages'], 'ID', 'post_parent');

		// take the hidden parents back out
		foreach ( $args['pages'] as $key => $page )
			if ( isset( $hidden_pages_by_id[$page->ID] ) )
				unset( $args['pages'][$key] );

		$output = '';
		
		// restore buffered titles in case they were filtered on get_pages hook
		scoper_restore_property_array( $args['pages'], $titles, 'ID', 'post_title' );
		
		if ( $object_id ) {
			$args['object_id'] = $object_id;
			$args['retain_page_ids'] = true; // retain static log to avoid redundant entries by subsequent call with use_parent_clause=false
			ScoperHardwayParent::walk_parent_dropdown($output, $args, true, $stored_parent_id);
		}
	
		// next we'll add disjointed branches, but don't allow this page's descendants to be offered as a parent
		$arr_parent = array();
		$arr_children = array();
		
		if ( $results = scoper_get_results("SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = 'page' $status_clause") ) {
			foreach ( $results as $row ) {
				$arr_parent[$row->ID] = $row->post_parent;
				
				if ( ! isset($arr_children[$row->post_parent]) )
					$arr_children[$row->post_parent] = array();
					
				$arr_children[$row->post_parent] []= $row->ID;
			}
			
			$descendants = array();
			if ( ! empty( $arr_children[$object_id] ) ) {
				foreach ( $arr_parent as $page_id => $parent_id ) {
					if ( ! $parent_id || ($page_id == $object_id) )
						continue;
						
					do {
						if ( $object_id == $parent_id ) {
							$descendants[$page_id] = true;
							break;
						}
						
						$parent_id = $arr_parent[$parent_id];
					} while ( $parent_id );
				}
			}
			$args['descendants'] = $descendants;
		}

		ScoperHardwayParent::walk_parent_dropdown($output, $args, false, $stored_parent_id);
		
		//log_mem_usage_rs( 'end dropdown_pages()' );
		
		return $output;
	}
				
	// slightly modified transplant of WP 2.6 core parent_dropdown
	function walk_parent_dropdown( &$output, &$args, $use_parent_clause = true, $default = 0, $parent = 0, $level = 0 ) {
		static $use_class;
		static $page_ids;
		
		if ( ! isset($use_class) )
			$use_class = awp_ver('2.7');

		if ( ! isset( $page_ids ) )
			$page_ids = array();
			
		// todo: defaults, merge
		//extract($args);
		// args keys: pages, object_id
		
		$page_ids[$parent] = true;
		
		if ( ! is_array( $args['pages'] ) )
			$args['pages'] = array();

		if ( empty($args['descendants'] ) || ! is_array( $args['descendants'] ) )
			$args['descendants'] = array();

		foreach ( array_keys($args['pages']) as $key ) {
			// we call this without parent criteria to include pages whose parent is unassociable
			if ( $use_parent_clause && $args['pages'][$key]->post_parent != $parent )
				continue;
				
			$id = $args['pages'][$key]->ID;
				
			if ( in_array($id, array_keys($args['descendants']) ) )
				continue;

			if ( isset($page_ids[$id]) )
				continue;
		
			$page_ids[$id] = true;
		
			// A page cannot be its own parent.
			if ( $args['object_id'] && ( $id == $args['object_id'] ) )
				continue;

			$class = ( $use_class ) ? 'class="level-' . $level . '" ' : '';

			$current = ( $id == $default) ? ' selected="selected"' : '';
			$pad = str_repeat( '&nbsp;', $level * 3 );
			$output .= "\n\t<option " . $class . 'value="' . $id . '"' . $current . '>' . $pad . wp_specialchars($args['pages'][$key]->post_title) . '</option>';
			
			ScoperHardwayParent::walk_parent_dropdown( $output, $args, true, $default, $id, $level +1 );
		}
		
		if ( ! $level && empty($args['retain_page_ids']) )
			$page_ids = array();
	}
	
	// object_array = db results 2D array
	function order_by_hierarchy($object_array, $col_id, $col_parent, $id_key = false) {
		$ordered_results = array();
		$find_parent_id = 0;
		$last_parent_id = array();
		
		do {
			$found_match = false;
			$lastcount = count($ordered_results);
			foreach ( $object_array as $key => $item )
				if ( $item->$col_parent == $find_parent_id ) {
					if ( $id_key )
						$ordered_results[$item->$col_id]= $object_array[$key];
					else
						$ordered_results[]= $object_array[$key];
					
					unset($object_array[$key]);
					$last_parent_id[] = $find_parent_id;
					$find_parent_id = $item->$col_id;
					
					$found_match = true;
					break;	
				}
			
			if ( ! $found_match ) {
				if ( ! count($last_parent_id) )
					break;
				else
					$find_parent_id = array_pop($last_parent_id);
			}
		} while ( true );
		
		return $ordered_results;
	}
	
	// listed_objects[object_id] = object, including at least the parent property
	// unlisted_objects[object_id] = object, including at least the parent property
	function add_missing_parents($listed_objects, $unlisted_objects, $col_parent) {
		$need_obj_ids = array();
		foreach ( $listed_objects as $obj )
			if ( $obj->$col_parent && ! isset($listed_objects[ $obj->$col_parent ]) )
				$need_obj_ids[$obj->$col_parent] = true;

		$last_need = '';
				
		while ( $need_obj_ids ) { // potentially query for several generations of object hierarchy (but only for parents of objects that have roles assigned)
			if ( $need_obj_ids == $last_need )
				break; //precaution

			$last_need = $need_obj_ids;

			if ( $add_objects = array_intersect_key( $unlisted_objects, $need_obj_ids) ) {
				$listed_objects = $listed_objects + $add_objects; // array_merge will not maintain numeric keys
				$unlisted_objects = array_diff_key($unlisted_objects, $add_objects);
			}
			
			$new_need = array();
			foreach ( array_keys($need_obj_ids) as $id ) {
				if ( ! empty($listed_objects[$id]->$col_parent) )  // does this object itself have a nonzero parent?
					$new_need[$listed_objects[$id]->$col_parent] = true;
			}

			$need_obj_ids = $new_need;
		}
		
		return $listed_objects;
	}
	
	function get_page_titles() {
		global $wpdb;
		
		$is_administrator = is_content_administrator_rs();
		
		if ( ! $is_administrator )
			remove_filter('get_pages', array('ScoperHardway', 'flt_get_pages'), 1, 2);
		
		// don't retrieve post_content, to save memory
		$all_pages = scoper_get_results( "SELECT ID, post_parent, post_title, post_date, post_date_gmt, post_status, post_name, post_modified, post_modified_gmt, guid, menu_order, comment_count FROM $wpdb->posts WHERE post_type = 'page'" );
		
		foreach ( array_keys( $all_pages ) as $key )
			$all_pages[$key]->post_content = '';		// add an empty post_content property to each item, in case some plugin filter requires it
		
		$all_pages = apply_filters( 'get_pages', $all_pages );

		if ( ! $is_administrator )
			add_filter('get_pages', array('ScoperHardway', 'flt_get_pages'), 1, 2);

		return scoper_get_property_array( $all_pages, 'ID', 'post_title' );
	}
}

?>