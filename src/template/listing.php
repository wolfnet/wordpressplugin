<?php

/**
 *
 * @title         listing.php
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

?>

<div id="wolfnet_listing_<?php echo $listing['property_id']; ?>" class="wolfnet_listing" itemscope="itemscope">
    <a class="wolfnet_listingLink" href="<?php echo $listing['property_url']; ?>" rel="follow">
        <div class="wolfnet_listingMain">
            <div class="wolfnet_listingHead">
                <span class="wolfnet_listingImage">
                    <img src="<?php echo $listing['thumbnail_url']; ?>"
                     alt="Property for sale at <?php echo $listing['address']; ?>"
                     data-photo-url="<?php echo $listing['thumbnails_url']; ?>" />
                </span>
                <div class="wolfnet_listingInfo">
                    <span class="wolfnet_price" title="<?php echo htmlspecialchars($listing['listing_price']); ?>" itemprop="price">
                        <?php echo $listing['listing_price']; ?>
                    </span>
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
                    <div class="wolfnet_detailsLink"></div>
                </div>
            </div>
        </div>
        <div class="wolfnet_locationInfo" title="<?php echo htmlspecialchars($listing['address']); ?>">
            <div class="wolfnet_address">
                <?php echo $listing['display_address']; ?>
            </div>
            <div class="wolfnet_location" itemprop="locality">
                <?php echo $listing['location']; ?>
            </div>
            <div class="wolfnet_full_address" itemprop="street-address" style="display: none;">
                <?php echo $listing['address']; ?>
            </div>
        </div>
        <div class="wolfnet_branding">
            <span class="wolfnet_brokerLogo<?php echo ($listing['branding']['type']=='idx') ? ' wolfnet_idxLogo' : ''; ?>"
             <?php if (trim($listing['branding']['logo']) == '') { ?>style="display: none;"<?php } ?>>
                <img src="<?php echo $listing['branding']['logo']; ?>" />
            </span>
            <span class="wolfnet_brandingMessage">
                <span class="wolfnet_brandingCourtesyText">
                    <?php echo $listing['branding']['courtesy_text']; ?>
                </span>
                <span class="wolfnet_brandingAgent wolfnet_brandingAgentName">
                    <?php echo $listing['branding']['agent_name']; ?>
                </span>
                <span class="wolfnet_brandingAgent wolfnet_brandingAgentPhone">
                    <?php echo $listing['branding']['agent_phone']; ?>
                </span>
                <span class="wolfnet_brandingOffice wolfnet_brandingOfficeName">
                    <?php echo $listing['branding']['office_name']; ?>
                </span>
                <span class="wolfnet_brandingOffice wolfnet_brandingOfficePhone">
                    <?php echo $listing['branding']['office_phone']; ?>
                </span>
                <span class="wolfnet_brandingTollFreePhone">
                    <?php echo $listing['branding']['toll_free_phone']; ?>
                </span>
            </span>
        </div>
    </a>
</div>
