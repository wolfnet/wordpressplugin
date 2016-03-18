<?php

/**
 *
 * @title         smartSearch.php
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

<div id="<?php echo $instance_id; echo ' '; echo $componentId; ?>"
    class="wolfnet_smartSearch wolfnet_widget">

    <?php if (trim($title) != '') { ?>
        <h2 class="wolfnet_widgetTitle"><?php echo $title; ?></h2>
    <?php } ?>

    <form id="<?php echo $instance_id; ?>_smartSearchForm"
        class="wolfnet_smartSearch_form wnt-smart-search<?php echo $componentId; ?>"
        name="<?php echo $instance_id; ?>_smartSearchForm"
        method="get" action="<?php echo $formAction; ?>" >

        <input type="hidden" name="search_mode" value="form">
        <input type="hidden" name="resetform" value="1">
        <input type="hidden" name="action" value="newsearchsession">

        <fieldset class="wnt-smartsearch">
            <div class="form-group">
                <div class="wnt-smartsearch-input-container">
                    <input name="q" type="text" value=""
                        id="<?php echo $instance_id; ?>_search_text"
                        class="<?php echo $smartsearchInput; ?>_search_text wnt-smart-search"
                        placeholder="<?php echo $smartSearchPlaceholder; ?>" />
                </div>
            </div>
            <div class="wnt-smart-menu smart-menu<?php echo $componentId; ?>"></div>
        </fieldset>


        <div class="wolfnet_smartHorizontalWidget">

            <!-- Min Price -->
            <div class="wolfnet_smartWidgetMinPrice">
                <select id="<?php echo $instance_id; ?>_min_price" name="min_price">
                    <option value="">Min. Price</option>
                    <?php
                    if (is_array($prices) && array_key_exists('min_price', $prices)) {
                        foreach ($prices['min_price']['options'] as $price) { ?>
                            <option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
                        <?php
                        }
                    } ?>
                </select>
            </div>

            <!-- Max Price -->
            <div class="wolfnet_smartWidgetMaxPrice">
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

            <!-- Beds -->
            <div class="wolfnet_smartWidgetBeds">
                <select id="<?php echo $instance_id; ?>_min_beds" name="min_bedrooms">
                    <option value="">Beds</option>
                    <?php foreach ($beds as $bed) { ?>
                    <option value="<?php echo $bed['value']; ?>"><?php echo $bed['label']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Baths -->
            <div class="wolfnet_smartWidgetBaths">
                <select id="<?php echo $instance_id; ?>_min_baths" name="min_bathrooms">
                    <option value="">Baths</option>
                    <?php foreach ($baths as $bath) { ?>
                    <option value="<?php echo $bath['value']; ?>"><?php echo $bath['label']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="wolfnet_smartSearchFormButton">
                <button class="wolfnet_smartSearchForm_submitButton" name="search" type="submit">Search!</button>
            </div>

        <div>

    </form>
</div>


<script type="text/javascript">

    jQuery(function($){
        var $form = $('#<?php echo $instance_id; ?>_smartSearchForm');

        var fields = JSON.parse('<?php echo $smartSearchFields; ?>');
        var map = JSON.parse('<?php echo $smartSearchFieldMap; ?>');

        $form.find('.wnt-smartsearch input:first').wolfnetSmartSearch({
            ajaxUrl    : wolfnet_ajax.ajaxurl,
            ajaxAction : 'wolfnet_smart_search',
            componentId: '<?php echo $componentId; ?>',
            fields     : fields,
            fieldMap   : map
		})
    });

</script>
