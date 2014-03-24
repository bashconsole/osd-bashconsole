Title: The test task for the "Wordpress/PHP Senior Developer" position
Author: bashconsole
Website: http://bashconsole.com
Tags: Sidebar submenu, sidebar vertical submenu, sidebar submenu widget, sidebar submenu plugin
Requires at least: 3.8
Tested up to: 1.0
Stable tag: 1.0

== Description ==

This is the implementation of object oriented Wordpress 3.8+ plug-in called “osd-bashconsole” that can be used as
a Wordpress widget and as a Wordpress shortcode (naming convention: [osdsubpagesbashconsole]).
 
The plug-in  displays a list of all subpages of the page it is placed on. It fetches and displays all subpages' titles,
truncated after 20 characters and (if present) shows a very small thumbnail version of the pages/posts 'featured image'
next to the title.
 
== Installation ==

1. Upload `osd-bashconsole` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add submenu to your widgetable area (no configuration options supported in this version) or add a shorcode
[osdsubpagesbashconsole] to a page (no shortcode parameters supported in this version).


== Changelog ==

= 1.0 =
* Initial Release