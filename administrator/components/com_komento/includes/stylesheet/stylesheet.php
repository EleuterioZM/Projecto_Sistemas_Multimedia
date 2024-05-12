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

use Foundry\Libraries\StyleSheets;

class KomentoStylesheet
{
	private $config = null;
	public $app = null;
	public $location = null;
	public $environment = null;

	public function __construct($location)
	{
		$this->config = KT::config();
		$this->app = JFactory::getApplication();

		$this->location = $location;

		if (!defined('KOMENTO_CLI')) {
			$this->environment = $this->config->get('komento_environment');
		}

		// For legacy purposes
		if ($this->environment === 'static') {
			$this->environment = 'production';
		}
	}

	/**
	 * Attaches stylesheet on the site.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function attach()
	{
		$isDev = $this->environment == 'development';

		if ($this->location === 'admin') {
			// Backend always renders fontawesome
			$this->attachFontawesome();

			$extension = $isDev ? 'css' : 'min.css';
			StyleSheets::add('/media/foundry/css/joomla-backend.' . $extension, 'component');

			return $this->attachStylesheet('/media/com_komento/themes/admin/css/style');
		}

		// Do not load the built-in font-awesome file if the setting is turn off
		if ($this->config->get('enable_fontawesome')) {
			$this->attachFontawesome();
		}

		// Attach dependencies
		$this->attachDependencies();

		// Attach frontend stylesheets
		$this->attachStylesheet('/media/com_komento/themes/wireframe/css/style');

		// For rtl, we need to handle this manually
		$doc = JFactory::getDocument();
		$direction = $doc->getDirection();

		if ($direction === 'rtl') {
			$this->attachStylesheet('/media/com_komento/themes/wireframe/css/style-rtl');
		} 

		// Check if custom.css exists on the site as template overrides
		$file = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_komento/css/custom.css';
		$exists = JFile::exists($file);

		if ($exists) {
			$this->attachStylesheet('/templates/' . $this->app->getTemplate() . '/html/com_komento/css/custom', false);
		}
	}

	/**
	 * Attach dependency stylesheets
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function attachDependencies()
	{
		// Attach prism if we are using code
		$config = KT::config();
		
		if ($config->get('bbcode_code')) {
			StyleSheets::load('prism');
		}

		// These are required by Komento
		$dependencies = [
			'lightbox',
			'markitup'
		];

		foreach ($dependencies as $dependency) {
			StyleSheets::load($dependency);	
		}
	}

	/**
	 * Internal method to attach stylesheets 
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	private function attachStylesheet($file, $applyMinify = true, $cacheBusting = true)
	{
		if ($cacheBusting) {
			static $hash = null;

			if (is_null($hash)) {
				$hash = md5(KT::getLocalVersion());
			}
		}
		
		if ($this->environment === 'production' && $applyMinify) {
			$file .= '.min';
		}

		$file .= '.css';

		$baseUrl = '';

		// If cdn is enabled, we need to update the base url
		$cdn = KT::getCdnUrl();

		if ($this->environment === 'production' && $cdn) {
			$baseUrl = $cdn;
		}

		$uri = rtrim($baseUrl, '/') . $file;

		if ($cacheBusting) {
			$uri .= '?' . $hash . '=1';
		}

		return StyleSheets::add($uri, 'component');
	}

	/**
	 * Attaches font awesome css library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function attachFontawesome()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			StyleSheets::load('fontawesome');

			$loaded = true;
		}

		return $loaded;
	}
}