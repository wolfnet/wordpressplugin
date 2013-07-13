<div id="wolfnet_listing_<?php echo $listing->property_id; ?>" class="wolfnet_listing" itemscope>
    <a href="<?php echo $listing->property_url; ?>" rel="follow">
        <span class="wolfnet_listingImage"><img src="<?php echo $listing->photo_url; ?>" alt="Property for sale at <?php echo $listing->address; ?>" /></span>
        <span class="wolfnet_price" itemprop="price"><?php echo $listing->listing_price; ?></span>
        <span class="wolfnet_bed_bath" title="<?php echo $listing->bedsbaths_full; ?>"><?php echo $listing->bedsbaths; ?></span>
        <span title="<?php echo $listing->address; ?>">
            <span class="wolfnet_address"><?php echo $listing->display_address; ?></span>
            <span class="wolfnet_location" itemprop="locality"><?php echo $listing->location; ?></span>
            <span class="wolfnet_full_address" itemprop="street-address" style="display:none;"><?php echo $listing->address; ?></span>
        </span>
        <?php if (property_exists($listing, 'branding') && ($listing->branding->brokerLogo != '' || $listing->branding->content != '')) { ?>
        <div class="wolfnet_branding">
            <span class="wolfnet_brokerLogo"><img src="<?php echo $listing->branding->brokerLogo; ?>" /></span>
            <span class="wolfnet_brandingMessage"><?php echo $listing->branding->content; ?></span>
        </div>
        <?php } ?>
    </a>
</div>
