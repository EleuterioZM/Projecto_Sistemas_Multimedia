<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldtextpercent extends JFormField {
 
        protected $type = 'textpercent';
 
        // getLabel() left out
 
        public function getInput() {
                return 	'<div class="input-group">'.
                        '<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
                        . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
                        '<span class="input-group-text">%</span>'.
                        '</div>';
        }
}