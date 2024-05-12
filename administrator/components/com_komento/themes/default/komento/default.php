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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-7 w-auto m-no">
		<?php echo $this->fd->html('adminWidgets.statistics', 'Statistics', 'Statistics of comments on the site', [
			(object) [
				'url' => 'index.php?option=com_komento&view=comments',
				'icon' => 'fdi fa fa-comments',
				'title' => 'COM_KOMENTO_DASHBOARD_COMMENTS',
				'count' => $totalComments
			],
			(object) [
				'url' => 'index.php?option=com_komento&view=comments&layout=pending',
				'icon' => 'fdi far fa-comment-dots',
				'title' => 'COM_KOMENTO_DASHBOARD_PENDING',
				'count' => $totalPending
			],
			(object) [
				'url' => 'index.php?option=com_komento&view=comments&layout=spamlist',
				'icon' => 'fdi fa fa-shield-virus',
				'title' => 'Spams Caught',
				'count' => $totalSpams
			],
			(object) [
				'url' => 'index.php?option=com_komento&view=comments&layout=reports',
				'icon' => 'fdi fa fa-exclamation-triangle',
				'title' => 'COM_KOMENTO_DASHBOARD_REPORTS',
				'count' => $totalReports
			],
			(object) [
				'url' => 'index.php?option=com_komento&view=subscribers',
				'icon' => 'fdi fa fa-bell',
				'title' => 'COM_KOMENTO_DASHBOARD_SUBSCRIBERS',
				'count' => $totalSubscribers
			],
			(object) [
				'url' => 'index.php?option=com_komento&view=mailq',
				'icon' => 'fdi far fa-envelope',
				'title' => 'Pending Mails',
				'count' => $totalPendingMails
			]
		]);?>

		<div class="db-activity">
			<?php echo $this->fd->html('tabs.render', [
				$this->fd->html('tabs.item', 'comments', 'COM_KOMENTO_LATEST', function() use ($comments) {
					echo $this->fd->html('adminwidgets.comments', $comments, 'COM_KOMENTO_COMMENTS_NO_COMMENT');
				}, true),

				$this->fd->html('tabs.item', 'pending', 'COM_KOMENTO_PENDING', function() use ($pendingComments) {
					echo $this->fd->html('adminwidgets.comments', $pendingComments, 'COM_KOMENTO_COMMENTS_NO_PENDING_COMMENTS');
				}),
			], 'line', 'horizontal', ['tabContentClass' => 'px-sm']); ?>
		</div>
	</div>
	
	<div class="col-span-1 md:col-span-5 w-auto m-no">
		<?php echo $this->fd->html('adminwidgets.version', $this->config->get('main_apikey'), $currentVersion, KOMENTO_SERVICE_VERSION, $updateTaskUrl); ?>

		<?php echo $this->fd->html('adminwidgets.news'); ?>
	</div>
</div>


