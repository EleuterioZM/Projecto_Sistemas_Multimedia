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
defined('_JEXEC') or die('Unauthorized Access');

class OembedAdapterGist extends OembedAdapter
{
	/**
	 * Tests to see if the url is a valid url for this adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValid($url)
	{
		if (stristr($url, 'gist.github.com') === false) {
			return false;
		}

		return true;
	}

	public function process(&$result)
	{
		$oembed = new stdClass();
		$oembed->html = '<script src="' . $this->url . '.js"></script>';

		$result->oembed = $oembed;
	}
}
