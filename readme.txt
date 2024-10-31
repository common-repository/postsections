=== PostSections ===
Contributors: webcat
Donate link: http://rachel-webcat.com/
Tags: post, ajax, usability
Requires at least:
Tested up to: 3.2.1
Stable tag: `/trunk/`

Split your posts into sections and provide an easy to navigate interface for your readers

== Description ==

Provides support for splitting up long posts into sections.  Set the maximum length you want to display of your posts.
The plugin provides xhtml Transitional compliant code with back and next navigation to scroll through your posts.  It uses jQuery in no conflict mode to provide ajax functionality to speed up page load times.  Non-javascript browsers or even those that dont support jQuery will load post sections through normal wordpress mechanism.

== Installation ==

1. Upload `post-sections` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit section lengths via the admin panel provided under the Wordpress Tools menu
4. Depends on Wordpress add_filter function and has run order set to 999
5. Depends on own version of jQuery and uses jQuery in no conflict mode - is a source of potential clashes with other plugins

== Frequently Asked Questions ==

= Is this plugin supported? =

Yes it will be supported via http://www.rachel-webcat.com/customer-support.

== Screenshots ==

* 1. Plain Vanilla response times `/tags/2.11/postsections/no-plugins-running-obgzip-post-nd-get.png`,
* 2. With plugins 1000 user load `/tags/2.11/postsections/Max-Load-1000-150-words-per-section-plugins-running-obgzip-post-nd-get.png`

== Changelog ==

==2.25==
*Added class to bookmark
*Bug fix for division by zero
*Planned option to add br, img and p tags to wordcount to keep posts the same size
*Planned remove [section] tag from all posts on uninstall
*Planned option to deactivate plugin so section tags dont show
*Planned print view of whole post

==2.23==
*Update to include zip in file - last update used old zip file

==2.22==
*Bug fix for archive pages and [sections] appearing in posts

==2.2==
*Fixed bugs in settings
*Added Section titles and bookmarks for each section allowing readers to come back to the same section - firefox opens the bookmaarks in the sidebar
*Tidied up code
*Ajax runs faster (see scrrenshots of speed profiles)
*Added error logging
*Please submit contents of errors.txt in plugin folder with any bug reports.  Thank you.
*Added optimising code
*taken graphs of performance under load with jmeter changed version
*Added number of sections to get/post params

==2.11==
*Bug fix

==2.1==
*Records problems in errors.txt file and deactivates the plugin to avoid unnecessary disruption to the blog
*Check errors file in plugin folder if the plugin will not activate - please report bugs.  Thanks
*Corrected bug in handling errors

==2.0==
*Add sections only to required posts with editor button.
*Edit the id's of navigation links for convenient styling
*Known issue: Plugins that use AJAX calls from post will not work with this plugin

= 1.3 =
*Tidier parsing of content
*Added return to start link

= 1.2 =
*Bug fix
*Better section finding
*Refactoring bugs

= 1.1 =
*Bug fix
*Works via front end without login now
*Shortcodes with Javascript esp ajax calls will NOT work with this version
*Planned: add page/post selection to admin panel, add height/lines options for display of content

= 1.0 =
*First release


== Upgrade Notice ==
Upgrade to fix minor bugs and run the newest version of the plugin with additional features