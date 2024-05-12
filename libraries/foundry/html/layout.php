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

class Layout extends Base
{
	/**
	 * Renders a standard box layout on the page
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function box($title, $icon = null, $actions = null, $options = [])
	{
		$rounded = \FH::normalize($options, 'rounded', null);
		$padding = \FH::normalize($options, 'padding', 'p-3xl');
		$wrapperClass = \FH::normalize($options, 'wrapperClass', '');

		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('icon', $icon);
		$theme->set('actions', $actions);
		$theme->set('rounded', $rounded);
		$theme->set('padding', $padding);
		$theme->set('wrapperClass', $wrapperClass);

		return $theme->output('html/layout/box');
	}
}
