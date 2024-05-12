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

class UserGroup extends Condition
{
    /**
     *  Get the user's authorized groups
     * 
     *  @return array User groups
     */
	public function value()
	{
		return $this->user->getAuthorisedGroups();
	}

	/**
	 * A one-line text that describes the current value detected by the rule. Eg: The current time is %s.
	 *
	 * @return string
	 */
	public function getValueHint()
	{
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select($db->qn('title'))
            ->from('#__usergroups')
            ->where($db->qn('id') . ' IN ' . '(' . implode(',', $this->value()) . ')');

        $db->setQuery($query);

        $value = implode(', ', $db->loadColumn());
        
		return \JText::sprintf('NR_DISPLAY_CONDITIONS_HINT_' . strtoupper($this->getName()), $value);
	}
}