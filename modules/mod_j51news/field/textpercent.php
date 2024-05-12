<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldtextpercent extends JFormField {
 
        protected $type = 'textpercent';
 
        // getLabel() left out
 
        public function getInput() {
                return 	'<div class="input-append">'.
						'<input class="input-medium" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
						. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
						'<span class="add-on">%</span>'.
						'</div>';
        }
}