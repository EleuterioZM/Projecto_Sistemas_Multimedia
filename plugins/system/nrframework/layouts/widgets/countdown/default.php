<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

if ($countdown_type === 'static' && (empty($value) || $value === '0000-00-00 00:00:00'))
{
	return;
}

if ($load_stylesheet)
{
	if ($theme !== 'custom')
	{
		\JHtml::stylesheet('plg_system_nrframework/widgets/countdown.css', ['relative' => true, 'version' => 'auto']);
	}
	else
	{
		\JHtml::stylesheet('plg_system_nrframework/widgets/widget.css', ['relative' => true, 'version' => 'auto']);
	}
}

if ($load_css_vars && !empty($custom_css))
{
	JFactory::getDocument()->addStyleDeclaration($custom_css);
}

\JHtml::script('plg_system_nrframework/widgets/countdown.js', ['relative' => true, 'version' => 'auto']);
?>
<div class="nrf-widget nrf-countdown<?php echo $css_class; ?>" id="<?php echo $id; ?>" <?php echo $atts; ?>></div>