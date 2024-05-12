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

class KomentoThemesHtml
{
	/**
	 * Displays the avatar of the user
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function avatar($user = null, $name = '', $email = '', $website = '')
	{
		$config = KT::config();

		if (!$config->get('layout_avatar_enable')) {
			return;
		}

		$id = null;

		if (is_string($user) || is_int($user)) {
			$id = $user;
		}

		if (is_null($user)) {
			$id = (int) JFactory::getUser()->id;
		}

		if (is_object($user)) {
			$id = (int) $user->id;
		}

		$user = KT::user($id);

		// Pass in a guest name for the avatar image alt value
		if (!$name && !$user->id) {
			$name = JText::_('COM_KOMENTO_GUEST');
		}

		static $items = [];

		$key = $id . $name . $email . $website;

		if (isset($items[$key])) {
			return $items[$key];
		}

		$themes = KT::themes();
		$themes->set('name', $name);
		$themes->set('email', $email);
		$themes->set('website', $website);
		$themes->set('user', $user);

		$items[$key] = $themes->output('site/html/avatar');

		return $items[$key];
	}

	/**
	 * Displays the name of the user
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function name($user = null, $name = '', $email = '', $website = '', $application = null, $options = [])
	{
		$class = FH::normalize($options, 'class', '');
		$config = KT::config();

		$id = null;

		if (is_int($user) || is_string($user)) {
			$id = $user;
		}

		if (is_null($user)) {
			$id = JFactory::getUser()->id;
		}

		if (is_object($user)) {
			$id = $user->id;
		}

		$applicationAuthorId = null;

		if ($application) {
			$applicationAuthorId = $application->getAuthorId();
		}

		static $items = [];

		$key = $id . $name . $email . $website . $applicationAuthorId;

		if (isset($items[$key])) {
			return $items[$key];
		}

		$user = KT::user($id);
		$config = KT::config();

		$nofollow = $config->get('links_nofollow') ? ' rel="nofollow"' : '';

		if ($website) {
			$nofollow = ' rel="nofollow"';
		}

		$permalink = FH::escape($user->getProfileLink($email, $website));
		$name = $user->getName($name);

		$theme = KT::themes();
		$theme->set('class', $class);
		$theme->set('applicationAuthorId', $applicationAuthorId);
		$theme->set('nofollow', $nofollow);
		$theme->set('user', $user);
		$theme->set('name', $name);
		$theme->set('permalink', $permalink);
		$items[$key] = $theme->output('site/html/name');

		return $items[$key];
	}

	/**
	 * Helper to display the date format 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function date($dateString)
	{
		$config = KT::config();
		$date = FH::date($dateString, true);

		if ($config->get('enable_lapsed_time')) {
			return $date->toLapsed();
		}
		
		return $date->format($config->get('date_format'));
	}
}
