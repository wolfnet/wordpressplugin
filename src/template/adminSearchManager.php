<?php

/**
 *
 * @title         adminSearchManager.php
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

<div id="wolfnet-search-manager" class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br></div>

    <h2>WolfNet <sup>&reg;</sup> - Search Manager</h2>

    <noscript>
        <div class="error">
            This page will not work without JavaScript enabled.
        </div>
    </noscript>

    <div style="width:875px">

        <p>The <strong>Search Manager</strong> allows you to create and save custom searches for use
            in shortcodes and widgets. The WordPress Search Manager works much the same way as the
            URL Search Builder within the MLSFinder Admin.</p>

        <p>Custom searches can target any of the search criteria that is available on your property
            search. Keep in mind that some search criteria is more restrictive than others, which
            means less results will be produced. Use the <strong>Results</strong> feature to
            determine how restrictive a search may be. NOTE: the search criteria available on your
            property search is based on the data available in the feed from your MLS. This data is
            subject to change, which may affect custom search strings you generate. WolfNet
            recommends that you periodically review your custom searches to verify that they still
            produce the expected results. If not, you may need to revisit the search manager and
            create a new custom search.</p>

    </div>

    <?php if(count($markets) > 1): ?>
    <div class="style_box">
        <div class="style_box_header">Market</div>
        <div class="style_box_content">
            Select the market that you'd like to use to create searches and click Apply.
            <p><select id="keyid" name="keyid">
                <?php for($i=0; $i<=count($markets)-1; $i++): ?>
                <option value="<?php echo $markets[$i]->id; ?>"
                    <?php if($markets[$i]->id == $selectedKey) echo ' selected="selected"'?>><?php echo $markets[$i]->label; ?></option>
                <?php endfor; ?>
            </select>
            <input type="button" id="changeMarket" value="Apply" /></p>
        </div>
    </div>
    <?php else: ?>
    <input type="hidden" id="keyid" name="keyid" value="<?php echo $markets[0]->id; ?>" />
    <?php endif; ?>

    <div id="searchmanager">
    <?php echo $searchForm; ?>
    </div>

    <div id="save_search" class="style_box">
        <div class="style_box_header">Save</div>
        <div class="style_box_content">
            <input type="text" title="Description" style="width: 85%;" placeholder="Description">
            <button class="button-primary" style="margin-left: 15px;">Save Search</button>
        </div>
    </div>

    <table id="savedsearches" class="wp-list-table widefat" style="width:100%;">
        <thead>
            <tr>
                <th style="text-align:left;">Description</th>
                <th style="wwidth:200px;">Date Created</th>
                <th style="width:110px;"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>


<script type="text/javascript">

    if ( typeof jQuery != 'undefined' ) {

        ( function ( $ ) {

			$('#savedsearches').wolfnetSearchManager({
				baseUrl    : '<?php echo $baseUrl; ?>',
				ajaxUrl    : wolfnet_ajax.ajaxurl,
				ajaxAction : 'wolfnet_search_manager_ajax',
				saveForm   : $( '#save_search' )
			});

            <?php if(count($markets) > 1): ?>
            $( '#changeMarket' ).click(function() {
                document.location.href = "admin.php?page=wolfnet_plugin_search_manager&keyid=" + $('#keyid').val();
            });
            <?php endif; ?>

        } )( jQuery );

    }

</script>
