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

$pinned = isset($pinned) ? $pinned : false;
$indentStyling = $comment->getIndentStyling();
?>
<div class="kt-comments__item <?php echo $comment->getCustomCss();?> 
		<?php echo $comment->isFeatured() ? 'is-featured' : '';?> 
		<?php echo $comment->isEdited() ? 'is-edited' : '';?> 
		<?php echo $comment->isParent() ? 'is-parent' : 'is-child';?> 
		<?php echo $comment->isPending() ? 'is-pending' : '';?>
		<?php if ($this->config->get('enable_minimize')) { ?>
			<?php echo $comment->isMinimized() ? 'is-minimized' : '';?>
			<?php echo !$comment->isMinimized() ? 'can-minimize' : 'can-expand';?>
		<?php } ?>
		"
		data-kt-comment-item
		data-pinned="<?php echo $pinned; ?>"
		data-id="<?php echo $comment->id;?>" 
		data-parentid="kmt-<?php echo $comment->parent_id; ?>"
		data-depth="<?php echo $comment->depth; ?>"
		itemscope itemtype="http://schema.org/Comment"
		style="<?php echo $indentStyling;?>">

	<div class="kt-comment">
		<div class="flex">
			<div class="flex-shrink-0 pr-md">
				<?php echo $this->html('html.avatar', $comment->created_by, $comment->getAuthorName(), $comment->getAuthorEmail(), $comment->url); ?>
			</div>
			<div class="flex-grow space-y-md min-w-0 break-words">
				<div class="kt-comment__hd">
					<?php if (!$pinned) { ?>
						<a id="comment-<?php echo $comment->id;?>"></a>
					<?php } ?>
					<div class="flex">
						<div class="space-y-2xs md:space-y-no flex-grow">

							<div class="kt-reply-to text-sm" <?php if ($comment->parent_id != 0) { ?>
									data-fd-tooltip
									data-fd-tooltip-title="<?php echo JText::sprintf('COM_KOMENTO_REPLYING_TO', $comment->getParent()->getAuthorName());?>"
									data-fd-tooltip-placement="top"
									<?php } ?>>

								<?php echo $this->html('html.name', $comment->created_by, $comment->getAuthorName(), $comment->getAuthorEmail(), $comment->url, $application, ['class' => 'text-sm font-bold text-gray-800']); ?>

								<?php if ($comment->parent_id != 0) { ?>
								&nbsp;<?php echo $this->fd->html('icon.font', 'fdi fa fa-caret-right'); ?>&nbsp; 

								<a href="<?php echo FH::escape($comment->getParentAuthorLink()); ?>">
									<?php echo $comment->getParent()->getAuthorName();?>
								</a>
								<?php } ?>
							</div>

							<div class="o-inline-list">
								<div class="">
									<div class="kt-comment-date text-gray-500 text-xs">
										<time itemprop="dateCreated" datetime="<?php echo FH::date($comment->created)->format('c'); ?>">
											<?php echo $this->html('html.date', $comment->created);?>
										</time>
										<time class="hidden" itemprop="datePublished" datetime="<?php echo FH::date($comment->publish_up)->format('c'); ?>"></time>
									</div>
								</div>
								
								<div fd-breadcrumb="·">
									<div class="kt-comment-permalink">
										<a href="<?php echo $comment->getPermalink();?>" title="<?php echo JText::_('COM_KOMENTO_COMMENT_PERMALINK');?>" data-kt-permalink class="text-gray-500 text-xs no-underline">
											#<?php echo $comment->id;?>
										</a>
									</div>
								</div>
								
							</div>
							
						</div>
						<div class="flex-shrink-0">
							<a href="javascript:void(0);" class="kt-expand-label-wrap" data-kt-user-expand-comment data-kt-provide="tooltip" data-title="<?php echo JText::_('COM_KT_EXPAND_COMMENT');?>">
								<?php echo $this->fd->html('icon.font', 'fdi fa fa-angle-double-down'); ?>
							</a>

							<?php echo $this->html('comment.admin', $comment); ?>
						</div>
					</div>
					<div class="kt-comment-minimize">
						<span><?php echo JText::_('COM_KT_COMMENT_MINIMIZED_BY_MODERATOR'); ?></span>
						
					</div>
				</div>

				<div class="kt-comment__bd space-y-md">
					<div class="kt-comment-content">
						<div class="kt-comment-content__bd space-y-sm" itemprop="text">
							<div class="kt-comment-message" data-kt-comment-content>
								<?php if ($this->config->get('comment_enable_truncation')) { ?>
									<?php echo $this->fd->html('str.truncate', $comment->getContent(), $this->config->get('comment_truncation_length')); ?>
								<?php } else { ?>
									<?php echo $comment->getContent(); ?>
								<?php } ?>
							</div>

							<?php if ($this->config->get('enable_info')) { ?>
							<span class="kt-edited-info" data-kt-comment-edited>
								
									<?php if ($comment->isEdited()) { ?>
										<?php echo JText::sprintf('COM_KOMENTO_COMMENT_EDITTED_BY', $comment->getModifiedDate()->toLapsed(), $this->html('html.name', $comment->modified_by, $comment->getAuthorName(), $comment->getAuthorEmail(), $comment->url, $application)); ?>
									<?php } ?>
							</span>

							<span class="t-hidden" itemprop="creator" itemscope itemtype="https://schema.org/Person">
								<span itemprop="name"><?php echo $comment->getAuthorName(); ?></span>
							</span>

							<time class="t-hidden" itemprop="dateModified" datetime="<?php echo FH::date($comment->modified)->format('c'); ?>"></time>
							<?php } ?>

							<?php if ($this->config->get('upload_enable')) { ?>
								<div class="kt-editor-attachments" data-kt-attachment-wrapper>
									<?php echo $this->output('site/comments/attachments', [
										'comment' => $comment, 
										'attachments' => $comment->getAttachments()
									]); ?>
								</div>
							<?php } ?>

							<?php if ($this->config->get('enable_location') && $comment->hasLocation()) { ?>
							<div class="kt-location text-xs leading-sm" data-kt-location-wrapper>
								<?php echo $this->output('site/comments/location', array('comment' => $comment)); ?>
							</div>
							<?php } ?>
						</div>
					</div>
					
				</div>
				<?php if (KT::likes()->isEnabled() || (!$pinned && ($comment->canReplyTo() || $comment->canReport())) || ($this->config->get('enable_ratings') && $comment->ratings) || ($comment->isFeatured() && $comment->childs > 0)) { ?>
					<div class="kt-comment__ft space-y-md" data-comment-footer>
						<div class="kt-comment-content-action space-y-xs">
							<div class="flex flex-col md:flex-row">
								<div class="flex-grow">
									
									<div class="o-inline-list">
										<?php if (!$pinned) { ?>
											<?php if ($comment->canReplyTo()) { ?>
												<div fd-breadcrumb="·" class="text-gray-500">
													<div class="kt-reply-wrap">
														<a href="javascript:void(0);" class="text-xs text-gray-500 font-bold" data-kt-reply><?php echo JText::_('COM_KOMENTO_COMMENT_REPLY'); ?></a>
													</div>
												</div>
											<?php } ?>
											<?php if ($comment->canReport()) { ?>
												<div fd-breadcrumb="·" class="text-gray-500">
													<div class="kt-report-wrap">
														<a href="javascript:void(0);" class="text-xs text-gray-500 font-bold" data-kt-report>
															<?php echo $comment->isReport() ? JText::_('COM_KOMENTO_COMMENT_REPORTED') : JText::_('COM_KOMENTO_COMMENT_REPORT'); ?>
														</a>
													</div>
												</div>
											<?php } ?>
										<?php } ?>

										<?php if (KT::likes()->isEnabled()) { ?>
											<div fd-breadcrumb="·" class="text-gray-500">
												<div class="kt-like-wrap text-xs">
													<div class="relative">
														<a class="dropdown-toggle_ no-underline <?php echo $comment->liked ? 'text-success-500' : 'text-gray-500'; ?>" 
															href="javascript:void(0);"
															data-kt-likes-wrapper
															data-commentid="<?php echo $comment->id; ?>"
															data-action="likes">
															<i class="fdi far fa-thumbs-up fa-fw mr-3xs" data-kt-likes-action data-type="<?php echo $comment->liked ? 'unlike' : 'like'; ?>"></i> 
															<span data-kt-likes-counter><?php echo $comment->likes; ?></span>
														</a>

														<div class="t-hidden">
															<div id="fd" data-fd-popover-block data-appearance="<?php echo $this->config->get('layout_appearance');?>">
																<div class="<?php echo $this->config->get('layout_appearance');?> si-theme-<?php echo $this->config->get('layout_accent');?>">
																	<div class="o-dropdown divide-y divide-gray-200 w-[320px] md:w-[320px]" data-fd-dropdown-wrapper>
																		<div class="o-dropdown__bd overflow-y-auto max-h-[380px] px-md py-md" data-fd-dropdown-body>
																			<div class="px-sm py-sm hover:no-underline text-gray-800">
																				<?php echo $this->fd->html('placeholder.standard', 'rounded'); ?>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<?php if (!JFactory::getUser()->guest) { ?>
															<div class="dropdown-menu dropdown-menu-left dropdown-menu--avatar-list">
																<div data-kt-likes-browser-contents>
																</div>
															</div>
														<?php } ?>
													</div>
												</div>
											</div>

											<div fd-breadcrumb="·" class="text-gray-500">
												<div class="kt-like-wrap text-xs">
													<div class="relative">
														<a class="dropdown-toggle_ no-underline <?php echo $comment->disliked ? 'text-danger-500' : 'text-gray-500'; ?>" 
															href="javascript:void(0);"
															data-kt-dislikes-wrapper
															data-commentid="<?php echo $comment->id; ?>"
															data-action="dislikes">
															<i class="fdi far fa-thumbs-down fa-fw mr-3xs" data-kt-dislikes-action data-type="<?php echo $comment->disliked ? 'removedislike' : 'dislike'; ?>"></i> 
															<span data-kt-dislikes-counter><?php echo $comment->dislikes; ?></span>
														</a>

														<div class="t-hidden">
															<div id="fd" data-fd-popover-block data-appearance="<?php echo $this->config->get('layout_appearance');?>">
																<div class="<?php echo $this->config->get('layout_appearance');?> si-theme-<?php echo $this->config->get('layout_accent');?>">
																	<div class="o-dropdown divide-y divide-gray-200 w-[280px] md:w-[400px]" data-fd-dropdown-wrapper>
																		<div class="o-dropdown__bd xpy-sm xpx-xs overflow-y-auto max-h-[380px] divide-y divide-gray-200 space-y-smx" data-fd-dropdown-body>
																			<div class="px-sm py-sm hover:no-underline text-gray-800">
																				<?php echo $this->fd->html('placeholder.standard', 'rounded'); ?>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<?php if (!JFactory::getUser()->guest) { ?>
															<div class="dropdown-menu dropdown-menu-left dropdown-menu--avatar-list">
																<div data-kt-dislikes-browser-contents>
																</div>
															</div>
														<?php } ?>
													</div>
												</div>
											</div>

										<?php } ?>

									</div>
								</div>
								<?php if ($this->config->get('enable_ratings') && $comment->ratings) { ?>
									<div class="flex-shrink-0 pt-md md:pt-no">
										<div class="kt-ratings-wrap" data-kt-ratings-wrapper>
											<?php echo $this->fd->html('rating.item', [
												'score' => $comment->ratings,
												'showScore' => true
											]); ?>
										</div>
									</div>
								<?php }?>
							</div>

							<?php if ($comment->isFeatured() && $comment->childs > 0) { ?>
								<div class="flex flex-col md:flex-row">
									<div class="flex-grow">
										<a href="javascript:void(0);" data-kt-view-featured-replies><?php echo JText::_('COM_KT_SHOW_REPLIES'); ?></a>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>

			</div>
		</div>
	</div>
</div>

<?php if ($comment->childs > 0) { ?>
	<?php $replies = $comment->getReplies(); ?>

	<?php if ($replies) { ?>
		<div class="<?php echo $comment->isFeatured() ? 't-hidden' : ''; ?>" data-kt-replies-container>
			<?php if ($comment->childs > count($replies)) { ?>
				<div class="text-center" style="<?php echo $this->config->get('enable_threaded') ? 'margin-left:' . $this->config->get('thread_indentation'). 'px;' : ''; ?>" data-kt-comment-item data-kt-view-reply data-id="<?php echo $comment->id; ?>" data-rownumber="<?php echo $comment->rownumber; ?>">
					<a href="javascript:void(0);" class="kt-comment-view-all text-gray-500 flex items-center w-full justify-center no-underline mb-xs">
						<?php echo JText::sprintf('COM_KOMENTO_VIEW_OTHER_REPLIES', $comment->childs - count($replies)); ?>
						<?php echo $this->fd->html('icon.font', 'fdi fa fa-angle-down ml-2xs'); ?>
						<div class="o-loader o-loader--sm o-loader--inline"></div>
					</a>
				</div>
			<?php } ?>

			<div class="kt-comments" data-kt-replies-<?php echo $comment->id;?>>
				<?php foreach ($replies as $reply) { ?>
					<?php echo $this->output('site/comments/item', [
						'comment' => $reply, 
						'application' => $application, 
						'pinned' => $pinned
					]); ?>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
<?php } ?>
