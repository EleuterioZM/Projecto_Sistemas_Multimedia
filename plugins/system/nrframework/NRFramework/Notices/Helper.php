<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @credits			https://github.com/codeigniter4/CodeIgniter4/blob/develop/app/Config/Mimes.php
*/

namespace NRFramework\Notices;

// No direct access
defined('_JEXEC') or die;

use \NRFramework\Extension;

class Helper
{
	/**
	 * Returns the extension details for given element.
	 * 
	 * @param   array   $data
	 * @param   string  $element
	 * 
	 * @return  array
	 */
	public static function getExtensionDetails($data, $element)
	{
		// Return bundle only if its active
		if (isset($data['bundle']) && $data['bundle']['active'])
		{
			return $data['bundle'];
		}
		
		$alias = Extension::getExtensionDataFileAlias($element);

		// If no license data found for this extension
		if (!isset($data[$alias]))
		{
			// Return the expired bundle information if it exists
			if (isset($data['bundle']))
			{
				return $data['bundle'];
			}
			
			// No bundle exists, return nothing
			return;
		}

		// Return the extension's license data details
		return $data[$alias];
	}
}