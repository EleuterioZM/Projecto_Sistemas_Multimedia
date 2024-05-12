<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

require_once __DIR__ . '/componentitems.php';

class JFormFieldJShoppingComponentItems extends JFormFieldComponentItems
{
    public function init()
    {
        // Get default language
        $this->element['column_title'] = 'name_' . $this->getLanguage();

        parent::init();
    }

	/**
     *  JoomShopping is using different columns per language. Therefore, we need to use their API to get the default language code.
     *
     *  @return  string
     */
    private function getLanguage($default = 'en-GB')
    {	
		// Silent inclusion.
        @include_once JPATH_SITE . '/components/com_jshopping/lib/factory.php';

        if (!class_exists('JSFactory'))
        {
            return $default;
        }

		return JSFactory::getConfig()->defaultLanguage;
    }
}