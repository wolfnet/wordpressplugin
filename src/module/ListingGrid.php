<?php

/**
 * WolfNet Listing Grid module
 *
 * This module represents the listing grid and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_ListingGrid
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin, $views) {
        $this->plugin = $plugin;
        $this->views = $views;
    }


    public function sclistingGrid($attrs)
    {
        try {
            $default_maxrows = '50';
            $criteria = array_merge($this->getDefaults(), (is_array($attrs)) ? $attrs : array());

            // TODO: sort out all these max fields (also an alias in prepareListingQuery)
            if ($criteria['maxrows'] == $default_maxrows && $criteria['maxresults'] != $default_maxrows) {
                $criteria['maxrows'] = $criteria['maxresults'];
            }

            $this->plugin->decodeCriteria($criteria);

            $out = $this->listingGrid($criteria);

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function getDefaults()
    {

        return array(
            'title'       => '',
            'class'       => 'wolfnet_listingGrid ',
            'criteria'    => '',
            'ownertype'   => 'all',
            'maptype'     => 'disabled',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'maxrows'     => 50,
            'mode'        => 'advanced',
            'savedsearch' => '',
            'zipcode'     => '',
            'city'        => '',
            'exactcity'   => null,
            'minprice'    => '',
            'maxprice'    => '',
            'keyid'       => 1,
            'key'         => $this->plugin->keyService->getDefault(),
            'startrow'    => 1,
            );

    }


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        if (array_key_exists('keyid', $options) && $options['keyid'] != '') {
            $keyid = $options['keyid'];
        } else {
            $keyid = 1;
        }

        $options['mode_basic_wpc']        = checked($options['mode'], 'basic', false);
        $options['mode_advanced_wpc']     = checked($options['mode'], 'advanced', false);
        $options['paginated_false_wps']   = selected($options['paginated'], 'false', false);
        $options['paginated_true_wps']    = selected($options['paginated'], 'true', false);
        $options['sortoptions_false_wps'] = selected($options['sortoptions'], 'false', false);
        $options['sortoptions_true_wps']  = selected($options['sortoptions'], 'true', false);
        $options['ownertypes']            = $this->plugin->getOwnerTypes();
        $options['prices']                = $this->plugin->getPrices($this->plugin->keyService->getById($keyid));
        $options['savedsearches']         = $this->plugin->getSavedSearches(-1, $keyid);
        $options['mapEnabled']            = $this->plugin->getMaptracksEnabled($this->plugin->keyService->getById($keyid));
        $options['maptypes']              = $this->plugin->getMapTypes();

        return $options;

    }


    /**
     * Returns the markup for listings. generates both the listingGrid layout as well as the property list layout
     * @param  array  $criteria      the search criteria
     * @param  string $layout        'grid' or 'list'
     * @param  array  $dataOverride  listing data passed in to be used in place of the API request
     * @return string                listings markup
     */
    public function listingGrid(array $criteria, $layout = 'grid', $dataOverride = null)
    {
        $key = $this->plugin->keyService->getFromCriteria($criteria);

        if (!$this->plugin->keyService->isSaved($key)) {
            return false;
        }

        if($dataOverride === null) {
            if (!array_key_exists('numrows', $criteria)) {
                $criteria['maxrows'] = $criteria['maxresults'];
            }

            $qdata = $this->plugin->prepareListingQuery($criteria);

            try {
                $data = $this->plugin->apin->sendRequest($key, '/listing', 'GET', $qdata);
            } catch (Wolfnet_Exception $e) {
                return $this->plugin->displayException($e);
            }
        } else {
            // $dataOverride is passed in. As of writing this comment, this is data
            // is coming from the AgentPagesHandler - we need to display a listing
            // grid of an agent's featured listings. This is a vain attempt at 
            // repurposing this code as-is.
            $data = $dataOverride;
        }
        

        // add some elements to the array returned by the API
        // wpMeta should contain any criteria or other setting which do not come from the API
        $data['wpMeta'] = $criteria;

        $data['wpMeta']['total_rows'] = $data['responseData']['data']['total_rows'];

        $this->plugin->augmentListingsData($data, $key);

        $listingsData = array();

        if (is_array($data['responseData']['data'])) {
            $listingsData = $data['responseData']['data']['listing'];
        }

        $listingsHtml = '';

        if (!count($listingsData)) {
            $listingsHtml = 'No Listings Found.';
        } else {
            foreach ($listingsData as &$listing) {
                // do we need this ??
                $vars = array(
                   'listing' => $listing
                   );

                if ($layout=='list') {
                    $listingsHtml .= $this->views->listingBriefView($vars);

                } elseif ($layout=='grid') {
                    $listingsHtml .= $this->views->listingView($vars);
                }

            }

            $_REQUEST['wolfnet_includeDisclaimer'] = true;
        }

        $_REQUEST[$this->plugin->requestPrefix.'productkey'] = $key;

        // Keep a running array of product keys so we can output all necessary disclaimers
        if (!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }

        if (!in_array($_REQUEST[$this->plugin->requestPrefix.'productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST[$this->plugin->requestPrefix.'productkey']);
        }

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            // TODO: not needed?? we are merging $vars and listing data below.
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'wpMeta'             => $data['wpMeta'],
            'title'              => $data['wpMeta']['title'],
            'class'              => $criteria['class'],
            'mapEnabled'         => $this->plugin->getMaptracksEnabled($key),
            'map'                => '',
            'maptype'            => $data['wpMeta']['maptype'],
            'hideListingsTools'  => '',
            'hideListingsId'     => uniqid('hideListings'),
            'showListingsId'     => uniqid('showListings'),
            'collapseListingsId' => uniqid('collapseListings'),
            'toolbarTop'         => '',
            'toolbarBottom'      => '',
            'maxrows'            => ((count($listingsData) > 0) ? $data['requestData']['maxrows'] : 0),
            'gridalign'          => (array_key_exists('gridalign', $criteria)) ? $criteria['gridalign'] : 'center',
        );

        if (count($listingsData) && is_array($listingsData)) {
            $vars = $this->plugin->convertDataType(array_merge($vars, $listingsData));
        }

        if ($vars['wpMeta']['maptype'] != "disabled") {
            $vars['map'] = $this->plugin->getMap(
            	$listingsData, 
            	$_REQUEST[$this->plugin->requestPrefix.'productkey']
            );
            $vars['wpMeta']['maptype'] = $vars['maptype'];
            $vars['hideListingsTools'] = $this->plugin->getHideListingTools(
                $vars['hideListingsId'],
                $vars['showListingsId'],
                $vars['collapseListingsId'],
                $vars['instance_id']
            );
        }

        if (!array_key_exists('startrow', $vars['wpMeta'])) {
            $vars['wpMeta']['startrow'] = 1;
        }

        $vars['wpMeta']['paginated'] = ($vars['wpMeta']['paginated'] === true || $vars['wpMeta']['paginated'] === 'true');
        $vars['wpMeta']['sortoptions'] = ($vars['wpMeta']['sortoptions'] === true || $vars['wpMeta']['sortoptions'] === 'true');

        if ($vars['wpMeta']['paginated'] || $vars['wpMeta']['sortoptions']) {
            $vars['toolbarTop']    = $this->plugin->getToolbar($vars, 'wolfnet_toolbarTop ');
            $vars['toolbarBottom'] = $this->plugin->getToolbar($vars, 'wolfnet_toolbarBottom ');
        }

        if ($vars['wpMeta']['paginated']) {
            $vars['class'] .= 'wolfnet_withPagination ';
        }

        if ($vars['wpMeta']['sortoptions']) {
            $vars['class'] .= 'wolfnet_withSortOptions ';
        }

        // $layout='grid'
        if ($layout=='list') {
            // echo "propertyListView<br>";
            return $this->views->propertyListView($vars);
        } else {
            return $this->views->listingGridView($vars);
        }
    }
}