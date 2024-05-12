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
<form action="index.php" name="adminForm" id="adminForm" method="post" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search, 'search', ['tooltip' => 'COM_KT_SEARCH_TOOLTIP_COMMENTS']); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $pagination->limit); ?>
	</div>

	<div class="panel-table">
		<div class="relative overflow-auto">
			<table class="app-table app-table-middle">
				<thead>
				<tr>
					<?php if (!$browse) { ?>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<?php } ?>

					<th style="text-align:left;">
						<?php echo JText::_('COM_KOMENTO_COLUMN_FULLNAME'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_KOMENTO_COLUMN_USERNAME'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_KOMENTO_COLUMN_EMAIL'); ?>
					</th>

					<?php if (!$browse) { ?>
					<th width="15%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_REGISTERDATE', 'u.registerDate', $order, $orderDirection); ?>
					</th>

					<th width="15%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_LASTVISITDATE', 'u.lastvisitDate', $order, $orderDirection); ?>
					</th>
					<?php } ?>

					<th width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_ID', 'u.id', $order, $orderDirection); ?>
					</th>
				</tr>
				</thead>

				<tbody>
				<?php if ($users) { ?>
					<?php $i = 0; ?>

					<?php foreach ($users as $user) { ?>
					<tr>
						<?php if (!$browse) { ?>
						<td class="center">
							<?php echo $this->fd->html('table.id', $i, $user->id); ?>
						</td>
						<?php } ?>

						<td>
							<?php if ($browse) { ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $user->id;?>','<?php echo addslashes($this->fd->html('str.escape', $user->name));?>');"><?php echo $user->name;?></a>
							<?php } else { ?>
								<?php echo $user->name; ?>
							<?php } ?>
						</td>

						<td class="center">
							<?php echo $user->username; ?>
						</td>

						<td class="center">
							<?php echo $user->email; ?>
						</td>

						<?php if (!$browse) { ?>
						<td class="center">
							<?php echo $user->registerDate; ?>
						</td>

						<td class="center">
							<?php echo $user->lastvisitDate; ?>
						</td>
						<?php } ?>

						<td class="center">
							<?php echo $user->id; ?>
						</td>
					</tr>
					<?php } ?>

				<?php } ?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="13">
							<div class="footer-pagination">
								<?php echo $pagination->getListFooter(); ?>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', '', 'users', 'users'); ?>
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $orderDirection); ?>
</form>