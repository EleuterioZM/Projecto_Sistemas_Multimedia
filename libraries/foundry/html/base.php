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

use Foundry\Libraries\Themes;

class Base
{
	protected $fd = null;

	public function __construct($fd)
	{
		$this->fd = $fd;
	}

	/**
	 * Central method to generate a new theme object so that we can centralize and inject variables into the library
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getTemplate()
	{
		$theme = new Themes($this->fd);

		return $theme;
	}
}
