<?php
/* 
 * User Role Editor plugin Settings form
 * 
 */

if (!defined('URE_PLUGIN_URL')) {
  die;  // Silence is golden, direct call is prohibited
}

$shinephpFavIcon = URE_PLUGIN_URL.'/images/vladimir.png';
$mess = '';

$ure_caps_readable = get_option('ure_caps_readable');
$option_name = $wpdb->prefix.'user_roles';

if (isset($_GET['action'])) {
  $action = $_GET['action'];
  // restore roles capabilities from the backup record
  if ($action=='reset') {
    $mess = restoreUserRoles();
  } else if ($action=='addnewrole') {
    // process new role create request
    $mess = ure_newRoleCreate($ure_currentRole);
  } else if ($action=='delete') {
    $mess = ure_deleteRole();
  } else if ($action=='default') {
    $mess = ure_changeDefaultRole();
  } else if ($action=='capsreadable') {
    if ($ure_caps_readable) {
      $ure_caps_readable = 0;
    } else {
      $ure_caps_readable = 1;
    }
    update_option('ure_caps_readable', $ure_caps_readable);
  }
}

$defaultRole = get_option('default_role');

if (isset($_POST['ure_apply_to_all'])) {
  $ure_apply_to_all = 1;
} else {
  $ure_apply_to_all = 0;
}

if (!isset($ure_roles) || !$ure_roles) {
// get roles data from database
  $ure_roles = ure_getUserRoles();
  if (!$ure_roles) {
    return;
  }
}

$ure_rolesId = array();
foreach ($ure_roles as $key=>$value) {
  $ure_rolesId[] = $key;
}

if (!isset($ure_currentRole) || !$ure_currentRole) {
  $ure_currentRole = $ure_rolesId[count($ure_rolesId) - 1];
  if (isset($_REQUEST['user_role']) && $_REQUEST['user_role']) {
    $ure_currentRole = $_REQUEST['user_role'];
  }
}

$roleDefaultHTML = '<select id="default_user_role" name="default_user_role" width="200" style="width: 200px">';
$roleSelectHTML = '<select id="user_role" name="user_role" onchange="ure_Actions(\'role-change\', this.value);">';
foreach ($ure_roles as $key=>$value) {
  $selected1 = ure_optionSelected($key, $ure_currentRole);
  $selected2 = ure_optionSelected($key, $defaultRole);
  if ($key!='administrator') {
    $roleSelectHTML .= '<option value="'.$key.'" '.$selected1.'>'.__($value['name'], 'ure').'</option>';
    $roleDefaultHTML .= '<option value="'.$key.'" '.$selected2.'>'.__($value['name'], 'ure').'</option>';
  }
}
$roleSelectHTML .= '</select>';
$roleDefaultHTML .= '</select>';

$fullCapabilities = array();
foreach($ure_roles as $role) {
    foreach ($role['capabilities'] as $key=>$value) {
        $fullCapabilities[] = $key;
    }
}
$fullCapabilities = array_unique($fullCapabilities);
asort($fullCapabilities);

// save role changes to database block
if (isset($_POST['action']) && $_POST['action']=='update' && isset($_POST['user_role'])) {
  $ure_currentRole = $_POST['user_role'];
  $ure_capabilitiesToSave = array();
  foreach($fullCapabilities as $availableCapability) {
    $cap_id = str_replace(' ', URE_SPACE_REPLACER, $availableCapability);
    if (isset($_POST[$cap_id])) {
      $ure_capabilitiesToSave[$availableCapability] = 1;
    }
  }
  if (count($ure_capabilitiesToSave)>0) {
    if (!ure_updateRoles()) {
      return;
    }
    if ($mess) {
      $mess .= '<br/>';
    }
    $mess = __('Role', 'ure').' <em>'.__($ure_roles[$ure_currentRole]['name'], 'ure').'</em> '.__('is updated successfully', 'ure');
  }
}

$ure_rolesCanDelete = getRolesCanDelete($ure_roles);
if ($ure_rolesCanDelete && count($ure_rolesCanDelete)>0) {
  $roleDeleteHTML = '<select id="del_user_role" name="del_user_role" width="200" style="width: 200px">';
  foreach ($ure_rolesCanDelete as $key=>$value) {
    $roleDeleteHTML .= '<option value="'.$key.'">'.__($value, 'ure').'</option>';
  }
  $roleDeleteHTML .= '</select>';
} else {
  $roleDeleteHTML = '';
}

