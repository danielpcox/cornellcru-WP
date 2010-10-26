<?php
add_filter( 'map_meta_cap', 'scoper_map_custom_meta_cap', 99, 2 );

function scoper_map_custom_meta_cap( $cap, $user_id ) {
	if ( count($cap) > 1 )
		return $cap;
	
	if ( $custom_types = array_diff( get_post_types(), array( 'post', 'page', 'attachment', 'revision' ) ) ) {
		$args = array_slice( func_get_args(), 2 );
		$caps = array();
		
		//print_r( $cap );
		
		foreach ( $custom_types as $type ) {
	
			if ( "delete_{$type}" == $cap[0] ) {
				$author_data = get_userdata( $user_id );
				
				$post = get_post( $args[0] );
				$post_type = get_post_type_object( $post->post_type );
		
				if ( '' != $post->post_author ) {
					$post_author_data = get_userdata( $post->post_author );
				} else {
					//No author set yet so default to current user for cap checks
					$post_author_data = $author_data;
				}
		
				// If the user is the author...
				if ( $user_id == $post_author_data->ID ) {
					// If the post is published...
					if ( 'publish' == $post->post_status ) {
						$caps[] = "delete_published_{$type}s";
					} elseif ( 'trash' == $post->post_status ) {
						if ('publish' == get_post_meta($post->ID, '_wp_trash_meta_status', true) )
							$caps[] = "delete_published_{$type}s";
					} else {
						// If the post is draft...
						$caps[] = "delete_{$type}s";
					}
				} else {
					// The user is trying to edit someone else's post.
					$caps[] = "delete_others_{$type}s";
					// The post is published, extra cap required.
					if ( 'publish' == $post->post_status )
						$caps[] = "delete_published_{$type}s";
					elseif ( 'private' == $post->post_status )
						$caps[] = "delete_private_{$type}s";
				}
				break;
				
			} elseif ( "edit_{$type}" == $cap[0] ) {
				$author_data = get_userdata( $user_id );
				
				$post = get_post( $args[0] );
				$post_type = get_post_type_object( $post->post_type );

				$post_author_data = get_userdata( $post->post_author );
				//echo "current user id : $user_id, post author id: " . $post_author_data->ID . "<br />";
				// If the user is the author...
				if ( $user_id == $post_author_data->ID ) {
					// If the post is published...
					if ( 'publish' == $post->post_status ) {
						$caps[] = "edit_published_{$type}s";
					} elseif ( 'trash' == $post->post_status ) {
						if ('publish' == get_post_meta($post->ID, '_wp_trash_meta_status', true) )
							$caps[] = "edit_published_{$type}s";
					} else {
						// If the post is draft...
						$caps[] = "edit_{$type}s";;
					}
				} else {
					// The user is trying to edit someone else's post.
					$caps[] = "edit_others_{$type}s";;
					// The post is published, extra cap required.
					if ( 'publish' == $post->post_status )
						$caps[] = "edit_published_{$type}s";
					elseif ( 'private' == $post->post_status )
						$caps[] = "edit_private_{$type}s";
				}
				break;	
				
				
			} elseif ( "read_{$type}" == $cap[0] ) {
				$post = get_post( $args[0] );
				$post_type = get_post_type_object( $post->post_type );
		
				if ( 'private' != $post->post_status ) {
					$caps[] = 'read';
					break;
				}
		
				$author_data = get_userdata( $user_id );
				$post_author_data = get_userdata( $post->post_author );
				if ( $user_id == $post_author_data->ID )
					$caps[] = 'read';
				else
					$caps[] = "read_private_{$type}s";
				break;
			}		
		} // end foreach custom types
		
		// If no meta caps match, return the original cap.
		if( ! $caps )
			$caps = $cap;	
		
		//dump($caps);
		return $caps;
	} else {
		return $cap;
	}
}
?>