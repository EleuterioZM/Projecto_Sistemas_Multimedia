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

class EngageBox extends Condition
{
    /**
     * Checks if the user viewed any of the given boxes
     * 
     * @return  bool
     */
    public function pass()
    {
        // Skip if the visitorID is not set
        $visitorID = \NRFramework\VisitorToken::getInstance()->get();
        if (empty($visitorID))
        {
            return true;
        }

        $box_ids  = $this->selection;
        if (!is_array($box_ids) || empty($box_ids))
        {
            return true;
        }

        $box_ids = implode(',', $box_ids);
        
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('COUNT(id)')
            ->from($db->quoteName('#__rstbox_logs'))
            ->where($db->quoteName('event') . ' = 1')
            ->where($db->quoteName('box') . " IN ( $box_ids )")
            ->where($db->quoteName('visitorid') . ' = '. $db->quote($visitorID));
        
        $db->setQuery($query);

        $pass = (int) $db->loadResult();

        return (bool) $pass;
	}
}