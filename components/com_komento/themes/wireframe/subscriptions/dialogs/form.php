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
<dialog>
	<width>500</width>
	<height>380</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{submit}": "[data-submit-button]",
		"{name}": "[data-subscribe-name]",
		"{email}": "[data-subscribe-email]",
		"{userId}": "[data-subscribe-userid]",
		"{form}": "[data-subscribe-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_SUBSCRIBE_DIALOG_TITLE'); ?></title>
	<content>
		<p class="mb-md"><?php echo JText::_('COM_KOMENTO_FORM_SUBSCRIBE_ENTER_DETAILS'); ?></p>

		<form action="<?php echo JRoute::_('index.php');?>" method="post" class="space-y-md" data-subscribe-form>
			<div class="o-form-group">
				<label for="subscribe-name" class="o-form-label"><?php echo JText::_('COM_KOMENTO_FORM_NAME'); ?></label>
				<div class="o-control-input">
					<input type="text" name="name" id="subscribe-name" value="<?php echo $this->my->getName();?>" class="o-form-control" <?php echo $this->my->id ? 'disabled="true"' : '';?> data-subscribe-name />
				</div>
			</div>

			<div class="o-form-group">
				<label for="subscribe-email" class="o-form-label"><?php echo JText::_('COM_KOMENTO_FORM_EMAIL'); ?></label>
				<div class="o-control-input">
					<input type="text" name="email" id="subscribe-email" value="<?php echo $this->my->email;?>" class="o-form-control" <?php echo $this->my->id ? 'disabled="true"' : '';?> data-subscribe-email />
				</div>
			</div>

			<?php if ($this->config->get('email_digest_enabled') && !$defaultInterval) { ?>
				<div class="o-form-group">
					<label for="subscribe-email" class="o-form-label"><?php echo JText::_('COM_KT_SUBSCRIPTIONS_INTERVAL'); ?></label>
					<div class="o-control-input">
						<?php echo $this->fd->html('form.dropdown', 'interval', $this->config->get('email_digest_interval', 'instant'), $intervalOptions, ['attributes' => 'data-subscription-interval']); ?>
					</div>
				</div>
			<?php } ?>

			<?php if ($this->config->get('email_digest_enabled') && $defaultInterval) { ?>
				<input type="hidden" name="interval" value="<?php echo $defaultInterval; ?>" />
			<?php } ?>
			<input type="hidden" name="component" value="<?php echo $component; ?>" />
			<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
			<input type="hidden" name="userId" value="<?php echo $this->my->id; ?>" data-subscribe-userid />

			<?php echo $this->fd->html('form.returnUrl', ''); ?>
			<?php echo $this->fd->html('form.action', 'subscribe', 'subscriptions'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_KOMENTO_FORM_SUBSCRIBE', 'primary'); ?>
	</buttons>
</dialog>
