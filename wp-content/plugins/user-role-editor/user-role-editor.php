<?php
/*
Plugin Name: User Role Editor
Plugin URI: http://www.shinephp.com/user-role-editor-wordpress-plugin/
Description: It allows you to change any standard WordPress user roles (except administrator) capabilities list with a few clicks.
Version: 3.0.1
Author: Vladimir Garagulya
Author URI: http://www.shinephp.com
Text Domain: ure
Domain Path: /lang/
*/

/*
Copyright 2010-2011  Vladimir Garagulya  (email: vladimir@shinephp.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!function_exists("get_option")) {
  die;  // Silence is golden, direct call is prohibited
}

global $wp_version, $current_user;

$exit_msg = __('User Role Editor requires WordPress 3.0 or newer.', 'ure').'<a href="http://codex.wordpress.org/Upgrading_WordPress">'.__('Please update!', 'ure').'</a>';

if (version_compare($wp_version,"3.0","<"))
{
	return ($exit_msg);
}


require_once('ure-lib.php');

load_plugin_textdomain('ure','', $urePluginDirName.'/lang');


function ure_optionsPage() {
  
  global $wpdb, $current_user, $ure_OptionsTable, $ure_roles, $ure_capabilitiesToSave, $ure_toldAboutBackup, $ure_currentRole, $ure_apply_to_all;

  if (!empty($current_user)) {
    $user_id = $current_user->ID;
  } else {
    $user_id = false;
  }
  if (!ure_is_admin($user_id)) {
    if (is_multisite()) {
      $admin = 'SuperAdministrator';
    } else {
      $admin = 'Administrator';
    }
    die('Only '.$admin.' is allowed to use this plugin');
  }
  
?>

<div class="wrap">
  <div class="icon32" id="icon-options-general"><br/></div>
    <h2><?php _e('User Role Editor', 'ure'); ?></h2>
		<?php require ('ure-options.php'); ?>
  </div>
<?php

}
// end of ure_optionsPage()


// Install plugin
function ure_install() {

  add_option('ure_caps_readable', 0);

}
// end of ure_install()


function ure_excludeAdminRole($roles) {

  if (isset($roles['administrator'])){
		unset( $roles['administrator'] );
	}

  return $roles;

}
// end of excludeAdminRole()


function ure_admin_jquery(){
	global $pagenow;
	if (URE_PARENT==$pagenow){
		wp_enqueue_script('jquery');
	}
}
// end of ure_admin_jquery()


function ure_admin_user_hide(){

  global $current_user;

  if (!empty($current_user->ID)) {
    $user_id = $current_user->ID;
  } else {
    $user_id = 0;
  }

	if (!ure_is_admin($user_id)) {
?>
		<script type='text/javascript' >
			jQuery(document).ready(function(){
			  var admin_count;
			  var total_count;
			  
			  // Grab Administrator Count
			  jQuery("#list-filter > .subsubsub > li > a:contains(Administrator)").each(function(){
			  	admin_count = jQuery(this).children('.count').text();
				admin_count = admin_count.substring(1, admin_count.length - 1);
			  });
			  
			  // Remove Administrator Filter
			  jQuery("#list-filter > .subsubsub > li > a:contains(Administrator)").parent().remove();
			  
			  // Update All Filter Count
			  jQuery("#list-filter > .subsubsub > li > a:contains(All)").each(function(){
			  	total_count = jQuery(this).children('.count').text();
				total_count = total_count.substring(1, total_count.length - 1) - admin_count;
				jQuery(this).children('.count').text('('+total_count+')');
			  });
			  
			  // Hide Administrator Table Rows
			  jQuery("#users > tr .administrator").parent().parent().remove();
			});
		</script>
<?php
	}
}
// end of ure_admin_user_hide()


// We have to vulnerable queries id users admin interfase which should be processed
// 1st: http://blogdomain.com/wp-admin/user-edit.php?user_id=ID&wp_http_referer=%2Fwp-admin%2Fusers.php
// 2nd: http://blogdomain.com/wp-admin/users.php?action=delete&user=ID&_wpnonce=ab34225a78
// If put Administrator user ID into such request, user with lower capabilities (if he has 'edit_users')
// can edit, delete admin record
// This function removes 'edit_users' capability from current user capabilities
// if request has admin user ID in it
function ure_not_edit_admin($allcaps, $caps, $name) {

  global $ure_userToEdit;

  $userKeys = array('user_id', 'user');
  foreach ($userKeys as $userKey) {
    $accessDeny = false;
    if (isset($_GET[$userKey])) {
      $ure_UserId = $_GET[$userKey];
      if ($ure_UserId==1) {  // built-in WordPress Admin
        $accessDeny = true;
      } else {
        if (!isset($ure_userToEdit[$ure_UserId])) {
          // check if user_id has Administrator role
          $accessDeny = ure_has_administrator_role($ure_UserId);
        } else {
          // user_id was checked already, get result from cash
          $accessDeny = $ure_userToEdit[$ure_UserId];
        }
      }
      if ($accessDeny) {
        unset($allcaps['edit_users']);
      }
      break;
    }
  }

	return $allcaps;
}
// end of ure_not_edit_admin()


function ure_init() {

  global $current_user;

  if (!empty($current_user->ID)) {
    $user_id = $current_user->ID;
  } else {
    $user_id = 0;
  }

  // these filters and actions should prevent editing users with administrator role
  // by other users with 'edit_users' capabilities
	if (!ure_is_admin($user_id)) {
    // Exclude administrator role from edit list.
    add_filter('editable_roles', 'ure_excludeAdminRole');
    // Enqueue jQuery
    add_action('admin_enqueue_scripts' , 'ure_admin_jquery' );
    // Hide Administrator from list of users
    add_action('admin_head' , 'ure_admin_user_hide');
    // prohibit any actions with user who has Administrator role
    add_filter('user_has_cap', 'ure_not_edit_admin', 10, 3);
  }
  
}
// end of ure_init()


function ure_plugin_action_links($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__).'/user-role-editor.php')){
        $settings_link = "<a href='".URE_PARENT."?page=user-role-editor.php'>".__('Settings','ure')."</a>";
        array_unshift( $links, $settings_link );
    }
    return $links;
}
// end of ure_plugin_action_links


function ure_plugin_row_meta($links, $file) {
  if ($file == plugin_basename(dirname(__FILE__).'/user-role_editor.php')){
		$links[] = '<a target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#changelog">'.__('Changelog', 'ure').'</a>';
	}
	return $links;
} // end of ure_plugin_row_meta


function ure_settings_menu() {

	if ( function_exists('add_submenu_page')) {
    if (!is_multisite()) {
      $keyCapability = 'edit_users';
    } else {
      $keyCapability = 'manage_network_users';
    }
    $ure_page = add_submenu_page('users.php', __('User Role Editor'), __('User Role Editor'), $keyCapability, basename(__FILE__), 'ure_optionsPage');
		add_action( "admin_print_styles-$ure_page", 'ure_adminCssAction' );
	}
}
// end of ure_settings_menu()

function ure_adminCssAction() {

  wp_enqueue_style('ure_admin_css', URE_PLUGIN_URL.'/css/ure-admin.css', array(), false, 'screen');

}
// end of ure_adminCssAction()


if (is_admin()) {
  // activation action
  register_activation_hook(__FILE__, "ure_install");

  add_action('admin_init', 'ure_init');
  // add a Settings link in the installed plugins page
  add_filter('plugin_action_links', 'ure_plugin_action_links', 10, 2);
  add_filter('plugin_row_meta', 'ure_plugin_row_meta', 10, 2);
  add_action('admin_menu', 'ure_settings_menu');
}
  
?>
