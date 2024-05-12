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

class KomentoForm
{
	/**
	 * Determines if captcha is needed on the form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function requireCaptcha()
	{
		static $required = null;

		if (is_null($required)) {
			$config = KT::config();
			$groups = $config->get('show_captcha');

			if (!is_array($groups)) {
				$groups = trim($groups);
				$groups = explode(',', $groups);
			}

			$profile = KT::user();
			$userGroups = $profile->getUserGroups();
			$required = false;

			foreach ($userGroups as $userGroupId) {
				if (in_array($userGroupId, $groups)) {
					$required = true;
					break;
				}
			}
		}

		return $required;
	}

	/**
	 * Determines if a given field is required on the form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function requireField($name)
	{
		$my = JFactory::getUser();
		$config	= KT::config();
		$require = false;

		if ($config->get($name) == 2 || ($config->get($name) == 1 && $my->guest)) {
			$require = true;
		}

		return $require;
	}

	/**
	 * Determines if the terms and conditions field is required on the form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function requireTerms()
	{
		static $terms = null;

		if (is_null($terms)) {
			$config = KT::config();
			$groups = $config->get('show_tnc', '');

			if (!is_array($groups)) {
				$groups = explode(',', $groups);
			}

			$profile = KT::user();
			$userGroups = $profile->getUserGroups();

			// Default to not require terms
			$terms = false;

			foreach ($userGroups as $userGroupId) {
				if (in_array($userGroupId, $groups)) {
					$terms = true;
					break;
				}
			}
		}

		return $terms;
	}

	/**
	 * Determines if a given field should be visible on the form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function showField($name)
	{
		$my = JFactory::getUser();
		$config = KT::config();
		$show = false;

		if ($config->get($name) == 2 || ($config->get($name) == 1 && $my->guest)) {
			$show = true;
		}

		return $show;
	}
}
