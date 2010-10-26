<?php
/* 
 * * User Role Editor plugin Lirary general staff
 * Author: Vladimir Garagulya vladimir@shinephp.com
 * 
 */


if (!defined("WPLANG")) {
  die;  // Silence is golden, direct call is prohibited
}

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

global $wpdb, $ure_OptionsTable;

$ure_OptionsTable = $wpdb->prefix .'options';
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
  global $wpdb, $ure_OptionsTable;
  
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
  $roles = unserialize($record[0]->option_value);

  return $roles;
}
// end of getUserRoles()


// restores User Roles from the backup record
function restoreUserRoles() {

  global $wpdb, $ure_OptionsTable;

  $errorMessage = 'Error! '.__('Database operation error. Check log file.', 'ure');
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


// Save Roles to database
function ure_saveRolesToDb($roles) {
  global $wpdb, $ure_OptionsTable;

  $option_name = $wpdb->prefix.'user_roles';
  $serialized_roles = serialize($roles);
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


// process new role create request
function ure_newRoleCreate(&$currentRole) {

  $mess = '';
  $currentRole = '';
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $user_role = utf8_decode(urldecode($_GET['user_role']));
    // sanitize user input for security
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*/', $user_role)) {
      return 'Error! '.__('Error: Role name must contain latin characters and digits only!', 'ure');;
    }
   
    if ($user_role) {
      $user_role = esc_html($user_role);
      $user_role = mysql_real_escape_string($user_role);
      $roles = ure_getUserRoles();
      if (!$roles) {
        return 'Error! '.__('Roles list reading error is encountered', 'ure');;
      }
      // add new role to the roles array
      $currentRole = strtolower($user_role);
      $result = add_role($currentRole, $user_role, array('read'=>1, 'level_0'=>1));
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
function getRolesCanDelete($roles) {
  global $wpdb;

  $tableName = $wpdb->prefix.'usermeta';
  $metaKey = $wpdb->prefix.'capabilities';
  $defaultRole = get_option('default_role');
  $standardRoles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
  $rolesCanDelete = array();
  foreach ($roles as $key=>$role) {
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
    $rolesUsed = $wpdb->get_results($query);
    if ($rolesUsed && count($rolesUsed>0)) {
      foreach ($rolesUsed as $roleUsed) {
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
      $rolesCanDelete[$key] = $role['name'];
    }
  }

  return $rolesCanDelete;
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


// returns true is user has Role "Administrator"
function ure_is_admin($user_id) {
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
// end of ure_is_admin()


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

?>
