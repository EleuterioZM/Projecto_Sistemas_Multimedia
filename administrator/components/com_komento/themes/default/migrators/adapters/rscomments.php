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
<div class="panel">
	<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_MIGRATORS_LAYOUT_MAIN', false); ?>

	<div class="panel-body">
		<?php echo $this->output('admin/migrators/adapters/fields/components'); ?>
		
		<?php echo $this->output('admin/migrators/adapters/fields/state'); ?>

		<?php echo $this->output('admin/migrators/adapters/fields/likes'); ?>

		<?php echo $this->output('admin/migrators/adapters/fields/button'); ?>
	</div>
</div>