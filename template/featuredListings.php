<?php if (trim($title)!='') { ?>
    <h2><?php echo $title ?></h2>
<?php } ?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_featuredListings">
    <?php echo (isset($listingsHtml)) ? $listingsHtml : 'No Listings to Display.'; ?>
</div>

<script type="text/javascript">

    jQuery(function($){
        $('#<?php echo $instance_id; ?>').wolfnetScrollingItems({
              autoPlay  : <?php echo ($autoplay) ? 'true' : 'false'; ?>
            , direction : <?php echo "'" . $direction . "'"; ?>
            , speed     : <?php echo $speed; ?>
        });
    });

</script>
