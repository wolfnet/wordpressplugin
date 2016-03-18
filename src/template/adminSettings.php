<?php

/**
 *
 * @title         adminSettings.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
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

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h1>WolfNet <sup>&reg;</sup> - General Settings</h1>

    <form method="post" id="wolfnetSettings" action="options.php">

        <?php echo $formHeader; ?>

        <input type="hidden" id="wolfnet_setSslVerify" value="<?php echo $setSslVerify; ?>" />
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
                            if ($numrows < 1 ) {
                                $numrows = 1;
                                $productKey = array( (object) array('key' => '', 'market' => '', 'label' => '') );
                            }
                            for($i=1; $i<=$numrows; $i++):
                            ?>
                                <tr class="row<?php echo $i; ?>">
                                    <th scope="row">
                                        <label for="wolfnet_productKey_<?php echo $i; ?>">Product Key</label>
                                    </th>
                                    <th scope="row">
                                        Market Name
                                    </th>
                                    <th scope="row">
                                        <label for="wolfnet_keyLabel_<?php echo $i; ?>">Label<label>
                                    </th>
                                    <th scope="row"></th>
                                </tr>
                                <tr class="row<?php echo $i; ?>">
                                    <td>
                                        <input class="wolfnet_productKey"
                                         type="text" size="50"
                                         id="wolfnet_productKey_<?php echo $i; ?>"
                                         name="wolfnet_productKey_<?php echo $i; ?>"
                                         value="<?php echo $productKey[$i-1]->key; ?>" />
                                    </td>
                                    <td>
                                        <span class="wolfnet_keyMarket">
                                            <?php
                                                if (isset($productKey[$i-1]->market)) {
                                                    echo $productKey[$i-1]->market;
                                                }
                                            ?>
                                            <input class="wolfnet_keyMarket_value"
                                             type="hidden"
                                             id="wolfnet_keyMarket_<?php echo $i; ?>"
                                             name="wolfnet_keyMarket_<?php echo $i; ?>"
                                             value="<?php echo $productKey[$i-1]->market; ?>" />
                                        </span>
                                    </td>
                                    <td>
                                        <input class="wolfnet_keyLabel"
                                         type="text"
                                         id="wolfnet_keyLabel_<?php echo $i; ?>"
                                         name="wolfnet_keyLabel_<?php echo $i; ?>"
                                         value="<?php echo $productKey[$i-1]->label; ?>" size="30" />
                                    </td>
                                    <td>
                                        <?php if($i != 1): ?>
                                            <button class="button action wolfnet_deleteKey wnt-text-danger"
                                             type="button" data-wnt-key="<?php echo $i; ?>">
                                                <span class="wnt-icon wnt-icon-bin wnt-text-danger"></span>
                                                <?php _e('Delete'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <button type="button" class="button action" id="wolfnet_addKey">
                            <span class="wnt-icon wnt-icon-plus"></span>
                            <?php _e('Add Product Key'); ?>
                        </button>
                    </td>
                </tr>

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
                    <td>
                        <div>
                            <input name="<?php echo Wolfnet_Plugin::SSL_WP_OPTION; ?>"
                             id="wnt-<?php echo Wolfnet_Plugin::SSL_WP_OPTION; ?>"
                             <?php checked($sslEnabled, true); ?> type="checkbox" value="1" />
                            <label for="wnt-<?php echo Wolfnet_Plugin::SSL_WP_OPTION; ?>">
                                SSL Enabled
                            </label>
                        </div>
                        <div>
                            <small>
                                This option determines if the plugin will communicate with the API via
                                a secure connection. In the near future this option will be deprecated
                                and the API will only work over SSL.
                            </small>
                        </div>
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
                rootUri: '<?php echo site_url(); ?>?pagename=wolfnet-admin-validate-key',
                setSslVerify: $('#wolfnet_setSslVerify').val()
            } );


            $( '#wolfnetSettings' ).submit( function() {
                // Validate that all keys have labels.
                var valid = true;
                $('.wolfnet_keyLabel').each(function() {
                    // Remove error class if it was there before.
                    $(this).removeClass('error');

                    if($(this).val() == '') {
                        $(this).addClass('error');
                        valid = false;
                    }
                });
                if(!valid) {
                    $('.error')[0].focus();
                    alert('Please add a label for every product key.');
                    return false;
                }

                /* We need to collect the keys and associated labels from the form into a JSON string,
                then put that into a form variable to retain backwards compatibility. */
                var json = [];
                var itr = 1;
                $('.wolfnet_productKey').each(function() {
                    if($(this).val() != '') {
                        json.push({
                            "id" : itr,
                            "key" : $(this).val(),
                            "label" : $(this).closest('tr').find('.wolfnet_keyLabel').val(),
                            "market" : $(this).closest('tr').find('.wolfnet_keyMarket_value').val()
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
