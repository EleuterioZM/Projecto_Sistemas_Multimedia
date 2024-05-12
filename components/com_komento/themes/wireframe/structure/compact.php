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
<div class="kt-compact-scroll" data-kt-perfectscroll>

	<div class="kt-compact-listing-section space-y-sm" data-kt-compact-listing>
		<div class="kt-bar">
			<?php echo $this->fd->html('snackbar.standard', function() use ($commentCount) {
				ob_start();
				?>
				<b><?php echo JText::_('COM_KOMENTO_COMMENTS'); ?></b> <span class="text-gray-500">(<span data-kt-counter><?php echo $commentCount; ?></span>)</span>
				<?php
				$contents = ob_get_contents();
				ob_end_clean();

				return $contents;
			}, function() use ($totalRating, $totalRatingCount) {

				if (!$this->config->get('enable_ratings')) {
					return;
				}

				return [$this->fd->html('rating.overall', $totalRating, $totalRatingCount)];
			}); ?>
		</div>

		<div class="flex flex-col md:flex-row mt-lg flex-wrap gap-xs">
			<?php if ($showSubscribe || $showRss) { ?>
			<div class="flex-grow space-x-xs mb-md md:mb-no">
				<?php if ($showRss) { ?>
					<?php echo $this->fd->html('button.link', KT::router()->getFeedUrl($component, $cid), $this->fd->html('icon.font', 'fdi fa fa-rss-square text-gray-500 mr-xs') . ' RSS', 'default', 'sm', [], true); ?>
				<?php } ?>

				<?php if ($showSubscribe) { ?>
					<?php echo $this->html('comment.subscribe', $subscriptionId); ?>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('show_sort_buttons')) { ?>
			<div class="kt-sortable">
				<?php echo $this->html('comment.sorting', $activeSort); ?>
			</div>
			<?php } ?>
		</div>

		<div class="kt-comments-container" data-kt-comments-container>
			<?php if ($pinnedComments) { ?>
			<div class="kt-comments" data-kt-comments-pinned>
				<?php foreach ($pinnedComments as $pinnedComment) { ?>
					<?php echo $this->output('site/comments/item', ['comment' => $pinnedComment, 'pinned' => true, 'application' => $application]); ?>
				<?php } ?>
			</div>
			<hr class="kt-divider">
			<?php } ?>

			<div class="kt-comments <?php echo !$comments ? 'is-empty' : '';?>" data-kt-comments>
				<?php echo $this->fd->html('loader.standard', [
					'class' => 'mx-auto mt-lg mb-lg'
				]); ?>

				<?php if ($comments) { ?>
					<?php foreach ($comments as $comment) { ?>
						<?php echo $this->output('site/comments/item', ['comment' => $comment, 'application' => $application]); ?>
					<?php } ?>
				<?php } ?>

				<?php echo $this->fd->html('html.emptyList', 'COM_KOMENTO_NO_COMMENTS_POSTED_YET', [
					'icon' => 'fdi far fa-comments'
				]); ?>
			</div>

			<?php if ($showMoreButton) { ?>
				<?php echo $this->fd->html('button.link', '#cmt_' . $moreStartCount, 'COM_KOMENTO_LIST_LOAD_MORE', 'default', 'default', [
					'attributes' => 'data-kt-loadmore data-nextstart="' . $moreStartCount . '"',
					'class' => 'mt-lg mb-lg',
					'block' => true
				]); ?>
			<?php } ?>
		</div>
	</div>

	<div class="kt-compact-form-section <?php echo isset($hideForm) && $hideForm === true ? ' t-hiddenx' : ''; ?>" data-kt-compact-form>
		<?php echo $this->output('site/form/default'); ?>
	</div>
</div>
