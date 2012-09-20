=== WolfNet IDX for WordPress  ===
Author:             WolfNet Technologies, LLC
Contributors:       wolfnettech, ajmichels
Tags:               idx, mls, homes
Requires at least:  3.3.0
Tested up to:       3.4.1
Stable tag:         {X.X.X}
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

The WolfNet IDX for WordPress plugin provides IDX search solution integration with any WordPress
website.


== Description ==
The WolfNet IDX for WordPress plugin provides IDX search solution integration with any WordPress
website. To integrate WolfNet IDX data with your WordPress website, you must have a WolfNet IDX
property search solution. To activate the WolfNet IDX for WordPress plugin, you must have a unique
product key. Please contact WolfNet Customer Service for support via phone at 612-342-0088 or toll
free at 1-866-WOLFNET, or via email at service@wolfnet.com.


== Installation ==
There are no special instructions for installing the plugin, however a valid product key must be
entered in the "WolfNet >> General Settings" page before any IDX data can be displayed.

= WordPress.org Installation =
1. From the your WordPress admin (http://example.com/wp-admin) go to the Plugins page.
1. Click "Add New"
1. Search for "WolfNet".
1. Click "Install Now" under the "WolfNet IDX for WordPress" plugin.
1. Click "Activate Plugin"

= Manual Installation =
1. Place the 'wolfnet' folder in your '/wp-content/plugins/' directory.
1. Activate "WolfNet IDX for WordPress" from the "Plugins" page in the admin.


== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 0.1.0 =
* Initial beta release for limited distribution.

= 0.1.1 =
* Fixed CSS issue causing a hidden overlapping element to interfere with other elements on the page.

= 0.1.2 =
* Implemented new version of WPPF which fixed some HTTP web service issues.

= 1.0.0 =
* Initial version for public release.
* Added Title Option to All Widgets
* Updated plugin admin menu to use a generic top level title and a more specific sub menu title.
* Added Search Manager for creating advanced search criteria.
* Added Custom Post Type to save search manager data.
* Updated Widgets and Shortcodes to support "advanced" mode to pull from saved search criteria.
* Added asynchronous product key validation
* Created "Shortcode Wizard" as a new button on the Post/Page edit form.
* Moved jQuery files into root JS directory (all JS files are now in the same directory)
* Aligned save button on settings page with fields rather than labels.
* Added custom description to each widget.
* Added input types to widget option forms which were missing them.
* Fixed some issues with CSS and JavaScript.
* Added new shortcode and widget for displaying properties in a list with address and price (wnt_list).
* Implemented updated framework code.
* Simplified the inclusion of the autoloader class.
* Removed some unnecessary styles.

= 1.0.1 =
* Updated hardcoded URI in admin JS.
* Adding placeholder content for support page.
* Moved search builder HTTP call to service and added support for cfid and cftoken in mlsfinder URLs.
* Fixed JavaScript compatability issue with date by moving date formating into the backend.
* Fixing minor bug preventing "more info" tool tips from being displayed in widget forms.
* Fixing minor bug causing ** DELETED ** item to be displayed on new widget instances.
* Fixed JavaScript for property list widgets
* Fixed some bugs with IE
* Fixed bug causing URLs with no trailing slash to break ajax requests.
* Fixed minor bug with Abs/Rel paths.

= 1.0.2 =
* Fixed bug preventing original Grid parameters from working correctly.

= 1.0.3 =
* Fixed some minor bugs based on initial QA feedback.
* Fixed some PHP warnings and notices.

= 1.0.4 =
* Fixed bug with jQuery datepicker in Search Manager.

= 1.0.5 =
* Increased price cap from $10mil to $100mil.
* Updated text on General Settings page.
* Updated text on Support page.
* Updated text in JavaScript message displayed when the user is about the changed a widget using a deleted saved search.
* Updated text on Search Manager page.
* Updated Info Tooltip text on widget and shortcode pages.
* Updated widget and shortcode descriptions.
* Added JavaScript to remove unused buttons on Saved Search custom post type edit screen.
* Updated Listing Grid jQuery plugin to account for the varying heights of grid items.

== Upgrade Notice ==

