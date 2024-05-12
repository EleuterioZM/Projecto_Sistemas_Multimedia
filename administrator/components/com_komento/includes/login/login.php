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

class KomentoLogin
{
	public function getRegistrationLink()
	{
		$config = KT::config();

		$link = JRoute::_('index.php?option=com_users&view=registration');

		
		switch($config->get('login_provider'))
		{
			case 'cb':
				$link = JRoute::_('index.php?option=com_comprofiler&task=registers');
				break;
			break;

			case 'joomla':
				$link = JRoute::_('index.php?option=com_users&view=registration');
			break;

			case 'jomsocial':
				$link = JRoute::_('index.php?option=com_community&view=register');
			break;

			case 'easysocial':
				$easysocial = KT::easysocial();

				if ($easysocial->exists()) {
					$link = FRoute::registration();
				}
			break;
		}

		return $link;
	}

	public function getLoginLink($returnURL = '')
	{
		$config = KT::config();

		if (!empty($returnURL)) {
			$returnURL = '&return=' . $returnURL;
		}

		$link = JRoute::_('index.php?option=com_users&view=login' . $returnURL);

		switch($config->get('login_provider'))
		{
			case 'cb':
				$link = JRoute::_('index.php?option=com_comprofiler&task=login' . $returnURL);
				break;
			break;

			case 'joomla':
			case 'jomsocial':
				$link = JRoute::_('index.php?option=com_users&view=login' . $returnURL);
			break;

			case 'easysocial':
				$easysocial = KT::easysocial();

				if ($easysocial->exists()) {
					$link = FRoute::login();
				}
			break;
		}

		return $link;
	}

	/**
	 * Get a login form HTML
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getLoginForm($returnURL = '')
	{
		$config = KT::config();
		$theme = KT::themes();

		$usernameField = 'COM_KOMENTO_LOGIN_USERNAME';

		if (KT::easysocial()->exists() && $config->get('login_provider') == 'easysocial') {
			
			$esConfig = ES::config();

			$usernameField = $esConfig->get('general.site.loginemail') ? 'COM_KOMENTO_LOGIN_NAME_OR_EMAIL' : 'COM_KOMENTO_LOGIN_USERNAME';
			if ($esConfig->get('registrations.emailasusername')) {
				$usernameField = 'COM_KOMENTO_LOGIN_EMAIL';
			}
		}

		if (empty($returnURL)) {
			$returnURL = base64_encode(JURI::getInstance()->toString() . '#commentform');
		}

		$theme->set('usernameField', $usernameField);
		$theme->set('returnURL', $returnURL);
		$theme->set('uniqid', uniqid());
		$contents = $theme->output('site/login/default');

		return $contents;
	}

	public function getResetPasswordLink()
	{
		$config	= KT::config();

		$link = JRoute::_('index.php?option=com_users&view=reset');

		switch($config->get('login_provider'))
		{
			case 'cb':
				$link = JRoute::_('index.php?option=com_comprofiler&task=lostpassword');
			break;

			case 'joomla':
			case 'jomsocial':
				$link = JRoute::_('index.php?option=com_users&view=reset');
			break;

			case 'easysocial':
				$easysocial = KT::easysocial();

				if ($easysocial->exists()) {
					$link = FRoute::account(array('layout' => 'forgetPassword'));
				}
			break;
		}

		return $link;
	}

	public function getRemindUsernameLink()
	{
		$config = KT::config();

		$link = JRoute::_('index.php?option=com_users&view=remind');

		switch($config->get('login_provider'))
		{
			case 'easysocial':
				$easysocial = KT::easysocial();

				if ($easysocial->exists()) {
					$link = FRoute::account(array('layout' => 'forgetPassword'));
				}
			break;

			default:
				$link	= JRoute::_('index.php?option=com_users&view=remind');

			break;
		}

		return $link;
	}
}
