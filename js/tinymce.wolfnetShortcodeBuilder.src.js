/**
 * This script is responsible for creating a button in the tinyMCE editor used in the post and page
 * editor. Only the button is created with this script the functionality of the button is managed in
 * js/jquery.wolfnetShortcodeBuilder.src.js
 *
 * @title         tinymce.wolfnetShortcodeBuilder.src.js
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
 *
 */

jQuery(function($){

    tinyMCE.create('tinymce.plugins.wolfnetShortcodeBuilder', {

        init : function(editor, url)
        {
            var wolfnetPluginUrl = url.substring(0, url.length - 2);

            // $(document).wolfnetShortcodeBuilder();

            editor.addButton('wolfnetShortcodeBuilderButton', {

                title   : 'WolfNet Shortcode Builder',

                /* since the URL automatically include the js directory we need to strip it off
                 * to get to the img directory. */
                image   : wolfnet_ajax.buildericon,

                onclick : function ()
                {
                    $(document).wolfnetShortcodeBuilder('open', editor);
                }

            });

        }
    });

    tinymce.PluginManager.add('wolfnetShortcodeBuilder', tinymce.plugins.wolfnetShortcodeBuilder);

    $.fn.wolfnetUpdateShortcodeControls = function (container)
    {

        var keyid = $(container).find('#keyid').val();

        $.ajax( {
            url: wolfnet_ajax.ajaxurl,
            data: { action:'wolfnet_price_range', keyid:keyid },
            dataType: 'json',
            type: 'GET',
            cache: false,
            timeout: 2500,
            statusCode: {
                404: function () {
                    commFailure();
                }
            },
            success: function ( data ) {
                var options = buildPriceDropdownOptions(data);
                $(container).find('.pricerange').html('');
                $(container).find('#maxprice').append($('<option />').attr('value', '').html('Max. Price'));
                $(container).find('#minprice').append($('<option />').attr('value', '').html('Min. Price'));
                $(options).each(function() {
                    $(container).find('.pricerange').append(this);
                });
            },
            error: function ( error ) {
                console.log(error);
            }
        } );

        $.ajax( {
            url: wolfnet_ajax.ajaxurl,
            data: { action:'wolfnet_saved_searches', keyid:keyid },
            dataType: 'json',
            type: 'GET',
            cache: false,
            timeout: 2500,
            statusCode: {
                404: function () {
                    commFailure();
                }
            },
            success: function ( data ) {
                var options = buildSavedSearchDropdownOptions(data);
                $(container).find('#savedsearch').html('');
                $(container).find('#savedsearch').append($('<option />').html('-- Saved Search --'));
                $(options).each(function() {
                    $(container).find('#savedsearch').append(this);
                });
            },
            error: function ( error ) {
                console.log(error);
            }
        } );

        $.ajax( {
            url: wolfnet_ajax.ajaxurl,
            data: { action:'wolfnet_map_enabled', keyid:keyid },
            dataType: 'json',
            type: 'GET',
            cache: false,
            timeout: 2500,
            statusCode: {
                404: function () {
                    commFailure();
                }
            },
            success: function ( data ) {
                if(data == true) {
                    $(container).find('#mapDisabled').css('display', 'none');
                    $(container).find('#maptype').removeAttr('disabled');
                } else {
                    $(container).find('#mapDisabled').css('display', 'block');
                    $(container).find('#maptype').attr('disabled', 'true');
                }
            },
            error: function ( error ) {
                console.log(error);
            }
        } );

        var buildPriceDropdownOptions = function(data) 
        {
            var options = [];
            $(data).each(function() {
                options.push(
                    $('<option />').attr('value', this.value).html(this.label)
                );
            });
            return options;
        }

        var buildSavedSearchDropdownOptions = function(data)
        {
            var options = [];
            $(data).each(function() {
                options.push(
                    $('<option />').attr('value', this.ID).html(this.post_title)
                );
            });
            return options;
        }

    }

});
