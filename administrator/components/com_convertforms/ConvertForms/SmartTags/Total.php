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

namespace ConvertForms\SmartTags;

defined('_JEXEC') or die('Restricted access');

use NRFramework\SmartTags\SmartTag;

class Total extends SmartTag
{
	/**
	 * Returns the total submissions.
	 * Used in Convert Forms Front End Submissions View.
	 * 
	 * @return  string
	 */
	public function getTotal()
	{
		return isset($this->data['front_end_submission']['total']) ? $this->data['front_end_submission']['total'] : '';
	}
}