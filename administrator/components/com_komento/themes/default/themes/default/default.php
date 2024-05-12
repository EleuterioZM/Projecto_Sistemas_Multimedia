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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-5 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_THEMES_EDIT_FILE_INFO'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_THEMES_SELECT_FILE', 'file', '', '', false); ?>

						<div class="flex-grow">
							<select id="file" name="file" class="o-form-control" data-files-selection>
								<option value=""><?php echo JText::_('COM_KOMENTO_THEMES_DROPDOWN_SELECT_FILE'); ?></option>
								<?php foreach ($files as $group => $files) { ?>
									<optgroup label="<?php echo ucfirst($group);?>">
										<?php foreach ($files as $file) { ?>
										<option value="<?php echo $file->id;?>" <?php echo $id == $file->id ? 'selected="selected"' : '';?>
										<?php echo $file->modified ? 'style="background-color: #d3f1d7;"' : '';?>
										>
											<?php echo $file->title;?> <?php echo $file->modified ? JText::_('(Modified)') : ''; ?>
										</option>
										<?php } ?>
									</optgroup>
								<?php } ?>
							</select>

							<?php if ($item) { ?>
							<div class="mt-xs">
								<?php if ($item->modified) { ?>
									<span class="text-success">
										<?php echo JText::_('COM_KOMENTO_THEMES_EDIT_FILE_MODIFIED'); ?>
									</span>
								<?php } else {  ?>
									<span>
										<?php echo JText::_('COM_KOMENTO_THEMES_EDIT_FILE_NOT_MODIFIED'); ?>
									</span>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
					</div>

					<?php if ($item) { ?>
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_THEMES_PATH', 'absolute', '', '', false); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'absolute', $item->absolute, 'absolute', ['disabled' => true]); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_THEMES_OVERRIDE_PATH', 'override', '', '', false); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'override', $item->override, 'override', ['disabled' => true]); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_THEMES_CUSTOM_NOTES', 'notes', '', '', false); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.textarea', 'notes', $item->notes, 'notes'); ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-span-1 md:col-span-7 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_THEMES_EDIT_FILE_EDITOR'); ?>
				<div class="panel-body">
					<?php if ($item) { ?>
						<?php echo $editor->display('contents', $item->contents, '100%', '400px', 80, 20, false, null, null, null, array('syntax' => 'php', 'filter' => 'raw')); ?>
					<?php } else { ?>
					<div class="is-empty">
						<?php echo $this->fd->html('html.emptyList', 'COM_KOMENTO_THEMES_EMPTY_SELECT_FILE', ['icon' => 'fdi far fa-edit']); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', 'themes', 'themes'); ?>
	<?php echo $this->fd->html('form.hidden', 'id', $id); ?>
	<?php echo $this->fd->html('form.hidden', 'element', $element); ?>
</form>