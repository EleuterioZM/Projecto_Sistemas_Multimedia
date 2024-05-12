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
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Conversion View Class
 */
class ConvertFormsViewConversion extends JViewLegacy
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
        // Access check.
        ConvertForms\Helper::authorise('convertforms.submissions.manage', true);

        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        $this->isnew = (!isset($_REQUEST["id"])) ? true : false;

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() 
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);

        JToolBarHelper::title($isNew ? JText::_('COM_CONVERTFORMS_NEW_CONVERSION') : JText::_('COM_CONVERTFORMS_EDIT_CONVERSION'));
        JToolbarHelper::apply('conversion.apply');
        JToolBarHelper::save('conversion.save');
        JToolBarHelper::cancel('conversion.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}