<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

use NRFramework\Extension;

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldNRConditions extends JFormFieldGroupedList
{
	/**
     * List of available conditions
     *
     * @var array
     */
    public static $conditions = [
		'NR_DATETIME' => [
			'Date\Date' => 'NR_DATE',
			'Date\Day' => 'NR_WEEKDAY',
			'Date\Month' => 'NR_MONTH',
			'Date\Time' => 'NR_TIME'
		],
		'Joomla' => [
			'com_content#Component\ContentArticle' => 'NR_CONTENT_ARTICLE',
			'com_content#Component\ContentCategory' => 'NR_CONTENT_CATEGORY',
			'com_content#Component\ContentView' => 'NR_CONTENT_VIEW',
			'Joomla\UserID' => 'NR_ASSIGN_USER_ID',
			'Joomla\UserGroup' => 'NR_USERGROUP',
			'Joomla\AccessLevel' => 'NR_USERACCESSLEVEL',
			'Joomla\Menu' => 'NR_MENU',
			'Joomla\Component' => 'NR_ASSIGN_COMPONENTS',
			'Joomla\Language' => 'NR_ASSIGN_LANGS'
		],
		'NR_TECHNOLOGY' => [
			'Device' => 'NR_ASSIGN_DEVICES',
			'Browser' => 'NR_ASSIGN_BROWSERS',
			'OS' => 'NR_ASSIGN_OS'
		],
		'NR_GEOLOCATION' => [
			'Geo\City' => 'NR_CITY',
			'Geo\Country' => 'NR_ASSIGN_COUNTRIES',
			'Geo\Region' => 'NR_REGION',
			'Geo\Continent' => 'NR_CONTINENT'
		],
		'NR_INTEGRATIONS' => [
			'com_rstbox#EngageBox'=> 'NR_VIEWED_ANOTHER_BOX',
			'com_convertforms#ConvertForms'=> 'NR_CONVERT_FORMS_CAMPAIGN',
			'com_k2#Component\K2Item' => 'NR_K2_ITEM',
			'com_k2#Component\K2Category' => 'NR_K2_CATEGORY',
			'com_k2#Component\K2Tag' => 'NR_K2_TAG',
			'com_k2#Component\K2Pagetype' => 'NR_K2_PAGE_TYPE',
			'com_acymailing#AcyMailing|com_acym#AcyMailing' => 'NR_ACYMAILING_LIST',
			'com_akeebasubs#AkeebaSubs' => 'NR_AKEEBASUBS_LEVEL'
		],
		'NR_ADVANCED' => [
			'URL' => 'NR_URL',
			'Referrer' => 'NR_ASSIGN_REFERRER',
			'IP' => 'NR_IPADDRESS',
			'Pageviews' => 'NR_ASSIGN_PAGEVIEWS_VIEWS',
			'Cookie' => 'NR_COOKIE',
			'PHP' => 'NR_ASSIGN_PHP',
			'TimeOnSite' => 'NR_ASSIGN_TIMEONSITE'
		]
	];

	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		$include_rules = empty($this->element['include_rules']) ? [] : explode(',', $this->element['include_rules']);
		$exclude_rules = empty($this->element['exclude_rules']) ? [] : explode(',', $this->element['exclude_rules']);

		$groups[''][] = JHtml::_('select.option', null, JText::_('NR_CB_SELECT_CONDITION'));

		foreach (self::$conditions as $conditionGroup => $conditions)
		{
			foreach ($conditions as $conditionName => $condition)
			{
				$skip_condition = false;

				/**
				 * Checks conditions that have multiple components as dependency.
				 * Check for multiple given components for a particular condition, i.e. acymailing can be loaded via com_acymailing or com_acym
				 */
				$multiple_components = explode('|', $conditionName);
				if (count($multiple_components) >= 2)
				{
					foreach ($multiple_components as $component)
					{
						$skip_condition = false;

						if (!$conditionName = $this->getConditionName($component))
						{
							$skip_condition = true;
							continue;
						}
					}
				}
				
				// If the condition must be skipped, skip it
				if ($skip_condition)
				{
					continue;
				}

				// Checks for a single condition whether its component exists and can be used.
				if (!$conditionName = $this->getConditionName($conditionName))
				{
					continue;
				}

				// If its excluded, skip it
				if (!empty($exclude_rules) && in_array($conditionName, $exclude_rules))
				{
					continue;
				}

				// If its not included, skip it
				if (!empty($include_rules) && !in_array($conditionName, $include_rules))
				{
					continue;
				}

				// Add condition to the group
				$groups[JText::_($conditionGroup)][] = JHtml::_('select.option', $conditionName, JText::_($condition), 'value', 'text');
			}
		}

		// Merge any additional groups in the XML definition.
		return array_merge(parent::getGroups(), $groups);
	}

	/**
	 * Returns the parsed condition name.
	 * 
	 * i.e. $condition: com_k2#Component\K2Item
	 * will return: Component\K2Item
	 * 
	 * @param   string  $condition
	 * 
	 * @return  mixed
	 */
	private function getConditionName($condition)
	{
		$conditionNameParts = explode('#', $condition);

		if (count($conditionNameParts) >= 2 && !Extension::isEnabled($conditionNameParts[0]))
		{
			return false;
		}
		
		return isset($conditionNameParts[1]) ? $conditionNameParts[1] : $conditionNameParts[0];
	}
}