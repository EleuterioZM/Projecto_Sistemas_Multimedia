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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

if ($this->params->get('load_css', true))
{
	JHtml::stylesheet('com_convertforms/submissions.css', ['relative' => true, 'version' => 'auto']);
}

$print_view = JFactory::getApplication()->input->get('print') == 1;

if ($print_view)
{
	JFactory::getDocument()->addScriptDeclaration('
		window.print();
	');
}

$print_link = Route::link('site', 'index.php?option=com_convertforms&view=submission&id=' . $this->submission->id . '&tmpl=component&print=1&Itemid=' . Factory::getApplication()->input->get('Itemid'));

?>
<div class="convertforms-submissions item <?php echo $print_view ? 'print' : '' ?>">
	<h1><?php echo JText::_('COM_CONVERTFORMS_SUBMISSION') ?> #<?php echo $this->submission->id ?></h1>

	<?php if (!$print_view) { ?>
	<p>
		<a target="_blank" href="<?php echo $print_link ?>">
			Print
		</a>
	<p>
	<?php } ?>
	<div class="submission_section submission_info">
		<h3><?php echo JText::_('COM_CONVERTFORMS_LEAD_INFO') ?></h3>
		<table>
			<tr class="cfs-id">
				<th><?php echo JText::_('COM_CONVERTFORMS_ID') ?></th>
				<td><?php echo $this->submission->id ?></td>
			</tr>
			<tr class="cfs-state">
				<th><?php echo JText::_('JSTATUS') ?></th>
				<td>
					<span class="badge <?php echo ($this->submission->state == '1' ? 'badge-success bg-success' : (defined('nrJ4') ? 'badge-danger bg-danger' : 'important')) ?>">
						<?php echo JText::_(($this->submission->state == '1' ? 'COM_CONVERTFORMS_SUBMISSION_CONFIRMED' : 'COM_CONVERTFORMS_SUBMISSION_UNCONFIRMED')) ?>
					</span>
					<?php 
						if (isset($this->submission->params['sync_error'])) { 
							echo $this->submission->params['sync_error'];
						}
					?>
				</td>
			</tr>
			<tr class="cfs-dated-created">
				<th><?php echo JText::_('COM_CONVERTFORMS_CREATED') ?></th>
				<td><?php echo $this->submission->created ?></td>
			</tr>
			<tr class="cfs-date-modified">
				<th><?php echo JText::_('COM_CONVERTFORMS_MODIFIED') ?></th>
				<td><?php echo $this->submission->modified ?></td>
			</tr>
			<tr class="cfs-form">
				<th><?php echo JText::_('COM_CONVERTFORMS_FORM_NAME') ?></th>
				<td><?php echo $this->submission->form->name ?></td>
			</tr>
			<tr class="cfs-user">
				<th>User</th>
				<td><?php echo isset($this->submission->user_name) ? $this->submission->user_name : $this->submission->user_id ?></td>
			</tr>
			<?php
				JPluginHelper::importPlugin('convertformstools');
				JFactory::getApplication()->triggerEvent('onConvertFormsFrontSubmissionViewInfo', array($this->submission));
			?>
		</table>
	</div>
	<div class="submission_section submission_info">
		<h3><?php echo JText::_('COM_CONVERTFORMS_LEAD_USER_SUBMITTED_DATA') ?></h3>
		<?php if (count($this->submission->fields)) { ?>
			<table>
				<?php foreach ($this->submission->fields as $field) { ?>
					<tr class="cfs-<?php echo $field->options->get('name') ?>">
						<th><?php echo $field->class->getLabel() ?></th>
						<td><?php echo $field->value_html ?></td>
					</tr>
				<?php } ?>	
			</table>
		<?php } else { ?>
			<p><?php echo JText::_('COM_CONVERTFORMS_NO_SUBMITTED_DATA') ?></p>
		<?php } ?>
	</div>
	
	<?php if (!$print_view) { ?>
	<a href="<?php echo $this->submissions_link ?>">
		<?php echo JText::_('COM_CONVERTFORMS_SUBMISSIONS_LIST') ?>
	</a>
	<?php } ?>
</div>