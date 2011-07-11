<?php
// menu icons by Jonas Rask: http://www.jonasraskdesign.com/
if( basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) )
	die();
	
$wp_content = ( is_ssl() || ( is_admin() && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ) ) ? str_replace( 'http:', 'https:', WP_CONTENT_URL ) : WP_CONTENT_URL;
define ('SCOPER_URLPATH', $wp_content . '/plugins/' . SCOPER_FOLDER);

define ('ROLE_ASSIGNMENT_RS', 'role_assignment');
define ('ROLE_RESTRICTION_RS', 'role_restriction');

define ('REMOVE_ASSIGNMENT_RS', '');
define ('ASSIGN_FOR_ENTITY_RS', 'entity');
define ('ASSIGN_FOR_CHILDREN_RS', 'children');
define ('ASSIGN_FOR_BOTH_RS', 'both');

define( 'OBJECT_UI_RS', 'object_ui' );
	
require_once( 'admin_lib_rs.php' );

if ( IS_MU_RS )
	require_once( 'admin_lib-mu_rs.php' );

if ( ( 'index.php' == $GLOBALS['pagenow'] ) && ! defined( 'USE_RVY_RIGHTNOW' )  )
	include_once( 'admin-dashboard_rs.php' );

	
class ScoperAdmin
{
	var $scoper;
	var $role_assigner;	//object reference
	var $tinymce_readonly;
	
	function ScoperAdmin() {
		global $pagenow;
		
		$this->scoper =& $GLOBALS['scoper'];
		
		add_action('admin_head', array(&$this, 'admin_head_base'));

		if ( ! defined('DISABLE_QUERYFILTERS_RS') || is_content_administrator_rs() ) {
			add_action('admin_head', array(&$this, 'admin_head'));
			
			if ( ! defined('XMLRPC_REQUEST') && ( 'async-upload.php' != $pagenow ) ) {
				add_action('admin_menu', array(&$this,'build_menu'));

				if ( 'plugins.php' == $pagenow )
					add_filter( 'plugin_row_meta', array(&$this, 'flt_plugin_action_links'), 10, 2 );
			}
		}

		if ( ( ( 'edit-tags.php' == $pagenow ) || ( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'p-admin/edit-tags.php') ) ) && awp_is_plugin_active('subscribe2') )
			require_once('subscribe2_helper_rs.php');
			
