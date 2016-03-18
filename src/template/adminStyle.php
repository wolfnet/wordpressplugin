<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h1>WolfNet <sup>&reg;</sup> - Appearance</h1>

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
                <div class="wolfnet_widget_theme">
                    <label for="wolfnet_widgetTheme_ash">
                        <div class="wolfnet_widget_theme_thumb">
                            <img src="<?php echo $imgdir; ?>support-ash-listing.png" />
                        </div>
                        <div class="wolfnet_widget_theme_label">
                            <input type="radio" name="wolfnet_widgetTheme"
                             id="wolfnet_widgetTheme_ash" value="ash"
                             <?php if (($widgetTheme == 'ash') || ($widgetTheme == '')) echo 'checked="checked"'; ?> />
                            Classic
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
                            Modern
                        </div>
                    </label>
                </div>
            </div>
        </fieldset>

        <div class="notice notice-warning below-h2">
            <p>
                Updating to the 'Modern' widget theme may cause display conflicts on your website.
                If you experience any conflicts, switch back to the 'Classic' widget theme
                and contact your web developer to correct these issues.
            </p>
        </div>

        <p class="submit">
            <?php submit_button(
                $text = NULL, $type = 'primary', $name = 'submit', $wrap = FALSE, $other_attributes = NULL
            ); ?>
        </p>

    </form>

</div>
