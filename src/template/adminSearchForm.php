<?php

?>

	<div class="wnt-criteria">

		<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-status">
				Status
				<span class="wnt-badge wnt-search-criteria-count" title="1 selected">
					1
					<span class="screen-reader-text">selected</span>
				</span>
			</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-status">
				<fieldset class="wnt-form-group">
					<label>Property Status</label>
					<div class="wnt-form-check">
						<input type="radio" name="so" id="so0" value="" class="wnt-form-control"
						 autocomplete="off" checked="checked" />
						<label for="so0">Active Listings</label>
					</div>
					<div class="wnt-form-check">
						<input type="radio" name="so" id="so1" value="1" class="wnt-form-control"
						 autocomplete="off" />
						<label for="so1">Sold Listings</label>
					</div>
				</fieldset>

				<fieldset class="wnt-form-group">
					<label>Listed as</label>
					<div class="wnt-form-check">
						<input name="ss" id="ss_searchInput" type="checkbox" class="wnt-form-control"
						 value="1" />
						<label for="ss_searchInput">Short Sales</label>
					</div>
					<div class="wnt-form-check">
						<input name="fc" id="fc_searchInput" type="checkbox" class="wnt-form-control"
						 value="1" />
						<label for="fc_searchInput">Foreclosures</label>
					</div>
				</fieldset>

				<fieldset class="wnt-form-group">
					<label>Display Only</label>
					<div class="wnt-form-check">
						<input name="nl" id="nl_searchInput" type="checkbox" class="wnt-form-control"
						 value="1" />
						<label for="nl_searchInput">New Listings</label>
					</div>
					<div class="wnt-form-check">
						<input name="oh" id="oh_searchInput" type="checkbox" class="wnt-form-control"
						 value="1" />
						<label for="oh_searchInput">Open Houses</label>
					</div>
					<div class="wnt-form-check">
						<input name="pr" id="pr_searchInput" type="checkbox" class="wnt-form-control"
						 value="1" />
						<label for="pr_searchInput">Price Reduced</label>
					</div>
				</fieldset>

				<fieldset class="wnt-form-group">
					<label>Sold Date</label>
					<select name="sa" id="sa_searchInput" class="wnt-form-control"
					 placeholder="Any Timeframe">
						<option value="">Any Timeframe</option>
						<option value="30">Last Month</option>
						<option value="60">Last 2 Months</option>
						<option value="90">Last 3 Months</option>
						<option value="150">Last 5 Months</option>
						<option value="180">Last 6 Months</option>
						<option value="365">Last 12 Months</option>
						<option value="548">Last 18 Months</option>
						<option value="720">Last 24 Months</option>
						<option value="912">Last 30 Months</option>
						<option value="1096">Last 36 Months</option>
					</select>
				</fieldset>

				<fieldset class="wnt-form-group">
					<label>MLS Status</label>
					<div class="wnt-form-check">
						<input name="ls" id="ls_active" type="checkbox" class="wnt-form-control"
						 value="Active" checked="checked" />
						<label for="ls_active">Active</label>
					</div>
					<div class="wnt-form-check">
						<input name="ls" id="ls_pending" type="checkbox" class="wnt-form-control"
						 value="Pending - Taking Backups" />
						<label for="ls_pending">Pending - Taking Backups</label>
					</div>
					<div class="wnt-form-check">
						<input name="ls" id="ls_contingent" type="checkbox" class="wnt-form-control"
						 value="Active Contingent" />
						<label for="ls_contingent">Active Contingent</label>
					</div>
					<div class="wnt-form-check">
						<input name="ls" id="ls_comingsoon" type="checkbox" class="wnt-form-control"
						 value="Coming Soon" />
						<label for="ls_comingsoon">Coming Soon</label>
					</div>
				</fieldset>

			</div>
		</div>

		<?php /*<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-status">
				Status
				<span class="wnt-badge wnt-search-criteria-count" title="1 selected">
					1
					<span class="screen-reader-text">selected</span>
				</span>
			</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-status">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">Property Status</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">Property Status</legend>
									<div class="wnt-form-check wnt-form-check-inline">
										<input type="radio" name="so" id="so0" value="" class="wnt-form-control"
										 autocomplete="off" checked="checked" />
										<label for="so0">Active Listings</label>
									</div>
									<div class="wnt-form-check wnt-form-check-inline">
										<input type="radio" name="so" id="so1" value="1" class="wnt-form-control"
										 autocomplete="off" />
										<label for="so1">Sold Listings</label>
									</div>
								</fieldset>
							</td>
						</tr>
						<tr class="wnt-sold-hidden">
							<th scope="row"><label for="ss_searchInput">Listed as</label></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">Listed as</legend>
									<div class="wnt-form-check">
										<input name="ss" id="ss_searchInput" type="checkbox" class="wnt-form-control"
										 value="1" />
										<label for="ss_searchInput">Short Sales</label>
									</div>
									<div class="wnt-form-check">
										<input name="fc" id="fc_searchInput" type="checkbox" class="wnt-form-control"
										 value="1" />
										<label for="fc_searchInput">Foreclosures</label>
									</div>
								</fieldset>
							</td>
						</tr>
						<tr class="wnt-sold-hidden">
							<th scope="row">Display Only</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">Display Only</legend>
									<div class="wnt-form-check">
										<input name="nl" id="nl_searchInput" type="checkbox" class="wnt-form-control"
										 value="1" />
										<label for="nl_searchInput">New Listings</label>
									</div>
									<div class="wnt-form-check">
										<input name="oh" id="oh_searchInput" type="checkbox" class="wnt-form-control"
										 value="1" />
										<label for="oh_searchInput">Open Houses</label>
									</div>
									<div class="wnt-form-check">
										<input name="pr" id="pr_searchInput" type="checkbox" class="wnt-form-control"
										 value="1" />
										<label for="pr_searchInput">Price Reduced</label>
									</div>
								</fieldset>
							</td>
						</tr>
						<tr class="wnt-sold-only">
							<th scope="row"><label for="sa_searchInput">Sold Date</label></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">Sold Date</legend>
									<select name="sa" id="sa_searchInput" class="wnt-form-control"
									 placeholder="Any Timeframe">
										<option value=""></option>
										<option value="30">Last Month</option>
										<option value="60">Last 2 Months</option>
										<option value="90">Last 3 Months</option>
										<option value="150">Last 5 Months</option>
										<option value="180">Last 6 Months</option>
										<option value="365">Last 12 Months</option>
										<option value="548">Last 18 Months</option>
										<option value="720">Last 24 Months</option>
										<option value="912">Last 30 Months</option>
										<option value="1096">Last 36 Months</option>
									</select>
								</fieldset>
							</td>
						</tr>
						<tr class="wnt-sold-hidden">
							<th scope="row">MLS Status</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">MLS Status</legend>
									<div class="wnt-form-check">
										<input name="ls" id="ls_active" type="checkbox" class="wnt-form-control"
										 value="Active" checked="checked" />
										<label for="ls_active">Active</label>
									</div>
									<div class="wnt-form-check">
										<input name="ls" id="ls_pending" type="checkbox" class="wnt-form-control"
										 value="Pending - Taking Backups" />
										<label for="ls_pending">Pending - Taking Backups</label>
									</div>
									<div class="wnt-form-check">
										<input name="ls" id="ls_contingent" type="checkbox" class="wnt-form-control"
										 value="Active Contingent" />
										<label for="ls_contingent">Active Contingent</label>
									</div>
									<div class="wnt-form-check">
										<input name="ls" id="ls_comingsoon" type="checkbox" class="wnt-form-control"
										 value="Coming Soon" />
										<label for="ls_comingsoon">Coming Soon</label>
									</div>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>*/ ?>

		<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-price">Price Range</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-price">
				<div class="wnt-form-group">
					<label for="pl_searchInput">Minimum Price</label>
					<select name="pl" id="pl_searchInput" class="wnt-form-control" placeholder="No Minimum Price">
						<option value="">No Minimum Price</option>
						<?php foreach($prices as $key => $value): ?>
							<option value="<?php echo esc_attr($value); ?>">$<?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="wnt-form-group">
					<label for="ph_searchInput">Maximum Price</label>
					<select name="ph" id="ph_searchInput" class="wnt-form-control" placeholder="No Maximum Price">
						<option value="">No Maximum Price</option>
						<?php foreach($prices as $key => $value): ?>
							<option value="<?php echo esc_attr($value); ?>">$<?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>

		<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-beds">Beds/Baths</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-beds">
				<fieldset class="wnt-form-row">
					<div class="wnt-form-group">
						<label for="be_searchInput">Beds</label>
						<select name="be" id="be_searchInput" class="wnt-form-control">
							<option value="">0+</option>
							<option value="1">1+</option>
							<option value="2">2+</option>
							<option value="3">3+</option>
							<option value="4">4+</option>
							<option value="5">5+</option>
							<option value="6">6+</option>
							<option value="7">7+</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="ba_searchInput">Baths</label>
						<select name="ba" id="ba_searchInput" class="wnt-form-control">
							<option value="">0+</option>
							<option value="1">1+</option>
							<option value="2">2+</option>
							<option value="3">3+</option>
							<option value="4">4+</option>
							<option value="5">5+</option>
							<option value="6">6+</option>
							<option value="7">7+</option>
						</select>
					</div>
				</fieldset>
			</div>
		</div>

		<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-type">Type</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-type">
				<fieldset class="wnt-form-group">
					<legend class="screen-reader-text">Property Type</legend>
					<div class="wnt-form-check">
						<input name="sfh" id="sfh_searchInput" type="checkbox" value="1" />
						<label for="sfh_searchInput">Single Family</label>
					</div>
					<div class="wnt-form-check">
						<input name="con" id="con_searchInput" type="checkbox" value="1">
						<label for="con_searchInput">Condo</label>
					</div>
					<div class="wnt-form-check">
						<input name="twh" id="twh_searchInput" type="checkbox" value="1">
						<label for="twh_searchInput">Townhouse</label>
					</div>
					<div class="wnt-form-check">
						<input name="mob" id="mob_searchInput" type="checkbox" value="1" />
						<label for="mob_searchInput">Mobile Home</label>
					</div>
					<div class="wnt-form-check">
						<input name="mtl" id="mtl_searchInput" type="checkbox" value="1" />
						<label for="mtl_searchInput">Multi Family</label>
					</div>
					<div class="wnt-form-check wnt-sold-hidden wnt-foreclosure-hidden">
						<input name="res" id="res_searchInput" type="checkbox" value="1" />
						<label for="res_searchInput">Residential Lease</label>
					</div>
					<div class="wnt-form-check">
						<input name="lta" id="lta_searchInput" type="checkbox" value="1" />
						<label for="lta_searchInput">Lot</label>
					</div>
					<div class="wnt-form-check">
						<input name="frm" id="frm_searchInput" type="checkbox" value="1" />
						<label for="frm_searchInput">Farms/Ranch/Acreage</label>
					</div>
					<div class="wnt-form-check">
						<input name="com" id="com_searchInput" type="checkbox" value="1" />
						<label for="com_searchInput">Commercial</label>
					</div>
					<div class="wnt-form-check wnt-sold-hidden wnt-foreclosure-hidden">
						<input name="coml" id="coml_searchInput" type="checkbox" value="1" />
						<label for="coml_searchInput">Commercial Lease</label>
					</div>
				</fieldset>
			</div>
		</div>

		<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-lifestyle">Lifestyle</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-lifestyle">
				<fieldset>
					<div class="wnt-form-group">
						<label for="sr_searchInput">School Rating</label>
						<select name="sr" id="sr_searchInput" class="wnt-form-control">
							<option value=""></option>
							<option value="8">Extremely Important</option>
							<option value="7">Very Important</option>
							<option value="6">Important</option>
							<option value="5">Less Important</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="cr_searchInput">Crime Rating</label>
						<select name="cr" id="cr_searchInput" class="wnt-form-control">
							<option value=""></option>
							<option value="4">Extremely Important</option>
							<option value="6">Very Important</option>
							<option value="8">Important</option>
							<option value="11">Less Important</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="mhi_searchInput">Median Income</label>
						<select name="mhi" id="mhi_searchInput" class="wnt-form-control">
							<option value=""></option>
							<option value="3">$30K+</option>
							<option value="4">$40K+</option>
							<option value="5">$50K+</option>
							<option value="6">$60K+</option>
							<option value="7">$70K+</option>
							<option value="8">$80K+</option>
							<option value="9">$90K+</option>
							<option value="10">$100K+</option>
							<option value="11">$110K+</option>
							<option value="12">$120K+</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="tn_searchInput">Neighborhood</label>
						<select name="tn" id="tn_searchInput" class="wnt-form-control">
							<option value=""></option>
							<option value="City Neighborhood">City Neighborhood</option>
							<option value="Inner City">Inner City</option>
							<option value="Rural">Rural</option>
							<option value="Small Town">Small Town</option>
							<option value="Suburban">Suburban</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="col_searchInput">Cost of Living</label>
						<select name="col" id="col_searchInput" class="wnt-form-control">
							<option value=""></option>
							<option value="18">Lower than Average</option>
							<option value="19">Average</option>
							<option value="20">Higher than Average</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="tt_searchInput">Commute Time</label>
						<select name="tt" id="tt_searchInput" class="wnt-form-control">
							<option value=""></option>
							<option value="1">&lt; 5 min.</option>
							<option value="2">&lt; 10 min.</option>
							<option value="3">&lt; 15 min.</option>
							<option value="4">&lt; 20 min.</option>
							<option value="5">&lt; 25 min.</option>
							<option value="6">&lt; 30 min.</option>
							<option value="7">&lt; 35 min.</option>
							<option value="8">&lt; 40 min.</option>
						</select>
					</div>
				</fieldset>
			</div>
		</div>

		<div class="wolfnet_box">
			<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-features">Features</h4>
			<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-features">
				<fieldset>
					<div class="wnt-form-group">
						<label for="sf_searchInput">Min Square Feet</label>
						<select name="sf" id="sf_searchInput" class="wnt-form-control" placeholder="Any Property Size">
							<option value="">Any Property Size</option>
							<?php foreach($sqft as $key => $value): ?>
								<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="ac_searchInput">Acreage</label>
						<select name="ac" id="ac_searchInput" class="wnt-form-control" placeholder="Any Lot Size">
							<option value="">Any Lot Size</option>
							<?php foreach($acres as $key => $value): ?>
								<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="bua_searchInput">Year Built</label>
						<input name="bua" id="bua_searchInput" type="number" value="" class="wnt-form-control"
						 min="1800" max="2019" placeholder="Min Year" />
						<span>to</span>
						<input name="bub" id="bub_searchInput" type="number" value="" class="wnt-form-control"
						 min="1800" max="2019" placeholder="Max Year" />
					</div>
					<div class="wnt-form-group">
						<label for="gs_searchInput">Garage/Carport</label>
						<select name="gs" id="gs_searchInput" class="wnt-form-control" placeholder="0+">
							<option value="">0+</option>
							<option value="1">1+</option>
							<option value="2">2+</option>
							<option value="3">3+</option>
							<option value="4">4+</option>
							<option value="5">5+</option>
							<option value="6">6+</option>
							<option value="7">7+</option>
							<option value="8">8+</option>
							<option value="9">9+</option>
						</select>
					</div>
					<div class="wnt-form-group">
						<label for="amenity_searchInput">Amenities</label>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-has_fireplace" type="checkbox" value="has_fireplace" />
							<label for="wnt-sbf-amenity-has_fireplace">Fireplace</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-has_golf" type="checkbox" value="has_golf" />
							<label for="wnt-sbf-amenity-has_golf">Near Golf Course</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-has_pool" type="checkbox" value="has_pool" />
							<label for="wnt-sbf-amenity-has_pool">Pool</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-gated_community" type="checkbox" value="gated_community" />
							<label for="wnt-sbf-amenity-gated_community">Gated Community</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-has_waterfront" type="checkbox" value="has_waterfront" />
							<label for="wnt-sbf-amenity-has_waterfront">Waterfront</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-custom7" type="checkbox" value="custom7" />
							<label for="wnt-sbf-amenity-custom7">Backs to Greenbelt</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-custom8" type="checkbox" value="custom8" />
							<label for="wnt-sbf-amenity-custom8">Backs to Golf Course</label>
						</div>
						<div class="wnt-form-check">
							<input name="amenity" id="wnt-sbf-amenity-custom16" type="checkbox" value="custom16" />
							<label for="wnt-sbf-amenity-custom16">Green Rating</label>
						</div>
					</div>
					<div class="wnt-form-group wnt-sold-hidden">
						<label for="c2_searchInput">Stories</label>
						<select name="c2" id="c2_searchInput" class="wnt-form-control" placeholder="All">
							<option value="">All</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="Multi-Leve">Multi-Level</option>
						</select>
					</div>
				</fieldset>
			</div>
		</div>

	</div>

	<div class="form-group redundant-controls">
		<button type="button" class="btn btn-default btn-close">Close</button>
		<button type="submit" class="btn btn-primary">
			<span class="icon icon-search"></span>
			<span>Search</span>
		</button>
		<a class="btn btn-default reset-btn" href="http://search.hometoaustx.com/reset" rel="nofollow">Reset</a>
	</div>


<script>

	var formId = '<?php echo $form_id; ?>';

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var $form = $('#' + formId)
			var $collapsible = $form.find('.wnt-criteria');

			$collapsible.wolfnetCollapsible();

		});

	}

</script>
