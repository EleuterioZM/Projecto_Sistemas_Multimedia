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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', $tabs); ?>

		<div class="tab-content">
			<?php foreach ($tabs as $tab) { ?>
			<div id="<?php echo $tab->id;?>" class="t-hidden <?php echo $tab->active ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output($tab->namespace, ['tab' => $tab]); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->fd->html('admin.toolbarSearch'); ?>

	<?php echo $this->fd->html('admin.toolbarActions', 'COM_KT_OTHER_ACTIONS', [
		(object) [
			'title' => 'COM_KT_EXPORT_SETTINGS',
			'cmd' => 'export'
		],
		(object) [
			'title' => 'COM_KT_IMPORT_SETTINGS',
			'cmd' => 'import'
		]
	]); ?>

	<input type="hidden" name="current" value="<?php echo $layout;?>" />
	<input type="hidden" name="tab" value="<?php echo $active; ?>" data-fd-active-tab-input />
	<input type="hidden" name="component" value="com_komento" />
	<?php echo $this->fd->html('form.action', 'save', 'settings'); ?>
</form>