		if ( defined( 'FLUTTER_NAME' ) )
			require_once('flutter_helper_rs.php');
	}

	function menu_handler() {
		$rs_page = $_GET['page'];
		$url = SCOPER_ABSPATH . '/admin/';
		
		switch ($rs_page) {
		case 'rs-options' :
			include_once( $url . 'options.php');
			scoper_options( false );
			break;
			
		case 'rs-general_roles' :
			include_once( $url . 'general_roles.php');
			break;
			
		case 'rs-groups' :
			include_once( $url . 'groups.php');
			break;
			
		case 'rs-default_groups' :
			include_once( $url . 'default_groups.php');
			break;
			
		case 'rs-group_members' :
			include_once( $url . 'group_members.php');
			break;
			
		case 'rs-object_role_edit' :
			include_once( $url . 'object_role_edit.php');
			break;

		case 'rs-about' :
			include_once( $url . 'about.php');
			break;
			
		case 'rs-attachments_utility' :
			include_once( $url . 'attachments_utility.php');
			break;
			
		default :
			if ( strpos( $rs_page, '-roles' ) )
				$topic = 'roles';
			elseif( strpos( $rs_page, '-restrictions' ) )
				$topic = 'restrictions';

			if ( ! empty($topic) ) {
				$matches = array();
				if ( preg_match( "/rs-(.*)-$topic(.*)/", $rs_page, $matches ) ) {
					if ( strpos( $rs_page, "{$topic}_t" ) ) {
						include_once( SCOPER_ABSPATH . "/admin/section_{$topic}.php" );
						call_user_func( "scoper_admin_section_{$topic}", $matches[1] ); 
					} else {
						if ( ! $matches[2] ) {
							global $scoper;
							if ( $scoper->data_sources->member_property( 'post', 'object_types', $matches[1] ) )
								$matches[2] = 'post';
							else
								$matches[2] = $matches[1];
						}
	
						include_once( SCOPER_ABSPATH . "/admin/object_{$topic}.php" );
						call_user_func( "scoper_admin_object_{$topic}", $matches[2], $matches[1] ); 
					}
				}
			}
		} // end switch
	}
	
	// adds an Options link next to Deactivate, Edit in Plugins listing
	function flt_plugin_action_links($links, $file) {
		if ( $file == SCOPER_BASENAME ) {
			$links[] = "<a href='http://agapetry.net/forum/'>" . __awp('Support Forum') . "</a>";
			
			$page = ( IS_MU_RS ) ? 'rs-site_options' : 'rs-options';
			$links[] = "<a href='admin.php?page=$page'>" . __awp('Options') . "</a>";
		}
	
		return $links;
	}
	
	function admin_head_base() {		
		if ( isset( $_POST['rs_defaults'] ) ) {
			// User asked to restore default options, so restore htaccess rule for attachment filtering (if it's not disabled)
			scoper_flush_site_rules();
			scoper_expire_file_rules();
		}
	}
	
	function admin_head() {
		global $pagenow, $plugin_page_cr;
		
		echo '<link rel="stylesheet" href="' . SCOPER_URLPATH . '/admin/role-scoper.css" type="text/css" />'."\n";

		if ( 'rs-options' == $plugin_page_cr ) {
			if ( scoper_get_option('version_update_notice') ) {
				require_once('misc/version_notice_rs.php');
				scoper_new_version_notice();
			}

		} elseif ( 'rs-about' == $plugin_page_cr ) {
			echo '<link rel="stylesheet" href="' . SCOPER_URLPATH . '/admin/about/about.css" type="text/css" />'."\n";
		}

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php') ) ) {
			$src_name = 'post';
			$object_type = cr_find_post_type();	
		} elseif ( isset($_GET['src_name']) && isset($_GET['object_type']) ) {
			$src_name = $_GET['src_name'];
			$object_type = $_GET['object_type'];	
		}
		
		if ( ! empty($object_type) ) {
			// dynamically set checkbox titles for user/group object role selection
			$src = $this->scoper->data_sources->get($src_name);
			$otype_def = $this->scoper->data_sources->member_property($src_name, 'object_types', $object_type);
			
			if ( ! empty($src) && ! empty($src->cols->parent) && empty($otype_def->ignore_object_hierarchy) ) {
				$obj_title = sprintf( __('assign role for this %s', 'scoper'), agp_strtolower( $otype_def->labels->singular_name ) );
				$child_title = sprintf( __('assign role for sub-%s', 'scoper'), agp_strtolower( $otype_def->labels->name ) );

				$js_params = "var role_for_object_title = '$obj_title';"
						. "var role_for_children_title = '$child_title';";
		
				// TODO: replace some of this JS with equivalent JQuery
				echo "\n" . '<script type="text/javascript">' . $js_params . '</script>';
				echo "\n" . "<script type='text/javascript' src='" . SCOPER_URLPATH . "/admin/rs-objrole-cbox-maint.js'></script>";
			}
		}
		
		add_filter( 'contextual_help_list', array(&$this, 'flt_contextual_help_list'), 10, 2 );
		
		if ( ( 0 === strpos( $plugin_page_cr, 'rs-' ) ) && strpos( $plugin_page_cr, 'roles' ) ) {
			// add Ajax goodies we need for role duration/content date limit editing Bulk Role Admin
			wp_print_scripts( array( 'page' ) );
			
			require_once( 'admin_lib-bulk_rs.php' );
			ScoperAdminBulkLib::date_limits_js();
		}
		
		// TODO: replace some of this JS with equivalent JQuery
		echo "\n" . "<script type='text/javascript' src='" . SCOPER_URLPATH . "/admin/agapetry.js'></script>";
		echo "\n" . "<script type='text/javascript' src='" . SCOPER_URLPATH . "/admin/role-scoper.js'></script>";
		
		if ( scoper_get_option( 'group_ajax' ) && ( in_array( $pagenow, array( 'user-edit.php', 'profile.php' ) ) || ( 'rs-groups' == $plugin_page_cr ) ) ) {
			global $scoper_user_search;

			if ( 'rs-groups' == $plugin_page_cr ) {
				$agent_type = 'users';
				$agent_id = isset( $_GET['id'] ) ? $_GET['id'] : 0;
			} else {
				$agent_type = 'groups';
				if ( 'profile.php' == $pagenow ) {
					global $current_user;
					$agent_id = $current_user->ID;	
				} else
					$agent_id = $_GET['user_id'];
			}

			require_once( 'user_search_ui_rs.php' );
			$scoper_user_search = new ScoperUserSearch( $agent_type );
			$scoper_user_search->output_js( $agent_type, $agent_id );
		}
	}
	
	function flt_contextual_help_list ($help, $screen) {
		$link_section = '';
		
		// WP < 3.0 passes ID as string
		if ( is_object($screen) )
			$screen = $screen->id;
		
		if ( strpos( $screen, 'rs-' ) ) {
			$match = array();
			if ( ! preg_match( "/admin_page_rs-[^@]*-*/", $screen, $match ) )
				if ( ! preg_match( "/_page_rs-[^@]*-*/", $screen, $match ) )
					preg_match( "/rs-[^@]*-*/", $screen, $match );

			if ( $match )
				if ( $pos = strpos( $match[0], 'rs-' ) ) {
					$link_section = substr( $match[0], $pos + strlen('rs-') );
					$link_section = str_replace( '_t', '', $link_section );	
				}
					
		} elseif ( in_array( $screen, array( 'post', 'page', 'upload', 'users' ) ) ) {
			$link_section = $screen;
		}

		if ( $link_section ) {
			$link_section = str_replace( '.php', '', $link_section);
			$link_section = str_replace( '/', '~', $link_section);
			
			if ( ! isset($help[$screen]) )
				$help[$screen] = '';
			
			$help[$screen] .= ' ' . sprintf(__('%1$s Role Scoper Documentation%2$s', 'scoper'), "<a href='http://agapetry.net/downloads/RoleScoper_UsageGuide.htm#$link_section' target='_blank'>", '</a>')
			. ', ' . sprintf(__('%1$s Role Scoper Support Forum%2$s', 'scoper'), "<a href='http://agapetry.net/forum/' target='_blank'>", '</a>');
		}

		return $help;
	}
			
	function filter_add_new_content_links() {
		global $scoper, $submenu;
	
		if ( is_content_administrator_rs() )
			return;
		
		// workaround for WP's universal inclusion of "Add New"
		$src = $this->scoper->data_sources->get( 'post' );
		foreach ( array_keys($src->object_types) as $_post_type ) {
			if ( $wp_type = get_post_type_object( $_post_type ) ) {
				$edit_key = ( 'post' == $_post_type ) ? 'edit.php' : "edit.php?post_type=$_post_type";
				$add_key = ( 'post' == $_post_type ) ? 'post-new.php' :  "post-new.php?post_type=$_post_type";
				
				if ( isset($submenu[$edit_key]) ) {
					foreach ( $submenu[$edit_key] as $key => $arr ) {
						if ( isset($arr['2']) && ( $add_key == $arr['2'] ) ) {
							if ( ! cr_user_can( $wp_type->cap->edit_posts, 0, 0, array('skip_id_generation' => true, 'skip_any_object_check' => true ) ) )
								unset( $submenu[$edit_key][$key]);
						}
					}
				}
			}
		}
		
		if ( isset($submenu['link-manager.php'][10]) ) {
			$this->scoper->ignore_object_roles = true;
			if ( ! current_user_can( 'manage_links' ) )
				unset( $submenu['link-manager.php'][10] );

			$this->scoper->ignore_object_roles = false;
		}
		
		if ( 'edit.php' == $GLOBALS['pagenow'] ) {
			$_post_type = ( ! empty($_REQUEST['post_type']) ) ? $_REQUEST['post_type'] : 'post';
			if ( $wp_type = get_post_type_object( $_post_type ) ) {
				if ( ! cr_user_can( $wp_type->cap->edit_posts, 0, 0, array('skip_id_generation' => true, 'skip_any_object_check' => true ) ) ) 
					add_action( 'admin_footer', array( &$this, 'hide_add_new_button' ) );
			}
		}
	}
	
	function hide_add_new_button() {
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready( function($) {
	$('.add-new-h2').hide();
});
/* ]]> */
</script>
<?php
	}
	
	function build_menu() {
		global $plugin_page_cr;
		
		if ( ! defined('USER_ROLES_RS') && isset( $_POST['enable_group_roles'] ) )
			scoper_use_posted_init_options();

		global $current_user;

		$this->filter_add_new_content_links();
		
		$is_option_administrator = is_option_administrator_rs();
		$is_user_administrator = is_user_administrator_rs();
		$is_content_administrator = is_content_administrator_rs();
			
		/*
		// optional hack to prevent roles / restrictions menu for non-Administrators
		//
		// This is now handled as a Role Scoper Option.
		// In Roles > Options > Features > Content Maintenance, set "Roles and Restrictions can be set" to "Administrators only" 
		//
		// To prevent Role Scoper from filtering the backend at all, go to Roles > Options > Realm > Access Types and deselect "editing and administering content"
		//
		// end optional hack
		*/
				
		$require_blogwide_editor = scoper_get_option('role_admin_blogwide_editor_only');

		if ( ! $is_content_administrator && ( 'admin_content' == $require_blogwide_editor ) )
			if ( ! $is_option_administrator )
				return;

		if ( ! $is_user_administrator && ( 'admin' == $require_blogwide_editor ) )
			if ( ! $is_option_administrator )
				return;

		$can_admin_objects = array();
		$can_admin_terms = array();
		
		$use_post_types = scoper_get_option( 'use_post_types' );
		$use_taxonomies = scoper_get_option( 'use_taxonomies' );

		
		// which object types does this user have any administration over?
		foreach ( $this->scoper->data_sources->get_all() as $src_name => $src ) {
			if ( ! empty($src->no_object_roles) || ! empty($src->taxonomy_only) || ('group' == $src_name) )
				continue;
			
			$object_types = ( isset($src->object_types) ) ? $src->object_types : array( $src_name => true );

			foreach ( array_keys($object_types) as $object_type ) {
				if ( ( 'post' == $src_name ) && empty( $use_post_types[$object_type] ) )
					continue;
				
				if ( is_administrator_rs($src, 'user') || $this->user_can_admin_object($src_name, $object_type, 0, true) )
					if ( scoper_get_otype_option('use_object_roles', "$src_name:$object_type") )
						$can_admin_objects[$src_name][$object_type] = true;
			}
		}

		// which taxonomies does this user have any administration over?
		foreach ( $this->scoper->taxonomies->get_all() as $taxonomy => $tx ) {
			if ( taxonomy_exists($taxonomy) && empty( $use_taxonomies[$taxonomy] ) && ( 'post' == $tx->object_source ) )
				continue;
				
			if ( is_taxonomy_used_rs( $taxonomy ) && ( is_administrator_rs($tx->source, 'user') || $this->user_can_admin_terms($taxonomy) ) )
				$can_admin_terms[$taxonomy] = true;
		}

		$can_manage_groups = DEFINE_GROUPS_RS && ( $is_user_administrator || current_user_can('recommend_group_membership') );

		// Users Tab
		if ( DEFINE_GROUPS_RS ) {
			$cap_req = ( $can_manage_groups ) ? 'read' : 'manage_groups';
			
			$groups_caption = ( defined( 'GROUPS_CAPTION_RS' ) ) ? GROUPS_CAPTION_RS : __('Role Groups', 'scoper');

			if ( IS_MU_RS && scoper_get_site_option( 'mu_sitewide_groups' ) )
				add_submenu_page( "ms-admin.php", $groups_caption, $groups_caption, $cap_req, 'rs-groups', array( &$this, 'menu_handler' ) );
			else
				add_submenu_page( 'users.php', $groups_caption, $groups_caption, $cap_req, 'rs-groups', array( &$this, 'menu_handler' ) );

			// satisfy WordPress' demand that all admin links be properly defined in menu
			if ( 'rs-default_groups' == $plugin_page_cr )
				add_submenu_page('users.php', __('User Groups', 'scoper'), __('Default Groups', 'scoper'), $cap_req, 'rs-default_groups', array( &$this, 'menu_handler' ) );
		
			if ( 'rs-group_members' == $plugin_page_cr )
				add_submenu_page('users.php', __('User Groups', 'scoper'), __('Group Members', 'scoper'), $cap_req, 'rs-group_members', array( &$this, 'menu_handler' ) );
		}

		// the rest of this function pertains to Roles and Restrictions menus
		if ( ! $is_user_administrator && ! $can_admin_terms && ! $is_user_administrator && ! $can_admin_objects )
			return;
	
		$general_roles = $is_user_administrator; // && scoper_get_option('rs_blog_roles');  // rs_blog_roles option has never been active in any RS release; leave commented here in case need arises
		
		// determine the official WP-registered URL for roles and restrictions menus
		$object_submenus_first = false;

		if ( ! empty($can_admin_terms['category']) ) {
			$roles_menu = 'rs-category-roles_t';
			$restrictions_menu = 'rs-category-restrictions_t';

		} elseif ( ! empty($can_admin_objects['post']['post']) ) {
			$roles_menu = 'rs-post-roles';
			$restrictions_menu = 'rs-post-restrictions';
			$object_submenus_first = true;
			
		} elseif ( ! empty($can_admin_objects['post']['page']) ) {  // TODO: handle custom types here?
			$roles_menu = 'rs-page-roles';
			$restrictions_menu = 'rs-page-restrictions';
			$object_submenus_first = true;

		} elseif ( $can_admin_terms && $this->scoper->taxonomies->member_property( key($can_admin_terms),  'requires_term' ) ) {
			$taxonomy = key($can_admin_terms);
			$roles_menu = "rs-$taxonomy-roles_t";
			$restrictions_menu = "rs-$taxonomy-restrictions_t";

		} elseif ( $can_admin_objects ) {
			$src_name = key($can_admin_objects);
			$object_type = key($can_admin_objects[$src_name]);
			
			if ( ( $src_name != $object_type ) && ( 'post' != $src_name ) ) {
				$roles_menu = "rs-{$object_type}-roles_{$src_name}";
				$restrictions_menu = "rs-{$object_type}-restrictions_{$src_name}";
			} else {
				$roles_menu = "rs-$object_type-roles";
				$restrictions_menu = "rs-$object_type-restrictions";
			}

			$object_submenus_first = true;
		} else {
			// shouldn't ever need this
			$roles_menu = 'rs-roles-post';
			$restrictions_menu = 'rs-restrictions-post';
			$object_submenus_first = true;
		}

		if ( $general_roles )
			$roles_menu = 'rs-general_roles';


		if ( $is_option_administrator )
			$roles_menu = 'rs-options';  // option administrators always have RS Options as top level roles submenu

		if ( $is_user_administrator ) { 
			if ( empty( $restrictions_menu ) )
				$restrictions_menu =  'rs-category-restrictions_t';  // If RS Realms are customized, the can_admin_terms / can_admin_objects result can override this default, even for user administrators
		}
		
		// Register the menus with WP using URI and links determined above
		global $menu;
		$tweak_menu = false; // don't mess with menu order unless we know we can get away with it in current WP version
			
		//  Manually set menu indexes for positioning below Users menu,
		//  but not if Flutter (a.k.a. Fresh Page) plugin is active.  It re-indexes menu items 
		if ( ! defined( 'SCOPER_DISABLE_MENU_TWEAK' ) ) {
			//if ( awp_ver('2.9') ) {
				// review each WP version for menu indexes until there's a clean way to force menu proximity to 'Users'
				if ( isset( $menu[70] ) && $menu[70][2] == 'users.php' ) {  // WP 2.9 and 3.0
					$tweak_menu = true;
					$restrictions_menu_key = 71;
					$roles_menu_key = 72;
				}
			//}
		}

		$roles_cap = 'read'; // we apply other checks within this function to confirm the menu is valid for current user
		$restrictions_caption = __('Restrictions', 'scoper');
		$roles_caption = __('Roles', 'scoper');

		if ( $tweak_menu ) {
			add_menu_page($restrictions_caption, __('Restrictions', 'scoper'), 'read', $restrictions_menu, array(&$this, 'menu_handler'), SCOPER_URLPATH . '/admin/images/menu/restrictions.png', $restrictions_menu_key );
			add_menu_page($roles_caption, __('Roles', 'scoper'), $roles_cap, $roles_menu, array(&$this, 'menu_handler'), SCOPER_URLPATH . '/admin/images/menu/roles.png', $roles_menu_key );
		} else {
			add_menu_page($restrictions_caption, __('Restrictions', 'scoper'), 'read', $restrictions_menu, array(&$this, 'menu_handler'), SCOPER_URLPATH . '/admin/images/menu/restrictions.png' );
			add_menu_page($roles_caption, __('Roles', 'scoper'), $roles_cap, $roles_menu, array(&$this, 'menu_handler'), SCOPER_URLPATH . '/admin/images/menu/roles.png' );
		}

		if ( $general_roles )
			add_submenu_page($roles_menu, __('General Roles', 'scoper'), __('General', 'scoper'), 'read', 'rs-general_roles', array( &$this, 'menu_handler' ) );
			
		$first_pass = true;

		$submenu_types = ( $object_submenus_first ) ? array( 'object', 'term' ) : array( 'term', 'object' );
		foreach ( $submenu_types as $scope ) {
			if ( 'term' == $scope ) {
				// Term Roles and Restrictions (will only display objects user can edit)
				if ( $can_admin_terms ) {
					// Will only allow assignment to terms for which current user has admin cap
					// Term Roles page also prevents assignment or removal of roles current user doesn't have
					foreach ( $this->scoper->taxonomies->get_all() as $taxonomy => $tx ) {
						
						if ( empty($can_admin_terms[$taxonomy]) )
							continue;
						
						if ( $require_blogwide_editor ) {
							if ( ! $this->scoper->user_can_edit_blogwide( 'post', '', array( 'require_others_cap' => true, 'status' => 'publish' ) ) )
								continue;
						}
						
						$show_roles_menu = true;

						add_submenu_page($roles_menu, sprintf(__('%s Roles', 'scoper'), $tx->labels->singular_name ), $tx->labels->name, 'read', "rs-$taxonomy-roles_t", array( &$this, 'menu_handler' ) );

						if ( ! empty($tx->requires_term) ) {
							$show_restrictions_menu = true;

							add_submenu_page($restrictions_menu, sprintf(__('%s restrictions', 'scoper'), $tx->labels->singular_name), $tx->labels->name, 'read', "rs-$taxonomy-restrictions_t", array( &$this, 'menu_handler' ) );
						}
					} // end foreach taxonomy
				} // endif can admin terms
			
			} else {
				// Object Roles (will only display objects user can edit)
				if ( $can_admin_objects ) {
					foreach ( $this->scoper->data_sources->get_all() as $src_name => $src ) {
						if ( ! empty($src->no_object_roles) || ! empty($src->taxonomy_only) || ('group' == $src_name) )
							continue;
						
						$object_types = ( isset($src->object_types) ) ? $src->object_types : array( $src_name => true );
		
						foreach ( array_keys($object_types) as $object_type ) {
							if ( empty($can_admin_objects[$src_name][$object_type]) )
								continue;
		
							if ( $require_blogwide_editor ) {
								if ( ! $this->scoper->user_can_edit_blogwide( $src_name, $object_type, array( 'require_others_cap' => true ) ) )
									continue;
							}
	
							$show_roles_menu = true;
							$show_restrictions_menu = true;
						
							if ( ( $src_name != $object_type ) && ( 'post' != $src_name ) ) {
								$roles_page = "rs-{$object_type}-roles_{$src_name}";
								$restrictions_page = "rs-{$object_type}-restrictions_{$src_name}";
							} else {
								$roles_page = "rs-$object_type-roles";
								$restrictions_page = "rs-$object_type-restrictions";
							}
							
							$src_otype = ( isset($src->object_types) ) ? "{$src_name}:{$object_type}" : $src_name;
							$item_label_singular = $this->interpret_src_otype($src_otype, 'singular_name');
							$item_label = $this->interpret_src_otype($src_otype);
							
							add_submenu_page($roles_menu, sprintf(__('%s Roles', 'scoper'), $item_label_singular), $item_label, 'read', $roles_page, array( &$this, 'menu_handler' ) );
							
							add_submenu_page($restrictions_menu, sprintf(__('%s Restrictions', 'scoper'), $item_label_singular), $item_label, 'read', $restrictions_page, array( &$this, 'menu_handler' ) );	
						} // end foreach obj type
					} // end foreach data source
				} // endif can admin objects
			} // endif drawing object scope submenus
		} // end foreach submenu scope

		if ( $is_user_administrator )
			add_submenu_page($roles_menu, __('About Role Scoper', 'scoper'), __('About', 'scoper'), 'read', 'rs-about', array( &$this, 'menu_handler' ) );
			
			
		global $submenu;
			
		// Change Role Scoper Options submenu title from default "Roles" to "Options"
		if ( $is_option_administrator ) {
			if ( isset($submenu[$roles_menu][0][2]) && ( $roles_menu == $submenu[$roles_menu][0][2] ) )
				$submenu[$roles_menu][0][0] = __awp('Options');
				
			// satisfy WordPress' demand that all admin links be properly defined in menu
			if ( 'rs-attachments_utility' == $plugin_page_cr )
				add_submenu_page($roles_menu, __('Attachment Utility', 'scoper'), __('Attachment Utility', 'scoper'), 'read', 'rs-attachments_utility', array( &$this, 'menu_handler' ) );

		} elseif ( empty($show_restrictions_menu) || empty($show_roles_menu) ) {
			// Remove Roles or Restrictions menu if it has no submenu
			if ( $tweak_menu ) { // since we forced the menu keys, no need to loop through menu looking for them
				if ( empty($show_restrictions_menu) && isset($menu[$restrictions_menu_key]) )
					unset($menu[$restrictions_menu_key]);
					
				if ( empty($show_roles_menu) && isset($menu[$roles_menu_key]) )
					unset($menu[$roles_menu_key]);
				
			} else {
				global $menu;
				foreach ( array_keys($menu) as $key ) {
					if ( isset( $menu[$key][0]) )
						if ( empty($show_roles_menu) && ( $roles_caption == $menu[$key][0] ) )
							unset($menu[$key]);
						elseif ( empty($show_restrictions_menu) && ( $restrictions_caption == $menu[$key][0] ) )
							unset($menu[$key]);
				}
			}
		}
		
		// WP MU site options
		if ( $is_option_administrator && IS_MU_RS )
			scoper_mu_site_menu();
		

		// satisfy WordPress' demand that all admin links be properly defined in menu
		if ( 'rs-object_role_edit' == $plugin_page_cr )
			add_submenu_page($roles_menu, __('Object Role Edit', 'scoper'), __('Object Role Edit', 'scoper'), 'read', 'rs-object_role_edit', array( &$this, 'menu_handler' ) );
	}

	
	function interpret_src_otype( $src_otype, $label_property = 'name' ) {
		if ( ! $arr_src_otype = explode(':', $src_otype) )
			return $src_otype;
			
		if ( isset( $arr_src_otype[1]) ) {
			$label_text = $this->scoper->data_sources->member_property($arr_src_otype[0], 'object_types', $arr_src_otype[1], 'labels', $label_property);
		} else
			$label_text = $this->scoper->data_sources->member_property($arr_src_otype[0], 'labels', $label_property);
			
		if ( ! $label_text ) { // need this in cases (Realm option labeling) where taxonomy is RS-enabled but its corresponding post type is not.  TODO: eliminate redundancy
			if ( ( 'post' == $arr_src_otype[0] ) && ( $type_object = get_post_type_object( $arr_src_otype[1] ) ) )
				$label_text = $type_object->labels->name;
		}

		if ( ! $label_text )	// in case of data sources definition error, cryptic fallback better than nullstring
			$label_text = $src_otype;
			
		return $label_text;
	}

	function user_can_admin_role($role_handle, $item_id, $src_name = '', $object_type = '', $args = array() ) {
		if ( is_user_administrator_rs() )
			return true;

		require_once( 'permission_lib_rs.php' );
		return user_can_admin_role_rs($role_handle, $item_id, $src_name, $object_type, $args );
	}
	
	function user_can_admin_object($src_name, $object_type, $object_id = false, $any_obj_role_check = false, $user = '' ) {
		if ( is_content_administrator_rs() )
			return true;
		
		require_once( 'permission_lib_rs.php' );
		return user_can_admin_object_rs($src_name, $object_type, $object_id, $any_obj_role_check, $user );
	}
	
	function user_can_admin_terms($taxonomy = '', $term_id = '', $user = '') {
		if ( is_user_administrator_rs() )
			return true;
		
		require_once( 'permission_lib_rs.php' );
		return user_can_admin_terms_rs($taxonomy, $term_id, $user);
	}
} // end class ScoperAdmin
?>