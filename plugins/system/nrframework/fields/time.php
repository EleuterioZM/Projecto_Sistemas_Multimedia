<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNR_Time extends NRFormField
{

	/**
	 * Sets the time value
	 * 
	 * @var  string  $time_value
	 */
	private $time_value = null;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		// Setup properties
		$this->hint      = $this->get('hint', '00:00');
		$this->class     = $this->get('class');
		$this->placement = $this->get('placement', 'top');
		$this->align     = $this->get('align', 'left');
		$this->autoclose = $this->get('autoclose', 'true');
		$this->default   = $this->get('default', 'now');
		$this->donetext  = $this->get('donetext', 'Done');
		$this->required  = $this->get('required') === 'true';

		/**
		 * When an object is created using this class, it cannot set $this->value
		 * So we set $time_value and then use it's value to display the time
		 */
		$this->value = !is_null($this->time_value) ? $this->time_value : $this->value;

		// Add styles and scripts to DOM
		JHtml::_('jquery.framework');
		JHtml::script('plg_system_nrframework/vendor/jquery-clockpicker.min.js', ['relative' => true, 'version' => true]);
		JHtml::stylesheet('plg_system_nrframework/vendor/jquery-clockpicker.min.css', ['relative' => true, 'version' => true]);

		static $run;
		// Run once to initialize it
		if (!$run)
		{
			$this->doc->addScriptDeclaration('
				jQuery(function($) {
					$(".clockpicker").clockpicker();
				});
        	');

			// Fix a CSS conflict caused by the template.css on Joomla 3
			if (!defined('nrJ4'))
			{
				// Fuck you template.css
				$this->doc->addStyleDeclaration('
					.clockpicker-align-left.popover > .arrow {
					    left: 25px;
					}
				');
			}

			$run = true;
		}

		return '
			<div class="clockpicker" data-donetext="' . $this->donetext . '" data-default="' . $this->default . '" data-placement="' . $this->placement . '" data-align="' . $this->align . '" data-autoclose="' . $this->autoclose . '">
				' . parent::getInput() . '
			</div>';
	}

	/**
	 * Sets the $time_value of the time when created as an object
	 * due to not being able to set the $this->value byitself
	 * 
	 * @param   string  $value
	 * 
	 * @return  void
	 */
	public function setValue($value)
	{
		$this->time_value = $value;
	}
}