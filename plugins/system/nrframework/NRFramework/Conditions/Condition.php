<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Conditions;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

/**
 *  Assignment Class
 */
class Condition
{
	/**
	 *  Application Object
	 *
	 *  @var  object
	 */
	protected $app;

	/**
	 *  Document Object
	 *
	 *  @var  object
	 */
	protected $doc;

	/**
	 *  Date Object
	 *
	 *  @var  object
	 */
	protected $date;

	/**
	 *  Database Object
	 *
	 *  @var  object
	 */
	protected $db;

	/**
	 *  User Object
	 *
	 *  @var  object
	 */
	protected $user;

	/**
	 *  Assignment Selection
	 *
	 *  @var  mixed
	 */
	protected $selection;

	/**
	 *  Assignment Parameters
	 *
	 *  @var  mixed
	 */
	protected $params;

	/**
	 *  Assignment State (Include|Exclude)
	 *
	 *  @var  string
	 */
    public $assignment;
    
    /**
     *  Options
	 * 
	 *  @var  object
     */
    public $options;
    
    /**
     *  Framework factory object
	 * 
	 *  @var  object
     */
    public $factory;

	/**
	 * The default operator that will be used to compare haystack with needle.
	 *
	 * @var string
	 */
	protected $operator;

	/**
	 * Class constructor
	 *
	 * @param	array  $options		The rule options. Expected properties: selection, value, params
	 * @param   object $factory		The framework's factory class.
	 */
	public function __construct($options = null, $factory = null)
	{
        $this->factory = is_null($factory) ? new \NRFramework\Factory() : $factory;

		// Set General Joomla Objects
		$this->db   = $this->factory->getDbo();
		$this->app  = $this->factory->getApplication();
		$this->doc  = $this->factory->getDocument();
		$this->user = $this->factory->getUser();

		$this->options = new Registry($options);

		$this->setParams($this->options->get('params'));
		$this->setOperator($this->options->get('operator', 'includesSome'));

		// For performance reasons we might move this inside the pass() method
		$this->setSelection($this->options->get('selection', ''));
    }

