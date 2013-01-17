=== Hello Simpsons Chalkboard Gag ===
Contributors: dan.rossiter
Tags: simpsons, bart simpson, admin panel, fun, chalkboard gag, hello dolly
Requires at least: 3.0
Stable tag: 1.2.1
Tested up to: 3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is a chance to relive your childhood. Featuring the chalkboard gags of Bart Simpson, 
this plugin is sure to put a smile on your face.

== Description ==

**A big thanks is owed to [The Simpsons Archive](http://www.snpp.com/guides/chalkboard.openings.html),
without which this plugin would not be possible!**

This plugin was based on the Hello Dolly plugin that most active WordPress users are familiar. Much like 
the reason why Hello Dolly was created, the Simpsons Chalkboard Gag plugin was designed to bring a little 
light-hearted fun into every page of the admin panel.

As an added bonus, this plugin creates the `[simpsons]` shortcode which can be included on any page or post 
and, when used, will return a different Bartism each time the page is loaded.

*If this plugin brings a smile to your day, please take a moment to
[rate it](http://wordpress.org/support/view/plugin-reviews/hello-simpsons-chalkboard-gag#postform)!*

== Installation ==

1. Upload `hello-simpsons-chalkboard-gag` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Sit back and chuckle at Bart's wit every time you load an admin page

Once installed, you will also be able to use the `[simpsons]` shortcode to include a random Bartism anywhere 
within any of your pages or posts.

**NOTE:** Though this plugin can, in theory, run alongside the **Hello Dolly** plugin that is active 
by default in WordPress distributions, it is strongly discouraged since the two don't look nice 
together. Please disable or uninstall **Hello Dolly** before using this plugin for best results.

== Changelog ==

= 1.2.1 =
* **Enhancement:** Very minor change to help better debug any potential issues that users may have.

= 1.2 =
* **Bug Fix:** Plugin now utilizes a WordPress core wrapper function (`wp_remote_get`) to **reliably** retrieve 
the remote chalkboard gags without breaking.
* **Bug Fix:** A few minor changes were made to how the chalkboard gags are stored, making the plugin faster.
* **Change:** The alert that **Hello Dolly** is active was removed. User can decide whether they want both running or not.

= 1.0 =
* **Enhancement:** Plugin now performs **much** more efficiently by storing most up-to-date.
chalkboard gags in your local database. A daily update checks for more Chalkboard Gags to display.
* **Enhancement:** Plugin now alerts user on activation if **Hello Dolly** is active (since the two 
plugins do not look good running side-by-side).
* **Enhancement:** This release also provides much cleaner code for anyone interested in looking 
at the backend.
* **Bug Fix:** Minor typo corrections in readme and plugin documentation.

= 0.5 =
* **Initial Release:** Supports display of random Chalkboard Gag within the admin panel **and** through 
use of the `[simpsons]` shortcode in any page or post.

== How The Plugin Stays Up-To-Date ==

As new episodes of The Simpsons are released, more chalkboard gags are avialable. To avoid pushing a new 
plugin version every week or two, a remote repository is maintained with up-to-date chalkboard gags 
(within a week or two). 

If this remote repository is ever down for any reason, the plugin also includes a local 
database with all of the chalkboard gags up to the most recent release of the plugin.
