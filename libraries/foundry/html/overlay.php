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
use Foundry\Libraries\Scripts;

class Overlay extends Base
{
	/**
	 * Generates a grid's Upgrade to Pro overlay in backend
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function grid($buttonText = 'FD_UPGRADE_TO_PRO', $description = '', $upgradeUrl = '')
	{
		$theme = $this->getTemplate();
		$theme->set('buttonText', $buttonText);
		$theme->set('description', $description);
		$theme->set('upgradeUrl', $upgradeUrl);
		
		$html = $theme->output('html/overlay/grid');

		return $html;
	}

	/**
	 * Generates a form's Upgrade to Pro overlay in backend
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function form($showOverlay = false, $buttonText = '', $upgradeUrl = '')
	{
		if (empty($buttonText)) {
			$buttonText = 'FD_UPGRADE_TO_PRO';
		}

		$theme = $this->getTemplate();
		$theme->set('showOverlay', $showOverlay);
		$theme->set('buttonText', $buttonText);
		$theme->set('upgradeUrl', $upgradeUrl);
		
		$html = $theme->output('html/overlay/form');

		return $html;
	}
}
