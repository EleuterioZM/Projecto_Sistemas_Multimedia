<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldNRDevice extends NRFormFieldList
{
	/**
	 *  Browsers List
	 *
	 *  @var  array
	 */
	public $options = array(
        'desktop' => 'NR_DESKTOPS',
        'mobile'  => 'NR_MOBILES',
        'tablet'  => 'NR_TABLETS'
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