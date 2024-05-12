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

require_once(dirname(__DIR__) . '/views.php');

jimport('joomla.document.feed.feed');

class KomentoViewRss extends KomentoView
{
	/**
	 * Renders the feed view for comments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		if (!$this->config->get('enable_rss')) {
			return;
		}

		$component = $this->input->get('component', 'all', 'string');
		$cid = $this->input->get('cid', 0, 'int');
		$cid = !$cid ? 'all' : $cid;

		// Default document link
		$this->doc->link = JURI::root();

		// Filter comments by specific unique item
		if ($component != 'all' && $cid != 'all') {
			$application = KT::loadApplication($component)->load($cid);

			if ($application === false) {
				$application = KT::getErrorApplication($component, $cid);
			}

			$contentTitle = ($component !== 'all' && $cid !== 'all') ? $application->getContentTitle() : '';

			$this->doc->link = $application->getContentPermalink();
		}

		$this->doc->setTitle(JText::_('COM_KOMENTO_FEEDS_LATEST_TITLE'));
		$this->doc->setDescription(JText::_('COM_KOMENTO_FEEDS_LATEST_TITLE_DESCRIPTION'));

		if ($component === 'all') {

			// impossible all component and specific article
			if ($cid !== 'all') {
				throw FH::exception('Invalid filter type', 500);
			}

			$this->doc->setTitle(JText::_('COM_KOMENTO_FEEDS_ALL_COMMENTS_TITLE'));
			$this->doc->setDescription(JText::_('COM_KOMENTO_FEEDS_ALL_COMMENTS_TITLE_DESCRIPTION'));
		} else {

			// Render comments from all unique items
			if ($cid === 'all') {
				$this->doc->setTitle(JText::_('COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_TITLE' ) . ' : ' . KT::loadApplication($component)->getComponentName());
				$this->doc->setDescription(JText::_('COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_TITLE_DESCRIPTION'));
			}

			// Render comments from specific item
			if ($cid !== 'all') {
				$this->doc->setTitle(JText::_('COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_OF_ARTICLE_TITLE' ) . ' : ' . KT::loadApplication($component)->getComponentName() . ' : ' . $contentTitle);
				$this->doc->setDescription(JText::_('COM_KOMENTO_FEEDS_COMMENTS_FOR_COMPONENT_OF_ARTICLE_TITLE_DESCRIPTION'));
			}
		}

		$options = [
			'sort' => 'latest',
			'limit' => $this->config->get('rss_max_items'),
			'userid' => 'all',
			'threaded' => 0
		];

		$model = KT::model('Comments');
		$comments = $model->getComments($component, $cid, $options);

		if (!$comments) {
			return;
		}

		// Format the comments 
		$comments = KT::formatter('comment', $comments);

		foreach ($comments as $comment) {
			$item = new JFeedItem();
			$item->title = JText::sprintf('COM_KOMENTO_FEEDS_COMMENT_TITLE', $comment->getCreatedDate()->toSql());
			$item->link = $comment->permalink;
			$item->description = $comment->comment;
			$item->date = $comment->getCreatedDate()->toSql();
			$item->author = $comment->name;
			$item->authorEmail = $comment->email;

			$this->doc->addItem($item);
		}
	}
}
