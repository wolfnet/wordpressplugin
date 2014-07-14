=== WolfNet IDX for WordPress  ===
Author:             WolfNet Technologies, LLC
Contributors:       wolfnettech, ajmichels, asbaumgart, michaelpidde, tpenney
Tags:               idx, mls, homes
Requires at least:  3.5.1
Tested up to:       3.9
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
free at 1-866-WOLFNET, or via email at service@wolfnet.com. You may also find us online at
[WolfNet.com](http://wolfnet.com).


== Installation ==
There are no special instructions for installing the plugin, however a valid product key must be
entered in the "WolfNet >> General Settings" page before any IDX data can be displayed.

[Downloading & Activating Your WolfNet WordPress Plugin](https://www.youtube.com/watch?v=hp9ojJdstQM&list=UUWqJ_1djHKa_Uxg_bZ4HiTw)

= WordPress.org Installation =
1. From the your WordPress websites admin section go to the Plugins page.
2. Click "Add New"
3. Search for "WolfNet".
4. Click "Install Now" under the "WolfNet IDX for WordPress" plugin.
5. Click "Activate Plugin"

= Manual Installation =
1. Place the 'wolfnet' folder in your '/wp-content/plugins/' directory.
2. Activate "WolfNet IDX for WordPress" from the "Plugins" page in the admin.


== Frequently Asked Questions ==

[WolfNet WordPress Plugin Basic Display Options](https://www.youtube.com/watch?v=tOLJDhCvgzQ&list=UUWqJ_1djHKa_Uxg_bZ4HiTw)

[WolfNet WordPress Plugin Advanced Display Options](https://www.youtube.com/watch?v=oAoljC6_yF8&list=UUWqJ_1djHKa_Uxg_bZ4HiTw)


== Screenshots ==


== Changelog ==

= 1.5.2 =
* Deprecating support for versions 3.5.0 and below
* MLS logo sizing on maps

= 1.5.1 =
* Maintenance
* Branding logo sizing

= 1.5.0 =
* Introducing maps for Listing Grid and Property List components.
* Adding an option to city searches for exact or like search behavior to Listing Grid and Property List components.

= 1.4.2 =
* Fixing minor bug impacting sites running specific plugins.

= 1.4.1 =
* Fixing session management bug causing issues with Search Manager.

= 1.4.0 =
* Adding CSS Editor page.
* Implementing jsonp for pagination so that pagniated components can be displayed as part of header/footer content wrapping search solution on mlsfinder servers.

= 1.3.19 =
* Fixing issue with grid columns when paginating.
* Fixing bug with special characters in search parameters.
* Adding WordPress version number to all API request URLs. This is for user metrics data.
* Performance improvement with widget page in the admin. Now caching saveSearches query in Request scope so that it is only requested once per page request.

= 1.3.18 =
* Fixing bug with generated header and footer files using legacy URL structure.
* Fixing issue with widget option forms

= 1.3.17 =
* Added maximum transient expiration date to prevent unnecessarily stale data from congesting the database.
* Added an activation hook which removes legacy transient data from older plugin versions.
* Added a deactivation hook which removes transient data to clean the database if the plugin is disabled.

= 1.3.16 =
* Major re-architecture of the plugin code.
    * Removed third-party PHP libraries and Framework code.
    * Consolidated most code into wolfnet.php file.
* Updated Ajax requests to use built in WordPress Ajax hooks.
* Changed plugin URI to WordPress.org location
* Fixed CSS issue with shortcode builder dialog window on WordPress 3.6

= 1.3.15 =
* Updating pagination URL to be relative to the root of the site rather than the current page.

= 1.3.14 =
* Updates to resolve issues with BrandCo theme architecture.
* Updated to new version of WPPF which supports hook arguments.

= 1.3.13 =
* Removing custom cursor on featured listings as they are not supported well in IE.
* Removing some code that was not intended for release.

= 1.3.12 =
* Fixed minor bug creating inconsistency between initial widget output and paged results.

= 1.3.11 =
* Added SEO support for pagination. Pages can now be viewed even when JavaScript is unavailable/disabled.
* Updated the order of address information and added postal code.

= 1.3.10 =
* Fixing minor bug causing scripts to be included more than once on search solutions.

= 1.3.9 =
* Fixed a similar issue to that in 1.3.8 which was impacting the search manager and the key validation.

= 1.3.8 =
* Fixed issue with shortcode builder causing problems with multi-site WordPress installs.

= 1.3.7 =
* Fixed a bug in the "toolbar" JavaScript which caused HTML content to be escaped.
* Restructured parts of the "toolbar" JavaScript for performance.

= 1.3.6 =
* Fixed minor JavaScript bug.

= 1.3.5 =
* Updated to new version of WPPF with caching strategy that uses the WordPress Transient API instead of flat files.

= 1.3.4 =
* Fixed bug with HTTP User-Agents getting caught by MLSFinder mobile browser detection.

= 1.3.3 =
* Updated styles and some JavaScript improve appearance of controls.
* Implemented option to enable sort options. It is now disabled by default.

= 1.3.0 =
* Fixing bug with search manager saving and deleting functionality.
* Updated API calls to explicitly include .json file type.
* Updated wppf code and other service code to avoid caching data if there is a server side error.
* Added WNT class to the HTML tag on dynamic content pages, for easier styling integration with MLSFinder search solutions.
* Updated to perform ajax key validation against plugin exposed endpoint rather than calling the WolfNet API directly.
* Increased timeout for remote call to 3 minutes.
* Re-wrote shortcode builder JavaScript
* Removed a couple styles which were causing issues in IE7.
* Introduced pagination feature to Listing Grid and Property List.

= 1.1.3 =
* Fixed a bug preventing the shortcode builder from working correctly when there is more than one instance of TinyMCE on the page at a time.

= 1.1.3 =
* Updated to ensure that dynamic pages created by the plugin return the correct status code.

= 1.1.2 =
* Fixed bug with shortcode builder introduced in version 1.1.0.

= 1.1.1 =
* Updated all plugin specific CSS classes to make sure they are prefixed to avoid conflicts.

= 1.1.0 =
* Exposed dynamic URLs which can be used to retrieve the header and footer of the WordPress site for use in wrapper a MLSFinder sub-domain search solution.

= 1.0.12 =
* Fixing bug with & special character in query strings.
* Removed some debug code.

= 1.0.11 =
* Fixing bug that was already fixed but reverted some how.

= 1.0.10 =
* Added regular expression replacement to remove included jQuery source from search builder code. This fixes a bug caused by jQuery being included more than once.
* Fixed CSS issue with Property List causing price and address to be on different lines in some browsers.
* Added the following WordPress filters which will effect the display for all listing displays and instances of the quick search:
    * wolfnet_listingView_id
    * wolfnet_listingView_url
    * wolfnet_listingView_address
    * wolfnet_listingView_address_full
    * wolfnet_listingView_image
    * wolfnet_listingView_price
    * wolfnet_listingView_location
    * wolfnet_listingView_fullLocation
    * wolfnet_listingView_bedbath
    * wolfnet_listingView_bedbath_full
    * wolfnet_listingView_branding_brokerLogo
    * wolfnet_listingView_branding_content
    * wolfnet_listingView_listing_class
    * wolfnet_quickSearchView_formAction
* Switched framework code to newly re-branded GreenTie Development code.

= 1.0.9 =
* Fixed bug preventing drop down lists from populating in QuickSearch widget.

= 1.0.8 =
* Updated QuickSearch view to use new site_base_url method. Fixing bug preventing form action from being populated.

= 1.0.7 =
* Updated WPPF code to v1.1.6. Fixed a bug created in version 1.1.5 affecting sites running older version of PHP ```(<5.3)```.

= 1.0.6 =
* Updating WPPF code to v1.1.5
* Added method to retrieve only site_base_url settings.
* Updated search service to build search manager URL a little more intelligently.
* Updated styles on the search manager page to more closely match the default WordPress admin styles.
* Added placeholder text to search manager save field.
* Moved search manager JavaScript into a self contained jQuery plugin.

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

= 1.0.4 =
* Fixed bug with jQuery datepicker in Search Manager.

= 1.0.3 =
* Fixed some minor bugs based on initial QA feedback.
* Fixed some PHP warnings and notices.

= 1.0.2 =
* Fixed bug preventing original Grid parameters from working correctly.

= 1.0.1 =
* Updated hard-coded URI in admin JS.
* Adding placeholder content for support page.
* Moved search builder HTTP call to service and added support for cfid and cftoken in mlsfinder URLs.
* Fixed JavaScript compatibility issue with date by moving date formating into the back-end.
* Fixing minor bug preventing "more info" tool tips from being displayed in widget forms.
* Fixing minor bug causing ** DELETED ** item to be displayed on new widget instances.
* Fixed JavaScript for property list widgets
* Fixed some bugs with IE
* Fixed bug causing URLs with no trailing slash to break ajax requests.
* Fixed minor bug with Abs/Rel paths.

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

= 0.1.2 =
* Implemented new version of WPPF which fixed some HTTP web service issues.

= 0.1.1 =
* Fixed CSS issue causing a hidden overlapping element to interfere with other elements on the page.

= 0.1.0 =
* Initial beta release for limited distribution.


== Upgrade Notice ==
