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

class Attachment extends Base
{
	/**
	 * Renders the attachment html output
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function item($attachment)
	{
		Scripts::load('lightbox');
		
		$themes = $this->getTemplate();
		$themes->set('attachment', $attachment);

		$output = $themes->output('html/attachment/item');

		return $output;
	}

	/**
	 * Renders the attachment html template that is used by respective extensions
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function template($options = [])
	{
		$download = \FH::normalize($options, 'download', false);

		$theme = $this->getTemplate();
		$theme->set('download', $download);
		$output = $theme->output('html/attachment/template');

		return $output;
	}
}
