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

class ConvertFormsAcyMailingHelper
{
	/**
	 * @deprecated Use subscribe()
	 */
	public static function subscribe_v6($email, $params, $lists, $doubleOptin = true)
	{
		self::subscribe($email, $params, $lists, $doubleOptin);
	}

    /**
	 * Subscribe method for AcyMailing v6
	 *
	 * @param  array $lists
	 *
	 * @return void
	 */
	public static function subscribe($email, $params, $lists, $doubleOptin = true)
	{
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php'))
        {
			throw new Exception(JText::sprintf('PLG_CONVERTFORMS_ACYMAILING_HELPER_CLASS_ERROR', 6));
        }

		// Create user object
		$user = new stdClass();
		$user->email 	 = $email;
		$user->confirmed = $doubleOptin ? 0 : 1;

		$user_fields = array_change_key_case($params);

		$user->name = isset($user_fields['name']) ? $user_fields['name'] : '';

		// Load User Class
		$acym = acym_get('class.user');

		// Check if exists
		$existing_user = $acym->getOneByEmail($email);

		if ($existing_user)
		{
			$user->id = $existing_user->id;
		} else
		{
			// Save user to database only if it's a new user.
			if (!$user->id = $acym->save($user))
			{
				throw new Exception(JText::_('PLG_CONVERTFORMS_ACYMAILING_CANT_CREATE_USER'));
			}
		}

		// Save Custom Fields
		$fieldClass = acym_get('class.field');

		// getAllfields was removed in 7.7.4 and we must use getAll moving forward.
		$acy_fields_method = method_exists($fieldClass, 'getAllfields') ? 'getAllfields' : 'getAll';
		$acy_fields = $fieldClass->$acy_fields_method();
		
		unset($user_fields['name']); // Name is already used during user creation.

		$fields_to_store = [];

		foreach ($user_fields as $paramKey => $paramValue)
		{
			// Check if paramKey it's a custom field
			$field_found = array_filter($acy_fields, function($field) use($paramKey) {
				return (strtolower($field->name) == $paramKey || $field->id == $paramKey);
			});

			if ($field_found)
			{
				// Get the 1st occurence
				$field = array_shift($field_found);

				// AcyMailing 6 needs field's ID to recognize a field.
				$fields_to_store[$field->id] = $paramValue;

				// $paramValue output: array(1) { [0]=> string(2) "gr" }
				// AcyMailing will get the key as the value instead of "gr"
				// We combine to remove the keys in order to keep the values
				if (is_array($paramValue))
				{
					$fields_to_store[$field->id] = array_combine($fields_to_store[$field->id], $fields_to_store[$field->id]);
				}
			}
		}

		if ($fields_to_store)
		{
			$fieldClass->store($user->id, $fields_to_store);
		}

		// Subscribe user to AcyMailing lists
		return $acym->subscribe($user->id, $lists);
    }
    
    /**
	 * Subscribe method for AcyMailing v5
	 *
	 * @param  array $lists
	 *
	 * @return void
	 */
	public static function subscribe_v5($email, $params, $lists, $doubleOptin = true)
	{
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php'))
        {
			throw new Exception(JText::sprintf('PLG_CONVERTFORMS_ACYMAILING_HELPER_CLASS_ERROR', 5));
		}

		// Create user object
		$user = new stdClass();
		$user->email 	 = $email;
		$user->confirmed = $doubleOptin ? false : true;

		// Get Custrom Fields
    	$db = JFactory::getDbo();

        $customFields = $db->setQuery(
            $db->getQuery(true)
                ->select($db->quoteName('namekey'))
                ->from($db->quoteName('#__acymailing_fields'))
        )->loadColumn();

		if (is_array($customFields) && count($customFields))
		{
			foreach ($params as $key => $param)
			{
				if (in_array($key, $customFields))
				{
					$user->$key = $param;
				}
			}
		}
		
		$acymailing = acymailing_get('class.subscriber');
		$userid = $acymailing->subid($email);

		// AcyMailing sends account confirmation e-mails even if the user exists, so we need
		// to run save() method only if the user actually is new.
		if (is_null($userid)) 
		{
			// Save user to database
			if (!$userid = $acymailing->save($user))
			{
				throw new Exception(JText::_('PLG_CONVERTFORMS_ACYMAILING_CANT_CREATE_USER'));
			}
		}

		// Subscribe user to AcyMailing lists
		$lead = [];
		foreach($lists as $listId)
		{
			$lead[$listId] = ['status' => 1];
		}

		return $acymailing->saveSubscription($userid, $lead);
	}
}