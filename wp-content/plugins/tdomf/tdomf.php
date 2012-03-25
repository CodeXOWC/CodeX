<?php
/*
Plugin Name: TDO Mini Forms
Plugin URI: http://thedeadone.net/download/tdo-mini-forms-wordpress-plugin/
Description: This plugin allows you to add custom posting forms to your website that allows your readers (including non-registered) to submit posts.
Version: 0.13.9
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

/*  Copyright 2006-2007  Mark Cunningham  (email : tdomf@thedeadone.net)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* - Permalink Widget also modifies comment link!
   - Move common form options into two sets of arrays (code/speed improvement)
   - Make Wordpress 2.9/latest only 
   - Option to add a comment with what changed */

////////////////////////////////////////////////////////////////////////////////
// Version History
//
// See readme.txt
//
// v0.13.9
// - Updated jQuery calls in tdomf-edit-form.php for WP3.0 needed some more
//   tweaking
//
////////////////////////////////////////////////////////////////////////////////

/*
////////////////////////////////////////////////////////////////////////////////
Work Queue:

Bugs
====
   - 'gmt_offset' not calcualted in %%SUBMISSIONDATE%%
   - not working with vbbridge - http://thedeadone.net/forum/?p=2805#comment-4296
   - image capatcha must update after failed "submit"
   - Possible incompatibility with Google XML Sitemaps plugin - http://thedeadone.net/forum/?p=1923
   - Bug in GeoMashup - http://thedeadone.net/forum/?p=2062#comment-4070
   - Bug TwitterTools not working with TDOMF publish - http://thedeadone.net/forum/?p=1916#comment-3972
   - Sanatized file upload filenames/paths - http://thedeadone.net/blog/v0127-of-tdo-mini-forms-just-released/comment-page-1/#comment-181601
   - diacritc marks reset form hacker: http://thedeadone.net/forum/?p=1888#topic-1888
   - Bug: Illegal characters in file names: http://thedeadone.net/forum/?p=1544#topic-1544
   - Bug: Weird "From" email for post moderation emails: http://thedeadone.net/forum/?p=71
   - Bug: No image preview when using attachment options: http://thedeadone.net/forum/?p=1498#comment-3485
   - Bug: slashes: http://thedeadone.net/forum/?p=1444#comment-3331 (but it might be related to AJAX!)
   - Bug: Widget configuration panels not showing up: http://thedeadone.net/forum/?p=1379
   - Investigate: no sidebars in widget configuration in IE.7
   - Investigate: tinymce conflict with AJAX form
   - Investigate: upload-link error:
       http://wordpress.org/support/topic/186919#post-838957

TODO
====
   - Allow options to format the "submitted by" text and also disabling for registered users
   - Quicktag like options for form hacker
   - Investigate: Copy widgets to other forms
   - Allow Error, StyleSheet and Validation Form Hacking
   - Allow only the Form or the Preview to be "hacked" in the Form Hacker
   - Add Error Warning for custom field widgets when non-unique keys used
   - Investigate: Auto bring up the conf panel for widgets on the "fix" links

Feature Requests
================
   - delete spam straight away (TDOMF_OPTION_SPAM_AUTO_DELETE_NOW already setup, just not implemented)
   - parent category - http://thedeadone.net/forum/?p=3205#comment-4524
   - display less information and perhaps some sort of funky JS to order columns - http://thedeadone.net/forum/?p=3526#comment-4514
   - category names instead of ids - http://thedeadone.net/forum/?p=3171#topic-3171
   - widget class have functions to allow message customisation
   - "delete all spam"
   - "recheck for spam"
   - Most pages private... - http://thedeadone.net/forum/?p=224#comment-4221
   - Requred option in category field
   - Related posts! http://thedeadone.net/forum/?p=1904#comment-3937
   - Entry in FAQ: Widget Changes not showing up
   - Investigate: Integeration with NextGen
   - Template: List of submitters
   - Code: Tags as checkboxes - http://thedeadone.net/forum/?p=1377
   - Some simple javascript to track number of chars/words typed so far in textarea: http://thedeadone.net/forum/?p=1321
   - Study: http://wordpress.org/extend/plugins/download-monitor/ integration

Other
=====
   - Code to add a email hook on publish - http://thedeadone.net/forum/?p=3195#comment-4519
   - http://thedeadone.net/forum/?p=2100#comment-4278 (alternative input?)   


*/

/*
Magic Quotes for 0.13.5 (pre cf editing):

Wordpress 2.8.2
+++++++++++++++

Magic Quotes: ON
================
Widget Screen    : OK
Form Hacker      : OK

[AJAX: On]         Title | Content | Excerpt | CF: TextArea | CF: TextField
--------------------------------------------------------------------------          
New-Post-Preview : OK    | OK      | OK      | OK           | OK
New-Post-Submit  : OK    | OK      | OK      | OK           | OK
Edit-Post-Preview: OK    | OK      | N/A     | N/A          | N/A
Edit-Post-Submit : OK    | OK      | N/A     | N/A          | N/A

[AJAX: Off]        Title | Content | Excerpt | CF: TextArea | CF: TextField
--------------------------------------------------------------------------
New-Post-Preview : OK    | OK      | OK      | OK           | OK
New-Post-Submit  : OK    | OK      | OK      | OK           | OK
Edit-Post-Preview: OK    | OK      | N/A     | N/A          | N/A
Edit-Post-Submit : OK    | OK      | N/A     | N/A          | N/A

Magic Quotes: OFF
=================
Widget Screen    : OK
Form Hacker      : OK

[AJAX: On]         Title | Content | Excerpt | CF: TextArea | CF: TextField
--------------------------------------------------------------------------          
New-Post-Preview : OK    | OK      | OK      | OK           | OK
New-Post-Submit  : OK    | OK      | OK      | OK           | OK
Edit-Post-Preview: OK    | OK      | N/A     | N/A          | N/A
Edit-Post-Submit : OK    | OK      | N/A     | N/A          | N/A

[AJAX: Off]        Title | Content | Excerpt | CF: TextArea | CF: TextField
--------------------------------------------------------------------------
New-Post-Preview : OK    | OK      | OK      | OK           | OK
New-Post-Submit  : OK    | OK      | OK      | OK           | OK
Edit-Post-Preview: OK    | OK      | N/A     | N/A          | N/A
Edit-Post-Submit : OK    | OK      | N/A     | N/A          | N/A

*/