// options page display part
function ure_displayBoxStart($title, $style='') {
?>
			<div class="postbox" style="float: left; <?php echo $style; ?>">
				<h3 style="cursor:default;"><span><?php echo $title ?></span></h3>
				<div class="inside">
<?php
}
// 	end of ure_displayBoxStart()

function ure_displayBoxEnd() {
?>
				</div>
			</div>
<?php
}
// end of thanks_displayBoxEnd()


ure_showMessage($mess);

?>
  <form method="post" action="<?php echo URE_PARENT; ?>?page=user-role-editor.php" onsubmit="return ure_onSubmit();">
<?php
    settings_fields('ure-options');
?>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div class="inner-sidebar" >
						<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
									<?php ure_displayBoxStart(__('About this Plugin:', 'ure')); ?>
											<a class="ure_rsb_link" style="background-image:url(<?php echo $shinephpFavIcon; ?>);" target="_blank" href="http://www.shinephp.com/"><?php _e("Author's website", 'ure'); ?></a>
											<a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/user-role-editor-icon.png'; ?>" target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/"><?php _e('Plugin webpage', 'ure'); ?></a>
											<a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/changelog-icon.png'; ?>)" target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#changelog"><?php _e('Changelog', 'ure'); ?></a>
											<a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/faq-icon.png'; ?>)" target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq"><?php _e('FAQ', 'ure'); ?></a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/donate-icon.png'; ?>)" target="_blank" href="http://www.shinephp.com/donate"><?php _e('Donate', 'ure'); ?></a>
									<?php ure_displayBoxEnd();
  ure_displayBoxStart(__('More plugins from','ure').' <a href="http://www.shinephp.com" title="ShinePHP.com">ShinePHP.com</a>');
  ure_shinephpNews();
  ure_displayBoxEnd();
	ure_displayBoxStart(__('Greetings:','ure')); ?>
											<a class="ure_rsb_link" style="background-image:url(<?php echo $shinephpFavIcon; ?>);" target="_blank" title="<?php _e("It's me, the author", 'ure'); ?>" href="http://www.shinephp.com/">Vladimir</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/marsis.png'; ?>)" target="_blank" title="<?php _e("For the help with Belorussian translation", 'ure'); ?>" href="http://pc.de">Marsis G.</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/rafael.png'; ?>)" target="_blank" title="<?php _e("For the help with Brasilian translation", 'ure'); ?>" href="http://www.arquiteturailustrada.com.br/">Rafael Galdencio</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/jackytsu.png'; ?>)" target="_blank" title="<?php _e("For the help with Chinese translation", 'ure'); ?>" href="http://www.jackytsu.com">Jackytsu</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/remi.png'; ?>)" target="_blank" title="<?php _e("For the help with Dutch translation", 'ure'); ?>" href="http://www.remisan.be">Rémi Bruggeman</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/whiler.png'; ?>)" target="_blank" title="<?php _e("For the help with French translation", 'ure'); ?>" href="http://blogs.wittwer.fr/whiler/">Whiler</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/peter.png'; ?>)" target="_blank" title="<?php _e("For the help with German translation", 'ure'); ?>" href="http://www.red-socks-reinbek.de">Peter</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/blacksnail.png'; ?>)" target="_blank" title="<?php _e("For the help with Hungarian translation", 'ure'); ?>" href="http://www.blacksnail.hu">István</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/talksina.png'; ?>)" target="_blank" title="<?php _e("For the help with Italian translation", 'ure'); ?>" href="http://www.iadkiller.org">Talksina</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/alessandro.png'; ?>);" target="_blank" title="<?php _e("For the help with Italian translation",'pgc');?>" href="http://technodin.org">Alessandro Mariani</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/technologjp.png'; ?>)" target="_blank" title="<?php _e("For the help with Japanese translation", 'ure'); ?>" href="http://technolog.jp">Technolog.jp</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/good-life.png'; ?>)" target="_blank" title="<?php _e("For the help with Persian translation", 'ure'); ?>" href="http://good-life.ir">Good Life</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/tagsite.png'; ?>)" target="_blank" title="<?php _e("For the help with Polish translation", 'ure'); ?>" href="http://www.tagsite.eu">TagSite</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/dario.png'; ?>)" target="_blank" title="<?php _e("For the help with Spanish translation", 'ure'); ?>" href="http://www.darioferrer.com">Dario  Ferrer</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/sadri.png'; ?>)" target="_blank" title="<?php _e("For the help with Turkish translation", 'ure'); ?>" href="http://www.faydaliweb.com">Sadri Ercan</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/cartaca.png'; ?>)" target="_blank" title="<?php _e("For the help with Turkish translation", 'ure'); ?>" href="http://www.kartaca.com">Can KAYA</a>
                      <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL.'/images/fullthrottle.png'; ?>)" target="_blank" title="<?php _e("For the code to hide administrator role", 'ure'); ?>" href="http://fullthrottledevelopment.com/how-to-hide-the-adminstrator-on-the-wordpress-users-screen">FullThrottle</a>
											<?php _e('Do you wish to see your name with link to your site here? You are welcome! Your help with translation and new ideas are very appreciated.', 'ure'); ?>
									<?php ure_displayBoxEnd(); ?>
						</div>
					</div>
					<div class="has-sidebar" >
						<div id="post-body-content" class="has-sidebar-content">
