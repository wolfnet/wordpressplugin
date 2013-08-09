<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br></div>

    <h2>WolfNet - General Settings</h2>

    <form method="post" action="options.php">

        <?php echo $formHeader; ?>

        <fieldset>

            <legend><h3>General Settings</h3></legend>

            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><label for="wolfnet_productKey">Product Key</label></th>
                    <td>
                        <input id="wolfnet_productKey" name="wolfnet_productKey" type="text"
                            value="<?php echo $productKey; ?>" size="50" />
                        <p class="description" style="width:400px;">
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
                    <th scope="row">&nbsp;</th>
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

            $( '#wolfnet_productKey' ).wolfnetValidateProductKey( {
                rootUri: '<?php echo site_url(); ?>?pagename=wolfnet-admin-validate-key'
            } );

        } )( jQuery );

    }

</script>