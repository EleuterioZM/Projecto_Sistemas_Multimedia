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

class Progress extends Base
{
	/**
	 * Renders a progress bar
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function bar($progress = 0)
	{
		$theme = $this->getTemplate();
		$theme->set('progress', $progress);
		$output = $theme->output('html/progress/bar');

		return $output;
	}
}