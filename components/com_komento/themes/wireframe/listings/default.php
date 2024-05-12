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
<?php KT::trigger('onBeforeKomentoBar', ['component' => $component, 'cid' => $cid, 'commentCount', &$commentCount]); ?>

<?php if ($showReadmore || $this->config->get('layout_frontpage_comment') || $this->config->get('layout_frontpage_hits') || $this->config->get('layout_frontpage_preview')) { ?>
<div id="fd" data-kt-structure="<?php echo $uniqid; ?>">
	<div class="<?php echo $this->config->get('layout_appearance'); ?> si-theme-<?php echo $this->config->get('layout_appearance'); ?>">
		<div id="kt">
			<?php if ($showReadmore || $this->config->get('layout_frontpage_comment') || $this->config->get('layout_frontpage_hits')) { ?>
			<div class="o-snackbar">
				<div class="flex flex-col flex-wrap <?php echo $this->config->get('layout_frontpage_alignment') === 'right' ? 'md:flex-row-reverse' : 'md:flex-row'; ?> items-center">
					<div class="flex-grow min-w-0 <?php echo $this->config->get('layout_frontpage_alignment') === 'right' ? 'text-right' : 'text-left'; ?>">
						<div class="o-inline-list">
							<?php if ($this->config->get('layout_frontpage_comment')) { ?>
							<div class="leading-md">
								<a <?php echo $instantComment ? 'data-kt-toggle' : ''; ?> href="<?php echo $adapter->getContentPermalink() . '#comments'; ?>" class="any-link font-bold text-gray-500 text-xs">
									<?php echo JText::sprintf(FH::pluralize($commentCount, 'COM_KT_FRONTPAGE_COMMENT', 'COM_KT_FRONTPAGE_COMMENT_PLURAL'), FH::toCurrencyFormat($commentCount)); ?>
								</a>
							</div>
							<?php } ?>

							<?php if ($this->config->get('layout_frontpage_hits')) { ?>
							<div fd-breadcrumb="·" class="leading-md">
								<a href="<?php echo $adapter->getContentPermalink();?>" title="<?php echo FH::escape($adapter->getContentTitle());?>" class="any-link font-bold text-gray-500 text-xs">
									<?php echo JText::sprintf(FH::pluralize($adapter->getContentHits(), 'COM_KT_FRONTPAGE_VIEWS', 'COM_KT_FRONTPAGE_VIEWS_PLURAL'), FH::toCurrencyFormat($adapter->getContentHits())); ?>
								</a>
							</div>
							<?php } ?>
							
							<?php if ($showReadmore) { ?>
							<div fd-breadcrumb="·" class="leading-md">
								<a href="<?php echo $adapter->getContentPermalink();?>" title="<?php echo FH::escape($adapter->getContentTitle());?>" class="any-link font-bold text-gray-500 text-xs">
									<?php echo JText::_('COM_KOMENTO_FRONTPAGE_READMORE');?>
								</a>
							</div>
							<?php } ?>
						</div>
					</div>

					<?php if ($this->config->get('layout_frontpage_ratings') && $this->config->get('enable_ratings')) { ?>
					<div class="">
						<?php echo $this->fd->html('rating.item', [
							'score' => $totalRating,
							'totalRates' => $totalRatingCount,
							'showScore' => true,
							'showTotalRatings' => true
						]); ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->config->get('layout_frontpage_preview') && $this->my->allow('read_comment') && $comments) { ?>
			<div class="mt-md kt-comments-listing space-y-xs">
				<?php foreach ($comments as $comment) { ?>
				<div class="kt-comments-listing__item">
					<div class="text-xs bg-gray-50 p-md rounded-md">
						<a href="<?php echo $comment->getPermalink();?>" class="font-bold text-xs  text-gray-800 "><?php echo $comment->getAuthorName();?></a> &mdash; 
						<?php echo $comment->getContent($this->config->get('preview_comment_length')); ?><?php echo JText::_('COM_KOMENTO_ELLIPSES');?>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php if ($instantComment) { ?>
		<?php echo $this->output('site/structure/container'); ?>
	<?php } ?>

	<?php echo $this->fd->html('html.tooltip', $this->config->get('layout_appearance'), $this->config->get('layout_accent')); ?>
</div>
<?php } ?>

<?php KT::trigger('onAfterKomentoBar', ['component' => $component, 'cid' => $cid, 'commentCount', &$commentCount]); ?>