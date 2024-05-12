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

require_once(__DIR__ . '/abstract.php');

class KomentoComHikashop extends KomentoExtension
{
	public $_item;
	public $_map = [
			'id' => 'product_id',
			'title' => 'product_name',
			'hits' => 'viewed',
			'created_by' => 'created_by',
			'catid' => 'category_id',
			'permalink' => 'permalink',
			'state' => 'product_published'
	];

	public function __construct($component)
	{
		$this->addFile(JPATH_ROOT.'/administrator/components/com_hikashop/helpers/helper.php');

		parent::__construct($component);
	}

	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		$this->_item = $article;

		return true;
	}

	/**
	 * Loads a product from Hikashop
	 *
	 * @since	3.0. 6
	 * @access	public
	 */
	public function load($cid)
	{
		static $instances = array();

		if (!isset($instances[$cid])) {

			$filters = array('a.product_id=' . $cid);
			$query = 'SELECT a.*, b.`product_category_id`, b.`category_id`, b.`ordering` FROM ' . hikashop_table('product') . ' AS a LEFT JOIN ' . hikashop_table('product_category') . ' AS b ON a.`product_id` = b.`product_id` WHERE ' . implode(' AND ',$filters) . ' LIMIT 1';

			$db = JFactory::getDBO();
			$db->setQuery($query);

			$this->_item = $db->loadObject();

			// Fixes for product variant. #435
			if ($this->_item->product_parent_id) {
				$query = 'SELECT `product_name`, `product_description` from ' . hikashop_table('product') . ' WHERE `product_id` = (SELECT `product_parent_id` from ' . hikashop_table('product') . ' where `product_id` = ' . $cid . ')';
				$db->setQuery($query);

				$variant = $db->loadObject();

				foreach ($variant as $key => $item) {
					$this->_item->{$key} = $item;
				}
			}

			$this->_item->permalink = hikashop_contentLink('product&task=show&cid=' . $this->_item->product_id.'&name='.$this->_item->product_alias, $this->_item);

			// Since Hikashop does not store the creator, we need to map it ourselves by finding the first super admin on the site
			$admins = KT::getSAUsersIds();
			$this->_item->created_by = $admins[0];

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds( $categories = '' )
	{
		$db		= KT::db();
		$query = '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__aceshop_product_to_category' ) . ' ORDER BY `product_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__aceshop_product_to_category' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `product_id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		return false;
	}

	public function isListingView()
	{
		$state = (($this->input->get('view', '', 'string') === 'category') || ($this->input->get('route', '', 'string') === 'product/category'));

		return $state;
	}

	public function isEntryView()
	{
		$state = (($this->input->get('ctrl', '', 'string') === 'product') && ($this->input->get('task', '', 'string') === 'show'));

		return $state;
	}

	public function onExecute(&$article, $html, $view, $options = array())
	{
		if ($view == 'listing') {
			return $html;
		}

		if ($view == 'entry') {
			return $html;
		}
	}

	public function onRollBack($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		$article = $article->text;

		return true;
	}

	public function getContentPermalink()
	{
		$link = $this->prepareLink($this->_item->permalink);
		
		return $link;
	}
}
