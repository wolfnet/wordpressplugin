<?php

/**
 *
 * @title         featuredListingsOptions.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_featuredListingsOptions">

    <input id="<?php echo $direction_wpid; ?>" name="<?php echo $direction_wpname; ?>" type="hidden"
     class="wolfnet_featuredListingsOptions_dirField" />

    <?php if(count($markets) == 1): ?>
        <input type="hidden" id="<?php echo $keyid_wpid; ?>" name="<?php echo $keyid_wpid; ?>" value="1" />
    <?php endif; ?>

    <table class="form-table">
        <tr>
            <th>
                <label for="wnt-<?php echo $title_wpid; ?>">Title:</label>
            </th>
            <td>
                <input id="wnt-<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" value="<?php echo $title; ?>" type="text" />
            </td>
        </tr>

        <?php if(count($markets) > 1): ?>
            <tr>
                <th>
                    <label for="<?php echo $keyid_wpid; ?>">Market:</label>
                </th>
                <td>
                    <select id="<?php echo $keyid_wpid; ?>" name="<?php echo $keyid_wpname; ?>">
                        <?php for($i=0; $i<=count($markets)-1; $i++): ?>
                        <option value="<?php echo $markets[$i]->id; ?>"
                         <?php if($markets[$i]->id == $keyid) echo ' selected="selected"'?>>
                            <?php echo $markets[$i]->label; ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <th>
                <label for="<?php echo $autoplay_wpid; ?>">Scroll Control:</label>
            </th>
            <td>
                <select id="<?php echo $autoplay_wpid; ?>" name="<?php echo $autoplay_wpname; ?>"
                    class="wolfnet_featuredListingsOptions_autoPlayField">
                    <option value="true"<?php echo $autoplay_true_wps; ?>>Automatic &amp; Manual</option>
                    <option value="false"<?php echo $autoplay_false_wps; ?>>Manual Only</option>
                </select>
                <span class="wolfnet_moreInfo">
                    Define user control of the scrolling featured properties. When set to Automatic
                    &amp; Manual, featured properties scroll upon page load, but the user can
                    override the animation via controls displayed on hover. When set to Manual Only,
                    the user must activate animation via controls displayed on hover.
                </span>
            </td>
        </tr>



        <tr class="wolfnet_featuredListingsOptions_autoPlayOptions">
            <td colspan="2">

                <fieldset>

                    <legend>Automatic Playback Options</legend>

                    <table class="form-table">

                        <tr>
                            <th>
                                <label for="wnt-<?php echo $direction_wpid; ?>">Direction:</label>
                            </th>
                            <td>
                                <select id="wnt-<?php echo $direction_wpid; ?>" name="<?php echo $direction_wpname; ?>"
                                 class="wolfnet_featuredListingsOptions_autoDirField">
                                    <option value="right"<?php echo $direction_right_wps; ?>>Left to Right</option>
                                    <option value="left"<?php echo $direction_left_wps; ?>>Right to Left</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="<?php echo $speed_wpid; ?>">Animation Speed:</label>
                            </th>
                            <td>
                                <select id="<?php echo $speed_wpid; ?>" name="<?php echo $speed_wpname; ?>">
                                    <?php foreach ( $speedsettings as $setting ) { ?>
                                        <option value="<?php echo $setting['value']; ?>"<?php selected($speed, $setting['value']); ?>>
                                            <?php echo $setting['label']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="wolfnet_moreInfo">
                                    Set the speed for the scrolling animation.
                                </span>
                            </td>
                        </tr>

                    </table>

                </fieldset>

            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo $ownertype_wpid; ?>">Agent/Broker:</label>
            </th>
            <td>
                <select id="<?php echo $ownertype_wpid; ?>" name="<?php echo $ownertype_wpname; ?>">
                    <?php foreach ( $ownertypes as $ot ) { ?>
                        <option value="<?php echo $ot['value']; ?>"<?php selected($ownertype, $ot['value']); ?>>
                            <?php echo $ot['label']; ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="wolfnet_moreInfo">
                    Define the properties to be featured. Appropriate properties are included as
                    indicated by the name of the option (ie, Agent Then Broker, Agent Only, Broker
                    Only).
                </span>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?php echo $maxresults_wpid; ?>">Max Results:</label>
            </th>
            <td>
                <input id="<?php echo $maxresults_wpid; ?>" name="<?php echo $maxresults_wpname; ?>"
                 value="<?php echo $maxresults; ?>" type="text" size="2" maxlength="2" />
                <span class="wolfnet_moreInfo">
                    Define the number of properties to be featured.  The maximum number of
                    properties that can be included is 50.
                </span>
            </td>
        </tr>

    </table>

</div>

<script type="text/javascript">

    jQuery(function($){
        $('#<?php echo $instance_id; ?>').wolfnetFeaturedListingsControls();
        wolfnet.initMoreInfo( $( '#<?php echo $instance_id; ?> .wolfnet_moreInfo' ) );
    });

</script>
