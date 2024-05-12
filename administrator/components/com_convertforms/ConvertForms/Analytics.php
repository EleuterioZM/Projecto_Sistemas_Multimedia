<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

use NRFramework\Cache;

defined('_JEXEC') or die('Restricted access');

/**
 *  Analytics Helper Class
 */
class Analytics
{
	/**
	 *  Returns average submission in current month
	 *
	 *  @return  float
	 */
	public static function getLeadsAverageThisMonth()
	{
		return number_format(self::getTotal('this_month') / date('d'), 1);
	}

	/**
	 *  Returns current month projection
	 *
	 *  @return  integer
	 */
	public static function getMonthProjection()
	{
		return number_format(self::getTotal('this_month') / date('d') * date('t'), 1);
	}

	public static function getRows($type = null, $options = [])
	{
		return number_format(self::getTotal($type, $options));
	}

	public static function getTotal($type = null, $options = [])
	{
		$hash = md5($type . serialize($options));

		if (Cache::has($hash))
		{
			return Cache::get($hash);
		}

		$model = \JModelLegacy::getInstance('Conversions', 'ConvertFormsModel', ['ignore_request' => true]);

		$model->setState('filter.state', [1, 2]);
		$model->setState('filter.join_campaigns', 'skip');
		$model->setState('filter.join_forms', 'skip');

		if ($type)
		{
			$model->setState('filter.period', $type);
		}
		
		if ($type == 'range')
		{
			$model->setState('filter.created_from', $options['created_from']);
			$model->setState('filter.created_to', $options['created_to']);
		}

		$query = $model->getListQuery();

		$query->clear('select');
        $query->select('count(a.id)');

		$db = \JFactory::getDbo();
		
		// Prevent the "The SELECT would examine more than MAX_JOIN_SIZE rows; " MySQL error
		// on websites with a big number of menu items in the db.
		$db->setQuery('SET SQL_BIG_SELECTS = 1')->execute();
		
		$db->setQuery($query);

		$count = $db->loadResult();

	    return Cache::set($hash, $count);
	}
}

?>