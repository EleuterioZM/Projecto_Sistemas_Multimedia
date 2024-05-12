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

class ConvertFormsViewEditorbutton extends JViewLegacy
{
	/**
	 * Items view display method
	 * @return void
	 */
	public function display($tpl = null)
	{

		// Load plugin language file
		NRFramework\Functions::loadLanguage("plg_editors-xtd_convertforms");

		// Get editor name
		$eName = JFactory::getApplication()->input->getCmd('e_name');

		// Get form fields
		$xml  = JPATH_PLUGINS . "/editors-xtd/convertforms/form.xml";
		$form = new JForm("com_convertforms.button", array('control' => 'jform'));
		$form->loadFile($xml, false);

		// Template properties
		$this->eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);
		$this->form  = $form;

		parent::display($tpl);
		return;

	}

}