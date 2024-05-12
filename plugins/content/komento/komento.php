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

/************************************************************************************************
DEVELOPER'S NOTE - To integrate com_komento to yours, simply refer to the follwing samples:
*************************************************************************************************

2 LINES SIMPLE VERSION:

require_once(JPATH_ROOT . '/components/com_komento/bootstrap.php');
KT::commentify('com_yourextension', $content, array( 'params' => ''));

************************************************************************************************/

jimport('joomla.plugin.plugin');

class plgContentKomento extends JPlugin
{
	private $extension = null;

	/**
	 * Loads Komento's dependency codes
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function loadBootstrap()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			$loaded = false;

			$file = JPATH_ROOT . '/components/com_komento/bootstrap.php';

			jimport('joomla.filesystem.file');

			// Check if komento exists
			if (JFile::exists($file)) {
				require_once($file);
				$loaded = true;
			}
		}

		return $loaded;
	}

	/**
	 * Integrations with com_redshop extension to render comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterDisplayProduct(&$template_desc, $params = false, $data = 0)
	{
		if ($this->extension != 'com_redshop') {
			return;
		}

		return $this->execute( __FUNCTION__, null, $template_desc, $params, $data );
	}

	/**
	 * Integrations with com_jshopping extension to render comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onBeforeDisplayProductView(&$view)
	{
		if ($this->extension !== 'com_jshopping') {
			return;
		}

		$jshopConfig = JSFactory::getConfig();
		$product = $view->product;

		$contents = $this->execute(__FUNCTION__, 'jshopping_products', $product, $jshopConfig, '');

		$view->_tmp_product_html_before_review = $contents;
	}

	/**
	 * Integrations with com_k2 extension to render comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onK2CommentsBlock(&$item, &$params, $limitstart)
	{
		return $this->execute(__FUNCTION__, 'k2block', $item, $params, $limitstart);
	}

	/**
	 * Integrations with com_k2 extension to render the comment counter on listings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onK2CommentsCounter(&$item, &$params, $limitstart)
	{
		$this->extension = 'com_k2';

		return $this->execute(__FUNCTION__, 'k2counter', $item, $params, $limitstart);
	}

	/**
	 * Integrations with com_k2 extension to render the comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onK2BeforeDisplayContent(&$item, &$params, $limitstart)
	{
		$this->extension = 'com_k2';
		return $this->execute(__FUNCTION__, 'k2counter', $item, $params, $limitstart);
	}


	/**
	 * Integrations with com_easyblog extension to render the comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onDisplayComments(&$blog, &$articleParams)
	{
		return $this->execute(__FUNCTION__, null, $blog, $articleParams, 0);
	}

	/**
	 * This trigger entry point is used for com_ohanah and com_ohanah's venue
	 * We are now use this trigger instead because of some issue in Ohanah integration
	 *
	 * @since 2.0.9
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params, $page);
	}

	public function onBeforeDisplayContent( &$article, &$articleParams, $limitstart, $page = 0 )
	{
		return $this->execute(__FUNCTION__, null, $article, $params, $page);
	}

	/**
	 * This trigger entry point is used for com_content, com_flexicontent, com_virtuemart, com_dpcalendar
	 * 
	 * NOTE: For DPCalendar, $article is the event item object.
	 *
	 * @since 2.0.9
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params, $page);
	}

	/**
	 * This trigger entry point is used for Ohanah Venue's
	 *
	 * @since 2.0.9
	 */
	public function onAfterDisplayContent(&$article, &$articleParams, $limitstart, $page = 0)
	{
		return $this->execute(__FUNCTION__, null, $article, $params, $page);
	}

	/**
	 * com_tz_portfolio
	 *
	 */
	public function onTZPortfolioCommentDisplay($context, &$article, $params)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params);
	}

	/**
	 * Integrations with com_jblance extension to render comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onJBlanceCommentDisplay($context, &$article, $params)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params);
	}

	/**
	 * Integrations with com_jdownloads extension to render comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params, $page);
	}

	/**
	 * Integrations with com_content extension to render comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPrepareContent(&$article, &$params, $limitstart, $page = 0)
	{
		return $this->execute(__FUNCTION__, null, $article, $params, $page);
	}

	/**
	 * Main execution code for Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function execute($eventTrigger, $context, &$article, &$params, $page = 0)
	{
		// Load bootstrap
		if (!$this->loadBootstrap()) {
			return;
		}

		$input = JFactory::getApplication()->input;

		// If unknown extension, try to get it from the REQUEST
		if (!$this->extension) {
			$this->extension = $input->get('option', '', 'cmd');
		}

		// We cannot render hikashop on the description
		if ($this->extension === 'com_hikashop') {
			return;
		}

		// Fix flexicontent's mess as they are trying to reset the option=com_flexicontent to com_content.
		$isFlexiContent = $input->get('isflexicontent', '', 'default');

		if ($isFlexiContent) {
			$this->extension = 'com_flexicontent';
		}

		// Ohanah Venue plugin
		if ($context === 'com_ohanah.venue') {
			$this->extension = 'com_ohanahvenue';
		}

		// @task: trigger onAfterEventTriggered
		$renderExtension = KT::onAfterEventTriggered(__CLASS__, $eventTrigger, $this->extension, $context, $article, $params);

		if (!$renderExtension) {
			return false;
		}

		$options = [
			'trigger' => $eventTrigger,
			'context' => $context,
			'params' => $params,
			'page' => $page
		];

		$contents = KT::commentify($this->extension, $article, $options);

		return $contents;
	}
}
