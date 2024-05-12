<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoString
{
	/**
	 * Deterects a list of name matches using @ symbols
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function detectNames($text, $exclude = [])
	{
		$extendedlatinPattern = "\\x{0c0}-\\x{0ff}\\x{100}-\\x{1ff}\\x{180}-\\x{27f}";
		$arabicPattern = "\\x{600}-\\x{6FF}";
		$pattern = '/@[' . $extendedlatinPattern . $arabicPattern .'A-Za-z0-9][' . $extendedlatinPattern . $arabicPattern . 'A-Za-z0-9_\-\.\s\,\&]+/ui';

		$text = html_entity_decode($text);


		preg_match_all($pattern, $text, $matches);

		if (!isset($matches[0]) || !$matches[0]) {
			return false;
		}

		$result = $matches[0];

		$users = [];

		foreach ($result as $name) {
			$name = str_ireplace(['@','#'], '', $name);

			// Given a name, try to find the correct user id.
			$model = KT::model('Users');
			$id = $model->getUserId('name', $name);

			if (!$id || in_array($id, $exclude)) {
				continue;
			}

			$users[] = KT::user($id);
		}

		return $users;
	}
}