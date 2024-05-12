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

class ConvertFormsController extends JControllerLegacy
{
    /**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $viewName = $this->input->getCmd('view');
        
        // Access front-end submissions only through a predefined Convert Forms Menu Item.
        if (in_array($viewName, ['submissions', 'submission']))
        {
            $app  = JFactory::getApplication();
            $menu = $app->getMenu()->getActive();
            
            if (!$menu || !$menu->id || $menu->component != 'com_convertforms')
            {
                $app->enqueueMessage(JText::_('COM_CONVERTFORMS_NOT_AUTHORIZED'), 'error');
                return;
            }

            $model = $this->getModel($viewName);
            if (!$model->authorize())
            {
                $app->enqueueMessage(JText::_('COM_CONVERTFORMS_NOT_AUTHORIZED'), 'error');
                return; 
            }
        }

        parent::display($cachable, $urlparams);
        
		return $this;
    }
}