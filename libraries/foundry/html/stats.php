<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Html\Base;

class Stats extends Base
{
	/**
	 * Renders a simple card for statistical purposes
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function card($label, $count, $icon, $url = null)
	{

		$theme = $this->getTemplate();
		$theme->set('url', $url);
		$theme->set('label', $label);
		$theme->set('count', $count);
		$theme->set('icon', $icon);

		$contents = $theme->output('html/stats/card');

		return $contents;
	}
}
