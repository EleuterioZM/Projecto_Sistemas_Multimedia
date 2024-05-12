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
<form name="adminForm" id="adminForm" class="ktForm" action="index.php" method="post" data-fd-grid>
	<div class="panel-table">
		<table class="app-table app-table-middle" data-language-list>
			<thead>
			<tr>
				<th width="1%">
					<?php echo $this->fd->html('table.checkAll'); ?>
				</th>
				<th>
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_TITLE', 'title', $ordering, $direction); ?>
				</th>
				<th width="10%" class="center">
					<?php echo JText::_('COM_KOMENTO_TABLE_COLUMN_LOCALE'); ?>
				</th>
				<th width="15%" class="center">
					<?php echo JText::_('COM_KOMENTO_TABLE_COLUMN_STATE'); ?>
				</th>
				<th width="10%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_PROGRESS', 'progress', $ordering, $direction); ?>
				</th>
				<th width="10%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_LAST_UPDATED', 'updated', $ordering, $direction); ?>
				</th>
				<th width="5%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_TABLE_COLUMN_ID', 'id', $ordering, $direction); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($languages) { ?>

				<?php $i = 0; ?>
				<?php foreach($languages as $language){ ?>
				<tr id="<?php echo 'kmt-' . $language->id; ?>" class="kmt-row" data-id="<?php echo $language->id;?>">
					<td class="center">
						<?php echo $this->fd->html('table.id', $i, $language->id); ?>
					</td>
					<td>
						<b><?php echo $language->title; ?></b>
					</td>
					<td class="center">
						<?php echo $language->locale;?>
					</td>
					<td class="center">
						<?php if ($language->state == KOMENTO_LANGUAGES_INSTALLED) { ?>
						<span class="t-text--success">
							<b><?php echo JText::_('COM_KOMENTO_LANGUAGES_INSTALLED'); ?></b>
						</span>
						<?php } ?>

						<?php if ($language->state == KOMENTO_LANGUAGES_NEEDS_UPDATING) { ?>
						<span class="t-text--danger">
							<b><?php echo JText::_('COM_KOMENTO_LANGUAGES_REQUIRES_UPDATING'); ?></b>
						</span>
							
						<?php } ?>

						<?php if ($language->state == KOMENTO_LANGUAGES_NOT_INSTALLED) { ?>
							<?php echo JText::_('COM_KOMENTO_LANGUAGES_NOT_INSTALLED'); ?>
						<?php } ?>
					</td>
					<td class="center">
						<?php echo !$language->progress ? 0 : $language->progress;?> %
					</td>
					<td class="center">
						<?php echo $language->updated; ?>
					</td>
					<td class="center">
						<?php echo $language->id; ?>
					</td>
				</tr>
				<?php $i++; ?>
				<?php } ?>
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

	<?php echo $this->fd->html('form.action', '', 'languages', 'languages'); ?>
	<?php echo $this->fd->html('form.ordering', 'ordering', $ordering); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'direction', $direction); ?>
</form>
