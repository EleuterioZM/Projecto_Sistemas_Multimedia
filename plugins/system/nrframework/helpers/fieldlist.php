<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class NRFormFieldList extends JFormFieldList
{
	/**
	 *  Document object
	 *
	 *  @var  object
	 */
	public $doc;

	/**
	 *  Database object
	 *
	 *  @var  object
	 */
	public $db;

	/**
	 *  Application Object
	 *
	 *  @var  object
	 */
	protected $app;

	/**
	 *  Class constructor
	 */
	function __construct()
	{
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->db = JFactory::getDbo();

		parent::__construct();
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		$label = $this->get("label");
		if (empty($label))
		{
			return "";
		}

		return parent::getLabel();
	}

	protected function showSelect($default = "true")
	{
		return $this->get("showselect", $default) == "true" ? true : false;
	}

	/**
	 *  Method to get field parameters
	 *
	 *  @param   string  $val      Field parameter
	 *  @param   string  $default  The default value
	 *
	 *  @return  string
	 */
	public function get($val, $default = '')
	{
		return (isset($this->element[$val]) && (string) $this->element[$val] != '') ? (string) $this->element[$val] : $default;
	}
}