<?php

/**
 *
 * @title         listing.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
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

<div id="wolfnet_listing_<?php echo $listing['property_id']; ?>" class="wolfnet_listing" itemscope>
    <a href="<?php echo $listing['property_url']; ?>" rel="follow">
        <div class="wolfnet_listingMain">
            <div class="wolfnet_listingHead">
                <span class="wolfnet_listingImage">
                    <img src="<?php echo $listing['thumbnail_url']; ?>" alt="Property for sale at <?php echo $listing['address']; ?>" />
                </span>
                <div class="wolfnet_listingInfo">
                    <span class="wolfnet_price" title="<?php echo htmlspecialchars($listing['listing_price']); ?>" itemprop="price">
                        <?php echo $listing['listing_price']; ?>
                    </span>
                    <?php if (trim($listing['total_bedrooms']) || trim($listing['total_baths'])) { ?>
                        <span class="wolfnet_bed_bath" title="<?php echo htmlspecialchars($listing['bedsbaths_full']); ?>">
                            <?php if (trim($listing['total_bedrooms'])) { ?>
                                <span class="wolfnet_beds">
                                    <?php echo $listing['total_bedrooms']; ?>
                                    <span class="wolfnet_label">Bedrooms</span>
                                </span>
                                <?php if (trim($listing['total_baths'])) { ?>
                                    <span class="wolfnet_info_separator"></span>
                                <?php } ?>
                            <?php } ?>
                            <?php if (trim($listing['total_baths'])) { ?>
                                <span class="wolfnet_baths">
                                    <?php echo $listing['total_baths']; ?>
                                    <span class="wolfnet_label">Bathrooms</span>
                                </span>
                            <?php } ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <span class="wolfnet_locationInfo" title="<?php echo htmlspecialchars($listing['address']); ?>">
            <span class="wolfnet_address">
                <?php echo $listing['display_address']; ?>
            </span>
            <span class="wolfnet_location" itemprop="locality">
                <?php echo $listing['location']; ?>
            </span>
            <span class="wolfnet_full_address" itemprop="street-address" style="display: none;">
                <?php echo $listing['address']; ?>
            </span>
        </span>
        <div class="wolfnet_branding">
            <?php if (trim($listing['branding']['logo']) !== '') { ?>
                <span class="wolfnet_brokerLogo<?php echo ($listing['branding']['type']=='idx') ? ' wolfnet_idxLogo' : ''; ?>">
                    <img src="<?php echo $listing['branding']['logo']; ?>" />
                </span>
            <?php } ?>
            <span class="wolfnet_brandingMessage">
                <?php if (trim($listing['branding']['courtesy_text']) !== '') { ?>
                    <span class="wolfnet_brandingCourtesyText">
                        <?php echo $listing['branding']['courtesy_text']; ?>
                    </span>
                <?php } ?>
                <?php if (trim($listing['branding']['agent_name']) !== '') { ?>
                    <span class="wolfnet_brandingAgent wolfnet_brandingAgentName">
                        <?php echo $listing['branding']['agent_name']; ?>
                    </span>
                <?php } ?>
                <?php if (trim($listing['branding']['agent_phone']) !== '') { ?>
                    <span class="wolfnet_brandingAgent wolfnet_brandingAgentPhone">
                        <?php echo $listing['branding']['agent_phone']; ?>
                    </span>
                <?php } ?>
                <?php if (trim($listing['branding']['office_name']) !== '') { ?>
                    <span class="wolfnet_brandingOffice wolfnet_brandingOfficeName">
                        <?php echo $listing['branding']['office_name']; ?>
                    </span>
                <?php } ?>
                <?php if (trim($listing['branding']['office_phone']) !== '') { ?>
                    <span class="wolfnet_brandingOffice wolfnet_brandingOfficePhone">
                        <?php echo $listing['branding']['office_phone']; ?>
                    </span>
                <?php } ?>
                <?php if (trim($listing['branding']['toll_free_phone']) !== '') { ?>
                    <span class="wolfnet_brandingTollFreePhone">
                        <?php echo $listing['branding']['toll_free_phone']; ?>
                    </span>
                <?php } ?>
            </span>
        </div>
    </a>
</div>
