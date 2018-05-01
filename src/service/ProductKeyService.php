<?php

/**
 * WolfNet Product Key Service
 *
 * This service is used to manage and retrieve product key data.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Service_ProductKeyService
{
	/**
     * This constant is a unique idenitfier that is used to define a plugin option which saves the
     * product key used by the plugin to retreive data from the WolfNet API.
     * @var string
     */
    const PRODUCT_KEY_OPTION = 'wolfnet_productKey';

    /**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
    	$this->plugin = $plugin;
    }


	/**
     * This method retrieves a specific product key from the WordPress options table based on a
     * provided unique ID value.
     * @param  integer $id The ID of the key to be retrieved.
     * @return string      The key that was retrieved from the WP options table.
     */
    public function getById($id)
    {
		$key = $this->getKeyById($id);
		return $key->key;
    }


	/**
	 * This method retrieves a specific product key JSON from the WordPress options table based on a
	 * provided unique ID value.
	 * @param  integer $id The ID of the key to be retrieved.
	 * @return string      The key that was retrieved from the WP options table.
	 */
	public function getKeyById($id)
	{
		$keyList = json_decode($this->get());

		foreach ($keyList as $key) {
			if ($key->id == $id) {
				return $key;
			}
			// TODO: Add some sort of error throwing if no key is found for the given ID.
		}

	}


    /**
     * This method retrieves a specific product key ID from the WordPress options table based on a
     * provided unique product key.
     * @param  integer $id The ID of the key to be retrieved.
     * @return string      The key that was retrieved from the WP options table.
     */
    public function getIdByKey($findKey) {
        $keyList = json_decode($this->get());

        foreach($keyList as $key) {
            if($key->key == $findKey) {
                return $key->id;
            }
        }
    }


    /**
     * This method retrieves a specific product key from the WordPress options table based on a
     * provided market name.
     * @param  string $market  The market name associated with the key to be retrieved.
     * @return string          The key that was retrieved from the WP options table.
     */
    public function getByMarket($market)
    {
        $keyList = json_decode($this->get());

        foreach ($keyList as $key) {
            if(!array_key_exists('market', $key) || strlen($key->market) == 0) {
                $this->update();
                $keyList = json_decode($this->get());
            }

            if (strtoupper($key->market) == strtoupper($market)) {
                return $key->key;
            }
        }

        return null;

    }


    /**
     * This method retrieved the 'default' key (or first key on the stack) from the WP options table.
     * @return string The key that was retrieved from the WP options table.
     */
    public function getDefault()
    {

        $productKey = json_decode($this->get());
        // TODO: Add some sort of error throwing for if there are no keys.

        if (is_array($productKey) && array_key_exists(0, $productKey)) {
            return $productKey[0]->key;
        } else {
            return false;
        }

    }


    /**
     * This method retrieves a JSON representation of stored product keys from the WP options table.
     * @return string JSON representation of the stored product keys.
     */
    public function get()
    {
        $key = get_option(trim(self::PRODUCT_KEY_OPTION));

        // If the value stored in the options table is a legacy, single key value convert it to the
        // newer JSON format.
        if (!$this->isJsonEncoded($key)) {
            $key = $this->getAsJson($key);
        }

        // TODO: perhaps it would be better to decode the JSON here instead of multiple other places.
        return $key;

    }


    /**
     * This method returns the number of keys associated with the plugin.
     * @return int Number of keys
     */
    public function getCount()
    {
        return count(json_decode($this->get()));
    }


    /**
     * This method updates the product key structure to make sure it has all the
     * necessary attributes.
     */
    public function update()
    {
        $keyStruct = json_decode($this->get());

        for($i = 0; $i < count($keyStruct); $i++) {
            if(!array_key_exists('market', $keyStruct[$i])
                || strlen($keyStruct[$i]->market) == 0) {
                $market = $this->plugin->data->getMarketName($keyStruct[$i]->key);
                $keyStruct[$i]->market = $market;
            }
        }

        // Update key in Wordpress settings data.
        update_option(self::PRODUCT_KEY_OPTION, json_encode($keyStruct));
    }


    /**
     * check if key is valid
     * @param  string $key
     * @return bool         true?
     */
    public function isValid($key = null)
    {
        $valid = true;

        if ($key != null) {
            $productKey = $key;
        } else {
            $productKey = json_decode($this->getDefault());
        }

        if (trim($productKey) !== '') {
            try {
                $http = $this->plugin->api->authenticate($productKey, array('force'=>true));
            } catch (Wolfnet_Api_ApiException $e) {
                if ($e->getCode() == Wolfnet_Api_Client::NO_AUTH_ERROR) {
                    $valid = false;
                } else {
                    throw $e;
                }

            }

        } else {
            $valid = false;
        }

        return $valid;

    }


    public function getFromCriteria(&$criteria)
    {
        $key = '';

        // Maintain backwards compatibility if there is no keyid in the shortcode.
        if (!array_key_exists('keyid', $criteria) || $criteria['keyid'] == '') {
            $key = $this->getDefault();
        } else {
            $key = $this->getById($criteria['keyid']);
        }

        $criteria['key'] = $key;

        return $key;
    }


    public function isSaved($find)
    {
        $keyList = json_decode($this->get());

        foreach ($keyList as $key) {
            if ($key->key == $find) {
                return true;
            }
        }

        return false;

    }


    protected function getAsJson($keyString)
    {
        // This takes the old style single key string and returns a JSON formatted key array
        $keyArray = array(
            array(
                "id" => "1",
                "key" => $keyString,
                "label" => ""
            )
        );

        return json_encode($keyArray);

    }


    public function isJsonEncoded($str)
    {
        if (is_array(json_decode($str)) || is_object(json_decode($str))) {
            return true;
        } else {
            return false;
        }

    }
}
?>
