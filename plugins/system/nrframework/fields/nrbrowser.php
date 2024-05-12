<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldNRBrowser extends NRFormFieldList
{
	/**
	 *  Browsers List
	 *
	 *  @var  array
	 */
	public $options = array(
		'chrome'  => 'NR_CHROME',
		'firefox' => 'NR_FIREFOX',
		'edge'    => 'NR_EDGE',
		'ie'      => 'NR_IE',
		'safari'  => 'NR_SAFARI',
		'opera'   => 'NR_OPERA'
	);

	protected function getOptions()
	{
		asort($this->options);

		foreach ($this->options as $key => $option)
		{
			$options[] = JHTML::_('select.option', $key, JText::_($option));
		}

		return array_merge(parent::getOptions(), $options);
	}
}