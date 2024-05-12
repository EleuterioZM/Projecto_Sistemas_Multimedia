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
<?php echo $this->fd->html('html.name', $name, [
	'permalink' => $permalink,
	'attributes' => $nofollow,
	'class' => $class
]); ?>

<?php if ($this->config->get('admin_label') && KT::isSiteAdmin($user->id)) { ?>
	<?php echo $this->fd->html('label.standard', 'COM_KT_ADMIN', 'primary', ['rounded' => false]); ?>
<?php } ?>

<?php if ($applicationAuthorId && $user->id == $applicationAuthorId && $this->config->get('author_label')) { ?>
	<?php echo $this->fd->html('label.standard', 'COM_KT_AUTHOR', 'success', ['rounded' => false]); ?>
<?php } ?>