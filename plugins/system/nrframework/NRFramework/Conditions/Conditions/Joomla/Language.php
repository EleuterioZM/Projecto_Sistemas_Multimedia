<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Joomla;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class Language extends Condition
{
	/**
     *  Returns the assignment's value
     * 
     *  @return array Language strings
     */
	public function value()
	{
		$lang = $this->factory->getLanguage();

		$lang_strings 	= $lang->getLocale();
		$lang_strings[] = $lang->getTag();

		return $lang_strings;
	}
}