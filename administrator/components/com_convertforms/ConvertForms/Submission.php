<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

class Submission
{
    /**
     * Replaces the Smart Tags of a submission.
     * 
     * @param   object  $submission
     * @param   string  $layout
     * 
     * @return  string
     */
	public static function replaceSmartTags($submission, $layout)
	{
		$st = new \NRFramework\SmartTags();
		
		// Register CF Smart Tags
		$st->register(
			'\ConvertForms\SmartTags',
			JPATH_SITE . '/administrator/components/com_convertforms/ConvertForms/SmartTags', 
			[
				'submission' => $submission
			]
		);

		return $st->replace($layout);
	}
}