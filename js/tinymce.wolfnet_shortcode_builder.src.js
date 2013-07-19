/**
 * This script is responsible for creating a button in the tinyMCE editor used in the post and page
 * editor. Only the button is created with this script the functionality of the button is managed in
 * js/jquery.wolfnet_shortcode_builder.src.js
 *
 * @package       js
 * @title         tinymce.wolfnet_shortcode_builder.src.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
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

            $(document).wolfnetShortcodeBuilder();

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

});