/*
////////////////////////////////////////////////////////////////////////////////
Notes:
 - Potential nice hack: query.php @ line 1479
   $this->posts = apply_filters('the_posts', $this->posts);
     1. is single/page [should include category and index?]
     2. what post?
     3. is it tdomf, draft/unmoderation, not spam, viewer is submitter
     4. add to array
     This will allow users see their submitted posts, however comments should
     be disabled as they cannot be used in preview mode
 - What Clickable links in your posts? http://thedeadone.net/forum/?p=500#comment-1598
*/

 /*
////////////////////////////////////////////////////////////////////////////////
TODO for future versions

Known Bugs
- Upload Link error
- TinyMCE integration problem with AJAX

New Features
- Option to redirect all comment notifications on submitted posts to the admin (globally or per form)
- Allow moderators append a message to the approved/rejected notification (allows communication between submitter and moderator)
- Widget Manager Menu
  * Info about loaded widgets
  * Disable loading of specific widgets
- Style Sheet Editor
  * Preview
  * Select from pre-configured Styles
  * Submit new style to include in TDOMF
- Email verification of non-registered users
- Manage Downloads page
- Authors of posts should be able to see "previews" of post
- Get input during validation of form (for capatchas)
- Option to use "Post ready for review" instead of draft for unapproved submitted posts
- Turn Forms into multiple steps
- Shortcode Support

New Form Options
- Force Preview before submission
- Hide Form on Preview
- Forms can be used to submit links, emails, etc.
- Select Form Style/include Custom CSS

New Widgets
- Widget to allow user to enter a date to post the submission (as in future post)
- Widget to allow submitter to copy the submission to another email
- Widget that inputs only title
  * or that allows formatting of title by
  * user-input
  * custom field
  * username/submitter
  * date submitted (requires date/time format)
  * PHP code
- Login/Register/Who Am I Widget

Existing Widget Improvements
- Any widget with a size or length field should be customisable.
- Textfield Class (support numeric, date, email, webpage, etc.)
- Textarea Class
- Copy Widget to another Form
- Individual save
- Upload Files
  * Multiple Instances
  * Thumbnail size
  * Limit size of image by w/h
  * Image cropping
  * Title field for file links/attachment pages
  * Nicer integration: background uploading using iframe
  * Prevent submission until files uploaded
  * Progress bar
- Content
  * TinyMCE Integration
  * Allow users to define their own quicktags
  * Mechanism to allow sumitter to select where the link/image for upload should go
  * Default Value
- Custom Field: Textarea
  * TinyMCE Integration
  * Allow users to define their own quicktags
  * Mechanism to allow sumitter to select where the link/image for upload should go
  * Default Value
- Custom Field
  * Radio Groups
  * Multiple Checkboxes (grid-layout)
- Custom Field: Textfield
  * Numeric
  * Date
- Custom Field: Select
  * Required support
- Tags
  * Select from existing tag list or tag cloud
  * Select from only a specified set of tags
- 1 Question Captcha
  * Random questions for Captcha
- Category
  * Co-operate with "Set Category from GET variables" Widget
- Notify Me
  * Option to always notify submitter
- Image Captcha
  * Do not reload image on every preview (would be resolved by a seperate validation step)
- Text
  * Option to not use the form formatting (i.e. no "<fieldset>" tags before and after)
  * Option to have the text popup (would require a HTML space for the link)
- Set Category from GET variables
  * Add options (or at least information) for this widget
  * Co-operate with "Categories" Widget
- Who Am I
  * Integration with WP-OpenID?
  * Allow login as part of form submit
- Categories
  * Categories displayed but unselectable

Template Tags
- Log
- Moderation Queue
- Approved Posts
- File Info
- Country codes on submitter's IP
- Image Tags

Misc
- Documentation on creating your own widgets

////////////////////////////////////////////////////////////////////////////////
*/

///////////////////////////////////////////////////
// Defines and other global vars for this Plugin //
///////////////////////////////////////////////////

// Older versions of PHP may not define DIRECTORY_SEPARATOR so define it here,
// just in case.
if(!defined('DIRECTORY_SEPARATOR')) {
  define('DIRECTORY_SEPARATOR','/');
}

// Build Number (must be a integer)
define("TDOMF_BUILD", "56");
// Version Number (can be text)
define("TDOMF_VERSION", "0.13.9");

///////////////////////////////////////
// 0.1 to 0.5 Settings (no longer used)
//
define("TDOMF_ACCESS_LEVEL", "tdomf_access_level");
define("TDOMF_NOTIFY_LEVEL", "tdomf_notify_level");

///////////////////////////////////////
// 0.6 Settings (no longer used)
//
define('TDOMF_NOTIFY','tdomf_notify');
define("TDOMF_ITEMS_PER_PAGE", 30);
define("TDOMF_POSTS_INDEX",    0);
define("TDOMF_USERS_INDEX",    1);
define("TDOMF_IPS_INDEX",      2);
define("TDOMF_OPTIONS_INDEX",  3);

