<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class AkeebaSubs extends Condition
{
	/**
	 *  Returns the assignment's value
	 * 
	 *  @return  array   Akeeba Subscriptions
	 */
	public function value()
	{
		return $this->getlevels();
	}

	/**
	 *  Returns all user's active subscriptions
	 *
	 *  @param   int     $userid  User's id
	 *
	 *  @return  array   Akeeba Subscriptions
	 */
	private function getLevels()
	{
		if (!$user = $this->user->id)
		{
			return false;
		}

		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			return false;
		}

		// Get the Akeeba Subscriptions container. Also includes the autoloader.
		$container = \FOF30\Container\Container::getInstance('com_akeebasubs');

		$subscriptionsModel = $container->factory->model('Subscriptions')->tmpInstance();

		$items = $subscriptionsModel
			->user_id($user)
			->enabled(1)
			->get();

		if (!$items->count())
		{
			return false;
		}

		$levels = array();

		foreach ($items as $subscription)
		{
			$levels[] = $subscription->akeebasubs_level_id;
		}

		return array_unique($levels);
	}
}