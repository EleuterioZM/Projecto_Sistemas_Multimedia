<?php
/**
 * @package     ArtelWEB module mod_aw_news
 * http://extension-joomla.com/
 * @license     GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/components/com_content/src/Service/Router.php';
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');
abstract class ModJ51NewsHelper
{
	public static function getList(&$params)
	{
		// Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app       = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 6));
		$model->setState('filter.published', 1);

		// Access filter
		$access     = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));



		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1' :
				$model->setState('filter.featured', 'only');
				break;
			case '0' :
				$model->setState('filter.featured', 'hide');
				break;
			default :
				$model->setState('filter.featured', 'show');
				break;
		}

		// Set ordering
		$ordering = $params->get('ordering', 'a.publish_up');
		$model->setState('list.ordering', $ordering);

		if (trim($ordering) === 'rand()')
		{
			$model->setState('list.ordering', JFactory::getDbo()->getQuery(true)->Rand());
		}
		else
		{
			$direction = $params->get('direction', 1) ? 'DESC' : 'ASC';
			$model->setState('list.direction', $direction);
			$model->setState('list.ordering', $ordering);
		}

		$items = $model->getItems();

		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

		return $items;
	}
}