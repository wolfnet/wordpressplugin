<?php

/**
 *
 * @title         Wolfnet_Views.php
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
 */

class Wolfnet_Views
{

    function __construct($wolfnet)
    {

        $this->wolfnet = $wolfnet;

    }

    /* Admin Menus ****************************************************************************** */
    /*                                                                                            */
    /*  /\   _| ._ _  o ._    |\/|  _  ._       _                                                 */
    /* /--\ (_| | | | | | |   |  | (/_ | | |_| _>                                                 */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function amSettingsPage()
    {
        ob_start(); settings_fields($this->optionGroup); $formHeader = ob_get_clean();
        $productKey = json_decode($this->getProductKey());

        // add the market name
        for($i=1; $i<=count($productKey); $i++) {
            $productKey[$i-1]->market = strtoupper($this->api->getMarketName($productKey[$i-1]->key));
        }

        include $this->woldnet->dir . 'template/adminSettings.php';

    }


    public function amEditCssPage()
    {
        ob_start(); settings_fields($this->CssOptionGroup); $formHeader = ob_get_clean();
        $publicCss = $this->getPublicCss();
        $adminCss = $this->getAdminCss();

        include 'template/adminEditCss.php';

    }


    public function amSearchManagerPage()
    {
        $key = (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1";
        $productkey = $this->getProductKeyById($key);

        if (!$this->api->productKeyIsValid($productkey)) {
            include 'template/invalidProductKey.php';
            return;
        }
        else {
            $searchForm = ($this->smHttp !== null) ? $this->smHttp['body'] : '';
            $markets = json_decode($this->getProductKey());
            $selectedKey = $key;
            include 'template/adminSearchManager.php';

        }

    }


    public function amSupportPage()
    {
        $imgdir = $this->url . 'img/';
        include 'template/adminSupport.php';

    }

    private function getPublicCss() 
    {
        return get_option(trim($this->publicCssOptionKey));

    }

    /**
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles()
    {
        $adminCss = $this->getAdminCss();
        echo '<style>' . $adminCss . '</style>';

    }

    public function getAdminCss() 
    {
        return get_option($this->adminCssOptionKey);

    }

    
}