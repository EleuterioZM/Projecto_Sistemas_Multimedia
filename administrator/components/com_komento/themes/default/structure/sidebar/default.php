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
<div class="container-nav hidden">
	<a class="nav-sidebar-toggle" data-bp-toggle="collapse" data-target=".app-sidebar-collapse">
		<?php echo $this->fd->html('icon.font', 'fdi fa fa-bars'); ?>
		<span><?php echo JText::_('COM_EASYBLOG_MOBILE_MENU');?></span>
	</a>
	<a class="nav-subhead-toggle" data-bp-toggle="collapse" data-target=".subhead-collapse">
		<?php echo $this->fd->html('icon.font', 'fdi fa fa-cog'); ?>
		<span><?php echo JText::_('COM_EASYBLOG_MOBILE_OPTIONS');?></span>
	</a>
</div>

<div class="app-sidebar app-sidebar-collapse" data-sidebar>
	<ul class="app-sidebar-nav list-unstyled">

		<?php foreach ($menus as $menu) { ?>
			<li class="sidebar-item <?php echo isset($menu->childs) && $menu->childs ? 'dropdown' : '';?> <?php echo $menu->view == $view ? 'open active' : '';?>" data-sidebar-item>

				<?php if (isset($menu->childs) && $menu->childs) { ?>
				<a href="javascript:void(0);" class="dropdown-toggle_" data-sidebar-parent>
				<?php } else { ?>
				<a href="<?php echo $menu->link;?>">
				<?php } ?>

					<?php if (isset($menu->icon) && $menu->icon) { ?><i class="fa <?php echo $menu->icon;?>"></i><?php } ?><?php echo JText::_($menu->title);?>

					<?php if (isset($menu->counter) && $menu->counter) { ?>
					<span class="badge"><?php echo $menu->counter;?></span>
					<?php } ?>
				</a>

				<?php if (isset($menu->childs) && $menu->childs) { ?>
				<ul class="dropdown-menu" role="menu" data-sidebar-child>
					<?php foreach ($menu->childs as $child) { ?>
					<li class="childItem<?php echo $layout == $child->url->layout ? ' active' : '';?>">
						<a href="<?php echo $child->link;?>">
							<?php echo JText::_($child->title);?>

							<?php if (isset($child->counter) && $child->counter) { ?>
							<span class="badge"><?php echo $child->counter;?></span>
							<?php } ?>
						</a>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
</div>
