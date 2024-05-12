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

class Admin extends Base
{
	/**
	 * Generates a panel heading used at the back end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function headers($title, $description = '')
	{
		if (!$description && $description !== false) {
			$description = $title . '_DESC';
		}
		
		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('description', $description);

		$html = $theme->output('html/admin/headers');

		return $html;
	}

	/**
	 * Renders an outdated notice on the back-end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function notice($message, $type = 'info', $button = null, $options = [])
	{
		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');
		$icon = \FH::normalize($options, 'icon', 'fdi fa fa-bolt');
		$dismissible = \FH::normalize($options, 'dismissible', false);
		$dismissAttribute = \FH::normalize($options, 'dismissAttribute', 'data-fd-dismiss');

		// Ensure that the button truly has the correct properties
		if ($button && !is_object($button)) {
			$button = null;
		}

		if ($button) {
			$button->text = \FH::normalize($button, 'text', '');
			$button->type = \FH::normalize($button, 'type', '');
			$button->url = \FH::normalize($button, 'url', '');
			$button->attributes = \FH::normalize($button, 'attributes', '');
		}


		$theme = $this->getTemplate();
		$theme->set('class', $class);
		$theme->set('message', $message);
		$theme->set('button', $button);
		$theme->set('type', $type);
		$theme->set('attributes', $attributes);
		$theme->set('icon', $icon);
		$theme->set('dismissible', $dismissible);
		$theme->set('dismissAttribute', $dismissAttribute);

		$html = $theme->output('html/admin/notice');

		return $html;
	}

	/**
	 * Renders an outdated notice on the back-end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function outdated($message, $updateTaskUrl, $buttonText = 'FD_UPDATE_NOW')
	{
		Scripts::load('admin');

		$options = [
			'attributes' => 'data-fd-outdated-notice',
			'class' => 'hidden'
		];

		$button = (object) [
			'text' => $buttonText,
			'url' => $updateTaskUrl,
			'type' => 'danger',
			'attributes' => 'data-fd-update-button'
		];

		return $this->notice($message, 'danger', $button, $options);
	}

	/**
	 * Generates the sidebar for the back-end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function sidebar($menus, $view, $layout)
	{
		Scripts::load('admin');

		$theme = $this->getTemplate();
		$theme->set('menus', $menus);
		$theme->set('layout', $layout);
		$theme->set('view', $view);

		$html = $theme->output('html/admin/sidebar');

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
		Scripts::load('shared');

		// Tab properties must contain the following:
		// - id
		// - title
		// - active (bool)

		if (is_callable($tabs)) {
			$tabs = $tabs();
		}
		
		$theme = $this->getTemplate();
		$theme->set('tabs', $tabs);

		$html = $theme->output('html/admin/tabs');

		return $html;
	}

	/**
	 * Generates the help button for the back-end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function toolbarHelp($link, $title = 'JHELP')
	{
		$theme = $this->getTemplate();
		$theme->set('link', $link);
		$theme->set('title', $title);

		$html = $theme->output('html/admin/toolbar.help');

		return $html;
	}

	/**
	 * Generates the template for adding a dropdown actions on the toolbar
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function toolbarActions($title = 'FD_OTHER_ACTIONS', $actions = [])
	{
		// Actions should consist the following properties
		//
		// $action->title
		// $action->cmd

		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('actions', $actions);

		$html = $theme->output('html/admin/toolbar.actions');

		return $html;
	}

	/**
	 * Generates the template for grouping save buttons on the toolbar
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function toolbarSaveGroup()
	{
		$theme = $this->getTemplate();

		$html = $theme->output('html/admin/toolbar.save');

		return $html;
	}

	/**
	 * Renders the search DOM in the toolbar
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function toolbarSearch($placeholder = 'FD_SEARCH_FOR_SETTINGS')
	{
		$theme = $this->getTemplate();
		$theme->set('placeholder', $placeholder);

		$html = $theme->output('html/admin/toolbar.search');

		return $html;
	}

	/**
	 * Renders the search search result in the toolbar
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function toolbarSearchResults($result)
	{
		$theme = $this->getTemplate();
		$theme->set('result', $result);

		$html = $theme->output('html/admin/toolbar.searchresult');

		return $html;
	}
}
