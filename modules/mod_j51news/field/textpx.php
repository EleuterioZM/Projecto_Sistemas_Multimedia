<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldtextpx extends JFormField
{
	protected $type = 'textpx';

	public function getInput()
	{
		return 	'<div class="input-append">'.
				'<input class="input-medium" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
				'<span class="add-on">px</span>'.
				'</div>';
	}
}
