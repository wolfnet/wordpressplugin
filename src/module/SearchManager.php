<?php

/**
 * WolfNet Search Manager module
 *
 * This module represents the agent pages feature and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
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
                $http['body'] = $this->removeStyleTag($http['body']);
                $http['body'] = $this->replaceStyleBoxClasses($http['body']);

                return $http;
            } else {
                //null returned on non-200; wperrors returned in all other error handling in this fctn
                return array('body' => '');
            }
        } else {
			return array('body' => $this->plugin->getWpError($http));
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


    public function getSavedSearches($count=-1, $keyid=null)
    {
        // Cache the data in the request scope so that we only have to query for it once per request.
        $cacheKey = 'wntSavedSearches';
		$data = wp_cache_get($cacheKey, 'wnt');

		if (!$data) {

            $dataArgs = array(
                'numberposts' => $count,
                'post_type' => $this->plugin->customPostTypeSearch,
                'post_status' => 'publish'
			);

			if ($keyid != null) {
				$dataArgs['meta_query'] = array(
					array(
						'key' => 'keyid',
						'value' => $keyid,
					)
				);
			}

            $data = get_posts($dataArgs);

			/*
			* This is for backwards compatibility:
			* We will loop through these custom posts and add the keyid meta key if it is missing.
			* Only do this on a keyid of 1 since that would be the default key back when we only allowed one.
			*/
			foreach ($data as $post) {
				$post_keyid = (array) get_post_meta($post->ID, 'keyid');
				if (empty($post_keyid)) {
					add_post_meta($post->ID, 'keyid', 1);
				}
			}

			wp_cache_set($cacheKey, $data, 'wnt', 1);

        }

        return $data;

    }


	public function getSavedSearchesArray ($count=-1, $keyid=null) {
		$search_posts = $this->getSavedSearches($count, $keyid);
		$searches = array();

		foreach ($search_posts as $search_post) {
			$search_keyid = get_post_meta($search_post->ID, 'keyid');
			if (is_array($search_keyid)) {
				$search_keyid = (int) $search_keyid[0];
			}
			$search_key = $this->plugin->keyService->getKeyById($search_keyid);
			array_push($searches, array(
				'ID'                     => $search_post->ID,
				'post_author'            => $search_post->post_author,
				'post_date'              => $search_post->post_date,
				'post_date_gmt'          => $search_post->post_date_gmt,
				'post_content'           => $search_post->post_content,
				'post_title'             => $search_post->post_title,
				'post_excerpt'           => $search_post->post_excerpt,
				'post_status'            => $search_post->post_status,
				'comment_status'         => $search_post->comment_status,
				'ping_status'            => $search_post->ping_status,
				'post_password'          => $search_post->post_password,
				'post_name'              => $search_post->post_name,
				'to_ping'                => $search_post->to_ping,
				'pinged'                 => $search_post->pinged,
				'post_modified'          => $search_post->post_modified,
				'post_modified_gmt'      => $search_post->post_modified_gmt,
				'post_content_filtered'  => $search_post->post_content_filtered,
				'post_parent'            => $search_post->post_parent,
				'guid'                   => $search_post->guid,
				'menu_order'             => $search_post->menu_order,
				'post_type'              => $search_post->post_type,
				'post_mime_type'         => $search_post->post_mime_type,
				'comment_count'          => $search_post->comment_count,
				'filter'                 => $search_post->filter,
				'keyid'                  => $search_keyid,
				'key_market'             => ($search_key->market ?: $search_key->label),
				'key_label'              => $search_key->label,
				'user_login'             => get_userdata($search_post->post_author)->user_login,
			));
		}

		return $searches;

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


	private function removeStyleTag($string)
	{
		return preg_replace(
			'/(<style[^>]*>[^<]*<\/style>)/i',
			"<!-- Blocked styles \n$1\n -->",
			$string
		);
	}


	private function replaceStyleBoxClasses($string)
	{
		return preg_replace(
			array(
				'/([\s\'"])style_box([\s\'"])/i',
				'/([\s\'"])style_box_content([\s\'"])/i',
				'/<[^>]*[\s\'"]style_box_header[\s\'"][^>]*>([^<]*(<[^\/][^>]*>[^<]*(<[^\/][^>]*>[^<]*<\/[^>]*>)?[^<]*<\/[^>]*>)?[^<]*)<\/[^>]*>/i',
			),
			array(
				'$1wolfnet_box$1',
				'$1wolfnet_boxContent$1',
				'<h3>$1</h3>',
			),
			$string
		);
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
