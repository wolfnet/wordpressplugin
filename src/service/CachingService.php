<?php

/**
 * WolfNet Caching Service
 *
 * This class is used for caching wolfnet data and serves as a facade to the WordPress Transients
 * API (see http://codex.wordpress.org/Transients_API). Encapsulating this interaction with the WP
 * API into its own class allows us to apply WolfNet logic to every request to the API.
 *
 * One big aspect of our implementation is that we maintain our own registry of cache keys which we
 * can then use to interact with all cache items that are WolfNet specific. This allows us to do
 * things such as delete all WolfNet specific cache items while still only interacting with the API
 * that WordPress provides, making our plugin fully compatible with other plugins or WordPress
 * extensions which might completely change the way the Transients API works. For example, a site
 * may use a plugin which uses Memcached for caching instead of the database.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Service_CachingService
{


    /* CONSTANT ********************************************************************************* */

    /**
     * @var  string  Used to identify when a request should cause cached content to force renewal.
     */
    const CACHE_FLAG = 'wolfnet-cache';

    /**
     * @var  string  A prefix for all cache keys going in or out of the cache.
     */
    const KEY_PREFIX = 'wnt_';

    /**
     * @var  int  The number of seconds data should be cached of no other duration is specified.
     */
    const DEFAULT_CACHE_SPAN = 3600;

    /**
     * @var  string  The cache key for the registry.
     */
    const REGISTRY_KEY = 'registry';

    /**
     * @var  int  The number of seconds the registry should be cached for. 0 = forever
     */
    const REGISTRY_CACHE_SPAN = 0;

    /**
     * This constant is a flag to indicate all cache items should be cleared.
     * @var  int
     */
    const CLEAR_ALL = 2;

    /**
     * This constant is a flag to indicate only expired cache items should be cleared.
     * @var  int
     */
    const CLEAR_EXPIRED = 4;

    /* PROPERTIES ******************************************************************************* */

    /**
     * @var  boolean  Should the cache return cached values or just null, which in most cases will
     *                force the cache consumer to "renew" the cached data.
     */
    private $forceRenewal;


    /* CONSTRUCTOR ****************************************************************************** */

    /**
     * This simple constructor method sets up the object. Here is where we capture if the cached
     * values that are retrieved during the life of this object should actually be forced to renew.
     *
     * @param  boolean  $force  Should cached items be forced to renew.
     *
     */
    public function __construct($force=false)
    {
        $forceRenewal = $force;
    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method wraps the WordPress 'get_transient' method with WolfNet logic. Specifically we
     * are applying a prefix to all items which are being retrieved from the cache. Additionally we
     * return a value of null when items cannot be retrieved from the cache rather than 'false'.
     *
     * @param  string  $key  The key for the data we are attempting to retrieve.
     *
     * @return mixed         The data retrieved from the cache, or null if none can be retrieved.
     *
     */
    public function cacheGet($key)
    {

        // Check if we should just return null and for the cache consumer to retrieve fresh data.
        if ($this->forceRenewal) {
            return null;
        }

        $cachedData = get_transient(self::KEY_PREFIX . $key);

        return ($cachedData !== false) ? $cachedData : null;

    }


    /**
     * This method wraps the WordPress 'set_transient' method with WolfNet logic. Specifically we
     * are applying a prefix to all items which are being entered into the cache. This method also
     * allows the service consumer to skip the duration argument and we will instead apply a global
     * default. Finally, every item that is entered into the cache also receives an entry in a
     * separate cache entry called the 'registry'.
     *
     * @param  string  $key       The key for the cached item.
     * @param  mixed   $data      The data to be cached.
     * @param  int     $duration  The number of seconds to cache the value. 0 = forever
     *
     * @return boolean            A boolean indicator of whether or not the caching attempt was
     *                            successful. true=success, false=failure
     *
     */
    public function cachePut($key, $data, $duration=null)
    {
        $duration = ($duration !== null) ? $duration : self::DEFAULT_CACHE_SPAN;

        if ($key !== self::REGISTRY_KEY) {
            $registry = $this->registry();
            $registry[$key] = time() + $duration;
            $this->registry($registry);
        }

        return set_transient(self::KEY_PREFIX . $key, $data, $duration);

    }


    /**
     * This method wraps the WordPress 'delete_transient' method with WolfNet logic. Specifically we
     * are applying a prefix to all items which are being deleted from the cache. We also remove the
     * entry from the registry if applicable.
     *
     * @param  string  $key  The key for the data we are attempting to delete.
     *
     * @return boolean            A boolean indicator of whether or not the deletion attempt was
     *                            successful. true=success, false=failure
     *
     */
    public function cacheDelete($key)
    {
        if ($key !== self::REGISTRY_KEY) {
            $registry = $this->registry();

            if (array_key_exists($key, $registry)) {
                unset($registry[$key]);
            }

            $this->registry($registry);

        }

        return delete_transient(self::KEY_PREFIX . $key);

    }


    /**
     * This method is a public interface to clear all WolfNet items from the cache.
     *
     * @return null
     *
     */
    public function clearAll()
    {
        $this->clear(self::CLEAR_ALL);
    }


    /**
     * This method is a public interface to clear all expired WolfNet items from the cache.
     *
     * @return null
     */
    public function clearExpired()
    {
        $this->clear(self::CLEAR_EXPIRED);
    }


    /* PRIVATE METHODS ************************************************************************** */

    /**
     * This method is use to get and set the WolfNet cache registry. If no value is passed the
     * method will attempt to retrieve the registry data from the cache. If a value is given the
     * registry in the cache will be updated.
     *
     * @param  array|null  $data  The data to be stored in the registry.
     *
     * @return array              Registry data.
     *
     */
    private function registry(array $data=null)
    {

        // If the function received data update the value in the registry
        if ($data !== null) {
            $this->cachePut(self::REGISTRY_KEY, $data, self::REGISTRY_CACHE_SPAN);
        } else {
            $data = $this->cacheGet(self::REGISTRY_KEY);
        }

        return ($data !== null) ? $data : array();

    }


    /**
     * This method is simple an interface for deleted the registry entry in the cache.
     *
     * @return  boolean  A boolean indicator of whether or not the deletion attempt was successful.
     *
     */
    private function destroyRegistry()
    {
        return $this->cacheDelete(self::REGISTRY_KEY);
    }


    /**
     * This method uses the WolfNet cache registry to identify and delete entries which are
     * WolfNet specific without impacting any other values in the cache. There are multiple modes
     * which can be used to clear cache items, these modes are identified by class constants:
     *
     *     CLEAR_EXPIRED: Clears all items which have a registry time-stamp that is older than the
     *                    current time.
     *
     *     CLEAR_ALL: Clears all items that are defined in the registry and then deletes the registry.
     *
     * @param  $mode  int  The mode to use to clear the cache.
     *
     * @return null
     *
     */
    private function clear($mode=self::CLEAR_EXPIRED)
    {
        $registry = $this->registry();
        $currentTime = time();

        foreach ($registry as $key => $time) {

            switch ($mode) {

                case self::CLEAR_EXPIRED:
                    if ($time < $currentTime) {
                        $this->cacheDelete($key);
                    }
                    break;

                case self::CLEAR_ALL:
                    $this->cacheDelete($key);
                    break;

            }
        }

        if ($mode === self::CLEAR_ALL) {
            // Remove the registry from the cache
            $this->cacheDelete(self::REGISTRY_KEY);
        }

    }


}
