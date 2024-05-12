<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" autocomplete="off">

	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', $tabs); ?>

		<div class="tab-content">
			<?php $i = 0; ?>
			<?php foreach ($tabs as $tab) { ?>
			<div id="<?php echo $tab->id;?>" class="t-hidden <?php echo $tab->active ? 't-block' : '';?>">
				<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
					<div class="col-span-1 md:col-span-8 w-auto">
						<div class="panel">
							<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_ACL_SECTION_' . strtoupper($tab->id)); ?>

							<div class="panel-body">
								<?php foreach ($rulesets->{$tab->id} as $rule => $value) { ?>
								<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-acl-wrap>
									<?php echo $this->fd->html('form.label', 'COM_KOMENTO_ACL_RULE_' . strtoupper($rule), $rule); ?>

									<div class="flex-grow">
										<?php echo $this->fd->html('form.toggler', $rule, $value, '', 'data-acl-toggle'); ?>

										<div>
											<span class="text-success <?php echo $value ? '' : 't-hidden';?>" data-info data-on><?php echo JText::_('COM_KOMENTO_ACL_RULE_' . strtoupper($rule) . '_ON');?></span>
											<span class="text-danger <?php echo !$value ? '' : 't-hidden';?>" data-info data-off><?php echo JText::_('COM_KOMENTO_ACL_RULE_' . strtoupper($rule) . '_OFF');?></span>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php $i++;?>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', '', 'acl'); ?>
	<?php echo $this->fd->html('form.hidden', 'id', (int) $id); ?>
	<?php echo $this->fd->html('form.hidden', 'target_id', (int) $id); ?>
	<?php echo $this->fd->html('form.hidden', 'target_type', FH::escape($type)); ?>
	<?php echo $this->fd->html('form.hidden', 'current', FH::escape($current), '', 'data-fd-active-tab-input'); ?>
</form>