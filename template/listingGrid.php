<div id="<?php echo $instance_id; ?>" class="wolfnet_widget <?php echo $class; ?>">

    <?php if (trim($title) != '') { ?>
        <h2 class="widget-title"><?php echo $title; ?></h2>
    <?php } ?>

    <?php if ($paginated || $sortoptions) { echo $toolbarTop; } ?>

    <div class="wolfnet_listings">
        <?php echo ( isset($listingsHtml) ) ? $listingsHtml : 'No Listings to Display.'; ?>
    </div>

    <?php if ($paginated || $sortoptions) { echo $toolbarBottom; } ?>

</div>

<div class="wolfnet_clearfix"></div>

<script type="text/javascript">

    jQuery(function($){

        var instance = <?php echo "'#" . $instance_id . "';"; ?>

        $(instance).wolfnetToolbar({
             numrows     : <?php echo $numrows; ?>
            ,ownertype   : <?php echo "'" . $ownertype . "'"; ?>
            ,criteria    : <?php echo (trim($criteria)!='') ? $criteria : '{}'; ?>
            ,max_results : <?php echo $maxresults; ?>
            ,baseUrl     : <?php echo "'" . $siteUrl . "'"; ?>
        });

        <?php if (strpos($instance_id, 'wolfnet_listingGrid_') !== false) { ?>
        $(instance).wolfnetListingGrid();
        <?php } elseif (strpos($instance_id, 'wolfnet_propertyList_') !== false) { ?>
        $(instance).wolfnetPropertyList();
        <?php } ?>

    });

</script>
