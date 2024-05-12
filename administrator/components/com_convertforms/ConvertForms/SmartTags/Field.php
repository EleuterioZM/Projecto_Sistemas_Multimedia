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

namespace ConvertForms\SmartTags;

defined('_JEXEC') or die('Restricted access');

use NRFramework\SmartTags\SmartTag;

class Field extends SmartTag
{
	/**
	 * Run only when we have a valid submissions object.
	 *
	 * @return boolean
	 */
	public function canRun()
	{
		return isset($this->data['submission']) ? parent::canRun() : false;
	}

	/**
	 * Fetch field value
	 * 
	 * @param   string  $key
	 * 
	 * @return  string
	 */
	public function fetchValue($key)
	{
		$submission = $this->data['submission'];

		// Separate key parts into an array as it's very likely to have a key in the format: field.label
		$keyParts = explode('.', $key);
		$fieldName = strtolower($keyParts[0]);
		$special_param = isset($keyParts[1]) ? $keyParts[1] : null;
		$fields = $submission->prepared_fields;

		// Check that the field name does exist in the submission data
		if (!array_key_exists($fieldName, $fields))
		{
			return;
		}

		// Make sure $fieldName is strtolower-ed as prepared_fields is an assoc array with lower case keys.
		$field = $submission->prepared_fields[$fieldName];

		// In case of a dropdown and radio fields, make also the label and the calc-value properties available. 
		// This is rather useful when we want to display the dropdown's selected text rather than the dropdown's value.
		if (in_array($special_param, ['label', 'calcvalue', 'calc-value']) && in_array($field->options->get('type'), ['dropdown', 'radio']))
		{
			foreach ($field->class->getOptions() as $choice)
			{
				if ($field->value !== $choice['value'])
				{
					continue;
				}

				// Special case: Keep old syntax: calcvalue
				$special_param = $special_param == 'calcvalue' ? 'calc-value' : $special_param;

				if (isset($choice[$special_param]))
				{
					return $choice[$special_param];
				}
			}
		}

		// We need to return the value of the field
		switch ($special_param)
		{
			case 'raw':
				// If we do care about performance, better call a getValueRaw() method here.
				// The raw value as saved in the database.
				return $field->value_raw;
				break;

			case 'html':
				// The value as transformed to be shown in HTML.
				// If we do care about performance, better call a getValueHtml() method here.
				return $field->value_html;
				break;
			
			default:
				// If we do care about performance, better call a getValue() method here.
				// The value in plain text. Arrays will be shown comma separated.
				return $field->value;
		}
	}
}