<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

use \NRFramework\HTML;

require_once dirname(__DIR__) . '/helpers/field.php';


class JFormFieldNRMenuItems extends NRFormField
{
	/**
	 * Output the HTML for the field
	 * Example of usage: <field name="field_name" type="nrmenuitems" label="NR_SELECTION" />
	 */
	protected function getInput()
	{
		$size    = $this->get('size', 300);
		$options = $this->getMenuItems();

		return HTML::treeselect($options, $this->name, $this->value, $this->id, $size);
	}

	/**
	 * Get a list of menu links for one or all menus.
	 * Logic from administrator\components\com_menus\helpers\menus.php@getMenuLinks()
	 */
	public function getMenuItems()
	{
		NRFramework\Functions::loadLanguage('com_menus', JPATH_ADMINISTRATOR);
		$db = $this->db;

		// Prevent the "The SELECT would examine more than MAX_JOIN_SIZE rows; " MySQL error
		// on websites with a big number of menu items in the db.
		$db->setQuery('SET SQL_BIG_SELECTS = 1')->execute();

		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, a.alias, a.level, a.menutype, a.type, a.template_style_id, a.checked_out, a.language')
			->from('#__menu AS a')
			->join('LEFT', $db->quoteName('#__menu') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->where('a.published != -2')
			->group('a.id, a.alias, a.title, a.level, a.menutype, a.type, a.template_style_id, a.checked_out, a.lft, a.language')
			->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$links = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			return false;
		}

		// Group the items by menutype.
		$query->clear()
			->select('*')
			->from('#__menu_types')
			->where('menutype <> ' . $db->quote(''))
			->order('title, menutype');
		$db->setQuery($query);

		try
		{
			$menuTypes = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			return false;
		}

		// Create a reverse lookup and aggregate the links.
		$rlu = array();
		foreach ($menuTypes as &$type)
		{
			$type->value      = 'type.' . $type->menutype;
			$type->text       = $type->title;
			$type->level      = 0;
			$type->class      = 'hidechildren';
			$type->labelclass = 'nav-header';

			$rlu[$type->menutype] = &$type;
			$type->links          = array();
		}

		foreach ($links as &$link)
		{
			if (isset($rlu[$link->menutype]))
			{
				if (preg_replace('#[^a-z0-9]#', '', strtolower($link->text)) !== preg_replace('#[^a-z0-9]#', '', $link->alias))
				{
					$link->text .= ' <small>[' . $link->alias . ']</small>';
				}

				if ($link->language && $link->language != '*')
				{
					$link->text .= ' <small>(' . $link->language . ')</small>';
				}

				if ($link->type == 'alias')
				{
					$link->text .= ' <small>(' . JText::_('COM_MENUS_TYPE_ALIAS') . ')</small>';
					$link->disable = 1;
				}

				$rlu[$link->menutype]->links[] = &$link;

				unset($link->menutype);
			}
		}

		return $menuTypes;
	}

}