/////////////////
// 0.6 Settings
//
define("TDOMF_ACCESS_ROLES", "tdomf_access_roles");
define("TDOMF_NOTIFY_ROLES", "tdomf_notify_roles");
define("TDOMF_DEFAULT_CATEGORY", "tdomf_default_category");
define("TDOMF_DEFAULT_AUTHOR", "tdomf_default_author");
define("TDOMF_AUTO_FIX_AUTHOR", "tdomf_auto_fix_author");
define("TDOMF_BANNED_IPS", "tdomf_banned_ips");
//
// These keys are used to store info about a post, on the post.
// Keys with underscore prefix (i.e. "_") are hidden from the general user. Keys
// without can be modified and displayed using Wordpress normal features such as
// template tags and custom fields editor.
//
define("TDOMF_KEY_FLAG","_tdomf_flag");
define("TDOMF_KEY_NAME","Author Name");
define("TDOMF_KEY_EMAIL","Author Email");
define("TDOMF_KEY_WEB","Author Webpage");
define("TDOMF_KEY_IP","_tdomf_original_poster_ip");
define("TDOMF_KEY_USER_ID","_tdomf_original_poster_id");
define("TDOMF_KEY_USER_NAME","Original Submitter Username");
//
// This key is very important. It determines if a user is trusted or banned..
//
define("TDOMF_KEY_STATUS","_tdomf_status");


/////////////////
// 0.7 Settings
//
define('TDOMF_FOLDER', dirname(plugin_basename(__FILE__)));
define('TDOMF_FULLPATH', WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER.DIRECTORY_SEPARATOR);
#define('TDOMF_FULLPATH', ABSPATH.PLUGINDIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER.DIRECTORY_SEPARATOR);
define('TDOMF_URLPATH', get_option('siteurl').'/wp-content/plugins/'.TDOMF_FOLDER.'/');
define('TDOMF_WIDGET_PATH',TDOMF_FULLPATH.'widgets/');
define('TDOMF_VERSION_CURRENT', "tdomf_version_current");
define('TDOMF_LOG', "tdomf_log");
define('TDOMF_OPTION_MODERATION', "tdomf_enable_moderation");
define('TDOMF_OPTION_TRUST_COUNT', "tdomf_trust_count");
define('TDOMF_OPTION_ALLOW_EVERYONE', "tdomf_allow_everyone");
define('TDOMF_OPTION_AJAX', "tdomf_ajax");
define('TDOMF_OPTION_PREVIEW', "tdomf_preview");
define('TDOMF_OPTION_FROM_EMAIL', "tdomf_from_email");
define('TDOMF_OPTION_AUTHOR_THEME_HACK', "tdomf_author_submitter");
define('TDOMF_OPTION_FORM_ORDER', "tdomf_form_order");
define('TDOMF_OPTION_ADD_SUBMITTER', "tdomf_add_submitter_info");
define('TDOMF_CAPABILITY_CAN_SEE_FORM', "tdomf_can_see_form");
define('TDOMF_STAT_SUBMITTED', "tdomf_stat_submitted");
define('TDOMF_USER_STATUS_OK', "Normal");
define('TDOMF_USER_STATUS_BANNED', "Banned");
define('TDOMF_USER_STATUS_TRUSTED', "Trusted");
define('TDOMF_KEY_NOTIFY_EMAIL', "_tdomf_notify_email");

/////////////////
// 0.8 Settings
//
define('TDOMF_UPLOAD_PERMS',0777);
define('TDOMF_UPLOAD_TIMEOUT',(60 * 60)); // 1 hour
define('TDOMF_KEY_DOWNLOAD_COUNT',"_tdomf_download_count_");
define('TDOMF_KEY_DOWNLOAD_TYPE',"_tdomf_download_type_");
define('TDOMF_KEY_DOWNLOAD_PATH',"_tdomf_download_path_");
define('TDOMF_KEY_DOWNLOAD_NAME',"_tdomf_download_name_");
define('TDOMF_KEY_DOWNLOAD_CMD_OUTPUT',"_tdomf_download_cmd_output_");

/////////////////
// 0.9 Settings
//
define('TDOMF_KEY_DOWNLOAD_THUMB',"_tdomf_download_thumb_");
define('TDOMF_OPTION_DISABLE_ERROR_MESSAGES',"tdomf_disable_error_messages");
define('TDOMF_OPTION_EXTRA_LOG_MESSAGES',"tdomf_extra_log_messages");
define('TDOMF_OPTION_YOUR_SUBMISSIONS',"tdomf_your_submissions");
define('TDOMF_WIDGET_URLPATH',TDOMF_URLPATH.'widgets/');

////////////////
// 0.10 Settings
//

define('TDOMF_OPTION_NAME',"tdomf_form_name");
define('TDOMF_OPTION_DESCRIPTION',"tdomf_form_description");
define('TDOMF_OPTION_CREATEDPAGES',"tdomf_form_created_pages");
define('TDOMF_OPTION_INCLUDED_YOUR_SUBMISSIONS',"tdomf_form_inc_user_page");
define('TDOMF_OPTION_CREATEDUSERS',"tdomf_form_created_users");
define('TDOMF_OPTION_WIDGET_INSTANCES',"tdomf_form_widget_instances");
define('TDOMF_OPTION_SUBMIT_PAGE',"tdomf_form_submit_page");
define('TDOMF_KEY_FORM_ID',"_tdomf_form_id");

// DB Table Names
//
define("TDOMF_DB_TABLE_FORMS", "tdomf_table_forms");
define("TDOMF_DB_TABLE_WIDGETS", "tdomf_table_widgets");

//////////////////
// 0.10.2 Settings

