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
<div class="panel-table">	
	<table class="app-table app-table-middle">
		<tbody>
			<thead>
				<tr>
					<th><?php echo JText::_('COM_KOMENTO_ACL_COLUMN_GROUP_TITLE'); ?></th>
					<th width="5%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_ID'); ?></th>
				</tr>
			</thead>

			<?php $i = 0; ?>
			<?php foreach ($usergroups as $usergroup) { ?>
			<tr >
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $usergroup->level ); ?> 
					<a href="index.php?option=com_komento&view=acl&layout=form&type=usergroup&id=<?php echo $usergroup->id; ?>"><?php echo $usergroup->title; ?></a>
				</td>
				<td class="center">
					<?php echo $usergroup->id; ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>