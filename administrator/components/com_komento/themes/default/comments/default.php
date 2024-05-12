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

		<?php if ($layout !== 'pending') { ?>
			<?php echo $this->fd->html('filter.lists', 'filter_publish', [
				'all' => 'COM_KOMENTO_ALL_STATUS',
				'published' => 'COM_KOMENTO_PUBLISHED',
				'unpublished' => 'COM_KOMENTO_UNPUBLISHED'
			], $publishState); ?>
		<?php } ?>

		<?php echo $this->html('filter.extensions', 'filter_component', $selectedExtension); ?>
		
		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<div class="relative overflow-auto">
			
			<table class="app-table app-table-middle" data-comments-list>
				<thead>
				<tr>
					

					<th><?php echo JText::_('COM_KOMENTO_COLUMN_COMMENT'); ?></th>

					<th widht="10%" class="center">
					<?php if (!$search) { ?>
						<?php echo JText::_('COM_KOMENTO_COLUMN_COMMENT_CHILD'); ?>
					<?php } else { ?>
						<?php echo JText::_('COM_KOMENTO_COLUMN_COMMENT_PARENT'); ?>
					<?php } ?>
					</th>

					<th width="5%" class="center">
						<?php echo JText::_('COM_KOMENTO_COLUMN_STATUS');?>
					</th>

					<?php if ($layout === 'reports') { ?>
					<th width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_REPORT_COUNT', 'reports', $order, $orderDirection); ?>
					</th>
					<?php } ?>

					<?php if (!in_array($layout, ['reports', 'pending'])) { ?>
					<th width="5%" class="center">
						<?php echo JText::_('COM_KOMENTO_COLUMN_FEATURED'); ?>
					</th>
					<?php } ?>

					<th width="20%" class="center">
						<?php echo JText::_('COM_KOMENTO_COLUMN_EXTENSION'); ?>	
					</th>

					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_DATE', 'created', $order, $orderDirection); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_KOMENTO_COLUMN_AUTHOR'); ?>
					</th>

					<th width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_ID', 'id', $order, $orderDirection); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php if ($comments) { ?>
					<?php $i = 0; ?>
					<?php foreach( $comments as $comment ){ ?>
						<?php echo $this->output('admin/comments/item', [
							'comment' => $comment, 
							'layout' => $layout, 
							'i' => $i, 
							'search' => $search
						]); ?>
						<?php $i++; ?>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="13" class="is-empty">
						<?php echo $this->fd->html('html.emptyList', $emptyMessage, ['icon' => 'fdi fa fa-comments']); ?>
					</td>
				</tr>
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

	<?php echo $this->fd->html('form.action', '', 'comments', 'comments'); ?>
	<?php echo $this->fd->html('form.hidden', 'return', $return); ?>
	<?php echo $this->fd->html('form.hidden', 'layout', $layout); ?>
	<?php echo $this->fd->html('form.hidden', 'parentid', (int) $parentid); ?>
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $orderDirection); ?>
</form>

