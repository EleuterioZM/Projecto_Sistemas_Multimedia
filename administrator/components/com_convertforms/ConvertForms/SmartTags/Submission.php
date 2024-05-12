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

class Submission extends SmartTag
{
	/**
	 * Returns the submsission ID
	 * 
	 * @return  string
	 */
	public function getID()
	{
		return isset($this->data['submission']->id) ? $this->data['submission']->id : '';
	}

	/**
	 * Returns the submsission User ID
	 * 
	 * @return  string
	 */
	public function getUser_ID()
	{
		return isset($this->data['submission']->user_id) ? $this->data['submission']->user_id : '';
	}

	/**
	 * Returns the submission created date
	 * 
	 * @return  string
	 */
	public function getCreated()
	{
		return isset($this->data['submission']->created) ? $this->data['submission']->created : '';
	}

	/**
	 * Returns the submission modified date
	 * 
	 * @return  string
	 */
	public function getModified()
	{
		return isset($this->data['submission']->modified) ? $this->data['submission']->modified : '';
	}

	/**
	 * Returns the submission created date
	 * 
	 * @return  string
	 */
	public function getDate()
	{
		return isset($this->data['submission']->created) ? $this->data['submission']->created : '';
	}

	/**
	 * Returns the submission campaign id
	 * 
	 * @return  string
	 */
	public function getCampaign_ID()
	{
		return isset($this->data['submission']->campaign_id) ? $this->data['submission']->campaign_id : '';
	}

	/**
	 * Returns the submission form id
	 * 
	 * @return  string
	 */
	public function getForm_ID()
	{
		return isset($this->data['submission']->form_id) ? $this->data['submission']->form_id : '';
	}

	/**
	 * Returns the submission visitor id
	 * 
	 * @return  string
	 */
	public function getVisitor_ID()
	{
		return isset($this->data['submission']->visitor_id) ? $this->data['submission']->visitor_id : '';
	}

	/**
	 * Returns the submission status
	 * 
	 * @return  string
	 */
	public function getStatus()
	{
		return isset($this->data['submission']->state) && $this->data['submission']->state === '1' ? \JText::_('COM_CONVERTFORMS_SUBMISSION_CONFIRMED') : \JText::_('COM_CONVERTFORMS_SUBMISSION_UNCONFIRMED');
	}

	/**
	 * Returns the submission PDF
	 * 
	 * @return  string
	 */
	public function getPDF()
	{
		if (!isset($this->data['extra_data']['pdf']))
		{
			return '';
		}

		return $this->data['extra_data']['pdf'];
	}
}