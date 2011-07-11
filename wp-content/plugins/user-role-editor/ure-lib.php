<?php
/* 
 * * User Role Editor plugin Lirary general staff
 * Author: Vladimir Garagulya vladimir@shinephp.com
 * 
 */


if (!defined("WPLANG")) {
  die;  // Silence is golden, direct call is prohibited
}

require_once(ABSPATH.WPINC.'/class-simplepie.php');

$ure_siteURL = get_option( 'siteurl' );

// Pre-2.6 compatibility
if ( !defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', $ure_siteURL . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

$urePluginDirName = substr(dirname(__FILE__), strlen(WP_PLUGIN_DIR) + 1, strlen(__FILE__) - strlen(WP_PLUGIN_DIR)-1);

define('URE_PLUGIN_URL', WP_PLUGIN_URL.'/'.$urePluginDirName);
define('URE_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.$urePluginDirName);
define('URE_WP_ADMIN_URL', $ure_siteURL.'/wp-admin');
define('URE_ERROR', 'Error is encountered');
define('URE_SPACE_REPLACER', '_URE-SR_');
define('URE_PARENT', 'users.php');

global $wpdb, $ure_roles, $ure_capabilitiesToSave, $ure_currentRole, $ure_toldAboutBackup, $ure_apply_to_all;

$ure_roles = false; $ure_capabilitiesToSave = false; $ure_toldAboutBackup = false; $ure_apply_to_all = false;

// this array will be used to cash users checked for Administrator role
$ure_userToEdit = array();

function ure_logEvent($message, $showMessage = false) {
  include(ABSPATH .'wp-includes/version.php');

  $fileName = URE_PLUGIN_DIR.'/user-role-editor.log';
  $fh = fopen($fileName,'a');
  $cr = "\n";
  $s = $cr.date("d-m-Y H:i:s").$cr.
      'WordPress version: '.$wp_version.', PHP version: '.phpversion().', MySQL version: '.mysql_get_server_info().$cr;
  fwrite($fh, $s);
  fwrite($fh, $message.$cr);
  fclose($fh);

  if ($showMessage) {
    ure_showMessage('Error! '.__('Error is occur. Please check the log file.', 'ure'));
  }
}
// end of ure_logEvent()


// returns true is user has Role "Administrator"
function ure_has_administrator_role($user_id) {
  global $wpdb, $ure_userToEdit;

  if (!isset($user_id) || !$user_id) {
    return false;
  }

  $tableName = $wpdb->prefix.'usermeta';
  $metaKey = $wpdb->prefix.'capabilities';
  $query = "SELECT count(*)
                FROM $tableName
                WHERE user_id=$user_id AND meta_key='$metaKey' AND meta_value like '%administrator%'";
  $hasAdminRole = $wpdb->get_var($query);
  if ($hasAdminRole>0) {
    $result = true;
  } else {
    $result = false;
  }
  $ure_userToEdit[$user_id] = $result;

  return $result;
}
// end of ure_has_administrator_role()


// true if user is superadmin under multi-site environment or has administrator role
function ure_is_admin( $user_id = false ) {
  global $current_user;

	if ( ! $user_id ) {
    if (empty($current_user) && function_exists('get_currentuserinfo')) {
      get_currentuserinfo();
    }
		$user_id = ! empty($current_user) ? $current_user->id : 0;
	}

	if ( ! $user_id )
		return false;

	$user = new WP_User($user_id);

  $simpleAdmin = ure_has_administrator_role($user_id);

	if ( is_multisite() ) {
		$super_admins = get_super_admins();
		$superAdmin =  is_array( $super_admins ) && in_array( $user->user_login, $super_admins );
	} else {
    $superAdmin = false;
  }

	return $simpleAdmin || $superAdmin;
}
// end of ure_is_super_admin()


function ure_optionSelected($value, $etalon) {
  $selected = '';
  if ($value==$etalon) {
    $selected = 'selected="selected"';
  }

  return $selected;
}
// end of ure_optionSelected()


function ure_showMessage($message) {

  if ($message) {
    if (strpos(strtolower($message), 'error')===false) {
      $class = 'updated fade';
    } else {
      $class = 'error';
    }
    echo '<div class="'.$class.'" style="margin:0;">'.$message.'</div><br style="clear: both;"/>';
  }

}
// end of ure_showMessage()


function ure_getUserRoles() {
  global $wpdb;

  $ure_OptionsTable = $wpdb->prefix .'options';
  $option_name = $wpdb->prefix.'user_roles';
  $getRolesQuery = "select option_id, option_value
                      from $ure_OptionsTable
                      where option_name='$option_name'
                      limit 0, 1";
  $record = $wpdb->get_results($getRolesQuery);
  if ($wpdb->last_error) {
    ure_logEvent($wpdb->last_error);
    return;
  }
  $ure_roles = unserialize($record[0]->option_value);

  return $ure_roles;
}
// end of getUserRoles()


// restores User Roles from the backup record
function restoreUserRoles() {

  global $wpdb;

  $errorMessage = 'Error! '.__('Database operation error. Check log file.', 'ure');
  $ure_OptionsTable = $wpdb->prefix .'options';
  $option_name = $wpdb->prefix.'user_roles';
  $backup_option_name = $wpdb->prefix.'backup_user_roles';
  $query = "select option_value
              from $ure_OptionsTable
              where option_name='$backup_option_name'
              limit 0, 1";
  $option_value = $wpdb->get_var($query);
  if ($wpdb->last_error) {
    ure_logEvent($wpdb->last_error, true);
    return $errorMessage;
  }
  if ($option_value) {
    $query = "update $ure_OptionsTable
                    set option_value='$option_value'
                    where option_name='$option_name'
                    limit 1";
    $record = $wpdb->query($query);
    if ($wpdb->last_error) {
        ure_logEvent($wpdb->last_error, true);
        return $errorMessage;
    }
    $mess = __('Roles capabilities are restored from the backup data', 'ure');
  } else {
    $mess = __('No backup data. It is created automatically before the first role data update.', 'ure');
  }
  if (isset($_REQUEST['user_role'])) {
    unset($_REQUEST['user_role']);
  }

  return $mess;
}
// end of restorUserRoles()


function ure_makeRolesBackup() {
  global $wpdb, $mess, $ure_roles, $ure_capabilitiesToSave, $ure_toldAboutBackup;

  $ure_OptionsTable = $wpdb->prefix .'options';
  // check if backup user roles record exists already
  $backup_option_name = $wpdb->prefix.'backup_user_roles';
  $query = "select option_id
              from $ure_OptionsTable
              where option_name='$backup_option_name'
          limit 0, 1";
  $option_id = $wpdb->get_var($query);
  if ($wpdb->last_error) {
    ure_logEvent($wpdb->last_error, true);
    return false;
  }
  if (!$option_id) {
    // create user roles record backup
    $serialized_roles = mysql_real_escape_string(serialize($ure_roles));
    $query = "insert into $ure_OptionsTable
                (option_name, option_value, autoload)
                values ('$backup_option_name', '$serialized_roles', 'yes')";
    $record = $wpdb->query($query);
    if ($wpdb->last_error) {
      ure_logEvent($wpdb->last_error, true);
      return false;
    }
    if (!$ure_toldAboutBackup) {
      $ure_toldAboutBackup = true;
      $mess .= __('Backup record is created for the current role capabilities', 'ure');
    }
  }

  return true;
}
// end of ure_makeRolesBackup()


// Save Roles to database
function ure_saveRolesToDb() {
  global $wpdb, $ure_roles, $ure_capabilitiesToSave, $ure_currentRole;

  $ure_OptionsTable = $wpdb->prefix .'options';
  if (!isset($ure_roles[$ure_currentRole])) {
    $ure_roles[$ure_currentRole]['name'] = $ure_currentRole;
  }
  $ure_roles[$ure_currentRole]['capabilities'] = $ure_capabilitiesToSave;
  $option_name = $wpdb->prefix.'user_roles';
  $serialized_roles = serialize($ure_roles);
  $query = "update $ure_OptionsTable
                set option_value='$serialized_roles'
                where option_name='$option_name'
                limit 1";
  $record = $wpdb->query($query);
  if ($wpdb->last_error) {
    ure_logEvent($wpdb->last_error, true);
    return false;
  }

  return true;
}
// end of saveRolesToDb()


function ure_updateRoles() {
  global $wpdb, $ure_apply_to_all, $ure_roles;

  $ure_toldAboutBackup = false;
  if (is_multisite() && $ure_apply_to_all) {  // update Role for the all blogs/sites in the network
    $old_blog = $wpdb->blogid;
    // Get all blog ids
    $blogIds = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
    foreach ($blogIds as $blog_id) {
      switch_to_blog($blog_id);
      $ure_roles = ure_getUserRoles();
      if (!$ure_roles) {
        return false;
      }
      if (!ure_makeRolesBackup()) {
        return false;
      }
      if (!ure_saveRolesToDb()) {
        return false;
      }
    }
    switch_to_blog($old_blog);
    $ure_roles = ure_getUserRoles();
  } else {
    if (!ure_makeRolesBackup()) {
      return false;
    }
    if (!ure_saveRolesToDb()) {
      return false;
    }
  }

  return true;
}
// end of ure_updateRoles()


// process new role create request
function ure_newRoleCreate(&$ure_currentRole) {

  $mess = '';
  $ure_currentRole = '';
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $user_role = utf8_decode(urldecode($_GET['user_role']));
    // sanitize user input for security
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*/', $user_role)) {
      return 'Error! '.__('Error: Role name must contain latin characters and digits only!', 'ure');;
    }
   
    if ($user_role) {
      $user_role = esc_html($user_role);
      $user_role = mysql_real_escape_string($user_role);
      $ure_roles = ure_getUserRoles();
      if (!$ure_roles) {
        return 'Error! '.__('Roles list reading error is encountered', 'ure');;
      }
      // add new role to the roles array
      $ure_currentRole = strtolower($user_role);
      $result = add_role($ure_currentRole, $user_role, array('read'=>1, 'level_0'=>1));
      if (!isset($result) || !$result) {
        $mess = 'Error! '.__('Error is encountered during new role create operation', 'ure');
      } else {
        $mess = sprintf(__('Role %s is created successfully', 'ure'), $user_role);
      }
    }
  }
  return $mess;
}
// end of newRoleCreate()


