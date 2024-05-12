<?php

defined('JPATH_BASE') or die();

/**
 * Renders a text element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JFormFieldInfoBox extends JFormField
{
	public $type = 'InfoBox';
	public function getInput(){
		// Output		
		return '
		
		<div class="infoBox">
			'.JText::_($this->value).'
		</div>';
	}

	public function getLabel() {
		return false;
	}
}

?>