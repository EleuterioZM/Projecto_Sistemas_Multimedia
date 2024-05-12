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

class KomentoLocation
{
	public $key = null;

	static $providers = array();

	protected $provider;

	public $providersBaseClassname = 'KomentoLocationProviders';

	public function __construct($provider = null)
	{
		$this->loadProvider($provider);
	}

	public function loadProvider($provider = null)
	{
		// If provider is empty, then we get it based on settings
		if (empty($provider)) {
			$provider = KT::config()->get('location_service_provider', '');
		}

		$this->provider = $this->getProvider($provider);

		return $this->provider;
	}

	/**
	 * Retrieves the location provider
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getProvider($provider)
	{

		if (!isset(self::$providers[$provider])) {

			$providerFile = dirname(__FILE__) . '/providers/' . $provider . '.php';

			if (!JFile::exists($providerFile)) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError(JText::_('COM_KT_LOCATION_PROVIDERS_PROVIDER_FILE_NOT_FOUND'));
				return $fallback;
			}

			require_once($providerFile);

			$providerClassname = $this->providersBaseClassname . ucfirst($provider);

			if (!class_exists($providerClassname)) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError(JText::_('COM_KT_LOCATION_PROVIDERS_PROVIDER_CLASS_NOT_FOUND'));
				return $fallback;
			}

			$providerClass = new $providerClassname;

			// If provider is not a extended class from abstract class, we do not want it
			if (!is_a($providerClass, $this->providersBaseClassname)) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError(JText::_('COM_KT_LOCATION_PROVIDERS_PROVIDER_INVALID_CLASS'));
				return $this->provider;
			}

			// Now we check if the provider constructed properly
			if ($providerClass->hasErrors()) {
				dump($providerClass->hasErrors());
				$fallback = $this->getProvider('fallback');
				$fallback->setError($providerClass->getError());
				return $fallback;
			}

			self::$providers[$provider] = $providerClass;
		}

		return self::$providers[$provider];
	}

	public function __call($method, $arguments)
	{
		if (!isset($this->provider)) {
			$this->loadFallbackProvider();
		}

		return call_user_func_array(array($this->provider, $method), $arguments);
	}

	/**
	 * Determines if the location services are enabled
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {
			$enabled = true;

			$config = KT::config();

			if (!$config->get('enable_location')) {
				$enabled = false;
			}

			if ($config->get('location_service_provider') === 'maps' && !$config->get('location_key')) {
				$enabled = false;
			}
		}

		return $enabled;
	}
}
