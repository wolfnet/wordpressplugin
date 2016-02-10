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

?>

<?php
    $keyids = array();
    foreach($markets as $market) {
        array_push($keyids, $market->id);
    }
    $jsKeyids = json_encode($keyids);
?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_quickSearchOptions">
    <?php if(count($markets) > 1): ?>
	<input type="hidden" id="<?php echo $keyids_wpid; ?>" class="keyids" name="<?php echo $keyids_wpname; ?>" value="<?php echo implode(",", $keyids); ?>" />
    <?php endif; ?>

	<table class="form-table">

		<tr>
			<td><label>Title:</label></td>
			<td><input id="<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" value="<?php echo $title; ?>" type="text" /></td>
		</tr>

		<tr>
			<td><label>Layout:</label></td>
			<td>
				<select id="<?php echo $view_wpid; ?>" name="<?php echo $view_wpname; ?>" >
					<option value="basic" <?php echo ($view == "basic" ? 'selected="selcted"': "") ?>>Basic</option>
					<option value="legacy" <?php echo ($view == "legacy" ? 'selected="selcted"': "") ?>>Legacy</option>
				</select>
			</td>
		</tr>

		<?php if(count($markets) < 2): ?>
		<tr>
			<td><label for="<?php echo $smartsearch_wpid; ?>">SmartSearch:</label></td>
			<td>
				<select id="<?php echo $smartsearch_wpid; ?>" name="<?php echo $smartsearch_wpname; ?>" >
					<option value="false" <?php echo $smartsearch_false_wps; ?>>Disabled</option>
					<option value="true" <?php echo $smartsearch_true_wps; ?>>Enabled</option>
				</select>
				<span class="wolfnet_moreInfo">
					Enabling SmartSearch on your WolfNet Quick Search will allow website visitors to select a specific search type from a drop-down of suggestions based on what they type into the search. The SmartSearch will present the search types available for the criteria they enter, for example: city, area, neighborhood or school district, thus allowing them to further customize their results.
				</span>
			</td>
		</tr>
		<?php endif; ?>

		<?php if(count($markets) > 1): ?>
        <tr>
        	<td><label>Market:</label></td>
        	<td>
                <table>
                    <tr>
                        <td>
                            <input type="checkbox" class="allproductkeys" id="all" value="all"> All
                        </td>
                    </td>
                    <?php for($i=0; $i<=count($markets)-1; $i++): ?>
                    <tr>
                        <td>
        	                <input type="checkbox" class="productkey" value="<?php echo $markets[$i]->id; ?>"
                                <?php if( in_array($markets[$i]->id, $keyids) ) echo ' checked'; ?>
        	                    >
                                <?php echo $markets[$i]->label; ?>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </table>
        	</td>
        </tr>
        <tr>
            <td><label>Routing</label></td>
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
        var form = $("#<?php echo $instance_id; ?>").parents("form:first");

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
    });

</script>
