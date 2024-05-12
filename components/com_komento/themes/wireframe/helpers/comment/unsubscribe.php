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
<?php echo $this->fd->html('button.link', null, $this->fd->html('icon.font', 'fdi fa fa-envelope text-gray-500 mr-xs') . ' ' . JText::_('COM_KOMENTO_FORM_UNSUBSCRIBE'), 'default', 'sm', [
	'attributes' => 'data-kt-unsubscribe'
]); ?>