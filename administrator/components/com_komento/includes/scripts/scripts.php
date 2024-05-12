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

use Foundry\Libraries\Scripts;

class KomentoScripts
{
	public $dependencies = [];
	public $baseurl = null;
	private $async = false;
	private $defer = false;
	private $location = 'site';
	static $attached = false;

	public function __construct()
	{
		if (!defined('KOMENTO_CLI')) {
			$this->jconfig = FH::jconfig();
			$this->doc = JFactory::getDocument();
			$this->config = KT::config();
			$this->environment = $this->config->get('komento_environment');
		}

		$this->baseurl = JURI::root(true);
		$this->dependencies = $this->getDependencies();

		// For legacy purposes
		if ($this->environment == 'static') {
			$this->environment = 'production';
		}

		$cdn = KT::getCdnUrl();

		if ($this->environment == 'production' && $cdn) {
			$this->baseurl = $cdn;
		}

		if (!defined('KOMENTO_CLI') && FH::isFromAdmin()) {
			$this->location = 'admin';
		}
	}

	/**
	 * Generates a configuration string for EasySocial's javascript library
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getJavascriptConfiguration()
	{
		$siteName = $this->jconfig->get('sitename');
		$locale = JFactory::getLanguage()->getTag();

		ob_start();
?>
<!--googleoff: index-->
<script type="text/javascript">
window.kt = {
	"appearance": "<?php echo FH::isFromAdmin() ? 'light' : $this->config->get('layout_appearance');?>",
	"theme": "<?php echo $this->config->get('layout_accent');?>",
	"ajaxUrl": "<?php echo KT::fd()->getAjaxUrl($this->config->get('komento_ajax_index'));?>",
	"direction": "<?php echo $this->doc->getDirection();?>",
	"environment": "<?php echo $this->environment;?>",
	"ios": <?php echo FH::responsive()->isIphone() ? 'true' : 'false';?>,
	"locale": "<?php echo $locale;?>",
	"mobile": <?php echo FH::responsive()->isMobile() ? 'true' : 'false'; ?>,
	"rootUrl": "<?php echo rtrim(JURI::root(), '/');?>",
	"ratings": {
		"options": {
			"starType": 'i',
			"half": true,
			"starOn": 'raty raty-star raty-star--on',
			"starOff": 'raty raty-star-o raty-star--off',
			"starHalf": 'raty raty-star-half-o',
			"hints": [
				"<?php echo JText::_('COM_KOMENTO_RATINGS_HINT_BAD', true); ?>", 
				"<?php echo JText::_('COM_KOMENTO_RATINGS_HINT_POOR', true); ?>",
				"<?php echo JText::_('COM_KOMENTO_RATINGS_HINT_REGULAR', true); ?>",
				"<?php echo JText::_('COM_KOMENTO_RATINGS_HINT_GOOD', true); ?>",
				"<?php echo JText::_('COM_KOMENTO_RATINGS_HINT_GORGEOUS', true); ?>"
			]
		}
	},
	"siteName": "<?php echo FH::escape($siteName);?>",
	"token": "<?php echo FH::token();?>",
	"isHttps": <?php echo FH::isHttps() ? 'true' : 'false'; ?>,
};
</script>
<!--googleon: index-->
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Retrieves the main dependencies from vendors
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getDependencies($absolutePath = false, $jquery = true)
	{
		$coreFiles = [
			'lodash.js',
			'bootstrap3.js',
			'utils.js',
			'uri.js',
			'mvc.js',
			'joomla.js',
			'module.js',
			'script.js',
			'template.js',
			'require.js',
			'server.js',
			'component.js'
		];

		// Determines if we should include jquery.komento.js library
		if ($jquery) {
			array_unshift($coreFiles, 'jquery.komento.js');
		} else {
			array_unshift($coreFiles, 'jquery.js');
		}

		if ($absolutePath) {
			foreach ($coreFiles as &$file) {
				$file = KOMENTO_SCRIPTS . '/vendors/' . $file;
			}
		}

		return $coreFiles;
	}

	/**
	 * Generates script tags that should be added on the page
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function createScriptTag($path)
	{
		$script = '<script src="' . $path . '"></script>';

		return $script;
	}

	/**
	 * Generates the file name
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getFileName($section, $jquery = true)
	{
		$version = KT::getLocalVersion();
		$file = $section . '-' . $version;

		if (!$jquery && $section != 'admin') {
			$file .= '-basic';
		}

		return $file;
	}

	/**
	 * Generates the file path
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getFileUri($section, $minified = true, $jquery = true)
	{
		$path = $this->baseurl . '/media/com_komento/scripts/' . $this->getFileName($section, $jquery);

		$extension = '.js';

		if ($minified) {
			$extension = '.min.js';
		}

		$path .= $extension;

		return $path;
	}

	/**
	 * Attaches the necessary script libraries on the page
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function attach()
	{
		// Only attach the scripts on the page once.
		if (self::$attached) {
			return true;
		}

		// We should only attach scrips on html documents otherwise JDocument would hit an error
		if ($this->doc->getType() != 'html') {
			return;
		}

		// Add configurations about the site
		$configuration = $this->getJavascriptConfiguration();
		$this->doc->addCustomTag($configuration);

		// check if we need to load komento's jquery or not.
		$loadKomentoJQuery = $this->config->get('komento_jquery');

		if (!$loadKomentoJQuery) {
			// lets check if EB install or not. if yes, we force it to load komento's jquery #201
			if (KT::EasyBlog()->exists()) {
				$loadKomentoJQuery = true;
			}
		}

		// In production mode, we need to attach the compiled scripts
		if ($this->environment === 'production') {

			$minified = true;

			// test if ES or EB already load Joomla jquery or not. if yes, we have to load our own jquery
			if (!$loadKomentoJQuery) {

				// test if EB or KMT already load Joomla jquery or not. if yes, we have to load our own jquery
				if (defined('COM_EASYSOCIAL_JQUERY_FRAMEWORK') || defined('COM_EASYBLOG_JQUERY_FRAMEWORK') || class_exists('ES') || class_exists('KB')) {
					$loadKomentoJQuery = true;
				}

			}

			// If jquery is not rendered, we need to trigger Joomla to enforce it to load jquery
			if (!$loadKomentoJQuery) {
				define('COM_KOMENTO_JQUERY_FRAMEWORK', 1);
				JHtml::_('jquery.framework');
			}

			$fileName = $this->getFileUri($this->location, $minified, $loadKomentoJQuery);

			$this->doc->addCustomTag($this->createScriptTag($fileName));
		}

		// We need to initialize foundry dialog
		KT::fd()->html('dialog.init');

		// In development mode, we need to attach the easysocial main entry file so the system knows which files to be
		// rendered asynchronously.
		if ($this->environment === 'development') {

			// Render the bootloader on the page first
			$bootloader = $this->baseurl . '/media/com_komento/scripts/bootloader.js';
			$this->doc->addCustomTag($this->createScriptTag($bootloader));

			// Render dependencies from the core
			$dependencies = $this->getDependencies(false, $loadKomentoJQuery);

			// If jquery is not rendered, we need to trigger Joomla to enforce it to load jquery
			if (!$loadKomentoJQuery) {
				JHtml::_('jquery.framework');
			}

			foreach ($dependencies as $dependency) {
				$path = $this->baseurl . '/media/com_komento/scripts/vendors/' . $dependency;

				$this->doc->addCustomTag($this->createScriptTag($path));
			}

			// Render easysocial's dependencies
			$script = $this->createScriptTag($this->baseurl . '/media/com_komento/scripts/' . $this->location . '/' . $this->location . '.js');
			$this->doc->addCustomTag($script);
		}

		// Determines if we should attach the migrate script
		$loadMigrateScript = $this->renderMigrateScript();

		if ($loadMigrateScript) {
			$migrate = $this->baseurl . '/media/vendor/jquery-migrate/js/jquery-migrate.min.js';

			$this->addScript($migrate);
		}

		if ($this->config->get('enable_live_notification')) {
			Scripts::load('toast');
		}

		self::$attached = true;
	}

	/**
	 * Allows caller to attach an external script
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function addScript($url)
	{
		$tag = $this->createScriptTag($url);

		$this->doc->addCustomTag($tag);
	}

	/**
	 * Determines if we should render the jquery migrate script
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function renderMigrateScript()
	{
		static $loadMigrateScript = null;

		if (is_null($loadMigrateScript)) {
			$loadMigrateScript = false;

			// If the site is rendering EasyBlog's jquery, we do not need the jquery-migrate
			if ($this->config->get('komento_jquery') || !FH::isJoomla4()) {
				return $loadMigrateScript;
			}

			// For Joomla 4, we need to render the jquery-migrate.min.js for our scripts to work
			$loadMigrateScript = true;
		}

		return $loadMigrateScript;
	}
}