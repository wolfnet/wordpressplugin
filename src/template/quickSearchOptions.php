<?php

/**
 *
 * @title         quickSearchOptions.php
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

// TODO: Disable "Routing" select if Smartsearch is true (logic is specific to Quick Search)
?>

<?php
    $jsKeyids = json_encode($keyids);
?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget_options wolfnet_quickSearchOptions">
    <?php if(count($markets) > 1): ?>
        <input type="hidden" id="<?php echo $keyids_wpid; ?>" class="keyids"
         name="<?php echo $keyids_wpname; ?>" value="<?php echo implode(",", $keyids); ?>" />
    <?php endif; ?>

    <table class="form-table">

        <tr>
            <th scope="row">
                <label for="wnt-<?php echo $title_wpid; ?>">Title:</label>
            </th>
            <td>
                <input id="wnt-<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>"
                 value="<?php echo $title; ?>" type="text" class="regular-text" />
            </td>
        </tr>



		<tr>
		    <th scope="row">
		        <label for="<?php echo $smartsearch_wpid; ?>">SmartSearch:</label>
		    </th>
		    <td>
		        <select id="<?php echo $smartsearch_wpid; ?>" name="<?php echo $smartsearch_wpname; ?>" >
		            <option value="false" <?php echo $smartsearch_false_wps; ?>>Disabled</option>
		            <option value="true" <?php echo $smartsearch_true_wps; ?>>Enabled</option>
		        </select>
		        <span class="wolfnet_moreInfo">
		            Enabling SmartSearch on your WolfNet Quick Search will allow website visitors to search all available locale-based search types, including categories like ‘area,’ ‘body of water’ and ‘school district,’ while also being presented with search suggestions as they enter their search terms.
		        </span>
		    </td>
		</tr>


        <tr>
            <th scope="row">
                <label for="<?php echo $view_wpid; ?>">Layout:</label>
            </th>
            <td>
                <select id="<?php echo $view_wpid; ?>" name="<?php echo $view_wpname; ?>" >
                    <option value="basic" <?php echo ($view == "basic" ? 'selected="selcted"': "") ?>>Basic</option>
                    <option value="legacy" <?php echo ($view == "legacy" ? 'selected="selcted"': "") ?>>Legacy</option>
                </select>
            </td>
        </tr>

        <?php if(count($markets) > 1): ?>
            <tr>
                <th scope="row">
                    <label>Markets:</label>
                </th>
                <td>
                    <table>
                        <tr>
                            <td>
								The display name for each of your markets can be customized in the General Settings section of the WolfNet IDX Plugin.
                            </td>
                        </td>
                        <tr>
                            <td>
                                <input id="productkey_all" type="checkbox" class="allproductkeys"
                                 name="all" value="all" />
                                <label for="productkey_all">All</label>
                            </td>
                        </td>
                        <?php for($i=0; $i<=count($markets)-1; $i++): ?>
                            <tr>
                                <td>
                                    <input id="productkey_<?php echo $markets[$i]->id; ?>"
                                     type="checkbox" class="productkey" value="<?php echo $markets[$i]->id; ?>"
                                     <?php if( in_array($markets[$i]->id, $keyids) ) echo ' checked'; ?> />
                                    <label for="productkey_<?php echo $markets[$i]->id; ?>">
                                        <?php echo $markets[$i]->label; ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo $routing_wpid; ?>">Routing</label>
                </th>
                <td>
                    <select id="<?php echo $routing_wpid; ?>" name="<?php echo $routing_wpname; ?>" >
                        <option value="auto" <?php echo ($routing == "auto" ? 'selected="selcted"': "") ?>>Auto</option>
                        <option value="manual" <?php echo ($routing == "manual" ? 'selected="selcted"': "") ?>>Manual</option>
                    </select>
                    <span class="wolfnet_moreInfo">
                        Auto routing will automatically send users to your IDX solution that has the most
                        matching listings for their search criteria. Manual routing will require the
                        user to select which if your IDX solutions to search on.
                    </span>
                </td>
            </tr>
    	<?php endif; ?>

    </table>
    <span class="validate_msg"></span>
</div>

<script type="text/javascript">

    jQuery(function($){

        wolfnet.initMoreInfo( $( '#<?php echo $instance_id; ?> .wolfnet_moreInfo' ) );

        <?php if(count($markets) > 1): ?>

        var <?php echo $instance_id; ?> = <?php echo $jsKeyids; ?>;
        var form;
        if($("#widgets-right").length) {
            form = $("#widgets-right #<?php echo $instance_id; ?>").parents("form:first");
        } else {
            form = $("#<?php echo $instance_id; ?>").parents("form:first");
        }


        $(".productkey").click(function(){
            // uncheck "All" if it is checked in this instance of the form only
            form.find(".allproductkeys").attr("checked", false);
            if($(this).prop('checked')) {
                // add to array
                <?php echo $instance_id; ?>.push($(this).val());
            } else {
                // remove from array
                <?php echo $instance_id; ?>.splice(<?php echo $instance_id; ?>.indexOf($(this).val()), 1);

            }

            setValidate();

        });

        $(".allproductkeys").click(function(){
            if($(this).prop('checked')) {
                form.find(".productkey").attr("checked", true);
                // add all the keys to the array
                <?php echo $instance_id; ?> = form.find(".productkey").map(function() { return $(this).val() }).get();

            } else {
                // uncheckthem and remove everything from the array
                form.find(".productkey").attr("checked", false);
                <?php echo $instance_id; ?> = [];
            }

            setValidate();

        });

        function setValidate() {
            form.find(".keyids").val(<?php echo $instance_id; ?>);
            if (<?php echo $instance_id; ?>.length < 1) {
                form.find(".validate_msg").html("You must select at least one market.");
            } else {
                form.find(".validate_msg").html("");
            }
        }
        <?php endif; ?>

        $("button[type=submit]").click(function() {
        <?php if(count($markets) > 1): ?>
            var productkeys = $(".productkey:checked");
            if(productkeys.length == 0 && !$("#all").is(":checked")) {
                alert("Please select a market for your quick search.");
                return false;
            } else {
                if($("#all").is(":checked")) {
                    // Use all key IDs.
                   $("#keyids").val('<?php echo implode(",", $keyids); ?>');
                } else {
                    // Get selected key IDs.
                    var idArray = [];
                    $(productkeys).each(function() {
                        idArray.push($(this).val());
                    });
                    $("#keyids").val(idArray.join(","));
                }
            }
        <?php endif; ?>
        });

        var $smartsearch = $('#<?php echo $smartsearch_wpid; ?>');

        $smartsearch.change(function() {

        	// toggle "Layout" setting onchange
            var $layoutSetting = $('#<?php echo $view_wpid; ?>');
            var $layoutRow = $layoutSetting.closest('tr');
            if ($smartsearch.val() === 'true') {
                $layoutRow.hide();
                $layoutSetting.prop('disabled',true);
            } else {
                $layoutSetting.prop("disabled", false);
                $layoutRow.show();
            }

        	// toggle "Routing" setting onchange
            var $routingSetting = $('#<?php echo $routing_wpid; ?>');
            var $routingRow = $routingSetting.closest('tr');
            if ($smartsearch.val() === 'true') {
                $routingRow.hide();
                $routingSetting.prop('disabled',true);
            } else {
                $routingSetting.prop("disabled", false);
                $routingRow.show();
            }

        }).change();

    });

</script>
