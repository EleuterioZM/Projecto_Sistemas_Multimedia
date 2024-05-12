<?php
/**
* J51_Progress
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldtextsec extends JFormField
{
	protected $type = 'textsec';

	public function getInput()
	{
		return 	'<div class="input-append">'.
				'<input class="input-medium" type="text" name="' . htmlspecialchars($this->name, ENT_COMPAT, 'UTF-8') . '" id="' . htmlspecialchars($this->id, ENT_COMPAT, 'UTF-8') . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>'.
				'<span class="add-on">sec</span>'.
				'</div>';
	}
}