	/**
	 * Set the rule's user selected value
	 *
	 * @param	mixed	$selection
	 * @return 	object
	 */
	public function setSelection($selection)
	{
		$this->selection = $selection;

		if (method_exists($this, 'prepareSelection'))
		{
			$this->selection = $this->prepareSelection();
		}

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function getSelection()
	{
		return $this->selection;
	}

	/**
	 * Set the operator that will be used for the comparison
	 *
	 * @param	string $operator
	 * @return	object
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
		return $this;
	}

	/**
	 * Set the rule's parameters
	 *
	 * @param  array $params
	 */
	public function setParams($params)
	{
		$this->params = new Registry($params);
	}

	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Checks the validitity of two values based on the given operator.
	 * 
	 * Consider converting this method as a Trait.
	 * 
	 * @param   mixed   $value
	 * @param   mixed   $selection
	 * @param   string  $operator
	 * @param   array   $options 	ignoreCase: true,false
	 * 
	 * @return  bool
	 */
	public function passByOperator($value = null, $selection = null, $operator = null, $options = null)
	{
		$value = is_null($value) ? $this->value() : $value;

		if (!is_null($selection))
		{
			$this->setSelection($selection);
		}

		$selection = $this->getSelection();

		$options = new Registry($options);
		$ignoreCase = $options->get('ignoreCase', true);

		if (is_object($value))
		{
			$value = (array) $value;
		}

		if (is_object($selection))
		{
			$selection = (array) $selection;
		}

		if ($ignoreCase)
		{
			if (is_string($value))
			{
				$value = strtolower($value);
			}

			if (is_string($selection))
			{
				$selection = strtolower($selection);
			}

			if (is_array($value))
			{
				$value = array_map('strtolower', $value);
			}

			if (is_array($selection))
			{
				$selection = array_map('strtolower', $selection);
			}
		}

		$operator = (is_null($operator) OR empty($operator)) ? $this->operator : $operator;
		$pass = false;

        switch ($operator)
        {
			case 'exists':
				$pass = !is_null($value);
				break;

			// Determines whether haystack is empty. Accepts: array, string
            case 'empty':
				if (is_array($value))
				{
					$pass = empty($value);
				}

				if (is_string($value))
				{
					$pass = $value == '' || trim($value) == '';
				}
				break;

			// Determine whether haystack is less than needle.
			case 'less_than':
				$pass = $value < $selection;
				break;

			// Determine whether haystack is less than or equal to needle.
			case 'less_than_or_equal_to':
				$pass = $value <= $selection;
				break;

			// Determine whether haystack is greater than needle.
			case 'greater_than':
				$pass = $value > $selection;
				break;

			// Determine whether haystack is greater than or equal to needle.
			case 'greater_than_or_equal_to':
				$pass = $value >= $selection;
				break;

			// Determine whether haystack contains all elements in needle.
			case 'includesAll':
				$pass = count(array_intersect((array) $selection, (array) $value)) == count((array) $selection);
				break;

			// Determine whether haystack contains at least one element from needle.
			case 'includesSome':
				$pass = !empty(array_intersect((array) $value, (array) $selection));
				break;

			// Determine whether haystack contains at least one element from needle. Accepts; string, array.
			case 'includes':
				if (is_string($value) && $value != '' && is_string($selection) && $selection != '')
				{
					if (StringHelper::strpos($value, $selection) !== false)
					{
						$pass = true;
					}
				}

				if (is_array($value) || is_array($selection))
				{
					$pass = $this->passByOperator($value, $selection, 'includesSome', $options);
				}

				break;

			// Determine whether haystack starts with needle. Accepts: string
            case 'starts_with':
                $pass = StringHelper::substr($value, 0, StringHelper::strlen($selection)) === $selection;
				break;
			
			// Determine whether haystack ends with needle. Accepts: string
            case 'ends_with':
                $pass = StringHelper::substr($value, -StringHelper::strlen($selection)) === $selection;
				break;

			// Determine whether haystack equals to needle. Accepts any object.
			default:
				$pass = $value == $selection;
        }

		return $pass;
	}

    /**
     *  Base assignment check
     * 
     *  @return bool
     */
	public function pass()
	{	
		return $this->passByOperator();
	}

	/**
	 *  Returns all parent rows
	 * 
	 *  This method doesn't belong here. Move it to Functions.php.
	 *
	 *  @param   integer  $id      Row primary key
	 *  @param   string   $table   Table name
	 *  @param   string   $parent  Parent column name
	 *  @param   string   $child   Child column name
	 *
	 *  @return  array             Array with IDs
	 */
	public function getParentIds($id = 0, $table = 'menu', $parent = 'parent_id', $child = 'id')
	{
		if (!$id)
		{
			return [];
		}

		$cache = $this->factory->getCache(); 
		$hash  = md5('getParentIds_' . $id . '_' . $table . '_' . $parent . '_' . $child);

		if ($cache->has($hash))
		{
			return $cache->get($hash);
		}

		$parent_ids = array();

		while ($id)
		{
			$query = $this->db->getQuery(true)
				->select('t.' . $parent)
				->from('#__' . $table . ' as t')
				->where('t.' . $child . ' = ' . (int) $id);
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			// Break if no parent is found or parent already found before for some reason
			if (!$id || in_array($id, $parent_ids))
			{
				break;
			}

			$parent_ids[] = $id;
		}

		return $cache->set($hash, $parent_ids);
	}

	/**
	 * A one-line text that describes the current value detected by the rule. Eg: The current time is %s.
	 *
	 * @return string
	 */
	public function getValueHint()
	{
		$value = $this->value();

		// If the rule returns an array, use the 1st one.
		$value = is_array($value) ? $value[0] : $value;

		return \JText::sprintf('NR_DISPLAY_CONDITIONS_HINT_' . strtoupper($this->getName()), ucfirst(strtolower($value)));
	}

	/**
	 * Return the rule name
	 *
	 * @return string
	 */
	protected function getName()
	{
		$classParts = explode('\\', get_called_class());
		return array_pop($classParts);
	}
}