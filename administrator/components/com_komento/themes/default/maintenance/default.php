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
<form action="index.php?option=com_komento&view=maintenance" name="adminForm" id="adminForm" method="post" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.lists', 'filter_version', $versions, $version, [
			'minWidth' => 300
		]); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle" data-comments-list>
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>

					<th class="title" nowrap="nowrap" style="text-align:left;">
						<?php echo JText::_('COM_KOMENTO_MAINTENANCE_COLUMN_TITLE'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_KOMENTO_MAINTENANCE_COLUMN_VERSION'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($scripts) { ?>
					<?php $i = 0; ?>
					<?php foreach ($scripts as $script) { ?>
					<tr>
						<td class="center">
							<?php echo $this->fd->html('table.id', $i++, $script->key); ?>
						</td>

						<td>
							<div><b><?php echo $script->title; ?></b></div>
							<div><?php echo $script->description; ?></div>
						</td>
						<td class="center"><?php echo $script->version; ?></td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="3" align="center" class="center">
							<?php echo JText::_('COM_KOMENTO_MAINTENANCE_SCRIPT_NOT_FOUND');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="3">
						<div class="footer-pagination">
						<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>

		</table>
	</div>

	<?php echo $this->fd->html('form.action', 'maintenance', 'maintenance'); ?>
	<input type="hidden" name="ordering" value="<?php echo $order;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $orderDirection;?>" data-table-grid-direction />
</form>

