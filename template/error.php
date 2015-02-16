<?php

/**
 *
 * @title         error.php
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

// a list of error codes contained in the wp_error object
$codes = $error->get_error_codes()

?>

<div class="wolfnet_error">
    An error has occured:<br>
    <?php
    foreach ($codes as $code) {
        $data = $error->get_error_data($code);

        if ( isset($data['body']) && $api_responce = json_decode($data['body']) ) {
            // if we have a clean error resopnce from the api server
            $msg = $api_responce->metadata->status->message;
            $msg_long = $api_responce->metadata->status->extendedInfo;
            $err_id = $api_responce->metadata->status->error_id;
        } elseif ( isset($data['response']) && is_array($data['response']) ) {
            // if we generated and without getting a responce from the api server
            $msg = $data['response']['code'];
            $msg_long = print_r($data, true);
            $err_id = $data['response']['message'];

        }  else {
            // if we don't have any data with this error.
            $msg = $error->get_error_message($code);
            $msg_long = "$code : $msg";
            $err_id = $code;   
        }
    
        echo "<span>$msg</span><br>";
        echo '<a>More Info</a>';
        echo '<div class="wolfnet_more" style="display:none;">';
        // echo '<div>';
        echo $msg_long . "<br>";
        echo "Error ID: $err_id";
        echo "</div>";
    }
    ?>
</div>
