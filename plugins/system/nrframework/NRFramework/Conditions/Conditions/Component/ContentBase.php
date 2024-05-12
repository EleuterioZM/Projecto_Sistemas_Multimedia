<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class ContentBase extends ComponentBase
{
    /**
     * Get single page's assosiated categories
     *
     * @param   integer  The Single Page id
	 * 
     * @return  integer
     */
	protected function getSinglePageCategories($id)
	{
		// If the article is not assigned to any menu item, the cat id should be available in the query string. Let's check it.
		if ($requestCatID = $this->app->input->getInt('catid', null))
		{
			return $requestCatID;
		}

		// Apparently, the catid is not available in the Query String. Let's ask Article model.
		$item = $this->getItem($id);

		if (is_object($item) && isset($item->catid)) 
		{
			return $item->catid;
		}
	}
	
	/**
	 *  Load a Joomla article data object.
	 *
	 *  @return  object
	 */
	public function getItem($id = null)
	{
		$id = is_null($id) ? $this->request->id : $id;
        $hash  = md5('contentItem' . $id);
        $cache = $this->factory->getCache(); 

        if ($cache->has($hash))
        {
            return $cache->get($hash);
        }

		// Setup model
		if (defined('nrJ4'))
		{	
			$model = new \Joomla\Component\Content\Site\Model\ArticleModel(['ignore_request' => true]);
			$model->setState('article.id', $id);
			$model->setState('params', $this->app->getParams());
		} else 
		{
			require_once JPATH_SITE . '/components/com_content/models/article.php';
			$model = \JModelLegacy::getInstance('Article', 'ContentModel');
		}

		try
		{
			$item = $model->getItem($id);
			return $cache->set($hash, $item);
		}
		catch (\JException $e)
		{
			return null;
		}
	}
}