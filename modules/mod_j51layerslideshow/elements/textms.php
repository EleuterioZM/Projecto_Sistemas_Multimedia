<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
  
class JFormFieldtextms extends FormField {
 
	protected $type = 'textms';

	public function getInput() {

		return '<div class="input-group">'.
		'<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
		. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
		'<span class="input-group-text">ms</span>'.
		'</div>';
	}
}