// define roles which we could delete, e.g self-created and not used with any blog user
function getRolesCanDelete($ure_roles) {
  global $wpdb;

  $tableName = $wpdb->prefix.'usermeta';
  $metaKey = $wpdb->prefix.'capabilities';
  $defaultRole = get_option('default_role');
  $standardRoles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
  $ure_rolesCanDelete = array();
  foreach ($ure_roles as $key=>$role) {
    $canDelete = true;
    // check if it is default role for new users
    if ($key==$defaultRole) {
      $canDelete = false;
      continue;
    }
    // check if it is standard role
    foreach ($standardRoles as $standardRole) {
      if ($key==$standardRole) {
        $canDelete = false;
        break;
      }
    }
    if (!$canDelete) {
      continue;
    }
    // check if user with such role exists
    $query = "SELECT meta_value
                FROM $tableName
                WHERE meta_key='$metaKey' AND meta_value like '%$key%'";
    $ure_rolesUsed = $wpdb->get_results($query);
    if ($ure_rolesUsed && count($ure_rolesUsed>0)) {
      foreach ($ure_rolesUsed as $roleUsed) {
        $roleName = unserialize($roleUsed->meta_value);
        foreach ($roleName as $key1=>$value1) {
          if ($key==$key1) {
            $canDelete = false;
            break;
          }
        }
        if (!$canDelete) {
          break;
        }
      }
    }
    if ($canDelete) {
      $ure_rolesCanDelete[$key] = $role['name'];
    }
  }

  return $ure_rolesCanDelete;
}
// end of getRolesCanDelete()


