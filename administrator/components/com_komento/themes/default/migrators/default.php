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
<form action="<?php echo JRoute::_('index.php');?>" method="post" name="adminForm" id="adminForm" data-migrate-article-form>

	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-6 w-auto">
			<?php echo $contents; ?>
		</div>

		<div class="col-span-1 md:col-span-6 w-auto">
			<?php echo $this->output('admin/migrators/adapters/progress'); ?>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<?php echo $this->fd->html('form.hidden', 'layout', $layout); ?>
</form>
