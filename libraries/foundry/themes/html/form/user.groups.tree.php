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
<div class="app-tree">
	<?php foreach ($groups as $group) { ?>
		<?php if ((!$checkSuperAdmin) || FH::isSiteAdmin() || (!JAccess::checkGroup($group->id, 'core.admin'))) { ?>
		<div class="tree-control">
			<label for="<?php echo $name . '_' . $group->id;?>" class="checkbox">
				<input type="checkbox" id="<?php echo $name . '_' . $group->id;?>" 
					value="<?php echo $group->id;?>" 
					name="<?php echo $name;?>[]"

					<?php if ($selected) { ?>
						<?php echo in_array($group->id, $selected) ? 'checked="checked"' : '';?>
					<?php } ?>

					<?php if ($group->parent_id > 0) { ?>
						rel="<?php echo $name;?>_group_<?php echo $group->parent_id;?>"
					<?php } ?>
				/>
				
				<div class="tree-title">
					<?php echo str_repeat('<span class="gi"></span>', $group->level);?> <b><?php echo $group->title;?></b>
				</div>
			</label>
		</div>			
		<?php } ?>
	<?php } ?>
	<?php echo $this->fd->html('form.hidden', $name . '[]', '0'); ?>
</div>
