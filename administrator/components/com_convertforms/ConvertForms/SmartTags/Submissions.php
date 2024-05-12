<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\SmartTags;

defined('_JEXEC') or die('Restricted access');

use NRFramework\SmartTags\SmartTag;
use ConvertForms\Helper;

class Submissions extends SmartTag
{
	/**
	 * Returns the submsissions data.
	 * Used in Convert Forms Front End Submissions View.
	 * 
	 * @return  string
	 */
	public function getSubmissions()
	{
		$data = isset($this->data['front_end_submission']) ? $this->data['front_end_submission'] : [];

		if (!$data)
		{
			return '';
		}

		$submissions = isset($data['submissions']) ? $data['submissions'] : '';

		if (!$submissions)
		{
			return '';
		}

		$layout_row = isset($data['layout_row']) ? $data['layout_row'] : '';

		if (!$layout_row)
		{
			return '';
		}

		$html = '';

		foreach ($submissions as $submission)
		{
			$html .= \ConvertForms\Submission::replaceSmartTags($submission, $layout_row);
		}

		return $html;
	}
	
	/**
	 * Returns the submsissions count
	 * 
	 * @return  string
	 */
	public function getCount()
	{
		$submission = isset($this->data['submission']) ? $this->data['submission'] : null;

		if (!$submission)
		{
			return 0;
		}

		$form_id = isset($submission->form_id) ? $submission->form_id : null;

		if (!$form_id)
		{
			return 0;
		}

		return Helper::getFormLeadsCount($form_id);
	}
}