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
<?php KT::trigger('onBeforeKomentoBox', ['component' => $component, 'cid' => $cid, 'system' => &$system, 'comments' => &$comments]); ?>

<a id="comments"></a>

<?php if ($componentHelper->getCommentAnchorId()) { ?>
	<a id="<?php echo $componentHelper->getCommentAnchorId(); ?>"></a>
<?php } ?>

<?php if (!$this->my->allow('read_comment') && !$this->my->allow('add_comment')) { ?>
	<?php echo $this->fd->html('layout.box', JText::_('COM_KOMENTO_NOT_ALLOWED_TO_VIEW_COMMENTS'), 'fdi fa fa-lock text-gray-500'); ?>

	<?php if ($this->my->guest && $this->config->get('enable_login_form')) { ?>
		<?php echo KT::login()->getLoginForm($returnURL);?>
	<?php } ?>
<?php } ?>

<?php if ($this->my->allow('read_comment') || $this->my->allow('add_comment')) { ?>

	<?php if ($this->config->get('enable_conversation_bar') && $authors) { ?>
	<div class="kt-participants">
		<?php echo $this->fd->html('snackbar.standard', '<b>' . JText::_('COM_KOMENTO_COMMENT_CONVERSATION_BAR_TITLE') . '</b>'); ?>

		<div class="mt-sm o-inline-list">
			<?php foreach ($authors->registered as $item) { ?>
			<div>
				<?php echo $this->html('html.avatar', $item->created_by); ?>
			</div>
			<?php } ?>

			<?php foreach ($authors->guest as $item) { ?>
			<div>
				<?php echo $this->html('html.avatar', $item->created_by); ?>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<div class="flex flex-col md:flex-row mt-lg">
		<div class="flex-grow space-x-xs mb-md md:mb-no">
			<?php if ($showRss) { ?>
				<?php echo $this->fd->html('button.link', KT::router()->getFeedUrl($component, $cid), $this->fd->html('icon.font', 'fdi fa fa-rss-square text-gray-500 mr-xs') . ' RSS', 'default', 'sm', [], true); ?>
			<?php } ?>

			<?php if ($showSubscribe) { ?>
				<?php echo $this->html('comment.subscribe', $subscriptionId); ?>
			<?php } ?>
		</div>

		<?php if ($this->config->get('show_sort_buttons')) { ?>
		<div class="kt-sortable">
			<?php echo $this->html('comment.sorting', $activeSort); ?>
		</div>
		<?php } ?>
	</div>

	<?php if (!$this->my->allow('read_comment')) { ?>
		<?php echo $this->fd->html('layout.box', JText::_('COM_KOMENTO_COMMENT_NOT_ALLOWED'), 'fdi fa fa-comment-slash text-gray-500'); ?>
	<?php }?>

	<?php if ($this->my->allow('read_comment')) { ?>
		
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

	
		<div class="kt-comments-container" data-kt-comments-container>
			<?php if ($pinnedComments) { ?>
				<?php echo $this->output('site/comments/featured', ['pinnedComments' => $pinnedComments, 'application' => $application]); ?>
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
				<?php echo $this->fd->html('button.link', $loadMoreLink, 'COM_KOMENTO_LIST_LOAD_MORE', 'default', 'default', [
					'attributes' => 'data-kt-loadmore data-nextstart="' . $moreStartCount . '"',
					'class' => 'mt-lg mb-lg',
					'block' => true
				]); ?>
			<?php } ?>
		</div>
	<?php } ?>

	<?php echo $this->output('site/form/default'); ?>
<?php } ?>