function ure_deleteRole() {
  global $wp_roles;

  $mess = '';
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $role = $_GET['user_role'];
    //$result = remove_role($_GET['user_role']);
    // use this modified code from remove_role() directly as remove_role() returns nothing to check
    if (!isset($wp_roles)) {
      $wp_roles = new WP_Roles();
    }
    if (isset($wp_roles->roles[$role])) {
      unset($wp_roles->role_objects[$role]);
      unset($wp_roles->role_names[$role]);
      unset($wp_roles->roles[$role]);
      $result = update_option($wp_roles->role_key, $wp_roles->roles);
    } else {
      $result = false;
    }
    if (!isset($result) || !$result) {
      $mess = 'Error! '.__('Error encountered during role delete operation', 'ure');
    } else {
      $mess = sprintf(__('Role %s is deleted successfully', 'ure'), $role);
    }
    unset($_REQUEST['user_role']);
  }

  return $mess;
}
// end of ure_deleteRole()


function ure_changeDefaultRole() {
  global $wp_roles;

  $mess = '';
  if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
  }
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $errorMessage = 'Error! '.__('Error encountered during default role change operation', 'ure');
    if (isset($wp_roles->role_objects[$_GET['user_role']])) {
      $result = update_option('default_role', $_GET['user_role']);
      if (!isset($result) || !$result) {
        $mess = $errorMessage;
      } else {
        $mess = sprintf(__('Default role for new users is set to %s successfully', 'ure'), $wp_roles->role_names[$_GET['user_role']]);
      }
    } else {
      $mess = $errorMessage;
    }
    unset($_REQUEST['user_role']);
  }

  return $mess;
}
// end of ure_changeDefaultRole()


