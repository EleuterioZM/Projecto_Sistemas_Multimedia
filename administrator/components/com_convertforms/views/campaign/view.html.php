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

/**
 * Campaign View Class
 */
class ConvertFormsViewCampaign extends JViewLegacy
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
		// Access check.
		ConvertForms\Helper::authorise('convertforms.campaigns.manage', true);

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

        JToolBarHelper::title($isNew ? JText::_('COM_CONVERTFORMS_NEW_CAMPAIGN') : JText::_('COM_CONVERTFORMS_EDIT_CAMPAIGN') . ": " . $this->item->name . " - ". $this->item->id);

        if (defined('nrJ4'))
        {
            $toolbar = Toolbar::getInstance();

            $saveGroup = $toolbar->dropdownButton('save-group');
            
			$saveGroup->configure(
				function (Toolbar $childBar)
				{
                    $childBar->apply('campaign.apply');
                    $childBar->save('campaign.save');
                    $childBar->save2new('campaign.save2new');
					$childBar->save2copy('campaign.save2copy');
				}
            );
            
			$toolbar->cancel('campaign.cancel', 'JTOOLBAR_CLOSE');

            return;
        }

        JToolbarHelper::apply('campaign.apply');
        JToolBarHelper::save('campaign.save');
        JToolbarHelper::save2new('campaign.save2new');
        JToolBarHelper::cancel('campaign.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}