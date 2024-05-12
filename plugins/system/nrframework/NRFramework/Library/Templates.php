<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Library;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

class Templates
{
	/**
	 * Library
	 * 
	 * @var   Library
	 */
	protected $library = [];

    public function __construct($library = [])
    {
        $this->library = $library;
    }
    
	/**
	 * Checks whether we have the template locally and retrives its layout.
	 * If no local template is found, then retrieves it from remote and returns its layout.
	 * 
	 * @return  string
	 */
	public function tf_library_ajax_get_templates()
	{
		return $this->getTemplates($this->getList());
	}

	/**
	 * Returns all available templates
	 */
	private function getTemplates($templates)
	{
		if (isset($templates->error) && $templates->error)
		{
			return $templates;
		}

		$layout_payload = [
			'main_category_label' => $this->library->getLibrarySetting('main_category_label'),
			'project_name' => $this->library->getLibrarySetting('project_name'),
			'project_license_type' => $this->library->getLibrarySetting('project_license_type'),
			'project_version' => $this->library->getLibrarySetting('project_version'),
			'product_license_settings_url' => $this->library->getLibrarySetting('product_license_settings_url'),
			'template_use_url' => $this->library->getLibrarySetting('template_use_url'),
			'license_key' => $this->library->getLibrarySetting('license_key'),
			'license_key_status' => $this->library->getLibrarySetting('license_key_status'),
			'templates' => isset($templates->templates) ? $templates->templates : [],
			'favorites' => $this->library->favorites->getFavorites()
		];

		$filters_payload = [
			'filters' => $this->getTemplatesFilters(isset($templates->filters) ? $templates->filters : [])
		];

		$layouts_path = JPATH_PLUGINS . '/system/nrframework/layouts';

		return [
			'templates' => \JLayoutHelper::render('library/items_list', $layout_payload, $layouts_path),
			'filters' => \JLayoutHelper::render('library/filters', $filters_payload, $layouts_path)
		];
	}

	/**
	 * Returns the filters payload.
	 * 
	 * @param   object  $filters
	 * 
	 * @return  array
	 */
	private function getTemplatesFilters($filters)
	{
		// Main filters
		$data = [
			'category' => [
				'label' => $this->library->getLibrarySetting('main_category_label', Text::_('NR_CATEGORIES_PLURAL')),
				'items' => isset($filters->categories) ? $filters->categories : []
			],
			'solution' => [
				'label' => Text::_('NR_SOLUTIONS'),
				'items' => isset($filters->solutions) ? $filters->solutions : []
			],
			'goal' => [
				'label' => Text::_('NR_GOALS'),
				'items' => isset($filters->goals) ? $filters->goals : []
			]
		];

		// Add compatibility filter (Free/Pro filtering) only in the Lite version
		if ($this->library->getLibrarySetting('project_license_type') === 'lite')
		{
			$data['compatibility'] = [
				'label' => Text::_('NR_COMPATIBILITY'),
				'items' => isset($filters->compatibility) ? $filters->compatibility : []
			];
		}

		return $data;
	}

	/**
	 * Retrieve remote templates, store them locally and return new layout.
	 * 
	 * @return  string
	 */
	public function tf_library_ajax_refresh_templates()
	{
		return $this->getTemplates($this->getRemoteTemplatesAndStore());
	}

	/**
	 * Insert template.
	 * 
	 * @return  void
	 */
	public function tf_library_ajax_insert_template()
	{
		$template_id = $this->library->getLibrarySetting('template_id');
		
        // Get remote template
		$templates_url = str_replace('{{PROJECT}}', $this->library->getLibrarySetting('project'), TF_TEMPLATE_GET_URL);
		$templates_url = str_replace('{{DOWNLOAD_KEY}}', $this->library->getLibrarySetting('license_key'), $templates_url);
		$templates_url = str_replace('{{TEMPLATE}}', $template_id, $templates_url);

		if (!$body = json_decode($this->curlRequest($templates_url), true))
		{
			return [
				'error' => true,
				'message' => 'Cannot insert template.'
			];
		}

		// An error has occurred
		if (isset($body['error']) && $body['error'])
		{
			return [
				'error' => true,
				'message' => $body['response']
			];
		}

		// Prepare template
		$template = $body['response']['template'];
		// Set ID used to check if we are adding a valid template within the extension's item edit page
		$template['id'] = $body['response']['id'];

		// Save template locally so we can fetch its contents on redirect
		file_put_contents($this->library->getTemplatesPath() . 'template.json', json_encode($template));

		return [
			'error' => false,
			'message' => 'Inserting template.',
			'redirect' => $this->library->getLibrarySetting('template_use_url') . $template_id
		];
	}

    /**
     * Save templates locally
     * 
     * @param   array  $body
     * 
     * @return  void
     */
    private function saveLocalTemplate($body)
    {
        // Create directory if not exist
        if (!is_dir($this->library->getTemplatesPath()))
        {
            \NRFramework\File::createDirs($this->library->getTemplatesPath());
        }
		
        $path = $this->library->getTemplatesPath() . 'templates.json';
        
        file_put_contents($path, json_encode($body));
    }

    /**
     * Returns the local templates
     * 
     * @return  array
     */
	private function getLocalTemplates()
	{
        $path = $this->library->getTemplatesPath() . 'templates.json';
        
		if (!file_exists($path))
		{
			return false;
        }

		return json_decode(file_get_contents($path));
	}

    /**
     * Returns the remote templates
     * 
     * @return  array
     */
	private function getRemoteTemplates()
	{
        // Get remote templates
		$templates_url = str_replace('{{PROJECT}}', $this->library->getLibrarySetting('project'), TF_TEMPLATES_GET_URL);
		$templates_url = str_replace('{{DOWNLOAD_KEY}}', $this->library->getLibrarySetting('license_key'), $templates_url);

		$response = $this->curlRequest($templates_url);

		if (!$response = json_decode($response, true))
		{
			return [
				'error' => true,
				'response' => 'Cannot retrieve templates.'
			];
		}

		if ($response['error'])
		{
			return $repsonse;
		}

		return json_decode($response['response']);
    }
    
    /**
     * Gets the remote templates and stores them locally
     * 
     * @return  array
     */
    private function getRemoteTemplatesAndStore()
    {
        $templates = $this->getRemoteTemplates();

        if (!$templates)
        {
            return [
				'error' => true,
				'message' => Text::_('NR_TEMPLATES_CANNOT_BE_RETRIEVED')
			];
        }

        $this->saveLocalTemplate($templates);

        return $templates;
    }

    /**
     * Get templates list
     * 
     * @return  array
     */
    private function getList()
	{
		// try to find local templates with fallback remote templates
        return $this->getLocalTemplates() ?: $this->getRemoteTemplatesAndStore();
    }

	/**
	 * Executes a cURL request.
	 * 
	 * @param   string  $url
	 * 
	 * @return  mixed
	 */
	private function curlRequest($url)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $response = curl_exec($ch);
        curl_close($ch);

		return $response;
	}
}