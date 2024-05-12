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

// Load dropzone library
JHtml::script('https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.min.js');
JHtml::script('com_convertforms/field_fileupload.js', ['relative' => true, 'version' => 'auto']);

// Add language strings used by dropzone.js
JText::script('COM_CONVERTFORMS_ERROR_WAIT_FILE_UPLOADS');
JText::script('COM_CONVERTFORMS_UPLOAD_FILETOOBIG');
JText::script('COM_CONVERTFORMS_UPLOAD_INVALID_FILE');
JText::script('COM_CONVERTFORMS_UPLOAD_FALLBACK_MESSAGE');
JText::script('COM_CONVERTFORMS_UPLOAD_RESPONSE_ERROR');
JText::script('COM_CONVERTFORMS_UPLOAD_CANCEL_UPLOAD');
JText::script('COM_CONVERTFORMS_UPLOAD_CANCEL_UPLOAD_CONFIRMATION');
JText::script('COM_CONVERTFORMS_UPLOAD_REMOVE_FILE');
JText::script('COM_CONVERTFORMS_UPLOAD_MAX_FILES_EXCEEDED');

?>

<div class="cfup-tmpl" style="display:none;">
	<div class="cfup-file">
		<div class="cfup-status"></div>
		<div class="cfup-thumb">
			<img data-dz-thumbnail />
		</div>
		<div class="cfup-details">
			<div class="cfup-name" data-dz-name></div>
			<div class="cfup-error"><div data-dz-errormessage></div></div>
			<div class="cfup-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
		</div>
		<div class="cfup-right">
			<span class="cfup-size" data-dz-size></span>
			<a href="#" class="cfup-remove" data-dz-remove>×</a>
		</div>
	</div>
</div>

<div id="<?php echo $field->input_id ?>" 
	data-name="<?php echo $field->input_name ?>"
	data-key="<?php echo $field->key ?>"
	data-maxfilesize="<?php echo $field->max_file_size ?>"
	data-maxfiles="<?php echo $field->limit_files ?>"
	data-acceptedfiles="<?php echo $field->upload_types ?>"
	class="cfupload">
	<div class="dz-message">
		<span><?php echo JText::_('COM_CONVERTFORMS_UPLOAD_DRAG_AND_DROP_FILES') ?></span>
		<span class="cfupload-browse"><?php echo JText::_('COM_CONVERTFORMS_UPLOAD_BROWSE') ?></span>
	</div>
</div>