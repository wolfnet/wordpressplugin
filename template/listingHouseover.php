<?php

/**
 * @title         Wolfnet_Api.php
 * @copyright     Copyright (c) 2012-2014, WolfNet Technologies, LLC
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



error_log(print_r($listing, true));

// this template renders the markup for map property info that shows up on mouse hover.
if (!is_null($listing['geo']['lat']) && !is_null($listing['geo']['lng'])) : 
    ?>
    <a style="display:block" rel="follow" href="<?php echo $listing['property_url']; ?>">
    <div class="wolfnet_wntHouseOverWrapper">
    <div data-property-id="<?php echo $listing['property_id'] ?>" class="wntHOItem">
    <table class="wolfnet_wntHOTable">
    <tbody>
    <tr>
    <td class="wntHOImgCol" valign="top" style="vertical-align:top;">
    <div class="wolfnet_wntHOImg">
    <img src="<?php echo $listing['thumbnail_url']; ?>" style="max-height:100px;width:auto">
    </div>
    <?php if ($showBrokerImage) : ?>
        <div class="wolfnet_wntHOBroker" style="text-align: center">
        <img src="<?php echo $listing['branding']['logo']; ?>" style="max-height:50px;width:auto" alt="Broker Reciprocity">
        </div>
    <?php endif; ?>
    </td>
    <td valign="top" style="vertical-align:top;">
    <div class="wolfnet_wntHOContentContainer">
    <div style="text-align:left;font-weight:bold"><?php echo $listing['listing_price']; ?>
    </div>
    <div style="text-align:left;"><?php echo $listing['display_address']; ?>
    </div>
    <div style="text-align:left;"><?php echo $listing['city']; ?>, <?php echo $listing['state']; ?>
    </div>
    <div style="text-align:left;"><?php echo $listing['bedsbaths']; ?>
    </div>
    <div style="text-align:left;padding-top:20px;"><?php echo $listing['branding']['courtesy_text']; ?>
    </div>
    </div>
    </td>
    </tr>
    </tbody>
    </table>
    </div>
    </div>
    </a>
<?php endif; ?>
</td>
<td valign="top" style="vertical-align:top;">
<div class="wolfnet_wntHOContentContainer">
<div style="text-align:left;font-weight:bold"><?php echo $listing['listing_price']; ?>
</div>
<div style="text-align:left;"><?php echo $listing['display_address']; ?>
</div>
<div style="text-align:left;"><?php echo $listing['city']; ?>, <?php echo $listing['state']; ?>
</div>
<div style="text-align:left;"><?php echo $listing['bedsbaths']; ?>
</div>
<div style="text-align:left;padding-top:20px;"><?php echo $listing['branding']['courtesy_text']; ?>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</a>
