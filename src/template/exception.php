<?php

/**
 *
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GNU v2
 *
 */

?>

<div class="wolfnet_error">
    <p>An error has occurred:</p>
    <strong>[<?php echo $exception->getCode(); ?>] <?php echo $exception->getMessage(); ?></strong>
    <a>More Info</a>
    <div class="wolfnet_more" style="display:none;">
        <p><?php echo $exception->getDetails(); ?></p>
        <pre><?php echo htmlentities(json_encode($exception->getData())); ?></pre>
    </div>
</div>
