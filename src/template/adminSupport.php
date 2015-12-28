<?php

/**
 *
 * @title         adminSupport.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

function wolfnet_print_thumbnail($img, $imgdir)
{
    $url = $imgdir . $img;
    echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="wolfnet_thumbnail" /></a>';
}

?>
<div id="wolfnet_support_page" class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br/></div>

    <h2>WolfNet <sup>&reg;</sup> - Plugin Support</h2>

    <p>
        Please contact WolfNet Technologies via phone at 612-342-0088 or toll-free at 1-866-WOLFNET,
        or via email at <a href="mailto:service@wolfnet.com">service@wolfnet.com</a>.
        You may also find us online at <a href="http://wolfnet.com" target="_blank">WolfNet.com</a>.
    </p>

    <div id="wolfnet_support_content">

        <h3>Table of Contents</h3>

        <ol>
            <li><a href="#activation">Activating your Plugin</a></li>
            <li><a href="#overview">Overview</a>
                <!--
                <ol>
                    <li><a href="#overview-featured-listings">WolfNet Featured Listings</a></li>
                    <li><a href="#overview-property-list">WolfNet Property List</a></li>
                    <li><a href="#overview-listing-grid">WolfNet Listing Grid</a></li>
                    <li><a href="#overview-quick-search">WolfNet Quick Search</a></li>
                    <li><a href="#overview-search-manager">Search Manager</a></li>
                </ol>
                -->
            </li>
            <li><a href="#search-manager">Using the Search Manager</a></li>
            <li><a href="#widgets">Widgets</a>
                <ol>
                    <li><a href="#widgets-add">Adding a Widget</a></li>
                    <li><a href="#widgets-configure">Configuring a Widget</a>
                        <ol>
                            <li><a href="#widgets-configure-featured">WolfNet Featured Listings Widget</a></li>
                            <li><a href="#widgets-configure-list">WolfNet Property List Widget</a></li>
                            <li><a href="#widgets-configure-grid">WolfNet Listing Grid Widget</a></li>
                            <li><a href="#widgets-configure-search">WolfNet Quick Search Widget</a></li>
                        </ol>
                    </li>
                    <li><a href="#widgets-saving">Saving Widget Configuration</a></li>
                </ol>
            </li>
            <li><a href="#shortcodes">Shortcodes</a>
                <ol>
                    <li><a href="#shortcodes-configure">Configuring Shortcodes</a>
                        <ol>
                            <li><a href="#shortcodes-configure-featured">WolfNet Featured Listings</a></li>
                            <li><a href="#shortcodes-configure-list">WolfNet Property List</a></li>
                            <li><a href="#shortcodes-configure-grid">WolfNet Listing Grid</a></li>
                            <li><a href="#shortcodes-configure-search">WolfNet Quick Search</a></li>
                        </ol>
                    </li>
                    <li><a href="#shortcodes-create">Publishing a Post or Page</a></li>
                    <li><a href="#shortcodes-edit">Editing a Post or Page</a></li>
                </ol>
            </li>
        </ol>

        <div id="activation">

            <h3>Activating your Plugin</h3>

            <p>To integrate WolfNet IDX data with your WordPress website, you must have the following:</p>

            <ol>

                <li>A WolfNet IDX property search solution – if you do not have a WolfNet IDX property search solution, please contact WolfNet Sales via phone at 612-342-0088 or toll free at 1-866-WOLFNET, or via email at <a href="mailto:sales@wolfnet.com">sales@wolfnet.com</a>.</li>

                <li>A unique plugin product key – you must contact WolfNet Technologies to obtain your unique plugin product key.  Please contact WolfNet Customer Service via phone at 612-342-0088 or toll free at 1-866-WOLFNET, or via email at <a href="mailto:service@wolfnet.com">service@wolfnet.com</a>.</li>

            </ol>

            <p>To activate your WolfNet IDX for WordPress plugin, your unique plugin product key must be entered and validated within the WolfNet section of your WordPress dashboard. In the left navigation, choose WolfNet &gt; General Settings. Enter your product key and Save Changes. A green checkmark indicates that your product key has been validated.  A red X indicates that your product key is invalid.</p>

            <?php wolfnet_print_thumbnail( 'support-product-key.png', $imgdir ); ?>

        </div>

        <div id="overview">

            <h3>Overview</h3>

            <p>The WolfNet IDX for WordPress Plugin features several options to integrate your WolfNet IDX property search into your WordPress website, either as a widget or via shortcode.</p>

            <ol>

                <li>
                    <strong>WolfNet Featured Listings</strong> can be used to add a scrollable display of your featured properties.  The featured listings display consists of an image, price, number of bedrooms, number of bathrooms, and address information.
                    <?php wolfnet_print_thumbnail( 'support-featured-listings.png', $imgdir ); ?>
                </li>

                <li>
                    <strong>WolfNet Property List</strong> can be used to display a text list of properties based on defined criteria.  The property list display consists of address and price information.
                    <?php wolfnet_print_thumbnail( 'support-list.png', $imgdir ); ?>
                </li>

                <li>
                    <strong>WolfNet Listing Grid</strong> can be used to display a grid of properties based on defined criteria.  The grid display consists of an image, price, number of bedrooms, number of bathrooms, and address information.
                    <?php wolfnet_print_thumbnail( 'support-grid.png', $imgdir ); ?>
                </li>

                <li>
                    <strong>WolfNet Quick Search</strong> can be used to add a quick search form to your website.  When a quick search is executed, the user is directed to the applicable search results within your WolfNet IDX property search solution.  The quick search form includes location, listing number, price, number of bedrooms, and number of bathrooms search options.
                    <?php wolfnet_print_thumbnail( 'support-quicksearch.png', $imgdir ); ?>
                </li>

                <li>
                    <strong>Search Manager</strong> can be used to pre-define criteria to return a specific subset of properties.  This feature is comparable to the URL Search Builder feature that is available in your WolfNet BackOffice.
                    <?php wolfnet_print_thumbnail( 'support-search-manager.png', $imgdir ); ?>
                </li>

            </ol>

        </div>

        <div id="search-manager">

            <h3>Using the Search Manager</h3>

            <p>The Search Manager feature can be found within the WolfNet section of your WordPress dashboard. In the left navigation, choose WolfNet &gt; Search Manager.</p>

            <p>The Search Manager interface features a replica of your WolfNet IDX property search solution, including all search features that are available on your actual property search solution. You can select criteria as if you were executing a search on your actual property search solution. Once satisfied with the criteria selected, you can name and save the criteria. The saved search criteria can then be applied to a widget or as shortcode within a page or post.</p>

            <?php wolfnet_print_thumbnail( 'support-search-manager.png', $imgdir ); ?>

        </div>

        <div id="widgets">

            <h3>Widgets</h3>

            <div id="widgets-add">

                <h4>Adding a Widget</h4>

                <p>Widgets are added via your WordPress dashboard. In the left navigation of your WordPress control panel, choose Appearance &gt; Widgets.  All available widgets are displayed in the center of the page.</p>

                <p>Widgets can be added to a number of designated places on your website based on the theme chosen for your website. The locations where widgets can be added are indicated on the right side of the Appearance > Widgets control panel.</p>

                <p>To add a widget, simply click and drag the widget to the desired location.</p>

                <?php wolfnet_print_thumbnail( 'support-widgets.png', $imgdir ); ?>

            </div>

            <div id="widgets-configure">

                <h4>Configuring a Widget</h4>

                <p>To configure a widget that you have added to your website, click the down arrow to the right of the name of the widget.</p>

                <?php wolfnet_print_thumbnail( 'support-widgets-config.png', $imgdir ); ?>

            </div>

            <div id="widgets-configure-featured">

                <h5>WolfNet Featured Listings Widget</h5>

                <p>The WolfNet Featured Listings widget offers the following configuration options.</p>

                <ol>

                    <li>
                        <strong>Title</strong> – the title displays above the featured properties. <em>NOTE: the inclusion of the title depends on the theme chosen for your WordPress website.</em>
                        <?php wolfnet_print_thumbnail( 'support-featured-listings-title.png', $imgdir ); ?>
                    </li>

                    <li>

                        <strong>Scroll Control</strong> – this setting allows you to establish how your website visitors can control the scrolling animation of the featured properties.

                        <ol>

                            <li><strong>Automatic & Manual</strong> – featured properties scroll upon page load, but the user can override the animation via previous and next controls displayed on hover.</li>

                            <li><strong>Manual Only</strong> – the user must activate scrolling animation via the previous and next controls display on hover.</li>

                        </ol>

                    </li>

                    <li>

                        <strong>Automatic Playback Options</strong> – these settings allow you to configure the scrolling animation and only display when the Scroll Control field is set to Automatic &amp; Manual.

                        <ol>

                            <li><strong>Direction</strong> – choose the direction the featured properties scroll (right to left or left to right).</li>

                            <li><strong>Animation Speed</strong> – choose how fast or slow the featured properties scroll. A value between 1 and 99 can be entered; the higher the number, the slower the speed.</li>

                        </ol>

                    </li>

                    <li>

                        <strong>Agent/Broker</strong> – this setting allows you to establish which properties are featured. <em>NOTE: the display of properties via this feature is tied to the MLS IDs entered in the Contact Information section of your WolfNet BackOffice. These IDs must be accurate in order for properties to display.</em>

                        <ol>

                            <li><strong>Agent Then Broker</strong> – properties associated with the agent MLS ID display first, followed by properties associated with the office MLS ID. This option is intended for agents.</li>

                            <li><strong>Agent Only</strong> – only properties associated with the agent MLS ID display. This option is intended for agents.</li>

                            <li><strong>Broker Only</strong> – only properties associated with the office MLS ID display. This option is intended for brokers.</li>

                        </ol>

                    </li>

                    <li><strong>Max Results</strong> – this option allows you to choose how many properties are displayed. The maximum number of properties that can be displayed is 50.</li>

                </ol>

            </div>

            <div id="widgets-configure-list">

                <h5>WolfNet Property List Widget</h5>

                <p>The WolfNet Property List widget offers the following configuration options.</p>

                <ol>

                    <li>
                        <strong>Title</strong> – the title displays above the property list. <em>NOTE: the inclusion of the title depends on the theme chosen for your WordPress website.</em>
                        <?php wolfnet_print_thumbnail( 'support-list-title.png', $imgdir ); ?>
                    </li>

                    <li>

                        <strong>Mode</strong> – this setting allows you to choose how you define criteria for the properties that display in the list.

                        <ol>

                            <li><strong>Basic</strong> – when selected, a limited set of fields are available to modify, including Price, City and Zip Code.</li>

                            <li><strong>Advanced</strong> – when selected, choose a saved search with pre-defined criteria created via the Search Manager.</li>

                        </ol>

                    </li>

                    <li>

                        <strong>Agent/Broker</strong> – this setting allows you to filter the properties that are displayed. <em>NOTE: the display of properties via this feature is tied to the MLS IDs entered in the Contact Information section of your WolfNet BackOffice. These IDs must be accurate in order for properties to display.</em>

                        <ol>

                            <li><strong>All</strong> – all matching properties display, regardless of the listing brokerage and/or agent.</li>

                            <li><strong>Agent Then Broker</strong> – properties associated with the agent MLS ID display first, followed by properties associated with the office MLS ID. This option is intended for agents.</li>

                            <li><strong>Agent Only</strong> – only properties associated with the agent MLS ID display. This option is intended for agents.</li>

                            <li><strong>Broker Only</strong> – only properties associated with the office MLS ID display. This option is intended for brokers.</li>

                        </ol>

                    </li>

                    <li><strong>Max Results</strong> – this option allows you to choose how many properties are displayed. The maximum number of properties that can be displayed is 50.</li>

                </ol>

            </div>

            <div id="widgets-configure-grid">

                <h5>WolfNet Listing Grid Widget</h5>

                <p>The WolfNet Listing Grid widget offers the following configuration options.</p>

                <ol>

                    <li>

                        <strong>Title</strong> – the title displays above the properties. <em>NOTE: the inclusion of the title depends on the theme chosen for your WordPress website.</em>

                        <?php wolfnet_print_thumbnail( 'support-grid-title.png', $imgdir ); ?>

                    </li>

                    <li>

                        <strong>Mode</strong> – this setting allows you to choose how you define criteria for the properties that display in the grid.

                        <ol>

                            <li><strong>Basic</strong> – when selected, a limited set of fields are available to modify, including Price, City and Zip Code.</li>

                            <li><strong>Advanced</strong> – when selected, choose a saved search with pre-defined criteria created via the Search Manager.</li>

                        </ol>

                    <li>

                        <strong>Agent/Broker</strong> – this setting allows you to filter the properties that are displayed. <em>NOTE: the display of properties via this feature is tied to the MLS IDs entered in the Contact Information section of your WolfNet BackOffice. These IDs must be accurate in order for properties to display.</em></li>

                        <ol>

                            <li><strong>All</strong> – all matching properties display, regardless of the listing brokerage and/or agent.</li>

                            <li><strong>Agent Then Broker</strong> – properties associated with the agent MLS ID display first, followed by properties associated with the office MLS ID. This option is intended for agents.</li>

                            <li><strong>Agent Only</strong> – only properties associated with the agent MLS ID display. This option is intended for agents.</li>

                            <li><strong>Broker Only</strong> – only properties associated with the office MLS ID display. This option is intended for brokers.</li>

                        </ol>

                    <li><strong>Max Results</strong> – this option allows you to choose how many properties are displayed. The maximum number of properties that can be displayed is 50.</li>

                </ol>

            </div>

            <div id="widgets-configure-search">

                <h5>WolfNet Quick Search Widget</h5>

                <p>The WolfNet Quick Search widget offers the following configuration options.</p>

                <ol>

                    <li>

                        <strong>Title</strong> – the title displays above the quick search form.

                        <?php wolfnet_print_thumbnail( 'support-quicksearch-title.png', $imgdir ); ?>

                    </li>

                </ol>

            </div>

            <div id="widgets-saving">

                <h4>Saving Widget Configuration</h3>

                <p>Once a widget has been configured, settings can be saved via the Save call to action in the bottom right corner.</p>

                <?php wolfnet_print_thumbnail( 'support-widget-save.png', $imgdir ); ?>

            </div>

        </div>

        <div id="shortcodes">

            <h3>Shortcodes</h3>

            <p>A shortcode is a quick way to insert content into a post or page within your WordPress website. Posts and pages are added via your WordPress dashboard.</p>

            <p>To add a post, in the left navigation of your WordPress control panel, choose Posts &gt; Add New. An editor appears where a title and content can be created. The editor includes a WolfNet <span class="wolfnet_pawicon">paw print icon</span> that allows you to quickly and easily choose a WolfNet plugin feature to insert.</p>

            <p>Posts appear in designated places on your website based on the theme chosen for your website.</p>

            <?php wolfnet_print_thumbnail( 'support-post-edit.png', $imgdir ); ?>

            <p>To add a page, in the left navigation of your WordPress control panel, choose Pages &gt; Add New. An editor appears where a title and content can be created. The editor includes a WolfNet <span class="wolfnet_pawicon">paw print icon</span> that allows you to quickly and easily choose a WolfNet plugin feature to insert.</p>

            <p>Page navigation appears in a designated location on your website based on the theme chosen for your website.</p>

            <?php wolfnet_print_thumbnail( 'support-page-edit.png', $imgdir ); ?>

            <p>When the WolfNet <span class="wolfnet_pawicon">paw icon</span> is selected, a window displays with the available features that can be inserted into the post or page.</p>

            <?php wolfnet_print_thumbnail( 'support-shortcode-builder.png', $imgdir ); ?>

            <p>Once a WolfNet plugin feature is selected (and configured – see the Configuring Shortcodes section below), the applicable shortcode is dropped directly into the editor on the page.</p>

            <?php wolfnet_print_thumbnail( 'support-shortcode.png', $imgdir ); ?>

            <div id="shortcodes-configure">

                <h4>Configuring Shortcodes</h4>

                <p>When working within a post or page editor, once you select the WolfNet <span class="wolfnet_pawicon">paw icon</span> and choose a feature to insert, you are presented with options to configure the feature.</p>

                <p>Once a feature has been configured, settings can be saved via the Insert Shortcode call to action.</p>

                <?php wolfnet_print_thumbnail( 'support-shortcode-builder2.png', $imgdir ); ?>

                <div id="shortcodes-configure-featured">

                    <h5>WolfNet Featured Listings</h5>

                    <p>The WolfNet Featured Listings feature offers the following configuration options.</p>

                    <ol>

                        <li>

                            <strong>Title</strong> – the title displays above the featured properties. <em>NOTE: the inclusion of the title depends on the theme chosen for your WordPress website.</em>

                            <?php wolfnet_print_thumbnail( 'support-featured-listing-title2.png', $imgdir ); ?>

                        </li>

                        <li>

                            <strong>Scroll Control</strong> – this setting allows you to establish how your website visitors can control the scrolling animation of the featured properties.

                            <ol>

                                <li><strong>Automatic & Manual</strong> – featured properties scroll upon page load, but the user can override the animation via previous and next controls displayed on hover.</li>

                                <li><strong>Manual Only</strong> – the user must activate scrolling animation via the previous and next controls display on hover.</li>

                            </ol>

                        </li>

                        <li>

                            <strong>Automatic Playback Options</strong> – these settings allow you to configure the scrolling animation and only display when the Scroll Control field is set to Automatic &amp; Manual.

                            <ol>

                                <li><strong>Direction</strong> – choose the direction the featured properties scroll (right to left or left to right).</li>

                                <li><strong>Animation Speed</strong> – choose how fast or slow the featured properties scroll.  A value between 1 and 99 can be entered; the higher the number, the slower the speed.</li>

                            </ol>

                        <li>

                            <strong>Agent/Broker</strong> – this setting allows you to establish which properties are featured. NOTE: the display of properties via this feature is tied to the MLS IDs entered in the Contact Information section of your WolfNet BackOffice. These IDs must be accurate in order for properties to display.

                            <ol>

                                <li><strong>Agent Then Broker</strong> – properties associated with the agent MLS ID display first, followed by properties associated with the office MLS ID. This option is intended for agents.</li>

                                <li><strong>Agent Only</strong> – only properties associated with the agent MLS ID display. This option is intended for agents.</li>

                                <li><strong>Broker Only</strong> – only properties associated with the office MLS ID display. This option is intended for brokers.</li>

                            </ol>

                        <li><strong>Max Results</strong> – this option allows you to choose how many properties are displayed. The maximum number of properties that can be displayed is 50.</li>

                    </ol>

                </div>

                <div id="shortcodes-configure-list">

                    <h5>WolfNet Property List</h5>

                    <p>The WolfNet Property List feature offers the following configuration options.</p>

                    <ol>

                        <li>

                            <strong>Title</strong> – the title displays above the property list. <em>NOTE: the inclusion of the title depends on the theme chosen for your WordPress website.</em>

                            <?php wolfnet_print_thumbnail( 'support-list-title2.png', $imgdir ); ?>

                        </li>

                        <li>

                            <strong>Mode</strong> – this setting allows you to choose how you define criteria for the properties that display in the list.

                            <ol>

                                <li><strong>Basic</strong> – when selected, a limited set of fields are available to modify, including Price, City and Zip Code.</li>

                                <li><strong>Advanced</strong> – when selected, choose a saved search with pre-defined criteria created via the Search Manager.</li>

                            </ol>

                        </li>

                        <li>

                            <strong>Agent/Broker</strong> – this setting allows you to filter the properties that are displayed. NOTE: the display of properties via this feature is tied to the MLS IDs entered in the Contact Information section of your WolfNet BackOffice. These IDs must be accurate in order for properties to display.

                            <ol>

                                <li><strong>All</strong> – all matching properties display, regardless of the listing brokerage and/or agent.</li>

                                <li><strong>Agent Then Broker</strong> – properties associated with the agent MLS ID display first, followed by properties associated with the office MLS ID. This option is intended for agents.</li>

                                <li><strong>Agent Only</strong> – only properties associated with the agent MLS ID display. This option is intended for agents.</li>

                                <li><strong>Broker Only</strong> – only properties associated with the office MLS ID display. This option is intended for brokers.</li>

                            </ol>

                        </li>

                        <li><strong>Max Results</strong> – this option allows you to choose how many properties are displayed. The maximum number of properties that can be displayed is 50.</li>

                    </ol>

                </div>

                <div id="shortcodes-configure-grid">

                    <h5>WolfNet Listing Grid</h5>

                    <p>The WolfNet Listing Grid feature offers the following configuration options.</p>

                    <ol>

                        <li>

                            <strong>Title</strong> – the title displays above the properties. <em>NOTE: the inclusion of the title depends on the theme chosen for your WordPress website.</em>

                            <?php wolfnet_print_thumbnail( 'support-grid-title2.png', $imgdir ); ?>

                        </li>

                        <li>

                            <strong>Mode</strong> – this setting allows you to choose how you define criteria for the properties that display in the grid.

                            <ol>

                                <li><strong>Basic</strong> – when selected, a limited set of fields are available to modify, including Price, City and Zip Code.</li>

                                <li><strong>Advanced</strong> – when selected, choose a saved search with pre-defined criteria created via the Search Manager.</li>

                            </ol>

                        </li>

                        <li>

                            <strong>Agent/Broker</strong> – this setting allows you to filter the properties that are displayed. <em>NOTE: the display of properties via this feature is tied to the MLS IDs entered in the Contact Information section of your WolfNet BackOffice. These IDs must be accurate in order for properties to display.</em>

                            <ol>

                                <li><strong>All</strong> – all matching properties display, regardless of the listing brokerage and/or agent.</li>

                                <li><strong>Agent Then Broker</strong> – properties associated with the agent MLS ID display first, followed by properties associated with the office MLS ID. This option is intended for agents.</li>

                                <li><strong>Agent Only</strong> – only properties associated with the agent MLS ID display. This option is intended for agents.</li>

                                <li><strong>Broker Only</strong> – only properties associated with the office MLS ID display. This option is intended for brokers.</li>

                            </ol>

                        </li>

                        <li><strong>Max Results</strong> – this option allows you to choose how many properties are displayed. The maximum number of properties that can be displayed is 50.</li>

                    </ol>

                </div>

                <div id="shortcodes-configure-search">

                    <h5>WolfNet Quick Search</h5>

                    <p>The WolfNet Quick Search feature offers the following configuration options.</p>

                    <ol>

                        <li>

                            <strong>Title</strong> – the title displays above the quick search form.

                            <?php wolfnet_print_thumbnail( 'support-quicksearch-title2.png', $imgdir ); ?>

                        </li>

                    </ol>

                </div>

            </div>

            <div id="shortcodes-create">

                <h4>Publishing a Post or Page</h4>

                <p>A post or page can be published directly from the Add post/page form. Once published, the post or page is added to your WordPress website in real time.</p>

                <?php wolfnet_print_thumbnail( 'support-post-edit2.png', $imgdir ); ?>

                <?php wolfnet_print_thumbnail( 'support-posts.png', $imgdir ); ?>

            </div>

            <div id="shortcodes-edit">

                <h4>Editing a Post or Page</h4>

                <p>A post or page can be edited at any time via the Edit option within the Posts and Pages section of your WordPress dashboard. To publish edits to your WordPress website, select the Update call to action. Once published, the post or page is updated on your WordPress website in real time.</p>

                <?php wolfnet_print_thumbnail( 'support-publish-page.png', $imgdir ); ?>

            </div>

        </div>

    </div>

</div>
