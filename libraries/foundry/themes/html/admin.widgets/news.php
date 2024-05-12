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
<div class="panel" data-fd-panel-news>
	<div class="">
		<div class="flex">
			<div class="flex-grow">
				<b class="panel-head-title"><?php echo JText::_('StackIdeas Blog');?></b>
				<div class="panel-info"><?php echo JText::_('Recent updates and news from the team');?></div>
			</div>
		</div>
	</div>
	<div class="panel-body bg-white px-no pt-no m-no">
		<div class="space-y-md mb-md" data-result>
			<?php echo $this->fd->html('loader.block', [
				'class' => 'flex items-center',
				'loaderClass' => 'block',
			]); ?>
		</div>

		<div class="text-center mt-md">
			<a href="https://stackideas.com/blog" target="_blank" class="o-btn o-btn--primary block"><?php echo JText::_('View StackIdeas Blog');?></a>
		</div>

		<div data-template>
			<div class="db-post-item t-hidden">
				<div class="min-w-0 w-full">
					<div class="flex-grow min-w-0 pr-md">
						<div class="flex w-full items-start">
							<div class="mr-md order-1">
								<a class="rounded-md block overflow--hidden" href="javascript:void(0);" target="_blank" data-permalink>
									<img class="rounded-md" data-image width="80" align="right" />
								</a>
							</div>
							<div class="order-2 min-w-0 flex-1">
								<div class="l-stack l-spaces--xs">

									<div class="overflow-hidden truncate whitespace-nowrap text-md">
										<b><a href="javascript:void(0);" data-permalink target="_blank" class="fd-link" data-title></a></b>
									</div>

									<div class="text-gray-500">
										<i class="fdi far fa-calendar-alt"></i>&nbsp; <span data-date></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
