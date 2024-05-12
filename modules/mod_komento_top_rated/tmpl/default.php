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
	<div id="kt" class="mod-kt mod-kt-top-rated-article theme-layer <?php echo $config->get('layout_appearance'); ?> si-theme-<?php echo $config->get('layout_accent'); ?> <?php echo $params->get('moduleclass_sfx'); ?> <?php echo JFactory::getDocument()->getDirection() === 'rtl' ? 'is-rtl' : '';?>">
		<div class="fd-mod-list space-y-md">
			<?php foreach ($articles as $article) { ?>
			<div class="fd-mod-list__item <?php echo 'kmt-' . $article->component . ' kmt-article-' . $article->cid; ?>">
				<div class="pb-2xs">
					<a href="<?php echo $article->permalink;?>" class="fd-mod-title font-bold"><?php echo $article->title;?></a>
				</div>

				<div class="o-inline-list">
					<?php if ($params->get('showComponent', true)) { ?>
					<div>
						<a href="javascript:void(0);" class="fd-mod-link text-xs text-gray-500" title="<?php echo JText::_('MOD_KT_TOP_RATED_COMPONENT');?>">
							<?php echo $fd->html('icon.font', 'fa fa-cube mr-3xs fa-fw fa-sm'); ?>

							<?php echo $article->componentName;?>
						</a>
					</div>
					<?php } ?>

					<?php if ($params->get('showTotalRating', true)) { ?>
					<div>
						<span class="text-xs text-gray-500" title="<?php echo JText::_('MOD_KT_TOP_RATED_AVERAGE_RATING');?>">
							<?php echo $fd->html('icon.font', 'fa fa-star mr-3xs fa-fw fa-sm'); ?>

							<?php echo $article->avgRating;?>
						</span>
					</div>
					<?php } ?>

					<?php if ($params->get('showTotalVoters', true)) { ?>
					<div>
						<span class="text-xs text-gray-500" title="<?php echo JText::_('MOD_KT_TOP_RATED_TOTAL_VOTERS');?>">
							<?php echo $fd->html('icon.font', 'fa fa-user mr-3xs fa-fw fa-sm'); ?>

							<?php echo $article->count;?>
						</span>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>