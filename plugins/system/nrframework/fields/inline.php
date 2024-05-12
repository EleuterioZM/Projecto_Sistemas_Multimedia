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

class JFormFieldNR_Inline extends NRFormField
{
	/**
	 *  Method to render the input field
	 *
	 *  @return  string
	 */
	protected function getInput()
	{
		JHtml::stylesheet('plg_system_nrframework/inline-control-group.css', ['relative' => true, 'version' => 'auto']);

		$start = $this->get('start', 1);
		$end   = $this->get('end', 0);

		if ($start && !$end)
		{
			return '<div class="inline-control-group '.$this->get('class').'"><div><div>';
		}

		return '</div></div></div>';
	}
}