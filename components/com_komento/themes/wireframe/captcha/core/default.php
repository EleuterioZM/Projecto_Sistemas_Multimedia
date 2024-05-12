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
<div class="kt-captcha">
	<div class="kt-captcha__title">
		<?php echo JText::_('COM_KOMENTO_COMMENT_CAPTCHA_DESC'); ?>	
	</div>

	<div class="flex flex-col md:flex-row space-y-sm md:space-y-no md:space-x-xs">
		<div class="flex-shrink-0">
			<div class="kt-captcha__form">
				<div class="kt-captcha__img">
					<img src="<?php echo $url;?>" data-kt-captcha-image />					
				</div>
				
				<div class="kt-captcha__reload">
					<?php echo $this->fd->html('button.link', null, $this->fd->html('icon.font', 'fdi fa fa-redo'), 'default', 'default', [
						'iconOnly' => true,
						'attributes' => 'data-kt-captcha-reload'
					]); ?>
				</div>
			</div>
		</div>

		<div class="kt-captcha__input">
			<?php echo $this->fd->html('form.text', 'captchaResponse', '', 'captcha-response', [
				'attributes' => 'maxlength="5" data-kt-captcha-response',
				'class' => 'text-center'
			]); ?>
		</div>
	</div>	
	<?php echo $this->fd->html('form.hidden', 'captchaId', FH::escape($id), 'captcha-id', ['data-kt-captcha-id']); ?>
</div>

