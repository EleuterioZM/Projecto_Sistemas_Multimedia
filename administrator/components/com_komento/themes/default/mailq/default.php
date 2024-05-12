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
<form name="adminForm" id="adminForm" action="index.php" method="post" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search, 'search'); ?>

		<?php echo $this->fd->html('filter.lists', 'filter_publish', [
			'all' => 'COM_KOMENTO_FILTER_SELECT_STATUS',
			'sent' => 'COM_KOMENTO_MAILER_SENT',
			'pending' => 'COM_KOMENTO_MAILER_PENDING'
		], $published); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>


	<div class="panel-table">

		<?php if ($cronLastExecuted) { ?>
		<div class="mt-lg mb-lg">
			<?php echo $this->fd->html('alert.standard', JText::sprintf('COM_KOMENTO_CRON_LAST_EXECUTED', $cronLastExecuted), 'success', ['icon' => 'fdi fa fa-check-circle']); ?>
		</div>
		<?php } ?>

		<?php if (!$cronLastExecuted) { ?>
		<div class="mt-md mb-md">
			<?php echo $this->fd->html('alert.standard', 'COM_KOMENTO_MAILER_DESCRIPTION', 'warning', [
				'icon' => '',
				'button' => $this->fd->html('button.link', 'https://stackideas.com/docs/komento/administrators/cronjobs', 'COM_KOMENTO_SETUP_CRON', 'default', 'sm', [
					'icon' => 'fdi fa fa-external-link-alt',
					'class' => 'ml-lg'
				])
			]); ?>
		</div>
		<?php } ?>

		<div class="relative overflow-auto">
			<table class="app-table app-table-middle">
				<thead>
				<tr>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<th>
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_MAILER_EMAIL_TITLE', 'subject', $ordering, $direction); ?>
					</th>
					<th width="20%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_RECIPIENT', 'recipient', $ordering, $direction); ?>
					</th>
					<th width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_STATE', 'status', $ordering, $direction); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_CREATED', 'created', $ordering, $direction); ?>
					</th>
					<th width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_ID', 'id', $ordering, $direction); ?>
					</th>
				</tr>
				</thead>
				<tbody>
					<?php if ($emails) { ?>
						<?php $i = 0; ?>
						<?php foreach ($emails as $email) { ?>
						<tr id="<?php echo 'kmt-' . $email->id; ?>" class="kmt-row">
							<td class="center">
								<?php echo $this->fd->html('table.id', $i, $email->id); ?>
							</td>
							<td>
								<a href="javascript:void(0);" data-mailer-item-preview data-id="<?php echo $email->id;?>"><?php echo $email->subject; ?></a>
							</td>
							<td class="center">
								<a href="mailto:<?php echo $email->recipient;?>" target="_blank"><?php echo $email->recipient;?></a>
							</td>
							<td class="center">
								<?php echo $this->html('grid.published' , $email , 'mailer' , 'status'); ?>
							</td>
							<td class="center">
								<?php echo $email->created; ?>
							</td>
							<td class="center">
								<?php echo $email->id; ?>
							</td>
						</tr>
						<?php $i++; ?>
						<?php } ?>

					<?php } else { ?>
					<tr>
						<td colspan="8" class="is-empty">
							<?php echo $this->fd->html('html.emptyList', 'COM_KOMENTO_MAILER_NO_EMAILS_YET', ['icon' => 'fdi far fa-envelope']); ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="8">
							<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>


	<?php echo $this->fd->html('form.action', '', 'mailq', 'mailq'); ?>
	<?php echo $this->fd->html('form.ordering', 'filter_order', $ordering); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $direction); ?>
</form>
