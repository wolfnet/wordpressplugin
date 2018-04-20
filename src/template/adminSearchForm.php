<?php

?>

<form id="<?php echo esc_attr($form_id); ?>" role="form" method="get" class="wnt-form"
 action="<?php echo esc_attr($form_action); ?>">

	<div class="wolfnet_box">
		<div class="wolfnet_boxContent">
			<div class="wnt-form-group">
				<label for="q_searchInput">Search</label>
				<input name="q" id="q_searchInput" type="text" autocomplete="off" class="wnt-form-control large-text"
				 placeholder="Search by School, Area, Subdivision, Address/zip, School District, Area, County/city, and more!" />
			</div>
		</div>
	</div>

	<div class="wnt-criteria wolfnet_box">

		<h3 class="wnt-collapsible-trigger">Filters</h3>

		<div class="wolfnet_boxContent">

			<div class="wolfnet_box">
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
			</div>

			<div class="wolfnet_box">
				<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-price">Price Range</h4>
				<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-price">
					<label for="pl_searchInput" class="screen-reader-text">Minimum Price</label>
					<select name="pl" id="pl_searchInput" class="wnt-form-control" placeholder="No Minimum Price">
						<option value=""></option>
						<?php foreach($prices as $key => $value): ?>
							<option value="<?php echo esc_attr($value); ?>">$<?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
					&ndash;
					<label for="ph_searchInput" class="screen-reader-text">Maximum Price</label>
					<select name="ph" id="ph_searchInput" class="wnt-form-control" placeholder="No Maximum Price">
						<option value=""></option>
						<?php foreach($prices as $key => $value): ?>
							<option value="<?php echo esc_attr($value); ?>">$<?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="wolfnet_box">
				<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-beds">Beds/Baths</h4>
				<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-beds">
					<fieldset class="wnt-form-row">
						<legend class="screen-reader-text">Beds/Baths</legend>
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
					<fieldset>
						<legend class="screen-reader-text">Type</legend>

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
					<div class="form-group school_rating">
						<label class="col-xs-4 control-label" for="school_rating_searchInput">
							School Rating
						</label>
						<div class="col-xs-8">
							<select name="sr" id="school_rating_searchInput" class="form-control input-sm">
								<option value=""></option>
								<option value="8">Extremely Important</option>
								<option value="7">Very Important</option>
								<option value="6">Important</option>
								<option value="5">Less Important</option>
							</select>
						</div>
					</div>
					<div class="form-group crime_rating">
						<label class="col-xs-4 control-label" for="crime_rating_searchInput">
							Crime Rating
						</label>
						<select name="cr" id="crime_rating_searchInput" class="form-control input-sm">
							<option value=""></option>
							<option value="4">Extremely Important</option>
							<option value="6">Very Important</option>
							<option value="8">Important</option>
							<option value="11">Less Important</option>
						</select>
					</div>
					<div class="form-group median_household_income">
						<label class="col-xs-4 control-label" for="median_household_income_searchInput">
							Median Income
						</label>
						<select name="mhi" id="median_household_income_searchInput" class="form-control input-sm">
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
					<div class="form-group type_of_neighborhood">
						<label class="col-xs-4 control-label" for="type_of_neighborhood_searchInput">
							Neighborhood
						</label>
						<select name="tn" id="type_of_neighborhood_searchInput" class="form-control input-sm">
							<option value=""></option>
							<option value="City Neighborhood">City Neighborhood</option>
							<option value="Inner City">Inner City</option>
							<option value="Rural">Rural</option>
							<option value="Small Town">Small Town</option>
							<option value="Suburban">Suburban</option>
						</select>
					</div>
					<div class="form-group cost_of_living">
						<label class="col-xs-4 control-label" for="cost_of_living_searchInput">
							Cost of Living
						</label>
						<select name="col" id="cost_of_living_searchInput" class="form-control input-sm">
							<option value=""></option>
							<option value="18">Lower Than Average</option>
							<option value="19">Average</option>
							<option value="20">Higher Than Average</option>
						</select>
					</div>
					<div class="form-group commute_time">
						<label class="col-xs-4 control-label" for="commute_time_searchInput">
							Commute Time
						</label>
						<select name="tt" id="commute_time_searchInput" class="form-control input-sm">
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
				</div>
			</div>

			<div class="wolfnet_box">
				<h4 class="wnt-collapsible-trigger" data-wnt-target="#wnt-criteria-features">Features</h4>
				<div class="wolfnet_boxContent wnt-collapsible wnt-collapse-default" id="wnt-criteria-features">
					<fieldset class="features badge-group">
						<legend>
							<span class="legend-label">More</span>
							<span class="icon icon-triangle-down"></span>
						</legend>
						<div class="form-group square_feet">
							<label class="col-xs-5 control-label" for="square_feet_searchInput">
								Min Square Feet
							</label>
							<select name="sf" id="square_feet_searchInput" class="form-control input-sm" placeholder="Any Property Size">
								<option value=""></option>
								<?php foreach($sqft as $key => $value): ?>
									<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group acres">
							<label class="col-xs-5 control-label" for="acres_searchInput">
								Acreage
							</label>
							<select name="ac" id="acres_searchInput" class="form-control input-sm" placeholder="Any Lot Size">
								<option value=""></option>
								<?php foreach($acres as $key => $value): ?>
									<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group year-built">
							<label class="col-xs-5 control-label" for="built_after_searchInput">
								Year Built
							</label>
							<input name="bua" id="built_after_searchInput" type="number" value="" class="form-control input-sm" placeholder="Min Year" />
							<span>to</span>
							<input name="bub" id="built_before_searchInput" type="number" value="" class="form-control input-sm" placeholder="Max Year" />
						</div>
						<div class="form-group garage">
							<label class="col-xs-5 control-label" for="garage_searchInput">
								Garage/Carport
							</label>
							<select name="gs" id="garage_searchInput" class="form-control input-sm" placeholder="0+">
								<option value=""></option>
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
						<div class="form-group amenities">
							<label class="col-xs-5 control-label" for="amenities_searchInput">
								Amenities
							</label>
							<select name="amenity" id="amenities_searchInput" class="form-control input-sm" multiple="multiple" placeholder="Any Amenities">
								<option value="has_fireplace">Fireplace</option>
								<option value="has_golf">Near Golf Course</option>
								<option value="has_pool">Pool</option>
								<option value="gated_community">Gated Community</option>
								<option value="has_waterfront">Waterfront</option>
								<option value="custom7">Backs to Greenbelt</option>
								<option value="custom8">Backs to Golf Course</option>
								<option value="custom16">Green Rating</option>
							</select>
						</div>
						<div class="form-group custom2 sold-hidden">
							<label class="col-xs-5 control-label" for="custom2_searchInput">
								Stories
							</label>
							<select name="c2" id="custom2_searchInput" class="form-control input-sm" placeholder="All">
								<option value="" class="placeholder">All</option>
								<option value=""></option>
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

	</div>

	<div class="form-group redundant-controls">
		<button type="button" class="btn btn-default btn-close">Close</button>
		<button type="submit" class="btn btn-primary">
			<span class="icon icon-search"></span>
			<span>Search</span>
		</button>
		<a class="btn btn-default reset-btn" href="http://search.hometoaustx.com/reset" rel="nofollow">Reset</a>
	</div>

</form>


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
