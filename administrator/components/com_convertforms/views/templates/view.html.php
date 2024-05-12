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
 * Templates View
 */
class ConvertFormsViewTemplates extends JViewLegacy
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    function display($tpl = null) 
    {
        $this->config    = JComponentHelper::getParams('com_convertforms');
        $this->templates = $this->getTemplates();

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     *  Get list of all available templates
     *
     *  @return  array
     */
    function getTemplates()
    {
        $templatesPath = JPATH_ROOT . "/media/com_convertforms/templates/";
        $xmlFile  = $templatesPath . "templates.xml";

        if (!JFile::exists($xmlFile))
        {
            return;
        }

        if (!$templateGroups = simplexml_load_file($xmlFile))
        {
            return;
        }

        $templates = array();

        foreach ($templateGroups as $templateGroup)
        {
            $templateGroupName = (string) $templateGroup["name"];

            foreach ($templateGroup as $template)
            {
                $templateName = (string) $template["name"];

                // Check if template thumb file exists
                if (!JFile::exists($templatesPath . $templateName . ".jpg"))
                {
                    continue;
                }

                $templateInfo = array(
                    "name"  => $templateName,
                    "label" => (string) $template["label"],
                    "thumb" => JURI::root() . 'media/com_convertforms/templates/' . $templateName . '.jpg',
                    "link"  => JURI::base() . "index.php?option=com_convertforms&view=form&layout=edit&template=" . $templateName
                );

                // Check if template thumb file exists
                if (!JFile::exists($templatesPath . $templateName . ".cnvf"))
                {
                    unset($templateInfo["link"]);
                }

                $templates[$templateGroupName][] = $templateInfo;
            }
        }

        return $templates;
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        JToolBarHelper::title(JText::_('COM_CONVERTFORMS') . ": " . JText::_('COM_CONVERTFORMS_TEMPLATES'));
    }
}