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
	echo JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA') . ': ' . JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE');
	return;
}

JHtml::_('script', 'com_convertforms/recaptcha_v2_invisible.js', ['version' => 'auto', 'relative' => true]);
JHtml::_('script', 'https://www.google.com/recaptcha/api.js?onload=ConvertFormsInitInvisibleReCaptcha&render=explicit&hl=' . JFactory::getLanguage()->getTag());

?>

<div class="g-invisible-recaptcha" data-sitekey="<?php echo $class->getSiteKey(); ?>" data-badge="<?php echo $field->badge ?>"></div>