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
 * Conversions list controller class.
 */
class ConvertFormsControllerConversions extends JControllerAdmin
{
	protected $text_prefix = 'COM_CONVERTFORMS_SUBMISSION';

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
	public function getModel($name = 'Conversion', $prefix = 'ConvertFormsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Export Method
	 * Export the selected items specified by id
	 */
	public function export()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Get the model.
		$model = $this->getModel('Conversions');
		$model->export($ids);
	}
}