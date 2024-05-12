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
<form action="index.php?option=com_komento&view=subscribers" method="post" name="adminForm" id="adminForm" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->html('filter.extensions', 'filter_component', $selectedExtension); ?>
		
		<?php echo $this->fd->html('filter.spacer'); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
			<tr>
				<th width="1%">
					<?php echo $this->fd->html('table.checkAll'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KOMENTO_COLUMN_USER'); ?>
				</th>
				<th width="30%" class="center">
					<?php echo JText::_('COM_KT_COLUMN_ITEM_TITLE'); ?>
				</th>
				<th width="15%" class="center">
					<?php echo JText::_('COM_KOMENTO_COLUMN_ITEM_EXTENSION'); ?>
				</th>
				<th width="15%" class="center">
					<?php echo JText::_('COM_KOMENTO_COLUMN_SUBSCRIPTION_TYPE'); ?>
				</th>
				<th width="15%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_DATE', 'created', $order, $orderDirection); ?>
				</th>
				<th width="5%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_ID', 'id', $order, $orderDirection); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($subscribers) { ?>
				<?php $i = 0; ?>
				<?php foreach ($subscribers as $subscriber) { ?>
				<tr>
					<td class="center">
						<?php echo $this->fd->html('table.id', $i, $subscriber->id); ?>
					</td>
					<td>
						<a href="index.php?option=com_komento&view=subscribers&layout=form&id=<?php echo $subscriber->id;?>"><?php echo $subscriber->fullname; ?> (<?php echo $subscriber->email;?>)</a>
					</td>

					<td class="center">
						<?php echo $subscriber->contenttitle; ?>
					</td>
					
					<td class="center">
						<?php echo $subscriber->componenttitle; ?>
					</td>

					<td class="center">
						<?php echo JText::_('COM_KOMENTO_SUBSCRIPTION_' . strtoupper($subscriber->type));?>
					</td>

					<td class="center">
						<?php echo $subscriber->created;?>
					</td>

					<td class="center">
						<?php echo $subscriber->id; ?>
					</td>
				</tr>
				<?php $i++;?>
				<?php } ?>
			<?php } else { ?>
			<tr>
				<td colspan="7" class="is-empty">
					<?php echo $this->fd->html('html.emptyList', 'COM_KOMENTO_SUBSCRIBERS_NO_SUBSCRIBERS', ['icon' => 'fdi fa fa-bell']); ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action', '', 'subscribers', 'subscribers'); ?>
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $orderDirection); ?>
</form>
