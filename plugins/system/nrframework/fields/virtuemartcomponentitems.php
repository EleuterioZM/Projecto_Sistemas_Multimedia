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

class JFormFieldVirtueMartComponentItems extends JFormFieldComponentItems
{
    public function init()
    {
        // Get default language
        $this->element['table'] = 'virtuemart_products_' . $this->getLanguage();

        parent::init();
    }

	/**
     *  VirtueMart is using different tables per language. Therefore, we need to use their API to get the default language code
     *
     *  @return  string
     */
    private function getLanguage($default = 'en_gb')
    {	
		// Silent inclusion.
		@include_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php'; 

        if (!class_exists('VmConfig'))
		{
			return $default;
        }
            
        // Init configuration
		VmConfig::loadConfig();
		
        return VmConfig::$jDefLang;
    }
}