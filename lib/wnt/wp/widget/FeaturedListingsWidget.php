<?php

/**
 * This is the featuredListingsWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         featuredListingsWidget.php
 * @extends       com_wolfnet_wordpress_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
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
class WNT_WP_Widget_FeaturedListingsWidget
extends WNT_WP_Widget_AbstractWidget
{


    /* PROPERTIES ******************************************************************************* */

    /**
     * This property holds an array of different options that are available for each widget instance.
     *
     * @type  array
     *
     */
    public $options = array(
        'title'       => '',
        'description'  => 'Configure a scrollable list to feature your properties.',
        'direction'   => 'left',
        'autoplay'    => true,
        'speed'       => 5,
        'ownertype'   => 'agent_broker',
        'maxresults'  => 50
        );


    /**
     * This property holds an array of options for the widget admin form.
     *
     * @type  array
     *
     */
    public $controls = array(
        'width' => '300px'
        );


    /* CONSTRUCTOR METHOD *********************************************************************** */

    /**
     * This constructor method passes some key information up to the parent classes and eventionally
     * the information gets registered with the WordPress application.
     *
     * @return  void
     *
     */
    public function __construct()
    {
        parent::__construct( 'wolfnet_featuredListingsWidget', 'WolfNet Featured Listings' );
    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method is the primary output for the widget. This is the information the end user of the
     * site will see.
     *
     * @param   array  $args      An array of arguments passed to a widget.
     * @param   array  $instance  An array of widget instance data
     * @return  void
     *
     */
    public function widget($args, $instance)
    {
        $options = $this->getOptionData( $instance );

        $listingService = $this->fac->get('ListingService');

        $featuredListings = $listingService->getFeaturedListings(
            $options['ownertype']['value'],
            $options['maxresults']['value']
            );

        $data = array(
            'listings' => $featuredListings,
            'options'  => $options
            );

        $this->getFeaturedListingsView()->out( $data );

    }


    /**
     * This method is responsible for display of the widget instance form which allows configuration
     * of each widget instance in the WordPress admin.
     *
     * @param   array  $instance  An array of widget instance data
     * @return  void
     *
     */
    public function form($instance)
    {
        $listingService = $this->fac->get('ListingService');

        $data = array(
            'fields'     => $this->getOptionData( $instance ),
            'ownerTypes' => $listingService->getOwnerTypeData()
            );

        $this->getFeaturedListingsOptionsView()->out( $data );

    }


    /**
     * This method is responsible for saving any data that comes from the widget instance form.
     *
     * @param   array  $new_instance  An array of widget instance data from after the form submit
     * @param   array  $old_instance  An array of widget instance data from before the form submit
     * @return  array                 An array of data that needs to be saved to the database.
     *
     */
    public function update($new_instance, $old_instance)
    {
        /* processes widget options to be saved */
        $newData = $this->getOptionData( $new_instance );
        $saveData = array();

        foreach ( $newData as $opt => $data ) {
            $saveData[$opt] = strip_tags( $data['value'] );
        }

        return $saveData;

    }


}
