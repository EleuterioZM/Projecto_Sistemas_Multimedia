<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$activeTab = count($items) > 0 ? 'fmAllFields' : 'fmaddField';

JHtml::script('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js');

?>

<div class="fm" data-formcontrol="<?php echo $formControl ?>" data-nextid="<?php echo $nextid; ?>">

	<?php 
		echo JHtml::_('bootstrap.startTabSet', 'fieldsManager', array('active' => $activeTab));
		echo JHtml::_('bootstrap.addTab', 'fieldsManager', 'fmaddField', JText::_('COM_CONVERTFORMS_ADD_FIELD'));
	?>

	<div class="fmAvailableFields">
		<?php foreach ($fieldgroups as $group => $fieldgroup) { 
			?>
			<div class="fmFieldGroup">
				<h5><?php echo $fieldgroup['title'] ?></h5>
				<div class="fmFields">
					<?php foreach ($fieldgroup['fields'] as $key => $field) { 
						$isProOnly = !$field['class'];
					?>
					<div>
						<button class="cf-btn btn-dark addField" type="button" data-type="<?php echo $field['name']; ?>" title="<?php echo $field['desc']; ?>"<?php if ($isProOnly) { ?> data-pro-only="<?php echo str_replace('Field', '', $field['title']) . ' Field' ?>"<?php } ?>>
							<?php echo $field['title'] ?>
							<?php if ($isProOnly) { ?>
								<span class="icon-lock right"></span>
							<?php } ?>
						</button>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
	
	<?php 
		echo JHtml::_('bootstrap.endTab'); 
		echo JHtml::_('bootstrap.addTab', 'fieldsManager', 'fmAllFields', JText::_('COM_CONVERTFORMS_ALL_FIELDS'));
	?>

	<div class="fmAddedFields">
		<?php foreach ($items as $key => $item) { ?>
			<div class="item" data-key="<?php echo $item['data']['key'] ?>">
				<span class="fmFieldLabel"></span>
				<span class="fmFieldControl">
					<a href="#" class="copyField" title="<?php echo JText::_('COM_CONVERTFORMS_FIELDS_COPY') ?>">
						<span class="cf-icon-copy"></span>
					</a>
					<a href="#" class="removeField" title="<?php echo JText::_('COM_CONVERTFORMS_FIELDS_DELETE') ?>">
						<span class="cf-icon-cancel"></span>
					</a>
				</span>
			</div>
		<?php } ?>
	</div>

	<?php
		echo JHtml::_('bootstrap.endTab'); 
		echo JHtml::_('bootstrap.addTab', 'fieldsManager', 'fmItems', JText::_('JOPTIONS')); 
	?>

	<div class="fmItems">
		<?php 
			foreach ($items as $key => $item)
			{
				echo $item['form'];
			}
		?>
	</div>

	<?php 
		echo JHtml::_('bootstrap.endTab'); 
		echo JHtml::_('bootstrap.endTabSet');
	?>
	
</div>