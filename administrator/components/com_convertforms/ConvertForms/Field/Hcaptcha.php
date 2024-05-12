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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

use \ConvertForms\Helper;

class Hcaptcha extends \ConvertForms\Field
{
	/**
	 *  Exclude all common fields
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'name',
		'required',
		'size',
		'value',
		'placeholder',
		'browserautocomplete',
		'inputcssclass'
	);

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);

		$this->field->required = true;

        // When captcha is in invisible mode, don't show the control group
        if ($this->field->hcaptcha_type == 'invisible')
        {
            $this->field->cssclass .= 'hide';
        }

		return $this;
	}

	/**
	 *  Get the hCaptcha Site Key used in Javascript code
	 *
	 *  @return  string
	 */
	public function getSiteKey()
	{
		return Helper::getComponentParams()->get('hcaptcha_sitekey');
	}

	/**
	 *  Get the hCaptcha Secret Key used in communication between the website and the hCaptcha server
	 *
	 *  @return  string
	 */
	public function getSecretKey()
	{
		return Helper::getComponentParams()->get('hcaptcha_secretkey');
	}

	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		if (!$this->field->get('required'))
		{
			return true;
		}

		// In case this is a submission via URL, skip the check.
		if (\JFactory::getApplication()->input->get('task') == 'optin')
		{
			return true;
		}

        $hcaptcha = new \NRFramework\Integrations\HCaptcha(
            ['secret' => $this->getSecretKey()]
        );

		$response = isset($this->data['h-captcha-response']) ? $this->data['h-captcha-response'] : null;

        $hcaptcha->validate($response);

        if (!$hcaptcha->success())
        {
            throw new \Exception($hcaptcha->getLastError());
        }
	}

	/**
	 *  Display a text before the form options
	 *
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader()
	{
		if ($this->getSiteKey() && $this->getSecretKey())
		{
			return;
		}

		$url = \JURI::base() . 'index.php?option=com_config&view=component&component=com_convertforms#hcaptcha';

		return 
			\JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE') . 
			' <a onclick=\'window.open("' . $url . '", "cfrecaptcha", "width=1000, height=750");\' href="#">' 
				. \JText::_("COM_CONVERTFORMS_FIELD_HCAPTCHA_CONFIGURE") . 
			'</a>.';
	}
}