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

<div id="<?php echo $instance_id; ?>" class="wolfnet_agentPagesOptions">
    <?php if(count($offices) > 1): ?>
    <input type="hidden" id="<?php echo $excludeoffices_wpid; ?>" class="officeids" name="<?php echo $excludeoffices_wpname; ?>" value="" />
    <?php endif; ?>

    <table class="form-table">
        <tr>
            <th>
                <label for="<?php echo $officetitle_wpid; ?>">
                    Office list title:
                </label>
            </th>
            <td>
                <input id="<?php echo $officetitle_wpid; ?>"
                    name="<?php echo $officetitle_wpname; ?>"
                    value="<?php echo $officetitle; ?>" type="text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $agenttitle_wpid; ?>">
                    Agent list title:
                </label>
            </th>
            <td>
                <input id="<?php echo $agenttitle_wpid; ?>"
                    name="<?php echo $agenttitle_wpname; ?>"
                    value="<?php echo $agenttitle; ?>" type="text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $detailtitle_wpid; ?>">
                    Agent detail title:
                </label>
            </th>
            <td>
                <input id="<?php echo $detailtitle_wpid; ?>"
                    name="<?php echo $detailtitle_wpname; ?>"
                    value="<?php echo $detailtitle; ?>" type="text" />
            </td>
        </tr>
        <?php
        if(count($offices) > 1) {
        ?>
            <tr>
                <th>
                    <label for="<?php echo $showoffices_wpid; ?>">
                        Show offices:
                    </label>
                </th>
                <td>
                    <select id="<?php echo $showoffices_wpid; ?>" name="<?php echo $showoffices_wpname; ?>">
                        <option value="true"<?php if($showoffices == 'true') echo ' selected="selected"'?>>Yes</option>
                        <option value="false"<?php if($showoffices == 'false') echo ' selected="selected"'?>>No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label>Exclude offices:</label>
                </th>
                <td>
                    <?php
                    $selectedOffices = array_unique(explode(",", $excludeoffices), SORT_STRING);
                    foreach ($offices as $office) {
                        if (strlen($office['office_id']) > 0) {
                            echo '<input id="officeexclude_' . $office['office_id'] . '"';
                            echo ' type="checkbox" class="officeexclude"';
                            if (in_array($office['office_id'], $selectedOffices)) {
                                echo ' checked="checked"';
                            }
                            echo ' value="' . $office['office_id'] . '" /> ';
                            echo '<label for="officeexclude_' . $office['office_id'] . '">';
                            echo $office['name'] . ' (' . $office['office_id'] . ')';
                            echo '</label><br />';
                        }
                    }
                    ?>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <th>
                <label for="<?php echo $numperpage_wpid; ?>">Agents per page:</label>
            </th>
            <td>
                <select id="<?php echo $numperpage_wpid; ?>" name="<?php echo $numperpage_wpname; ?>">
                    <option value="10"<?php if($numperpage == 10) echo ' selected="selected"'; ?>>10</option>
                    <option value="20"<?php if($numperpage == 20) echo ' selected="selected"'; ?>>20</option>
                    <option value="30"<?php if($numperpage == 30) echo ' selected="selected"'; ?>>30</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $activelistings_wpid; ?>">Show active listings:</label>
            </th>
            <td>
                <select id="<?php echo $activelistings_wpid; ?>" name="<?php echo $activelistings_wpname; ?>">
                    <option value="true"<?php if($activelistings == 'true') echo ' selected="selected"'; ?>>Yes</option>
                    <option value="false"<?php if($activelistings == 'false') echo ' selected="selected"'; ?>>No</option>
                </select>
            </td>
        </tr>
        <?php
        if($showSoldOption) {
        ?>
        <tr>
            <th>
                <label for="<?php echo $soldlistings_wpid; ?>">Show sold listings:</label>
            </th>
            <td>
                <select id="<?php echo $soldlistings_wpid; ?>" name="<?php echo $soldlistings_wpname; ?>">
                    <option value="true"<?php if($soldlistings == 'true') echo ' selected="selected"'; ?>>Yes</option>
                    <option value="false"<?php if($soldlistings == 'false') echo ' selected="selected"'; ?>>No</option>
                </select>
            </td>
        </tr>
        <?php
        }
        ?>
    </table>
</div>

<?php if(count($offices) > 1): ?>
<script type="text/javascript">

    jQuery(function($){
        $("button[type=submit],input[type=submit]").click(function() {
            var <?php echo $instance_id; ?> = [];
            var array = <?php echo $instance_id; ?>;

            $('.officeexclude').each(function() {
                if($(this).prop('checked')) {
                    if(array.indexOf($(this).val()) == -1) {
                        array.push($(this).val());
                    }
                }
            });

            $('#<?php echo $excludeoffices_wpid; ?>').val(array.join(','));

        });
    });

</script>
<?php endif; ?>
