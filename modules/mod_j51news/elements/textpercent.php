<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
  
class JFormFieldtextpercent extends FormField {
 
	protected $type = 'textpercent';

	public function getInput() {

		return '<div class="input-group">'.
		'<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
		. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
		'<span class="input-group-text">%</span>'.
		'</div>';
	}
}