<script language="javascript" type="text/javascript">
<?php
if (is_multisite()) {
?>

  function ure_applyToAllOnClick(cb) {
    el = document.getElementById('ure_apply_to_all_div');
    if (cb.checked) {
      el.style.color = '#FF0000';
    } else {
      el.style.color = '#000000';
    }
  }
<?php
}
?>

  function ure_Actions(action, value) {
    if (action=='cancel') {
      document.location = '<?php echo URE_WP_ADMIN_URL.'/'.URE_PARENT; ?>?page=user-role-editor.php';
      return;
    }
    if (action=='addnewrole') {
      var el = document.getElementById('new_user_role');
      value = el.value;
      if (value=='') {
        alert('<?php _e('Role Name can not be empty!','ure');?>');
        return false;
      }
      if  (!(/^[a-z$_][\w$]*$/i.test(value))) {
        alert('<?php _e('Role Name must contain latin characters and digits only!','ure');?>');
        return false;
      }
    } else if (action!='role-change' && action!='capsreadable') {
      if (action=='delete') {
        actionText = '<?php _e('Delete Role', 'ure'); ?>';
      } else if (action=='default') {
        actionText = '<?php _e('Change Default Role', 'ure'); ?>';
      } else if (action=='reset') {
        actionText = '<?php _e('Restore Roles from backup copy', 'ure'); ?>';
      }
      if (!confirm(actionText+': '+ "<?php _e('Please confirm to continue', 'ure'); ?>")) {
        return false;
      }
    }
    if (action!='update') {
      url = '<?php echo URE_WP_ADMIN_URL.'/'.URE_PARENT; ?>?page=user-role-editor.php&action='+ action;
      if (action=='delete') {
        el = document.getElementById('del_user_role');
        value = el.options[el.selectedIndex].value;
      } else if (action=='default') {
        el = document.getElementById('default_user_role');
        value = el.options[el.selectedIndex].value;
      }
      if (value!='' && value!=undefined) {
        url = url +'&user_role='+ escape(value);
      }
      document.location = url;
    } else {
      document.getElementById('ure-form').submit();
    }
    
  }


  function ure_onSubmit() {
    if (!confirm('<?php echo sprintf(__('Role "%s" update: please confirm to continue', 'ure'), __($ure_roles[$ure_currentRole]['name'], 'ure')); ?>')) {
      return false;
    }
  }


</script>
<?php
						ure_displayBoxStart(__('Select Role and change its capabilities list', 'ure'));
?>
              <div style="float: left;"><?php echo __('Select Role:', 'ure').' '.$roleSelectHTML; ?></div>
<?php
  if ($ure_caps_readable) {
    $checked = 'checked="checked"';
  } else {
    $checked = '';
  }
?>
              <div style="display:inline;float: right;"><input type="checkbox" name="ure_caps_readable" id="ure_caps_readable" value="1" <?php echo $checked; ?> onclick="ure_Actions('capsreadable');"/>
                <label for="ure_caps_readable"><?php _e('Show capabilities in human readable form', 'ure');?></label>
              </div>
<?php
if (is_multisite()) {
  $hint = __('If checked, then apply action to ALL sites of this Network');
  if ($ure_apply_to_all) {
    $checked = 'checked="checked"';
    $fontColor = 'color:#FF0000;';
  } else {
    $checked = '';
    $fontColor = '';
  }
?>
              <div style="float: right; margin-right: 20px; <?php echo $fontColor;?>" id="ure_apply_to_all_div"><input type="checkbox" name="ure_apply_to_all" id="ure_apply_to_all" value="1" <?php echo $checked; ?> title="<?php echo $hint;?>" onclick="ure_applyToAllOnClick(this)"/>
                <label for="ure_apply_to_all" title="<?php echo $hint;?>"><?php _e('Apply to All Sites', 'ure');?></label>
              </div>
<?php
}
?>
<br/><br/><hr/>
        <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
          <tr>
            <td style="vertical-align:top;">
