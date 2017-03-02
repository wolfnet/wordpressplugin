<?php

/**
 * WolfNet Search Manager module
 *
 * This module represents the agent pages feature and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_SearchManager
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }


	/**
     * This method is used to retrieve search solution HTML from an MLSFinder 2.5 search solution
     * for use as a 'search manager' interface in the WordPress admin.
     * @param  string $productKey The product key for the solution to be retrieved.
     * @return string             The HTML retrieved from the MLSFinder server.
     */
    public function searchManagerHtml($productKey = null)
    {
        global $wp_version;
        $http = array();

        $baseUrl = $this->plugin->data->getSearchManagerBaseUrl($productKey);
        //$maptracksEnabled = $this->plugin->data->getMaptracksEnabled($productKey);

        if (is_wp_error($baseUrl)) {
            $http['body'] = $this->plugin->getWpError($baseUrl);
            return $http;
        }

        if (!strstr($baseUrl, 'index.cfm')) {
            if (substr($baseUrl, strlen($baseUrl) - 1) != '/') {
                $baseUrl .= '/';
            }
            $baseUrl .= 'index.cfm';
        }

        $_GET['search_mode'] = 'form';

        $url = $baseUrl . ((!strstr($baseUrl, '?')) ? '?' : '');

        $url .= ((substr($url, -1) === '?') ? '' : '&' ) . 'action=wpshortcodebuilder';

        $resParams = array(
            'page',
            'action',
            'market_guid',
            'reinit',
            'show_header_footer'
            );

        foreach ($_GET as $param => $paramValue) {
            if (!array_search($param, $resParams)) {
            	$sanitizedParamValue = sanitize_text_field($paramValue);
                $sanitizedParamValue = urlencode($this->plugin->htmlEntityDecodeNumeric($sanitizedParamValue));
                $url .= "&{$param}={$sanitizedParamValue}";
            }
        }

        $reqHeaders = array(
            'cookies'    => $this->searchManagerCookies(),
            'timeout'    => 180,
            'user-agent' => 'WordPress/' . $wp_version,
            );

        $http = wp_remote_get($url, $reqHeaders);

        if (!is_wp_error($http)) {
            $http['request'] = array(
                'url' => $url,
                'headers' => $reqHeaders,
                );

            if ($http['response']['code'] == '200') {
                $this->searchManagerCookies($http['cookies']);
                $http['body'] = $this->removeJqueryFromHTML($http['body']);

                return $http;
            } else {
                //null returned on non-200; wperrors returned in all other error handling in this fctn
                return array('body' => '');
            }
        } else {
            $http['body'] = $this->plugin->getWpError($http);
            return $http;
        }

    }


	public function searchRelay($url='', $requestMethod='get', $params='')
	{

		if (strlen($params) > 0) {
			$urlHash = '';
			$urlHashPos = strpos($url, '#');
			if ($urlHashPos !== false) {
				$urlHash = substr($url, $urlHashPos, strlen($url) - $urlHashPos);
				$url = substr($url, 0, $urlHashPos + 1);
			}
			if (strpos($url, '?') === false) {
				$url .= '?';
			} else {
				$url .= '&';
			}
			$url .= $params . $urlHash;
		}

		$reqHeaders = array(
			'cookies'    => $this->searchManagerCookies(),
			'timeout'    => 180,
			'user-agent' => 'WordPress/' . $wp_version,
		);

		$http = wp_remote_get($url, $reqHeaders);

		if (!is_wp_error($http)) {

			$http['request'] = array(
				'url'     => $url,
				'headers' => $reqHeaders,
			);

			if ($http['response']['code'] == '200') {
				$this->searchManagerCookies($http['cookies']);
				//$http['body'] = $this->removeJqueryFromHTML($http['body']);
				return $http['body'];
			} else {
				return '';
			}

		} else {

			return $this->plugin->getWpError($http);

		}

	}


    public function getSavedSearches($count = -1, $keyid = null)
    {
        // Cache the data in the request scope so that we only have to query for it once per request.
        $cacheKey = 'wntSavedSearches';
		$data = wp_cache_get($cacheKey);

        if ($keyid == null) {
            $keyid = "1";
        }

		if (!$data) {
            $dataArgs = array(
                'numberposts' => $count,
                'post_type' => $this->plugin->customPostTypeSearch,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'keyid',
                        'value' => $keyid,
                    )
                )
            );

            $data = get_posts($dataArgs);

            if (count($data) == 0 && $keyid == 1) {
                /*
                 * This is for backwards compatibility - get posts without keyid meta query.
                 * We will loop through these custom posts and add the keyid meta key.
                 * Only do this on a keyid of 1 since that would be the default key back when we only allowed one.
                 */
                $dataArgs = array(
                    'numberposts' => $count,
                    'post_type' => $this->plugin->customPostTypeSearch,
                    'post_status' => 'publish',
                );

                $data = get_posts($dataArgs);

                foreach ($data as $post) {
                    add_post_meta($post->ID, 'keyid', 1);
                }

            }

			wp_cache_set($cacheKey, $data);

        }

        return $data;

    }


    public function getSavedSearch($id = 0)
    {
        $data = array();
        $customFields = get_post_custom($id);

        if ($customFields !== false) {
            foreach ($customFields as $field => $value) {
                if (substr($field, 0, 1) != '_') {
                    $data[$field] = $value[0];
                }
            }
        }

        return $data;

    }


    private function removeJqueryFromHTML($string)
    {
        return preg_replace('/(<script)(.*)(jquery\.min\.js)(.*)(<\/script>)/i', '', $string);
    }


    private function searchManagerCookies($cookies = null)
    {
        if (is_array($cookies)) {
            foreach ($cookies as $name => $value) {
                if ($value instanceof WP_Http_Cookie) {
                    $cookieArgs = array(
                        $value->name,
                        $value->value,
                        ($value->expires !== null && is_numeric($value->expires)) ? $value->expires : 0,
                        );

                    if ($value->path !== null) {
                        array_push($cookieArgs, $value->path);

                        if ($value->domain !== null) {
                            array_push($cookieArgs, $value->domain);
                        }

                    }

                    call_user_func_array('setcookie', $cookieArgs);

                } else {
                    setcookie($name, $value);
                }
            }

        }

        $cookies = array();

        foreach ($_COOKIE as $name => $value) {
            $cookie = new WP_Http_Cookie($name);
            $cookie->name = $name;
            $cookie->value = $value;
            array_push($cookies, $cookie);
        }

        return $cookies;

    }
}
