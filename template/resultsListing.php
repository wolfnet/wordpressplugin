<?php

/**
 *
 * @title         resultsListing.php
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

<div id="wolfnet_listing_<?php echo $listing->property_id; ?>" class="wolfnet_listing" itemscope>
    <table>
        <tr>
            <td>
                <a href="<?php echo $listing->property_url; ?>" rel="follow">
                    <span class="wolfnet_listingImage"><img src="<?php echo $listing->photo_url; ?>" alt="Property for sale at <?php echo $listing->address; ?>" /></span>
                </a>
            </td>
            <td>
                <span class="wolfnet_price" itemprop="price"><?php echo $listing->listing_price; ?></span>
                <span class="wolfnet_address"><?php echo $listing->display_address; ?></span>
                <span class="wolfnet_location"><?php echo $listing->location; ?></span>    
                <span class="wolfnet_bedsbath"><?php echo $listing->bedsbaths; ?></span>    
                <?php if (property_exists($listing, 'branding') && ($listing->branding->brokerLogo != '' || $listing->branding->content != '')) { ?>
                <div span="wolfnet_branding">
                    <?php if (trim($listing->branding->brokerLogo) !== '') { ?>
                        <span class="wolfnet_brokerLogo"><img src="<?php echo $listing->branding->brokerLogo; ?>" /></span>
                    <?php } ?>
                    <span class="wolfnet_brandingMessage"><?php echo $listing->branding->content; ?></span>
                </div>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>
