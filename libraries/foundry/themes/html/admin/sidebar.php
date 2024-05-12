<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="container-nav hidden" data-fd-container-nav>
	<a class="nav-sidebar-toggle" data-bp-toggle="collapse" data-target=".app-sidebar-collapse">
		<i class="fdi fa fa-bars"></i>
		<span><?php echo JText::_('FD_MENU');?></span>
	</a>
	<a class="nav-subhead-toggle" data-target=".subhead-collapse">
		<i class="fdi fa fa-cog"></i>
		<span><?php echo JText::_('FD_OPTIONS');?></span>
	</a>
</div>

<?php if (FH::isJoomla4()) { ?>
<div class="hidden" data-fd-j4-sidebar>
	<li class="item item-level-1">
		<a class="has-arrow" href="javascript:void(0);" aria-expanded="false" data-fd-back-extension>
			<span class="icon-">
				<img src="<?php echo rtrim(JURI::root(), '/');?>/media/<?php echo $this->fd->getComponentName();?>/images/<?php echo $this->fd->getName();?>-48x48.png" style="width: 18px;height: 18px;">
			</span>

			<span class="sidebar-item-title"><?php echo $this->fd->getExtensionTitle();?></span>
		</a>
	</li>
</div>
<?php } ?>

<div class="app-sidebar app-sidebar-collapse" data-fd-sidebar>
	<ul class="app-sidebar-nav">
		<?php if (FH::isJoomla4()) { ?>
		<li class="sidebar-item sidebar-item--joomla-4-btn">
			<a href="javascript:void(0);" data-fd-back-joomla>
				<i class="fdi fa fa-chevron-left"></i> <span class="app-sidebar-item-title"><?php echo JText::_('FD_BACK');?></span>
			</a>
		</li>
		<?php } ?>

		<?php foreach ($menus as $menu) { ?>
		<li class="sidebar-item
			<?php echo isset($menu->childs) && $menu->childs ? 'dropdown' : '';?>
			<?php echo $menu->isActive ? ' active' : '';?>"
			data-fd-sidebar-item
		>

			<a href="<?php echo $menu->childs ? 'javascript:void(0);' : $menu->link;?>" data-fd-sidebar-parent
				data-childs="<?php echo isset($menu->childs) && $menu->childs ? '1' : '0';?>"
			>
				<i class="<?php echo $menu->icon;?>"></i>&nbsp;<span class="sidebar-item-title"><?php echo JText::_($menu->title); ?></span>
				<span class="badge
				<?php echo !$menu->isActive && $menu->count > 0 ? 'block' : 'hidden';?>
				<?php echo $menu->isActive && $menu->childs ? 'hidden' : 'block';?>
				" data-fd-parent-badge>&nbsp;</span>
			</a>

			<?php if ($menu->childs) { ?>
			<ul class="dropdown-menu" role="menu">
				<?php foreach ($menu->childs as $child) { ?>
				<li class="<?php echo $child->isActive ? 'active' : '';?>">
					<a href="<?php echo $child->link;?>">
						<span class="app-sidebar-item-title"><?php echo JText::_($child->title);?></span>

						<?php if ($child->count) { ?>
						<span class="badge <?php echo $child->count > 0 ? 'has-counter block' : 'hidden';?>">&nbsp;</span>
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
