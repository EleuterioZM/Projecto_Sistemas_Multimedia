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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-maintenance-form>

	<div class="app-content-table" data-maintenance-container>
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th class="title" nowrap="nowrap" style="text-align:left;">
						<?php echo JText::_('COM_KOMENTO_MAINTENANCE_COLUMN_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_KOMENTO_MAINTENANCE_COLUMN_STATUS'); ?>
					</th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($scripts as $script) { ?>
				<tr data-row data-key="<?php echo $script->key; ?>">
					<td><?php echo $script->title; ?></td>
					<td class="center">
						<span class="label label-warning" data-status>
							<i data-icon class="fdi fa fa-wrench"></i>
						</span>
					</td>
				</tr>
			<?php } ?>

			</tbody>

		</table>
	</div>

	<input type="hidden" name="option" value="com_komento" />
	<input type="hidden" name="controller" value="maintenance" />
	<input type="hidden" name="task" value="form"/>
</form>
