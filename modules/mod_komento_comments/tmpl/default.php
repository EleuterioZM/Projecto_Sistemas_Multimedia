<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div id="fd" class="fd-module is-kt">
	<div id="kt" class="mod-kt mod-kt-comments theme-layer <?php echo $config->get('layout_appearance'); ?> si-theme-<?php echo $config->get('layout_accent');?> <?php echo $params->get('moduleclass_sfx'); ?> <?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'is-rtl' : '';?>">
		<div class="fd-mod-list space-y-lg">
			<?php foreach ($tmpComments as $comment) { ?>
			<div class="fd-mod-list__item <?php echo 'kmt-' . $comment->id; ?> <?php echo $comment->getCustomCss();?>">
				<div class="flex">
					<?php if ($params->get('showavatar')) { ?>
					<div class="flex-shrink-0 pr-md">
						<?php echo $comment->getAuthor()->getAvatarHtml($comment->getAuthorName(), $comment->getAuthorEmail(), $comment->url);?>
					</div>
					<?php } ?>

					<div class="flex-grow-1 space-y-2xs min-w-0 break-words">
						<?php if ($params->get('showauthor')) { ?>
						<div class="">
							<?php echo JText::sprintf('COM_KOMENTO_POSTED_COMMENT_IN', $comment->getAuthor()->getName($comment->name), $comment->getPermalink(), $comment->getItemTitle(), ' text-bold');?>
						</div>
						<?php } ?>

						<div class=" text-gray-500">
							<?php echo $comment->getContent($maxCommentLength); ?>
						</div>

						<div class="o-inline-list">
							<?php if ($params->get('showcomponent')) { ?>
							<div>
								<span class="text-xs text-gray-500">
									<?php echo $fd->html('icon.font', 'fa fa-cube mr-3xs fa-fw fa-sm'); ?>
									<?php echo JText::sprintf('COM_KOMENTO_TITLE_IN_COMPONENT', $comment->getComponentTitle()); ?>
								</span>
							</div>
							<?php } ?>

							<div>
								<a href="<?php echo $comment->getPermalink(); ?>" alt="<?php echo JText::_('COM_KOMENTO_COMMENT_PERMANENT_LINK'); ?>" class="fd-mod-link text-xs text-gray-500" title="<?php echo JText::_('COM_KOMENTO_COMMENT_PERMANENT_LINK'); ?>">
									<?php echo $fd->html('icon.font', 'fa fa-clock mr-3xs fa-fw fa-sm'); ?>
									<?php echo $themes->html('html.date', $comment->created);?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>