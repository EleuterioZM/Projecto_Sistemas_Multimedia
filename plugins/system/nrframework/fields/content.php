<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/groupfield.php';

class JFormFieldNR_Content extends NRFormGroupField
{
	public $type = 'Content';

	public function getCategories()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(c.id)')
			->from('#__categories AS c')
			->where('c.extension = ' . $this->db->quote('com_content'))
			->where('c.parent_id > 0')
			->where('c.published > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		$options = array();
		if ($this->get('show_ignore'))
		{
			if (in_array('-1', $this->value))
			{
				$this->value = array('-1');
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NR_IGNORE') . ' -', 'value', 'text', 0);
			$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', 1);
		}

		$query->clear('select')
			->select('c.id, c.title as name, c.level, c.published, c.language')
			->order('c.lft');

		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		$options = array_merge($options, $this->getOptionsByList($list, array('language'), -1));

		return $options;
	}

	public function getItems()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(i.id)')
			->from('#__content AS i')
			->where('i.access > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		$query->clear('select')
			->select('i.id, i.title as name, i.language, c.title as cat, i.access as published')
			->join('LEFT', '#__categories AS c ON c.id = i.catid')
			->order('i.title, i.ordering, i.id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('language', 'cat', 'id'));
	}
}
