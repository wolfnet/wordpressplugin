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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_smartSearch">

    <?php if (trim($title) != '') { ?>
        <h2 class="wolfnet_widgetTitle"><?php echo $title; ?></h2>
    <?php } ?>

    <form id="<?php echo $instance_id; ?>_smartSearchForm"
        class="wolfnet_smartSearch_form wnt-smart-search<?php echo $componentId; ?>"
        name="<?php echo $instance_id; ?>_smartSearchForm"
        method="get" action="<?php echo $formAction; ?>" >

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


        <div class="wolfnet_smartHorizontalFields">

            <div class="wolfnet_smartPriceFields">
                <!-- Min Price -->
                <div class="wolfnet_smartMinPrice">
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
                <div class="wolfnet_smartMaxPrice">
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
            </div>

            <div class="wolfnet_smartBedBathFields">
                <!-- Beds -->
                <div class="wolfnet_smartBeds">
                    <select id="<?php echo $instance_id; ?>_min_beds" name="min_bedrooms">
                        <option value="">Beds</option>
                        <?php foreach ($beds as $bed) { ?>
                        <option value="<?php echo $bed['value']; ?>">
                            <?php echo $bed['label']; ?>
                            BED<?php if ($bed['value'] > 1) { ?>S<?php } ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Baths -->
                <div class="wolfnet_smartBaths">
                    <select id="<?php echo $instance_id; ?>_min_baths" name="min_bathrooms">
                        <option value="">Baths</option>
                        <?php foreach ($baths as $bath) { ?>
                        <option value="<?php echo $bath['value']; ?>">
                            <?php echo $bath['label']; ?>
                            BATH<?php if ($bath['value'] > 1) { ?>S<?php } ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="wolfnet_smartSubmit">
                <button class="wolfnet_smartSearchForm_submitButton" name="search" type="submit">Search</button>
            </div>

        </div>

        <div class="wolfnet_clearfix"></div>

    </form>

</div>


<?php $marketsJson = json_encode($markets); ?>


	<script type="text/javascript">

		jQuery(function($){
			var $form = $('#<?php echo $instance_id; ?>_smartSearchForm');

			var markets = JSON.parse('<?php echo $marketsJson; ?>');

			var fields = JSON.parse('<?php echo $smartSearchFields; ?>');
			var map = JSON.parse('<?php echo $smartSearchFieldMap; ?>');

			$form.find('.wnt-smartsearch input:first').wolfnetSmartSearch({
				ajaxUrl    : wolfnet_ajax.ajaxurl,
				ajaxAction : 'wolfnet_smart_search',
				componentId: '<?php echo $componentId; ?>',
				fields     : fields,
				fieldMap   : map,
				markets    : markets
			})

		});

	</script>

