<?php
/*
Plugin Name: Peter's Collaboration E-mails
Plugin URI: http://www.theblog.ca/wordpress-collaboration-emails
Description: Enhance the "Submit for Review" feature for Contributor users. This plugin enables automatic e-mails to the relevant users when posts are pending, when they are approved, and when their statuses are changed from "pending" back to "draft".
Author: Peter Keung
Version: 1.4.0
Change Log:
2010-09-02  1.4.0: Added ability to specify contributor and moderator roles for sites with custom roles and capabilities
2010-04-25  1.3.5: E-mails are now all encoded in UTF-8.
2010-01-11  1.3.4: Plugin now removes its database tables when it is uninstalled, instead of when it is deactivated. This prevents the collaboration rules from being deleted when upgrading WordPress automatically.
2009-09-22  1.3.3: Maintenance release to remove unnecessary code calls and increase security.
2009-06-27  1.3.2: Minor fixes for translations.
2009-06-19  1.3.1: Updated for WordPress 2.8 so that the approver doesn't get an e-mail if they simply save an already pending post.
2009-02-16  1.3.0: Added e-mails at the "pending-to-future" and "future-to-publish" transitions.
2009-02-06  1.2.2: Backwards translation support for WordPress 2.5
2009-01-03  1.2.1: Added .po and .mo files for translators.
2008-12-10  1.2.0: Added another e-mail trigger: when a pending post's status is changed back to a draft. Also added interoperability with Peter's Post Notes (for WordPress 2.7 and up; http://www.theblog.ca/wordpress-post-notes) so that users can leave descriptive notes at each step in the workflow.
2008-09-18  1.1.0: You can specify moderators per category. This update also includes several bug fixes to the management page functionality.
2008-08-07  1.0.1: Database table names no longer use a fixed prefix. They now use whatever your WordPress installation uses ("wp_" by default).
2008-07-22  1.0.0: You can specify moderators per user. This is managed in the Settings section of the WordPress admin interface.
2007-11-11  0.2.0: You can specify a name and e-mail address for the sender of all collaboration e-mails or have the sender information default to the user performing the action. You can also toggle whether the post author should be told which user approved their post.
2007-10-31  First version. You can e-mail multiple moderators when a post is submitted for review. Also, the author is e-mailed when one of their posts is approved.
Author URI: http://www.theblog.ca/
*/

// ----------------------------------------------------------------------
// Follow the instructions in this section to customize the notifications
// ----------------------------------------------------------------------

// The URL to your site. Replace this with the base WordPress directory (containing the wp-admin folder) if the pending e-mail notification does not have the correct URL
$pce_siteurl = get_option('siteurl');

// The name of your blog, to appear in the title of e-mails. Replace this if e-mail subjects aren't correct
$pce_blogname = get_option('blogname');

// Enter the e-mail address for the person sending all e-mails. When this is set to false, the sender is the user performing the action. For example, the pending e-mail would be sent from the post author.
$pce_fromaddress = false;

// Enter the name for the person sending all e-mails. When this is set to false, the name is of the user performing the action.
$pce_fromname = false;

// Set this value to true if you want the contributor to know who approved his / her post.
// When this value is true, the above two settings are usually set to false
$pce_whoapproved = true;

// Which roles on your site can only "submit for review"
// Typically you do not have to edit this unless you have custom roles and capabilities
$pce_contributor_roles = array();
$pce_contributor_roles[] = 'contributor';

// Which roles on your site can approve posts
// Typically you do not have to edit this unless you have custom roles and capabilities
$pce_moderator_roles = array();
$pce_moderator_roles[] = 'administrator';
$pce_moderator_roles[] = 'editor';

global $wpdb;
global $pce_db_group;
// Name of the database table that will hold group information and moderator rules
$pce_db_group = $wpdb->prefix . 'collaboration';

global $pce_db_collab;
// Name of the database table that will hold group - collaborator associations
$pce_db_collab = $wpdb->prefix . 'collabwriters';

global $pce_db_cats;
// Name of the database table that will hold category-specific moderators
$pce_db_cats = $wpdb->prefix . 'collabcats';

// -------------------------------------------
// You should not have to edit below this line
// -------------------------------------------

global $pce_version;
$pce_version = '1.4.0';

