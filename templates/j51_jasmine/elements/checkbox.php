<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
JHTML::_('behavior.mootools');

/**
 * Form Field class for the Joomla Platform.
 * Single check box field.
 * This is a boolean field with null for false and the specified option for true
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.checkbox.html#input.checkbox
 * @see         JFormFieldCheckboxes
 * @since       11.1
 */

class JFormFieldCheckbox extends JFormField
{

	public $type = 'Checkbox';

	
	protected function getInput()
	{
		
		
		$document =& JFactory::getDocument();	

		// determine template filepath 
		$uri = str_replace(DS,"/",str_replace( JPATH_SITE, JURI::base (), dirname(dirname(__FILE__)) ));
		$uri = str_replace("/administrator/", "", $uri);
		$this->template = end( explode( '/', $uri ) );

		// add scripts 
		$document->addScript($uri.'/elements/js/ibutton.js');


		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$checked = ((string) $this->element['value'] == $this->value) ? ' checked="checked"' : '';

		// Initialize JavaScript field attributes.
		$onclick = $this->element['onclick'] ? ' onclick="' . (string) $this->element['onclick'] . '"' : '';

		return '<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars((string) $this->element['value'], ENT_COMPAT, 'UTF-8') . '"' . $class . $checked . $disabled . $onclick . '/>';
	}
}