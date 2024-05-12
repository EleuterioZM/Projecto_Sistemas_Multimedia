<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($comments) { ?>
	<div class="divide-y divide-solid divide-gray-200">
		<?php foreach ($comments as $comment) { ?>
			<div class="py-sm leading-sm">
				<div class="flex overflow-hidden">
					<div class="flex-grow min-w-0 overflow-hidden truncate whitespace-nowrap">
						<a class="fd-link overflow-hidden truncate whitespace-nowrap text-sm font-bold" href="<?php echo $comment->authorLink;?>">
							<b><?php echo $comment->authorName;?></b>
						</a>
						<?php echo JText::_('FD_POSTED_COMMENT_IN'); ?>
						<b>
							<a class="fd-link" href="<?php echo $comment->permalink;?>" target="_blank">
								<?php echo $comment->itemTitle;?>
							</a>
						</b>
					</div>
					<div class="text-gray-500 ml-auto flex-shrink-0 pl-md">
						<i class="fdi far fa-clock"></i>&nbsp; <?php echo $this->fd->html('str.date', $comment->created, JText::_('Y-m-d H:i'));?>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>

<?php if (!$comments) { ?>
	<div class="o-empty block">
	<div class="o-empty__content">
		<div class="o-empty__text">
		<?php echo JText::_($emptyText);?>
		</div>
	</div>
</div>
<?php } ?>
