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
<div class="kt-login">
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login');?>" method="post" data-kt-login-form="<?php echo $uniqid; ?>">
		<div class="grid md:grid-cols-2 gap-md">
			<div>
				<?php echo $this->fd->html('form.floatinglabel', $usernameField, 'username'); ?>
			</div>

			<div class="kt-login__cell-pass">
				<?php echo $this->fd->html('form.floatinglabel', 'COM_KOMENTO_LOGIN_PASSWORD', 'password', 'password'); ?>
			</div>
		</div>

		<div class="mt-sm text-right">
			<?php echo $this->fd->html('button.standard', JText::_('COM_KT_LOGIN_TO_MY_ACCOUNT') . ' &rarr;', 'default', 'sm', ['attributes' => 'data-kt-login-submit="' . $uniqid . '"']); ?>
		</div>

		<input type="hidden" value="com_users"  name="option">
		<input type="hidden" value="user.login" name="task">
		<input type="hidden" name="return" value="<?php echo $returnURL; ?>" />
		<?php echo $this->fd->html('form.token'); ?>
	</form>
</div>
