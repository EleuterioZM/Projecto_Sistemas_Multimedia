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
<div id="kt" class="kt-frontend theme-<?php echo $this->config->get('layout_theme'); ?>" 
	data-kt-form-wrapper
	data-component="<?php echo $component;?>" 
	data-cid="<?php echo $cid;?>" 
	data-url="<?php echo base64_encode(FH::getURI(true));?>"
>
	<?php if (!$this->my->allow('read_comment') && !$this->my->allow('add_comment')) { ?>
		<?php echo $this->fd->html('layout.box', JText::_('COM_KOMENTO_NOT_ALLOWED_TO_VIEW_COMMENTS'), 'fdi fa fa-lock text-gray-500'); ?>

		<?php if ($this->my->guest && $this->config->get('enable_login_form')) { ?>
			<?php echo KT::login()->getLoginForm();?>
		<?php } ?>

	<?php } else { ?>
		<?php echo $this->output('site/form/default'); ?>
	<?php } ?>
</div>
