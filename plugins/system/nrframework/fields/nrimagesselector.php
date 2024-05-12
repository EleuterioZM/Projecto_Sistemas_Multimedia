<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('text');

class JFormFieldNRImagesSelector extends JFormFieldText
{
    /**
	 * Renders the Images Selector
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$field_attributes = (array) $this->element->attributes();
		$attributes = isset($field_attributes["@attributes"]) ? $field_attributes["@attributes"] : null;
		$field_attributes = new JRegistry($attributes);

		if (!$images = $field_attributes->get('images', ''))
		{
			return;
		}
		
		$columns = $field_attributes->get('columns', 6);
		$width = $field_attributes->get('width', '100%');
		$height = $field_attributes->get('height', '');
		$key_type = $field_attributes->get('key_type', null);

		$class = 'cols_' . $columns . (!empty($this->class) ? ' ' . $this->class : '');

		$paths = explode(',', $images);

		$images = [];
		foreach ($paths as $key => $path)
		{
			// skip empty paths
			if (empty(rtrim(ltrim($path, ' '), ' ')))
			{
				continue;
			}

			if ($imgs = $this->getImagesFromPath($path))
			{
				// add new images to array of images
				$images = array_merge($images, $imgs);
			}
			else
			{
				// check if image exist
				if (file_exists(JPATH_ROOT . '/' . ltrim($path, ' /')))
				{
					// add new image to array of images
					$images[] = ltrim($path, ' /');
				}
			}
		}

		// load CSS
		JHtml::script('plg_system_nrframework/images-selector-field.js', ['relative' => true, 'version' => true]);
		JHtml::stylesheet('plg_system_nrframework/images-selector-field.css', ['relative' => true, 'version' => true]);
		
		$layout = new \JLayoutFile('imagesselector', JPATH_PLUGINS . '/system/nrframework/layouts');

		$data = [
			'value'    => !empty($this->value) ? $this->value : $this->default,
			'name' 	   => $this->name,
			'class'    => $class,
			'key_type' => $key_type,
			'images'   => $images,
			'columns'  => $columns,
			'id'  	   => $this->id,
			'required' => $this->required,
			'width'    => $width,
			'height'   => $height
		];
		
        return $layout->render($data);
	}

    /**
     * Returns all images in path
     * 
     * @return  mixed
     */
	private function getImagesFromPath($path)
	{
		$folder = JPATH_ROOT . '/' . ltrim($path, ' /');

		if (!is_dir($folder) || !$folder_files = scandir($folder))
		{
			return false;
		}
		
		$images = array_diff($folder_files, array('.', '..', '.DS_Store'));
		$images = array_values($images);

		// prepend path to image file names
		array_walk($images, function(&$value, $key) use ($path) { $value = ltrim($path, ' /') . '/' . $value; } );
		
		return $images;
	}
}
