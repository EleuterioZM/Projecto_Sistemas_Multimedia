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
<div class=" overflow-y-auto max-h-[280px] divide-y divide-gray-200" data-kt-liked-users-list>
	<?php if ($users) { ?>
		<?php foreach ($users as $user) { ?>
			<?php echo $this->output('site/comments/likes/name.item', ['user' => $user]); ?>
		<?php } ?>
	<?php } ?>
</div>

<?php if ($hasLoadMore) { ?>
<div class="pt-sm pl-no">
	<?php echo $this->fd->html('button.standard', 'COM_KT_LIKED_USERS_LOAD_MORE_BUTTON', 'default', 'xs', [
		'attributes' => 'data-limit-start="' . $limitstart .'" data-kt-liked-users-loadmore',
		'class' => 'text-xs text-gray-500 font-bold'
	]); ?>
</div>
<?php } ?>

<?php echo $this->fd->html('html.emptyList', JText::_('COM_KT_EMPTY_USER_' . strtoupper($type)), [
	'icon' => 'fa fa fa-user', 
	'class' => (!$users) ? 'block' : '', 
	'attributes' => 'data-fd-empty'
]); ?>