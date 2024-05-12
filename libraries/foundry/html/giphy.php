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

class Giphy extends Base
{
	/**
	 * Renders the GIPHY browser markup
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function browser($options = [])
	{
		$appearance = \FH::normalize($options, 'appearance', 'light');
		$accent = \FH::normalize($options, 'theme', 'foundry');

		$theme = $this->getTemplate();
		$theme->set('accent', $accent);
		$theme->set('appearance', $appearance);
		$output = $theme->output('html/giphy/browser');

		return $output;
	}

	/**
	 * Renders the GIPHY list markup
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function list($items, $type)
	{
		$themes = $this->getTemplate();
		$themes->set('giphies', $items);
		$themes->set('type', $type);
		$output = $themes->output('html/giphy/list');

		return $output;
	}

	/**
	 * Renders the GIPHY item markup
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function item($url)
	{
		$themes = $this->getTemplate();
		$themes->set('url', $url);
		$output = $themes->output('html/giphy/item');

		return $output;
	}
}