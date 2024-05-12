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

class Quote extends Base
{
	/**
	 * Renders a quote item
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function item($message, $options = [])
	{
		$inline = \FH::normalize($options, 'inline', false);
		$author = \FH::normalize($options, 'author', null);

		$themes = $this->getTemplate();
		$themes->set('message', $message);
		$themes->set('inline', $inline);
		$themes->set('author', $author);

		$output = $themes->output('html/quote/item');

		return $output;
	}
}