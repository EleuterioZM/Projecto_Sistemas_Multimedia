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

/**
 * Campaigns list controller class.
 */
class ConvertFormsControllerCampaigns extends JControllerAdmin
{
	protected $text_prefix = 'COM_CONVERTFORMS_CAMPAIGN';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Campaign', $prefix = 'ConvertFormsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 *  Copy items specified by array cid and set Redirection to the list of items
	 *
	 *  @return  void
	 */
	function duplicate()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Campaign');

  		foreach ($ids as $id)
        {
            $model->copy($id);
		}
		
		JFactory::getApplication(JText::sprintf('COM_CONVERTFORMS_CAMPAIGN_N_ITEMS_COPIED', count($ids)));
		JFactory::getApplication()->redirect('index.php?option=com_convertforms&view=campaigns');
	}

	/**
	 *  Export campaign submissions specified by campaign ids
	 *
	 *  @return  void
	 */
	function export()
	{
		$ids = JFactory::getApplication()->input->get('cid', null, 'INT');

		// Get the Conversions model
		$model = $this->getModel('Conversions');
		$model->export(null, $ids);
	}
}