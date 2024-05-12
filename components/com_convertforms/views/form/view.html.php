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

defined('_JEXEC') or die;

/**
 * Content categories view.
 *
 * @since  1.5
 */
class ConvertFormsViewForm extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->item   = $this->get('Item');
		$this->params = JFactory::getApplication()->getParams();

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$doc = \JFactory::getDocument();
		$app = \JFactory::getApplication();
		$activeMenuItem = $app->getMenu()->getActive();
		$params = $activeMenuItem->getParams();

		if ($robots_value = $params->get('robots'))
		{
			$robots = $doc->getMetaData('robots');
			$robots = empty($robots) ? $robots_value : $robots . ', ' . $robots_value;
	
			$doc->setMetaData('robots', $robots);
		}

		if ($params->get('menu-meta_keywords'))
		{
			$doc->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('menu-meta_description'))
		{
			$doc->setDescription($params->get('menu-meta_description'));
		}
	}
}
