<?php

/**
 *
 * @title         AgentPagesWidget.php
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

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_Widget_AgentPagesWidget extends Wolfnet_Widget_AbstractWidget
{


    public $idBase = 'wolfnet_agentPagesWidget';

    public $name = 'WolfNet Agent Pages';

    public $options = array(
        'description'  => 'Configure agent and office pages to include on your website.'
        );


    public function widget($args, $instance)
    {

        try {
            $response = $this->plugin->agentPageHandler($instance);

        } catch (Wolfnet_Api_ApiException $e) {
            $response = $this->plugin->displayException($e);
        }

        echo $args['before_widget'] . $response . $args['after_widget'];

    }


    public function form($instance)
    {
        $options = $this->getOptions($instance);
        $options['showSoldOption'] = $this->plugin->soldListingsEnabled();

        echo $this->plugin->views->agentPagesOptionsFormView($options);

    }


    public function update($new_instance, $old_instance)
    {
        return parent::updateWithDefault($this->plugin->getAgentPagesDefaults(), $new_instance, $old_instance);

    }


    protected function getOptions($instance = null)
    {
        $options = $this->plugin->getAgentPagesOptions($instance);

        return parent::prepOptions($options);

    }


}
