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
		<div class="col-span-1 md:col-span-6 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SUBSCRIBER_DETAILS'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_SUBSCRIBER_EXTENSION', 'component'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.extensions', 'component', $subscriber->component, 'data-kt-extension'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_SUBSCRIBER_EXTENSION_ITEM_ID', 'cid'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'cid', $subscriber->cid, 'cid'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_SUBSCRIBER_NAME', 'fullname'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'fullname', $subscriber->fullname); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_SUBSCRIBER_EMAIL', 'email'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.email', 'email', $subscriber->email); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_PUBLISHED', 'published'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.toggler', 'published', $subscriber->published); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $subscriber->id;?>" />
	<?php echo $this->fd->html('form.action', 'save', 'subscribers'); ?>
</form>