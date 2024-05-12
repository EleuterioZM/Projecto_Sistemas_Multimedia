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

<div class="kt-form-section " data-kt-form>
	<a name="commentform" id="commentform" hidden></a>

	<?php echo $this->fd->html('snackbar.standard', '<b>' . JText::_('COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS') . '</b>'); ?>

	<?php if (isset($options['lock']) && $options['lock']) { ?>
		<?php echo $this->fd->html('layout.box', JText::_('COM_KOMENTO_FORM_LOCKED'), 'fdi fa fa-lock text-gray-500', null, [
			'rounded' => true
		]); ?>
	<?php } ?>

	<?php if (!isset($options['lock']) || !$options['lock']) { ?>

		<?php if ($this->my->allow('add_comment') || ($this->my->guest && $this->config->get('enable_login_form'))) { ?>
		<div class="formArea kmt-form-area">

			<?php if ($this->my->allow('add_comment')) { ?>
			<div class="kt-form-header">
				<div class="flex items-center">
					<div class="flex-shrink-0 pr-sm">
						<?php echo $this->html('html.avatar', $this->my); ?>
					</div>
					<div class="flex-grow">
						<div class="flex items-center leading-sm text-sm">
							<div class="">
								<?php if ($this->my->guest) { ?>
								<span data-kt-post-as-guest>
									<?php echo JText::_('COM_KT_POSTING_AS_GUEST');?>
								</span>
								<?php } else { ?>
									<?php echo JText::sprintf('COM_KT_POSTING_AS', $this->html('html.name', $this->my->id, '', '', '', null, [
										'class' => 'no-underline'
									]));?>
								<?php } ?>
							</div>

							<?php if ($this->my->guest && $this->config->get('enable_login_form')) { ?>
							<div class="pl-xs">
								<span class="border-l border-solid border-gray-300 pr-xs"></span>
								<a href="javascript:void(0);" data-kt-login>
									<?php echo JText::_('COM_KT_LOGIN'); ?>
								</a>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->my->guest && $this->config->get('enable_login_form')) { ?>
			<div class="<?php echo $this->my->allow('add_comment') ? 't-hidden' : '';?>" data-kt-login-form>
				<?php echo KT::login()->getLoginForm($returnURL);?>
			</div>
			<?php } ?>

			<?php if ($this->my->allow('add_comment')) { ?>
				<form class="kt-form space-y-sm mb-md" data-kt-form-element>

					<?php if ($showHeaders) { ?>
					<div class="kt-user-info grid md:grid-cols-<?php echo $totalFields;?> gap-md" data-kt-userinfo>
						<?php if ($showNameField) { ?>
						<div class="kt-form-name">
							<?php echo $this->fd->html('form.floatingLabel', JText::_('COM_KOMENTO_FORM_NAME') . ($requireNameField ? ' (' . JText::_('COM_KOMENTO_FORM_REQUIRED') . ')' : ''), 'name', 'text', !$this->my->guest ? FH::escape($this->my->name) : ''); ?>
						</div>
						<?php } ?>

						<?php if ($showEmailField) { ?>
						<div class="kt-form-email">
							<?php echo $this->fd->html('form.floatinglabel', JText::_('COM_KOMENTO_FORM_EMAIL') . ($requireEmailField ? ' (' . JText::_('COM_KOMENTO_FORM_REQUIRED') . ')' : ''), 'email', 'text', !$this->my->guest ? FH::escape($this->my->email) : ''); ?>
						</div>
						<?php } ?>

						<?php if ($showWebsiteField) { ?>
						<div class="kt-form-website">
							<?php echo $this->fd->html('form.floatinglabel', JText::_('COM_KOMENTO_FORM_WEBSITE') . ($requireWebsiteField ? ' (' . JText::_('COM_KOMENTO_FORM_REQUIRED') . ')' : ''), 'url'); ?>
						</div>
						<?php } ?>
					</div>
					<?php } ?>

					<?php echo $this->html('form.honeypot'); ?>

					<?php echo $this->fd->html('alert.standard', '', 'danger', [
						'customClass' => 't-hidden',
						'attributes' => 'data-kt-alert'
					]); ?>

					<div class="kt-form-composer">
						<?php echo $this->output('site/form/editor', ['isEdit' => false]); ?>
					</div>

					<?php if ($showCaptcha) { ?>
					<div class="kt-form-captcha">
						<?php echo KT::captcha()->html(); ?>
					</div>
					<?php } ?>

					<div class="kt-form-submit">
						<div class="kt-form-submit__cell">
							<?php if ($showSubscribe && !$subscriptionId) { ?>
							<div class="subscribeForm kmt-form-subscription">
								<?php echo $this->fd->html('form.checkbox', 'subscribe', false, 1, 'subscribe-comments', 'COM_KOMENTO_FORM_ALSO_SUBSCRIBE_TO_THIS_THREAD'); ?>
							</div>
							<?php } ?>

							<?php if ($showTerms) { ?>
							<div class="mt-xs">
								<?php echo $this->fd->html('form.checkbox', 'tnc', false, 1, 'kt-terms', JText::sprintf('COM_KT_AGREE_TO_TNC', '<a href="javascript:void(0);" data-kt-tnc-view>' . JText::_('COM_KOMENTO_FORM_READ_TNC') . '</a>'), [
									'attributes' => 'data-kt-terms'
								]); ?>
							</div>
							<?php } ?>
						</div>

						<div class="kt-form-submit__cell space-x-sm mt-lg md:mt-no">
							<?php echo $this->fd->html('button.standard', 'COM_KOMENTO_FORM_CANCEL', 'default', 'default', [
								'attributes' => 'data-kt-cancel',
								'class' => 'btn-kt-cancel'
							]); ?>

							<?php echo $this->fd->html('button.standard', 'COM_KOMENTO_FORM_SUBMIT', 'primary', 'primary', [
								'attributes' => 'data-kt-submit'
							]); ?>
						</div>
					</div>

					<input type="hidden" name="parent_id" value="0" data-kt-parent />
					<input type="hidden" name="task" value="commentSave" />
					<input type="hidden" name="pageItemId" class="pageItemId" value="<?php echo $this->input->get('Itemid', 0, 'int'); ?>" />
				</form>
			<?php } ?>
		</div>
		<?php } ?>
	<?php } ?>
</div>