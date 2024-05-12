<?php

/**
 *  @author          Tassos.gr <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die;

/**
 *  Cache Manager
 * 
 *  Singleton
 */
class CacheManager
{
	/**
	 *  'static' cache array
	 *   @var  array
	 */
    protected $cache = [];

    /**
     *  Cache mechanism object
     *  @var object
     */
    protected $cache_mechanism = null;
    
    /**
     *  Construct
     */
    protected function __construct($cache_mechanism)
    {
        $this->cache_mechanism = $cache_mechanism;
    }

    static public function getInstance($cache_mechanism)
    {
        static $instance = null;

		if ($instance === null)
		{
            $instance = new CacheManager($cache_mechanism);
		}
		
        return $instance;
    }

	/**
	 *  Check if a hash already exists in memory
	 *
	 *  @param   string   $hash  The hash string
	 *
	 *  @return  boolean         
	 */
	public function has($hash)
	{
		return isset($this->cache[$hash]);
	}

	/**
	 *  Returns a hash's value
	 *
	 *  @param   string  $hash  The hash string
	 *
	 *  @return  mixed          False on error, Object on success
	 */
	public function get($hash)
	{
		if (!$this->has($hash))
		{
			return false;
		}

		return is_object($this->cache[$hash]) ? clone $this->cache[$hash] : $this->cache[$hash];
	}

	/**
	 *  Sets a hash value
	 *
	 *  @param  string  $hash  The hash string
	 *  @param  mixed   $data  Can be string or object
	 *
	 *  @return mixed
	 */
	public function set($hash, $data)
	{
		$this->cache[$hash] = $data;
		return $data;
	}

	/**
	 *  Reads a hash value from memory or file
	 *
	 *  @param   string   $hash   The hash string
	 *  @param   boolean  $force  If true, the filesystem will be used as well on the /cache/ folder
	 *
	 *  @return  mixed            The hash object value
	 */
	public function read($hash, $force = false)
	{
		if ($this->has($hash))
		{
			return $this->get($hash);
		}

		if ($force)
		{
			$this->cache_mechanism->setCaching(true);
		}

		return $this->cache_mechanism->get($hash);
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
	public function write($hash, $data, $ttl = 0)
	{
		if ($ttl)
		{
			$this->cache_mechanism->setLifeTime($ttl * 60);
		}

		$this->cache_mechanism->setCaching(true);
		$this->cache_mechanism->store($data, $hash);

		$this->set($hash, $data);

		return $data;
	}
}