<div id="wolfnet_listing_<?php echo $listing->property_id; ?>" class="wolfnet_listing" itemscope>
    <a href="<?php echo $listing->property_url; ?>" title="<?php echo $listing->address . ' - ' . $listing->listing_price; ?>" rel="follow">
        <span class="wolfnet_full_address"><?php echo $listing->address; ?></span>
        <span class="wolfnet_price" itemprop="price"><?php echo $listing->listing_price; ?></span>
        <span itemprop="street-address" style="display:none;"><?php echo $listing->address; ?></span>
    </a>
</div>
