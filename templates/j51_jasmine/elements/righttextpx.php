<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
// jimport('joomla.form.formfield');
 
class JFormFieldrighttextpx extends JFormField {
 
        protected $type = 'righttextpx';
 
        // getLabel() left out
 
        protected function getInput() {

            return 	'<div class="input-group ">'.
            		'<span class="input-group-text">Right</span>'.
                        '<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
                        . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
                        '<span class="input-group-text">px</span>'.
                        '</div>';
        }
}