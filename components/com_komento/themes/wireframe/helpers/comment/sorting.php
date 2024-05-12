<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php echo $this->fd->html('dropdown.standard', function() use ($activeItem) {
	
	return $this->fd->html('button.standard', '<span data-kt-sort-label>' . JText::_($activeItem->label) . '</span>' . $this->fd->html('icon.font', 'fdi fa fa-chevron-down text-gray-500 ml-xs'), 'default', 'sm');

}, function() use ($options, $activeItem) {

	$items = [];

	foreach ($options as $option) {
		$items[] = $this->fd->html('dropdown.item', $option->label, null, [
			'attributes' => $option->attributes,
			'active' => $activeItem->key === $option->key
		]);
	}

	return $items;
}, [
	'header' => JText::_('COM_KT_SORTING_HEADER'),
	'target' => 'self',
	'class' => 'md:w-[180px]',
	'appearance' => $this->config->get('layout_appearance'),
	'theme' =>  $this->config->get('layout_accent')
]); ?>