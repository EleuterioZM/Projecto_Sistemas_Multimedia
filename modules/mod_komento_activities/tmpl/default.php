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
defined('_JEXEC') or die('Restricted access');
?>
<div id="fd" class="fd-module is-kt">
	<div id="kt" class="mod-kt mod-kt-activities theme-layer <?php echo $config->get('layout_appearance'); ?> si-theme-<?php echo $config->get('layout_accent');?> <?php echo $params->get('moduleclass_sfx'); ?> <?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'is-rtl' : '';?>">
		<div class="fd-mod-list space-y-lg">
			<?php foreach ($activities as $activity) { ?>
			<div class="fd-mod-list__item <?php echo 'kmt-' . $activity->id; ?> <?php echo $comment->getCustomCss();?>">
				<div class="flex">
					<?php if ($params->get('showavatar', true)) { ?>
					<div class="flex-shrink-0 pr-md">
						<?php echo $activity->author->getAvatarHtml($activity->author->getName(), $activity->author->email, $activity->comment->url);?>
					</div>
					<?php } ?>

					<div class="flex-grow-1 space-y-2xs min-w-0 break-words">
						<div class="">
							<?php if ($activity->type === 'comment') { ?>
								<?php echo JText::sprintf('COM_KOMENTO_ACTIVITY_COMMENTED_ON', $activity->author->getName(), $activity->comment->getPermalink(), $activity->comment->itemTitle, ' text-bold'); ?>
							<?php } ?>

							<?php if ($activity->type === 'reply') { ?>
								<?php echo JText::sprintf('COM_KOMENTO_ACTIVITY_REPLIED_TO', $activity->author->getName(), $activity->comment->getPermalink(), $activity->comment->parent_id, 'parentLink  text-bold', $activity->comment->itemTitle); ?>
							<?php } ?>

							<?php if ($activity->type === 'like') { ?>
								<?php echo JText::sprintf('COM_KOMENTO_ACTIVITY_LIKED_ON', $activity->author->getName(), $activity->comment->getPermalink(), $activity->comment->itemTitle, ' text-bold'); ?>
							<?php } ?>
						</div>

						<?php if ($params->get('showcomment')) { ?>
						<div class=" text-gray-500">
							<?php echo $activity->comment->getContent($params->get('maxcommentlength')); ?>
						</div>
						<?php } ?>

						<div class="o-inline-list">
							<div>
								<a href="<?php echo $activity->comment->getPermalink(); ?>" class="fd-mod-link text-xs text-gray-500">
									<?php echo $fd->html('icon.font', 'fa fa-clock mr-3xs fa-fw fa-sm'); ?>
									<?php echo $themes->html('html.date', $activity->comment->created);?>
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