<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-avatar-list">
<?php if ($users) { ?>
	<?php foreach ($users as $user) { ?>
	<div class="o-avatar-list__item">
		<?php echo $this->html('html.avatar', $user); ?>
	</div>
	<?php } ?>
<?php } ?>
</div>

<?php if ($total > 10) { ?>
<a href="javascript:void(0);" class="dropdown-menu__view-all" data-kt-likes-view-all data-id="<?php echo $id;?>">View All</a>
<?php } ?>

<?php if (!$users) { ?>
<div><?php echo JText::_('COM_KOMENTO_COMMENT_NO_USER_LIKE_THIS'); ?></div>
<?php } ?>