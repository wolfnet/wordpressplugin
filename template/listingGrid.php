<div id="<?php echo $instance_id; ?>" class="wolfnet_widget <?php echo $class; ?>">

    <?php if (trim($title) != '') { ?>
        <h2 class="widget-title"><?php echo $title; ?></h2>
    <?php } ?>

    <?php if ($paginated) { echo $toolbarTop; } ?>

    <div class="wolfnet_listings">
        <?php echo ( isset($listingsHtml) ) ? $listingsHtml : 'No Listings to Display.'; ?>
    </div>

    <?php if ($paginated) { echo $toolbarBottom; } ?>

</div>

<div class="wolfnet_clearfix"></div>

<script type="text/javascript">

    jQuery(function($){

        var instance = '#<?php echo $instance_id; ?>';

        $(instance).wolfnetToolbar({
             numrows     : <?php echo $numrows; ?>
            ,ownerType   : <?php echo "'" . $ownertype . "'"; ?>
            ,criteria    : <?php echo (trim($criteria)!='') ? $criteria : '{}'; ?>
            ,max_results : <?php echo $maxresults; ?>
            ,baseUrl     : <?php echo "'" . $siteUrl . "'"; ?>
        });

        $(instance).wolfnetListingGrid();

    });

</script>
