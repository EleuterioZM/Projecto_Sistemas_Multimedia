<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Html\Base;
use Foundry\Libraries\Scripts;

class AdminWidgets extends Base
{
	/**
	 * Generates recent comments widget at the backend
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function comments($comments, $emptyText = 'FD_NO_COMMENTS_YET')
	{
		// Comment properties must consist of
		//
		// $comment->authorLink - Link to the author
		// $comment->authorName - Name of the author
		// $comment->permalink - Permalink to the comment item
		// $comment->itemTitle - Title of the article
		// $comment->created - Posted date

		$theme = $this->getTemplate();
		$theme->set('comments', $comments);
		$theme->set('emptyText', $emptyText);

		$html = $theme->output('html/admin.widgets/comments');

		return $html;
	}

	/**
	 * Generates news widget on the dashboard
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function news()
	{
		Scripts::load('admin');

		$theme = $this->getTemplate();
		$html = $theme->output('html/admin.widgets/news');

		return $html;
	}

	/**
	 * Generates statistics widget at the backend
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function statistics($title, $description, $items)
	{
		if (!$description) {
			$description = $title . '_DESC';
		}
		
		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('description', $description);
		$theme->set('items', $items);

		$html = $theme->output('html/admin.widgets/statistics');

		return $html;
	}

	/**
	 * Generates tabs for the back-end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function tabs($tabs)
	{
		// In order for EasyBlog dashboard widget to operate correctly, this is a duplicate of tabs.render
		Scripts::load('shared');

		// Tab properties must contain the following:
		// - id
		// - title
		// - active (bool)

		$theme = $this->getTemplate();
		$theme->set('tabs', $tabs);

		$html = $theme->output('html/admin.widgets/tabs');

		return $html;
	}

	/**
	 * Generates versioning widget on the dashboard
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function version($apiKey, $installedVersion, $apiUrl, $updateTaskUrl)
	{
		Scripts::load('admin');
		
		$theme = $this->getTemplate();
		$theme->set('apiKey', $apiKey);
		$theme->set('apiUrl', $apiUrl);
		$theme->set('installedVersion', $installedVersion);
		$theme->set('updateTaskUrl', $updateTaskUrl);

		$html = $theme->output('html/admin.widgets/version');

		return $html;
	}
}
