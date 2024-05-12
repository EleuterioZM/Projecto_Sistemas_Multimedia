<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldtextpx extends JFormField {
 
        protected $type = 'textpx';

        protected $groupClass = 'hello';
 
        public function getInput() {

        	$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';



            return 	'<div class="input-group">'.
					'<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
					. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
					'<span class="input-group-text">px</span>'.
					'</div>';
        }

}
