<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h2>WolfNet <sup>&reg;</sup> - Appearance</h2>

    <p>Select the appearance of the widgets.</p>

    <form method="post" action="options.php">

        <?php echo $formHeader; ?>

        <h2>Widget Theme</h2>

        <fieldset>
            <legend class="screen-reader-text"><span>Widget Theme</span></legend>
            <div class="wolfnet_widget_themes">
                <div class="wolfnet_widget_theme">
                    <label for="wolfnet_widgetTheme_ash">
                        <div class="wolfnet_widget_theme_thumb">
                            <img src="<?php echo $imgdir; ?>support-ash-listing.png" />
                        </div>
                        <div class="wolfnet_widget_theme_label">
                            <input type="radio" name="wolfnet_widgetTheme"
                             id="wolfnet_widgetTheme_ash" value="ash"
                             <?php if (($widgetTheme == 'ash') || ($widgetTheme == '')) echo 'checked="checked"'; ?> />
                            Original
                        </div>
                    </label>
                </div>
                <div class="wolfnet_widget_theme">
                    <label for="wolfnet_widgetTheme_birch">
                        <div class="wolfnet_widget_theme_thumb">
                            <img src="<?php echo $imgdir; ?>support-birch-listing.png" />
                        </div>
                        <div class="wolfnet_widget_theme_label">
                            <input type="radio" name="wolfnet_widgetTheme"
                             id="wolfnet_widgetTheme_birch" value="birch"
                             <?php checked($widgetTheme, 'birch'); ?> />
                            Larger
                        </div>
                    </label>
                </div>
            </div>
        </fieldset>

        <p style="clear: both;">
            <input type="submit" name="submit" id="submit" class="button button-primary"
             value="<?php _e('Save Changes') ?>" />
        </p>

    </form>

</div>
