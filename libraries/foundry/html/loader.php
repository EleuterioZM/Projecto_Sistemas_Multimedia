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

class Loader extends Base
{
	/**
	 * Renders an inline loader on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function inline($options = [])
	{
		$class = \FH::normalize($options, 'class', '');

		$themes = $this->getTemplate();
		$themes->set('class', $class);
		$loader = $themes->output('html/loader/inline');

		return $loader;
	}

	/**
	 * Renders a loader on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function block($options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$loaderClass = \FH::normalize($options, 'loaderClass', '');

		$themes = $this->getTemplate();
		$themes->set('class', $class);
		$themes->set('loaderClass', $loaderClass);
		$loader = $themes->output('html/loader/block');

		return $loader;
	}

	/**
	 * Renders a standard loader on the site
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function standard($options = [])
	{
		$class = \FH::normalize($options, 'class', '');

		$themes = $this->getTemplate();
		$themes->set('class', $class);
		$loader = $themes->output('html/loader/standard');

		return $loader;
	}
}
