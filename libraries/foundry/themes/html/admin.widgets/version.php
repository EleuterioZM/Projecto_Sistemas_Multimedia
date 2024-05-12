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
<div class="panel" data-fd-panel-version data-url="<?php echo $apiUrl; ?>" data-key="<?php echo $apiKey; ?>" data-installed="<?php echo $installedVersion; ?>">
	<div class="db-version">
		<div>
			<i class="fdi fa fa-ellipsis-h text-gray-500 db-version__info-icon" data-info-icon style=""></i>
		</div>
		<div class="checking-updates" data-info-body>
			<b class="checking">
				<i class="fdi fa fa-circle-o-notch fa-spin"></i> <?php echo JText::_('FD_CHECKING_FOR_UPDATES'); ?>
			</b>
			<b class="latest">
				<?php echo JText::_('FD_SOFTWARE_IS_UP_TO_DATE'); ?>
			</b>
			<b class="requires-updating">
				<div class="flex">
					<div class="flex-grow">
						<?php echo JText::_('FD_REQUIRES_UPDATING'); ?>
					</div>


					<?php echo $this->fd->html('button.link', $updateTaskUrl, 'FD_UPDATE_NOW', 'primary', 'default', [
						'attributes' => 'data-fd-update-button',
						'icon' => 'fdi fa fa-cloud-download-alt',
						'class' => 'ml-sm'
					]); ?>
				</div>

			</b>
			<div class="versions-meta">
				<div class="text-gray-500 local-version"><?php echo JText::_('FD_INSTALLED_VERSION'); ?>: <span data-local></span></div>
				<div class="text-gray-500 latest-version"><?php echo JText::_('FD_LATEST_VERSION'); ?>: <span data-online></span></div>
			</div>
		</div>
	</div>
</div>