function ure_ConvertCapsToReadable($capsName) {

  $capsName = str_replace('_', ' ', $capsName);
  $capsName = ucfirst($capsName);

  return $capsName;
}
// ure_ConvertCapsToReadable


function ure_TranslationData() {

// for the translation purpose
  if (false) {
// Standard WordPress roles
    __('Editor', 'ure');
    __('Author', 'ure');
    __('Contributor', 'ure');
    __('Subscriber', 'ure');
// Standard WordPress capabilities
    __('Switch themes', 'ure');
    __('Edit themes', 'ure');
    __('Activate plugins', 'ure');
    __('Edit plugins', 'ure');
    __('Edit users', 'ure');
    __('Edit files', 'ure');
    __('Manage options', 'ure');
    __('Moderate comments', 'ure');
    __('Manage categories', 'ure');
    __('Manage links', 'ure');
    __('Upload files', 'ure');
    __('Import', 'ure');
    __('Unfiltered html', 'ure');
    __('Edit posts', 'ure');
    __('Edit others posts', 'ure');
    __('Edit published posts', 'ure');
    __('Publish posts', 'ure');
    __('Edit pages', 'ure');
    __('Read', 'ure');
    __('Level 10', 'ure');
    __('Level 9', 'ure');
    __('Level 8', 'ure');
    __('Level 7', 'ure');
    __('Level 6', 'ure');
    __('Level 5', 'ure');
    __('Level 4', 'ure');
    __('Level 3', 'ure');
    __('Level 2', 'ure');
    __('Level 1', 'ure');
    __('Level 0', 'ure');
    __('Edit others pages', 'ure');
    __('Edit published pages', 'ure');
    __('Publish pages', 'ure');
    __('Delete pages', 'ure');
    __('Delete others pages', 'ure');
    __('Delete published pages', 'ure');
    __('Delete posts', 'ure');
    __('Delete others posts', 'ure');
    __('Delete published posts', 'ure');
    __('Delete private posts', 'ure');
    __('Edit private posts', 'ure');
    __('Read private posts', 'ure');
    __('Delete private pages', 'ure');
    __('Edit private pages', 'ure');
    __('Read private pages', 'ure');
    __('Delete users', 'ure');
    __('Create users', 'ure');
    __('Unfiltered upload', 'ure');
    __('Edit dashboard', 'ure');
    __('Update plugins', 'ure');
    __('Delete plugins', 'ure');
    __('Install plugins', 'ure');
    __('Update themes', 'ure');
    __('Install themes', 'ure');
    __('Update core', 'ure');
    __('List users', 'ure');
    __('Remove users', 'ure');
    __('Add users', 'ure');
    __('Promote users', 'ure');
    __('Edit theme options', 'ure');
    __('Delete themes', 'ure');
    __('Export', 'ure');
  }
}
// end of ure_TranslationData()


function ure_shinephpNews() {

$feed = new SimplePie();
$feed->set_feed_url('http://www.shinephp.com/category/shinephp-wordpress-plugins/feed/');
$feed->enable_cache(false);
$feed->init();
$feed->handle_content_type();
$items = $feed->get_items();
if ($items && sizeof($items)>0) {
  echo '<ul>';
  foreach ($items as $item) {
    echo '<li><a href="'.$item->get_permalink().'">'.$item->get_title().'</a></li>';
  }
  echo '</ul>';
} else {
  echo '<ul><li>'.__('No items found.', 'thankyou') . '</li></ul>';
}
echo '<hr/>';
echo '<span style="font-size: 12px; font-weight: bold;">'.__('Recent Posts:','plugins-lang-switch').'</span><br/>';
$feed->set_feed_url('http://feeds.feedburner.com/shinephp');
$feed->init();
$feed->handle_content_type();
$items = $feed->get_items();
if ($items && sizeof($items)>0) {
  echo '<ul>';
  foreach ($items as $item) {
    echo '<li><a href="'.$item->get_permalink().'" title="'.substr($item->get_description(), 0, 160).'">'.$item->get_title().'</a>&nbsp;&ndash; <span class="rss-date">'.$item->get_date('j F Y').'</span></li>';
  }
  echo '</ul>';
} else {
  echo '<ul><li>'.__('No items found.', 'thankyou') . '</li></ul>';
}

}
// end of ure_shinephpNews()

?>
