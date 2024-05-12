<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use NRFramework\Helpers\Widgets\GalleryManager as GalleryManagerHelper;

/**
 *  Gallery Manager
 */
class GalleryManager extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		// The input name
		'name' => '',

		// The field ID associated to this Gallery Manager, used to retrieve the field settings on AJAX actions
		'field_id' => null,

		/**
		 * Max file size in MB.
		 * 
		 * Defults to 0 (no limit).
		 */
		'max_file_size' => 0,

		/**
		 * How many files we can upload.
		 * 
		 * Defaults to 0 (no limit).
		 */
		'limit_files' => 0,

		// Allowed upload file types
		'allowed_file_types' => '.jpg, .jpeg, .png, .gif',

		/**
		 * Original Image
		 */
		// Should the original uploaded image be resized?
		'original_image_resize' => false,

		// Original Image Resize Quality
		'original_image_resize_quality' => 80,

		// Main image width
		'original_image_resize_width' => 1920,

		/**
		 * Thumbnails
		 */
		// Thumbnails width
		'thumb_width' => 300,

		// Thumbnails height
		'thumb_height' => null,

		// Thumbnails resize method (crop, stretch, fit)
		'thumb_resize_method' => 'crop',

		// Thumbnails resize quality
		'thumb_resize_quality' => 80
	];

	public function __construct($options = [])
	{
		parent::__construct($options);

		// Set gallery items
		$this->options['gallery_items'] = is_array($this->options['value']) ? $this->options['value'] : [];

		// Set css class for readonly state
		if ($this->options['readonly'])
		{
			$this->options['css_class'] .= ' readonly';
		}

		// Adds a css class when the gallery contains at least one item
		if (count($this->options['gallery_items']))
		{
			$this->options['css_class'] .= ' dz-has-items';
		}

		// Load translation strings
        \JText::script('NR_GALLERY_MANAGER_CONFIRM_REGENERATE_THUMBNAILS');
        \JText::script('NR_GALLERY_MANAGER_CONFIRM_DELETE_ALL_SELECTED');
        \JText::script('NR_GALLERY_MANAGER_CONFIRM_DELETE_ALL');
        \JText::script('NR_GALLERY_MANAGER_CONFIRM_DELETE');
        \JText::script('NR_GALLERY_MANAGER_FILE_MISSING');
        \JText::script('NR_GALLERY_MANAGER_REACHED_FILES_LIMIT');
	}

	/**
	 * The upload task called by the AJAX hanler
	 *
	 * @return  void
	 */
	protected function ajax_upload()
	{
		$input = \JFactory::getApplication()->input;

		// Make sure we have a valid field id
		if (!$field_id = $input->getInt('field_id'))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_FIELD_ID_ERROR');
		}

		// Make sure we have a valid file passed
		if (!$file = $input->files->get('file', null, null))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_ERROR_INVALID_FILE');
		}

		if (!$field_data = \NRFramework\Helpers\CustomField::getData($field_id))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_INVALID_FIELD_DATA');
		}

		// get the media uploader file data, values are passed when we upload a file using the Media Uploader
		$media_uploader_file_data = [
			'is_media_uploader_file' => $input->get('media_uploader', false) == '1',
			'media_uploader_filename' => $input->getString('media_uploader_filename', '')
		];

		// In case we allow multiple uploads the file parameter is a 2 levels array.
		$first_property = array_pop($file);
		if (is_array($first_property))
		{
			$file = $first_property;
		}

		$uploadSettings = [
			'allow_unsafe' => false,
			'allowed_types' => $field_data->get('allowed_file_types', $this->widget_options['allowed_file_types'])
		];

		// resize image settings
		$resizeSettings = [
			'thumb_width' => $field_data->get('thumb_width', 300),
			// Send the height only if grid is selected. We do not need it for masonry style
			'thumb_height' => $field_data->get('style', 'masonry') === 'grid' ? $field_data->get('thumb_height', null) : null,
			'thumb_resize_method' => $field_data->get('thumb_resize_method', 'crop'),
			'thumb_resize_quality' => $field_data->get('thumb_resize_quality', 80),
			'original_image_resize' => ($field_data->get('original_image_resize', false)),
			'original_image_resize_width' => $field_data->get('original_image_resize_width', 1920),
			'original_image_resize_quality' => $field_data->get('original_image_resize_quality', 80)
		];

		// Upload the file and resize the image if needed
		if (!$uploaded_filenames = GalleryManagerHelper::upload($file, $uploadSettings, $media_uploader_file_data, $resizeSettings))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_ERROR_CANNOT_UPLOAD_FILE');
		}

		echo json_encode([
			'filename' => $uploaded_filenames['filename'],
			'thumbnail' => $uploaded_filenames['thumbnail'],
			'is_media_uploader_file' => $media_uploader_file_data['is_media_uploader_file']
		]);
	}

	/**
	 * The delete task called by the AJAX hanlder
	 *
	 * @return void
	 */
	protected function ajax_delete()
	{
		$input = \JFactory::getApplication()->input;

		// Make sure we have a valid file passed
		if (!$filename = $input->getString('filename'))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_ERROR_INVALID_FILE');
		}

		// Make sure we have a valid field id
		if (!$field_id = $input->getInt('field_id'))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_FIELD_ID_ERROR');
		}

		if (!$field_data = \NRFramework\Helpers\CustomField::getData($field_id))
		{
			$this->exitWithMessage('NR_GALLERY_MANAGER_INVALID_FIELD_DATA');
		}

		// Delete the uploaded file
		$deleted = GalleryManagerHelper::deleteFile($filename, $input->getString('thumbnail'));
		
		echo json_encode(['success' => $deleted]);
	}

	/**
	 * This task allows us to regenerate the thumbnails.
	 *
	 * @return void
	 */
	protected function ajax_regenerate_thumbs()
	{
		$input = \JFactory::getApplication()->input;

		// Make sure we have a valid field id
		if (!$field_id = $input->getInt('field_id'))
		{
			echo json_encode(['success' => false, 'message' => \JText::_('NR_GALLERY_MANAGER_FIELD_ID_ERROR')]);
			die();
		}

		if (!$field_data = \NRFramework\Helpers\CustomField::getData($field_id))
		{
			echo json_encode(['success' => false, 'message' => \JText::_('NR_GALLERY_MANAGER_INVALID_FIELD_DATA')]);
			die();
		}

		$resizeSettings = [
			'thumb_width' => $field_data->get('thumb_width', 300),
			// Send the height only if grid is selected. We do not need it for masonry style
			'thumb_height' => $field_data->get('style', 'masonry') === 'grid' ? $field_data->get('thumb_height', null) : null,
			'thumb_resize_method' => $field_data->get('thumb_resize_method', 'crop'),
			'thumb_resize_quality' => $field_data->get('thumb_resize_quality', 80),
		];

		$existing = $input->get('existing', null, 'ARRAY');
		$existing = json_decode($existing[0], true);
		
		$new = $input->get('new', null, 'ARRAY');
		$new = json_decode($new[0], true);

		$ds = DIRECTORY_SEPARATOR;

		// Check each existing file and re-create thumbs
		if (is_array($existing) && count($existing))
		{
			foreach ($existing as $path)
			{
				$_path = implode($ds, [JPATH_ROOT, $path]);
				
				if (!file_exists($_path))
				{
					continue;
				}

				\NRFramework\Helpers\Widgets\GalleryManager::generateThumbnail($_path, $resizeSettings, null, false);
			}
		}

		// Check each newly added file in temp folder and re-create thumbs
		if (is_array($new) && count($new))
		{
			foreach ($new as $path)
			{
				$_path = implode($ds, [\NRFramework\File::getTempFolder(), $path]);

				if (!file_exists($_path))
				{
					continue;
				}

				\NRFramework\Helpers\Widgets\GalleryManager::generateThumbnail($_path, $resizeSettings, null, false);
			}
		}

		echo json_encode(['success' => true, 'message' => \JText::_('NR_GALLERY_MANAGER_THUMBS_REGENERATED')]);
	}

	/**
	 * Exits the page with given message.
	 * 
	 * @param   string  $translation_string
	 * 
	 * @return  void
	 */
	private function exitWithMessage($translation_string)
	{
		http_response_code('500');
		die(\JText::_($translation_string));
	}
}