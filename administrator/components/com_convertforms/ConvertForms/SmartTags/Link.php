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

class Link extends SmartTag
{
	/**
	 * Returns the link to a single front-end submission.
	 * 
	 * @return  string
	 */
	public function getLink()
	{
		return isset($this->data['submission']->link) ? $this->data['submission']->link : '';
	}
}