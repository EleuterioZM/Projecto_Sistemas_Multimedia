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
<form action="index.php?option=com_komento&view=comments" name="adminForm" id="adminForm" method="post" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search, 'search', ['tooltip' => 'COM_KT_SEARCH_TOOLTIP_COMMENTS']); ?>

		<?php echo $this->html('filter.extensions', 'filter_component', $selectedExtension); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle" data-comments-list>
			<thead>
			<tr>
				

				<th><?php echo JText::_('COM_KOMENTO_COLUMN_COMMENT'); ?></th>
				
				<th width="15%" class="center">
					<?php echo JText::_('COM_KOMENTO_COLUMN_EXTENSION'); ?>
				</th>

				<th width="10%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_DATE', 'created', $order, $orderDirection); ?>
				</th>

				<th width="15%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_AUTHOR', 'created_by', $order, $orderDirection); ?>
				</th>

				<th width="5%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_KOMENTO_COLUMN_ID', 'id', $order, $orderDirection); ?>	
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($comments) { ?>
				<?php $i = 0; ?>
				<?php foreach ($comments as $comment) { ?>
					<tr id="<?php echo 'kmt-' . $comment->id; ?>" class="kmt-row" depth="<?php echo $comment->depth; ?>" parentid="<?php echo $comment->parent_id; ?>">
						<td class="center">
							<?php echo $this->fd->html('table.id', $i, $comment->id); ?>
						</td>

						<td class="comment-cell">
							<div>
								<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comments&from=spamlist&layout=form&id=' . $comment->id); ?>">
									<?php echo FCJString::substr(strip_tags($comment->getContent()), 0, 80); ?>...
								</a>
							</div>

							<?php if ($comment->getSpamType()) { ?>
							<div class="mt-xs text-info">
								<?php echo $this->fd->html('icon.font', 'fdi fa fa-shield-virus'); ?>&nbsp;<?php echo JText::sprintf('Detected with %1$s', ucfirst($comment->getSpamType())); ?>
							</div>
							<?php } ?>
						</td>
						
						<td class="center">
							<?php if ($comment->extension) { ?>
								<a href="<?php echo $comment->getPermalink(); ?>" target=_blank><?php echo $comment->contenttitle; ?></a>
							<?php } else { ?>
								<span class="error"><?php echo $comment->contenttitle; ?></span>
							<?php } ?>
							<div class="mt-xs">
								<?php echo $this->fd->html('label.standard', $comment->componenttitle); ?>
							</div>
						</td>

						<td class="center">
							<?php echo $comment->getCreatedDate()->toSql(); ?>
						</td>

						<td class="center">
							<?php echo $comment->name; ?>
							<div class="mt-sm">
								<?php echo $this->fd->html('label.standard', $comment->ip);?>

								<?php 
								$commentLib = KT::comment($comment->id);

								$author = $comment->getAuthor();

								if ($author->id && $author->block) {
								?>
									<?php echo $this->fd->html('label.standard', 'Banned', 'danger');?>
								<?php } ?>
							</div>
						</td>

						<td class="center">
							<?php echo $comment->id; ?>
						</td>
					</tr>
					<?php $i++; ?>
				<?php } ?>

			<?php } else { ?>
			<tr>
				<td colspan="13" class="is-empty">
					<?php echo $this->fd->html('html.emptyList', 'COM_KOMENTO_COMMENTS_NO_SPAM_COMMENTS', ['icon' => 'fdi fa fa-comments']); ?>
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

	<?php echo $this->fd->html('form.action', '', 'comments', 'comments'); ?>
	<?php echo $this->fd->html('form.hidden', 'return', $return); ?>
	<?php echo $this->fd->html('form.hidden', 'layout', 'spamlist'); ?>
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $orderDirection); ?>
</form>

<?php if ($this->config->get('antispam_akismet_key')) { ?>
	<?php echo $this->fd->html('admin.toolbarActions', 'COM_KOMENTO_AKISMET', [
		(object) [
			'title' => 'COM_KOMENTO_TRAIN_AKISMET_SPAM',
			'cmd' => 'spam',
			'custom' => true
		],

		(object) [
			'title' => 'COM_KOMENTO_TRAIN_AKISMET_HAM',
			'cmd' => 'ham',
			'custom' => true
		]
	]); ?>
<?php } ?>

