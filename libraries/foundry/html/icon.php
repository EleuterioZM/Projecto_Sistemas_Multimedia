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

class Icon extends Base
{
	/**
	 * Renders a font icon on the page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function font($icon, $iconLoading = false, $customLoadingIcon = '', $options = [])
	{
		static $icons = [];

		$index = $icon . $iconLoading . $customLoadingIcon;

		if (!isset($icons[$index])) {
			$icon = 'fdi ' . $icon;

			$class = \FH::normalize($options, 'class', '');

			$theme = $this->getTemplate();
			$theme->set('icon', $icon);
			$theme->set('iconLoading', $iconLoading);
			$theme->set('customLoadingIcon', $customLoadingIcon);
			$theme->set('class', $class);
			$icons[$index] = $theme->output('html/icon/font');
		}

		return $icons[$index];
	}
}
