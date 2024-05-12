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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-8 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_EDITING_COMMENT'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_NAME', 'name', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'name', $comment->name, 'name'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_EMAIL', 'email', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.email', 'email', $comment->email, 'email'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_WEBSITE', 'url', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'url', $comment->url, 'url'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_ARTICLEID', 'cid', '', '', true, ['columns' => 4]); ?>

						<div class="col-md-9">
							<?php echo $this->fd->html('form.text', 'cid', $comment->cid, 'cid'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_TEXT', 'comment', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.textarea', 'comment', $comment->comment, 'comment', ['rows' => 8]); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_IP', 'ip', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'ip', $comment->ip, 'ip'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_LATITUDE', 'latitude', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'latitude', $comment->latitude, 'latitude'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_LONGITUDE', 'longitude', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'longitude', $comment->longitude, 'longitude'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_ADDRESS', 'address', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'address', $comment->address, 'address'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-1 md:col-span-4 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_COMMENT_PUBLISHING_OPTIONS'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_AUTHOR', 'created_by', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'created_by', $comment->getAuthorName(), 'created_by', ['attributes' => 'disabled="disabled"']); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_CREATED_DATE', 'created', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.datetimepicker', 'created', $comment->getCreatedDate()->format('m/d/Y h:i A'));?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_PUBLISHED', 'published', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.toggler', 'published', $comment->published, 'published'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_COMMENT_FEATURED', 'sticked', '', '', true, ['columns' => 4]); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.toggler', 'sticked', $comment->sticked, 'sticked'); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?php echo $this->fd->html('form.action', 'comments', 'comments'); ?>
	<?php echo $this->fd->html('form.hidden', 'id', (int) $comment->id); ?>
	<?php echo $this->fd->html('form.hidden', 'return', $return); ?>
</form>
