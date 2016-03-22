<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h2>WolfNet <sup>&reg;</sup> - Appearance</h2>

    <p>
        The 'Modern' widget theme streamlines the look of the property photos within
        "featured listings" and the "listing grid". With this optional feature enabled,
        property photos will appear larger and will only include the most important property
        listing details within the photo.
    </p>

    <form method="post" action="options.php">

        <?php echo $formHeader; ?>

        <h2>Widget Theme</h2>

        <p>Select the appearance of the widgets.</p>

        <fieldset>
            <legend class="screen-reader-text"><span>Widget Theme</span></legend>
            <div class="wolfnet_widget_themes">
            <?php for ($i=0; $i<count($widgetThemes); $i++) { ?>
                <div class="wolfnet_widget_theme">
                    <label for="wolfnet_widgetTheme_<?php echo $widgetThemes[$i] ?>">
                        <div class="wolfnet_widget_theme_thumb">
                            <img src="<?php echo $imgdir; ?>support-<?php echo $widgetThemes[$i] ?>-listing.png?v={X.X.X}" />
                        </div>
                        <div class="wolfnet_widget_theme_label">
                            <input type="radio" name="wolfnet_widgetTheme"
                             id="wolfnet_widgetTheme_<?php echo $widgetThemes[$i] ?>" value="ash"
                             <?php if (($widgetTheme == $widgetThemes[$i]) || ($widgetTheme == '')) echo 'checked="checked"'; ?> />
                            Classic
                        </div>
                    </label>
                </div>
            <?php } ?>
            </div>
        </fieldset>

        <div class="notice notice-warning below-h2">
            <p>
                Updating to the 'Modern' widget theme may cause display conflicts on your website.
                If you experience any conflicts, switch back to the 'Classic' widget theme
                and contact your web developer to correct these issues.
            </p>
        </div>

        <p style="clear: both;">
            <input type="submit" name="submit" id="submit" class="button button-primary"
             value="<?php _e('Save Changes') ?>" />
        </p>

    </form>

</div>
