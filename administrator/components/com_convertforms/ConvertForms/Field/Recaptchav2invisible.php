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

class RecaptchaV2Invisible extends \ConvertForms\Field\Recaptcha
{
    /**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
        parent::setField($field);

        // When the widget is shown at the bottom right or left position, we need to remove the .control-group's div default padding.
        if ($this->field->badge != 'inline')
        {
            $this->field->cssclass .= 'cf-no-padding';
        }
        
		return $this;
    }
    
	/**
	 *  Get the reCAPTCHA Site Key used in Javascript code
	 *
	 *  @return  string
	 */
	public function getSiteKey()
	{
		return Helper::getComponentParams()->get('recaptcha_sitekey_invs');
	}

	/**
	 *  Get the reCAPTCHA Secret Key used in communication between the website and the reCAPTCHA server
	 *
	 *  @return  string
	 */
	public function getSecretKey()
	{
		return Helper::getComponentParams()->get('recaptcha_secretkey_invs');
	}
}

?>