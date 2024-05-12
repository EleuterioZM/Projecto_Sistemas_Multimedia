<?php

defined('JPATH_BASE') or die();

/**
 * Renders a text element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JFormFieldTitleBox extends JFormField
{
	public $type = 'TitleBox';
	public function getInput(){
		// Output		
		return '
		
		<div class="titleBox">
			'.JText::_($this->value).'
		</div>';
	}

	public function getLabel() {
		return false;
	}
}

?>