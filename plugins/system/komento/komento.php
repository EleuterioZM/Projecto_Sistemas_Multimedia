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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemKomento extends JPlugin
{
	private $extension = null;

	/**
	 * Disable page cache when komento is present. #612
	 *
	 * @since	4.0.3
	 * @access	public
	 */
	public function onPageCacheSetCaching()
	{
		if ($this->exists()) {
			return false;
		}

		return true;
	}

	/**
	 * Detects whether Komento is installed on the site.
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php';

			$exists = true;

			if (!JFile::exists($file)) {
				$exists = false;
			}

			if ($exists) {
				require_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Inject scripts on the body for the page to process
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if ($doc->getType() != 'html') {
			return;
		}

		require_once(JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php');

		$scripts = KT::getScripts();

		// Nothing to be added on the page, skip this altogether
		if (!$scripts) {
			return;
		}
		
		$body = $app->getBody();
		#394 For some reason Joomla function for str_ireplace doesn't properly replaced some character that caused the page to breaks.
		$body = str_ireplace(array('</body>', '< /body>', '</ body>'), $scripts . '</body>', $body);

		$app->setBody($body);
	}

	public function onAfterDispatch()
	{
		if (JFactory::getApplication()->isClient('administrator')){
			return;
		}

		if (!$this->exists()) {
			return;
		}

		if (!KT::isFoundryEnabled()) {
			return;
		}

		$jConfig = FH::jconfig();
		$caching = $jConfig->get('caching');

		// This is to fixed styling not loaded when cache is enabled
		if ($caching !== '0') {
			KT::initFoundry();
			KT::initialize();
		}
	}

	/**
	 * com_joomgallery
	 *
	 */
	public function onJoomAfterDisplayDetailImage($image)
	{
		$params = new stdClass();

		return $this->execute(__FUNCTION__, null, $image, $params);
	}

	/**
	 * Trigger for Sobipro
	 *
	 * @since	2.0.9
	 * @access	public
	 */
	public function ContentDisplayEntryView(&$text)
	{
		$input = KT::request();

		// Skip komento trying to display comments in adding new listing
		if ($input->get('task', '', 'string') == 'entry.add') {
			return;
		}

		$article = new stdClass;
		$article->id = $input->get('sid', '', 'int');
		$article->text = $text;

		$params = new stdClass();

		$this->execute(__FUNCTION__, null, $article, $params, null);
	}

	public function AfterDisplayEntryView()
	{
		$input = KT::request();

		// Skip komento trying to display comments in adding new listing
		if ($input->get('task', '', 'string') == 'entry.add') {
			return;
		}

		$article = new stdClass;
		$article->id = $input->get('sid', '', 'int');
		$article->text = '';
		$params = new stdClass;

		$this->execute(__FUNCTION__, null, $article, $params, null);
	}

	private function execute($eventTrigger, $context, &$article, &$params, $page = 0)
	{
		static $bootstrap = null;

		// If bootstrap isn't loaded yet, try to load the bootstrap
		if (is_null($bootstrap)) {

			$constants = JPATH_ADMINISTRATOR . '/components/com_komento/constants.php';

			if (!JFile::exists($constants)) {
				$bootstrap = false;

				return false;
			}

			// Include necessary files
			require_once($constants);
			require_once(KOMENTO_BOOTSTRAP);

			$bootstrap = true;
		}

		if ($bootstrap === false) {
			return false;
		}

		if (!$this->extension) {
			$this->extension = JFactory::getApplication()->input->getCmd('option');
		}

		// @task: trigger onAfterEventTriggered
		$result = KT::onAfterEventTriggered(__CLASS__, $eventTrigger, $this->extension, $context, $article, $params);

		if (!$result) {
			return false;
		}

		// Passing in the data
		$options = [
			'trigger' => $eventTrigger,
			'context' => $context,
			'params' => $params,
			'page' => $page
		];

		return KT::commentify($this->extension, $article, $options);
	}

	/**
	 * Remove associated comment if user delete article 
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function onContentAfterDelete($context, $article)
	{
		if (!$this->exists()) {
			return;
		}

		$input = KT::request();
		$task = $input->get('task', '', 'string');

		if ($task !== 'delete' && !$context && !$article) {
			return;
		}

		$context = explode('.', $context);
		$context = $context[0];

		$components = KT::components()->getAvailableComponents();

		// check for the article context whether exist available integration component
		if (!in_array($context, $components)) {
			return;
		}

		$model = KT::model('comments');
		$result = $model->deleteArticleComments($context, $article->id);

		return true;
	}
}
