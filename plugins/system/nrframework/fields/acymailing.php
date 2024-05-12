<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use NRFramework\Extension;

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldAcymailing extends JFormFieldGroupedList
{
	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 *
	 * @since   1.6
	 */
    protected function getGroups()
    {
        $groups = [];
        $lists  = [];

        if ($acymailing_5_is_installed = Extension::isInstalled('com_acymailing'))
        {
            $lists['5'] = $this->getAcym5Lists();
        }

        if ($acymailing_6_is_installed = Extension::isInstalled('com_acym'))
        {
            $lists['6'] = $this->getAcym6Lists();
        }

        foreach ($lists as $group_key => $group)
        {
            if (!is_array($group))
            {
                continue;
            }

            foreach ($group as $list)
            {
                $groups['AcyMailing ' . $group_key][] = JHtml::_('select.option', $list->id, $list->name);
            }
        }

        return $groups;
    }

    /**
     *  Get AcyMailing 6 lists
     *
     *  @return  mixed   Array on success, null on failure
     */
    private function getAcym6Lists()
    {
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php'))
        {
            return;
        }

        $lists = acym_get('class.list')->getAll();

        if (!is_array($lists))
        {
            return;
        }

        // Add 6: prefix to each list id.
        foreach ($lists as $key => &$list)
        {
            $list->id = '6:' . $list->id;
        }

        return $lists;
    }

    /**
     *  Get AcyMailing 5 lists
     *
     *  @return  mixed   Array on success, null on failure
     */
    private function getAcym5Lists()
    {
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php'))
        {
            return;
        }
         
        $lists = acymailing_get('class.list')->getLists();

        if (!is_array($lists))
        {
            return;
        }

        // The getGroups method expects the id property
        foreach ($lists as $key => $list)
        {
            $list->id = $list->listid;
        }

        return $lists;
    }
}