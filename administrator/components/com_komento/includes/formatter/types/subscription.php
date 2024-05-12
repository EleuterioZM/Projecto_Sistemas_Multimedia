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

class KomentoFormatterSubscription
{
	protected $items = null;
	protected $cache = null;
	protected $options = null;

	public function __construct(&$items, $options = array(), $cache = true)
	{
		$this->items = $items;
		$this->cache = $cache;
		$this->options = $options;
	}

	/**
	 * Default method to format comments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function execute()
	{
		$result = [];
		$type = FH::normalize($this->options, 'type', 'inline');

		$isSingleObject = false;

		if (!is_array($this->items)) {
			$isSingleObject = true;

			// convert into array
			$this->items = [$this->items];
		}

		foreach ($this->items as $item) {

			$sub = KT::table('Subscription');
			$sub->bind($item);

			KT::setCurrentComponent($sub->component);

			// set extension object
			$sub->extension = KT::loadApplication($sub->component)->load($sub->cid);

			if ($sub->extension === false) {
				$sub->extension = KT::getErrorApplication($sub->component, $sub->cid);
			}

			// get permalink
			$sub->pagelink = $sub->extension->getContentPermalink();

			// set content title
			$sub->contenttitle = $sub->extension->getContentTitle();

			// set component title
			$sub->componenttitle = $sub->extension->getComponentName();

			$result[] = $sub;
		}

		return ($isSingleObject) ? $result[0] : $result;
	}

}
