<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/conversion.php';

class ConvertFormsModelSubmission extends ConvertFormsModelConversion
{
	/**
	 * Application Object
	 *
	 * @var object
	 */
	private $app;

	/**
	 * Active Menu Item
	 *
	 * @var object
	 */
	public $menu;

	/**
	 * Submissions filter options and params;
	 *
	 * @var object
	 */
	public $options;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->app  = JFactory::getApplication();
		$this->menu = $this->app->getMenu()->getActive();
		$this->options = isset($config['options']) ? new Registry($config['options']) : $this->menu->getParams();

		parent::__construct();
	}

	/**
	 * Get Submission Data
	 *
	 * @param  object $pk	The submission's primary key
	 *
	 * @return object
	 */
	public function getItem($pk = null, $prepare = true)
	{
		$item = parent::getItem($pk, $prepare);

		if (!$item->id)
		{
			return;
		}

		if (!isset($item->prepared_fields))
		{
			return;
		}

		$item->fields = $item->prepared_fields;

		$hide_empty_values = (bool) $this->options->get('hide_empty_values', false);

		foreach ($item->fields as $key => $field)
		{
			if ($hide_empty_values && (is_null($field->value_html) || $field->value_html == ''))
			{
				unset($item->fields[$key]);
			}
		}

		return $item;
	}

	public function authorize()
	{
		// Verify we are browsing a submission that belongs the form selected in the menu item settings
		if (!$this->submissionBelongsToSelectedForm())
		{
			return;
		}

		$filter_submitters = $this->options->get('filter_user', 'current');
		$view_own_only = $filter_submitters == 'current' ?  true : (bool) $this->options->get('view_own_only', true);

		// User is allowed to see all submissions
		if (!$view_own_only)
		{
			return true;
		}

		// Deny access, if user is not logged-in
		if (!$user_id = JFactory::getUser()->id)
		{
			return;
		}

		$submission_user_id = (int) $this->getItem()->user_id;

		if ($filter_submitters == 'current' && $user_id != $submission_user_id)
		{
			return;
		}

		if ($filter_submitters == 'users')
		{
			$filter_users = explode(',', $this->options->get('users'));

			if (!in_array($user_id, $filter_users))
			{
				return;
			}
		}

		return true;
	}

	/**
	 * Verify we are browsing a submission that belongs the form selected in the menu item settings
	 *
	 * @return mixed  Null on failure, string on success
	 */
	private function submissionBelongsToSelectedForm()
	{
		$db = JFactory::getDbo();
         
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
            ->from($db->quoteName('#__convertforms_conversions'))
            ->where($db->quoteName('id') . ' = '. (int) $this->app->input->get('id'))
			->where($db->quoteName('form_id') . ' = ' . (int) $this->options->get('form_id'));
		
		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getMenu()
	{
		return $this->menu;
	}
}
