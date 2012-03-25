=== Plugin Name ===
Contributors: the_dead_one
Donate link: http://tinyurl.com/yvgcs9
Tags: anonymous, posting, editing, users, post, form, admin, submit, submissions, unregistered users, uploads, downloads, categories, tags, custom fields, captcha, custom posting interface, plugin, custom, widget, akismet, ajax, recaptcha, subscribe-to-comments, geo-mashup
Requires at least: 2.8.6
Tested up to: 3.0
Stable Tag: 0.13.9

This plugin can add themed custom posting and editing forms to your website that allows your readers (including non-registered) to contribute.

== Description ==

This plugin allows you to add highly customisable forms that work with your Wordpress Theme to your website that allows non-registered users and/or subscribers (also configurable) to submit and edit posts and pages. New posts are kept in "draft" until an admin can publish them (also configurable). Likewise edits can be kept be automatically kept as revisions until an admin approves them. It can optionally use Akismet to check if submissions and contributions are spam. TDO Mini Forms can be used to create "outside-the-box" uses for Wordpress, from Contact Managers, Ad Managers, Collaborate Image Sites, Submit Links, etc.

**0.13.9 is another compatibility fix for Wordpress 3.0. If you are running a Wordpress version prior to 3.0, you do not need to upgrade.**

TDO Mini Forms has been used to turn Wordpress into a [Forum](http://thedeadone.net/software/tdo-forum-wordpress-theme/), [Contact Manager](http://www.slipfire.com/wp-crm-58.htm)!

The plugin provides an extensive moderation view so administrators and editors can see posts and edits awaiting approval and publish or delete them. Administrators can also ban specific users and IPs from using the form. Administrators can also "Trust" specific users. This means that when they use the form, their posts are automatically published. This does not give them any other rights or permissions using the Wordpress software, it only affects usage of the form. This applies to user and IP bans as well. There is even an option to automatically trust users after so many approved submissions and edits.

Administrators can configure the forms using drag and drop "widgets". They are based on the same model as Wordpress' built-in Theme widgets and it is possible to write your own.

Registered users have access to a "Your Submissions" page which lists their current submissions and edits awaiting approval and links to their approved submissions.

* [Demo Site]( http://thedeadone.net/forums )

= Features =

* Submission and Editing of Posts and Pages
* Integration with the GeoMashup plugin
* Integration with Subcribe-to-Comments 2.1 plugin
* Integration with Akismet and reCaptcha to prevent Spam
* Highly customisable: Create your forms using a Widget interface and then hack it to make it work how you like!
* Create as many forms as you like.
* Import and Export form configurations
* Put a form in your sidebar using a widget for your Theme or put the form in a page or post.
* Submit posts with alternative permalinks (great for link sites)
* Simple Question and/or Image Captcha.
* Add Custom Fields to your Forms.
* QuickTags support for Forms.
* Upload Files and can be attached to posts. Uses Wordpress' core to create thumbnails if applicable.
* Submitters can be notified if post approved or rejected.
* Allow users to select category and tags.
* Ban users and IPs.
* Control what roles can access the form.
* Can automatically create a page with form for you.
* Can automatically modified author template tag with info about submitter.
* Can, optionally, automatically allow submissions to be published.
* Throttle number of submissions by user and/or IP
* Optionally queue publishing of posts 
* Numerous widgets for your theme, including a list of the top submitters
* And many more...

== Installation ==

Download the zip and extract the files to a subdirectory, called tdo-mini-forms, of your plugin directory. i.e. `/path_to_wordpress/wp-content/plugins/tdo-mini-forms`. 

It is currently assumed that the "wp-load.php" of your Wordpress install can be accessed from the tdo-mini-forms folder using the relative path: `../../../wp-load.php`

Also make sure that the files in the root of tdo-mini-forms can be accessed from the web as they are used to enable post submission. 

Once you've got it installed, active the plugin via the usual Wordpress plugin menu. Make sure you then configure it via the main TDOMF menu in the Wordpress Administration backend.

You must assign a user as the "Default Author". This user must **not** have rights to publish or edit posts, i.e. they should be of the subscriber role. When posts are submitted from unregistered users, this "Default Author" user is set as the author of the post to keep Wordpress happy. The TDOMF options menu can automatically create a dummy user to set as the Default Author. This is the recommended approach.

On the options menu, there is a button to automatically create a page with the necessary tag to display your form. There are also other options to help integrate with your theme on this page. For more information on Theme integration, please refer to the Frequently Asked Questions of this readme.

= Upgrade Instructions from versions previous to 0.7 =

Before installing the new version of TDO Mini Forms, delete the TDOMiniForms from your `.../wp-content/plugins/` folder. Now simply follow the installation instructions above. You will need to re-configure the plugin again, however previously submitted posts and other user information will be retained from your previous installation of the plugin.

= Configuration =

Please check the Frequently Asked Questions for answers to many of the common issues that arise.

If you are using the wp-cache or wp-super-cache plugins, please make sure that any page that uses a TDO Mini Forms form are **not** cached. If you cache the form it'll may contain an old "key" and will cause "Bad Data" error messages and also you will not be able to preview or see admin messages.

If you are using any plugins that will execute PHP code within post content/title or custom field, it is recommended to disable them or at the very least make sure that moderation is enabled and all submissions are scanned for malicious code. PHP code and Javascript tags can be submitted as part of input to any part of the form, in some cases Wordpress will strip them out. But custom fields, especially, will not be filtered automatically (this can be desirable, for example if you want people to post snippets of javascript code). 

= Creating a Form =

To start, go to "Options" menu under the main "TDO Mini Forms" menu in your admin dashboard. Make sure you've set a valid Default Author (who does not have publish rights). Go through the general options and make sure everything is okay.

By default, TDO Mini Forms must have at least one form. You can have multiple forms, with various different options in your configuration. You can even copy an existing Form. When you visit any of the Form specific menus, the forms are listed at the top of this menu, so choose go to "Form Options" and select Form 1 if this is the first time you have configured TDO Mini Forms. Read through the options and configure as appropriate. You can automatically create a page on your blog to hold the form from here.

Now move on to the "Create". From here you can configure what is on the Form and how it is processed by dragging and dropping "widgets" from the "Available Widgets" pool to "Your Form" and then configuring the widgets by clicking on the left icon for the widget. It is important to remember that widgets are processed from top down, so they will appear on your form in that order and they will be executed in that order when the form input is previewed, validated and submitted. This can mean that one widget can overwrite a submitted posts settings from another widget (for example multiple Categories widgets can overwrite the default category and themselves). At this stage, try using the form and making sure it works the way you want. 

If you're happy with the generated Form and the submitted posts it creates, then you are finished. However if you want to tweak stuff, change some of the messages, re-arrange fields you can move onto the Form Hacker. You must be careful with the Form Hacker as it is quite powerful and there is very little debugging or error reporting going on. You can totally change how your Form appears and even behaves using the Form Hacker. See the following section "Using the Form Hacker" for more on using the Form Hacker.

= Using the Form Hacker =

With version 0.12, the Form Hacker was added to TDO Mini Forms. Previously a lot of people would modify the source code of TDO Mini Forms to modify a single piece of text used in the form or re-arrange the fields in your form.

Currently the Form Hacker does not allow you to modify the Error message, the Upload Files widget and inline Form and the Custom Field widget preview (FYI: you can do this from within the Custom Field widget configuration).

What Form Hacker does allow you to do is to completely modify the code displayed for a Form. Parts of the Form are often generated on the fly; the Form Hacker gives you access to modifying that actual code as it supports using PHP code within. It is quite powerful but can also be very easy to screw up, so be careful. It also allows you to modify any of Form's general messages such as permission or throttling messages. To use PHP code is fairly easy, for example:

`<?php echo get_bloginfo('title'); ?>`

This will display your blog's title in the form. 

The Form Hacker has a number of "macros". These are special strings that are automatically expanded when the Form is displayed. Some of these macros are used to automatically generate the Form Key (which is used for security). The %%WIDGET:widget-name%% macro can be used to let individual widgets do their thing while you hack everything else. A list of macros can be found by clicking on "Show Help" link at the top of the page. 

**Please be aware that if you use the Form Hacker and you later change the setings of your form (modify widgets or options), these changes are not reflected automatically in the Form Hacker. You must re-edit the hacked form to pick up the new changes.**

= Getting even more out of your Form =

You can now attach additionally PHP code to your form using the "Append to Content" widget. If you drag and drop this widget to your Form, you can use it to add anything you like to the post content, however you can also insert PHP code that will get called when the submission is being processed (and any outputted info gets added to the post content).

For some examples, on the support forums, the Append to Content widget has been used to use the value set in [Custom Field as the Title]( http://thedeadone.net/forum/?p=418#comment-1542 ) and to use the [Submitter's username as Title]( http://thedeadone.net/forum/?p=269#comment-1459  )

== Frequently Asked Questions ==

Here are some useful topics from the [Support Forms]( http://thedeadone.net/forum )

* [Using a form outside of Wordpress]( http://thedeadone.net/forum/?p=3161#comment-4421 )
* [Custom validation to prevent duplicate post titles]( http://thedeadone.net/forum/?p=2814#comment-4406 )
* [Hacking TDOMF to redirect to a custom URL]( http://thedeadone.net/forum/?p=3131#comment-4402 )
* [Creating your own validation routine]( http://thedeadone.net/forum/?p=2224#comment-4315 )
* [How to add and process you're own inputs on a form]( http://thedeadone.net/forum/?p=1905#comment-4093 )
* [Figure the category using alphanumeric categorizations from title of post and add it]( http://thedeadone.net/forum/?p=1618#comment-3611 )
* [Dynamically displaying a form based on a checkbox]( http://thedeadone.net/forum/?p=1458#comment-3484 )
* [Additional Default Categories]( http://thedeadone.net/forum/?p=334#comment-2637 )
* [Potential Solution to not Saving Hacked Forms]( http://thedeadone.net/forum/?p=1230#comment-2571 )
* [Another Custom Title Example]( http://thedeadone.net/forum/?p=1230#comment-2680 )
* [Appending the Excerpt to the Content]( http://thedeadone.net/forum/?p=1306#comment-2862 )
* [Overwriting Default Category]( http://thedeadone.net/forum/?p=1613#comment-3613 )
    
= Plugin clashes and "Server 500 Errors" using Wordpress 2.6.2 =

A number of users experinced random 500 errors using TDO Mini Forms and conflicts with other plugins under Wordpress 2.6.2. If you experince a 500 error, please check your host's error logs. If you don't know how to do that, contant your host.

There are two things check. Are you using PHP4? If you can, try using PHP5 instead. (You can check via phpinfo() or by the error message that appears at the top of any of the TDO-Mini-Form admin menus). [Someone found this solution for their blog hosted on 1and1]( http://wordpress.org/support/topic/204256?replies=16#post-860103 ) and it requires modifying your .htaccess to use PHP5 instead of PHP4. This may not be applicable to other hosts.

The second thing to check is your .htaccess. If you are completely locked out of your blog by the 500 error, try deleting your .htaccess file (normally in the root of your wordpress install). Then you should be able to access the admin UI of your blog (normally at "yourbloguri/wp-admin"). You must then recreate your .htaccess by resetting your permalink structure in the admin UI and re-apply any other changes to your .htaccess (wp-super-cache for example requires changes in your .htaccess).

= Where is the best place to get support for this plugin? =

You can use the [TDOMF Support Forum]( http://thedeadone.net/forum ) or you can post on [Wordpress.org's Support Form]( http://wordpress.org/tags/tdo-mini-forms#postform ). Please avoid emailing me directly. I got a lot of email, so I have a tendency to attach low priorities to  these emails, unless it is directly related to some query on the forum that I have shown an interest in.

= Where do I get the latest updates and news on TDO Mini Forms? =

[TDO Mini Forms News]( http://thedeadone.net/index.php?tag=tdomf ) and here is the [RSS Feed]( http://thedeadone.net/index.php?tag=tdomf&feed=rss2 ).

You can also follow me on twitter [@thedeadone](https://twitter.com/thedeadone). I'll be posting updates about TDO Mini Forms with the tag #tdomf. 

= How do I add a form to a page or post? =

You can use the button in the options menu to create a page automatically.

Or instead you can add:

`[tdomf_form1]` 

to any post or page. The plugin will replace this with your Form 1. If you have multiple forms, each form has an ID. Just replace the '1' with the correct form ID.

You can add it to your template directly using this template tag:

`<?php tdomf_the_form(1); ?>`

If it's an edit form, you'll have to supply the post id:

`<?php tdomf_the_form(1,326); ?>`

= How do I display the submitter info? =

There are options to automatically modify the the_author tag with submitter information if available and also to append submitter information to the end of the post. If thats not good enough for you, you can use the template tag:

`<?php tdomf_the_submitter(); ?>`

= What template tags are available? =

(Replace '1' with the ID of the form you are interested in and replace '100' with the post id of the post or page you wish to edit. If not editing a page, leave out or set to 'false'.)

`<?php if(tdomf_can_current_user_see_form(1,100)) { ?> Link to form <?php } ?>`

`<?php echo tdomf_get_the_form(1,100); ?>`

`<?php tdomf_the_form(1,100); ?>`

These tags must be used within the loop:

`<?php echo tdomf_get_the_submitter(); ?>`

`<?php tdomf_the_submitter(); ?>`

`<?php tdomf_the_submitter_email(); ?>`

The tdomf_get_the_submitter_email template tag can be used to get the gravatar icon for a submitter.

= I want to add custom fields! =

With v0.9, you can! There is now a Custom Field widget avaliable to add to your form. Currenly only text fields and text areas are supported but future versions will support check boxes, drop down lists, radio groups, etc.

= I want to allow my readers to attach a image to a submission? =

With v0.8, you can allow users to upload files. You can specify what files can be uploaded and how big. You can also optionally have the upload files automatically added to the post as an image, link or a Wordpress attachment. 

To add the option to upload files, as admin, go into the TDOMF menu and then the widgets menu. On that page you can drap and drop widgets. Just drag and drop the "Upload Files" widget.

More from the Support Forums:

* [Programatically limit the size of an uploaded image]( http://thedeadone.net/forum/?p=1332#comment-3889 )
* [Styling the appearance of the Thumbnail using the Append widget]( http://thedeadone.net/forum/?p=1306#comment-2862 )
* [How to integrate with lightbox](  http://thedeadone.net/forum/?p=323#comment-3582 )
* [Getting the image thumbnails from a post using Template Tags]( http://thedeadone.net/forum/?p=1700 )
* [Thumbnail to Custom Field]( http://thedeadone.net/forum/?p=1700 )

= I want to allow only certain people to access the form =

The best way to do this is to use Wordpress roles. Create a role using the [Role Manager Plugin](http://redalt.com/Resources/Plugins/Role+Manager "Role Manager Plugin"). This plugin has nothing to do with me. Make sure it is not the default role and that it can't `edit_other_posts` or `publish_posts`. Then you can use the TDOMF options page to set that as the only role that can access the form.

If you don't want people to have to register, you might try looking at this plugin: [Wordpress OpenID Plugin](http://verselogic.net/projects/wordpress/wordpress-openid-plugin/ "Wordpress OpenID Plugin"). This plugin has nothing to do with me. This plugin allows people to use an OpenID identity to login to your Wordpress site. If the user has an account on Wordpress.com, LiveJournal, Yahoo and numerous other sites, they can log in using that account and once they have logged in, you can assign them to the right role.

Another suggestion, but much less secure and not recommended, is to have the page where you have the form, password protected and only send the page link and password to the people you want to access the form.

= I want submissions, even from unregistered users, be published automatically! =

You can disable moderation in the options menu for a specific form and all posts will be published. However such posts get passed through Wordpress' kses filters automatically to remove nasty scripts.

= When people submit posts with YouTube embedded code, it gets stripped! =

Enable moderation and it'll work. If you disable moderation, posts get passed through kses to remove nasty scripts before being published. This removes YouTube code. If you have to approve posts, you can make sure no-one has snuck in something tricky.

Alternativily you can use a custom field. Add the Custom Field widget to your form, set it as a URL and ask your submitters to add the URL of the YouTube video they want to include. Then in your theme, you can use the Custom Fields template tags to automatically display the YouTube video underneath the submitted post. Or you can use another plugin that gives you tags to support YouTube and have the Custom Field append the YouTube link with the tags to your post.

See also [How to submitting YouTube with v0.13+]( http://thedeadone.net/forum/?p=3156#comment-4423 ) on the support forums.

= Can we use TinyMCE, FckEditor, Niced for writing posts? =

I have spent some time exploring the use of TinyMCE (and to a lesser degree FckEditor) for TDO Mini Forms. Both libraries provide a WYSIWYG or "Rich Text" editors in place of your bog-standard text area. Wordpress' write screen using a heavily modified version of TinyMCE. 

There is no direct integration support for these toolkits. However you can easily integrate TinyMCE without modifying any of TDO Mini Forms. 

[I've written up a short tutorial on how you might integrate TinyMCE with the a TDO Mini Form's Form]( http://thedeadone.net/how-to/tinymce-tdomf/ )

= I want to add add tags to QuickTags such as embed video, etc.? =

Right now there is no user interface for adding your own tags to quicktags. Feel free, however, to modify `tdomf-quicktags.js.php` to add any tags you want.

= I want to display some information about the upload files? =

In later versions, proper template tag support will be added. However, for the moment you can use:

`// Gets the name of the first uploaded file for post $post_ID
get_post_meta($post_ID, "_tdomf_download_name_0"); 

// Gets the type of the first uploaded file for post $post_ID
get_post_meta($post_ID, "_tdomf_download_type_0"); 

// Gets the download count of the first uploaded file for post $post_ID
get_post_meta($post_ID, "_tdomf_download_count_0"); 

// Gets the path to the first uploaded file for post $post_ID
get_post_meta($post_ID, "_tdomf_download_path_0"); 

// Gets the command output for the first uploaded file for post $post_ID (if avaliable)
get_post_meta($post_ID, "_tdomf_download_cmd_output_0"); 

// Gets the URI for the thumbnail (if it exists)
get_post_meta($post_ID, "_tdomf_download_thumburi_0"); 

// Gets the name of the second uploaded file for post $post_ID
get_post_meta($post_ID, "_tdomf_download_name_1");

// And so on...`

= Styling TDO Mini Form Posts =

Styling and formatting of posts is really outside the scope of this plugin. Some nominal features in TDO Mini Forms allow you to append content to the Post's content area but this has limited application. Wordpress core gives more than enough options to do this. 

Custom Fields, Title, Categories, Tags, etc. can be displayed in myriad ways using [Template Tags](http://codex.wordpress.org/Template_Tags ), without having to modify your post content using TDO Mini Forms. 

You can style TDO Mini Forms differently to normally posts in your Theme (within the [loop](http://codex.wordpress.org/The_Loop )):

`<?php if(defined('TDOMF_KEY_FLAG') && get_post_meta($id,TDOMF_KEY_FLAG,true) != false) { ?>

   <p>This post was submitted by TDO Mini Forms, so use a TDO Mini Forms format, etc.</p>

<?php } else { ?>

   <p>Default Post Formatting</p>

<?php } ?>`

= Modifying tdomf-style-form.css has no effect on form! =

TDO Mini Forms uses the wp_head action in your theme to add a link to the stylesheet. Check for the existance of a line like this in your header.php:

`do_action('wp_head');`

You can also manually add like this:

`<link rel="stylesheet" href="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/tdo-mini-forms/tdomf-style-form.css" type="text/css" media="screen" />`

= Uploading a bmp file causes errors! =

If you have the options for attachments and thumbnail generation turned on for Upload Files and you try to upload a *.bmp (bitmap) image, you'll get an error like this:

`Warning: imageantialias(): supplied argument is not a valid Image resource...`

Wordpress does not support bitmaps for thumbnails so you cannot use bitmaps for thumbnail generation.

= I can't upload files! : safe_mode and open_basedir issues =

First step, make sure you can upload with the normal Wordpress admin UI. If you can't then your not going to be able to upload with TDOMF until that is sorted. 

If you do and you get an error with something like this

`Warning: mkdir() [function.mkdir]: open_basedir restriction in effect`

or

`Warning: mkdir(): [function.mkdir]: SAFE MODE Restriction in effect` 

Then you host has restricted where you can create and upload files. Safe mode is particularly bad because it'll fail in unexpected ways. Ultimately the best solution is not to use safe mode or open_basedir but you may not have the option to do that.

The best solution is to use a folder to store uploads that does not break safe mode. If you can upload with the normal wordpress interface then you can use something like <path to your wordpress install>/wp-content/uploads. Remember also that you cannot use symbolic links in your path to get around open_basedir restrictions.

You can enable extra log messages from the options screen to see more detailed messages about file uploading. You can also check your "phpinfo()" from the main TDOMF page.

= Having submitted posts not included on your main page =

This is outside the scope of TDOMF as TDOMF only enables people to submit posts. However you can use a plugin like [Advanced Category Excluder Plugin](http://wordpress.org/extend/plugins/advanced-category-excluder/ "Advanced Category Excluder Plugin"). This plugin has nothing to do with me. You could have posts submitted to a specific category that is excluded from your main blog.

= I'm getting "Bad Data" errors when I submit posts =

* Make sure you disable any caching plugins on the form page. 
* Try changing the Form Verification Options to "Wordpress nonce" (or "None" if you are really desperate)
* Try changing the Form Session Method to use the database

= Getting TDO Mini Forms to work with WordpressMU =

[Installing TDOMF plugin on WordPress MU](http://www.injoit.com/blog/2008/07/25/tdomf-plugin-hack-for-wordpress-mu/)

= Credits =

I've used code in TDOMF that I've found in the wild so some credit is due to these authors for making their source code avaliable for re-use. 

PHP Function to create a random string based on (http://www.tutorialized.com/view/tutorial/PHP-Random-String-Generator/13903)

PHP Function to validate an email address based on (http://www.ilovejackdaniels.com/php/email-address-validation/ )

PHP Function to turn a file size in bytes to an intelligable format based on (http://www.phpriot.com/d/code/strings/filesize-format/index.html)

Quicktags Javascript script taken from (http://www.alexking.org/)

Freecap (PHP Image capatcha) taken from (http://puremango.co.uk/)

Customfield Select Box javascript based on (http://www.mredkj.com/tutorials/tutorial006.html)

PHP Function to display a human readable time interval based on a function I found here (http://ie2.php.net/time)

Checkbox support in the Categories Widget initially added by [Sillybean](http://www.sillybean.com)

Several updates to make the generated forms W3C compliance done by [Laurent Grabielle]( www.3w3t.com ) 

SQL Time calcuation function used in calculating next post time in queue, donated [Adam Selvidge]( http://www.myconfinedspace.com/ )

reCaptcha PHP library taken from ( http://recaptcha.net/ )

GeoMashup widget based on hack from [hitekhomeless]( http://hitekhomeless.net )

Also thanks to everyone who donated and offered feedback and testing!

== Screenshots ==

1. Example of the default Submission Form as displayed to non-registered users
2. The Moderation page (v0.13) (Wordpress 2.7.1)
3. "Your Submissions" page for registered users (Wordpress 2.3)
4. Example of the default Editing Form as displayed to a logged in user (v0.13) (Wordpress 2.7.1)
5. The overview page (Wordpress 2.3)
6. Constructing your form using "widgets" (Wordpress 2.3)

== Known Bugs == 

* If you deactivate the plugin at a later date, links to uploaded files will break (as they use a wrapper in the plugin). However with v0.9.3, you can set an option in the "Upload Files" widget to use direct links instead of the wrapper. In v0.10.3, the default is to use direct links but you can switch back to the handler if this does not work correctly for you.
* Uploading a bmp image with attachment and thumbnail options turns on causes an error. Wordpress does not support bitmaps for thumbnail generation.
* In IE, can't select text in the Text widget (possible a common bug for IE and Wordpress)
* The 'gmt_offset' is not used in the macro subsitituion for %%SUBMISSIONDATE%% 
* Incomatibilitiy with with "vbbridge"
* Image capatcha must update after failed "submit"
* Possible incompatibility with Google XML Sitemaps plugin
* Bug with GeoMashup Integration
* TwitterTools not working with TDOMF publish
* Missing Required option in category field
* Sanatized file upload filenames/paths
* Diacritc marks reset form hacker
* Bug: Weird "From" email for post moderation emails
* Bug: No image preview when using attachment options
* Bug with AJAX and slashes
* Bug: Widget configuration panels not showing up
* To be investigated: tinymce conflict with AJAX form
* To be investigated: upload-link error
* To be investigated: Bug with post times
* Custom Field history is not stored in reivions edits (can't compare with older revisions/edits)

== Changelog ==

= 0.13.9 =

* The fix for the form creator page was incomplete. I had to update the "seralize" jQuery/js previousily to use the newer JQuery included with WP3.0. However this new seralize function mistakes '-' and '_' as '='. Apparently this is a feature. This meant widgets with '-' in the name like "i-agree" or "custom-field-1", would not be listed as being on the form when you save your configuration. If I was doing the edit form from scratch, I'd include jQuery with TDOMF so I wouldn't have to do maintaince code changes in the admin area.

= 0.13.8 =

* Fixed the form creator page to work again with latest Wordpress 3.0 upgrade

= 0.13.7 =

* Moved 'tdomf_random_string' to a different file so that it is always included. This was causing some blogs to show only a blank page after upgrade to 0.13.6

= 0.13.6 =

* "Number" option in TDOMFTextField (and therefore Custom Fields widget)
* Add some new checks to form hacker to catch the bug. Was able to reproduce it and it seems PHP has some problems with non-latin types being converted into database. Why doesn't this show up elsewhere in Wordpress?
* Removed usage of '_SERVER' in backend (or if not possible, added esc_url wrapper)
* Modified AJAX to prevent double submits
* Refactored "Hidden" field as TDOMFWidgetFieldHidden and it now allows PHP code to be executed
* Bug in TDOMFWidgetFields meant that checkbox options were getting reset when not being saved in 'Create'
* Upload Files Widget refactored into Widget Class. Should be completely compatible with existing setups
* Upload Files Widget extended to support Multiple Instances (thanks to being refactored into a Widget Class)
* Upload Files Widget now tracks the files it uploads and only deletes those when a post is removed. Before it would delete the folder however this could lead to a false-positive and delete non-TDOMF unintentionally.
* Imported some code improvements from [DD32](http://dd32.id.au/2009/11/01/youre-doing-it-wrong-2/)
* Select Field refactored in TDOMF Custom Field widget.
* Custom field widget now supports edit mode! Includes revision history for each edit with the ability to revert
* Select field on Custom Field widget for Select Field, is no longer shrunk

Id/name for some HTML elements has consquently updated:

* `customfields-s-list-X`       to    `customfields-s-X-s` (where X is an numeric id)
* `customfields-hidden-X`       to    `customfields-h-X-h` (where X is an numeric id)

= 0.13.5 =

* Previous recent versions prevented people from enabling spam. The code waswritten in such a way that the spam protection had to be enabled before spam protection could be enabled!
* Replaced the usage of $_REQUEST with $_POST in form hacker. Not sure if it'll have any impact on the form-hacker-reset problem but it should be safer
* Have tested and fixed extra slashes being added on WP 2.8.x builds with and without magic quotes turned on. 
* Refactored Custom Field, Content, Excerpt widgets and created a common  textfield and textarea (as part of the fix for magic quotes). This allows me to add new features to multiple widgets at a time. 

Id/name for some HTML elements has consquently updated:

* `content_content`            to    `content-text-ta`
* `content_title`              to    `content-title-tf`
* `excerpt_excerpt`            to    `excerpt-ta`
* `customfields-textfield-X`   to    `customfields-tf-X-tf` (where X is an numeric id)
* `customfields-textarea-X`    to    `customfields-ta-X-ta` (where X is an numeric id)
* `customfields-checkbox-X`    to    `customfields-cb-X-cb` (where X is an numeric id)
* `customfields-hidden-X`      to    `customfields-h-X-h`   (where X is an numeric id)

= 0.13.4 =

* "Warnings on post": errors appear about "post.php". I left some unfinished code in an action which was breaking one of the wordpress functions. 

= 0.13.3 =

* Added filtering to the moderation screen. Can filter by user or ip and/or by form
* Enabled the syntax code highlighting on Form Hacker (and messages)
* Added new built in wordpress diff as a diff render for form diffs
* Updated tdomf_the_form (it was dropping $post_id arg when calling tdomf_get_the_form)
* TDOMF Revision page (supports fields and custom fields)

= 0.13.2 =

* Fixed extra slashes in AJAX preview of submit forms
* Scheduled post count incorrect
* Shortcut links on moderation screen incorrect
* "Publish Now" when post was queued, now works
* Edit own posts was not actually working!
* Queue on all posts, not just tdomf
* Tabbed General Options
* New option to always show "moderation" links (no auto-hiding)
* Spam checked exclusion rules added/tested
* Some debug options added
* Per Form Spam check exclusion rules added/tested
* Wordpress 2.8 compatibility

= 0.13.1 =

* Fixed "Notify" and "Auto-Respond" widgets as theses were preventing not logged in users from accessing forms
* Removed "public static" from class definitions as they are not part of php4.
* Hacker replacements buggered - affected Upload Files widget

= 0.13 =

* Corrected TDOMF_FULLPATH to use WP_PLUGIN_DIR instead of absolute path
* Fixed '$' not working in preview
* Fixed characters getting eaten up in preview
* Fixed image capatcha not showing up (bad shorttag and path to wp-load.php)
* Added a message to the Form Creator to indicate if a the Form Hacker has been enabled and will prevent changes to form.
* Forms to edit posts and pages

= 0.12.7 = 

* Form Hacker did not use FORMID so when you copied a form, it would break
* Updated widget classes (may "break" existing forms)
* Added a "link" to the Auto Respond Email widget that allows users to set a custom field on a post. Can be used to verify if the user email is valid.
* Fixed critical Windows host bug that would attempt to delete root drive. The add_post_meta Wordpress function would strip back slashes out of input and basically feck up the Windows path. Now the path name is "protected" before being passed to add_post_meta.
* Fixed post queuing. This was broken in two ways. The date/time calculation was wrong and now has been updated based on generousily donated code from [Adam Selvidge]( http://www.myconfinedspace.com/ ). Second a change in Wordpress 2.7 meant that setting the future status was being ignored when the post was being published.

= 0.12.6 =

* Hopefully, finally fixed additionally slashes being added to the content.
* Bug with 2.5 and breaking wp-comments.php file
* Bug with file uploads and accidentially displaying an error when no error exists and therefore causing the form to break.
* New more powerful form access configuration
* Textarea and Textfield Custom Field couldn't support '0' as a valid input, as PHP would treat this as empty
* Fixed 'true' in title field of textarea for content
* Can now disable auto-publishing of admin posts
* Changed CSS class 'shadow' to 'tdomf_shadow' to avoid conflicts
* If there are too many users (say over 60), tdomf will instead ask for login names for the users in options rather than slow down the UI displaying a dropdown. This affects the edit-post screen and the general tdomf options page.
* AJAX on form will scroll the window up to the preview or message if there is any so users don't miss it
* ReCaptcha widget now works with AJAX
* New permalink widget 
* Links to uploaded files included on the moderation screen
* New GeoMasup integration widget
* Initial Widget Class 

= 0.12.5 =

* A "link" to the thumbnail is stored on the post using TDOMF_KEY_DOWNLOAD_THUMBURI key.
* Excerpt Widget
* Comments Management Widget
* Categories Include Field enabled (was accidently left disabled in previous release)
* Category Widget now has a Order by and Order options
* Recaptcha Widget
* Warning displayed if "I Agree" text is not modified
* Integration with Subscibe to Comments plugin via "Subscribe to Comments" widget
* Publish Now button
* Instead of notifying specific roles that a submission is requiring moderation, you can now specify an email list
* Tags Widget now has options for default tags, required and to disable user adding tags

= 0.12.4 =

* Solved "$post_ID == 0" problem. See ( http://thedeadone.net/forum/?p=325#comment-1446 )
* Added some error checking around cookie session info
* Stopped multiple revisions from being created on post submit
* Better Error/Warning reporting to Admins
* Form toolbar added to both Widgets and Form Hacker (easier to move between the screens for specific forms)
* Error checking on the categories widget when the default category is excluded (it selects a new "default") and also if all categories are excluded
* Error checking if post and ajax submit urls can be reached
* Include option enabled and tested for Categories widget
* Removed link to non-existant help page and set the y offset of widget controls to zero so it doesn't get lost. - Thanks Oleg Butuov for those fixes!
* Fixed multiple categories selection not showing in preview.
* Now tries to use wp-load.php before using wp-config.php as per the new Wordpress 2.6 way
* Import/Export re-implemented and much cleaner
* tdomfinfo() now produces useful output again
* Updated the AJAX code so that it now properly passes *all* variables (previousily multi-choice selections got reduced to single-choice)
* Forms now better validate as W3C compliance - Thanks Luarent Grabielle
* Potential source of 500 Server Error message is because TDO-Mini-Forms seems to require PHP5. See readme.txt for more information.
* Wordpress comment notification on tdomf submitted posts would go to the author set to the post who may not have admin rights to delete/spam the post. Now TDOMF will check if a post is owned by TDOMF and if so will redirect the notification email to the admin if it is post author cannot delete or spam comments.
* Readme.txt heavily expanded with help sections on the new complex features like form hacker (as if anyone reads the readme any more)
* Queue time calculation correction code imported. Code donated by Adam Selvidge
* Fixed a small bug in the widgets page, where if the page was localised fully, drag and drop would not work.

=  0.12.3 = 

* Bug in tdomf-msgs.php that would occur for unregistered users only
* Auto Respond Email widget
* Small mistake in whoami widget hack, "email" title used for webpage field
* Checkbox settings were not being correctly passed in AJAX
* Full paths are used, not just relative
* Ban User/IP links from moderation email
* Enabling extra debug messages and turning on error messages to user also turns on all error reporting in PHP
* Added extra debug messages and handling around post_id 0 submissions (still dont' have a clue about them)
* Moderation emails to admins can now be turned on if moderation is turned off
* Custom Field summary was not appearing in admin emails

= 0.12.2 =

* Broken code got into v0.12.1 in the rush to get the patch for the security risk out.

= 0.12.1 =

* Hacked messages could only be saved for Form ID 1.
* Gravatars in Top Submitters
* Fixed Category Widget radio button for Checkboxes doesn't work in Firefox
* get_memory_usage not supported on many user-installed versions of PHP
* Security risk with Custom Fields fixed.

= 0.12 =

* AJAX (with fallback support)
* Small bug in that validation widgets were not being called properly if they use the action "tdomf_validate_form_start" (such as any multiple instant widgets like 1 Question Capatcha and the Image Capatcha. Same also for preview.
* Redirect to published post option
* Initial implementation of Form Hacker
* Text Widget now uses Form Hacker macros
* Log now has size limit!
* Categories Widget now supports radio buttons and checkboxes
* Fixed a minor bug in the widgets panel where you had to reload the page after you saved a change in the number of any of the multiple widgets
* Append widget
* Upgrade notice
* New Template Tags: tdomf_get_the_submitter_email and tdomf_the_submitter_email

= 0.11.1 =

* Using a dollar sign plus a value in a input field would cause the first two digits to disappear - now fixed.
* Fixed a mistake in the post scheduling, GMT offset would kick in if time greater than an hour
* Added times and list of scheduled posts to Your Submissions
* Corrected some formatting mistakes on the options page
* Added a pot file and removed the po file.

= 0.11 = 

* Fixed a small behaviour issue in generate form where it would keep the preview, even after reloading the page!
* Integreted with Akismet for Spam protection
* Fixed an issue with "get category" widget where it would forget it's settings occasionally
* Increased the number of tdomf news items and added an list of the latest topics from the forum to the overview page
* Published Posts can now be automatically queued!
* Fixed "Your Submissions" links for users who are not-admin such as Editors
* Can add throttling rules to form
* Can now view tdomfinfo() in text and html-code formats
* Import and Export individual Form settings
* Top Submitter Theme Widget

= 0.10.4 =

* Fixed a bug that made TDOMF incompatible with PHP5 (see uniqid)
* Fixed a bug where some widgets were not making it to the form when the form is generated. This was a mistake in the "modes" support added in v0.10.3.

= 0.10.3 =

* Fixed a bug in the random_string generator: it did not validate input and I've been using a value that's too big (which meant it could return 0)
* Widgets now support "modes" which means widgets can be filtered per form type. Right now that means widgets that don't support pages will not appear, if the form is for submitting pages. 
* Can now choose how to verify an input form: original, wordpress nonce or none at all
* Implemented a workaround for register_global and unavaliablity of $_SESSION on some hosts
* Fixed double thumbnail issue in WP2.5

= 0.10.2 =

* Fixed a bug if you reload the image capatcha, it would not longer verify
* Added a flag `TDOMF_HIDE_REGISTER_GLOBAL_ERROR` in `tdomf.php` that can be set to true to hide the `register_globals` errors that get displayed.
* WP2.5 only: Can now set a max width or height for widgets control on the Form Widgets screen.
* Compatibily with Wordpress 2.5

= 0.10.1 =

* Fixed a bug when if you inserted an upload as an attachment it would overwrite the contents of the post.
* Fix to categories widget where widget on other forms than the default  would forget it's settings at post time.
* Custom Field widget was ignoring append format for multi-forms 

= 0.10 =

* Suppressed errors for is_dir and other file functions just in case of open_basedir settings!
* Use "get_bloginfo('charset')" in htmlentities in widget control. Hopefully this will finally resolve the issues with foreign lanaguage characters
* Multiple Form Support
* Widgets that validate know if it's for preview or post. Certain validation should only occur at post like captcha and "who am I" info for example.
* Option to specify the max number of instances of a multi-instant widget per form
* Can now set a form to submit pages instead of posts.
* Fixed a bug where customfield textfield would submit empty values for the custom field if you had magic quotes turned off.
* Update the "Freecap" Image Captcha so that the files get included in the release zip Wordpress creates.

= 0.9.4 = 

* Added "Set Category from get variables" widget
* If moderation turned off, when post published, redirect to published post page.
* Fixed Custom Field widget javascript. Now works properly in Firefox (why does Firefox break on code that works in Opera and IE all the time?)
* Image Captcha Widget
* Updated all text fields input (and output) to use htmlentities. Hopefully this will cure foreign character input/output issues and weird re-encoding issues with widget settings.
* Word count or character limit on post content
* Theme Widget that displays the form!
* Add "credits" to readme.txt for various places I pull source and other stuff from
* Added a "Read More..." `<!--more-->` tag to the quick tags
* Fixed Bug when multiple notifications to submitter when post is edited after approval

= 0.9.3 = 

* Fixed customfield textfield control radio group in Firefox
* Fixed customfield textfield ignoring size option
* Fixed customfield textarea putting magic quotes on HTML
* Fixed customfield textfield not handling HTML and quotes well.
* Fixed customfield textfield not handling foreign characters well.
* Fixed customfield textarea quicktag's extra button only working on post content's quicktag's toolbar
* Updated customfield to optionally can automatically add value to post with a user defined format
* Removed any "short tag" versions (i.e. use "<?php" instead of "<?")
* Add link to view post from moderator notification email
* Auto add buttons to post content to "approve" or "reject" submission on the spot
* Enable/disable preview of customfield value
* Added option to Upload Files widget to use direct links
* Get phpinfo page
* Conf dump page
* Updated stylesheet to look nice in IE
* Fixed borked thumbnails from v0.9
* Fixed some issues with file uploading and safe_mode
* New Option: Enable/Disable "Your Submissions" page
* New Option: Enable extra debug log messages
* Make the tags widget conditional on the existance of 'wp_set_post_tags'. This will improve backwards compatibility with Wordpress < 2.3 (officially unsupported)
* Category widget: Multiple category selection
* Category widget: Display as list
* Customfield now supports select and checkbox options
* Added po file for translation

= 0.9.2 =

* Potential fix for the never-ending "session_start" problem. Using template_redirect instead of get_header. 
* New Suppress Error Messages (works to a point)
* Warnings about register_globals added
* Fix for file uploads mkdir for windows included. Thansk to "feelexit" on the TDOMF forums for the patch
* "Latest Submissions" added to main Dashboard
* Two widgets for your theme!
* Fixed 1-q captcha widget not accepting quotes (")

= 0.9.1 =

* Fixed a javascript error in Quicktags that blocked it from working on Mozilla
* Fixed the admin notification email as the Wordpress cache for the custom fields for posts was being forgotten so the admin email did not contain information about IP and uploaded files.
* A define was missing from tdomf v0.9: TDOMF_KEY_DOWNLOAD_THUMB
* Spelling mistake fixed in "Your Submissions"

= 0.9 =

* Updated Upload Files: if a file is added as attachment, Wordpress will generate a thumbnail if the file is an image.
* New Upload File Options: You can now automatically have a link added to your post that goes to the attachment page (can even use the thumbnail if it exists). Additionally, if the thumbnail exists, can insert a direct link to file using the thumbnail).
* Uploads added as attachments will inherit the categories of the post (but remember the order of widgets is important so if the categories get modified after the upload widget has done it's biz, these changes won't be affected to the attachments)
* More info on error checking!
* "Notified" instead of "notify" in Notify Me widget
* Added quicktags to the post "Content" widget (restrict tags option hides restricted tags from toolbar)
* Uninstall was broken! Was not deleting option settings.
* Removed "About" menu and reorgainsed the overview page a bit. 
* Added first draft of custom fields (only textfield and textarea supported)
* Updated "1 Question Captcha" and "Categories widgets" to support multiple instances
* Added a "Text" widget
* Fixed a bug when deleting a post with uploaded files on PHP4 or less

= 0.8 =

* Upload Feature added
* Widgets can now append information to the email sent to moderators
* Tag Widget: allow submitters to add tags to their submissions
* Categories Widget: First run of the categories widget.

= 0.72 = 

* Date is not set when post is published. This was okay in WP2.2.
* Comments are getting automatically closed (even if default is open). This was okay in WP2.2.
* widget.css in admin menu has moved in WP2.3. This is no longer compatible with WP2.2.
* Can now again select a default category for submissions and new submissions will pick that category up. With WP2.3, tags and categories have changed to a new "taxonomy" structure, which messed up how TDOMF works.
* Added a "tdomf_widget_page" action hook
* Fixed Widget page to work in WP2.3. WP2.3 now uses jQuery for a lot of its javascript needs
* If you happen to use as your database prefix "tdomf_", and then if you uninstall on WP2.3, it would delete critical options and bugger up your wordpress install.

= 0.71 =

* Two small mistakes seemed to have wiggled into the files before 0.7 was released. Still getting the hang of SVN I guess.

= 0.7 =

* New "Overview" page
* Move the various admin pages to it's own submenu
* Updated Edit Post Panel (uses built in AJAX-SACK)
* Updated options menu
* Code refactored and renamed files and restructured directories
* Logging feature
* Can uninstall the plugin completely. Also removes v0.6 unused options too.
* "Create Dummy User" link on options page
* "Create Page with Form" from options page
* Properly implemented form POST and dropped AJAX support
* Can now automatically updates "the_author" template tag with submitter info
* Can now automatically add "This post submitted by..." to end of post content
* Bulk moderation of submitted posts, users and IPs
* "Nonce" support for admin backend pages
* "Your Submissions" page for all users. Form is included on this page.
* Form should be XHTML valid (unless a new widget breaks it!)
* Handle magic quotes properly
* Allow YouTube embedded code to be posted, though this option is only allowable if moderation is enabled! Otherwise Wordpress' kses filters will pull it out.
* Reject Notifications as well as Approved Notifications
* Can now restrict html tags on posted content
* New Template Tag: tdomf_can_current_user_see_form() returns true if current user can access the form
* Simple question-captcha widget: user must answer a simple question before post will be accepted.
* "I agree" widget: user must check a checkbox before post will be accepted.

= 0.6 =

* Options Menu: Control access to form based on roles
* Options Menu: Control who gets notified to approve posts by role.
* Options Menu: Default author is now chosen by login name instead of username
* Javascript code only included as necessary (i.e. not in every header)

= 0.5 =

* Tested on Windows based host
* Chinese text does not get mangled
* Post Edit Panel now works properly on Firefox (and does not prevent posting).

= 0.4 =

* New template tags: tdomf_get_submitter and tdomf_the_submitter.
* The plugin should work on Windows based servers
* A TDOMF panel on the edit post page
* Posts can now be very long (no 250 word limit)

= 0.3 =

* Ported to Wordpress 2.1.2.

= 0.2 =

* Fixed bug: If default author had rights to post, anon posts would be automatically published.
* Replaced the word "download" used in messages to the user.
* Added a "webpage" field when posting anonymously.

= 0.1 =

* Initial Release with basic features

= Preview: 21 November 2006 =

* Preview Release, only on wordpress.org/support forums.