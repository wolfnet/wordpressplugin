<?php

/**
 * @title         Wolfnet_Views.php
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

class Wolfnet_Views
{

    /* PROPERTIES ******************************************************************************* */

    /**
     * location of images hosted remotely
     * @var string
     */
    private $remoteImages = '//common.wolfnet.com/wordpress/';


    /* CONSTRUCTOR ****************************************************************************** */

    public function __construct()
    {
        $this->templateDir = dirname(__FILE__) . '/template';
    }


    /* Public Methods *************************************************************************** */

    public function amSettingsPage()
    {

        try {
            $productKey = json_decode($GLOBALS['wolfnet']->keyService->get());

            // add the market name
            for ($i=1; $i<=count($productKey); $i++) {
                $key = $productKey[$i-1]->key;

                try {
                    $validKey = $GLOBALS['wolfnet']->keyService->isValid($key);
                } catch (Wolfnet_Api_ApiException $e) {
                    $validKey = false;
                }

                if ($validKey) {
                    try {
                        $market = $GLOBALS['wolfnet']->data->getMarketName($key);

                    } catch (Wolfnet_Api_ApiException $e) {
                        // Catch the error and display no market
                        // TODO: We may want to display an error about this.
                        $market = '';
                    }

                } else {
                    $market = '';
                }

                if (!is_wp_error($market)) {
                    $productKey[$i-1]->market = strtoupper($market);
                }

            }

            $sslEnabled = $GLOBALS['wolfnet']->getSslEnabled();

            $out = $this->parseTemplate('adminSettings', array(
                'formHeader' => $this->settingsFormHeaders(),
                'productKey' => $productKey,
                'sslEnabled' => $sslEnabled,
                'setSslVerify' => (!get_option(Wolfnet_Plugin::VERIFYSSL_WP_OPTION)) ? 'true' : 'false',
            ));

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


	public function amStylePage()
	{

		try {

			$args = array(
				'url'               => $GLOBALS['wolfnet']->url,
				'colorOptionsUrl'   => admin_url('admin.php?page=wolfnet_plugin_colors'),
				'colorOut'          => $this->amColor(),
				'themeOut'          => $this->amTheme(),
				'sampleAgent'       => '',
				'sampleOffice'      => '',
			);

			if ($GLOBALS['wolfnet']->agentPages->showAgentFeature()) {
				$args['sampleAgent'] = $this->agentBriefView(array(
					'agent'        => $GLOBALS['wolfnet']->agentPages->getSampleAgent(),
					'agentLink'    => '#',
					'contactLink'  => '#',
				));
				$args['sampleOffice'] = $this->officeBriefView(array(
					'office'             => $GLOBALS['wolfnet']->agentPages->getSampleOffice(),
					'officeLink'         => '#',
					'contactLink'        => '#',
					'searchLink'         => '#',
					'searchResultLink'   => '#',
				));
			}

			$out = $this->parseTemplate('adminStyle', $args);

		} catch (Wolfnet_Exception $e) {
			$out = $this->exceptionView($e);
		}

		echo $out;

		return $out;

	}


	public function amTheme()
	{

		try {

			$themeDefaults = $GLOBALS['wolfnet']->widgetTheme->getDefaults();
			$sampleListing = $GLOBALS['wolfnet']->listings->getSample();

			$out = $this->parseTemplate('adminThemes', array(
				'imgdir'              => $this->remoteImages,
				'formHeader'          => $this->styleFormHeaders(),
				'widgetTheme'         => $this->getWidgetTheme(),
				'widgetThemes'        => $GLOBALS['wolfnet']->widgetTheme->getThemeOptions(),
				'defaultWidgetTheme'  => $themeDefaults['widgetTheme'],
				'sampleListing'       => $this->listingView(array( 'listing' => $sampleListing )),
			));

		} catch (Wolfnet_Exception $e) {
			$out = $this->exceptionView($e);
		}

		return $out;

	}


	public function amColor()
	{

		try {

			$themeDefaults = $GLOBALS['wolfnet']->widgetTheme->getDefaults();
			$sampleListing = $GLOBALS['wolfnet']->listings->getSample();

			$args = array(
				'imgdir'         => $this->remoteImages,
				'formHeader'     => $this->colorFormHeaders(),
				'widgetTheme'    => $this->getWidgetTheme(),
				'themeColors'    => $this->getThemeColors(),
				'themeOpacity'   => $this->getThemeOpacity(),
			);

			$out = $this->parseTemplate('adminColors', $args);

		} catch (Wolfnet_Exception $e) {
			$out = $this->exceptionView($e);
		}

		return $out;

	}


    public function amEditCssPage()
    {

        try {
            $out = $this->parseTemplate('adminEditCss', array(
                'formHeader' => $this->cssFormHeaders(),
                'publicCss' => $this->getPublicCss(),
                'adminCss' => $GLOBALS['wolfnet']->admin->getAdminCss(),
            ));

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


    public function amSearchManagerPage()
    {

        try {
            $productKey = $GLOBALS['wolfnet']->keyService->getById($_SESSION['keyid']);

            if (!$GLOBALS['wolfnet']->keyService->isValid($productKey)) {
                $out = $this->parseTemplate('invalidProductKey');
            } else {
                $out = $this->parseTemplate('adminSearchManager', array(
                    'searchForm' => ($GLOBALS['wolfnet']->smHttp !== null) ? $GLOBALS['wolfnet']->smHttp['body'] : '',
                    'markets' => json_decode($GLOBALS['wolfnet']->keyService->get()),
                    'selectedKey' => $_SESSION['keyid'],
                    'url' => $GLOBALS['wolfnet']->url,
                    'baseUrl' => $GLOBALS['wolfnet']->data->getSearchManagerBaseUrl($productKey),
                ));

            }

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


	public function amSearchPage () {

		try {
			$productKey = $GLOBALS['wolfnet']->keyService->getById($_SESSION['keyid']);
			if (!$GLOBALS['wolfnet']->keyService->isValid($productKey)) {
				$out = $this->parseTemplate('invalidProductKey');
			} else {
				$out = $this->parseTemplate('adminSearch', array(
					'searchForm'   => $this->amSearch(),
					'markets'      => json_decode($GLOBALS['wolfnet']->keyService->get()),
					'selectedKey'  => $_SESSION['keyid'],
					'url'          => $GLOBALS['wolfnet']->url,
					'baseUrl'      => $GLOBALS['wolfnet']->data->getSearchManagerBaseUrl($productKey),
				));
			}
		} catch (Wolfnet_Exception $e) {
			$out = $this->exceptionView($e);
		}

		echo $out;

		return $out;

	}


	public function amSearch()
	{

		try {

			$themeDefaults = $GLOBALS['wolfnet']->widgetTheme->getDefaults();
			$sampleListing = $GLOBALS['wolfnet']->listings->getSample();

			$out = $this->parseTemplate('adminSearchForm', array(
				'form_id'  => $GLOBALS['wolfnet']->createUUID(),
				'prices'   => array(250,500,750,1000,1500,2000,2500,5000,10000,20000,30000,40000,50000,60000,70000,75000,80000,85000,90000,95000,100000,110000,120000,130000,140000,150000,160000,170000,180000,190000,200000,210000,220000,230000,240000,250000,260000,270000,280000,290000,300000,310000,320000,330000,340000,350000,360000,370000,380000,390000,400000,410000,420000,430000,440000,450000,460000,470000,480000,490000,500000,510000,520000,530000,540000,550000,560000,570000,580000,590000,600000,610000,620000,630000,640000,650000,660000,670000,680000,690000,700000,710000,720000,730000,740000,750000,760000,770000,780000,790000,800000,810000,820000,830000,840000,850000,860000,870000,880000,890000,900000,910000,920000,930000,940000,950000,960000,970000,980000,990000,1000000,1100000,1200000,1300000,1400000,1500000,1600000,1700000,1800000,1900000,2000000,2500000,3000000,3500000,4000000,4500000,5000000,6000000,8000000,10000000),
				'sqft'     => array(600,800,1000,1200,1400,1600,1800,2000,2250,2500,2750,3000,3500,4000,5000,6000,7000,8000,9000,10000,11000,12000,13000,14000,15000),
				'acres'    => array(.1,.25,.33,.5,1,2,3,4,5,6,7,8,9,10,15,25,50,100,250,500),
			));

		} catch (Wolfnet_Exception $e) {
			$out = $this->exceptionView($e);
		}

		return $out;

	}


    public function amSupportPage()
    {

        try {
            $out = $this->parseTemplate('adminSupport', array(
                'imgdir' => $this->remoteImages,
            ));

        } catch (Wolfnet_Exception $e) {
            $out = $this->exceptionView($e);
        }

        echo $out;

        return $out;

    }


    public function getPublicCss()
    {
        return get_option(trim($GLOBALS['wolfnet']->publicCssOptionKey));
    }


	public function getWidgetTheme()
	{
		$themeDefaults = $GLOBALS['wolfnet']->widgetTheme->getDefaults();
		return get_option(trim($GLOBALS['wolfnet']->widgetThemeOptionKey), $themeDefaults['widgetTheme']);
	}


	public function getThemeColors()
	{
		$themeDefaults = $GLOBALS['wolfnet']->widgetTheme->getDefaults();
		$themeColors = get_option(trim($GLOBALS['wolfnet']->themeColorsOptionKey), $themeDefaults['colors']);

		// Set default values
		foreach ($themeColors as $key => $color) {
			if (strlen($color) == 0) {
				$themeColors[$key] = $themeDefaults['colors'][$key];
			}
		}

		return $themeColors;

	}


	public function getThemeOpacity()
	{
		$themeDefaults = $GLOBALS['wolfnet']->widgetTheme->getDefaults();
		$themeOpacity = get_option(trim($GLOBALS['wolfnet']->themeOpacityOptionKey), $themeDefaults['opacity']);

		if (!is_numeric($themeOpacity)) {
			$themeOpacity = $themeDefaults['opacity'];
		}

		return $themeOpacity;

	}


	public function getThemeStyleArgs(array $args = array())
	{
		$styleOptions = array(
			'colors'    => (array_key_exists('colors', $args)  ? $args['colors']  : $this->getThemeColors()),
			'opacity'   => (array_key_exists('opacity', $args) ? $args['opacity'] : $this->getThemeOpacity()),
		);

		return $GLOBALS['wolfnet']->widgetTheme->getStyleArgs($styleOptions);

	}


    /**
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles()
    {
        $adminCss = $GLOBALS['wolfnet']->getAdminCss();
        echo '<style>' . $adminCss . '</style>';

    }


    public function agentPagesOptionsFormView(array $args = array())
    {
        $offices = $GLOBALS['wolfnet']->data->getOffices();
        $offices = $offices['responseData']['data']['office'];
        $keyids = array();

        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', uniqid('wolfnet_agentPages_')),
            'offices'         => $offices,
            'keyids'          => $keyids,
        );

        $args = array_merge($defaultArgs, $args);

		if (count($offices) <= 1) {
			$args['showoffices'] = false;
		}

        return $this->parseTemplate('agentPagesOptions', $args);

    }


    public function featuredListingsOptionsFormView(array $args = array())
    {
        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', 'wolfnet_featuredListing_' . $GLOBALS['wolfnet']->createUUID()),
            'markets'         => json_decode($GLOBALS['wolfnet']->keyService->get()),
        );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('featuredListingsOptions', $args);

    }


    public function listingGridOptionsFormView(array $args = array())
    {
        $defaultArgs = array(
            'instance_id'      => str_replace('.', '', 'wolfnet_listingGrid_' . $GLOBALS['wolfnet']->createUUID()),
            'markets'          => json_decode($GLOBALS['wolfnet']->keyService->get()),
            'keyid'            => ''
        );

        $args = array_merge($defaultArgs, $args);

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->parseTemplate('listingGridOptions', $args);

    }


    public function quickSearchOptionsFormView(array $args = array())
    {
        $markets = json_decode($GLOBALS['wolfnet']->keyService->get());
        $view = '';

        $args['keyids'] = explode(',', $args['keyids']);

        $defaultArgs = array(
            'instance_id' => str_replace('.', '', 'wolfnet_quickSearch_' . $GLOBALS['wolfnet']->createUUID()),
            'markets'     => $markets,
            'view'        => $view,
        );


        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('quickSearchOptions', $args);

    }


    public function listingView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingView', $this->parseTemplate('listing', $args));

    }


    public function listingBriefView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingBriefView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingBriefView', $this->parseTemplate('briefListing', $args));

    }


    public function listingResultsView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingResultsView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingResultsView', $this->parseTemplate('resultsListing', $args));

    }


    public function agentsNavView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesNavView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesNavView', $this->parseTemplate('agentPagesNav', $args));

    }


    public function agentsListView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesListAgents', $args));
    }


    public function officesListView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesListOffices', $args));
    }


    public function agentView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesShowAgent', $args));
    }


    public function agentBriefView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesAgentBrief', $args));

    }


    public function officeBriefView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesOfficeBrief', $args));

    }


    public function agentContact(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesContactAgent', $args));
    }


    public function officeContact(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_agentPagesView_' . $key, $item);
        }

        return apply_filters('wolfnet_agentPagesView', $this->parseTemplate('agentPagesContactOffice', $args));
    }


    public function featuredListingView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_featuredListingView_' . $key, $item);
        }

        return apply_filters('wolfnet_featuredListingView', $this->parseTemplate('featuredListings', $args));

    }


    public function propertyListView(array $args = array())
    {
        if (!array_key_exists('keyid', $args)) {
            $args['productkey'] = $GLOBALS['wolfnet']->keyService->getDefault();
        } else {
            $args['productkey'] = $GLOBALS['wolfnet']->keyService->getById($args['keyid']);
        }

        $args['itemsPerPage'] = $GLOBALS['wolfnet']->data->getItemsPerPage();

        $data = $GLOBALS['wolfnet']->api->sendRequest($args['productkey'], '/search_criteria/sort_option');
        $args['sortOptions'] = $data['responseData']['data']['options'];
		$args['defaultSort'] = $data['responseData']['data']['default'];

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_propertyListView_' . $key, $item);
        }

        // Ensure the styles for property list are used instead of listing grid styles.
        $args['class'] = str_replace('wolfnet_listingGrid', 'wolfnet_propertyList', $args['class']);

        return apply_filters('wolfnet_propertyListView', $this->parseTemplate('propertyList', $args));

    }


    public function listingGridView(array $args = array())
    {

        if (!array_key_exists('keyid', $args)) {
            $args['productkey'] = $GLOBALS['wolfnet']->keyService->getDefault();
        } else {
            $args['productkey'] = $GLOBALS['wolfnet']->keyService->getById($args['keyid']);
        }

        $args['itemsPerPage'] = $GLOBALS['wolfnet']->data->getItemsPerPage();

        $data = $GLOBALS['wolfnet']->api->sendRequest($args['productkey'], '/search_criteria/sort_option');
        $args['sortOptions'] = $data['responseData']['data']['options'];
		$args['defaultSort'] = $data['responseData']['data']['default'];

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingGridView_' . $key, $item);
        }

        return apply_filters('wolfnet_listingGridView', $this->parseTemplate('listingGrid', $args));

    }


	public function quickSearchView(array $args = array())
	{

		// array containing possible values for 'view' arg
		$views = array('basic', 'legacy');

		//set up a custom css class for the wrapper. default 'wolfnet_quickSearch_legacy'
		$args['viewclass'] = 'wolfnet_quickSearch_' . (in_array($args['view'], $views) ? $args['view'] : 'legacy');

		foreach ($args as $key => $item) {
			$args[$key] = apply_filters('wolfnet_quickSearchView_' . $key, $item);
		}

		return apply_filters(
			'wolfnet_quickSearchView',
			$this->parseTemplate('quickSearch', $args)
		);

	}


    public function smartSearchView(array $args = array()) {
        $args['smartsearchInput'] = uniqid('wolfnet_smartsearch_');

        return apply_filters(
            'wolfnet_smartSearchView',
            $this->parseTemplate('smartSearch', $args)
        );

    }


    public function mapView($listingsData, $productKey = null)
    {
        $args = $GLOBALS['wolfnet']->data->getMapParameters($listingsData, $productKey);
        $args['url'] = $GLOBALS['wolfnet']->url;

        return apply_filters('wolfnet_mapView', $this->parseTemplate('map', $args));

    }


    public function hideListingsToolsView($hideId, $showId, $collapseId, $instance_id)
    {
        $args['hideId'] = $hideId;
        $args['showId'] = $showId;
        $args['collapseId'] = $collapseId;
        $args['instance_id'] = $instance_id;

        return apply_filters('wolfnet_hideListingsTools', $this->parseTemplate('hideListingsTools', $args));

    }


    public function toolbarView(array $args = array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_toolbarView_' . $key, $item);
        }

        return apply_filters('wolfnet_toolbarView', $this->parseTemplate('toolbar', $args));

    }


    public function errorView($error)
    {
        return $this->parseTemplate('error', array('error'=>$error));
    }


    public function exceptionView(Wolfnet_Exception $exception)
    {
        return $this->parseTemplate('exception', array('exception'=>$exception));
    }


    public function houseOver($args)
    {
        return $this->parseTemplate('listingHouseover', $args);
    }


    /* PRIVATE METHODS ************************************************************************** */

    private function parseTemplate($template, array $vars = array())
    {
		// Load theme option values for use in rendering
		$vars['widgetThemeName']   = $this->getWidgetTheme();
		$vars['widgetThemeClass']  = 'wolfnet-theme-' . $vars['widgetThemeName'];
		$vars['themeColors']       = $this->getThemeColors();
		$vars['themeOpacity']      = $this->getThemeOpacity();

        extract($vars, EXTR_OVERWRITE);

        ob_start();

        include $this->templateDir . '/' . $template . '.php';

        return trim(ob_get_clean());

    }


    private function styleFormHeaders()
    {
        ob_start();

        settings_fields($GLOBALS['wolfnet']->WidgetThemeOptionGroup);

        return trim(ob_get_clean());

    }


    private function colorFormHeaders()
    {
        ob_start();

        settings_fields($GLOBALS['wolfnet']->ColorOptionGroup);

        return trim(ob_get_clean());

    }


    private function cssFormHeaders()
    {
        ob_start();

        settings_fields($GLOBALS['wolfnet']->CssOptionGroup);

        return trim(ob_get_clean());

    }


    private function settingsFormHeaders()
    {
        ob_start();

        settings_fields($GLOBALS['wolfnet']->optionGroup);

        return trim(ob_get_clean());

    }


}