<?php
  $quant = count($fullCapabilities);
  $quantInColumn = (int) $quant/3;
  $quantInCell = 0;
  foreach( $fullCapabilities as $capability) {
    $checked = '';
    if (isset($ure_roles[$ure_currentRole]['capabilities'][$capability])) {
      $checked = 'checked="checked"';
    }
    $cap_id = str_replace(' ', URE_SPACE_REPLACER, $capability);
?>
   <input type="checkbox" name="<?php echo $cap_id; ?>" id="<?php echo $cap_id; ?>" value="<?php echo $capability; ?>" <?php echo $checked; ?>/>
<?php
  if ($ure_caps_readable) {
?>
   <label for="<?php echo $cap_id; ?>" title="<?php echo $capability; ?>" ><?php _e(ure_ConvertCapsToReadable($capability),'ure'); ?></label><br/>
<?php
  } else {
?>
   <label for="<?php echo $cap_id; ?>" title="<?php _e(ure_ConvertCapsToReadable($capability),'ure'); ?>" ><?php echo $capability; ?></label><br/>
<?php
  }
   $quantInCell++;
   if ($quantInCell>=$quantInColumn) {
     $quantInCell = 0;
     echo '</td>
           <td style="vertical-align:top;">';
   }
  }
?>
            </td>
          </tr>
      </table>
<hr/>

    <div class="submit" style="padding-top: 0px;">
      <div style="float:left; padding-bottom: 10px;">
          <input type="submit" name="submit" value="<?php _e('Update', 'ure'); ?>" title="<?php _e('Save Changes', 'ure'); ?>" />
          <input type="button" name="cancel" value="<?php _e('Cancel', 'ure') ?>" title="<?php _e('Cancel not saved changes','ure');?>" onclick="ure_Actions('cancel');"/>          
      </div>
      <div style="float:right; padding-bottom: 10px;">
        <input type="button" name="default" value="<?php _e('Reset', 'ure') ?>" title="<?php _e('Restore Roles from backup copy','ure');?>" onclick="ure_Actions('reset');"/>
      </div>
    </div>
<?php
  ure_displayBoxEnd();
?>
		</div>

<?php
  $boxStyle = 'width: 330px; min-width:240px;margin-right: 10px;';
  ure_displayBoxStart(__('Add New Role', 'ure'), $boxStyle); ?>
<div class="ure-bottom-box-input">
  <input type="text" name="new_user_role" id="new_user_role" size="25"/>
</div>
<div class="submit" style="margin-left: 0; margin-right: 0; margin-bottom: 0; padding: 0; width: 100%; text-align: center;">
  <input type="button" name="addnewrole" value="<?php _e('Add', 'ure') ?>" title="<?php _e('Add New User Role','ure');?>" onclick="ure_Actions('addnewrole');" />
</div>
<?php
  ure_displayBoxEnd();
  ure_displayBoxStart(__('Default Role for New User', 'ure'), $boxStyle); ?>
<div class="ure-bottom-box-input">
  <?php echo $roleDefaultHTML; ?>
</div>
<div class="submit" style="margin-left: 0; margin-right: 0; margin-bottom: 0; padding: 0; width: 100%; text-align: center;">
  <input type="button" name="default" value="<?php _e('Change', 'ure') ?>" title="<?php _e('Set as Default User Role','ure');?>" onclick="ure_Actions('default');" />
</div>
<?php
    ure_displayBoxEnd();
  if ($roleDeleteHTML) {
    ure_displayBoxStart(__('Delete Role', 'ure'), $boxStyle); ?>
<div class="ure-bottom-box-input">
  <?php echo $roleDeleteHTML; ?>
</div>
<div class="submit" style="margin-left: 0; margin-right: 0; margin-bottom: 0; padding: 0; width: 100%; text-align: center;">
  <input type="button" name="deleterole" value="<?php _e('Delete', 'ure') ?>" title="<?php _e('Delete User Role','ure');?>" onclick="ure_Actions('delete');" />
</div>
<?php
    ure_displayBoxEnd();
  }

?>

				</div>
    </form>

