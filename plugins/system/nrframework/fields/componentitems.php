<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

require_once __DIR__ . '/ajaxify.php';

/*
 * Creates an AJAX-based dropdown
 * https://select2.org/
 */
class JFormFieldComponentItems extends JFormFieldAjaxify
{
    /**
     * Single items table name
     *
     * @var string
     */
    protected $table = 'content';

    /**
     * Primary key column of the single items table
     *
     * @var string
     */
    protected $column_id = 'id';

    /**
     * The title column of the single items table
     *
     * @var string
     */
    protected $column_title = 'title';

    /**
     * The state column of the single items table
     *
     * @var string
     */
    protected $column_state = 'state';

    /**
     * Pass extra where SQL statement
     *
     * @var string
     */
    protected $where;

    /**
     * The Joomla database object
     *
     * @var object
     */
    protected $db;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
		if ($return = parent::setup($element, $value, $group))
		{
            $this->init();
        }
        
		return $return;
    }

    public function init()
    {
        $this->table = isset($this->element['table']) ? (string) $this->element['table'] : $this->table;

        $this->column_id = isset($this->element['column_id']) ? (string) $this->element['column_id'] : $this->column_id;
        $this->column_id = $this->prefix($this->column_id);

        $this->column_title = isset($this->element['column_title']) ? (string) $this->element['column_title'] : $this->column_title;
        $this->column_title = $this->prefix($this->column_title);

        $this->column_state = isset($this->element['column_state']) ? (string) $this->element['column_state'] : $this->column_state;
        $this->column_state = $this->prefix($this->column_state);

        $this->where = isset($this->element['where']) ? (string) $this->element['where'] : null;
        $this->join = isset($this->element['join']) ? (string) $this->element['join'] : null;

        if (!isset($this->element['placeholder']))
        {
            $this->placeholder = (string) $this->element['description'];
        }

        // Initialize database Object
        $this->db = JFactory::getDbo();
    }

    private function prefix($string)
    {
        if (strpos($string, '.') === false)
        {
            $string = 'i.' . $string;
        }

        return $string;
    }

    protected function getTemplateResult()
    {
        return '<span class="row-text">\' + state.text + \'</span><span style="float:right; opacity:.7">\' + state.id + \'</span>';
    }

    protected function getItemsQuery()
    {
        $db = $this->db;

        $query = $this->getQuery()
            ->order($db->quoteName($this->column_id) . ' DESC');

        if ($this->limit > 0)
        {
            // Joomla uses offset
            $page = $this->page - 1;

            $query->setLimit($this->limit, $page * $this->limit);
        }

        return $query;
    }

    protected function getItems()
    {
        $db = $this->db;

        $db->setQuery($this->getItemsQuery());
        
		return $db->loadObjectList();
    }

    protected function getItemsTotal()
    {
        $db = $this->db;

        $query = $this->getQuery()
            ->clear('select')
            ->select('count(*)');
        $db->setQuery($query);
        
		return (int) $db->loadResult();
    }

    protected function getQuery()
    {
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select([
                $db->quoteName($this->column_id, 'id'),
                $db->quoteName($this->column_title, 'text'),
                $db->quoteName($this->column_state, 'state')
            ])
            ->from($db->quoteName('#__' . $this->table, 'i'));

        if (!empty($this->search_term))
        {
            $query->where($db->quoteName($this->column_title) . ' LIKE ' . $db->quote('%' . $this->search_term . '%'));
        }

        if ($this->join)
        {
            $query->join('INNER', $this->join);
        }

        if ($this->where)
        {
            $query->where($this->where);
        }

        return $query;
    }

    protected function validateOptions($options)
    {
        $db = $this->db;

        $query = $this->getQuery()
            ->where($db->quoteName($this->column_id) . ' IN (' . implode(',', $options) . ')');
       
        $db->setQuery($query);

        return $db->loadAssocList('id', 'text');
    }
}