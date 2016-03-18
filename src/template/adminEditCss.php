<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br /></div>

    <h1>WolfNet <sup>&reg;</sup> - Edit CSS</h1>

    <p>This is a high-level tool that should only be used by a web developer or designer who is familiar with CSS.</p>

    <form method="post" action="options.php">

        <?php echo $formHeader; ?>

        <fieldset>

            <table class="form-table">

                <tr>
                    <th class="wolfnet_cssLabel"><label for="wolfnetCss_publicCss">Public CSS</label></th>
                </tr>

                <tr>
                    <td>
                        <p class="wolfnet_note">This CSS will affect the public-facing pages of your web site.</p>
                        <textarea id="wolfnetCss_publicCss" name="wolfnetCss_publicCss" class="wolfnet_cssBox"><?php echo $publicCss; ?></textarea>
                    </td>
                </td>

                <tr>
                    <th class="wolfnet_cssLabel"><label for="wolfnetCss_adminCss">Admin CSS</label></th>
                </tr>

                <tr>
                    <td>
                        <p class="wolfnet_note">This CSS will affect your Wordpress admin (the pages you are looking at now).</p>
                        <textarea id="wolfnetCss_adminCss" name="wolfnetCss_adminCss" class="wolfnet_cssBox"><?php echo $adminCss; ?></textarea>
                    </td>
                </tr>

            </table>

        </fieldset>

        <p class="submit">
            <?php submit_button(
                $text = NULL, $type = 'primary', $name = 'submit', $wrap = FALSE, $other_attributes = NULL
            ); ?>
        </p>

    </form>

</div>
