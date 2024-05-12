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
 * Item View
 */
class ConvertFormsViewForm extends JViewLegacy
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
		// Access check.
        ConvertForms\Helper::authorise('convertforms.forms.manage', true);

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Assign the Data
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->isnew = (!isset($_REQUEST["id"])) ? true : false;
        $this->tabs  = $this->get('Tabs');
        $this->name  = $this->item->name ?: JText::_('COM_CONVERTFORMS_UNTITLED_BOX');

        \JPluginHelper::importPlugin('convertformstools');
		\JFactory::getApplication()->triggerEvent('onConvertFormsBackendEditorDisplay');

        $title = JText::_('COM_CONVERTFORMS') . ' - ' . ($this->isnew ? JText::_("COM_CONVERTFORMS_UNTITLED_BOX") : $this->name);

        JFactory::getDocument()->setTitle($title);
        JToolbarHelper::title($title);

        // Display the template
        parent::display($tpl);
    }
}