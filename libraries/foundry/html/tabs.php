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

class Tabs extends Base
{
	/**
	 * Generates tabs for the back-end
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function render($tabs, $style = 'line', $position = 'horizontal', $options = [])
	{
		Scripts::load('shared');

		$tabHeaderClass = \FH::normalize($options, 'tabHeaderClass', '');
		$tabHeaderItemClass = \FH::normalize($options, 'tabHeaderItemClass', '');
		$tabContentClass = \FH::normalize($options, 'tabContentClass', '');
		$tabContentItemClass = \FH::normalize($options, 'tabContentItemClass', '');

		// Style supports pill and line
		if (!in_array($style, ['line', 'pill'])) {
			$style = 'line';
		}

		// Position supports horizontal and vertical
		if (!in_array($position, ['horizontal', 'vertical'])) {
			$position = 'horizontal';
		}

		$theme = $this->getTemplate();
		$theme->set('tabs', $tabs);
		$theme->set('style', $style);
		$theme->set('position', $position);
		$theme->set('tabHeaderClass', $tabHeaderClass);
		$theme->set('tabHeaderItemClass', $tabHeaderItemClass);
		$theme->set('tabContentClass', $tabContentClass);
		$theme->set('tabContentItemClass', $tabContentItemClass);

		$html = $theme->output('html/tabs/render');

		return $html;
	}

	/**
	 * Generates tabs for the back-end
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function item($id, $label, $contentsCallback, $active = false)
	{
		Scripts::load('shared');

		$tab = (object) [
			'id' => $id,
			'label' => $label,
			'active' => $active
		];

		ob_start();
		$contentsCallback();

		$tab->contents = ob_get_contents();
		ob_end_clean();

		return $tab;
	}
}
