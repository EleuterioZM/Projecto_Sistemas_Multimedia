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

defined('_JEXEC') or die('Restricted access');

extract($displayData);

if (!$class->getSiteKey() || !$class->getSecretKey())
{
	echo JText::_('COM_CONVERTFORMS_FIELD_HCAPTCHA') . ': ' . JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE');
	return;
}

// Load callback first for browser compatibility
JHtml::_('script', 'com_convertforms/hcaptcha.js', ['version' => 'auto', 'relative' => true], ['async' => 'async', 'defer' => 'defer']);

// Load hCAPTCHA API JS
JHtml::_('script', 'https://hcaptcha.com/1/api.js?onload=ConvertFormsInitHCaptcha&render=explicit&hl=' . \JFactory::getLanguage()->getTag(), [], ['async' => 'async', 'defer' => 'defer']);

?>

<div class="h-captcha"
	data-sitekey="<?php echo $class->getSiteKey(); ?>"
	data-theme="<?php echo $field->theme ?>"
	data-size="<?php echo $field->hcaptcha_type == 'invisible' ? $field->hcaptcha_type : $field->size ?>">
</div>