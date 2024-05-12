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

defined('_JEXEC') or die('Restricted access');

class plgConvertFormsAcyMailing extends \ConvertForms\Plugin
{
	/**
	 *  Main method to store data to service
	 *
	 *  @return  void
	 */
	public function subscribe()
	{
		// Make sure there's a list selected
		if (!isset($this->lead->campaign->list) || empty($this->lead->campaign->list))
		{
			throw new Exception(JText::_('PLG_CONVERTFORMS_ACYMAILING_NO_LIST_SELECTED'));
		}
			
		$lists    = $this->lead->campaign->list;
		$lists_v5 = [];
		$lists_v6 = [];

		// Discover lists for each version. v6 lists starts with 6: prefix.
		foreach ($lists as $list)
		{
			// Is a v5 list
			if (strpos($list, '6:') === false)
			{
				$lists_v5[] = $list;
				continue;
			}

			// Is a v6 list
			$lists_v6[] = str_replace('6:', '', $list);
		}

		require_once __DIR__ . '/helper.php';

		// Add user to AcyMailing 5 lists
		if (!empty($lists_v5))
		{
			ConvertFormsAcyMailingHelper::subscribe_v5($this->lead->email, $this->lead->params, $lists_v5, $this->lead->campaign->doubleoptin);
		}

		// Add user to AcyMailing 6+ lists
		if (!empty($lists_v6))
		{
			ConvertFormsAcyMailingHelper::subscribe($this->lead->email, $this->lead->params, $lists_v6, $this->lead->campaign->doubleoptin);
		}
	}
	
    /**
     *  Disable service wrapper
     *
     *  @return  boolean
     */
    protected function loadWrapper()
    {
		return true;
    }
}