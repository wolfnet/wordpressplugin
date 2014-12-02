<?php

/**
 *
 * @title         quickSearch.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_quickSearch">

    <?php if (trim($title) != '') { ?>
        <h2 class="wolfnet_widgetTitle"><?php echo $title; ?></h2>
    <?php } ?>

    <form id="<?php echo $instance_id; ?>_quickSearchForm" class="wolfnet_quickSearch_form"
        name="<?php echo $instance_id; ?>_quickSearchForm" method="get"
        action="<?php echo $formAction; ?>">

        <input name="action" type="hidden" value="newsearchsession" />
        <input name="submit" type="hidden" value="Search" />

        <input type="hidden" name="search_source" value="wp_plugin">

        <?php 
            if(count($keyids) > 1) {
                foreach($markets as $market) {
                    foreach($keyids as $key) {
                        if($market->id == $key) {
                            echo '<div class="wolfnet_market"><input type="radio" name="market" value="' . $key . '"> ' . $market->label . '</div>';
                        }
                    }
                }

                echo '<div class="wolfnet_clearfix"></div>';
            } 
        ?>
        
        <ul class="wolfnet_searchType">
            <li><a href="javascript:;" wolfnet:search_type="opentxt"><span>Location</span></a></li>
            <li><a href="javascript:;" wolfnet:search_type="mlsnum"><span>Listing Number</span></a></li>
        </ul>

        <div class="wolfnet_searchTypeField">
            <input id="<?php echo $instance_id; ?>_search_text" class="wolfnet_quickSearch_searchText"
                name="search_text" type="text" />
        </div>

        <div class="wolfnet_widgetPrice">

            <label>Price</label>


            <div>
                <select id="<?php echo $instance_id; ?>_min_price" name="min_price">
                    <option value="">Min. Price</option>
                    <?php 
                    if (is_array($prices) && array_key_exists('min_price', $prices)) {
                        foreach ($prices['min_price']['options'] as $price) { ?>
                            <option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
                        <?php 
                        } 
                    }
                    ?>
                </select>
            </div>

            <div>
                <select id="<?php echo $instance_id; ?>_max_price" name="max_price">
                    <option value="">Max. Price</option>
                    <?php 
                    if (is_array($prices) && array_key_exists('max_price', $prices)) {
                        foreach ($prices['max_price']['options'] as $price) { ?>
                            <option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
                        <?php 
                        }
                    } 
                    ?>
                </select>
            </div>

            <div class="wolfnet_clearfix"></div>

        </div>

        <div class="wolfnet_widgetBedBath">

            <div class="wolfnet_widgetBeds">
                <label for="<?php echo $instance_id; ?>_min_beds">Beds</label>
                <select id="<?php echo $instance_id; ?>_min_beds" name="min_bedrooms">
                    <option value="">Any</option>
                    <?php foreach ($beds as $bed) { ?>
                    <option value="<?php echo $bed['value']; ?>"><?php echo $bed['label']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="wolfnet_widgetBaths">

                <label for="<?php echo $instance_id; ?>_min_baths">Baths</label>
                <select id="<?php echo $instance_id; ?>_min_baths" name="min_bathrooms">
                    <option value="">Any</option>
                    <?php foreach ($baths as $bath) { ?>
                    <option value="<?php echo $bath['value']; ?>"><?php echo $bath['label']; ?></option>
                    <?php } ?>
                </select>

            </div>

        </div>

        <div class="wolfnet_quickSearchFormButton">

            <button class="wolfnet_quickSearchForm_submitButton" name="search" type="submit">Search!</button>

        </div>

    </form>

</div>

<script type="text/javascript">

    jQuery(function($){
        $('#<?php echo $instance_id; ?>').wolfnetQuickSearch();

        <?php if(count($keyids) > 1): ?>
        // Disable fields until market is selected.
        if(!$("[name=market]").is(':checked')) {
            $.fn.toggleQuickSearchFields(true);
        }
        <?php endif; ?>

        $("[name=market]").click(function() {
            $.fn.toggleQuickSearchFields(false);
            $.fn.rebuildQuickSearchOptions($(this).val());
        });
    });

</script>
