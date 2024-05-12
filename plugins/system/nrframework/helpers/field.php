<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class NRFormField extends JFormFieldText
{
	public $type = 'Field';

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

	/**
	 *  Prepares string through JText
	 *
	 *  @param   string  $string
	 *
	 *  @return  string
	 */
	public function prepareText($string = '')
	{
		$string = trim($string);

		if ($string == '')
		{
			return '';
		}

		return JText::_($string);
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

	public function getOptionsByList($list, $extras = array(), $levelOffset = 0)
	{
		$options = array();
		foreach ($list as $item)
		{
			$options[] = $this->getOptionByListItem($item, $extras, $levelOffset);
		}

		return $options;
	}

	public function getOptionByListItem($item, $extras = array(), $levelOffset = 0)
	{
		$name = trim($item->name);

		foreach ($extras as $key => $extra)
		{
			if (empty($item->{$extra}))
			{
				continue;
			}

			if ($extra == 'language' && $item->{$extra} == '*')
			{
				continue;
			}

			if (in_array($extra, array('id', 'alias')) && $item->{$extra} == $item->name)
			{
				continue;
			}

			$name .= ' [' . $item->{$extra} . ']';
		}
		
		require_once __DIR__ . '/text.php';

		$name = NRText::prepareSelectItem($name, isset($item->published) ? $item->published : 1);

		$option = JHtml::_('select.option', $item->id, $name, 'value', 'text', 0);

		if (isset($item->level))
		{
			$option->level = $item->level + $levelOffset;
		}

		return $option;
	}
}