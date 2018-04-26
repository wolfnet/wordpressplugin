<?php

/**
 *
 * @title         adminSearch.php
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

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h1>Search Manager - WolfNet<sup>&reg;</sup></h1>

    <noscript>
        <div class="error">
            This page will not work without JavaScript enabled.
        </div>
    </noscript>

	<div style="max-width: 875px;">

		<p>
			The <strong>Search Manager</strong> allows you to create and save custom searches for use
			in shortcodes and widgets. The WordPress Search Manager works much the same way as the
			URL Search Builder within the MLSFinder Admin.
		</p>

		<p>
			Custom searches can target any of the search criteria that is available on your property
			search. Keep in mind that some search criteria is more restrictive than others, which
			means less results will be produced. Use the <strong>Results</strong> feature to
			determine how restrictive a search may be. NOTE: the search criteria available on your
			property search is based on the data available in the feed from your MLS. This data is
			subject to change, which may affect custom search strings you generate. WolfNet
			recommends that you periodically review your custom searches to verify that they still
			produce the expected results. If not, you may need to revisit the search manager and
			create a new custom search.
		</p>

	</div>

    <?php if(count($markets) > 1): ?>
	<div class="wolfnet_box">
		<h3>Market</h3>
		<div class="wolfnet_boxContent">
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



	<form id="<?php echo esc_attr($form_id); ?>" role="form" method="get" class="wnt-form"
	 action="<?php echo esc_attr($form_action); ?>">

		<div class="wolfnet_box">
			<div class="wolfnet_boxContent">
				<h3>Search</h3>
				<div class="wnt-form-group">
					<label for="q_searchInput" class="screen-reader-text">Search</label>
					<input name="q" id="q_searchInput" type="text" autocomplete="off" class="wnt-form-control large-text"
					 placeholder="Search by School, Area, Subdivision, Address/zip, School District, Area, County/city, and more!" />
				</div>
			</div>
		</div>

		<div class="wnt-sb-criteria">
			<div class="wolfnet_box">
				<h3>Filters</h3>
				<div class="wolfnet_boxContent">
					<?php echo $searchForm; ?>
				</div>
			</div>
		</div>

		<div class="wnt-sb-results">
			<div class="wolfnet_box">
				<h3>Results</h3>
				<div class="wolfnet_boxContent">
					<div class="wnt-sb-tabs">
						<div class="wnt-sb-tab" id="wnt-sb-results">
							<h4>Results</h4>
							<?php //echo $searchResults; ?>
						</div>
						<div class="wnt-sb-tab" id="wnt-sb-map">
							<h4>Map</h4>
							<?php //echo $searchResults; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="wolfnet_clearfix"></div>

	</form>


	<div id="save_search" class="wolfnet_box">
		<h3>Save</h3>
		<div class="wolfnet_boxContent">
			<input type="text" title="Description" style="width: 85%;" placeholder="Description" />
			<button class="button button-primary" style="margin-left: 15px;">Save Search</button>
		</div>
	</div>

    <table id="savedsearches" class="wp-list-table widefat" style="width:100%;">
        <thead>
            <tr>
                <th style="text-align:left;">Description</th>
                <th style="width:200px;">Date Created</th>
                <th style="width:110px;"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>


<script>

	if (typeof jQuery !== 'undefined') {

		(function ($) {

			var formId = '<?php echo $form_id; ?>';
			var $form = $('#' + formId);
			var $searchMgr = $('#wolfnet-search-manager');
			var $searchResetBtn = $searchMgr.find('.resetForm a');

			// Dress the reset link as a button
			$searchResetBtn.addClass('button button-secondary').prepend(
				'<span class="wnt-icon wnt-icon-cancel-circle"></span> '
			);

			$('#savedsearches').wolfnetSearchManager({
				baseUrl:     '<?php echo $baseUrl; ?>',
				ajaxUrl:     wolfnet_ajax.ajaxurl,
				ajaxAction:  'wolfnet_search_manager_ajax',
				saveForm:    $( '#save_search' )
			});

			<?php if(count($markets) > 1): ?>
				$('#changeMarket').click(function () {
					document.location.href = 'admin.php?page=wolfnet_plugin_search&keyid=' + $('#keyid').val();
				});
			<?php endif; ?>


			// Search tabs
			/*
			var $searchTabs  = $form.find('.wnt-sb-tabs'),
				$searchTab   = $searchTabs.find('.wnt-sb-tab'),
				$spinner     = $('<div class="spinner is-active"></div>');

			if ($searchTab.length > 1) {

				var $searchNav = $('<ul>');

				$searchTab.each(function () {
					var $item = $(this);
					var $itemHeading = $item.find('>h1, >h2, >h3, >h4, >h5').first();
					var itemId = $item.attr('id'),
						itemLabel = $itemHeading.text();

					$itemHeading.remove();

					$searchNav.append($('<li><a href="#' + itemId + '">' + itemLabel + '</a></li>'));

				});

				$searchTab.first().before($searchNav);

				$searchTabs.tabs();

			}
			*/

		})(jQuery);

	}

</script>
