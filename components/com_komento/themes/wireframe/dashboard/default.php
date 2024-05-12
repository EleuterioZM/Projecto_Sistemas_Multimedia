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
<div id="fd">
	<div id="kt" class="<?php echo $this->config->get('layout_appearance');?> si-theme-<?php echo $this->config->get('layout_accent');?>">
		<div class="kt-dashboard" data-kt-dashboard>
			<form action="<?php echo JRoute::_('index.php');?>" method="post" data-dashboard-form>				
				<?php echo $this->fd->html('snackbar.standard', '<b>' . JText::_('COM_KOMENTO_DASHBOARD_HEADING') . '</b>', function() use ($comments, $showActionBar, $isModerator, $filter, $totalPending, $totalSpams, $totalReports, $canDeleteComment) {
				ob_start();
				?>
				<div class="flex-shrink-0 flex-col md:flex-row md:order-2 w-full md:w-auto">
					<?php if ($comments && $showActionBar) { ?>
					<div class="kt-dashboard-action-group t-hidden" data-kt-dashboard-actions>
						<div class="space-y-md md:space-y-no md:space-x-sm flex-col md:flex-row flex">
							<div class="w-[180px]">
								<div class="o-select-group">
									<?php echo $this->fd->html('form.dropdown', 'action', '', call_user_func(function() use ($filter, $isModerator, $canDeleteComment) {

										$items = [
											'' => JText::_('Bulk Actions')
										];

										if ($isModerator) {
											if ($filter === 'pending') {
												$items['approve'] = JText::_('COM_KOMENTO_COMMENT_APPROVE');
												$items['reject'] = JText::_('COM_KOMENTO_COMMENT_REJECT');
											}

											if ($filter === 'reports') {
												$items['clear'] = JText::_('COM_KOMENTO_CLEAR_REPORTS');
											}

											if ($filter === 'spam') {
												$items['notspam'] = JText::_('COM_KOMENTO_MARK_NOT_SPAM');
											}

											if ($filter !== 'spam') {
												$items['spam'] = JText::_('COM_KOMENTO_MARK_SPAM');
											}
										}

										if ($filter !== 'pending' && $filter !== 'spam') {

											if ($isModerator) {
												$items['publish'] = JText::_('COM_KOMENTO_COMMENT_PUBLISH');
												$items['unpublish'] = JText::_('COM_KOMENTO_COMMENT_UNPUBLISH');
											}

											if ($canDeleteComment) {
												$items['delete'] =  JText::_('COM_KOMENTO_COMMENT_DELETE');
											}
										}

										return $items;
									}), [
										'attributes' => 'data-action'
									]); ?>
								</div>
							</div>
							<?php echo $this->fd->html('button.standard', 'COM_KT_APPLY', 'default', 'default', [
								'block' => true,
								'class' => 'md:w-auto',
								'attributes' => 'data-kt-apply'
							]); ?>
						</div>
					</div>
					<?php } ?>

					<?php if ($isModerator) { ?>
					<div class="kt-db-filter">
						<?php echo $this->fd->html('form.dropdown', 'filter', $filter, [
							'all' => 'COM_KOMENTO_ALL',
							'pending' => JText::sprintf('COM_KOMENTO_DASHBOARD_PENDING_COUNT', $totalPending),
							'spam' => JText::sprintf('COM_KOMENTO_DASHBOARD_SPAM_COUNT', $totalSpams),
							'reports' => JText::sprintf('COM_KOMENTO_DASHBOARD_REPORT_COUNT', $totalReports)
						], [
							'attributes' => 'data-kt-filter'
						]); ?>
					</div>
					<?php } ?>
				</div>
				<?php
				$contents = ob_get_contents();
				ob_end_clean();

				return [$contents];
				}); ?>

				<div class="border-b border-solid border-gray-300 px-sm py-md md:py-xs">
					<div class="flex w-full items-center flex-col md:flex-row">
						<?php if ($comments) { ?>
						<div class="flex-grow order-2 md:order-1 py-sm md:py-no w-full md:w-auto mt-sm mb-sm">
							<?php if ($showActionBar) { ?>
								<?php echo $this->fd->html('form.checkbox', 'kt-all-checked', false, 1, 'kt-dashboard-check-all', JText::_('Select All'), [
									'attributes' => 'data-kt-dashboard-checkall'
								]); ?>
							<?php } ?>							
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="kt-db-comments divide-y divide-gray-300 mb-md <?php echo empty($comments) ? 'is-empty' : ''; ?>">
					<?php if ($comments) { ?>
						<?php foreach ($comments as $comment) { ?>
						<div class="kt-db-comments__item 
						<?php echo $comment->isPublished() ? 'is-published' : '';?> 
						<?php echo $comment->isUnpublished() ? 'is-unpublished' : '';?> 
						<?php echo $comment->isPending() ? 'is-pending' : '';?> 
						<?php echo $comment->isSpam() ? 'is-spam' : '';?>" data-kt-dashboard-item>
							<div class="kt-db-comment has-checkbox flex px-sm py-sm hover:no-underline text-gray-800">
								<div>
									<?php echo $this->fd->html('form.checkbox', 'cid[]', false, $comment->id, 'kt-comment-' . $comment->id, '', [
										'attributes' => 'data-kt-dashboard-item-checkbox'
									]); ?>
								</div>

								<div class="pr-sm flex-shrink-0">
									<div class="kt-avatar">
										<div class="o-flag__image o-flag--top">
											<?php echo $this->html('html.avatar', $comment->getAuthor(), $comment->getAuthorName()); ?>
										</div>
									</div>
								</div>
								<div class="flex-grow min-w-0 space-y-md">
									<div class="kt-db-comment-content">
										<div class="space-y-2xs md:space-y-no flex-grow">
											<div class="">
												<?php echo $this->html('html.name', $comment->created_by, $comment->getAuthorName(), $comment->getAuthorEmail(), $comment->url); ?>
											</div>

											<div class="flex">
												<div class="flex-grow">
													<div class="kt-comment-date text-gray-500 text-xs">
														<?php echo $comment->getCreatedDate()->toLapsed();?>
													</div>
												</div>
												<div>
													<?php if ($this->config->get('enable_ratings') && $comment->ratings) { ?>
													
														<?php echo $this->fd->html('rating.item', [
															'score' => $comment->ratings
														]); ?>
													
													<?php } ?>
												</div>
											</div>
											
										</div>
										<div class="kt-db-comment-content__bd">
											<p><?php echo $comment->getContent();?></p>
										</div>
										<div class="kt-db-comment-content__ft">
											<div class="kt-db-comment-content-action">
												<div class="o-inline-list text-xs leading-sm">
													<div class="text-gray-500">
														<a class="text-gray-500" href="<?php echo $comment->getPermalink();?>" target="_blank">#<?php echo $comment->id;?></a>
													</div>

													<div class="text-success kt-comment-published" fd-breadcrumb="·">
														<?php echo JText::_('COM_KOMENTO_PUBLISHED');?>
													</div>

													<div class="text-danger kt-comment-unpublished" fd-breadcrumb="·">
														<?php echo JText::_('COM_KOMENTO_UNPUBLISHED');?>
													</div>

													<div class="text-warning kt-comment-pending" fd-breadcrumb="·">
														<?php echo JText::_('COM_KOMENTO_MODERATE');?>
													</div>

													<div class="text-danger kt-comment-spam" fd-breadcrumb="·">
														<?php echo JText::_('COM_KOMENTO_SPAM');?>
													</div>

													<div class="text-gray-500 flex-shrink-0" fd-breadcrumb="·">
														<?php echo JText::sprintf('COM_KOMENTO_DASHBOARD_IN_RESPONSE_TO', '<a href="'. $comment->getItemPermalink() .'" class="text-gray-500 px-2xs">'. $comment->getItemTitle() .'</a>'); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					<?php } ?>

					<?php echo $this->fd->html('html.emptyList', 'COM_KT_DASHBOARD_NO_COMMENTS_POSTED_YET', [
						'icon' => 'fdi fa fa-comments',
						'class' => 'bg-gray-100 rounded-md mb-lg mt-lg'
					]); ?>
				</div>

				<?php if ($pagination) {?>
					<?php echo $pagination->getListFooter(true);?>
				<?php } ?>

				<?php echo $this->fd->html('form.hidden', 'return', $returnURL); ?>
				<?php echo $this->fd->html('form.action', '', 'dashboard', 'dashboard'); ?>
			</form>
		</div>
	</div>
</div>

