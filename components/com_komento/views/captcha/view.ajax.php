<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoViewCaptcha extends KomentoView
{
	/**
	 * Allows caller to reload a captcha image
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function reload()
	{
		$code = KT::captcha()->getReloadSyntax();

		return $this->ajax->resolve($code);
	}
}
