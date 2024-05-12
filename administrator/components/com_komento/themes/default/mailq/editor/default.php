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
<form name="adminForm" id="adminForm" class="esForm" action="index.php" method="post" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.lists', 'filter_editor_state', [
			'' => 'Select Template Section',
			'base' => 'FD_LABEL',
			'templates' => 'COM_KOMENTO'
		], $currentFilter, ['minWidth' => 280]); ?>
	</div>
	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
			<tr>
				<th width="1%">
					<?php echo $this->fd->html('table.checkAll'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KOMENTO_TABLE_COLUMN_FILENAME'); ?>
				</th>
				<th width="40%" class="center">
					<?php echo JText::_('COM_KOMENTO_TABLE_COLUMN_LOCATION'); ?>
				</th>
				<th width="10%" class="center">
					<?php echo JText::_('COM_KT_TABLE_COLUMN_PREVIEW'); ?>
				</th>
				<th width="10%" class="center">
					<?php echo JText::_('COM_KOMENTO_TABLE_COLUMN_MODIFIED'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
				<?php if ($files) { ?>
					<?php $i = 0; ?>
					<?php foreach ($files as $file) { ?>
					<tr>
						<td class="center">
							<?php echo $this->fd->html('table.id', $i, base64_encode($file->relative)); ?>
						</td>
						<td>
							<div>
								<a href="index.php?option=com_komento&view=mailq&layout=editfile&file=<?php echo urlencode($file->relative);?><?php echo $file->base ? '&base=1' : ''; ?>">
									<?php echo $file->name; ?>
								</a>

								<?php if ($file->base) { ?>
								&nbsp;
								<?php echo $this->fd->html('label.standard', 'FD_LABEL', 'gray'); ?>
								<?php } ?>
							</div>

							<div class="mt-xs">
								<?php echo $file->desc;?>
							</div>
						</td>
						<td width="40%" class="center">
							<?php echo $file->override ? str_ireplace(JPATH_ROOT, '', $file->overridePath) : '&mdash;'; ?>
						</td>
						<td width="10%" class="center">
							<?php if ($file->relative == '/template.php' || $file->base) { ?>
								&mdash;
							<?php } else { ?>
								<?php echo $this->fd->html('button.link', null, $this->fd->html('icon.font', 'fdi fa fa-eye'), 'default', 'sm', [
									'attributes' => 'data-mail-preview="' . urlencode($file->relative) . '"',
									'iconOnly' => true
								]); ?>
							<?php } ?>
						</td>
						<td width="10%" class="center">
							<?php echo $this->html('grid.published', $file, 'files', 'override', [], [
								0 => 'No overrides',
								1 => 'Override exists'
							], [], false); ?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->fd->html('form.action', '', 'mailq', 'mailq', 'editor'); ?>
</form>