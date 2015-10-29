/**
 * This jQuery script defines the functionality of the WolfNet Shortcode Builder tinyMCE button.
 *
 * @title         jquery.wolfnetShortcodeBuilder.src.js
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
 *
 */

 /* Make sure the 'trim' function is available in the String object. Fix for older versions of IE. */
if ( typeof String.prototype.trim !== 'function' ) {
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
    }
}

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the
 * $ variable.
 */
jQuery(function($){


    var pluginName     = 'wolfnetShortcodeBuilder';
    var idPrefix       = 'wolfnetShortcodeBuilder_';
    var baseTitle      = 'WolfNet Shortcode Builder';
    var $builderDialog = null;
    var tinyMCE        = null;
    var $currentPage   = null;
    var $loader        = null;
    var menuItems      = {
        'agent' : {
            title:'Agent Pages',
            action:'wolfnet_scb_options_agent',
            shortcode:'wnt_agent'
        },
        'feat' : {
            title:'Featured Listings',
            action:'wolfnet_scb_options_featured',
            shortcode:'wnt_featured'
        },
        'grid' : {
            title:'Listing Grid',
            action:'wolfnet_scb_options_grid',
            shortcode:'wnt_grid'
        },
        'list' : {
            title:'Property List',
            action:'wolfnet_scb_options_list',
            shortcode:'wnt_list'
        },
        /*  Removing until requirements for this component are better fleshed out
        'summ' : {
            title:'Results Summary',
            action:'wolfnet_scb_results_summary',
            shortcode:'wnt_results'
        },
        */
        'srch' : {
            title:'Quick Search',
            action:'wolfnet_scb_options_quicksearch',
            shortcode:'wnt_search'
        }
    };


    var createBuilderDialog = function (options)
    {
        if ($builderDialog == null || !(typeof $builderDialog === 'jQuery')) {
            $builderDialog = $('<div>')
            .dialog({
                modal    :true,
                autoOpen :false,
                height   : 600,
                width    : 600,
                title    : baseTitle,
                close    : function () {
                    // When the dialog window is closed reset the page back to the menu.
                    openPage('menu');
                    // Also reset all forms within the builder back to their defaults.
                    $builderDialog.find('form').trigger('reset');
                },
                dialogClass: (options.useDialogClass=='true') ? 'wolfnet_dialog' : ''
            });
            createMenuPage();
            createLoader();
        }
    }


    var createMenuPage = function ()
    {

        // Figure out if we can display the Agent Pages option.
        // Note: This needs to NOT be asyncronous. 
        $.ajax( {
            url: ajaxurl,
            type: 'GET',
            dataType: 'json',
            async: false,
            data: {action:'wolfnet_scb_showagentfeature'},
            success: function (data) {
                if(data == false) {
                    delete menuItems.agent;
                }
            },
        });

        var menuString = '';

        for (var id in menuItems) {
            menuString += '<button class="button" style="display:block;width:75%;margin: 0px auto 10px auto;" ';
            menuString += 'wolfnet:id="' + id + '"';
            menuString += '>';
            menuString += menuItems[id].title;
            menuString += '</button>';
        }

        $currentPage = $('<div id="' + idPrefix + 'menu"/>')
            .append(menuString)
            .appendTo($builderDialog);

        $('<button id="' + idPrefix + 'back" class="button">Back</button>')
            .prependTo($builderDialog)
            .hide()
            .click(function(){
                openPage('menu');
            });

        $currentPage.find('button').click(function(){
            var $button = $(this);
            var pageId  = $button.attr('wolfnet:id');
            openPage(pageId);
        });

    }


    var createLoader = function ()
    {
        $loader = $('<div id="' + idPrefix + 'loader" />')
            .hide()
            .appendTo($builderDialog)
            .css({
                position:'absolute',
                top:0,
                left:0,
                width:'100%',
                height:'100%',
                backgroundColor:'white',
                opacity : 0.5
            });

        $('<img src="' + wolfnet_ajax.loaderimg + '" />')
            .appendTo($loader)
            .css({
                display  : 'block',
                position : 'absolute',
                left     : '49%',
                top      : '49%'
            });

    }


    var openPage = function ( pageId )
    {
        // If page doesn't exist create it.
        if ($builderDialog.find('div#' + idPrefix + pageId).length == 0) {
            createPage(pageId);
        }

        // Hide current page and display requested page.
        $currentPage.hide();

        if (pageId != 'menu') {
            $builderDialog.find('button#' + idPrefix + 'back').show();
            $builderDialog.dialog({title:baseTitle + ' - ' + menuItems[pageId].title});
        }
        else {
            $builderDialog.find('button#' + idPrefix + 'back').hide();
            $builderDialog.dialog({title:baseTitle});
        }

        // Get the requested page, set it as the current page, and show it.
        $currentPage = $builderDialog.find('div#' + idPrefix + pageId ).show();

        $currentPage.find('.modeField input').trigger('ready');

    }


    var createPage = function ( pageId )
    {
        var $page = $('<div id="' + idPrefix + pageId + '"/>').appendTo($builderDialog).hide();

        $.ajax( {
            url : ajaxurl,
            data : {action:menuItems[pageId].action},
            success: function (data) {
                var $form = $('<form />')
                .attr( 'wolfnet:sc', menuItems[pageId].shortcode )
                .append(data)
                .append($('<button type="submit" class="button button-primary" style="position:absolute;bottom:15px;right:15px;">Insert</button>'))
                .submit(function(event) {
                    event.preventDefault();
                    insertShortCode.call($form);
                    return false;
                })
                .appendTo($page);
                wolfnet.initMoreInfo($form.find('.wolfnet_moreInfo'));
                switch (pageId) {
                    case 'grid':
                    case 'list':
                        $form.wolfnetListingGridControls();
                        break;
                    case 'feat':
                        $form.wolfnetFeaturedListingsControls();
                        break;
                }
            },
            beforeSend : function () {
                $loader.show();
            },
            complete : function () {
                $loader.hide();
            }
        });

    }


    var insertShortCode = function ()
    {
        buildShortcode.call(this, function (shortcode) {
            if (tinyMCE != null) {
                tinyMCE.execCommand('mceInsertContent', false, shortcode);
            }
            $builderDialog.dialog('close');
        });
    }


    var buildShortcode = function (callback)
    {
        var attrs    = {};
        var code     = '[' + this.attr('wolfnet:sc') + ' /]';
        var exclAttr = ['mode','savedsearch','criteria'];
        var $advMode = this.find( 'input[type="radio"][name="mode"][value="advanced"]:first:checked' );
        var $savSrch = this.find( 'select[name="savedsearch"]:first' );

        this.find('input, select').each(function(){

            if (this.name != '' && $.inArray(this.name, exclAttr) == -1) {

                switch (this.type) {

                    default:
                        if (this.value.trim() != '') {
                            attrs[this.name] = this.value.trim();
                        }
                        break;

                    case 'checkbox':
                    case 'radio':
                        if (this.checked == true) {
                            attrs[this.name] = this.value;
                        }
                        break;

                }

            }

        });

        if ($advMode.length != 0 && $savSrch.length != 0) {

            delete attrs.zipcode;
            delete attrs.city;
            delete attrs.minprice;
            delete attrs.maxprice;

            $.ajax( {
                url: ajaxurl,
                type: 'GET',
                dataType: 'json',
                data: {action:'wolfnet_scb_savedsearch', id:$savSrch.val()},
                success: function (data) {
                    for (var field in data) {
                        attrs[field] = data[field];
                    }
                    buildShortcodeString(attrs, code, callback);
                },
                beforeSend : function () {
                    $loader.show();
                },
                complete: function () {
                    $loader.hide();
                }
            });

        }
        else {
            buildShortcodeString(attrs, code, callback);
        }

    };


    var buildShortcodeString = function (attrs, code, callback)
    {
        for (var attr in attrs) {
            code = code.replace('/]', ' ' + attr + '="' + attrs[attr] + '" /]');
        }

        callback(code);

    };


    var methods = {

        init : function (options)
        {
            var opt = {useDialogClass:true};
            $.extend(opt, options||{});
            loaderUri = opt.loaderUri||null;
            createBuilderDialog(opt);
        },

        open : function (editor)
        {
            $builderDialog.dialog('open');
            tinyMCE = editor||window.tineyMCE||null;
        }

    };


    $.fn[pluginName] = function (method)
    {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method ' +  method + ' does not exist in jQuery.' + pluginName);
        }
    };


    $(document).wolfnetShortcodeBuilder({useDialogClass:wolfnet_ajax.useDialogClass||'true'});


});
