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

if (!$readonly && !$disabled)
{
	JHtml::script('plg_system_nrframework/widgets/rating.js', ['relative' => true, 'version' => 'auto']);
}

if ($load_stylesheet)
{
	JHtml::stylesheet('plg_system_nrframework/widgets/rating.css', ['relative' => true, 'version' => 'auto']);
}

if ($load_css_vars)
{
	JFactory::getDocument()->addStyleDeclaration('
		.nrf-rating-wrapper.' . $id . ' {
			--rating-selected-color: ' . $selected_color . ';
			--rating-unselected-color: ' . $unselected_color . ';
			--rating-size: ' . $size . 'px;
		}
	');
}

echo $this->sublayout($half_ratings ? 'half' : 'full', $displayData);