<?php

/**
 *
 * @title         quickSearchOptions.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_quickSearchOptions">

    <?php if(count($markets) > 1): ?>
	<input type="hidden" id="keyids" name="keyids" value="<?php echo $markets[0]->id; ?>" />
    <?php endif; ?>

    <table class="form-table">
        <tr>
            <td><label>Title:</label></td>
            <td><input id="<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" value="<?php echo $title; ?>" type="text" /></td>
        </tr>
        <?php if(count($markets) > 1): ?>
        <tr>
        	<td><label>Market:</label></td>
        	<td>
                <table>
                    <tr>
                        <td>
                            <input type="checkbox" id="all" value="all"> All
                        </td>
                    </td>
                    <?php for($i=0; $i<=count($markets)-1; $i++): ?>
                    <tr>
                        <td>
        	                <input type="checkbox" class="productkey" value="<?php echo $markets[$i]->id; ?>"
        	                    <?php if($markets[$i]->id == $selectedKey) echo ' selected="selected"'?>> <?php echo $markets[$i]->label; ?>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </table>
        	</td>
        </tr>
    	<?php endif; ?>
    </table>

</div>

<script type="text/javascript">

    jQuery(function($){
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