// Enable translations
add_action('init', 'pce_textdomain');
function pce_textdomain() {
	load_plugin_textdomain('peters_collaboration_emails', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
}

function pce_pending($pce_newstatus, $pce_oldstatus, $pce_object) {
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_cats, $pce_siteurl, $pce_blogname, $pce_fromname, $pce_fromaddress, $pce_whoapproved, $user_identity, $user_email, $_POST;

    // The person who wrote the post
    $pce_thisuser = get_userdata($pce_object->post_author);

    // Get information about the currently logged in user, as the person submitting the post for review or approving it
    // Their name is mapped to $user_identity and their e-mail address is mapped to $user_email
    get_currentuserinfo();

    // If specified in the settings, assign the current user values as the e-mail sender information
    if (!$pce_fromname) $pce_fromname = $user_identity;
    if (!$pce_fromaddress) $pce_fromaddress = $user_email;

    // Line break, which we will use many times in constructing e-mails
    $pce_eol = "\r\n";

    // Post category
    $pce_postcats = $_POST['post_category'];
    if (0 == count($pce_postcats) || !is_array($pce_postcats)) {
        $pce_postcats = array(get_option('default_category'));
    }
    
    // If a note was submitted, we will use it in the e-mails
    if (isset($_POST['ppn_post_note']) && $_POST['ppn_post_note'] != '') {
        $pce_post_note = stripslashes( $_POST['ppn_post_note'] );
    }
    
    // Make sure the mail client knows it's a UTF-8 e-mail
    $pce_headers = 'Content-Type: text/plain; charset=utf-8' . $pce_eol;

    // E-mail moderator(s) for pending posts
    if ('pending' == $pce_newstatus && 'pending' != $pce_oldstatus) {

        $pce_moderators_unserialized = array();
        
        // Get the moderator information based on the collaboration rules
        $pce_collabgroups = $wpdb->get_results('SELECT groupid FROM ' . $pce_db_collab . ' WHERE writerid = ' . $pce_object->post_author, ARRAY_N);
        
        // If they are part of groups, get the moderator info for each group
        if ($pce_collabgroups) {
            foreach ($pce_collabgroups as $pce_collabgroup) {
                $pce_moderators = $wpdb->get_var('SELECT moderators FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_collabgroup[0]);
                $pce_moderators_unserialized = array_merge(unserialize($pce_moderators), $pce_moderators_unserialized);
            }
        }

        // See if there are moderators for the post category
        $pce_postcat = implode(',', $pce_postcats);
        $pce_moderators = $wpdb->get_results('SELECT moderators FROM ' . $pce_db_cats . ' WHERE catid IN (' . $pce_postcat . ')');

        if ($pce_moderators) {
            foreach($pce_moderators as $pce_moderator) {
                $pce_moderators_unserialized = array_merge(unserialize($pce_moderator->moderators), $pce_moderators_unserialized);
            }
        }
        // Remove duplicate entries for groups and categories
        $pce_moderators_unserialized = array_unique($pce_moderators_unserialized);

        // Get the default moderator information
        if (count($pce_moderators_unserialized) == 0) {
            $pce_moderators = $wpdb->get_var('SELECT moderators FROM ' . $pce_db_group . ' WHERE collabgroup = 1');
            $pce_moderators_unserialized = unserialize($pce_moderators);
        }
        $pce_moderators_emails = array();
        
        foreach ($pce_moderators_unserialized as $pce_moderator_unserialized) {
            if (is_numeric($pce_moderator_unserialized)) {
                $pce_moderator_data = get_userdata($pce_moderator_unserialized);
                $pce_moderators_emails[] = $pce_moderator_data->user_email;
            }
            elseif($pce_moderator_unserialized == 'admin') {
                $pce_moderators_emails[] = get_option('admin_email');
            }
            
            // must be an e-mail address
            else {
                $pce_moderators_emails[] = $pce_moderator_unserialized;
            }
        }
        
        // Remove duplicate entries after converting to e-mail addresses
        $pce_moderators_emails = array_unique($pce_moderators_emails);
        
        $pce_moderator = implode (', ', $pce_moderators_emails);

        // Header stuff for a pending post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From:' . $pce_fromname . ' <' . $pce_fromaddress . '>'. $pce_eol;
        $pce_headers .= 'Reply-To:' . $pce_fromname . ' <'. $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path:' . $pce_fromname . ' <'. $pce_fromaddress . '>' . $pce_eol;

        // Body of the e-mail for a pending post
        $pce_body = sprintf(__('There is a new post to review, written by %s.', 'peters_collaboration_emails'), $pce_fromname) . $pce_eol . $pce_eol;
        // Insert note if applicable
        if(isset($pce_post_note)) {
            $pce_body .= sprintf(__('Accompanying note from %s:', 'peters_collaboration_emails'), $pce_fromname) . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }
        $pce_body .= __('Review and publish it here: ', 'peters_collaboration_emails') . $pce_siteurl . '/wp-admin/post.php?action=edit&post=' . $pce_object->ID;

        // E-mail subject for a pending post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('pending', 'peters_collaboration_emails');

        // Send the notification e-mail for a pending post
        wp_mail($pce_moderator, $pce_subject, $pce_body, $pce_headers);
    }


    // E-mail the post author when a post is approved
    elseif ('pending' == $pce_oldstatus && 'publish' == $pce_newstatus) {

        // Header stuff for an approved post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_fromname. ' <' . $pce_fromaddress .'>' . $pce_eol;

        // E-mail body for an approved post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        $pce_body .= __('Your post has been approved', 'peters_collaboration_emails');
        if ($pce_whoapproved) $pce_body .= ' ' . __('by', 'peters_collaboration_emails') . ' ' . $pce_fromname; 
        $pce_body .= ' ' . __('and is now published.', 'peters_collaboration_emails') . $pce_eol . $pce_eol;
        // Insert note if applicable
        if(isset($pce_post_note)) {
            $pce_body .= __('Accompanying note:', 'peters_collaboration_emails') . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }
        $pce_body .= __('See it here:', 'peters_collaboration_emails') . ' ' . get_permalink($pce_object->ID);

        // E-mail subject for an approved post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('published', 'peters_collaboration_emails');

        // Send the notification e-mail for an approved post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    
    // E-mail the post author when a post is scheduled to be published
    elseif ('pending' == $pce_oldstatus && 'future' == $pce_newstatus) {

        // Header stuff for an approved post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_fromname. ' <' . $pce_fromaddress .'>' . $pce_eol;

        // E-mail body for a scheduled post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        $pce_body .= __('Your post has been approved', 'peters_collaboration_emails');
        if ($pce_whoapproved) $pce_body .= ' ' . __('by', 'peters_collaboration_emails') . ' ' . $pce_fromname; 
        $pce_body .= ' ' . sprintf(__('and is scheduled to be published on %s UTC %s.', 'peters_collaboration_emails'), $pce_object->post_date, get_option('gmt_offset')) . $pce_eol . $pce_eol;
        
        // Insert note if applicable
        if(isset($pce_post_note)) {
            $pce_body .= __('Accompanying note:', 'peters_collaboration_emails') . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }

        // E-mail subject for an approved post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('approved and scheduled', 'peters_collaboration_emails');

        // Send the notification e-mail for an approved post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    
    // E-mail the post author if their post is back to draft status
    elseif ('pending' == $pce_oldstatus && 'draft' == $pce_newstatus) {
        // E-mail the post author to let them know that their post has been published

        // Header stuff for a "back to draft" post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_fromname . ' <' . $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_fromname. ' <' . $pce_fromaddress .'>' . $pce_eol;

        // E-mail body for a "back to draft" post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        $pce_body .= __('Your post has been reverted back to draft status', 'peters_collaboration_emails');
        if ($pce_whoapproved) $pce_body .= ' ' . __('by', 'peters_collaboration_emails') . ' ' . $pce_fromname; 
        $pce_body .= '.' . $pce_eol . $pce_eol;
        
        if(isset($pce_post_note)) {
            $pce_body .= __('Accompanying note:', 'peters_collaboration_emails') . $pce_eol;
            $pce_body .= $pce_post_note . $pce_eol . $pce_eol;
        }
        
        $pce_body .= __('Edit it again here:', 'peters_collaboration_emails') . ' ' . $pce_siteurl . '/wp-admin/post.php?action=edit&post=' . $pce_object->ID;

        // E-mail subject for a "back to draft" post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('back to draft', 'peters_collaboration_emails');

        // Send the notification e-mail for a "back to draft" post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

    
    // E-mail author when his/her scheduled post is published
    elseif ('future' == $pce_oldstatus && 'publish' == $pce_newstatus) {

        $pce_fromaddress = get_option('admin_email');

        // Header stuff for a pending post
        // Header stuff from http://ca.php.net/mail
        $pce_headers .= 'From: ' . $pce_blogname . ' <' . $pce_fromaddress . '>'. $pce_eol;
        $pce_headers .= 'Reply-To: ' . $pce_blogname . ' <'. $pce_fromaddress . '>' . $pce_eol;
        $pce_headers .= 'Return-Path: ' . $pce_blogname . ' <'. $pce_fromaddress . '>' . $pce_eol;

        // Body of the e-mail for a previously-scheduled, now published post
        $pce_body = sprintf(__('Hi %s!', 'peters_collaboration_emails'), $pce_thisuser->display_name) . $pce_eol . $pce_eol;
        $pce_body .= __('Your post is now live.', 'peters_collaboration_emails') . $pce_eol . $pce_eol;
        $pce_body .= __('See it here:', 'peters_collaboration_emails') . ' ' . get_permalink($pce_object->ID);

        // E-mail subject for a previously-scheduled, now published post
        $pce_subject = '[' . $pce_blogname . '] "' . $pce_object->post_title . '" ' . __('is now live', 'peters_collaboration_emails');
        
        // Send the notification e-mail for a previously-scheduled, now published post
        wp_mail($pce_thisuser->user_email, $pce_subject, $pce_body, $pce_headers);
    }

}

add_filter('transition_post_status', 'pce_pending','',3);

if (is_admin()) { // This line makes sure that all of this code below only runs if someone is in the WordPress back-end

// This generates an option of checkbox output for contributors or editors and administrators in the system, as well as an "admin" and "other" choice
function pce_usersoptions($pce_existingmoderators = array(), $pce_contributors_or_moderators, $pce_optionsoutput = true, $pce_numbered = 0) {
    global $wpdb, $pce_contributor_roles, $pce_moderator_roles, $pce_moderatorcache;

    $pce_usersoptions = '';
    
    // Build SQL query portion to filter contributors or approvers
    $pce_contrib_approve_code = '';
    switch ($pce_contributors_or_moderators) {
        case 'contributors':
            $pce_filter_roles = $pce_contributor_roles;
            break;
        case 'moderators':
        default:
            $pce_filter_roles = $pce_moderator_roles;
            break;
    }
    $delimiter = '';
    foreach( $pce_filter_roles as $pce_filter_role )
    {
        $pce_contrib_approve_code .= $delimiter;
        $pce_contrib_approve_code .= "'%" . $pce_filter_role . "%'";
        $delimiter = ' OR ' . $wpdb->usermeta . '.meta_value LIKE ';
    }
    
    if (isset($pce_userresultscache) && $pce_contributors_or_moderators != 'contributors') {
        $pce_userresults = $pce_moderatorcache;
    }
    else {
        $pce_userresults = $wpdb->get_results("SELECT ID, $wpdb->users.display_name, $wpdb->users.user_email FROM $wpdb->users, $wpdb->usermeta WHERE $wpdb->users.ID = $wpdb->usermeta.user_id AND $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND ($wpdb->usermeta.meta_value LIKE " . $pce_contrib_approve_code . ") ORDER BY $wpdb->users.display_name", ARRAY_N);
    }
    if ($pce_userresults) {
        $i = $pce_numbered;
        foreach ($pce_userresults as $pce_userresult) {
            if (isset($pce_existingmoderators[$pce_userresult[0]])) {
                continue;
            }
            if ($pce_optionsoutput) {
                $pce_usersoptions .= "\n" . '                    <option value="' . $pce_userresult[0] . '">' . $pce_userresult[1] . ' (' . $pce_userresult[2] . ')</option>';
            }
            else {
                $pce_usersoptions .= "\n" . '                    <p><input type="checkbox" name="pce_contributors[' . $i . ']" value="' . $pce_userresult[0] . '" /> ' . $pce_userresult[1] . '</p>';
            }
            ++$i;
        }
    }
    if ($pce_contributors_or_moderators == 'moderators' && $pce_optionsoutput) {
        $pce_moderatorcache = $pce_userresults;
    
        if (!isset($pce_existingmoderators['admin'])) {
            $pce_usersoptions .= "\n" . '                    <option value="admin">' . __('Admin address', 'peters_collaboration_emails') . ' (' . get_option('admin_email') . ')</option>';
        }
        $pce_usersoptions .= "\n" . '                    <option value="other">' . __('Other', 'peters_collaboration_emails') . '</option>';
    }
    return $pce_usersoptions;
}

// All sorts of validation on moderators, returning either an error or an array of moderators
function pce_mod_array($pce_mods, $pce_add, $pce_other_field) {
    $pce_return_mods = array();

    $i = 0;
    
    foreach ($pce_mods as $pce_mod) {
        
        // Check that it is a valid user
        if (is_numeric($pce_mod)) {
            $pce_validuser = get_userdata($pce_mod);
            if (!$pce_validuser) {
                return __('**** ERROR: Invalid moderator user ID ****', 'peters_collaboration_emails');
            }
            $pce_return_mods[$i] = intval($pce_mod);
        }
        
        // If it's a checkbox, we need the value of the dropdown list
        elseif ($pce_mod == 'on') {

            // If the dropdown equals "other" then look for content in the "other" field, which had better be an e-mail address
            if ($pce_add == 'other' && is_email($pce_other_field)) {
                $pce_return_mods[$i] = $pce_other_field;
            }
            
            elseif (is_numeric($pce_add)) {
                $pce_validuser = get_userdata($pce_add);
                if (!$pce_validuser) {
                    return __('**** ERROR: Invalid moderator user ID ****', 'peters_collaboration_emails');
                }
                $pce_return_mods[$i] = intval($pce_add);
            }
            
            elseif ($pce_add == 'admin') {
                $pce_return_mods[$i] = $pce_add;
            }
            
            else {
               return __('**** ERROR: Invalid moderator e-mail address submitted ****', 'peters_collaboration_emails');
            }
        }
        
        // Must be an e-mail address or admin
        elseif (is_email($pce_mod) || $pce_mod == 'admin') {
            $pce_return_mods[$i] = $pce_mod;
        }
        
        else {
            return __('**** ERROR: Invalid e-mail address submitted ****', 'peters_collaboration_emails');
        }
        ++$i;
    }
    return $pce_return_mods;
}

// Processes changes to the moderator rules (who approves whose posts)
function pce_modsubmit() {
    global $wpdb, $pce_db_group;
    
    $pce_whitespace = '        ';
    
    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";
    
    // Code to close the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";
    
    // ----------------------------------
    // Process the default mod changes
    // ----------------------------------
    
    $pce_defaultmods = $_POST['pce_defaultmod']; // An array of default moderators (contains User IDs, "admin" or strictly e-mail addresses)
    $pce_defaultmods_update = array();
    if ($pce_defaultmods) {
        $pce_defaultmods_update = pce_mod_array($pce_defaultmods, $_POST['adddefaultmod'], $_POST['pce_defaultmodadd']);

        // Nicely scrubbed array of mods to serialize
        if (is_array($pce_defaultmods_update)) {
            $pce_defaultmod_serialized = serialize($pce_defaultmods_update);
        }

        // It return an error
        else {
            $pce_process_submit .= '<p><strong>' . $pce_defaultmods_update . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;
        }
        
        $pce_defaultmodsuccess = $wpdb->query('UPDATE ' . $pce_db_group . ' SET moderators = \'' . $pce_defaultmod_serialized . '\' WHERE collabgroup = 1');
        
        if ($pce_defaultmodsuccess) {
            $pce_process_submit .= $pce_whitespace . '    <p><strong>' . __('Default moderators updated.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        }
    }
    else {
        $pce_process_submit .= $pce_whitespace . '    <p><strong>' . __('You must have at least one default mod.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
    }

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

function pce_rulesubmit() {
    global $wpdb, $pce_db_group;

    $pce_whitespace = '        ';

    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";

    // Code to close the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";
    
    // ----------------------------------
    // Process the rule changes
    // ----------------------------------

    $pce_usermods = $_POST['pce_usermod']; // An array of moderators for each group (contains User IDs, "admin" or strictly e-mail addresses)
    $pce_groupids = $_POST['pce_groupid']; // An array of group IDs whose moderators need to be updated
    $pce_num_submits = array_keys($pce_groupids);
    
    if ($pce_num_submits) {
        foreach($pce_num_submits as $pce_num_submit) {
            $pce_usermods_update = array();
            $pce_usermod = $pce_usermods[$pce_num_submit];
            $pce_groupid = intval($pce_groupids[$pce_num_submit]);
            
            // Does this group exist?
            $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
            
            if (!$pce_groupname) {
                $pce_process_submit .= '<p><strong>' . sprintf(__('**** ERROR: Group with ID of %d does not exist ****', 'peters_collaboration_emails'), $pce_groupid) . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pre_process_submit;
            }
            
            if ($pce_usermod) {
                $pce_usermod_update = pce_mod_array($pce_usermod, $_POST['addusermod'][$pce_num_submit], $_POST['pce_usermodadd'][$pce_num_submit]); 
            
                // Nicely scrubbed array of mods to serialize
                if (is_array($pce_usermod_update)) {
                    $pce_usermod_serialized = serialize($pce_usermod_update);
                }
                
                // It returns an error
                else {
                    $pce_process_submit .= '<p><strong>' . $pce_usermod_update . '</strong></p>' . "\n";
                    $pce_process_submit .= $pce_process_close;
                    return $pce_process_submit;
                }
                
                $pce_usermodsuccess = $wpdb->query('UPDATE ' . $pce_db_group . ' SET moderators = \'' . $pce_usermod_serialized . '\' WHERE collabgroup = ' . $pce_groupid);
                if ($pce_usermodsuccess) {
                    $pce_process_submit .= $pce_whitespace . '    <p><strong>' . sprintf(__('Moderators for the group %s updated.', 'peters_collaboration_emails'), $pce_groupname) . '</strong></p>' . "\n";
                }
            }
            else {
                $pce_process_submit .= $pce_whitespace . '    <p><strong>' . sprintf(__('You must have at least one default mod for the group "%s".', 'peters_collaboration_emails'), $pce_groupname) . '</strong></p>' . "\n";
            }
        }
    }

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

function pce_groupsubmit() {
    global $wpdb, $pce_db_group, $pce_db_collab;

    $pce_whitespace = '        ';
    
    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";
    
    // Code for closing the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";
    
    // ----------------------------------
    // Process a new group addition
    // ----------------------------------

    if (!empty($_POST['newgroupname']) && $_POST['addrule'] != -1 && $_POST['addgroupmod'] != -1) {
        $newgroupname = $_POST['newgroupname'];
        $addrule = intval($_POST['addrule']);
        $addgroupmod = $_POST['addgroupmod'];
        
        // Check a contributor (basically that this contributor exists)
        $check_contributor = get_userdata($addrule);
        if (!$check_contributor) {
            $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid new group contributor user ID ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;        
        }
        
        // Check the added group moderator (admin, user ID, or e-mail address)

        // Check that it is a valid user
        if (is_numeric($addgroupmod)) {
            $pce_validuser = get_userdata($addgroupmod);
            if (!$pce_validuser) {
                $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid new group moderator user ID ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pce_process_submit;
            }
            $addgroupmod = intval($addgroupmod);
        }
            
        // If the dropdown equals "other" then look for content in pce_groupmodadd, which had better be an e-mail address
        elseif ($addgroupmod == 'other' && is_email($_POST['pce_groupmodadd'])) {
            $addgroupmod = $_POST['pce_groupmodadd'];
        }
        elseif ($addgroupmod != 'admin') {
            $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid new group moderator submitted ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;
        }
        
        $addgroupmod_serialized = serialize(array($addgroupmod));
        $pce_addgroupsuccess = $wpdb->query('INSERT INTO ' . $pce_db_group . ' (moderators, groupname) VALUES(\'' . $addgroupmod_serialized . '\', \'' . $newgroupname . '\')');
        if ($pce_addgroupsuccess) {
            $pce_addwritersuccess = $wpdb->query('INSERT INTO ' . $pce_db_collab . ' (groupid, writerid) VALUES (LAST_INSERT_ID(), ' . $addrule . ')');
            if ($pce_addwritersuccess) {
                $pce_process_submit .= $pce_whitespace . '    <p><strong>' . __('New group created.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            }
            else {
                $pce_process_submit .= '<p><strong>' . __('**** ERROR: Unknown query error when adding a collaborator to the new group ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pce_process_submit;  
            }
        }
        else {
            $pce_process_submit .= '<p><strong>' . __('**** ERROR: Unknown query error when creating new group ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;            
        }
    }
    
    else {
        $pce_process_submit .= '<p><strong>' . __('**** ERROR: Not all necessary group information was submitted to add a group ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= $pce_process_close;
        return $pce_process_submit;
    }
    
    // 

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

// Processes changes to a group name and its members
function pce_edit_group_submit() {
    global $wpdb, $pce_db_group, $pce_db_collab;
    $pce_groupid = intval($_GET['group']);
    
    $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
    
    if (!$pce_groupname) {
        die(__('That group does not exist.', 'peters_collaboration_emails'));
    }
    
    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";
    
    // Code to close the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";
    
    if (!empty($_POST['pce_groupname']) && !empty($_POST['pce_contributors'])) {
        $pce_groupname = $_POST['pce_groupname'];
        $pce_contributors = $_POST['pce_contributors'];
    }
    else {
        $pce_process_submit .= '<p><strong>' . __('**** ERROR: Insufficient group name or contributor information ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= '<p><strong>' . __('**** Make sure that there is at least one contributor. ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= $pce_process_close;
        return $pce_process_submit;    
    }

    $pce_whitespace = '        ';
    
    // ----------------------------------
    // Process the group changes
    // ----------------------------------
    
    // First find out which contributors already exist
    $pce_existing_contributors = $wpdb->get_results('SELECT writerid FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid, ARRAY_N);
    $pce_existing_contributor_array = array();
    
    if ($pce_existing_contributors) {
        foreach($pce_existing_contributors as $pce_existing_contributor) {
            $pce_existing_contributor_array[$pce_existing_contributor[0]] = $pce_existing_contributor[0];
        }
    }
    
    $pce_insert_writer = false;
    
    $pce_contributors_update = array();
        
    foreach ($pce_contributors as $pce_contributor) {
            
        // Check that it is a valid user
        if (is_numeric($pce_contributor)) {
            $pce_validcontributor = get_userdata($pce_contributor);
            if (!$pce_validcontributor) {
                $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid contributor user ID ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pce_process_submit;
            }
            if (isset($pce_existing_contributor_array[$pce_contributor])) {
                unset($pce_existing_contributor_array[$pce_contributor]);
            }
            else {
                $pce_insert_success = $wpdb->query('INSERT INTO ' . $pce_db_collab . ' (groupid, writerid) VALUES (' . $pce_groupid . ', ' . $pce_contributor. ')');
                if ($pce_insert_success && !$pce_insert_writer) {
                    $pce_insert_writer = true;
                }
            }                        
        }
    }
    if (!empty($pce_existing_contributor_array)) {
        $pce_delete_contributors = $wpdb->query('DELETE FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid . ' AND writerid IN (' . implode(',', $pce_existing_contributor_array) . ')'); 
        if ($pce_delete_contributors && !$pce_insert_writer) {
            $pce_insert_writer = true;
        }
    }
    
    if ($pce_insert_writer) {
        $pce_process_submit .= $pce_whitespace . '    <p><strong>' . __('Collaborators updated.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
    }
    $pce_groupname_success = $wpdb->query('UPDATE ' . $pce_db_group . ' SET groupname = \'' . $pce_groupname . '\' WHERE collabgroup = ' . $pce_groupid);
        
    if ($pce_groupname_success) {
        $pce_process_submit .= $pce_whitespace . '    <p><strong>' . __('Group name updated.', 'peters_collaboration_emails') . '</strong></p>' . "\n";
    }

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

// Deletes a group
function pce_delete_group_submit() {
    global $wpdb, $pce_db_group, $pce_db_collab;
    
    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";
    
    // Code to close the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";

    $pce_groupid = intval($_POST['pce_groupid']);
    
    $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
    
    if (!$pce_groupname) {
        $pce_process_submit .= '<p><strong>' . __('**** ERROR: That group does not exist ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= $pce_process_close;
        return $pce_process_submit;    
    }

    $pce_whitespace = '        ';
    
    // ----------------------------------
    // Process the group deletion
    // ----------------------------------
    
    // Remove all contributors
    $pce_remove_contributors = $wpdb->query('DELETE FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid);
    
    // Remove the group
    $pce_remove_group = $wpdb->query('DELETE FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid . ' LIMIT 1');
    
    if ($pce_remove_contributors && $pce_remove_group) {
        $pce_process_submit .= '<p><strong>' . sprintf(__('Group %s successfully deleted.', 'peters_collaboration_emails'), $pce_groupname) . '</strong></p>' . "\n";    
    }
    else {
        $pce_process_submit .= '<p><strong>' . __('**** ERROR: Database problem in removing the group. ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";       
    }

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // Return the good or bad news
    return $pce_process_submit;
}

// Edit category-specific moderators

function pce_catsubmit() {
    global $wpdb, $pce_db_cats;

    $pce_whitespace = '        ';

    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";

    // Code to close the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";
    
    // ----------------------------------
    // Process the category-specific moderator changes
    // ----------------------------------
    $pce_catmods = $_POST['pce_catmod']; // An array of moderators for each group (contains User IDs, "admin" or strictly e-mail addresses)
    $pce_catids = $_POST['pce_catid']; // An array of category IDs whose moderators need to be updated
    $pce_num_submits = array_keys($pce_catids);

    if ($pce_num_submits) {
        foreach($pce_num_submits as $pce_num_submit) {
            $pce_catmods_update = array();
            $pce_catmod = $pce_catmods[$pce_num_submit];
            $pce_catid = intval($pce_catids[$pce_num_submit]);
            
            // Does this category exist?
            $pce_catname = get_cat_name($pce_catid);
            
            if (!$pce_catname) {
                $pce_process_submit .= '<p><strong>' . sprintf(__('**** ERROR: Category with ID of %d does not exist ****', 'peters_collaboration_emails'), $pce_catid) . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pre_process_submit;
            }
            
            if ($pce_catmod) {
                $pce_catmod_update = pce_mod_array($pce_catmod, $_POST['addcatmod'][$pce_num_submit], $_POST['pce_catmodadd'][$pce_num_submit]);

                // Nicely scrubbed array of mods to serialize
                if (is_array($pce_catmod_update)) {
                    $pce_catmod_serialized = serialize($pce_catmod_update);
                }
                // It returns an error
                else {
                    $pce_process_submit .= '<p><strong>' . $pce_catmod_update . '</strong></p>' . "\n";
                    $pce_process_submit .= $pce_process_close;
                    return $pce_process_submit;
                }
                
                $pce_catmodsuccess = $wpdb->update(
                    $pce_db_cats, 
                    array ('moderators' => $pce_catmod_serialized),
                    array ('catid' => $pce_catid));
                    
                if ($pce_catmodsuccess) {
                    $pce_process_submit .= $pce_whitespace . '    <p><strong>' . sprintf(__('Moderators for the category "%s" updated.', 'peters_collaboration_emails'), $pce_catname) . '</strong></p>' . "\n";
                }
            }
            else {
                $pce_process_submit .= $pce_whitespace . '    <p><strong>' . sprintf(__('You must have at least one default mod for the category "%s".', 'peters_collaboration_emails'), $pce_catname) . '</strong></p>' . "\n";
            }
        }
    }
    
    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

function pce_addcatsubmit() {
    global $wpdb, $pce_db_cats;

    $pce_whitespace = '        ';
    
    // Open the informational div
    $pce_process_submit = '<div id="message" class="updated fade">' . "\n";
    
    // Code for closing the informational div
    $pce_process_close = $pce_whitespace . '</div>' . "\n";
    
    // ----------------------------------
    // Process a new category moderator rule addition
    // ----------------------------------

    if ($_POST['addcatmod'] != -1) {
        $addcat = intval($_POST['cat']);
        $addcatmod = $_POST['addcatmod'];
        
        // Make sure this category exists
        $addcat_name = get_cat_name($addcat);
        if (!$addcat_name) {
            $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid category ID ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;        
        }
        
        // Check the added category moderator (admin, user ID, or e-mail address)

        // Check that it is a valid user
        if (is_numeric($addcatmod)) {
            $pce_validuser = get_userdata($addcatmod);
            if (!$pce_validuser) {
                $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid new category moderator user ID ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
                $pce_process_submit .= $pce_process_close;
                return $pce_process_submit;
            }
            $addcatmod = intval($addcatmod);
        }
            
        // If the dropdown equals "other" then look for content in pce_catmodadd, which had better be an e-mail address
        elseif ($addcatmod == 'other' && is_email($_POST['pce_catmodadd'])) {
            $addcatmod = $_POST['pce_catmodadd'];
        }
        elseif ($addcatmod != 'admin') {
            $pce_process_submit .= '<p><strong>' . __('**** ERROR: Invalid new category moderator submitted ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;
        }
        
        $addcatmod_serialized = serialize(array($addcatmod));
        $pce_addcatsuccess = $wpdb->insert(
            $pce_db_cats,
            array('catid' => $addcat,
                'moderators' => $addcatmod_serialized)
            );

        if ($pce_addcatsuccess) {
            $pce_process_submit .= $pce_whitespace . '    <p><strong>' . sprintf(__('New moderator added for the %s category.', 'peters_collaboration_emails'), $addcat_name) . '</strong></p>' . "\n";
        }
        else {
            $pce_process_submit .= '<p><strong>' . __('**** ERROR: Unknown query error when adding a new category moderator ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
            $pce_process_submit .= $pce_process_close;
            return $pce_process_submit;            
        }
    }
    
    else {
        $pce_process_submit .= '<p><strong>' . __('**** ERROR: No moderator was submitted for the category ****', 'peters_collaboration_emails') . '</strong></p>' . "\n";
        $pce_process_submit .= $pce_process_close;
        return $pce_process_submit;
    }
    
    // 

    // Close the informational div
    $pce_process_submit .= $pce_process_close;
    
    // We've made it this far, so success!
    return $pce_process_submit;
}

// This is the options page in the WordPress admin panel that enables you to set moderators on a per-user basis
function pce_optionsmenu() {
    if (isset($_GET['group'])) {
        pce_groupoptionsmenu();
    }
    elseif (isset($_GET['delete_cat'])) {
        pce_deletecat();
    }
    else {
        pce_mainoptionsmenu();
    }
}

function pce_groupoptionsmenu() {
    global $wpdb, $pce_db_group, $pce_db_collab;
    $pce_groupid = intval($_GET['group']);
    
    $pce_process_submit = '';
    
    // Update the group name and contributors
    if ($_POST['pce_edit_group_submit']) {
        $pce_process_submit = pce_edit_group_submit();
    }
    
    $pce_groupname = $wpdb->get_var('SELECT groupname FROM ' . $pce_db_group . ' WHERE collabgroup = ' . $pce_groupid);
    
    if (!$pce_groupname) {
        die(__('That group does not exist.', 'peters_collaboration_emails'));
    }
    
    $pce_groupname = htmlspecialchars($pce_groupname, ENT_QUOTES);
    
    $pce_contributors = $wpdb->get_results('SELECT writerid FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid, ARRAY_N);
    $pce_contributors_current = '';
    $pce_contributors_whitespace = '                    ';
    
    if ($pce_contributors) {
        $pce_contributors_array = array();
        $i = 0;
        foreach ($pce_contributors as $pce_contributor) {
            $pce_contributor_data = get_userdata($pce_contributor[0]);
            
            if ($pce_contributor_data) {
                $pce_contributors_current .= "\n" . $pce_contributors_whitespace . '<p><input type="checkbox" name="pce_contributors[' . $i . ']" value="' . $pce_contributor[0] . '" checked="checked"/> ' . $pce_contributor_data->display_name . '</p>';
                $pce_contributors_array[$pce_contributor[0]] = '';
            }
            ++$i;
        }
    }
    
    
    
    // Contributors that aren't part of this group
    $pce_contributors_remaining = pce_usersoptions($pce_contributors_array, 'contributors', false, $i);
?>
    <div class="wrap">
        <h2><?php _e('Manage group:', 'peters_collaboration_emails'); ?> <?php print $pce_groupname; ?></h2>
        <?php print $pce_process_submit; ?>
        <p><a href="<?php print '?page=' . basename(__FILE__); ?>"><?php _e('Back to the main collaboration config menu', 'peters_collaboration_emails'); ?></a></p>
        <form name="pce_edit_group" method="post" action="<?php print '?page=' . basename(__FILE__) . '&group=' . $pce_groupid; ?>">
            <p><?php _e('Group name:', 'peters_collaboration_emails'); ?> <input type="text" width="30" maxlength="90" name="pce_groupname" value="<?php print $pce_groupname; ?>" /></p>
            <p><strong><?php _e('Contributors in this group:', 'peters_collaboration_emails'); ?></strong></p>
            <?php print $pce_contributors_current; ?>            
            <?php print $pce_contributors_remaining; ?>
            
            <p class="submit"><input type="submit" name="pce_edit_group_submit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        
        <form name="pce_delete_group" method="post" action="<?php print '?page=' . basename(__FILE__); ?>">
            <p class="submit"><input type="hidden" name="pce_groupid" value="<?php print $pce_groupid;?>"><input type="submit" name="pce_delete_group_submit" value="<?php _e('Delete this group', 'peters_collaboration_emails'); ?>" /></p>
        </form>
    </div>
    
<?php
}
function pce_deletecat() {
    global $wpdb, $pce_db_cats;
    $pce_catid = intval($_GET['delete_cat']);
?>
    <div class="wrap">
<?php
    $pce_catname = get_cat_name($pce_catid);
    if (!$pce_catname || $_POST['pce_delete_cat_yes']) {
        
        // This check runs even if you didn't confirm the deletion because maybe the category doesn't even exist
        $pce_catexists = $wpdb->get_var('SELECT COUNT(*) FROM ' . $pce_db_cats . ' WHERE catid = ' . $pce_catid);
        if ($pce_catexists == 1) {
            $wpdb->query('DELETE FROM ' . $pce_db_cats . ' WHERE catid = ' . $pce_catid . ' LIMIT 1');
            
            // If they actually wanted to delete the moderators for this category, let them know the result
            if ($_POST['pce_delete_cat_yes']) {
                print '<p><strong>' . $pce_catname . '</strong> category successfully deleted.</p>' . "\n";
                print '<p><a href="?page=' . basename(__FILE__) . '">Back</a></p>' . "\n";
            }
        }
        else {
            print '<p>That category does not exist.</p>' . "\n";
            print '<p><a href="?page=' . basename(__FILE__) . '">Back</a></p>' . "\n";
        }
    }

    else {
?>
        <p><?php sprintf(_e('Are you sure you want to remove the moderators for the <strong>%s</strong> category?', 'peters_collaboration_emails'), $pce_catname); ?></p>
        <form method="post" action="<?php print '?page=' . basename(__FILE__) . '&delete_cat=' . $pce_catid; ?>">            
            <p class="submit"><input type="submit" name="pce_delete_cat_yes" value="<?php _e('Yes', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        
        <form method="post" action="<?php print '?page=' . basename(__FILE__); ?>">
            <p class="submit"><input type="submit" value="<?php _e('No, go back', 'peters_collaboration_emails'); ?>"></p>
        </form>    
<?php
    }
?>
    </div>
<?php
}

function pce_mainoptionsmenu() {
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_cats;
    
    // Upgrade for pre-1.1.0 versions
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_cats . '\'') != $pce_db_cats) {
        pce_upgrade_category_table();
    }
    
    if ($_POST['pce_modsubmit']) {    
        $pce_process_submit = pce_modsubmit();
    }
    elseif ($_POST['pce_rulesubmit']) {
        $pce_process_submit = pce_rulesubmit();
    }
    elseif ($_POST['pce_groupsubmit']) {
        $pce_process_submit = pce_groupsubmit();
    }
    elseif ($_POST['pce_delete_group_submit']) {
        $pce_process_submit = pce_delete_group_submit();
    }
    elseif ($_POST['pce_catsubmit']) {
        $pce_process_submit = pce_catsubmit();
    }
    elseif ($_POST['pce_addcatsubmit']) {
        $pce_process_submit = pce_addcatsubmit();
    }
    
    // -----------------------------------
    // Get the list of default moderators
    // -----------------------------------
    
    $pce_defaultmods_serialized = $wpdb->get_var('SELECT moderators FROM ' . $pce_db_group . ' WHERE collabgroup = 1');
    
    // Put this list into an array since it is stored in the database as serialized
    $pce_defaultmods = unserialize($pce_defaultmods_serialized);

    // Build the list of options based on this array

    // Set up the default options variable
    $pce_defaultoptions = '';

    // Whitespace!
    $pce_defaultoptionswhitespace = '                ';

    // Establish a counter for the checkboxes
    $i = 0;

    $pce_existingmods = array();
    
    foreach ($pce_defaultmods as $pce_defaultmod) {
        // If they've chosen a user ID, get the e-mail address associated with that user ID
        if (is_numeric($pce_defaultmod)) {
            $pce_userinfo = get_userdata($pce_defaultmod);
            $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '<p><input type="checkbox" name="pce_defaultmod[' . $i . ']" value="' . $pce_defaultmod . '" checked="checked" /> ' . $pce_userinfo->display_name . ' (' . $pce_userinfo->user_email . ')</p>';
            $pce_existingmods[$pce_defaultmod] = '';
        }

        // If they've chosen it to be the site admin, get the site admin e-mail address
        elseif ($pce_defaultmod == 'admin') {
            $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace  . '<p><input type="checkbox" name="pce_defaultmod[' . $i . ']" value="' . $pce_defaultmod . '" checked="checked" /> ' . __( 'General admin', 'peters_collaboration_emails' ) . '(' . get_option('admin_email') . ')</p>';
            $pce_existingmods['admin'] = '';
        }
        
        // Whatever is left should be a custom e-mail address
        else {
            $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '<p><input type="checkbox" name="pce_defaultmod[' . $i . ']" value="' . $pce_defaultmod . '" checked="checked" /> ' . $pce_defaultmod . '</p>';
        }

        ++$i;
    }
    
    $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '<p><input type="checkbox" name="pce_defaultmod[' . $i .']" /> ' . __( 'Add:', 'peters_collaboration_emails' ) . ' <select name="adddefaultmod" id="adddefaultmod" onchange="addMod(\'adddefaultmod\');">';
    $pce_defaultoptions .= pce_usersoptions($pce_existingmods, 'moderators');
    $pce_defaultoptions .= "\n" . $pce_defaultoptionswhitespace . '</select></p><p id="pce_adddefaultmod">E-mail: <input type="text" name="pce_defaultmodadd" width="30" maxlength="90" /></p>';
    
    // -----------------------------------
    // Get the group-specific moderator rules
    // -----------------------------------
    
    $pce_usermods_results = $wpdb->get_results('SELECT collabgroup, moderators, groupname FROM ' . $pce_db_group . ' WHERE collabgroup != 1 ORDER BY groupname', ARRAY_N);
    
    if ($pce_usermods_results) {

        $i_m = 0;
        
        // Set up the default options variable
        $pce_useroptions = '';
        
        // Whitespace!
        $pce_useroptionswhitespace = '                ';
        
        foreach ($pce_usermods_results as $pce_usermod_result) {
        
            // Define the group name
            $pce_groupname = htmlspecialchars($pce_usermod_result[2], ENT_QUOTES);
            
            $pce_useroptions .= '<tr>' . "\n";
            $pce_useroptions .= $pce_useroptionswhitespace . '<td><p><strong>' . $pce_groupname . '</strong> [<a href="?page=' . basename(__FILE__) . '&group=' . $pce_usermod_result[0]. '">Edit</a>]</p>';

            
            // Define the group ID
            $pce_groupid = $pce_usermod_result[0];
            
            // Get the writers in this group
            $pce_writers = $wpdb->get_results('SELECT writerid FROM ' . $pce_db_collab . ' WHERE groupid = ' . $pce_groupid, ARRAY_N);
            
            if ($pce_writers) {
            
                $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '<p>';
                
                foreach ($pce_writers as $pce_writer) {
                    $pce_thiswriter = get_userdata($pce_writer[0]);
                    $pce_useroptions .= "\n" . $pce_useroptionswhitespace . $pce_thiswriter->display_name . '<br />';
                }
                $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '</p>';
            }
            
            $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '</td>';
                        
            // Put this list of e-mail addresses an array since it is stored in the database as serialized
            $pce_usermods = unserialize($pce_usermod_result[1]);

            // Build the list of options based on this array

            // Establish a counter for the checkboxes
            $i = 0;
            
            $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '<td>';
            
            $pce_existingmods = array();
            
            foreach ($pce_usermods as $pce_usermod) {

                // If they've chosen a user ID, get the e-mail address associated with that user ID
                if (is_int($pce_usermod)) {
                    $pce_userinfo = get_userdata($pce_usermod);
                    $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '    <p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" value="' . $pce_usermod . '" checked="checked" /> ' . $pce_userinfo->display_name . ' (' . $pce_userinfo->user_email . ')</p>';
                    $pce_existingmods[$pce_usermod] = '';
                }

                // If they've chosen it to be the site admin, get the site admin e-mail address
                elseif ($pce_usermod == 'admin') {
                    $pce_useroptions .= "\n" . $pce_useroptionswhitespace  . '    <p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" value="' . $pce_usermod . '" checked="checked" /> ' . __( 'General admin', 'peters_collaboration_emails' ) . '(' . get_option('admin_email') . ')</p>';
                    $pce_existingmods['admin'] = '';
                }
                
                // Whatever is left should be a custom e-mail address
                else {
                    $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '    <p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" value="' . $pce_usermod . '" checked="checked" /> ' . $pce_usermod . '</p>';
                }
                
                ++$i;
            }
            
            $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '    <p><input type="checkbox" name="pce_usermod[' . $i_m . '][' . $i .']" /> ' . __( 'Add:', 'peters_collaboration_emails' ) . ' <select name="addusermod[' . $i_m . ']" id="usermodadd[' . $i_m . ']" onchange="addMod(\'usermodadd[' . $i_m . ']\')">';
            $pce_useroptions .= pce_usersoptions($pce_existingmods, 'moderators');
            $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '    </select></p><p id="pce_usermodadd[' . $i_m . ']">E-mail: <input type="text" name="pce_usermodadd[' . $i_m . ']" width="30" maxlength="90" /></p>';
            $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '<input type="hidden" name="pce_groupid[' . $i_m . ']" value="' . $pce_groupid . '" /></td>';
            $pce_useroptions .= "\n" . $pce_useroptionswhitespace . '</tr>';
            ++$i_m;
        }
    }
    
    // --------------------------------------------------------------------
    // Form to add a group, needing at least one user and at least one moderator 
    // --------------------------------------------------------------------
    
    $pce_groupoptions = '';
    
    $pce_groupoptions .= '<p>' . __( 'Group name:', 'peters_collaboration_emails' ) . ' <input type="text" name="newgroupname" width="30" maxlength="90" /></p>'; 
    $pce_groupoptions .= "\n" . $pce_useroptionswhitespace . '<p>' . __('Add contributor:', 'peters_collaboration_emails') . ' <select name="addrule">';
    $pce_groupoptions .= "\n" . $pce_useroptionswhitespace . '    <option value="-1"></option>';
    
    // This list should only include users
    $pce_groupoptions .= pce_usersoptions(array(), 'contributors');
    $pce_groupoptions .= "\n" . '            </select>';
    
    $pce_groupoptions .= "\n" . $pce_useroptionswhitespace . '    <p>' . __('Add moderator:', 'peters_collaboration_emails') . ' <select name="addgroupmod" id="groupmodadd" onchange="addMod(\'groupmodadd\')">';
    $pce_groupoptions .= "\n" . $pce_useroptionswhitespace . '    <option value="-1"></option>';
    $pce_groupoptions .= pce_usersoptions(array(), 'moderators');
    $pce_groupoptions .= "\n" . $pce_useroptionswhitespace . '    </select></p><p id="pce_groupmodadd">E-mail: <input type="text" name="pce_groupmodadd" width="30" maxlength="90" /></p>'; 

    // -----------------------------------
    // Get the category-specific moderator rules
    // -----------------------------------
    
    $pce_catmods_results = $wpdb->get_results('SELECT catid, moderators FROM ' . $pce_db_cats);
    $pce_existingcatids = array();
    
    if ($pce_catmods_results) {
    
        $i_c = 0;
        
        // Set up the default options variable
        $pce_catoptions = '';    
        
        foreach ($pce_catmods_results as $pce_catmod_result) {
        
            // Define the group name
            $pce_catname = get_cat_name($pce_catmod_result->catid);
            
            // Keep track of the existing category IDs so that we can exclude them for the "add" form
            $pce_existingcatids[] = $pce_catmod_result->catid;
            
            $pce_catoptions .= '<tr>' . "\n";
            $pce_catoptions .= $pce_useroptionswhitespace . '<td><p><strong>' . $pce_catname . '</strong> [<a href="?page=' . basename(__FILE__) . '&delete_cat=' . $pce_catmod_result->catid . '">X</a>]</p></td>';
            // Get the moderators for this category
            $pce_catmods = unserialize($pce_catmod_result->moderators);

            // Build the list of options based on this array
            
            // Establish a counter for the checkboxes
            $i = 0;
            
            $pce_existingmods = array();
            $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '<td>';
            
            foreach ($pce_catmods as $pce_catmod) {
                // If they've chosen a user ID, get the e-mail address associated with that user ID
                if (is_int($pce_catmod)) {
                    $pce_userinfo = get_userdata($pce_catmod);
                    $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '    <p><input type="checkbox" name="pce_catmod[' . $i_c . '][' . $i .']" value="' . $pce_catmod . '" checked="checked" /> ' . $pce_userinfo->display_name . ' (' . $pce_userinfo->user_email . ')</p>';
                    $pce_existingmods[$pce_catmod] = '';
                }

                // If they've chosen it to be the site admin, get the site admin e-mail address
                elseif ($pce_catmod == 'admin') {
                    $pce_catoptions .= "\n" . $pce_useroptionswhitespace  . '    <p><input type="checkbox" name="pce_catmod[' . $i_c . '][' . $i .']" value="' . $pce_catmod . '" checked="checked" /> ' . __( 'General admin', 'peters_collaboration_emails' ) . '(' . get_option('admin_email') . ')</p>';
                    $pce_existingmods['admin'] = '';
                }
                
                // Whatever is left should be a custom e-mail address
                else {
                    $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '    <p><input type="checkbox" name="pce_catmod[' . $i_c . '][' . $i .']" value="' . $pce_catmod . '" checked="checked" /> ' . $pce_catmod . '</p>';
                }
                
                ++$i;
            }
            $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '    <p><input type="checkbox" name="pce_catmod[' . $i_c . '][' . $i .']" /> ' . __('Add:', 'peters_collaboration_emails') . ' <select name="addcatmod[' . $i_c . ']" id="catmodadd[' . $i_c . ']" onchange="addMod(\'catmodadd[' . $i_c . ']\')">';
            $pce_catoptions .= pce_usersoptions($pce_existingmods, 'moderators');
            $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '    </select></p><p id="pce_catmodadd[' . $i_c . ']">' . __('E-mail:' , 'peters_collaboration_emails') . ' <input type="text" name="pce_catmodadd[' . $i_c . ']" width="30" maxlength="90" /></p>';
            $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '<input type="hidden" name="pce_catid[' . $i_c . ']" value="' . $pce_catmod_result->catid . '" /></td>';        
            $pce_catoptions .= "\n" . $pce_useroptionswhitespace . '</tr>';
            ++$i_c;
        }
    }
    
    // --------------------------------------------------------------------
    // Form to add a category, needing at least one moderator 
    // --------------------------------------------------------------------    

    $pce_addcatoptions = '';

    $pce_addcatoptions .= '<p>' . __('Category:', 'peters_collaboration_emails') . ' ';
    $pce_existingcatids_implode = implode(',', $pce_existingcatids);
    $pce_addcatoptions .= wp_dropdown_categories(array('orderby' => 'name', 'hide_empty' => 0, 'exclude' => $pce_existingcatids_implode, 'echo' => 0, 'hierarchical' => 1));
    $pce_addcatoptions .= '</p>'; 
    $pce_addcatoptions .= "\n" . $pce_useroptionswhitespace . '    <p>' . __('Add moderator:', 'peters_collaboration_emails') . '    <select name="addcatmod" id="catmodadd" onchange="addMod(\'catmodadd\')">';
    $pce_addcatoptions .= "\n" . $pce_useroptionswhitespace . '    <option value="-1"></option>';
    $pce_addcatoptions .= pce_usersoptions(array(), 'moderators');
    $pce_addcatoptions .= "\n" . $pce_useroptionswhitespace . '    </select></p><p id="pce_catmodadd">' . __('E-mail:' ,'peters_collaboration_emails') . ' <input type="text" name="pce_catmodadd" width="30" maxlength="90" /></p>';
 
?>
    <div class="wrap">
        <h2><?php _e('Manage collaboration e-mails', 'peters_collaboration_emails'); ?></h2>
        <p><?php _e('Set the moderators who should be e-mailed whenever Contributor users submit pending posts.', 'peters_collaboration_emails'); ?></p>
        <?php print $pce_process_submit; ?>

        <h3><?php _e('Default moderators', 'peters_collaboration_emails'); ?></h3>
        <form name="pce_modform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <p><?php _e('These users will be e-mailed if none of the rules below match. Note that they must be either editors or administrators.', 'peters_collaboration_emails'); ?></p>
            <?php print $pce_defaultoptions; ?>
            
        <p class="submit"><input type="submit" name="pce_modsubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
            
        <h3><?php _e('Moderators by group', 'peters_collaboration_emails'); ?></h3>
        <form name="pce_ruleform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Existing rules', 'peters_collaboration_emails'); ?></h4>
        <table class="widefat">
            <tr>
                <th><?php _e('Group', 'peters_collaboration_emails'); ?></th>
                <th><?php _e('Rules', 'peters_collaboration_emails'); ?></th>
            </tr>
            <?php print $pce_useroptions; ?>
            
        </table>
        <p class="submit"><input type="hidden" id="pce_num_adds" value="<?php print $i_m; ?>" /><input type="submit" name="pce_rulesubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        <form name="pce_groupform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Add a group', 'peters_collaboration_emails'); ?></h4>
            <?php print $pce_groupoptions; ?>
        
        <p class="submit"><input type="submit" name="pce_groupsubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        
        <h3><?php _e('Moderators by category', 'peters_collaboration_emails'); ?></h3>
        <form name="pce_catform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Existing rules', 'peters_collaboration_emails'); ?></h4>
        <table class="widefat">
            <tr>
                <th><?php _e('Category', 'peters_collaboration_emails'); ?></th>
                <th><?php _e('Rules', 'peters_collaboration_emails'); ?></th>
            </tr>
            <?php print $pce_catoptions; ?>
            
        </table>
        <p class="submit"><input type="hidden" id="pce_cat_adds" value="<?php print $i_c; ?>" /><input type="submit" name="pce_catsubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
        <form name="pce_addcatform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <h4><?php _e('Add category-specific moderators', 'peters_collaboration_emails'); ?></h4>
            <?php print $pce_addcatoptions; ?>
        
        <p class="submit"><input type="submit" name="pce_addcatsubmit" value="<?php _e('Update', 'peters_collaboration_emails'); ?>" /></p>
        </form>
    </div>

    <script type="text/javascript">
        var defaultModTextField = document.getElementById("pce_adddefaultmod");
        var defaultModSelectField = document.getElementById("adddefaultmod");
        if (defaultModSelectField.value != 'other') {
            defaultModTextField.style.display = 'none';
        }

        var numAdds = document.getElementById("pce_num_adds").value;
        for (i=0; i < numAdds; ++i) {
            var otherTextField = document.getElementById("pce_usermodadd[" + i + "]");
            var otherSelectField = document.getElementById("usermodadd[" + i + "]");
            if (otherSelectField.value != 'other') {
                otherTextField.style.display = 'none';
            }
       }

        var catAdds = document.getElementById("pce_cat_adds").value;
        for (i=0; i < catAdds; ++i) {
            var catOtherTextField = document.getElementById("pce_catmodadd[" + i + "]");
            var catOtherSelectField = document.getElementById("catmodadd[" + i + "]");
            if (catOtherSelectField.value != 'other') {
                catOtherTextField.style.display = 'none';
            }
        }
        
        var groupAddTextField = document.getElementById("pce_groupmodadd");
        var groupAddSelectField = document.getElementById("groupmodadd");
        if (groupAddSelectField.value != 'other') {
            groupAddTextField.style.display = 'none';
        }
        
        var catAddTextField = document.getElementById("pce_catmodadd");
        var catAddSelectField = document.getElementById("catmodadd");
        if (catAddSelectField.value != 'other') {
            catAddTextField.style.display = 'none';
        }
        
        function addMod(modId) {
            var addModValue = document.getElementById(modId).value;
            var addModTextField = document.getElementById("pce_" + modId);
            if (addModValue == 'other') {
                // Show the text field
                addModTextField.style.display = '';
            }
            else {
                // Hide the text field
                addModTextField.style.display = 'none';
            }
        }
    </script>
<?php
}

function pce_addoptionsmenu() {
    add_options_page(__('Collaboration e-mails', 'peters_collaboration_emails'), __('Collaboration e-mails', 'peters_collaboration_emails'), 7, basename(__FILE__), 'pce_optionsmenu');
}

add_action('admin_menu','pce_addoptionsmenu',1);

// Add category table
function pce_upgrade_category_table() {
    global $wpdb, $pce_db_cats;
    // Add the table to hold category-specific moderators
    $sql = 'CREATE TABLE ' . $pce_db_cats . ' (
    catid int(4) NOT NULL,
    moderators longtext NOT NULL,
    UNIQUE KEY catid (catid)
    )';
    $wpdb->query($sql);
}

// Add and remove database tables when installing and uninstalling

function pce_install () {
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_cats, $pce_version;
    
    // Add the table to hold group information and moderator rules
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_group . '\'') != $pce_db_group) {
        $sql = 'CREATE TABLE ' . $pce_db_group . ' (
        collabgroup bigint(20) NOT NULL auto_increment,
        moderators longtext NOT NULL,
        groupname varchar(255) NOT NULL,
        KEY collabgroup (collabgroup)
        ) AUTO_INCREMENT=2;';

          $wpdb->query($sql);
    }
    
    // Insert the default moderator rule
    $sql = 'INSERT INTO ' . $pce_db_group . ' (collabgroup, moderators, groupname) VALUES 
    (1, \'a:1:{i:0;s:5:"admin";}\', \'Default\')';
    $wpdb->query($sql);

    // Add the table to hold group - collaborator associations
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_collab . '\'') != $pce_db_collab) {
        $sql = 'CREATE TABLE ' . $pce_db_collab . ' (
        groupid bigint(20) NOT NULL,
        writerid bigint(20) NOT NULL,
        UNIQUE KEY groupwriter (groupid, writerid)
        )';
          $wpdb->query($sql);
    }
    
    // Add the table to hold category-specific moderators
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_cats . '\'') != $pce_db_cats) {
        $sql = 'CREATE TABLE ' . $pce_db_cats . ' (
        catid int(4) NOT NULL,
        moderators longtext NOT NULL,
        UNIQUE KEY catid (catid)
        )';
          $wpdb->query($sql);
    }

    // Add the version number to the database
    add_option( 'pce_version', $pce_version, '', 'no' );
}

function pce_uninstall() {
    global $wpdb, $pce_db_group, $pce_db_collab, $pce_db_cats;

    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_group . '\'') == $pce_db_group) {
        $sql = 'DROP TABLE ' . $pce_db_group;
        $wpdb->query($sql);
    }

    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_collab . '\'') == $pce_db_collab) {
        $sql = 'DROP TABLE ' . $pce_db_collab;
        $wpdb->query($sql);
    }
    if($wpdb->get_var('SHOW TABLES LIKE \'' . $pce_db_cats . '\'') == $pce_db_cats) {
        $sql = 'DROP TABLE ' . $pce_db_cats;
        $wpdb->query($sql);
    }
    delete_option( 'pce_version' );
}

register_activation_hook( __FILE__, 'pce_install' );
register_uninstall_hook( __FILE__, 'pce_uninstall' );

} // This closes that initial check to make sure someone is actually logged in
?>