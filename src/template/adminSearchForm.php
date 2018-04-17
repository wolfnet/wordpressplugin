<?php

?>

	<form id="<?php echo esc_attr($form_id); ?>" role="form"
	 action="<?php echo esc_attr($form_action); ?>" method="get">

		<fieldset class="open-search">

			<div class="form-group">
				<label class="sr-only">Search</label>
				<div class="input-group smart-search-input-container">
					<input autocomplete="off" name="q"
					 placeholder="Search by School, Area, Subdivision, Address/zip, School District, Area, County/city, and more!"
					 type="text"
					 style="border: 0px; padding: 0px; outline: none; min-width: 5em; width: 100%;" />
				</div>
			</div>

		</fieldset>

		<fieldset class="criteria">

			<fieldset class="status badge-group">
				<legend>
					<span class="badge">1</span>
					<span class="legend-label">Status</span>
					<span class="icon icon-triangle-down"></span>
				</legend>
				<div class="form-group property-status radio-switch">
					<label class="sr-only control-label">Property Status</label>
					<label class="status-selector checked">
						<input type="radio" name="so" id="so0" value="" class="no-badge" autocomplete="off" checked="checked">
						Active Listings
					</label>
					<label class="status-selector">
						<input type="radio" name="so" id="so1" value="1" class="no-badge" autocomplete="off">
						Sold Listings
					</label>
				</div>
				<div class="form-group listed-as sold-hidden">
					<label class="control-label">Listed As</label>
					<div class="checkbox">
						<label for="ss_searchInput">
							<input name="ss" id="ss_searchInput" type="checkbox" value="1">
							Short Sales
						</label>
					</div>
					<div class="checkbox">
						<label for="fc_searchInput">
							<input name="fc" id="fc_searchInput" type="checkbox" value="1">
							Foreclosures
						</label>
					</div>
				</div>
				<div class="form-group display-only sold-hidden">
					<label class="control-label">Display Only</label>
					<div class="checkbox">
						<label for="nl_searchInput">
							<input name="nl" id="nl_searchInput" type="checkbox" value="1">
							New Listings
						</label>
					</div>
					<div class="checkbox">
						<label for="oh_searchInput">
							<input name="oh" id="oh_searchInput" type="checkbox" value="1">
							Open Houses
						</label>
					</div>
					<div class="checkbox">
						<label for="pr_searchInput">
							<input name="pr" id="pr_searchInput" type="checkbox" value="1">
							Price Reduced
						</label>
					</div>
				</div>
				<div class="form-group sold-age sold-only">
					<label class="col-xs-4 control-label" for="sa_searchInput">Sold Date</label>
					<div class="sold-age-column">
						<select name="sa" id="sa_searchInput" data-placeholder="Any Timeframe">
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
					</div>
				</div>
				<div class="form-group mls-status sold-hidden">
					<label class="col-xs-5 control-label" for="ls_searchInput">MLS Status</label>
					<select name="ls" id="ls_searchInput" multiple="multiple" data-placeholder="Any MLS Status">
						<option value=""></option>
						<option value="All">All</option>
						<option value="Active" selected="selected">Active</option>
						<option value="Pending - Taking Backups">Pending - Taking Backups</option>
						<option value="Active Contingent">Active Contingent</option>
						<option value="Coming Soon">Coming Soon</option>
					</select>
				</div>
			</fieldset>

			<fieldset class="price values-label">
				<legend>
					<span class="legend-label">Price</span>
					<span class="icon icon-triangle-down"></span>
				</legend>
				<div class="form-group">
					<label class="col-xs-5 control-label" for="pl_searchInput">Minimum Price</label>
					<select name="pl" id="pl_searchInput" class="form-control input-sm" data-placeholder="No Minimum Price">
						<option value=""></option>
						<?php foreach($prices as $key => $value): ?>
							<option value="<?php echo esc_attr($value); ?>">$<?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label class="col-xs-5 control-label" for="ph_searchInput">Maximum Price</label>
					<select name="ph" id="ph_searchInput" class="form-control input-sm" data-placeholder="No Maximum Price">
						<option value=""></option>
						<?php foreach($prices as $key => $value): ?>
							<option value="<?php echo esc_attr($value); ?>">$<?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</fieldset>

			<fieldset class="beds select-dropdown">
				<legend>
					<span class="legend-value"></span>
					<span class="legend-label">Beds</span>
					<span class="icon icon-triangle-down"></span>
				</legend>
				<div class="form-group">
					<label class="col-xs-5 control-label" for="be_searchInput">Beds</label>
					<select name="be" id="be_searchInput" class="form-control input-sm">
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

			<fieldset class="baths select-dropdown">
				<legend>
					<span class="legend-value"></span>
					<span class="legend-label">Baths</span>
					<span class="icon icon-triangle-down"></span>
				</legend>
				<div class="form-group">
					<label class="col-xs-5 control-label" for="ba_searchInput">Baths</label>
					<select name="ba" id="ba_searchInput" class="form-control input-sm">
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

			<fieldset class="type badge-group">
				<legend>
					<span class="legend-label">Type</span>
					<span class="icon icon-triangle-down"></span>
				</legend>
				<div class="form-group">
					<label class="col-xs-5 control-label">Type</label>
					<div class="checkbox propertyType-single_family">
						<label for="sfh_searchInput">
							<input name="sfh" id="sfh_searchInput" type="checkbox" value="1" />
							Single Family
						</label>
					</div>
					<div class="checkbox propertyType-condo">
						<label for="con_searchInput">
							<input name="con" id="con_searchInput" type="checkbox" value="1">
							Condo
						</label>
					</div>
					<div class="checkbox propertyType-townhouse">
						<label for="twh_searchInput">
							<input name="twh" id="twh_searchInput" type="checkbox" value="1">
							Townhouse
						</label>
					</div>
					<div class="checkbox propertyType-mobile_home">
						<label for="mob_searchInput">
							<input name="mob" id="mob_searchInput" type="checkbox" value="1" />
							Mobile Home
						</label>
					</div>
					<div class="checkbox propertyType-multi_family">
						<label for="mtl_searchInput">
							<input name="mtl" id="mtl_searchInput" type="checkbox" value="1" />
							Multi Family
						</label>
					</div>
					<div class="checkbox propertyType-residential_lease sold-hidden foreclosure-hidden">
						<label for="res_searchInput">
							<input name="res" id="res_searchInput" type="checkbox" value="1" />
							Residential Lease
						</label>
					</div>
					<div class="checkbox propertyType-lots_acreage">
						<label for="lta_searchInput">
							<input name="lta" id="lta_searchInput" type="checkbox" value="1" />
							Lot
						</label>
					</div>
					<div class="checkbox propertyType-farm_hobby">
						<label for="frm_searchInput">
							<input name="frm" id="frm_searchInput" type="checkbox" value="1" />
							Farms/Ranch/Acreage
						</label>
					</div>
					<div class="checkbox propertyType-commercial">
						<label for="com_searchInput">
							<input name="com" id="com_searchInput" type="checkbox" value="1" />
							Commercial
						</label>
					</div>
					<div class="checkbox propertyType-commercial_lease sold-hidden foreclosure-hidden">
						<label for="coml_searchInput">
							<input name="coml" id="coml_searchInput" type="checkbox" value="1" />
							Commercial Lease
						</label>
					</div>
				</div>
			</fieldset>

			<fieldset class="more-criteria badge-group">

				<legend>
					<span class="legend-label">More</span>
					<span class="icon icon-triangle-down"></span>
				</legend>

				<fieldset class="lifestyle badge-group">
					<legend>
						<span class="legend-label">Lifestyle</span>
						<span class="icon icon-triangle-down"></span>
					</legend>
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
				</fieldset>

				<fieldset class="features badge-group">
					<legend>
						<span class="legend-label">More</span>
						<span class="icon icon-triangle-down"></span>
					</legend>
					<div class="form-group square_feet">
						<label class="col-xs-5 control-label" for="square_feet_searchInput">
							Min Square Feet
						</label>
						<select name="sf" id="square_feet_searchInput" class="form-control input-sm" data-placeholder="Any Property Size">
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
						<select name="ac" id="acres_searchInput" class="form-control input-sm" data-placeholder="Any Lot Size">
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
						<select name="gs" id="garage_searchInput" class="form-control input-sm" data-placeholder="0+">
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
						<select name="amenity" id="amenities_searchInput" class="form-control input-sm" multiple="multiple" data-placeholder="Any Amenities">
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

			</fieldset>

			<div class="form-group redundant-controls">
				<button type="button" class="btn btn-default btn-close">Close</button>
				<button type="submit" class="btn btn-primary">
					<span class="icon icon-search"></span>
					<span>Search</span>
				</button>
				<a class="btn btn-default reset-btn" href="http://search.hometoaustx.com/reset" rel="nofollow">Reset</a>
			</div>

		</fieldset>

	</form>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

		});

	}

</script>
