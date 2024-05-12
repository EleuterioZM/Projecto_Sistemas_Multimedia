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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

use ConvertForms\Helper;
use ConvertForms\Validate;
use Joomla\CMS\Filesystem\File as JFile;
use NRFramework\File;
use Joomla\Registry\Registry;

class FileUpload extends \ConvertForms\Field
{
	/**
	 * The default upload folder
	 *
	 * @var string
	 */
	protected $default_upload_folder = '/media/com_convertforms/uploads/{randomid}_{file.basename}';

	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'size',
		'value',
		'browserautocomplete',
		'placeholder',
		'inputcssclass'
	];

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);

		$field = $this->field;

		if (!isset($field->limit_files)) 
		{
			$field->limit_files = 1;
		}

		if (!isset($field->upload_types) || empty($field->upload_types)) 
		{
			$field->upload_types = 'image/*';
		}

		// Accept multiple values
		if ((int) $field->limit_files != 1)
		{
			$field->input_name .= '[]';
		}
	
		return $this;
	}

	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		$is_required 	   = $this->field->get('required');
		$max_files_allowed = $this->field->get('limit_files', 1);
		$allowed_types     = $this->field->get('upload_types');
		$upload_folder     = $this->field->get('upload_folder_type', 'auto') == 'auto' ? $this->default_upload_folder : $this->field->get('upload_folder', $this->default_upload_folder);

		// Remove null and empty values
		$value = is_array($value) ? $value : (array) $value;
		$value = array_filter($value);

		// We expect a not empty array
		if ($is_required && empty($value))
		{
			$this->throwError(\JText::_('COM_CONVERTFORMS_FIELD_REQUIRED'));
		}

		// Do we have the correct number of files?
		if ($max_files_allowed > 0 && count($value) > $max_files_allowed)
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_UPLOAD_MAX_FILES_LIMIT', $max_files_allowed));
		}

		// Validate file paths
		foreach ($value as $key => &$source_file)
		{
			$source_file = base64_decode($source_file);
			$source_file_info = File::pathinfo($source_file);
			$source_basename = $source_file_info['basename'];

			if (!JFile::exists($source_file))
			{	
				$this->throwError(\JText::sprintf('COM_CONVERTFORMS_UPLOAD_FILE_IS_MISSING', $source_basename));
			}

			// Although the file is already checked during upload, make another sanity check here. 
			File::checkMimeOrDie($allowed_types, ['tmp_name' => $source_file]);

			// Remove the random ID added to file's name during upload process
			$source_file_info['filename'] = preg_replace('#cf_(.*?)_(.*?)#', '', $source_file_info['filename']);
			$source_file_info['basename'] = preg_replace('#cf_(.*?)_(.*?)#', '', $source_basename);
			$source_file_info['index'] = $key + 1;

			// Replace Smart Tags in the upload folder value
			// Unfortunately at this time, we don't have submitted data available yet and so, we can't replace field-based Smart Tags. See @todo below.
			$SmartTags = new \NRFramework\SmartTags();
			$SmartTags->add($source_file_info, 'file.');
			$destination_file = JPATH_ROOT . DIRECTORY_SEPARATOR . $SmartTags->replace($upload_folder);

			// Validate destination file
			$destination_file_info = File::pathinfo($destination_file);
			if (!isset($destination_file_info['extension']))
			{
				$destination_file = implode(DIRECTORY_SEPARATOR, [$destination_file_info['dirname'], $destination_file_info['basename'], $source_basename]);
			}

			// Move uploaded file to the destination folder after the form passes all validation checks.
			// Thus, if an error is triggered by another field, the file will remain in the temp folder and the user will be able to re-submit the form.
			$this->app->registerEvent('onConvertFormsSubmissionBeforeSave', function(&$data) use ($key, $source_file, $destination_file)
			{
				try
				{
					// get the data
					$tmpData = $data;
					if (defined('nrJ4'))
					{
						$tmpData = $data->getArgument('0');
					}

					// This is a temporary workaround to support field-based Smart Tags in the upload folder
					// @todo 1: We need to prepare $data with ConvertFormsModelConversion->prepare() and pass it down to Submission::replaceSmartTags() in order for submitted values to be prepared with each field prepare() method.
					// @todo 2: Do Smart Tags replacement once. Merge previous replacement for file Smart Tags with this one.
					$SmartTags = new \NRFramework\SmartTags();
					$SmartTags->add($data['params'], 'field.');
					$destination_file = $SmartTags->replace($destination_file);

					// Move uploaded file from the temp folder to the destination folder.
					$destination_file = File::move($source_file, $destination_file);

					// Give a chance to manipulate the file with a hook.
					// We can move the file to another folder, rename it, resize it or even uploaded it to a cloud storage service.
					$this->app->triggerEvent('onConvertFormsFileUpload', [&$destination_file, $tmpData]);
					
					// Always save the relative path to the database.
					$destination_file = Helper::pathTorelative($destination_file);

					// Update fields' value
					$tmpData['params'][$this->field->get('name')][$key] = $destination_file;
					
					// Set back the new value to $data object
					if (defined('nrJ4'))
					{
						$data->setArgument(0, $tmpData);
					}
					else
					{
						$data = $tmpData;
					}

				} catch (\Throwable $th)
				{
					$this->throwError($th->getMessage());
				}
			});
		}
	}

	/**
	 * Event fired before the field options form is rendered in the backend
	 *
	 * @param  object $form
	 *
	 * @return void
	 */
	protected function onBeforeRenderOptionsForm($form)
	{
		// Set the maximum upload size limit to the respective options form field
		$max_upload_size_str = \JHtml::_('number.bytes', \JUtility::getMaxUploadSize());
		$max_upload_size_int = (int) $max_upload_size_str;

		$form->setFieldAttribute('max_file_size', 'max', $max_upload_size_int);

		$desc_lang_str = $form->getFieldAttribute('max_file_size', 'description');
		$desc = \JText::sprintf($desc_lang_str, $max_upload_size_str);
		$form->setFieldAttribute('max_file_size', 'description', $desc);
	}

	/**
	 * Ajax method triggered by System Plugin during file upload.
	 *
	 * @param	string	$form_id
	 * @param	string	$field_key
	 *
	 * @return	array
	 */
	public function onAjax($form_id, $field_key)
	{
        // Make sure we have a valid form and a field key
        if (!$form_id || !$field_key)
        {
            $this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR');
		}

		// Get field settings
		if (!$upload_field_settings = \ConvertForms\Form::getFieldSettingsByKey($form_id, $field_key))
		{
        	$this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_FIELD');
		}

		$allow_unsafe = $upload_field_settings->get('allow_unsafe', false);

		// Make sure we have a valid file passed
        if (!$file = $this->app->input->files->get('file', null, ($allow_unsafe ? 'raw' : null)))
        {
            $this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_FILE');
		}
		
        // In case we allow multiple uploads the file parameter is a 2 levels array.
        $first_property = array_pop($file);
        if (is_array($first_property))
        {
            $file = $first_property;
		}

		// Upload temporarily to the default upload folder
		$allowed_types = $upload_field_settings->get('upload_types');

		try {
			$uploaded_filename = File::upload($file, null, $allowed_types, $allow_unsafe, 'cf_');

			return [
				// Since the path of the uploaded file will be included in the form's POST data, obfuscate the path for security reasons. 
				// This will also prevent Akeeba Admin Tools DFIShield from mistakenly blocking File Uploads.
				'file' => base64_encode($uploaded_filename),
			];
		} catch (\Throwable $th)
		{
			$this->uploadDie($th->getMessage());
		}
	}

	/**
	 * DropzoneJS detects errors based on the response error code.
	 *
	 * @param  string $error_message
	 *
	 * @return void
	 */
	private function uploadDie($error_message)
	{
		http_response_code('500');
		die(\JText::_($error_message));
	}

	/**
	 * Prepare value to be displayed to the user as plain text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValue($value)
	{
		if (!$value)
		{
			return;
		}

		$value = (array) $value;

		foreach ($value as &$link)
		{
			$link = Helper::absURL($link);
		}

		return implode(', ', $value);
	}

	/**
	 * Prepare value to be displayed to the user as HTML/text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValueHTML($value)
	{
		if (!$value)
		{
			return;
		}

		$links = (array) $value;
		$value = '';

		foreach ($links as $link)
		{
			$link = Helper::absURL($link);
			$value .= '<div><a download href="' . $link . '">' . File::pathinfo($link)['basename'] . '</a></div>';
		}

		return '<div class="cf-links">' . $value . '</div>';
	}

	/**
	 *  Display a text before the form options
	 *
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader()
	{
		$html = '';

		$temp_folder = File::getTempFolder();

		if (!@is_writable($temp_folder))
		{
			$html .= '
				<div class="alert alert-danger">
					' . \JText::sprintf('COM_CONVERTFORMS_FILEUPLOAD_TEMP_FOLDER_NOT_WRITABLE', $temp_folder) . '
				</div>
			';
		}

		// Check if the Fileinfo PHP extension is installed required to detect the mime type.
		if (!extension_loaded('fileinfo') || !function_exists('mime_content_type'))
		{
			$html .= '
				<div class="alert alert-danger">
					' . \JText::sprintf('COM_CONVERTFORMS_FILEUPLOAD_MIME_CONTENT_TYPE_MISSING') . '
				</div>
			';
		}

		return $html;
	}
}