// Set to true if you want to hide the register_global errors. Do this only if
// you know what you are doing!
define("TDOMF_HIDE_REGISTER_GLOBAL_ERROR", false);

define('TDOMF_OPTION_WIDGET_MAX_WIDTH',"tdomf_form_widget_max_width");
define('TDOMF_OPTION_WIDGET_MAX_HEIGHT',"tdomf_form_widget_max_height");

//////////////////
// 0.10.3 Settings

define('TDOMF_OPTION_VERIFICATION_METHOD',"tdomf_verify");
define('TDOMF_OPTION_FORM_DATA_METHOD',"tdomf_form_data");
define('TDOMF_DB_TABLE_SESSIONS',"tdomf_table_sessions");

//////////////////////
// 0.11 Settings

define('TDOMF_OPTION_SPAM',"tdomf_spam_protection");
define('TDOMF_OPTION_SPAM_AKISMET_KEY',"tdomf_akismet_key");
define('TDOMF_OPTION_SPAM_AKISMET_KEY_PREV',"tdomf_akismet_key_prev");
define('TDOMF_OPTION_SPAM_NOTIFY',"tdomf_spam_notify");
define('TDOMF_OPTION_SPAM_AUTO_DELETE',"tdomf_spam_auto_delete");
define('TDOMF_VERSION_LAST', "tdomf_version_last");
define('TDOMF_KEY_SPAM',"_tdomf_spam_flag");
define('TDOMF_KEY_USER_AGENT',"_tdomf_user_agent");
define('TDOMF_KEY_REFERRER',"_tdomf_referrer");
define('TDOMF_STAT_SPAM', "tdomf_stat_spam");

define('TDOMF_OPTION_QUEUE_PERIOD', "tdomf_queue_period");
define('TDOMF_OPTION_THROTTLE_RULES', "tdomf_throttle_rules");

///////
// 0.12

define('TDOMF_OPTION_REDIRECT',"tdomf_redirect");

define('TDOMF_OPTION_MSG_SUB_PUBLISH',"tdomf_msg_sub_pub");
define('TDOMF_OPTION_MSG_SUB_FUTURE',"tdomf_msg_sub_fut");
define('TDOMF_OPTION_MSG_SUB_SPAM',"tdomf_msg_sub_spam");
define('TDOMF_OPTION_MSG_SUB_MOD',"tdomf_msg_sub_mod");
define('TDOMF_OPTION_MSG_SUB_ERROR',"tdomf_msg_sub_err");
define('TDOMF_OPTION_MSG_PERM_BANNED_USER',"tdomf_msg_perm_ban_user");
define('TDOMF_OPTION_MSG_PERM_BANNED_IP',"tdomf_msg_perm_ban_ip");
define('TDOMF_OPTION_MSG_PERM_THROTTLE',"tdomf_msg_perm_throttle");
define('TDOMF_OPTION_MSG_PERM_INVALID_USER',"tdomf_msg_perm_invaild_user");
define('TDOMF_OPTION_MSG_PERM_INVALID_NOUSER',"tdomf_msg_perm_invaild_nouser");

define("TDOMF_MACRO_SUBMISSIONERRORS", "%%SUBMISSIONERRORS%%");
define("TDOMF_MACRO_SUBMISSIONURL", "%%SUBMISSIONURL%%");
define("TDOMF_MACRO_SUBMISSIONDATE", "%%SUBMISSIONDATE%%");
define("TDOMF_MACRO_SUBMISSIONTIME", "%%SUBMISSIONTIME%%");
define("TDOMF_MACRO_SUBMISSIONTITLE", "%%SUBMISSIONTITLE%%");
define("TDOMF_MACRO_USERNAME", "%%USERNAME%%");
define("TDOMF_MACRO_IP", "%%IP%%");
define("TDOMF_MACRO_FORMKEY", "%%FORMKEY%%");
define("TDOMF_MACRO_FORMURL", "%%FORMURL%%");
define("TDOMF_MACRO_FORMID", "%%FORMID%%");
define("TDOMF_MACRO_FORMNAME", "%%FORMNAME%%");
define("TDOMF_MACRO_FORMDESCRIPTION", "%%FORMDESCRIPTION%%");
define("TDOMF_MACRO_FORMMESSAGE", "%%FORMMESSAGE%%");
define("TDOMF_MACRO_WIDGET_START", "%%WIDGET:");
define("TDOMF_MACRO_END", "%%");

define('TDOMF_OPTION_FORM_HACK',"tdomf_form_hack");
define('TDOMF_OPTION_FORM_HACK_ORIGINAL',"tdomf_form_hack_org");
define('TDOMF_OPTION_FORM_PREVIEW_HACK',"tdomf_form_preview_hack");
define('TDOMF_OPTION_FORM_PREVIEW_HACK_ORIGINAL',"tdomf_form_hack_preview_org");

define('TDOMF_OPTION_LOG_MAX_SIZE',"tdomf_option_log_max_size");

//////////
// 0.12.2

define('TDOMF_OPTION_MOD_EMAIL_ON_PUB',"tdomf_option_mod_email_on_pub");

//////////
// 0.12.5

define('TDOMF_KEY_DOWNLOAD_THUMBURI',"_tdomf_download_thumburi_");
define('TDOMF_OPTION_ADMIN_EMAILS', "tdomf_admin_emails");

/////////
// 0.12.6

define('TDOMF_OPTION_ALLOW_CAPS',"tdomf_allow_caps");
define('TDOMF_OPTION_ALLOW_USERS',"tdomf_allow_users");
define('TDOMF_OPTION_ALLOW_PUBLISH',"tdomf_allow_publish");
define('TDOMF_OPTION_PUBLISH_NO_MOD',"tdomf_option_publish_no_mod");
define('TDOMF_MAX_USERS_TO_DISPLAY',60);

