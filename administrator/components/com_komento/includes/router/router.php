<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filter.filteroutput');
jimport('joomla.application.router');

class KomentoRouter
{
	public static function _($url, $xhtml = true, $ssl = null, $search = false)
	{
		return JRoute::_($url, $xhtml, $ssl);
	}

	public function getFeedUrl($component = 'all', $cid = 'all', $userid = '')
	{
		$link = 'index.php?option=com_komento&view=rss';

		if ($component != 'all') {
			$link .= '&component=' . $component;
		}

		if ($cid != 'all') {
			$link .= '&cid=' . $cid;
		}

		if ($userid != '') {
			$link .= '&userid=' . $userid;
		}

		return self::_($link) . '&format=feed';
	}

	/**
	 * Determine if the filter translated name match with the original filter name
	 *
	 * @since   3.1.3
	 * @access  public
	 */
	public static function getOriginalFilterName($str, $type = 'filter')
	{
		// For the user dashboard page only has 4 filters
		$filters = [
			'all' => JText::_('COM_KT_DASHBOARD_FILTER_ALL'),
			'pending' => JText::_('COM_KT_DASHBOARD_FILTER_PENDING'),
			'spam' => JText::_('COM_KT_DASHBOARD_FILTER_SPAM'),
			'reports' => JText::_('COM_KT_DASHBOARD_FILTER_REPORTS')
		];

		foreach ($filters as $key => $translatedValue) {
			
			if ($translatedValue == $str) {
				return $key;
			}
		}

		return $str;
	}	
}
