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
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class ConvertformsControllerForms extends JControllerAdmin
{
	protected $text_prefix = 'COM_CONVERTFORMS_FORM';

    /**
     * Proxy for getModel.
     * @since       2.5
     */
    public function getModel($name = 'form', $prefix = 'ConvertFormsModel', $config = array('ignore_request' => true)) 
    {
        return parent::getModel($name, $prefix, $config);
    }

	/**
	 * Import Method
	 * Set layout to import
	 */
	public function import()
	{
		$app = JFactory::getApplication();

		$file = $app->input->files->get("file");

		if (!empty($file))
		{
			if (isset($file['name']))
			{
				// Get the model.
				$model      = $this->getModel('Forms');
				$model_item = $this->getModel('Form');
				$model->import($model_item);
			}
			else
			{
				$app->enqueueMessage(JText::_('NR_PLEASE_CHOOSE_A_VALID_FILE'), 'error');
				$app->redirect('index.php?option=com_convertforms&view=forms&layout=import');
			}
		}
		else
		{
			$app->redirect('index.php?option=com_convertforms&view=forms&layout=import');
		}
	}

	/**
	 * Export Method
	 * Export the selected items specified by id
	 */
	public function export()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Forms');
		$model->export($ids);
	}

	/**
	 * Copy Method
	 * Copy all items specified by array cid
	 * and set Redirection to the list of items
	 */
	public function duplicate()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Form');

  		foreach ($ids as $id)
        {
            $model->copy($id);
        }

		JFactory::getApplication(JText::sprintf('COM_CONVERTFORMS_FORM_N_ITEMS_COPIED', count($ids)));
		JFactory::getApplication()->redirect('index.php?option=com_convertforms&view=forms');
	}
}