////////
// 0.13

/*
 * @todo
 *
 * Option to disable Unapproved Edit Locking (require temp locks?)
 * Template Tags for editing (what is actually required?)
 * Moderation screen: implement filters: email, page/post, un/locked
 * Moderation screen: search
 * Moderation screen: bulk buttons at top of list too
 * 'Back' Button for Ajax inline editing
 * 'Reset' Button for Editing Forms
 * "Draft" Editing support
 * "Edited by XYZ": message in form hacker, last X edits (appear above "Submitted by")
 *    (and also add "Submitted by XYZ" to messages for individual forms)
 */

define('TDOMF_OPTION_FORM_EDIT',"tdomf_form_edit");
define('TDOMF_OPTION_ALLOW_AUTHOR',"tdomf_allow_author");
define('TDOMF_OPTION_ALLOW_TIME',"tdomf_allow_time");
define('TDOMF_OPTION_EDIT_RESTRICT_TDOMF',"tdomf_restrict_tdomf");
define('TDOMF_OPTION_EDIT_RESTRICT_CATS',"tdomf_restrict_cats");
define('TDOMF_OPTION_ADD_EDIT_LINK',"tdomf_add_edit_link");
define('TDOMF_OPTION_ADD_EDIT_LINK_TEXT',"tdomf_add_edit_link_text");
define('TDOMF_OPTION_AUTO_EDIT_LINK',"tdomf_auto_edit_link");
define('TDOMF_STAT_EDITED', "tdomf_stat_edited");
define('TDOMF_MACRO_POSTID', "%%POSTID%%");
define('TDOMF_DB_TABLE_EDITS', "tdomf_table_edits");
define('TDOMF_OPTION_AJAX_EDIT',"tdomf_ajax_edit");
define('TDOMF_OPTION_EDIT_PAGE_FORM',"tdomf_edit_page_form");
define('TDOMF_KEY_LOCK',"_tdomf_lock_editing");
define('TDOMF_OPTION_MSG_INVALID_POST',"tdomf_msg_invalid_post");
define('TDOMF_OPTION_MSG_INVALID_FORM',"tdomf_msg_invalid_form");
define('TDOMF_OPTION_MSG_SPAM_EDIT_ON_POST',"tdomf_msg_spam_edit_on_post");
define('TDOMF_OPTION_MSG_UNAPPROVED_EDIT_ON_POST',"tdomf_msg_unapproved_edit_on_post");
define('TDOMF_OPTION_MSG_LOCKED_POST',"tdomf_msg_locked_post");
define('TDOMF_KEY_SUBMISSION_DATE', "_tdomf_submission_date");
define('TDOMF_KEY_SUBMISSION_DATE_GMT', "_tdomf_submission_date_gmt");

//////////
// 0.13.2

define('TDOMF_OPTION_QUEUE_ON_ALL',"tdomf_queue_on_all");
define('TDOMF_OPTION_MOD_SHOW_LINKS',"tdomf_mod_show_links");
define('TDOMF_OPTION_SPAM_AUTO_DELETE_NOW', "tdomf_spam_auto_delete_now");
define('TDOMF_OPTION_NOSPAM_USER', "tdomf_nospam_user");
define('TDOMF_OPTION_NOSPAM_AUTHOR', "tdomf_nospam_author");
define('TDOMF_OPTION_NOSPAM_TRUSTED', "tdomf_nospam_trusted");
define('TDOMF_OPTION_NOSPAM_PUBLISH', "tdomf_nospam_publish");
define('TDOMF_OPTION_SPAM_OVERWRITE', "tdomf_spam_overwrite");
define('TDOMF_DEBUG_AKISMET_FAKE_SPAM', false); // set to true to get Akismet to flag everything as spam
define('TDOMF_DEBUG_FAKE_SPAM', false); // set to true to ignore akismet and treat everything as spam
define('TDOMF_KEY_FIELDS', "_tdomf_fields");
define('TDOMF_KEY_CUSTOM_FIELDS', "_tdomf_custom_fields");

//////////
// 0.13.6

define('TDOMF_KEY_UPLOADED_FILES','_tdomf_uploaded_files');

//////////////////////////////////////////////////
// loading text domain for language translation
//
load_plugin_textdomain('tdomf',PLUGINDIR.DIRECTORY_SEPARATOR.TDOMF_FOLDER);

/////////////////////////////////////////
// Loading "hacks" here before pluggable
//
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-hacks.php');

//////////////////////////////////////////////////////////////////////////
// A potential fix for WordpressMU (WordpressMU is officially unsupported)
//
require_once(ABSPATH . 'wp-includes/pluggable.php');

// Is this a Wordpress < 2.5 install?
//
function tdomf_wp23() {
  global $wp_db_version;
  #if($wp_db_verison <= 6124)
  #  return true;
  return !tdomf_wp25();
}

// Is this a Wordpress >= 2.5 install?
//
function tdomf_wp25() {
  global $wp_db_version;
  if($wp_db_version >= 7558) {
    return true;
  }
  return false;
}

// Is this a Wordpress >= 2.7
//
function tdomf_wp27() {
  global $wp_version;
  return version_compare($wp_version,"2.7-beta3",">=");
}


// Is this a Wordpress >= 3.0
//
function tdomf_wp30() {
  global $wp_version;
  return version_compare($wp_version,"3.0",">=");
}
///////////////////////////////////
// Configure Backend Admin Menus //
///////////////////////////////////

