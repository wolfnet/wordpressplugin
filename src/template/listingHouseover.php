<?php

/**
 * @title         Wolfnet_Api.php
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


// this template renders the markup for map property info that shows up on mouse hover.
?>
    <div class="wolfnet_wntHouseOverWrapper">
        <a class="wolfnet_listingLink" href="<?php echo $listing['property_url']; ?>" rel="follow">
            <div data-property-id="<?php echo $listing['property_id'] ?>" class="wntHOItem">
                <table class="wolfnet_wntHOTable">
                    <tbody>
                        <tr>
                            <td class="wntHOImgCol">
                                <div class="wolfnet_wntHOImg wolfnet_listingImage">
                                    <img src="<?php echo $listing['thumbnail_url']; ?>" />
                                </div>
                                <div class="wolfnet_wntHOBroker wolfnet_brokerLogo"
                                 <?php if (trim($listing['branding']['logo']) == '') { ?>
                                    style="display: none;"
                                 <?php } ?>>
                                    <img src="<?php echo $listing['branding']['logo']; ?>" />
                                </div>
                            </td>
                            <td>
                                <div class="wolfnet_wntHOContentContainer">
                                    <div class="wolfnet_listingInfo">
                                        <div class="wolfnet_price">
                                            <?php echo $listing['listing_price']; ?>
                                        </div>
                                        <span class="wolfnet_bed_bath" title="<?php echo htmlspecialchars($listing['bedsbaths_full']); ?>">
                                            <span class="wolfnet_beds"
                                             <?php if (!trim($listing['total_bedrooms'])) { ?>style="display: none;"<?php } ?>>
                                                <?php echo $listing['total_bedrooms']; ?>
                                                <span class="wolfnet_label">Bedrooms</span>
                                            </span>
                                            <span class="wolfnet_info_separator"
                                             <?php if (!trim($listing['total_bedrooms']) || !trim($listing['total_bedrooms'])) { ?>
                                                style="display: none;"
                                             <?php } ?>></span>
                                            <span class="wolfnet_baths"
                                             <?php if (!trim($listing['total_baths'])) { ?>style="display: none;"<?php } ?>>
                                                <?php echo $listing['total_baths']; ?>
                                                <span class="wolfnet_label">Bathrooms</span>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="wolfnet_locationInfo" title="<?php echo htmlspecialchars($listing['address']); ?>">
                                        <div class="wolfnet_address">
                                            <?php echo $listing['display_address']; ?>
                                        </div>
                                        <div class="wolfnet_location">
                                            <?php echo $listing['city']; ?>, <?php echo $listing['state']; ?>
                                        </div>
                                    </div>
                                    <div class="wolfnet_branding" style="text-align: left; padding-top: 20px;">
                                        <span class="wolfnet_brandingMessage">
                                            <span class="wolfnet_brandingCourtesyText">
                                                <?php echo $listing['branding']['courtesy_text']; ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </a>
    </div>
