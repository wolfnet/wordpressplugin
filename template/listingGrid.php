<div id="<?php echo $instance_id; ?>" class="wolfnet_widget <?php echo $class; ?>">

    <?php if (trim($title) != '') { ?>
        <h2 class="widget-title"><?php echo $title; ?></h2>
    <?php } ?>

    <?php echo $toolbarTop; ?>

    <div class="wolfnet_listings">
        <?php echo (isset($listingsHtml)) ? $listingsHtml : 'No Listings to Display.'; ?>
    </div>

    <?php echo $toolbarBottom; ?>

</div>

<div class="wolfnet_clearfix"></div>

<script type="text/javascript">

    jQuery(function($){
        var instance = <?php echo "'#" . $instance_id . "';"; ?>

        $(instance).wolfnetToolbar({
             numrows     : <?php echo $numrows . "\n"; ?>
            ,ownertype   : <?php echo "'" . $ownertype . "'" . "\n"; ?>
            ,criteria    : <?php echo ((trim($criteria)!='') ? $criteria : '{}')  . "\n"; ?>
            ,max_results : <?php echo $maxresults . "\n"; ?>
            ,baseUrl     : <?php echo "'" . $siteUrl . "'" . "\n"; ?>
        });
        $(instance).filter('.wolfnet_listingGrid').wolfnetListingGrid();
        $(instance).filter('.wolfnet_propertyList').wolfnetPropertyList();

    });

</script>