add_action('admin_menu', 'tdomf_add_menus');
function tdomf_add_menus()
{
    $unmod_count  = tdomf_get_unmoderated_posts_count();
    $unmod_count += tdomf_get_edits(array('state' => 'unapproved', 'count' => true, 'unique_post_ids' => true));

    /*if(tdomf_wp25() && $unmod_count > 0) {
        add_menu_page(__('TDO Mini Forms', 'tdomf'), sprintf(__("TDO Mini Forms <span id='awaiting-mod' class='count-%d'><span class='comment-count'>%d</span></span>", 'tdomf'), $unmod_count, $unmod_count), 'edit_others_posts', TDOMF_FOLDER, 'tdomf_overview_menu');
    } else {*/
        add_menu_page(__('TDO Mini Forms', 'tdomf'), __('TDO Mini Forms', 'tdomf'), 'edit_others_posts', TDOMF_FOLDER, 'tdomf_overview_menu');
    /*}*/

    // Options
    add_submenu_page( TDOMF_FOLDER , __('Plugin Options', 'tdomf'), __('Plugin Options', 'tdomf'), 'manage_options', 'tdomf_show_options_menu', 'tdomf_show_options_menu');
    //
    // Form Options
    add_submenu_page( TDOMF_FOLDER , __('Form Options', 'tdomf'), __('Form Options', 'tdomf'), 'manage_options', 'tdomf_show_form_options_menu', 'tdomf_show_form_options_menu');
    //
    // Form Widgets
    add_submenu_page( TDOMF_FOLDER , __('Form Creator', 'tdomf'), __('Form Creator', 'tdomf'), 'manage_options', 'tdomf_show_form_menu', 'tdomf_show_form_menu');
    //
    // Form Hacker
    add_submenu_page( TDOMF_FOLDER , __('Form Hacker', 'tdomf'), __('Form Hacker', 'tdomf'), 'manage_options', 'tdomf_show_form_hacker', 'tdomf_show_form_hacker');
    //
    // Form Export
    add_submenu_page( TDOMF_FOLDER , __('Form Export', 'tdomf'), __('Form Export', 'tdomf'), 'manage_options', 'tdomf_show_form_export_menu', 'tdomf_show_form_export_menu');
    /*//
    // Form Options
    add_submenu_page( TDOMF_FOLDER , __('Form Options', 'tdomf'), __('Forms', 'tdomf'), 'manage_options', 'tdomf_show_form_options_menu', 'tdomf_show_form_options_menu');
    //
    // Form Widgets
    add_submenu_page( 'admin.php' , __('Form Creator', 'tdomf'), __('Form Creator', 'tdomf'), 'manage_options', 'tdomf_show_form_menu', 'tdomf_show_form_menu');
    //
    // Form Hacker
    add_submenu_page( 'admin.php' , __('Form Hacker', 'tdomf'), __('Form Hacker', 'tdomf'), 'manage_options', 'tdomf_show_form_hacker', 'tdomf_show_form_hacker');
    //
    // Form Export
    add_submenu_page( 'admin.php' , __('Form Export', 'tdomf'), __('Form Export', 'tdomf'), 'manage_options', 'tdomf_show_form_export_menu', 'tdomf_show_form_export_menu');*/
    //
    // Moderation Queue
    if(tdomf_is_moderation_in_use()) {
      add_submenu_page( TDOMF_FOLDER , __('Moderation', 'tdomf'), sprintf(__('Moderation (%d)', 'tdomf'), $unmod_count), 'edit_others_posts', 'tdomf_show_mod_posts_menu', 'tdomf_show_mod_posts_menu');
    }
    else {
      add_submenu_page( TDOMF_FOLDER , __('Moderation', 'tdomf'), __('Moderation', 'tdomf'), 'edit_others_posts', 'tdomf_show_mod_posts_menu', 'tdomf_show_mod_posts_menu');
    }
    //
    // Manage Submitters
    add_submenu_page( TDOMF_FOLDER , __('Users and IPs', 'tdomf'), __('Users and IPs', 'tdomf'), 'edit_others_posts', 'tdomf_show_manage_menu', 'tdomf_show_manage_menu');
    //
    // Log
    add_submenu_page( TDOMF_FOLDER , __('Log', 'tdomf'), __('Log', 'tdomf'), 'manage_options', 'tdomf_show_log_menu', 'tdomf_show_log_menu');
    //
    // Uninstall
    add_submenu_page( TDOMF_FOLDER , __('Uninstall', 'tdomf'), __('Uninstall', 'tdomf'), 'manage_options', 'tdomf_show_uninstall_menu', 'tdomf_show_uninstall_menu');

    //
    // Your submissions
    if(get_option(TDOMF_OPTION_YOUR_SUBMISSIONS)) {
      add_submenu_page('profile.php', 'Your Submissions', 'Your Submissions', 0, 'tdomf_your_submissions', 'tdomf_show_your_submissions_menu');
    }
    
    // Restoring old behaviour that Wordpress 2.8 took away for this page
    //
    add_submenu_page( 'admin.php', __('Revisions','tdomf'), __('Revisions','tdomf'), 'manage_options' ,TDOMF_FOLDER . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tdomf-revision.php' );
}

//////////////////////////////////
// Load the rest of the plugin! //
//////////////////////////////////

// These files are required for basic functions
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-log-functions.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-widget-functions.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-template-functions.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-spam.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-form.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-notify.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-widget-classes.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-upload-functions.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-theme-widgets.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-db.php');
require_once(TDOMF_FULLPATH.'include'.DIRECTORY_SEPARATOR.'tdomf-msgs.php');

// Only need this pages if you're modifying the plugin
/*if(is_admin()) {*/
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-overview.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-edit-post-panel.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-form-options.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-form-export.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-options.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-edit-form.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-log.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-moderation.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-uninstall.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-form-hacker.php');
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-export.php');
/*}*/
// This file contains some admin test like functions for user/ip trust/ban checks
// @todo Move utility functions from here into a non-admin file
require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-manage.php');

// The "Your Submissions" can be user by normal users
/*if(is_user_logged_in()) {*/
    require_once(TDOMF_FULLPATH.'admin'.DIRECTORY_SEPARATOR.'tdomf-your-submissions.php');
/*}*/

/////////////////////////
// What's new since... //
/////////////////////////

function tdomf_new_features() {
  $last_version = get_option(TDOMF_VERSION_LAST);
  $features = "";

  if($last_version == false) { return false; }

  // 29 = 0.10.4
  if($last_version <= 29) {

    $link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu#spam";
    $features .= "<li>".sprintf(__('<a href="%s">Integration with Akismet for SPAM protection</a>','tdomf'),$link)."</li>";

    $link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu&form=".tdomf_get_first_form_id()."#queue";
    $features .= "<li>".sprintf(__('<a href="%s">Automatically schedule approved posts!</a>','tdomf'),$link)."</li>";

    $link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu&form=".tdomf_get_first_form_id()."#throttle";
    $features .= "<li>".sprintf(__('<a href="%s">Add submission throttling rules to your form!</a>','tdomf'),$link)."</li>";

    if(current_user_can('manage_options')) {
        $link = "admin.php?page=".TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."tdomf-info.php&text";
        $link2 = "admin.php?page=".TDOMF_FOLDER.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."tdomf-info.php&html";
        $features .= "<li>".sprintf(__('View tdomfinfo() in <a href="%s">text</a> and <a href="%s">html-code</a>. Useful for copying and pasting!',"tdomf"),$link,$link2)."</li>";
    }

    $link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu&form=".tdomf_get_first_form_id()."#import";
    $features .= "<li>".sprintf(__('<a href="%s">Import and export individual form settings</a>','tdomf'),$link)."</li>";

    $link = get_bloginfo('wpurl')."/wp-admin/widgets.php";
    $features .= "<li>".sprintf(__('New widget for your theme: <a href="%s">Top Submitters</a>','tdomf'),$link)."</li>";
  }
  // 30 = 0.11b
  // 31 = 0.11
  // 32 = 0.11.1 (bug fixes)
  if($last_version <= 32) {

      $link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_options_menu&form=".tdomf_get_first_form_id()."#ajax";
      $features .= "<li>".sprintf(__('<a href="%s">AJAX support for forms!</a>','tdomf'),$link)."</li>";

      $link = get_bloginfo('wpurl')."/wp-admin/admin.php?page=tdomf_show_form_hacker";
      $features .= "<li>".sprintf(__('<a href="%s">Hack the appearance of your form!</a>','tdomf'),$link)."</li>";

      $link = get_bloginfo('wpurl')."/wp-admin/admin.phppage=tdomf_show_form_menu&form=".tdomf_get_first_form_id();
      $features .= "<li>".sprintf(__('<a href="%s">Text widget updated to support macros and php code</a>','tdomf'),$link)."</li>";

      $link = get_bloginfo('wpurl')."/wp-admin/admin.phppage=tdomf_show_form_menu&form=".tdomf_get_first_form_id();
      $features .= "<li>".sprintf(__('<a href="%s">Categories widget can now be displayed as checkboxes or radio buttons</a>','tdomf'),$link)."</li>";

      $link = get_bloginfo('wpurl')."/wp-admin/admin.phppage=tdomf_show_form_menu&form=".tdomf_get_first_form_id();
      $features .= "<li>".sprintf(__('<a href="%s">New Widget: Append. Allows you add text to a submitted post. It can even run PHP code.</a>','tdomf'),$link)."</li>";

      $features .= "<li>".__('New Template Tags: tdomf_get_the_submitter_email and tdomf_the_submitter_email','tdomf')."</li>";
  }
  // 33 = 0.12b
  // 34 = 0.12
  // 40 = 0.12.3
  if($last_version < 40) {
      $features .= "<li>".__("Auto Respond Email widget for your form","tdomf")."</li>";
      $features .= "<li>".__("Ban User and IP links directly from the moderation email","tdomf")."</li>";
      $features .= "<li>".__("The moderation emails that are sent for admins can now be left turned on even if moderation is turned off","tdomf")."</li>";
  }
  // 41 = 0.12.4
  if($last_version < 41) {
      $features .= "<li>".__("Improved Error Reporting","tdomf")."</li>";
      $features .= "<li>".__("Form toolbar on all form pages (Widgets and Form Hacker)","tdomf")."</li>";
      $features .= "<li>".__("Include option implemented for Categories widget","tdomf")."</li>";
      $features .= "<li>".__("Import/Export restored and improved","tdomf")."</li>";
      $features .= "<li>".__("Default Generated Forms are now W3C compliant","tdomf")."</li>";
      $features .= "<li>".__("Comment Notification from Submitted Posts no longer go to the Submitter unless they can modify the comments","tdomf")."</li>";
  }
  // 43 = 0.12.5
  if($last_version < 43) {
      $features .= "<li>".__("Excerpt Widget","tdomf")."</li>";
      $features .= "<li>".__("Comments Management Widget","tdomf")."</li>";
      $features .= "<li>".__("Include option implemented for Categories widget (fixed)","tdomf")."</li>";
      $features .= "<li>".__("Order by and Sort options for Categories widget","tdomf")."</li>";
      $features .= "<li>".__("Integration with Subscribe to Comments plugin via widget","tdomf")."</li>";
      $features .= "<li>".__("Publish now button","tdomf")."</li>";
      $features .= "<li>".__("Specify Email Addresses instead of Roles to recieve Moderation notifications","tdomf")."</li>";
      $features .= "<li>".__('Default tags, required and enable/disable user adding tags options for Tags widget','tdomf').'</li>';
  }
  // 44 = pre-0.13 release
  if($last_version < 44) {
      $features .= "<li>".__("More powerful form access configuration. Now you can select by capability and user as well as role!","tdomf")."</li>";
      $features .= "<li>".__("2.7 Wordpress Compatibility","tdomf")."</li>";
      $features .= "<li>".__("Can now turn off auto-publishing of users with publishing rights","tdomf")."</li>";
      $features .= "<li>".__("New Permalink Widget","tdomf")."</li>";
      $features .= "<li>".__("New GeoMashup Integration Widget","tdomf")."</li>";
  }
  // 46 = 0.12.7
  if($last_version < 46) {
      $features .= "<li>".__("Auto Respond Email widget can now provide a link to flag posts using a Custom Field","tdomf")."</li>";
  }
  // 47 = 0.13
  if($last_version < 47) {
      $features .= "<li>".__("<b>You can now created forms to edit posts and pages!</b>","tdomf")."</li>";
  }
  // 48 = 0.13.1
  // 49 = 0.13.2
  if($last_version < 49) {
      $features .= "<li>".__("<b>More Spam Checking options added. Can now be configured per form!</b>","tdomf")."</li>";
  }
  // 50 = 0.13.3
  if($last_version < 50) {
      $features .= "<li>".__("<b>Filter by user, ip and form on moderations screen</b>","tdomf")."</li>";
      $features .= "<li>".__("<b>Enable syntax code highlighting on Form Hacker and messages</b>","tdomf")."</li>";
      $features .= "<li>".__("<b>Special revision page for comparing revisions created by TDOMF</b>","tdomf")."</li>";
  }
  // 51 = 0.13.4
  // 52 = 0.13.5
  // 53 = 0.13.6
  if($last_version < 53) {
      $features .= "<li>".__("<b>Custom Field widget for Edit Forms</b>","tdomf")."</li>";
      $features .= "<li>".__("<b>Restrict Custom Field Text Field to a number</b>","tdomf")."</li>";
      $features .= "<li>".__("<b>Execute PHP code in Custom Field Hidden Field</b>","tdomf")."</li>";
      $features .= "<li>".__("<b>Custom Fields modified by TDOMF Forms are stored in revision history</b>","tdomf")."</li>";      
      $features .= "<li>".__("<b>Allow multiple instances of the Upload Files widget</b>","tdomf")."</li>";
  }
  // 54 = 0.13.7
  // 55 = 0.13.8
  // 56 = 0.13.9
  
  if(!empty($features)) {
    return "<ul>".$features."</ul>";
  }

  return false;
}

/////////////////////////
// Start/Init/Upgrade //
////////////////////////

function tdomf_init(){

  // Update/upgrade options!
    
  // Pre 0.7 or a fresh install!
  if(get_option(TDOMF_VERSION_CURRENT) == false)
  {
    add_option(TDOMF_VERSION_CURRENT,TDOMF_BUILD);

    // Some defaults for new options!
    add_option(TDOMF_OPTION_MODERATION,true);
    add_option(TDOMF_OPTION_PREVIEW,true);
    add_option(TDOMF_OPTION_TRUST_COUNT,-1);
    add_option(TDOMF_OPTION_YOUR_SUBMISSIONS,true);
    add_option(TDOMF_OPTION_WIDGET_MAX_WIDTH,500);
    add_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT,400);
  }

  // Pre 0.9.3 (beta)/16
  if(intval(get_option(TDOMF_VERSION_CURRENT)) < 16) {
    add_option(TDOMF_OPTION_YOUR_SUBMISSIONS,true);
  }

  // Pre WP 2.5/0.10.2
  if(intval(get_option(TDOMF_VERSION_CURRENT)) < 26) {
    add_option(TDOMF_OPTION_WIDGET_MAX_WIDTH,500);
    add_option(TDOMF_OPTION_WIDGET_MAX_HEIGHT,400);
  }

  if(get_option(TDOMF_OPTION_VERIFICATION_METHOD) == false) {
    add_option(TDOMF_OPTION_VERIFICATION_METHOD,'wordpress_nonce');
  }

  if(get_option(TDOMF_OPTION_FORM_DATA_METHOD) == false) {
    if(ini_get('register_globals')) {
       add_option(TDOMF_OPTION_FORM_DATA_METHOD,'db');
    } else {
       add_option(TDOMF_OPTION_FORM_DATA_METHOD,'session');
    }
  }

  if(get_option(TDOMF_OPTION_LOG_MAX_SIZE) == false) {
      add_option(TDOMF_OPTION_LOG_MAX_SIZE,1000);
  }

  if(intval(get_option(TDOMF_VERSION_CURRENT)) < 44) {
      $form_ids = tdomf_get_form_ids();
      foreach($form_ids as $form_id) {
          tdomf_set_option_form(TDOMF_OPTION_ALLOW_PUBLISH,true,$form_id->form_id);
          tdomf_set_option_form(TDOMF_OPTION_PUBLISH_NO_MOD,true,$form_id->form_id);
      }
  }

  // Update build number
  if(get_option(TDOMF_VERSION_CURRENT) != TDOMF_BUILD) {
    update_option(TDOMF_VERSION_LAST,get_option(TDOMF_VERSION_CURRENT));
    update_option(TDOMF_VERSION_CURRENT,TDOMF_BUILD);
  }
}

// Tables should only be created by admin
if(is_admin()) {
    tdomf_db_create_tables();
}

// Must load widgets for everyone, otherwise forms will not work
tdomf_load_widgets();

?>
