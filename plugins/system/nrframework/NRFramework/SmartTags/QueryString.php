<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class QueryString extends SmartTag
{
	/**
	 * Fetch value of a specific query string
	 * 
	 * @param   string  $key
	 * 
	 * @return  string
	 */
	public function fetchValue($key)
	{
		$query = $this->factory->getURI()->getQuery(true);
		
		if (empty($query))
		{
			return;
		}

		// Convert array keys to lowercase
		$query = array_change_key_case($query);

		// Convert key to lowercase too
		$key = strtolower($key);

		return array_key_exists($key, $query) ? \JFilterInput::getInstance()->clean($query[$key]) : '';
	}
}