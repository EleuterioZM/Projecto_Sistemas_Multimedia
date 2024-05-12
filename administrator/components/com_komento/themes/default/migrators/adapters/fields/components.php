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
<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
	<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_SELECT_COMPONENTS', 'components'); ?>

	<div class="flex-grow">
		<?php echo $this->fd->html('form.dropdown', 'components', 'all', KT::migrator()->getAdapter($layout)->getComponentSelection(), ['attributes' => 'data-' . $layout . '-components']); ?>
	</div>
</div>