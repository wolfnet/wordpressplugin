<?php

/**
 *
 * @title         QuickSearchWidget.php
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

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_QuickSearchWidget extends Wolfnet_AbstractWidget
{


    public $idBase = 'wolfnet_quickSearchWidget';

    public $name = 'WolfNet Quick Search';

    public $options = array(
        'description'  => 'Configure a quick search to include on your website.  When executed,
            the user is directed to matching properties within your WolfNet property search.'
        );


    public function widget($args, $instance)
    {
        $options = $this->getOptions($instance);

        echo $this->plugin->quickSearch($options);

    }


    public function form($instance)
    {
        $options = $this->getOptions($instance);

        echo $this->plugin->quickSearchOptionsFormView($options);

    }


    public function update($new_instance, $old_instance)
    {
        return parent::update($this->plugin->getQuickSearchDefaults(), $new_instance, $old_instance);

    }


    protected function getOptions($instance=null)
    {
        $options = $this->plugin->getQuickSearchOptions($instance);

        return parent::prepOptions($options);

    }


}
