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

use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Campaigns View
 */
class ConvertFormsViewCampaigns extends JViewLegacy
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    public function display($tpl = null) 
    {
		// Access check.
		ConvertForms\Helper::authorise('convertforms.campaigns.manage', true);

        $this->items         = $this->get('Items');
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->config        = JComponentHelper::getParams('com_convertforms');

        ConvertForms\Helper::addSubmenu('campaigns');
        $this->sidebar = JHtmlSidebar::render();

        // Trigger all ConvertForms plugins
        JPluginHelper::importPlugin('convertforms');
        JFactory::getApplication()->triggerEvent('onConvertFormsServiceName');

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
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        $canDo = ConvertForms\Helper::getActions();
        $state = $this->get('State');
        $viewLayout = JFactory::getApplication()->input->get('layout', 'default');

        $title = JText::_('COM_CONVERTFORMS') . ": " . JText::_('COM_CONVERTFORMS_CAMPAIGNS');
        JFactory::getDocument()->setTitle($title);
        JToolbarHelper::title($title);
        
        // Joomla J4
        if (defined('nrJ4'))
        {
            $toolbar = Toolbar::getInstance('toolbar');

            if ($canDo->get('core.create'))
            {
                $toolbar->addNew('campaign.add');
            }

            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fas fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            
            if ($canDo->get('core.edit.state'))
            {
                $childBar->publish('campaigns.publish')->listCheck(true);
                $childBar->unpublish('campaigns.unpublish')->listCheck(true);
                $childBar->standardButton('copy')->text('JTOOLBAR_DUPLICATE')->task('campaigns.duplicate')->listCheck(true);
                $childBar->standardButton('export')->text('COM_CONVERTFORMS_LEADS_EXPORT')->task('campaigns.export')->icon('icon-download')->listCheck(true);
                $childBar->trash('campaigns.trash')->listCheck(true);
            }

            if ($this->state->get('filter.state') == -2)
            {
                $toolbar->delete('campaigns.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            if ($canDo->get('core.admin'))
            {
                $toolbar->preferences('com_convertforms');
            }

            $toolbar->help('JHELP', false, 'http://www.tassos.gr/joomla-extensions/convert-forms/docs');

            return;
        }

        if ($canDo->get('core.create'))
        {
            JToolbarHelper::addNew('campaign.add');
        }
        
        if ($canDo->get('core.edit'))
        {
            JToolbarHelper::editList('campaign.edit');
        }

        if ($canDo->get('core.create'))
        {
            JToolbarHelper::custom('campaigns.duplicate', 'copy', 'copy', 'JTOOLBAR_DUPLICATE', true);
        }

        if ($canDo->get('core.edit.state') && $state->get('filter.state') != 2)
        {
            JToolbarHelper::publish('campaigns.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('campaigns.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($canDo->get('core.delete') && $state->get('filter.state') == -2)
        {
            JToolbarHelper::deleteList('', 'campaigns.delete', 'JTOOLBAR_EMPTY_TRASH');
        }
        else if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::trash('campaigns.trash');
        }

        if ($canDo->get('core.create'))
        {
            JToolbarHelper::custom('campaigns.export', 'box-add', 'box-add', 'COM_CONVERTFORMS_LEADS_EXPORT');
        }

        if ($canDo->get('core.admin'))
        {
            JToolbarHelper::preferences('com_convertforms');
        }

        JToolbarHelper::help("Help", false, "http://www.tassos.gr/joomla-extensions/convert-forms/docs");
    }
}