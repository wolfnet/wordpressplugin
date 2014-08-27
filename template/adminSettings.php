<?php

/**
 *
 * @title         adminSettings.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

?>

<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br></div>

    <h2>WolfNet <sup>&reg;</sup> - General Settings</h2>

    <form method="post" id="wolfnetSettings" action="options.php">

        <?php echo $formHeader; ?>

        <input type="hidden" id="wolfnet_keyCount" value="<?php echo count($productKey); ?>" />

        <fieldset>

            <legend><h3>General Settings</h3></legend>

            <table class="form-table" style="width:800px">
                <tr valign="top">
                    <td>
                        <table class="key-table" id="wolfnet_keys">
                            <?php
                            $numrows = count($productKey);
                            // we need to show at least one row of form fields so they can add a key if there are none
                            if ($numrows < 1 ) $numrows = 1;
                            for($i=1; $i<=$numrows; $i++):
                            ?>
                            <tr class="row<?php echo $i; ?>">
                                <th scope="row"><label for="wolfnet_productKey_<?php echo $i; ?>">Product Key</label></th>
                                <th scope="row">Market Name</th>
                                <th scope="row"><label for="wolfnet_keyLabel_<?php echo $i; ?>">Label<label></th>
                                <th scope="row"></th>
                            </tr>
                            <tr class="row<?php echo $i; ?>">
                                <td>
                                    <input id="wolfnet_productKey_<?php echo $i; ?>" name="wolfnet_productKey_<?php echo $i; ?>" type="text"
                                        value="<?php echo $productKey[$i-1]->key; ?>" class="wolfnet_productKey" size="50" />
                                </td>
                                <td><span class="wolfnet_keyMarket"><?php echo $productKey[$i-1]->market; ?></span></td>
                                <td>
                                    <input id="wolfnet_keyLabel_<?php echo $i; ?>" class="wolfnet_keyLabel" name="wolfnet_keyLabel_<?php echo $i; ?>" type="text" 
                                        value="<?php echo $productKey[$i-1]->label; ?>" size="30" />
                                </td>
                                <td>
                                    <?php if($i != 1): ?>
                                        <input type="button" wnt-key="<?php echo $i; ?>" class="wolfnet_deleteKey"
                                        value="<?php _e('Delete'); ?>" />
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </table>
                    </td>
                </tr>

                <?php /*
                <tr>
                    <td>
                        <input type="button" id="wolfnet_addKey" value="<?php _e('Add Product Key'); ?>" />
                    </td>
                </tr>
                */ ?>

                <tr valign="top">
                    <td>
                        <p class="description">
                            Enter your unique product key for the WolfNet WordPress plugin. The
                            product key is required to connect your WordPress site to your WolfNet
                            property search. WolfNet Plugin features will not be available until the
                            correct key has been entered. If you do not have a key, please contact
                            WolfNet Technologies via phone at 612-342-0088 or toll free at
                            1-866-WOLFNET, or via email at
                            <a href="mailto:service@wolfnet.com">service@wolfnet.com</a>.
                            You may also find us online at
                            <a href="http://wolfnet.com" target="_blank">WolfNet.com</a>.
                        </p>
                    </td>
                </tr>

                <tr valign="top">
                    <td class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    </td>
                </tr>

            </table>

        </fieldset>

    </form>

</div>

<script type="text/javascript">

    if ( typeof jQuery != 'undefined' ) {

        ( function ( $ ) {

            $( '.wolfnet_productKey' ).wolfnetValidateProductKey( {
                rootUri: '<?php echo site_url(); ?>?pagename=wolfnet-admin-validate-key'
            } );


            $( '#wolfnetSettings' ).submit( function() {
                /* We need to collect the keys and associated labels from the form into a JSON string,
                then put that into a form variable to retain backwards compatibility. */
                var json = [];
                var itr = 1;
                $('.wolfnet_productKey').each(function() {
                    if($(this).val() != '') {
                        json.push({
                            "id" : itr,
                            "key" : $(this).val(),
                            "label" : $(this).closest('tr').find('.wolfnet_keyLabel').val()
                        });
                        itr++;
                    }
                });
                var input = $('<input />').attr('name', 'wolfnet_productKey')
                    .attr('type', 'hidden')
                    .attr('value', JSON.stringify(json));
                $('#wolfnetSettings').append(input);
            } );


            $( '.wolfnet_deleteKey' ).click( function(button) {
                $.fn.wolfnetDeleteKeyRow(button);
            } );


            $( '#wolfnet_addKey' ).click( function() {
                $.fn.wolfnetInsertKeyRow();
            } );

        } )( jQuery );

    }

</script>
