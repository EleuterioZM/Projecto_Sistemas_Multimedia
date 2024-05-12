<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

/**
 *  This file is deprecated. Use CacheManager instead of Cache.
 */

namespace NRFramework;

defined('_JEXEC') or die;

use \NRFramework\CacheManager;
/**
 *  Caching mechanism
 */
class Cache
{
	/**
	 *  Check if has alrady exists in memory
	 *
	 *  @param   string   $hash  The hash string
	 *
	 *  @return  boolean         
	 */
	static public function has($hash)
	{
		$cache = CacheManager::getInstance(\JFactory::getCache('novarain', ''));
		return $cache->has($hash);
	}

	/**
	 *  Returns hash value
	 *
	 *  @param   string  $hash  The hash string
	 *
	 *  @return  mixed          False on error, Object on success
	 */
	static public function get($hash)
	{
		$cache = CacheManager::getInstance(\JFactory::getCache('novarain', ''));
		return $cache->get($hash);
	}

	/**
	 *  Sets on memory the hash value
	 *
	 *  @param  string  $hash  The hash string
	 *  @param  mixed   $data  Can be string or object
	 *
	 *  @return mixed
	 */
	static public function set($hash, $data)
	{
		$cache = CacheManager::getInstance(\JFactory::getCache('novarain', ''));
		return $cache->set($hash, $data);
	}

	/**
	 *  Reads hash value from memory or file
	 *
	 *  @param   string   $hash   The hash string
	 *  @param   boolean  $force  If true, the filesystem will be used as well on the /cache/ folder
	 *
	 *  @return  mixed            The hash object valuw
	 */
	static public function read($hash, $force = false)
	{
		$cache = CacheManager::getInstance(\JFactory::getCache('novarain', ''));
		return $cache->read($hash, $force);
	}

	/**
	 *  Writes hash value in cache folder
	 *
	 *  @param   string   $hash  The hash string
	 *  @param   mixed    $data  Can be string or object
	 *  @param   integer  $ttl   Expiration duration in milliseconds
	 *
	 *  @return  mixed           The hash object value
	 */
	static public function write($hash, $data, $ttl = 0)
	{
		$cache = CacheManager::getInstance(\JFactory::getCache('novarain', ''));
		return $cache->write($hash, $data, $ttl);
	}

	/**
	 * Memoize a function to run once per runtime
	 *
	 * @param  string	$key		The key to store the result of the callback
	 * @param  callback $callback	The callable anonymous function to call
	 * 
	 * @return mixed
	 */
	static public function memo($key, callable $callback)
	{
		$hash = md5($key);

		if (Cache::has($hash))
		{
			return Cache::get($hash);
		}

		return Cache::set($hash, $callback());
	}
}