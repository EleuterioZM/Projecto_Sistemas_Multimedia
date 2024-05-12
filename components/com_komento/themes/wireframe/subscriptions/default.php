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
		<div class="kt-dashboard" data-kt-subscriptions>

			<form action="<?php echo JRoute::_('index.php');?>" method="post" data-subscriptions-form>

				<?php echo $this->fd->html('snackbar.standard', '<b>' . JText::_('COM_KT_SUBSCRIPTIONS') . '</b>');
				?>

				<div class="o-form-section space-y-md">
					<div class="flex px-md py-2xl flex-col md:flex-row gap-sm">
						<div class="md:w-[320px] flex-shrink-0">
							<label for="" class="o-form-label">
								<?php echo JText::_('COM_KT_SUBSCRIPTIONS_SETTINGS'); ?>
							</label>
							<div class="text-gray-500">
								<?php echo JText::_('COM_KT_SUBSCRIPTIONS_SETTINGS_DESC'); ?>
							</div>
						</div>
						<div class="flex-grow space-y-xs">
							<div class="">
								<div><?php echo JText::_('COM_KT_SUBSCRIPTIONS_INTERVAL'); ?></div>
								<div>
									<?php echo $this->fd->html('form.dropdown', 'interval', $defaultInterval, $intervalOptions, ['attributes' => 'data-subscription-interval']); ?>
								</div>
							</div>
							<div>
								<div><?php echo JText::_('COM_KT_SUBSCRIPTIONS_NUM_POSTS'); ?></div>
								<?php echo $this->fd->html('form.dropdown', 'postcount', $defaultPostCount, $postCountOptions, ['attributes' => 'data-subscription-postcount']); ?>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('snackbar.standard', '<b>' . JText::_('COM_KT_SUBSCRIPTIONS_POSTS_HEADING') . '</b>', function() use ($subscriptions, $showActionBar) {
				ob_start();
				?>
				<div class="flex-shrink-0 flex-col md:flex-row md:order-2 w-full md:w-auto">
					<?php if ($subscriptions && $showActionBar) { ?>
					<div class="kt-subscriptions-action-group t-invisible" data-kt-subscriptions-actions>
						<div class="space-y-md md:space-y-no md:space-x-sm flex-col md:flex-row flex">
							<?php echo $this->fd->html('button.standard', 'Unsubscribe', 'default', 'sm', [
								'block' => true,
								'class' => '',
								'attributes' => 'data-kt-unsubscribe'
							]); ?>
						</div>
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
						<?php if ($subscriptions) { ?>
						<div class="flex-grow order-2 md:order-1 py-sm md:py-no w-full md:w-auto mt-sm mb-sm">
							<?php if ($showActionBar) { ?>
								<?php echo $this->fd->html('form.checkbox', 'kt-all-checked', false, 1, 'kt-subscriptions-check-all', JText::_('Select All'), [
									'attributes' => 'data-kt-subscriptions-checkall'
								]); ?>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="kt-db-comments divide-y divide-gray-300 mb-md <?php echo empty($subscriptions) ? 'is-empty' : ''; ?>">
					<?php if ($subscriptions) { ?>
						<?php foreach ($subscriptions as $sub) { ?>
						<div class="kt-db-comments__item 
						<?php echo $sub->isPublished() ? 'is-published' : '';?> 
						<?php echo $sub->isPending() ? 'is-pending' : '';?>" data-kt-subscriptions-item>
							<div class="kt-db-comment has-checkbox flex px-sm py-sm hover:no-underline text-gray-800">
								<div>
									<?php echo $this->fd->html('form.checkbox', 'cid[]', false, $sub->id, 'kt-comment-' . $sub->id, '', [
										'attributes' => 'data-kt-subscriptions-item-checkbox'
									]); ?>
								</div>

								<div class="flex-grow min-w-0 space-y-md">
									<div class="kt-db-comment-content">
										<div class="space-y-2xs md:space-y-no flex-grow">
											<div class="">
												<a href="<?php echo $sub->getItemPermalink(); ?>" target="_BLANK"><?php echo $sub->getItemTitle(); ?></a>
											</div>
										</div>
										<div class="kt-db-comment-content__ft">
											<div class="kt-db-comment-content-action">
												<div class="o-inline-list text-xs leading-sm">

													<div class="text-gray-500 flex-shrink-0" fd-breadcrumb="·">
														<?php echo $sub->getComponentTitle(); ?>
													</div>

													<div class="text-success kt-comment-published" fd-breadcrumb="·">
														<?php echo JText::_('COM_KOMENTO_PUBLISHED');?>
													</div>

													<div class="text-warning kt-comment-pending" fd-breadcrumb="·">
														<?php echo JText::_('COM_KOMENTO_PENDING');?>
													</div>

													<div class="text-gray-500 flex-shrink-0" fd-breadcrumb="·">
														<a href="javascript:void(0);" data-kt-subscriptions-unsubscribe>Unsubscribe</a>
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

					<?php echo $this->fd->html('html.emptyList', 'COM_KT_SUBSCRIPTIONS_NO_POSTED_YET', [
						'icon' => 'fdi fa fa-envelope',
						'class' => 'bg-gray-100 rounded-md mb-lg mt-lg'
					]); ?>
				</div>

				<?php if ($pagination) {?>
					<?php echo $pagination->getListFooter(true);?>
				<?php } ?>

				<?php echo $this->fd->html('form.hidden', 'return', $returnURL); ?>
				<?php echo $this->fd->html('form.action', '', 'subscriptions', 'subscriptions'); ?>
			</form>
		</div>
	</div>
</div>

