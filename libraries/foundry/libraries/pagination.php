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
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Pagination\Pagination as JPagination;
use Foundry\Libraries\Themes;
use Foundry\Libraries\Responsive;

class Pagination extends JPagination
{
	public $fd = null;

	public function __construct($fd, $total, $limitstart, $limit, $prefix = '')
	{
		$this->fd = $fd;

		return parent::__construct($total, $limitstart, $limit);
	}

	/**
	 * Getter
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Retrieves the html block for pagination codes
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getPagesLinks()
	{
		return $this->getListFooter(true);
	}


	/**
	 * Retrieves the html block for pagination codes
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getListFooter($isLink = false)
	{
		Scripts::load('shared');

		// Retrieve pages data from Joomla itself.
		$theme = new Themes($this->fd);

		// If there's nothing here, no point displaying the pagination
		if ($this->total == 0) {
			return;
		}

		$responsive = new Responsive();

		$data = $this->getData();

		$theme->set('data', $data);
		$theme->set('isLink', $isLink);
		$theme->set('pagination', $this);
		$theme->set('responsive', $responsive);

		$contents = $theme->output('pagination/footer/default');
		return $contents;
	}

	/**
	 * Allows caller to set additional url parameters
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function setVar($key, $value)
	{
		$this->setAdditionalUrlParam($key, $value);
	}
}
