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

class Panel extends Base
{
	/**
	 * Generates a panel heading used at the back end
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function heading($header, $description = '', $helpLink = '')
	{
		if (!$description && $description !== false) {
			$description = $header . '_DESC';
		}

		$header = \JText::_($header);
		$description = \JText::_($description);

		if ($helpLink) {
			$helpLink = $this->fd->getDocumentationLink() . '/' . ltrim($helpLink, '/');
		}

		$theme = $this->getTemplate();
		$theme->set('header', $header);
		$theme->set('desc', $description);
		$theme->set('helpLink', $helpLink);

		$output = $theme->output('html/panel/heading');

		return $output;
	}

	/**
	 * Generates an info section within a panel
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function info($text, $link = '', $buttonText = '', $buttonSize = 'sm', $image = '', $imageSize = 64)
	{
		$text = \JText::_($text);
		$buttonText = \JText::_($buttonText);

		// Backwards compatibility
		if ($buttonSize === 'btn-sm') {
			$buttonSize = 'sm';
		}

		$theme = $this->getTemplate();
		$theme->set('image', $image);
		$theme->set('imageSize', $imageSize);
		$theme->set('buttonText', $buttonText);
		$theme->set('text', $text);
		$theme->set('link', $link);
		$theme->set('buttonSize', $buttonSize);

		$output = $theme->output('html/panel/info');

		return $output;
	}
}
