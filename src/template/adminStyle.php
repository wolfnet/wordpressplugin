<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h2>WolfNet <sup>&reg;</sup> - Appearance</h2>

    <p>Select the appearance of the widgets.</p>

    <form method="post" action="options.php">

        <?php echo $formHeader; ?>

        <fieldset>

            <table class="form-table">

                <tr>
                    <th>Widget Theme</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Widget Theme</span></legend>
                            <label for="wolfnet_widgetTheme_acanthite">
                                <input type="radio" name="wolfnet_widgetTheme"
                                 id="wolfnet_widgetTheme_acanthite" value="acanthite"
                                 <?php if (($widgetTheme == 'acanthite') || ($widgetTheme == '')) echo 'checked="checked"'; ?> />
                                Original
                            </label>
                            <br />
                            <label for="wolfnet_widgetTheme_bismuth">
                                <input type="radio" name="wolfnet_widgetTheme"
                                 id="wolfnet_widgetTheme_bismuth" value="bismuth"
                                 <?php checked($widgetTheme, "bismuth"); ?> />
                                Larger
                            </label>
                        </fieldset>
                    </td>
                </td>

                <tr>
                    <td colspan="2" class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary"
                         value="<?php _e('Save Changes') ?>" />
                    </td>
                </tr>

            </table>

        </fieldset>

    </form>

</div>
