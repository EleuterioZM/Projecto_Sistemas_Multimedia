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

<?php if ($showPreviousButton) { ?>
	<a class="loadMore kmt-btn-loadmore" href="<?php echo $contentLink; ?>#comments_<?php echo $startCount; ?>">
		<b><?php echo JText::_('COM_KOMENTO_LIST_LOAD_PREVIOUS'); ?></b>
	</a>
<?php } ?>

<div class="mainList kmt-fame-list-wrap kmt-tabs" loaded="1">
	<div class="commentList kmt-list-wrap commentList-<?php echo $cid; ?>">
		<ul class="kmt-list reset-list">
			<?php if ($comments) { ?>
				<?php foreach ($comments as $comment) { ?>
					<?php echo $this->output('site/comments/item', array('comment' => $comment)); ?>
				<?php } ?>
			<?php } else { ?>
				<li class="kmt-empty-comment">
					<?php echo JText::_('COM_KOMENTO_COMMENTS_NO_COMMENT'); ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>

<?php if ($showMoreButton) { ?>
	xxxxx
	<a class="loadMore kmt-btn-loadmore" href="<?php echo $contentLink; ?>#comments_<?php echo $moreStartCount; ?>">
		<b><?php echo JText::_('COM_KOMENTO_LIST_LOAD_MORE'); ?></b>
	</a>
<?php } ?>
