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
     * @var  string  The cache key for the caching key salt.
     */
    const SALT_KEY = 'cache_key_salt';

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
    public function __construct($force=false, $reap=false, $clear=false)
    {
        $forceRenewal = $force;

        if ($clear) {
            $this->clearAll();
        } else if ($reap) {
            $this->clearExpired();
        }

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

        $cachedData = get_transient(self::KEY_PREFIX . $this->salt($key));

        return ($cachedData !== false) ? $cachedData : null;

    }


    /**
     * This method wraps the WordPress 'set_transient' method with WolfNet logic. Specifically we
     * are applying a prefix to all items which are being entered into the cache. This method also
     * allows the service consumer to skip the duration argument and we will instead apply a global
     * default. Finally, every item that is entered into the cache has its key "salted". This way if
     * we want to invalidate all of the cache items we can do so by changing the salt and not have
     * to know the specific keys.
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

        return set_transient(self::KEY_PREFIX . $this->salt($key), $data, $duration);

    }


    /**
     * This method wraps the WordPress 'delete_transient' method with WolfNet logic. Specifically we
     * are applying a prefix to all items which are being deleted from the cache.
     *
     * @param  string  $key  The key for the data we are attempting to delete.
     *
     * @return boolean       A boolean indicator of whether or not the deletion attempt was
     *                       successful. true=success, false=failure
     *
     */
    public function cacheDelete($key)
    {
        return delete_transient(self::KEY_PREFIX . $this->salt($key));
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
     * This method applies the salt to the key that is provided.
     *
     * @param  string $key  The key to be salted.
     *
     * @return string       The salted key.
     */
    private function salt($key)
    {
        return sha1($key . $this->getSalt());
    }


    /**
     * This method retrieves the salt from the transient API.
     *
     * @return string
     */
    private function getSalt()
    {
        $salt = get_transient(self::KEY_PREFIX . self::SALT_KEY);

        if ($salt === false) {
            $salt = $this->renewSalt();
        }

        return $salt;

    }


    /**
     * This method updates the salt to a new value.
     *
     * @return string  The new salt value.
     */
    private function renewSalt()
    {
        $newSalt = uniqid();

        set_transient(self::KEY_PREFIX . self::SALT_KEY, $newSalt, 0);

        return $newSalt;

    }


    /**
     * This method attempts to flush values from the WP transient cache based on the mode that is
     * specified. First we attempt to remove values from the database directly. This will only work
     * if the WP installation is set up to use the default cache rather than something like
     * memcached or redis. Then we reset our cache salt. This means that the next time those values
     * are looked for in the cache they won't be their because the salt has changed. The original
     * values are still in the cache but should get cleaned up by the caches reaping process.
     *
     * NOTE: We can only clear expired cache values from the default WP database based cache. With
     *       other caches we have no way of knowing which keys are our keys.
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

        // Delete transients from the database (if they are present)
        $this->deleteTransientRecordsFromDatabase($mode);

        if ($mode == self::CLEAR_ALL) {
            // Update the cache salt
            $this->renewSalt();
        }

    }


    /**
     * Perform a database query to delete WolfNet specific transient values.
     *
     * NOTE: This will only clear transient values if the WordPress installation is configured to
     * use the default caching mechanism (database). If it uses something like memcached we are not
     * able to search for and delete only our values. A fall-back measure will be in place for this
     * that relies on the caching systems defined caching scheme.
     *
     * @param  int $mode  The mode use for clearing.
     *
     * @return null
     */
    private function deleteTransientRecordsFromDatabase($mode=self::CLEAR_EXPIRED)
    {
        global $wpdb;

        $optTable = $wpdb->options;
        $where = "";
        $args = array();

        // If we are clearing only expired values add a statement to the where clause.
        if ($mode === self::CLEAR_EXPIRED) {
            $where .= "AND (
                option_name LIKE '_transient_timeout_wnt%%'
                OR option_name LIKE '_transient_timeout_wolfnet%%'
            )";
            $where .= "AND option_value < %d";
            $args[] = time();
        }

        // Build a query that will select and delete all relevant values in one request.
        $qryStr = "
            DELETE FROM ${optTable}
            WHERE option_id IN (
                SELECT tmp.option_id
                FROM (
                    SELECT option_id
                    FROM ${optTable}
                    JOIN (
                        SELECT option_name AS `timeout`
                            , replace(option_name, '_timeout', '') AS `key`
                        FROM ${optTable}
                        WHERE (
                            option_name LIKE '_transient_%%wnt%%'
                            OR option_name LIKE '_transient_%%wolfnet%%'
                        )
                        ${where}
                    ) AS `keys` ON (`keys`.`key` = option_name OR `keys`.`timeout` = option_name)
                ) AS tmp
            );
        ";

        // Prepare and execute the query statement.
        $qry = $wpdb->prepare($qryStr, $args);
        $wpdb->query($qry);

    }


}
