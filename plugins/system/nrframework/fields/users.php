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

class JFormFieldNR_Users extends NRFormField
{
	public $type = 'Users';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		if (!is_array($this->value))
		{
			$this->value = explode(',', $this->value);
		}

		$options = $this->getUsers();

		$size     = (int) $this->get('size', 300);

		return HTML::treeselectSimple($options, $this->name, $this->value, $this->id, $size);
	}

	public function getUsers()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(u.id)')
			->from('#__users AS u')
			->where('u.block = 0');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		$query->clear('select')
			->select('u.name, u.username, u.id')
			->order('name');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('username', 'id'));
	}
}
