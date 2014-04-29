<?php

/**
 *
 * @title         listingGridOptions.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_listingGridOptions">

    <input id="<?php echo $criteria_wpid; ?>" name="<?php echo $criteria_wpname; ?>" 
           value="<?php echo $criteria; ?>" type="hidden" />

    <?php if(count($markets) == 1): ?>
    <input type="hidden" id="<?php echo $keyid_wpid; ?>" name="<?php echo $keyid_wpid; ?>" class="keyid" value="1" />
    <?php endif; ?>

    <table class="form-table">

        <tr>
            <td><label>Title:</label></td>
            <td><input id="<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" 
                       value="<?php echo $title; ?>" type="text" /></td>
        </tr>

        <?php if(count($markets) > 1): ?>
        <tr>
            <td><label>Market:</label></td>
            <td>
                <select id="<?php echo $keyid_wpid; ?>" class="keyid" name="<?php echo $keyid_wpname; ?>" class="keyid">
                    <?php for($i=0; $i<=count($markets)-1; $i++): ?>
                    <option value="<?php echo $markets[$i]->id; ?>" <?php if($markets[$i]->id == $keyid) echo 'selected="selected"'; ?>><?php echo $markets[$i]->label; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <?php endif; ?>
              
        <tr class="modeField">
            <td><label>Mode:</label></td>
            <td>
                <input id="<?php echo $mode_wpid; ?>" name="<?php echo $mode_wpname; ?>" value="basic" type="radio" checked="checked" /> Basic <br/>
                <input id="<?php echo $mode_wpid; ?>" name="<?php echo $mode_wpname; ?>" value="advanced" type="radio" /> Advanced
            </td>
        </tr>

        <tr>
            <td><label>Include Map:</label></td>
            <td>
                <select id="<?php echo $maptype_wpid; ?>" name="<?php echo $maptype_wpname; ?>" class="maptype" <?php if (!$mapEnabled) { ?>disabled<?php } ?> >
                    <?php foreach ($maptypes as $mt) { ?>
                        <option value="<?php echo $mt['value']; ?>" <?php selected($maptypes, $mt['value']); ?>>
                            <?php echo $mt['label']; ?>
                        </option>
                    <?php } ?>
                </select>
                
                <p id="mapDisabled" class="mapDisabled" style="display:<?php echo ($mapEnabled ? 'none' : 'block'); ?>">
                    <span style="color:#FF0000;font-weight:bold;">*</span> 
                    <span style="font-style:italic;font-size:0.75em;">Map option is unavailable at this time. To enable this feature, please contact WolfNet sales for more information (612) 342-0088.</span>
                </p>
            </td>
        </tr>

        <tr class="advanced-option savedSearchField">
            <td><label>Saved Search:</label></td>
            <td>
                <select id="<?php echo $savedsearch_wpid; ?>" class="savedsearch" name="<?php echo $savedsearch_wpname; ?>" style="width:200px;">
                    <?php $foundOne = false; ?>
                    <option value="">-- Saved Search --</option>
                    <?php foreach ($savedsearches as $ss) { ?>
                        <?php $foundOne = ($savedsearch == $ss->ID) ? true : $foundOne; ?>
                        <option value="<?php echo $ss->ID; ?>" <?php selected($savedsearch, $ss->ID) ?>>
                            <?php echo $ss->post_title; ?>
                        </option>
                    <?php } ?>
                    <?php if ( !$foundOne && ( $criteria != '' && $criteria != '[]' ) ) { ?>
                        <option value="deleted" selected="selected">** DELETED **</option>
                    <?php } ?>
                </select>
                <span class="wolfnet_moreInfo">
                    Select a saved search to define the properties to be displayed. Saved searches
                    are created via the Search Manager page within the WolfNet plugin admin section.
                </span>
            </td>
        </tr>

        <tr class="basic-option">
            <td><label>Price:</label></td>
            <td>
                <select id="<?php echo $minprice_wpid; ?>" class="pricerange minprice" name="<?php echo $minprice_wpname; ?>">
                    <option value="">Min. Price</option>
                    <?php foreach ($prices as $price) { ?>
                        <option value="<?php echo $price['value']; ?>" <?php selected($minprice, $price['value']); ?>>
                            <?php echo $price['label']; ?>
                        </option>
                    <?php } ?>
                </select>
                <span>to</span>
                <select id="<?php echo $maxprice_wpid; ?>" class="pricerange maxprice" name="<?php echo $maxprice_wpname; ?>">
                    <option value="">Max. Price</option>
                    <?php foreach ( $prices as $price ) { ?>
                        <option value="<?php echo $price['value']; ?>" <?php selected($maxprice, $price['value']); ?>>
                            <?php echo $price['label']; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>

        <tr class="basic-option">
            <td><label>City:</label></td>
            <td>
                <input id="<?php echo $city_wpid; ?>" name="<?php echo $city_wpname; ?>"
                    type="text" value="<?php echo $city; ?>" /> <br/>
                <input id="<?php echo $exactcity_wpid; ?>" name="<?php echo $exactcity_wpname; ?>"
                       type="checkbox" value="1" checked="checked">
                Only listings that exactly match this city.            
            </td>
        </tr>

        <tr class="basic-option">
            <td><label>Zipcode:</label></td>
            <td>
                <input id="<?php echo $zipcode_wpid; ?>" name="<?php echo $zipcode_wpname; ?>"
                    type="text" value="<?php echo $zipcode; ?>" />
            </td>
        </tr>

        <tr>
            <td><label>Agent/Broker:</label></td>
            <td>
                <select id="<?php echo $ownertype_wpid; ?>" name="<?php echo $ownertype_wpname; ?>">
                    <option value="all">All</option>
                    <?php foreach ($ownertypes as $ot) { ?>
                        <option value="<?php echo $ot['value']; ?>" <?php selected($ownertype, $ot['value']); ?>>
                            <?php echo $ot['label']; ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="wolfnet_moreInfo">
                    Restrict search results by brokerage and/or agent. When All (the default) is
                    selected, all matching properties display, regardless of listing brokerage and
                    agent. When any of the other options is selected, search results are restricted
                    to the site owning agent or brokerage, as indicated by the name of the option
                    (ie, Agent Then Broker, Agent Only, Broker Only).
                </span>
            </td>
        </tr>

        <tr>
            <td><label>Pagination Enabled/Disabled:</label></td>
            <td>
                <select id="<?php echo $paginated_wpid; ?>" name="<?php echo $paginated_wpname; ?>" >
                    <option value="false" <?php echo $paginated_false_wps; ?>>Disabled</option>
                    <option value="true"  <?php echo $paginated_true_wps; ?> >Enabled</option>
                </select>
                <span class="wolfnet_moreInfo">
                    Enable to add pagination capabilities for the user to the result set.
                    Results per page can be defined below in the Max Results Per Page field.
                </span>
            </td>
        </tr>

        <tr>
            <td><label>Sort Options:</label></td>
            <td>
                <select id="<?php echo $sortoptions_wpid; ?>" name="<?php echo $sortoptions_wpname; ?>" >
                    <option value="false" <?php echo $sortoptions_false_wps; ?>>Disabled</option>
                    <option value="true"  <?php echo $sortoptions_true_wps; ?> >Enabled</option>
                </select>
                <span class="wolfnet_moreInfo">
                    Enable to add a drop-down menu which will allow users to sort listings by a
                    predefined set of data fields.
                </span>
            </td>
        </tr>

        <tr>
            <td><label>Max Results Per Page:</label></td>
            <td>
                <input id="<?php echo $maxresults_wpid; ?>" name="<?php echo $maxresults_wpname; ?>"
                    type="text" maxlength="2" size="2" value="<?php echo $maxresults; ?>" />
                <span class="wolfnet_moreInfo">
                    Define the number of properties to display per search results page.
                    The maximum number of properties that can be displayed per page is 50.
                </span>
            </td>
        </tr>

    </table>

</div>

<script type="text/javascript">

    jQuery(function($){
        $('.wolfnet_listingGridOptions').wolfnetListingGridControls();
        wolfnet.initMoreInfo( $( '.wolfnet_listingGridOptions .wolfnet_moreInfo' ) );
    });

</script>
