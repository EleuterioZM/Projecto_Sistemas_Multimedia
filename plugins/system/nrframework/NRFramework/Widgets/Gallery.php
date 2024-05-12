<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

use \NRFramework\Helpers\Widgets\Gallery as GalleryHelper;
use NRFramework\Mimes;
use NRFramework\File;
use NRFramework\Image;

/**
 *  Gallery
 */
class Gallery extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		/**
		 * The gallery items source.
		 * 
		 * This can be one or combination of the following:
		 * 
		 * - Path to a relative folder (String)
		 * 		/path/to/folder
		 * - Path to a relative image (String)
		 * 		/path/to/folder/image.png
		 * - URL of an image (String)
		 * 		https://example.com/path/to/image.png
		 * - Array of images (Array)
		 * 		[
		 * 			'url' => 'https://example.com/path/to/image.png',
		 * 			'thumbnail_url' => 'https://example.com/path/to/image_thumb.png',
		 * 			'caption' => 'This is a caption',
		 * 			'thumbnail_size' => [
		 * 				'width' => '200',
		 * 				'height' => '200'
		 * 			],
		 * 			'module' => 'position-2'
		 * 		]
		 * 
		 * 		- The `url` property is required.
		 * 		- All other properties are optional.
		 */
		'items' => [],

		/**
		 * Set the ordering.
		 * 
		 * Available values:
		 * - default
		 * - alphabetical
		 * - reverse_alphabetical
		 * - random
		 */
		'ordering' => 'default',

		// Set the module key to display whenever we are viewing a single item's lightbox, appearing after the image
		'module' => '',

		// Set the style of the gallery (masonry, grid)
		'style' => 'masonry',

		/**
		 * Define the columns per supported device.
		 * 
		 * Example value:
		 * - An integer representing the columns for all devices: 3
		 * - A value for each device:
		 * [
		 * 	'desktop' => 3,
		 * 	'tablet' => 2,
		 * 	'mobile' => 1
		 * ]
		 */
		'columns' => 4,

		/**
		 * Define the gap per gallery item per supported device.
		 * 
		 * Example value:
		 * - An integer representing the gap for all devices: 30
		 * - A value for each device:
		 * [
		 * 	'desktop' => 30,
		 * 	'tablet' => 20,
		 * 	'mobile' => 10
		 * ]
		 */
		'gap' => 15,

		/**
		 * Set the allowed file types.
		 * 
		 * This is used to validate the files loaded via a directory or a fixed path to an image.
		 * 
		 * Given URLs are not validated by this setting.
		 */
		'allowed_file_types' => '.jpg, .jpeg, .png',

		// Gallery Items wrapper CSS classes
		'gallery_items_css' => '',

		// Set whether to display a lightbox
		'lightbox' => true,

		/**
		 * Source Image
		 */
		/**
		 * Should the source image be resized?
		 * 
		 * If `original_image_resize` is false, then the source image will appear
		 * in the lightbox (also if `thumbnails` is false, the source image will also appear as the thumbnail)
		 * 
		 * Issue: if this image is a raw photo, there are chances it will increase the page load in order for the browser to display the image.
		 * 
		 * By enabling this, we resize the source image to our desired dimensions and reduce the page load in the above scenario.
		 * 
		 * Note: Always ensure the source image is backed up to a safe place.
		 * Note 2: We require thumbnails or original image resize to be enabled for this to work.
		 * 		 Reason: The above options if enabled generate the gallery_info.txt file in the /cache folder which helps us
		 * 				 generate the source images only if necessary(image has been edited), otherwise, the source image would
		 * 				 be generated on each page refresh.
		 */
		'source_image_resize' => false,

		// Source image resize width
		'source_image_resize_width' => 1920,
		
		// Source image resize height
		'source_image_resize_height' => null,

		// Source image resize method (crop, stretch, fit)
		'source_image_resize_method' => 'crop',

		// Source image resize quality
		'source_image_resize_image_quality' => 80,

		/**
		 * Original Image
		 */
		// Should the original uploaded image be resized?
		'original_image_resize' => false,

		// Resize method (crop, stretch, fit)
		'original_image_resize_method' => 'crop',

		/**
		 * Original Image Resize Width.
		 * 
		 * If `original_image_resize_height` is null, resizes via the width to keep the aspect ratio.
		 */
		'original_image_resize_width' => 1920,

		// Original Image Resize Height
		'original_image_resize_height' => null,

		// Original Image Resize Quality
		'original_image_resize_image_quality' => 80,

		/**
		 * Thumbnails
		 */
		// Set whether to generate thumbnails on-the-fly
		'thumbnails' => false,

		// Resize method (crop, stretch, fit)
		'thumb_resize_method' => 'crop',

		// Resize quality
		'thumb_resize_quality' => 80,

		// Thumbnails width
		'thumb_width' => 300,

		// Thumbnails height
		'thumb_height' => null,

		// The CSS class of the thumbnail
		'thumb_class' => '',

		/**
		 * Set whether to resize the images whenever their source file changes.
		 * 
		 * i.e. If we edit the source image and also need to recreate the resized original image or thumbnail.
		 * This is rather useful otherwise we would have to delete the resized image or thumbnail in order for it to be recreated.
		 */
		'force_resizing' => false,

		// Destination folder
		'destination_folder' => 'cache/tassos/gallery',

		// The unique hash of this gallery based on its options
		'hash' => null,

		// Set whether to show warnings when an image that has been set to appear does not exist.
		'show_warnings' => true
	];

	public function __construct($options = [])
	{
		parent::__construct($options);

		$this->prepare();
	}

	/**
	 * Prepares the Gallery.
	 * 
	 * @return  void
	 */
	private function prepare()
	{
		$this->options['hash'] = $this->getHash();

		$this->options['destination_folder'] = JPATH_ROOT . DIRECTORY_SEPARATOR . $this->options['destination_folder'] . DIRECTORY_SEPARATOR . $this->options['hash'] . DIRECTORY_SEPARATOR;

		$this->parseGalleryItems();

		$this->cleanDestinationFolder();
		
		$this->resizeSourceImages();
		$this->resizeOriginalImages();
		$this->createThumbnails();
		
		// Set style on the gallery items container.
		$this->options['gallery_items_css'] .= ' ' . $this->getStyle();

		// Set class to trigger lightbox.
		if ($this->options['lightbox'])
		{
			$this->options['css_class'] .= ' lightbox';
		}

		$this->prepareItems();

		$this->setCSSVariables();

		$this->setOrdering();
	}

	/**
	 * Sets the ordering of the gallery.
	 * 
	 * @return  void
	 */
	private function setOrdering()
	{
		switch ($this->options['ordering']) {
			case 'random':
				shuffle($this->options['items']);
				break;
			case 'alphabetical':
				usort($this->options['items'], [$this, 'compareByThumbnailASC']);
				break;
			case 'reverse_alphabetical':
				usort($this->options['items'], [$this, 'compareByThumbnailDESC']);
				break;
		}
	}

	/**
	 * Compares thumbnail file names in ASC order
	 * 
	 * @param   array  $a
	 * @param   array  $b
	 * 
	 * @return  bool
	 */
	private function compareByThumbnailASC($a, $b)
	{
		return strcmp(basename($a['thumbnail']), basename($b['thumbnail']));
	}

	/**
	 * Compares thumbnail file names in DESC order
	 * 
	 * @param   array  $a
	 * @param   array  $b
	 * 
	 * @return  bool
	 */
	private function compareByThumbnailDESC($a, $b)
	{
		return strcmp(basename($b['thumbnail']), basename($a['thumbnail']));
	}

	/**
	 * Get the hash of this gallery.
	 * 
	 * Generate the hash with only the essential options of the Gallery widget.
	 * i.e. with the data that are related to the images.
	 * 
	 * @return  string
	 */
	private function getHash()
	{
		$opts = [
			'items',
			'style',
			'allowed_file_types',
			'source_image_resize',
			'source_image_resize_width',
			'source_image_resize_height',
			'source_image_resize_method',
			'source_image_resize_image_quality',
			'original_image_resize',
			'original_image_resize_method',
			'original_image_resize_width',
			'original_image_resize_height',
			'original_image_resize_image_quality',
			'thumbnails',
			'thumb_resize_method',
			'thumb_resize_quality',
			'thumb_width',
			'thumb_height',
			'force_resizing',
			'destination_folder'
		];

		$payload = [];
		
		foreach ($opts as $opt)
		{
			$payload[$opt] = $this->options[$opt];
		}
		
		return md5(serialize($payload));
	}

	/**
	 * Cleans the source folder.
	 * 
	 * If an image from the source folder is removed, we also remove the
	 * original image/thumbnail from the destination folder as well as
	 * from the gallery info file.
	 * 
	 * @return  void
	 */
	private function cleanDestinationFolder()
	{
		if (!$this->options['original_image_resize'] && !$this->options['thumbnails'])
		{
			return;
		}

		// Find all folders that we need to search
		$dirs_to_search = [];

		// Store all source files
		$source_files = [];

		foreach ($this->options['items'] as $key => $item)
		{
			if (!isset($item['path']))
			{
				continue;
			}

			$source_files[] = pathinfo($item['path'], PATHINFO_BASENAME);

			$directory = is_dir($item['path']) ? $item['path'] : dirname($item['path']);

			if (in_array($directory, $dirs_to_search))
			{
				continue;
			}
			
			$dirs_to_search[] = $directory;
		}

		if (empty($dirs_to_search))
		{
			return;
		}

		// Loop each directory found and check which files we need to delete
		foreach ($dirs_to_search as $dir)
		{
			$source_folder_info_file = GalleryHelper::getGalleryInfoFileData($dir);

			// Find all soon to be deleted files
			$to_be_deleted = array_diff(array_keys($source_folder_info_file), $source_files);

			if (!count($to_be_deleted))
			{
				continue;
			}
			
			foreach ($to_be_deleted as $source)
			{
				// Original image delete
				if (isset($source_folder_info_file[$source]))
				{
					$file = $this->options['destination_folder'] . $source_folder_info_file[$source]['filename'];
					if (file_exists($file))
					{
						unlink($file);
					}
				}
				
				// Thumbnail delete
				$parts = pathinfo($file);
				$thumbnail = $this->options['destination_folder'] . $parts['filename'] . '_thumb.' . $parts['extension'];
				if (file_exists($thumbnail))
				{
					unlink($thumbnail);
				}

				// Also remove the image from the gallery info file.
				GalleryHelper::removeImageFromGalleryInfoFile(rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $source);
			}
		}
	}

	/**
	 * Returns the gallery style.
	 * 
	 * @return  string
	 */
	private function getStyle()
	{
		$style = $this->options['style'];

		// Get aspect ratio for source image, original image resized and thumbnail
		$thumb_height = intval($this->options['thumb_height']);
		$thumb_aspect_ratio = $thumb_height ? intval($this->options['thumb_width']) / $thumb_height : 0;
		
		$source_image_height = intval($this->options['source_image_resize_height']);
		$source_image_aspect_ratio = $source_image_height ? intval($this->options['source_image_resize_width']) / $source_image_height : 0;
		
		$original_image_height = intval($this->options['original_image_resize_height']);
		$original_image_aspect_ratio = $original_image_height ? intval($this->options['original_image_resize_width']) / $original_image_height : 0;

		// Check whether the aspect ratio for thumb and lightbox image are the same and use `masonry` style
		$checking_aspect_ratio = $this->options['original_image_resize'] ? $original_image_aspect_ratio : $source_image_aspect_ratio;
		if ($thumb_aspect_ratio && $checking_aspect_ratio && $thumb_aspect_ratio === $checking_aspect_ratio)
		{
			return 'masonry';
		}

		/**
		 * If both thumbnail width & height are equal we use the `grid` style.
		 */
		if ($this->options['thumb_width'] === $this->options['thumb_height'])
		{
			$style = 'grid';
		}
		
		/**
		 * If the style is grid and we do not have a null or 0 thumb_height set the fade lightbox CSS Class.
		 * 
		 * This CSS Class tells PhotoSwipe to use the fade transition.
		 */
		if ($style === 'grid' && (!is_null($this->options['thumb_height']) && $this->options['thumb_height'] !== '0'))
		{
			$this->options['css_class'] .= ' lightbox-fade';
		}
		
		return $style;
	}

	/**
	 * Parses the gallery items by finding all iamges to display from all
	 * different sources.
	 * 
	 * @return  void
	 */
	private function parseGalleryItems()
	{
		// If it's a string, we assume its a path to a folder and we convert it to an array.
		$this->options['items'] = (array) $this->options['items'];

		$items = [];

		foreach ($this->options['items'] as $key => $value)
		{
			if (!$data = GalleryHelper::parseGalleryItems($value, $this->getAllowedFileTypes()))
			{
				continue;
			}

			$items = array_merge($items, $data);
		}

		// Ensure only unique image paths are used
		$items = array_unique($items, SORT_REGULAR);

		$this->options['items'] = $items;
	}

	/**
	 * Returns the allowed file types in an array format.
	 * 
	 * @return  array
	 */
	public function getAllowedFileTypes()
	{
		$types = explode(',', $this->options['allowed_file_types']);
		$types = array_filter(array_map('trim', array_map('strtolower', $types)));
		return $types;
	}

	/**
	 * Resizes the source images.
	 * 
	 * @return  mixed
	 */
	private function resizeSourceImages()
	{
		if (!$this->options['source_image_resize'])
		{
			return;
		}

		// We require either original image resize or thumbnails to be enabled
		if (!$this->options['original_image_resize'] && !$this->options['thumbnails'])
		{
			return;
		}

		foreach ($this->options['items'] as $key => &$item)
		{
			if (!isset($item['path']))
			{
				continue;
			}

			// Skip if source does not exist
			if (!is_file($item['path']))
			{
				continue;
			}

			$source = $item['path'];

			// Find source image in the destination folder
			if ($image_data = GalleryHelper::findSourceImageDetails($source, $this->options['destination_folder']))
			{
				// If force resizing is disabled, continue
				if (!$this->options['force_resizing'])
				{
					continue;
				}
				else
				{
					// If the destination image has not been edited and exists, abort
					if (!$image_data['edited'] && file_exists($image_data['path']))
					{
						continue;
					}
				}
			}

			if (is_null($this->options['source_image_resize_height']))
			{
				Image::resizeAndKeepAspectRatio(
					$source,
					$this->options['source_image_resize_width'],
					$this->options['source_image_resize_image_quality']
				);
			}
			else
			{
				Image::resize(
					$source,
					$this->options['source_image_resize_width'],
					$this->options['source_image_resize_height'],
					$this->options['source_image_resize_image_quality'],
					$this->options['source_image_resize_method']
				);
			}
		}
	}

	/**
	 * Resizes the original images.
	 * 
	 * @return  mixed
	 */
	private function resizeOriginalImages()
	{
		if (!$this->options['original_image_resize'])
		{
			return;
		}

		// Create destination folder if missing
		File::createDirs($this->options['destination_folder']);
		
		foreach ($this->options['items'] as $key => &$item)
		{
			if (!isset($item['path']))
			{
				continue;
			}

			// Skip if source does not exist
			if (!is_file($item['path']))
			{
				continue;
			}

			$source = $item['path'];

			$unique = true;

			// Path to resized image in destination folder
			$destination = $this->options['destination_folder'] . basename($source);
			
			// Find source image in the destination folder
			if ($image_data = GalleryHelper::findSourceImageDetails($source, $this->options['destination_folder']))
			{
				// If force resizing is disabled and the original image exists, set the URL of the destination image
				if (!$this->options['force_resizing'] && file_exists($image_data['path']))
				{
					$item['url'] = GalleryHelper::directoryImageToURL($image_data['path']);
					continue;
				}
				else
				{
					// If the destination image has not been edited and exists, abort
					if (!$image_data['edited'] && file_exists($image_data['path']))
					{
						$item['url'] = GalleryHelper::directoryImageToURL($image_data['path']);
						continue;
					}
					
					// Since we are forcing resizing, overwrite the existing image, do not create a new unique image
					$unique = false;

					// The destination path is the same resized image
					$destination = $image_data['path'];
				}
			}

			$original_image_file = is_null($this->options['original_image_resize_height'])
				?
				Image::resizeAndKeepAspectRatio(
					$source,
					$this->options['original_image_resize_width'],
					$this->options['original_image_resize_image_quality'],
					$destination,
					$unique
				)
				:
				Image::resize(
					$source,
					$this->options['original_image_resize_width'],
					$this->options['original_image_resize_height'],
					$this->options['original_image_resize_image_quality'],
					$this->options['original_image_resize_method'],
					$destination,
					$unique
				);

			if (!$original_image_file)
			{
				continue;
			}

			// Set image URL
			$item = array_merge($item, [
				'url' => GalleryHelper::directoryImageToURL($original_image_file)
			]);

			// Update image data in Gallery Info File
			GalleryHelper::updateImageDataInGalleryInfoFile($source, $item);
		}
	}

	/**
	 * Creates thumbnails.
	 * 
	 * If `force_resizing` is enabled, it will re-generate thumbsnails under the following cases:
	 * 
	 * - If a thumbnail does not exist.
	 * - If the original image has been edited.
	 * 
	 * @return  mixed
	 */
	private function createThumbnails()
	{
		if (!$this->options['thumbnails'])
		{
			return false;
		}
		
		// Create destination folder if missing
		File::createDirs($this->options['destination_folder']);

		foreach ($this->options['items'] as $key => &$item)
		{
			// Skip items that do not have a path set
			if (!isset($item['path']))
			{
				continue;
			}

			// Skip if source does not exist
			if (!is_file($item['path']))
			{
				continue;
			}

			$source = $item['path'];

			$unique = true;

			$parts = pathinfo($source);
			$destination = $this->options['destination_folder'] . $parts['filename'] . '_thumb.' . $parts['extension'];
			
			// Find source image in the destination folder
			if ($image_data = GalleryHelper::findSourceImageDetails($source, $this->options['destination_folder']))
			{
				/**
				 * Use the found original image path to produce the thumb file path.
				 * 
				 * This is used as we have multiple files with the same which produce file names of _copy_X
				 * and thus the above $destination will not be valid. Instead, we use the original file name
				 * to find the thumbnail file.
				 */
				if ($this->options['original_image_resize'])
				{
					$parts = pathinfo($image_data['path']);
					$destination = $this->options['destination_folder'] . $parts['filename'] . '_thumb.' . $parts['extension'];
				}
				
				// If force resizing is disabled and the thumbnail exists, set the URL of the destination image
				if (!$this->options['force_resizing'] && file_exists($destination))
				{
					$item['thumbnail_url'] = GalleryHelper::directoryImageToURL($destination);
					continue;
				}
				else
				{
					// If the destination image has not been edited and exists, abort
					if (!$image_data['edited'] && file_exists($destination))
					{
						$item['thumbnail_url'] = GalleryHelper::directoryImageToURL($destination);
						continue;
					}
					
					// Since we are forcing resizing, overwrite the existing image, do not create a new unique image
					$unique = false;
				}
			}

			// Generate thumbnails
			$thumb_file = is_null($this->options['thumb_height'])
				?
				Image::resizeAndKeepAspectRatio(
					$source,
					$this->options['thumb_width'],
					$this->options['thumb_resize_quality'],
					$destination,
					$unique
				)
				:
				Image::resize(
					$source,
					$this->options['thumb_width'],
					$this->options['thumb_height'],
					$this->options['thumb_resize_quality'],
					$this->options['thumb_resize_method'],
					$destination,
					$unique
				);

			if (!$thumb_file)
			{
				continue;
			}

			// Set image thumbnail URL
			$item = array_merge($item, [
				'thumbnail_url' => GalleryHelper::directoryImageToURL($thumb_file)
			]);

			// Update image data in Gallery Info File
			GalleryHelper::updateImageDataInGalleryInfoFile($source, $item);
		}
	}
	
	/**
	 * Prepares the items.
	 * 
	 * - Sets the thumbnails image dimensions.
	 * - Assures caption property exist.
	 * 
	 * @return  mixed
	 */
	private function prepareItems()
	{
		if (!is_array($this->options['items']) || !count($this->options['items']))
		{
			return;
		}

		foreach ($this->options['items'] as $key => &$item)
		{
			// Initialize image atts
			$item['img_atts'] = '';

			// Initializes caption if none given
			if (!isset($item['caption']))
			{
				$item['caption'] = '';
			}

            $item['alt'] = !empty($item['caption']) ? mb_substr($item['caption'], 0, 100) : pathinfo($item['url'], PATHINFO_FILENAME);
			
			// Ensure a thumbnail is given
			if (!isset($item['thumbnail_url']))
			{
				// If no thumbnail is given, set it to the full image
				$item['thumbnail_url'] = $item['url'];
				continue;
			}

			// If the thumbnail size for this item is given, set the image attributes
			if (isset($item['thumbnail_size']))
			{
				$item['img_atts'] = 'width="' . $item['thumbnail_size']['width'] . '" height="' . $item['thumbnail_size']['height'] . '"';
				continue;
			}
		}
	}

	/**
	 * Sets CSS variables for the widget.
	 * 
	 * @return  void
	 */
	private function setCSSVariables()
	{
		$data = array_merge(
			$this->getColumns(),
			$this->getGap()
		);

		$css = '';
		foreach ($data as $key => $value)
		{
			$css .= '--' . $key . ':' . $value . ';';
		}

		\JFactory::getDocument()->addStyleDeclaration('
		.nrf-widget.' . $this->options['id'] . ' { ' . $css . ' }');
	}

	/**
	 * Returns the columns of the gallery.
	 * 
	 * @return  array
	 */
	private function getColumns()
	{
		if (is_string($this->options['columns']))
		{
			$this->options['columns'] = (int) $this->options['columns'];
		}
		
		if (!is_int($this->options['columns']) && !is_array($this->options['columns']))
		{
			return [];
		}

		$columns_defaults = [
			'columns' => 4,
			'tablet-columns' => 2,
			'mobile-columns' => 1
		];

		$columns = [];

		// String
		if (is_int($this->options['columns']))
		{
			$columns = [
				'columns' => $this->options['columns'],
				'tablet-columns' => $this->options['columns'] !== 1 ? 2 : 1,
				'mobile-columns' => 1
			];
		}

		// Array
		if (is_array($this->options['columns']))
		{
			if (isset($this->options['columns']['desktop']))
			{
				$columns['columns'] = $this->options['columns']['desktop'];
			}
			if (isset($this->options['columns']['tablet']))
			{
				$columns['tablet-columns'] = $this->options['columns']['tablet'];
			}
			if (isset($this->options['columns']['mobile']))
			{
				$columns['mobile-columns'] = $this->options['columns']['mobile'];
			}
		}

		return array_merge($columns_defaults, $columns);
	}

	/**
	 * Returns the gaps of the gallery.
	 * 
	 * @return  array
	 */
	private function getGap()
	{
		if (is_string($this->options['gap']))
		{
			$this->options['gap'] = (int) $this->options['gap'];
		}
		
		if (!is_int($this->options['gap']) && !is_array($this->options['gap']))
		{
			return [];
		}

		$gap_defaults = [
			'gap' => 0,
			'tablet-gap' => 0,
			'mobile-gap' => 0
		];

		$gaps = [];
		
		// String
		if (is_int($this->options['gap']))
		{
			$gaps = [
				'gap' => $this->options['gap'] . 'px',
				'tablet-gap' => $this->options['gap'] . 'px',
				'mobile-gap' => $this->options['gap'] . 'px'
			];
		}

		// Array
		if (is_array($this->options['gap']))
		{
			if (isset($this->options['gap']['desktop']))
			{
				$gaps['gap'] = $this->options['gap']['desktop'] . 'px';
			}
			if (isset($this->options['gap']['tablet']))
			{
				$gaps['tablet-gap'] = $this->options['gap']['tablet'] . 'px';
			}
			if (isset($this->options['gap']['mobile']))
			{
				$gaps['mobile-gap'] = $this->options['gap']['mobile'] . 'px';
			}
		}

		return array_merge($gap_defaults, $gaps);
	}
}