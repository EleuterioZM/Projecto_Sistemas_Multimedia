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

class Dialog extends Base
{
	/**
	 * Renders a dialog button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function button($title, $type = 'default', $options = [])
	{
		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');

		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('type', $type);
		$theme->set('attributes', $attributes);
		$theme->set('class', $class);

		$output = $theme->output('html/dialog/button');

		return $output;
	}

	/**
	 * Initializes dialog on the site
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function init()
	{
		Scripts::load('shared');
	}
}
