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
    <table class="form-table">
        <tr>
            <td><label>Title:</label></td>
            <td><input id="<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" value="<?php echo $title; ?>" type="text" /></td>
        </tr>
        <tr>
            <td><label>Show offices:</label></td>
            <td>
                <select id="<?php echo $showoffices_wpid; ?>" name="<?php echo $showoffices_wpname; ?>">
                    <option value="true"<?php if($showoffices == true) echo ' selected="selected"'?>>Yes</option>
                    <option value="false"<?php if($showoffices == false) echo ' selected="selected"'?>>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Agents per page:</label></td>
            <td>
                <select id="<?php echo $numperpage_wpid; ?>" name="<?php echo $numperpage_wpname; ?>">
                    <option value="10"<?php if($numperpage == 10) echo ' selected="selected"'?>>10</option>
                    <option value="20"<?php if($numperpage == 20) echo ' selected="selected"'?>>20</option>
                    <option value="30"<?php if($numperpage == 30) echo ' selected="selected"'?>>30</option>
                </select>
            </td>
        </tr>
    </table>
</div>

