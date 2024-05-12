<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\SmartTags;

defined('_JEXEC') or die('Restricted access');

use NRFramework\SmartTags\SmartTag;

/**
 * Syntax:						{all_fields}
 * Hide labels:					{all_fields --hideLabels=true}
 * Exclude empty values:		{all_fields --excludeEmpty=true}
 * Exclude certain fields:		{all_fields --excludeFields=text1,dropdown2}
 * Exclude certain field types: {all_fields --excludeTypes=text,hidden}
 */
class All_fields extends SmartTag
{
	/**
	 * Get All Fields value
	 * 
	 * @return  string
	 */
	public function getAll_fields()
	{
		if (!$fields = $this->filteredFields())
		{
			return;
		}

		$all_fields = '';

		$hideLabels = $this->parsedOptions->get('hidelabels');

		foreach ($fields as $field)
		{
			if ($hideLabels)
			{
				$all_fields .= '<div>' . $field->value_html . '</div>';
				continue;
			}

			$all_fields .= '<div><strong>' . $field->class->getLabel() . '</strong>: ' . $field->value_html . '</div>';
		}

		return $all_fields;
	}

	/**
	 * Filter submitted data with given filter options
	 *
	 * @return mixed	Null when no submission is found, array otherwise
	 */
	private function filteredFields()
	{
		$submission = isset($this->data['submission']) ? $this->data['submission'] : '';

		if (!$submission)
		{
			return '';
		}

		$excludeEmpty  = $this->parsedOptions->get('excludeempty', false);
		$excludeTypes  = explode(',', $this->parsedOptions->get('excludetypes', ''));
		$excludeFields = explode(',', $this->parsedOptions->get('excludefields', ''));

		return array_filter($submission->prepared_fields, function($field) use ($excludeTypes, $excludeFields, $excludeEmpty)
		{
			if ($excludeEmpty && trim($field->value) == '')
			{
				return;
			}

			if ($excludeTypes && in_array($field->options->get('type'), $excludeTypes))
			{
				return;
			}

			if ($excludeFields && in_array($field->options->get('name'), $excludeFields))
			{
				return;
			}

			return true;
		});
	}
}